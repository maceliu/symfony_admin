<?php


namespace SymfonyAdmin\Utils\Cache;


class Keys
{
    const TEN_MIN_CACHE_TIME = 600;

    const USER_LOGIN_EXPIRE_TIME = 86400 * 30;

    const OPEN_API_TOKEN_EXPIRE_TIME = 86400;

    static function adminUserLogin($userId): string
    {
        return 'calendar_admin_user_login_info_' . $userId;
    }

    static function openApiToken(string $accessToken): string
    {
        return 'calendar_admin_open_api_access_token_' . $accessToken;
    }

    static function wxLoginToken(string $userId): string
    {
        return 'calendar_wx_login_token' . $userId;
    }
}
