<?php

namespace App\Utils\RemoteService;

use App\Exception\Base\ErrorException;
use App\Utils\RemoteService\Base\BaseRemoteService;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;

class Dh3TRemoteService extends BaseRemoteService
{

    public function __construct(LoggerInterface $httpLogger)
    {
        parent::__construct($_ENV['DH3T_HOST'], $httpLogger);
    }

    /**
     * @param $response
     * @return array
     * @throws ErrorException
     */
    protected function afterResponse($response): array
    {
        $responseArr = json_decode($response, true);
        if (!isset($responseArr['result']) || $responseArr['result'] != '0') {
            $msg = $responseArr['desc'] ?? '接口调用失败';
            $this->setReturnMsg($msg);
            $this->httpLogger->error($this->getReturnMsg());
            throw new ErrorException($msg);
        }
        return $responseArr;
    }

    /**
     * @desc H5验签
     * @param string $mobile
     * @param int $code
     * @return bool
     * @throws ErrorException
     */
    public function submit(string $mobile, int $code): bool
    {
        $options = [
            RequestOptions::BODY => json_encode([
                'account' => $_ENV['DH3T_ACCOUNT'],
                'password' => $_ENV['DH3T_PASSWORD'],
                'phones' => $mobile,
                'template' => [
                    'id' => '2c90818966756c1f01669ea71ada0a17',
                    'variables' => [
                        [
                            'name' => '1',
                            'value' => '',
                        ],
                        [
                            'name' => '2',
                            'value' => '：' . $code . '，',
                        ],
                        [
                            'name' => '3',
                            'value' => '，',
                        ],
                        [
                            'name' => '4',
                            'value' => '5分钟',
                        ],
                        [
                            'name' => '5',
                            'value' => '。',
                        ],
                    ]
                ],
            ]),
        ];

        $responseArr = $this->getResponseContent('POST', '/json/sms/Submit', $options);
        if (!empty($responseArr['failPhones'])) {
            throw new ErrorException('验证码发送失败，请稍后重试');
        }

        return true;
    }

}
