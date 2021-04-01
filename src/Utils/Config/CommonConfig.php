<?php


namespace SymfonyAdmin\Utils\Config;


class CommonConfig
{
    /**
     * @return string
     */
    public static function getCdnHost(): string
    {
        return $_ENV['CDN_HOST'];
    }

    /**
     * @return string
     */
    public static function getDefaultAvatar(): string
    {
        return '/uploads/20200523/1239e49358ec1eb13218fabe0c2254c7.jpeg';
    }
}