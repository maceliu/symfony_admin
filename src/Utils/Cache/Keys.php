<?php


namespace SymfonyAdmin\Utils\Cache;


use SymfonyAdmin\Utils\CommonUtils;

class Keys
{
    const TEN_MIN_CACHE_TIME = 600;

    const USER_LOGIN_EXPIRE_TIME = 86400 * 30;

    const OPEN_API_TOKEN_EXPIRE_TIME = 86400;

    const CHECK_CODE = 'checkCode';

    const COUNT = 'count';

    static function adminUserLogin(int $userId): string
    {
        return CommonUtils::getAppName() . '_admin_user_login_info_' . $userId;
    }

    static function openApiToken(string $accessToken): string
    {
        return CommonUtils::getAppName() .'_admin_open_api_access_token_' . $accessToken;
    }

    static function passwordCheckCode(int $userId): string
    {
        return CommonUtils::getAppName() .'_admin_find_my_pass_' . $userId;
    }
}
