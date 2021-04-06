<?php


namespace SymfonyAdmin\Service;


use SymfonyAdmin\Entity\AdminAuth;
use SymfonyAdmin\Entity\AdminRole;
use SymfonyAdmin\Exception\NotExistException;
use SymfonyAdmin\Exception\NotLoginException;
use SymfonyAdmin\Utils\Cache\Keys;
use SymfonyAdmin\Utils\Enum\StatusEnum;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use SymfonyAdmin\Service\Base\BaseService;

class AdminAuthService extends BaseService
{
    /** @var Request */
    protected $request;

    /** @var int */
    static public $loginUserId = 0;

    public function __construct(ManagerRegistry $doctrine, RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
        parent::__construct($doctrine);
    }

    /**
     * @return AdminAuth
     * @throws NotExistException
     * @throws NotLoginException
     */
    public function getLoginAuthInfo(): AdminAuth
    {
        $this->loginCheck();
        return $this->authCheck();
    }

    /**
     * @return bool
     * @throws NotLoginException
     */
    private function loginCheck(): bool
    {
        $loginUserId = intval($this->request->get('_loginUserId', 0));
        $accessToken = trim($this->request->headers->get('authorization', ''));
        # 登录状态验证
        if ($loginUserId && $accessToken) {
            $token = $this->getRedisClient()->get(Keys::adminUserLogin($loginUserId));
            if ($token && $token === $accessToken) {
                self::$loginUserId = $loginUserId;
                # 验证通过 刷新本次登录有效期
                $this->getRedisClient()->expire(Keys::adminUserLogin($loginUserId), Keys::USER_LOGIN_EXPIRE_TIME);
                return true;
            }
        }

        throw new NotLoginException('用户未登录');
    }

    /**
     * @return AdminAuth
     * @throws NotExistException
     * @throws NotLoginException
     */
    public function getOpenApiAuthInfo(): AdminAuth
    {
        $this->openApiCheck();
        return $this->authCheck();
    }

    /**
     * @return bool
     * @throws NotLoginException
     */
    private function openApiCheck(): bool
    {
        $accessToken = trim($this->request->get('accessToken', ''));
        # 登录状态验证
        if ($accessToken) {
            $loginUserId = $this->getRedisClient()->get(Keys::openApiToken($accessToken));
            if ($loginUserId) {
                self::$loginUserId = $loginUserId;
                # 验证通过 刷新本次登录有效期
                $this->getRedisClient()->expire(Keys::openApiToken($accessToken), Keys::OPEN_API_TOKEN_EXPIRE_TIME);
                return true;
            }
        }

        throw new NotLoginException('用户未登录');
    }

    /**
     * @throws NotExistException
     * @throws NotLoginException
     */
    private function authCheck(): AdminAuth
    {
        # 获取当前登录用户组信息
        $adminUser = $this->getAdminUserRepo()->findOneById(self::$loginUserId);
        if (!$adminUser || !$adminUser->getAdminRole()->getId()) {
            throw new NotLoginException('用户信息未查到，或用户未配置用户组');
        }

        # 非超级管理员，进行权限验证
        if ($adminUser->getAdminRole()->getRoleCode() !== AdminRole::ADMIN_ROLE_CODE) {
            # 查询菜单信息
            $adminMenu = $this->getAdminMenuRepo()->findOneByPath($this->request->getPathInfo());
            if (!$adminMenu) {
                throw new NotExistException('权限错误！所访问菜单配置不存在！');
            }

            if ($adminMenu->getStatus() === StatusEnum::OFF) {
                throw new NotExistException('权限错误！所访问菜单未启用！');
            }

            # 非公共菜单，进行权限校验
            if (!$adminMenu->getIsPublic()) {
                $userMenuMap = $this->getAdminRoleMenuMapRepo()->findOnOneByRoleIdAndMenuId($adminUser->getRoleId(), $adminMenu->getId());
                if (!$userMenuMap) {
                    throw new NotExistException('权限错误！无权限访问的资源！');
                }
            }
        }

        # 完成验证，生成权限实体
        $adminAuth = new AdminAuth();
        $adminAuth->setAdminUser($adminUser);
        $adminAuth->setAdminRole($adminUser->getAdminRole());
        $adminAuth->setRoutePath($this->request->getPathInfo());

        return $adminAuth;
    }
}
