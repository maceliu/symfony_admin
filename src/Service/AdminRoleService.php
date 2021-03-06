<?php


namespace SymfonyAdmin\Service;


use Doctrine\ORM\NonUniqueResultException;
use ReflectionException;
use SymfonyAdmin\Entity\AdminAuth;
use SymfonyAdmin\Entity\AdminRole;
use SymfonyAdmin\Exception\NoAuthException;
use SymfonyAdmin\Exception\NotExistException;
use SymfonyAdmin\Request\AdminRoleRequest;
use SymfonyAdmin\Service\Base\BaseService;
use SymfonyAdmin\Service\Base\CurdTrait;
use SymfonyAdmin\Utils\Enum\StatusEnum;

class AdminRoleService extends BaseService
{
    use CurdTrait;

    /**
     * @param AdminAuth $adminAuth
     * @param int $pageNum
     * @param int $pageSize
     * @param array $conditions
     * @return array
     * @throws NotExistException
     */
    public function getRoleListWithPage(AdminAuth $adminAuth, int $pageNum, int $pageSize, array $conditions = []): array
    {
        # 查询多层级下属用户组ID
        $roleIds = $this->getAdminRoleRepo()->findMultiAllByParentRole($adminAuth->getAdminRole());
        if (empty($roleIds)) {
            throw new NotExistException('没有所属下级用户组！');
        }

        $paginatorResult = $this->getAdminRoleRepo()->findAllByIdsWithPage($roleIds, $pageNum, $pageSize, $conditions);

        $rows = [];
        /** @var AdminRole $adminRole */
        foreach ($paginatorResult->getEntityList() as $adminRole) {
            $rows[] = $adminRole->getApiFormat();
        }
        $paginatorResult->setRowsList($rows);

        return $paginatorResult->toArray();
    }

    /**
     * @param AdminAuth $adminAuth
     * @return array
     */
    public function getAllRoleList(AdminAuth $adminAuth): array
    {
        # 查询多层级下属用户组ID
        return $this->getAdminRoleRepo()->findMultiAllByParentRole($adminAuth->getAdminRole(), false);
    }

    /**
     * @param AdminAuth $adminAuth
     * @param AdminRoleRequest $adminRoleRequest
     * @return AdminRole
     * @throws NoAuthException
     * @throws NotExistException
     */
    public function getChildOne(AdminAuth $adminAuth, AdminRoleRequest $adminRoleRequest): AdminRole
    {
        $childRoleIds = $this->getAdminRoleRepo()->findMultiAllByParentRole($adminAuth->getAdminRole());
        if (!in_array($adminRoleRequest->getId(), $childRoleIds)) {
            throw new NoAuthException('无权限编辑的角色组！');
        }

        $adminRole = $this->getAdminRoleRepo()->findOneById($adminRoleRequest->getId());
        if (!$adminRole) {
            throw new NotExistException('角色组信息不存在！');
        }

        return $adminRole;
    }

    /**
     * @param AdminAuth $adminAuth
     * @param AdminRoleRequest $adminRoleRequest
     * @return array
     * @throws NotExistException
     */
    public function create(AdminAuth $adminAuth, AdminRoleRequest $adminRoleRequest): array
    {
        $adminRole = $this->getAdminRoleRepo()->findOneByName($adminRoleRequest->getRoleName());
        if ($adminRole && $adminRole->getStatus() == StatusEnum::ON) {
            throw new NotExistException('角色组名称已存在，无法重复新增！');
        }

        $adminRole = $this->getAdminRoleRepo()->findOneByRoleCode($adminRoleRequest->getRoleCode());
        if ($adminRole && $adminRole->getStatus() == StatusEnum::ON) {
            throw new NotExistException('角色组编码已存在，无法重复新增！');
        }

        $em = $this->doctrine->getManager();
        # 添加菜单
        $adminRole = new AdminRole();
        $adminRole->setRoleName($adminRoleRequest->getRoleName());
        $adminRole->setRoleCode($adminRoleRequest->getRoleCode());
        $adminRole->setStatus($adminRoleRequest->getStatus());
        $adminRole->setParentId($adminRoleRequest->getParentRoleId() ?: $adminAuth->getAdminUser()->getRoleId());
        $em->persist($adminRole);
        $em->flush();
        return $adminRole->getApiFormat();
    }

    /**
     * @param AdminAuth $adminAuth
     * @param AdminRoleRequest $request
     * @return array
     * @throws NoAuthException
     * @throws NotExistException
     * @throws ReflectionException
     * @throws NonUniqueResultException
     */
    public function update(AdminAuth $adminAuth, AdminRoleRequest $request): array
    {
        $oldAdminRole = $this->getAdminRoleRepo()->findConflictOneOnyByNameOrCode($request->getRoleName(), $request->getRoleCode(), $request->getId());
        if ($oldAdminRole && $oldAdminRole->getStatus() == StatusEnum::ON) {
            throw new NotExistException('角色组名称或编码已存在，无法重复新增！');
        }

        $adminRole = $this->getChildOne($adminAuth, $request);
        $adminRole->setRoleName($request->getRoleName());
        $adminRole->setRoleCode($request->getRoleCode());
        $adminRole->setStatus($request->getStatus());
        $adminRole->setParentId($request->getParentRoleId() ?: $adminAuth->getAdminUser()->getRoleId());
        $em = $this->doctrine->getManager();
        $em->persist($adminRole);
        $em->flush();

        return $adminRole->toArray();
    }

    /**
     * @param AdminAuth $adminAuth
     * @param AdminRoleRequest $request
     * @return array
     * @throws NoAuthException
     * @throws NotExistException
     * @throws ReflectionException
     */
    public function updateStatus(AdminAuth $adminAuth, AdminRoleRequest $request): array
    {
        $adminRole = $this->getChildOne($adminAuth, $request);
        $adminRole->setStatus($request->getStatus());
        $em = $this->doctrine->getManager();
        $em->persist($adminRole);
        $em->flush();
        return $adminRole->toArray();
    }
}
