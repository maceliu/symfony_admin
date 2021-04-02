<?php


namespace SymfonyAdmin\Utils\RemoteService;


use SymfonyAdmin\Exception\NoAuthException;
use OSS\Core\OssException as OssException;
use OSS\OssClient;

class AliOssRemoteService
{
    protected static $instance;

    /**
     * @return object
     * @throws OssException
     */
    private static function getInstance(): object
    {
        if (!self::$instance instanceof OssClient) {
            self::$instance = new OssClient($_ENV['ALI_OSS_KEY'], $_ENV['ALI_OSS_SECRET'], $_ENV['ALI_OSS_HOST']);
        }
        return self::$instance;
    }

    /**
     * @throws NoAuthException
     */
    public function clone()
    {
        throw new NoAuthException('Clone is not allowed!');
    }

    /**
     * @param $object
     * @param $file
     * @return mixed
     * @throws OssException
     */
    public static function uploadImgFile($object, $file)
    {
        return self::getInstance()->uploadFile($_ENV['ALI_OSS_BUCKET_NAME'], $object, $file);
    }

    /**
     * @param $object
     * @return mixed
     * @throws OssException
     */
    public static function fileDelete($object)
    {
        return self::getInstance()->deleteObject($_ENV['ALI_OSS_BUCKET_NAME'], $object);
    }
}