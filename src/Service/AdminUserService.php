<?php


namespace SymfonyAdmin\Service;


use Symfony\Component\Mailer\MailerInterface;
use SymfonyAdmin\Entity\AdminAuth;
use SymfonyAdmin\Entity\AdminUser;
use SymfonyAdmin\Exception\DataDuplicationException;
use SymfonyAdmin\Exception\NoAuthException;
use SymfonyAdmin\Exception\NotExistException;
use SymfonyAdmin\Request\AdminUserRequest;
use SymfonyAdmin\Service\Base\BaseService;
use SymfonyAdmin\Service\Base\CurdTrait;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use ReflectionException;

class AdminUserService extends BaseService
{

    use CurdTrait;

    /** @var LoggerInterface */
    private $logger;

    /** @var MailerInterface */
    private $mailer;

    public function __construct(ManagerRegistry $doctrine, LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->logger = $logger;
        parent::__construct($doctrine);
    }

    /**
     * @param AdminAuth $adminAuth
     * @param AdminUserRequest $adminUserRequest
     * @return AdminUser
     * @throws NoAuthException
     * @throws NotExistException
     */
    protected function getChildOne(AdminAuth $adminAuth, AdminUserRequest $adminUserRequest): AdminUser
    {
        $adminUser = $this->getAdminUserRepo()->findOneById($adminUserRequest->getId());
        if (!$adminUser) {
            throw new NotExistException('用户信息不存在！');
        }

        # 查询多层级下属用户组ID
        $childRoleIds = $this->getAdminRoleRepo()->findMultiAllIdsByParentId($adminAuth->getAdminRole()->getId());
        if (!in_array($adminUser->getRoleId(), $childRoleIds) || ($adminUserRequest->getRoleId() && !in_array($adminUserRequest->getRoleId(), $childRoleIds))) {
            throw new NoAuthException('无权限修改此角色组用户信息！');
        }

        return $adminUser;
    }

    /**
     * @param AdminAuth $adminAuth
     * @param int $pageNum
     * @param int $pageSize
     * @param array $conditions
     * @return array
     * @throws ReflectionException|NotExistException
     */
    public function getListByPage(AdminAuth $adminAuth, int $pageNum, int $pageSize, array $conditions = []): array
    {
        # 查询多层级下属用户组ID
        $childRoleIds = $this->getAdminRoleRepo()->findMultiAllIdsByParentId($adminAuth->getAdminRole()->getId());
        if (empty($childRoleIds)) {
            throw new NotExistException('没有所属下级用户！');
        }

        # 分页查询用户信息
        $paginatorResult = $this->getAdminUserRepo()->findAllByIdsWithPage($childRoleIds, $pageNum, $pageSize, $conditions);

        $rows = [];
        /** @var AdminUser $adminUser */
        foreach ($paginatorResult->getEntityList() as $adminUser) {
            $data = $adminUser->toArray();
            $data['roleName'] = $adminUser->getAdminRole()->getRoleName();
            $rows[] = $data;
        }
        $paginatorResult->setRowsList($rows);

        return $paginatorResult->toArray();
    }

    /**
     * @param AdminAuth $adminAuth
     * @param AdminUserRequest $adminUserRequest
     * @return array
     * @throws NoAuthException
     * @throws ReflectionException
     * @throws DataDuplicationException
     */
    public function create(AdminAuth $adminAuth, AdminUserRequest $adminUserRequest): array
    {
        # 查询多层级下属用户组ID
        $childRoleIds = $this->getAdminRoleRepo()->findMultiAllIdsByParentId($adminAuth->getAdminRole()->getId());
        if ($adminUserRequest->getRoleId() && !in_array($adminUserRequest->getRoleId(), $childRoleIds)) {
            throw new NoAuthException('无权限修改此角色组用户信息！');
        }

        $oldUser = $this->getAdminUserRepo()->findOneByUsername($adminUserRequest->getUsername());
        if ($oldUser) {
            throw new DataDuplicationException('此用户名已被使用，无法创建用户！');
        }

        # 默认游客用户组
        $guestRole = $this->getAdminRoleRepo()->findOneByRoleCode('guest');
        if (!$guestRole) {
            throw new NoAuthException('默认游客用户组不存在！');
        }

        $em = $this->doctrine->getManager();

        # 新建模式
        $adminUser = new AdminUser();
        $adminUser->setRoleId($adminUserRequest->getRoleId());
        $adminUser->setAdminRole($guestRole);
        $adminUser->setUsername($adminUserRequest->getUsername());
        $adminUser->setPassword(AdminLoginService::makeUserPassword($adminUserRequest->getPassword(), $adminUserRequest->getUsername()));
        $adminUser->setEmail($adminUserRequest->getEmail());
        $adminUser->setTrueName($adminUserRequest->getTrueName());
        $adminUser->setStatus($adminUserRequest->getStatus());
        $adminUser->setAvatar($adminUserRequest->getAvatar());

        # 入库
        $em->persist($adminUser);
        $em->flush();
        return $adminUser->toArray();
    }

    /**
     * @param AdminAuth $adminAuth
     * @param AdminUserRequest $adminUserRequest
     * @return array
     * @throws NotExistException|NoAuthException|ReflectionException
     */
    public function updateUserRole(AdminAuth $adminAuth, AdminUserRequest $adminUserRequest): array
    {
        $adminUser = $this->getChildOne($adminAuth, $adminUserRequest);

        $em = $this->doctrine->getManager();
        $adminUser->setRoleId($adminUserRequest->getRoleId());
        $em->persist($adminUser);
        $em->flush();

        return $adminUser->toArray();
    }

    /**
     * @param AdminAuth $adminAuth
     * @param AdminUserRequest $adminUserRequest
     * @return array
     * @throws ReflectionException
     */
    public function selfUpdate(AdminAuth $adminAuth, AdminUserRequest $adminUserRequest): array
    {
        $adminUser = $adminAuth->getAdminUser();

        if ($adminUserRequest->getTrueName()) {
            $adminUser->setTrueName($adminUserRequest->getTrueName());
        }

        if ($adminUserRequest->getEmail()) {
            $adminUser->setEmail($adminUserRequest->getEmail());
        }

        if ($adminUserRequest->getAvatar()) {
            $adminUser->setAvatar($adminUserRequest->getAvatar());
        }

        if ($adminUserRequest->getPassword()) {
            $adminUser->setPassword($adminUserRequest->getPassword());
        }

        $em = $this->doctrine->getManager();
        $em->persist($adminUser);
        $em->flush();

        return $adminUser->toArray();
    }
}
