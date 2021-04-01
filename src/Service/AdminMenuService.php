<?php


namespace SymfonyAdmin\Service;


use SymfonyAdmin\Entity\AdminAuth;
use SymfonyAdmin\Entity\AdminMenu;
use SymfonyAdmin\Entity\AdminRole;
use SymfonyAdmin\Entity\AdminRoleMenuMap;
use SymfonyAdmin\Exception\InvalidParamsException;
use SymfonyAdmin\Exception\NoAuthException;
use SymfonyAdmin\Exception\NotExistException;
use SymfonyAdmin\Request\AdminMenuRequest;
use SymfonyAdmin\Service\Base\BaseService;
use App\Service\Base\CurdTrait;
use App\Utils\Enum\StatusEnum;
use ReflectionException;

class AdminMenuService extends BaseService
{
    use CurdTrait;

    /**
     * @param AdminAuth $adminAuth
     * @param bool $isOnlyId
     * @return array
     * @throws NotExistException
     */
    private function getUserMenuList(AdminAuth $adminAuth, bool $isOnlyId = true): array
    {
        # 超级管理员获取所有菜单信息 普通用户组根据配置获取
        if ($adminAuth->getAdminRole()->getRoleCode() == AdminRole::ADMIN_ROLE_CODE) {
            $menuList = $this->getAdminMenuRepo()->findAll();
            if ($isOnlyId) {
                $menuIds = [];
                foreach ($menuList as $menu) {
                    $menuIds[] = $menu->getId();
                }
                return $menuIds;
            }
        } else {
            $roleMenuMapList = $this->getAdminRoleMenuMapRepo()->findOnAllByRoleId($adminAuth->getAdminUser()->getRoleId());
            if (empty($roleMenuMapList)) {
                throw new NotExistException('没有任何菜单权限');
            }
            $menuIds = [];
            foreach ($roleMenuMapList as $roleMenuMap) {
                $menuIds[] = $roleMenuMap->getMenuId();
            }
            if ($isOnlyId) {
                return $menuIds;
            }
            $menuList = $this->getAdminMenuRepo()->findAllByMenuIds($menuIds);
        }

        return $menuList;
    }

    /**
     * @param AdminAuth $adminAuth
     * @param AdminMenuRequest $adminMenuRequest
     * @return AdminMenu
     * @throws NotExistException|InvalidParamsException|NoAuthException
     */
    private function getChildOne(AdminAuth $adminAuth, AdminMenuRequest $adminMenuRequest): AdminMenu
    {
        $menuIds = $this->getUserMenuList($adminAuth);
        if (!in_array($adminMenuRequest->getId(), $menuIds)) {
            throw new NoAuthException('无权限查看的菜单！');
        }

        $menu = $this->getAdminMenuRepo()->findOneById($adminMenuRequest->getId());
        if (!$menu) {
            throw new NotExistException('未查询到菜单详情！');
        }

        if ($adminMenuRequest->getParentId()) {
            $childMenuIds = $this->getAdminMenuRepo()->findMultiAllByParentMenu($menu);
            if ($adminMenuRequest->getParentId() && in_array($adminMenuRequest->getParentId(), $childMenuIds)) {
                throw new InvalidParamsException('菜单上级不能设置为自己的下级！');
            }
        }

        return $menu;
    }

    /**
     * 获取当前登录用户前端可展示的菜单列表（status=on & type=menu），其中hidden提供给前端自行判断
     * @param AdminAuth $adminAuth
     * @return array
     * @throws NotExistException
     */
    public function getUserMenu(AdminAuth $adminAuth): array
    {
        $menuList = $this->getUserMenuList($adminAuth, false);
        if (empty($menuList)) {
            throw new NotExistException('查询菜单详情错误！');
        }

        # 获取用户信息
        $r = [];
        $r['username'] = $adminAuth->getAdminUser()->getUsername();
        $r['roles'] = [$adminAuth->getAdminRole()->getRoleName()];
        foreach ($menuList as $menu) {
            $r['menus'][] = $menu->getApiFormat();
        }

        return $r;
    }

    /**
     * 获取当前登录用户所有可访问的菜单列表
     * @param AdminAuth $adminAuth
     * @return array
     * @throws NotExistException
     * @throws ReflectionException
     */
    public function getMenuListWithFormat(AdminAuth $adminAuth): array
    {
        $menuList = $this->getUserMenuList($adminAuth, false);

        # 查询并格式化展示菜单信息
        return $this->getAdminMenuRepo()->findAllByMenuIdsWithFormat($menuList);
    }

