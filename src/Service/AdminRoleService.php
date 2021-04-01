<?php


namespace SymfonyAdmin\Service;


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
     * @return array
     */
    public function getRoleList(AdminAuth $adminAuth): array
    {
        # 查询多层级下属用户组ID
        return $this->getAdminRoleRepo()->findMultiAllByParentRole($adminAuth->getAdminRole());
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
        $childRoleIds = $this->getAdminRoleRepo()->findMultiAllByParentRole($adminAuth->getAdminRole(), true);
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
            throw new NotExistException('菜单已存在，无法重复新增！');
        }

        $em = $this->doctrine->getManager();
        # 添加菜单
        $adminRole = new AdminRole();
        $adminRole->setRoleName($adminRoleRequest->getRoleName());
        $adminRole->setRoleCode($adminRoleRequest->getRoleCode());
        $adminRole->setParentId($adminAuth->getAdminUser()->getId());
        $em->persist($adminRole);
        $em->flush();
        return $adminRole->getApiFormat();
    }
}
