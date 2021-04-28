<?php


namespace SymfonyAdmin\Utils\Cache;


use Redis;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class RedisProvider
{
    /** @var Redis  */
    static $connection = null;

    /**
     * @return Redis
     */
    public static function getConnect(): ?Redis
    {
        if (empty(self::$connection)) {
            self::$connection = RedisAdapter::createConnection($_ENV['REDIS_URL']);
        }

        self::$connection->_prefix($_ENV['APP_NAME']);
        return self::$connection;
    }
}