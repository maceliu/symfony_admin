<?php


namespace SymfonyAdmin\Entity;


class AdminAuth
{
    /** @var AdminUser */
    protected $adminUser;

    /** @var AdminRole */
    protected $adminRole;

    /** @var string */
    protected $routePath;

    /**
     * @return AdminUser
     */
    public function getAdminUser(): AdminUser
    {
        return $this->adminUser;
    }

    /**
     * @param AdminUser $adminUser
     */
    public function setAdminUser(AdminUser $adminUser): void
    {
        $this->adminUser = $adminUser;
    }

    /**
     * @return AdminRole
     */
    public function getAdminRole(): AdminRole
    {
        return $this->adminRole;
    }

    /**
     * @param AdminRole $adminRole
     */
    public function setAdminRole(AdminRole $adminRole): void
    {
        $this->adminRole = $adminRole;
    }

    /**
     * @return string
     */
    public function getRoutePath(): string
    {
        return $this->routePath;
    }

    /**
     * @param string $routePath
     */
    public function setRoutePath(string $routePath): void
    {
        $this->routePath = $routePath;
    }


}
