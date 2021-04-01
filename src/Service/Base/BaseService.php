<?php


namespace SymfonyAdmin\Service\Base;


use SymfonyAdmin\Entity\Admin\AdminFile;
use App\Entity\Admin\AdminMenu;
use App\Entity\Admin\AdminRole;
use App\Entity\Admin\AdminRoleMenuMap;
use App\Entity\Admin\AdminUser;
use App\Entity\Calendar;
use App\Entity\Holiday;
use App\Entity\HolidayDateMap;
use App\Entity\HolidayMarketingMap;
use App\Entity\MarketingPlan;
use App\Entity\MarketingPlanModule;
use App\Entity\PosterLibrary;
use App\Entity\WechatUserFeedBack;
use App\Entity\WechatUserFootprint;
use App\Exception\Base\ErrorException;
use App\Repository\Admin\AdminFileRepository;
use App\Repository\Admin\AdminMenuRepository;
use App\Repository\Admin\AdminRoleMenuMapRepository;
use App\Repository\Admin\AdminRoleRepository;
use App\Repository\Admin\AdminUserRepository;
use App\Repository\CalendarRepository;
use App\Repository\HolidayDateMapRepository;
use App\Repository\HolidayMarketingMapRepository;
use App\Repository\HolidayRepository;
use App\Repository\MarketingPlanModuleRepository;
use App\Repository\MarketingPlanRepository;
use App\Repository\PosterLibraryRepository;
use App\Repository\WechatUserFeedBackRepository;
use App\Repository\WechatUserFootprintRepository;
use App\Utils\Cache\Keys;
use App\Utils\Cache\RedisProvider;
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

    /**
     * @return CalendarRepository
     */
    protected function getCalendarRepo(): CalendarRepository
    {
        if (!isset($this->repoInstance[Calendar::class])) {
            $this->repoInstance[Calendar::class] = $this->doctrine->getRepository(Calendar::class);
        }
        return $this->repoInstance[Calendar::class];
    }

    /**
     * @return HolidayRepository
     */
    public function getHolidayRepo(): HolidayRepository
    {
        if (!isset($this->repoInstance[Holiday::class])) {
            $this->repoInstance[Holiday::class] = $this->doctrine->getRepository(Holiday::class);
        }
        return $this->repoInstance[Holiday::class];
    }

    /**
     * @return MarketingPlanRepository
     */
    protected function getMarketingPlanRepo(): MarketingPlanRepository
    {
        if (!isset($this->repoInstance[MarketingPlan::class])) {
            $this->repoInstance[MarketingPlan::class] = $this->doctrine->getRepository(MarketingPlan::class);
        }
        return $this->repoInstance[MarketingPlan::class];
    }

    /**
     * @return MarketingPlanModuleRepository
     */
    protected function getMarketingPlanModuleRepo(): MarketingPlanModuleRepository
    {
        if (!isset($this->repoInstance[MarketingPlanModule::class])) {
            $this->repoInstance[MarketingPlanModule::class] = $this->doctrine->getRepository(MarketingPlanModule::class);
        }
        return $this->repoInstance[MarketingPlanModule::class];
    }

    /**
     * @return HolidayDateMapRepository
     */
    protected function getHolidayDateMapRepo(): HolidayDateMapRepository
    {
        if (!isset($this->repoInstance[HolidayDateMap::class])) {
            $this->repoInstance[HolidayDateMap::class] = $this->doctrine->getRepository(HolidayDateMap::class);
        }
        return $this->repoInstance[HolidayDateMap::class];
    }

    /**
     * @return HolidayMarketingMapRepository
     */
    protected function getHolidayMarketingMapRepo(): HolidayMarketingMapRepository
    {
        if (!isset($this->repoInstance[HolidayMarketingMap::class])) {
            $this->repoInstance[HolidayMarketingMap::class] = $this->doctrine->getRepository(HolidayMarketingMap::class);
        }
        return $this->repoInstance[HolidayMarketingMap::class];
    }

    /**
     * @return PosterLibraryRepository
     */
    protected function getPosterLibraryRepo(): PosterLibraryRepository
    {
        if (!isset($this->repoInstance[PosterLibrary::class])) {
            $this->repoInstance[PosterLibrary::class] = $this->doctrine->getRepository(PosterLibrary::class);
        }
        return $this->repoInstance[PosterLibrary::class];
    }

    /**
     * @return WechatUserFootprintRepository
     */
    protected function getWechatFootprintRepo(): WechatUserFootprintRepository
    {
        if (!isset($this->repoInstance[WechatUserFootprint::class])) {
            $this->repoInstance[WechatUserFootprint::class] = $this->doctrine->getRepository(WechatUserFootprint::class);
        }
        return $this->repoInstance[WechatUserFootprint::class];
    }

    /**
     * @return WechatUserFeedBackRepository
     */
    protected function getWechatUserFeedBackRepo(): WechatUserFeedBackRepository
    {
        if (!isset($this->repoInstance[WechatUserFeedBack::class])) {
            $this->repoInstance[WechatUserFeedBack::class] = $this->doctrine->getRepository(WechatUserFeedBack::class);
        }
        return $this->repoInstance[WechatUserFeedBack::class];
    }
}
