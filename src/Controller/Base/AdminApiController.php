<?php

namespace SymfonyAdmin\Controller\Base;

use SymfonyAdmin\Service\AdminAuthService;
use SymfonyAdmin\Utils\Cache\RedisProvider;
use Redis;

class AdminApiController extends BaseController
{

    /** @var Redis */
    protected $redisClient = null;

    /** @var AdminAuthService */
    protected $adminAuthService;

    /**
     * AdminController constructor.
     * @param AdminAuthService $adminAuthService
     */
    public function __construct(AdminAuthService $adminAuthService)
    {
        $this->redisClient = RedisProvider::getConnect();
        $this->adminAuthService = $adminAuthService;
        # 兼容前端H5跨域调用接口
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: X-Requested-With,Content-Type,Cache-Control,Pragma,Date,x-timestamp,Authorization,Token');
        header('Access-Control-Allow-Credentials: true');
    }
}
