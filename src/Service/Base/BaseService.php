<?php


namespace SymfonyAdmin\Service\Base;


use SymfonyAdmin\Entity\AdminFile;
use SymfonyAdmin\Entity\AdminMenu;
use SymfonyAdmin\Entity\AdminRole;
use SymfonyAdmin\Entity\AdminRoleMenuMap;
use SymfonyAdmin\Entity\AdminUser;
use SymfonyAdmin\Exception\Base\ErrorException;
use SymfonyAdmin\Repository\AdminFileRepository;
use SymfonyAdmin\Repository\AdminMenuRepository;
use SymfonyAdmin\Repository\AdminRoleMenuMapRepository;
use SymfonyAdmin\Repository\AdminRoleRepository;
use SymfonyAdmin\Repository\AdminUserRepository;
use SymfonyAdmin\Utils\Cache\Keys;
use SymfonyAdmin\Utils\Cache\RedisProvider;
use Doctrine\Persistence\ManagerRegistry;
use Redis;

class BaseService
{
    /** @var ManagerRegistry */
    protected $doctrine;

    /** @var Redis */
    protected $redisClient;

    protected $repoInstance = [];

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * 方法后带WithCache，自动给调用函数设置缓存方法，缓存时间默认10分钟
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws ErrorException
     */
    public function __call(string $name, array $arguments)
    {
        $nameArr = explode('With', $name);
        if ($_ENV['APP_ENV'] == 'dev') { # 开发环境不使用缓存
            return call_user_func_array(array($this, $nameArr[0]), $arguments);
        } elseif (!empty($nameArr[0]) && !empty($nameArr[1]) && $nameArr[1] == 'Cache' && method_exists($this, $nameArr[0])) {
            $redisKey = str_replace('\\', '_', self::class) . '_' . $nameArr[0] . '_' . implode('_', $arguments);
            $r = $this->getRedisClient()->get($redisKey);
            if (empty($r)) {
                $r = call_user_func_array(array($this, $nameArr[0]), $arguments);
                $this->getRedisClient()->set($redisKey, json_encode($r), Keys::TEN_MIN_CACHE_TIME);
            } else {
                $r = json_decode($r, true);
            }
            return $r;
        } else {
            throw new ErrorException('调用方法不存在！' . $name);
        }
    }

    /**
     * @return Redis
     */
    protected function getRedisClient(): Redis
    {
        if (!$this->redisClient) {
            $this->redisClient = RedisProvider::getConnect();
        }
        return $this->redisClient;
    }

    /**
     * @return AdminUserRepository
     */
    protected function getAdminUserRepo(): AdminUserRepository
    {
        if (!isset($this->repoInstance[AdminUser::class])) {
            $this->repoInstance[AdminUser::class] = $this->doctrine->getRepository(AdminUser::class);
        }
        return $this->repoInstance[AdminUser::class];
    }

    /**
     * @return AdminMenuRepository
     */
    protected function getAdminMenuRepo(): AdminMenuRepository
    {
        if (!isset($this->repoInstance[AdminMenu::class])) {
            $this->repoInstance[AdminMenu::class] = $this->doctrine->getRepository(AdminMenu::class);
        }
        return $this->repoInstance[AdminMenu::class];
    }

    /**
     * @return AdminRoleMenuMapRepository
     */
    protected function getAdminRoleMenuMapRepo(): AdminRoleMenuMapRepository
    {
        if (!isset($this->repoInstance[AdminRoleMenuMap::class])) {
            $this->repoInstance[AdminRoleMenuMap::class] = $this->doctrine->getRepository(AdminRoleMenuMap::class);
        }
        return $this->repoInstance[AdminRoleMenuMap::class];
    }

    /**
     * @return AdminRoleRepository
     */
    protected function getAdminRoleRepo(): AdminRoleRepository
    {
        if (!isset($this->repoInstance[AdminRole::class])) {
            $this->repoInstance[AdminRole::class] = $this->doctrine->getRepository(AdminRole::class);
        }
        return $this->repoInstance[AdminRole::class];
    }

    /**
     * @return AdminFileRepository
     */
    protected function getAdminFileRepo(): AdminFileRepository
    {
        if (!isset($this->repoInstance[AdminFile::class])) {
            $this->repoInstance[AdminFile::class] = $this->doctrine->getRepository(AdminFile::class);
        }
        return $this->repoInstance[AdminFile::class];
    }
}
