<?php


namespace App\Utils;

use EasyWeChat\Factory;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\Exceptions\DecryptException;
use EasyWeChat\MiniProgram\Application;

class MiniProgramUtils
{
    /** @var null */
    private static $instance = null;

    /**
     * @return array
     */
    public static function options(): array
    {
        return [
            'app_id' => trim($_ENV['ROUTINE_APP_ID']),
            'secret' => trim($_ENV['ROUTINE_APP_SECRET']),
            'token' => trim($_ENV['WECHAT_TOKEN']),
            'aes_key' => trim($_ENV['WECHAT_ENCODINGAES_KEY'])
        ];
    }

    /**
     * @return Factory|null
     */
    public static function application(): ?Factory
    {
        (self::$instance === null) && (self::$instance = new Factory());
        return self::$instance;
    }

    /**
     * @return Application
     */
    public static function miniProgram(): Application
    {
        return self::application()::miniProgram(self::options());
    }

    /**
     * @param string $code
     * @return array
     * @throws InvalidConfigException
     */
    public static function getUserInfo(string $code): array
    {
        return self::miniProgram()->auth->session($code);
    }

    /**
     * @param $sessionKey
     * @param $iv
     * @param $encryptData
     * @return array
     * @throws DecryptException
     */
    public static function encryptor($sessionKey, $iv, $encryptData): array
    {
        return self::miniProgram()->encryptor->decryptData($sessionKey, $iv, $encryptData);
    }

    /**
     * @param string $toUser
     * @param string $tempId
     * @param array $data
     * @return array
     * @throws InvalidConfigException
     * @throws InvalidArgumentException
     */
    public static function subscribe(string $toUser, string $tempId, array $data): array
    {
        $data = [
            'touser' => $toUser,
            'template_id' => $tempId,
            'data' => $data,
        ];
        return self::miniProgram()->subscribe_message->send($data);
    }

    /**
     * @param string $toUser
     * @param string $tempId
     * @param array $data
     * @return array
     * @throws InvalidConfigException
     * @throws InvalidArgumentException
     */
    public static function uniformMessage(string $toUser, string $tempId, array $data): array
    {
        $data = [
            'touser' => $toUser,
            'mp_template_msg' => [
                'appid' => 'wx0c1a1664441c4dd7',
                'template_id' => $tempId,
                'url' => 'http://xianglin.cn',
                'miniprogram' => [
                    "appid" => "wxa31f68973f13197b",
                    "pagepath" => "index/index"
                ],
                'data' => $data,
            ]
        ];
        return self::miniProgram()->uniform_message->send($data);
    }

}