    /**
     * @param AdminAuth $adminAuth
     * @param int $roleId
     * @return array
     * @throws NoAuthException
     * @throws NotExistException|ReflectionException
     */
    public function getMenuList(AdminAuth $adminAuth, int $roleId = 0): array
    {
        # 如果传入roleId，则查询指定roleId所有可访问的菜单列表
        if ($roleId) {
            $childRoleIds = $this->getAdminRoleRepo()->findMultiAllByParentRole($adminAuth->getAdminRole(), true);
            if (!in_array($roleId, $childRoleIds)) {
                throw new NoAuthException('没有权限编辑的用户组！');
            }
            $menuMapList = $this->getAdminRoleMenuMapRepo()->findOnAllByRoleId($roleId);

            $menuIds = [];
            foreach ($menuMapList as $roleMenuMap) {
                $menuIds[] = $roleMenuMap->getMenuId();
            }
            $menuList = $this->getAdminMenuRepo()->findAllByMenuIds($menuIds);
        } else {
            $menuList = $this->getUserMenuList($adminAuth, false);
        }

        $r = [];
        foreach ($menuList as $menu) {
            $r[] = $menu->getApiFormat();
        }

        return $r;
    }

    /**
     * @param AdminAuth $adminAuth
     * @param AdminMenuRequest $adminMenuRequest
     * @return array
     * @throws NoAuthException
     * @throws NotExistException
     * @throws ReflectionException|InvalidParamsException
     */
    public function getOneMenu(AdminAuth $adminAuth, AdminMenuRequest $adminMenuRequest): array
    {
        $menu = $this->getChildOne($adminAuth, $adminMenuRequest);

        return $menu->getApiFormat();
    }

    /**
     * @param AdminAuth $adminAuth
     * @param AdminMenuRequest $adminMenuRequest
     * @return array
     * @throws NoAuthException
     * @throws NotExistException
     * @throws ReflectionException
     */
    public function create(AdminAuth $adminAuth, AdminMenuRequest $adminMenuRequest): array
    {
        $menuIds = $this->getUserMenuList($adminAuth);

        # 权限校验
        if ($adminMenuRequest->getParentId() > 0 && !in_array($adminMenuRequest->getParentId(), $menuIds)) {
            throw new NoAuthException('无权限添加的菜单');
        }

        $menu = $this->getAdminMenuRepo()->findOneByPath($adminMenuRequest->getPath());
        if ($menu) {
            if ($menu->getStatus() == StatusEnum::ON) {
                throw new NotExistException('菜单已存在，无法重复新增！');
            }
        } else {
            $menu = new AdminMenu();
        }

        $em = $this->doctrine->getManager();

        # 添加菜单
        $menu->setWeight($adminMenuRequest->getWeight());
        $menu->setType($adminMenuRequest->getType());
        $menu->setStatus($adminMenuRequest->getStatus());
        $menu->setPath($adminMenuRequest->getPath());
        $menu->setParentId($adminMenuRequest->getParentId());
        $menu->setMenuName($adminMenuRequest->getMenuName());
        $menu->setIcon($adminMenuRequest->getIcon());
        $menu->setIsHidden($adminMenuRequest->getIsHidden());
        $em->persist($menu);
        $em->flush();

        # 添加菜单之后，给当前用户增加此菜单权限
        $adminRoleMenuMap = new AdminRoleMenuMap();
        $adminRoleMenuMap->setStatus(StatusEnum::ON);
        $adminRoleMenuMap->setMenuId($menu->getId());
        $adminRoleMenuMap->setRoleId($adminAuth->getAdminRole()->getId());
        $em->persist($adminRoleMenuMap);

        $em->flush();

        return $menu->getApiFormat();
    }

    /**
     * @param AdminAuth $adminAuth
     * @param array $menuIds
     * @param int $roleId
     * @return string
     * @throws NoAuthException
     * @throws NotExistException
     */
    public function updateRoleMenus(AdminAuth $adminAuth, int $roleId, array $menuIds): string
    {
        $childRoleIds = $this->getAdminRoleRepo()->findMultiAllByParentRole($adminAuth->getAdminRole(), true);
        if (!in_array($roleId, $childRoleIds)) {
            throw new NoAuthException('没有权限编辑的用户组！');
        }

        $allMenuIds = $this->getUserMenuList($adminAuth);
        $diffMenuIds = array_diff($menuIds, $allMenuIds);
        if (!empty($diffMenuIds)) {
            throw new NoAuthException('要分配的菜单没有权限使用！');
        }

        $adminRole = $this->getAdminRoleRepo()->findOneById($roleId);
        if (!$adminRole) {
            throw new NotExistException('指定用户组详情不存在！');
        }

        $em = $this->doctrine->getManager();

        # 先将所有状态置为关闭
        $roleMenuMapList = $this->getAdminRoleMenuMapRepo()->findOnAllByRoleId($roleId);
        $menuIdKeyMapList = [];
        foreach ($roleMenuMapList as $roleMenuMap) {
            $roleMenuMap->setStatus(StatusEnum::OFF);
            $em->persist($roleMenuMap);
            $menuIdKeyMapList[$roleMenuMap->getMenuId()] = $roleMenuMap;
        }

        foreach ($menuIds as $menuId) {
            if (empty($menuIdKeyMapList[$menuId])) {
                $roleMenuMap = new AdminRoleMenuMap();
                $roleMenuMap->setRoleId($roleId);
                $roleMenuMap->setMenuId($menuId);
            } else {
                $roleMenuMap = $menuIdKeyMapList[$menuId];
            }
            $roleMenuMap->setStatus(StatusEnum::ON);
            $em->persist($roleMenuMap);
        }

        $em->flush();
        return 'success';
    }
}
