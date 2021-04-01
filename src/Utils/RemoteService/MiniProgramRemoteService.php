<?php


namespace App\Utils\RemoteService;


use App\Utils\RemoteService\Base\BaseRemoteService;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use App\Exception\Base\ErrorException;

class MiniProgramRemoteService extends BaseRemoteService
{

    /**
     * MiniProgramRemoteService constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct($_ENV['WECHAT_BASE_URI'], $logger);

    }

    /**
     * @param string $accessToken
     * @param string $template_id
     * @param string $url
     * @param string $toUser
     * @param array $data
     * @return bool
     * @throws ErrorException
     */
    public function sendUniformMessage(string $accessToken, string $template_id, string $url, string $toUser, array $data): bool
    {
        $options = [
            RequestOptions::BODY => json_encode([
                'touser' => $toUser,
                'appid' => $_ENV['PUBLIC_APPID'],
                'template_id' => $template_id,
                'url' => $url,
                'miniprogram' => [
                    "appid" => $_ENV['ROUTINE_APPID'],
                    "pagepath" => "index/index"
                ],
                'data' => $data,
            ]),
        ];
        $responseArr = $this->getResponseContent('POST', '/cgi-bin/message/wxopen/template/uniform_send?access_token=' . $accessToken, $options);
        if ($responseArr['errcode'] != 0) {
            $this->setReturnMsg($responseArr['errmsg']);
            $this->httpLogger->error($this->getReturnMsg());
            throw new ErrorException($this->getReturnMsg());
        }
        return true;
    }

    /**
     * @param string $code
     * @param LoggerInterface $errorLogger
     * @return array
     * @throws ErrorException
     */
    public function getSessionKeyAndOpenIdByCode(string $code, LoggerInterface $errorLogger): array
    {
        $lanRemoteService = new LanRemoteService($errorLogger);
        $miniSecret = $lanRemoteService->getMiniProgramSecret();
        $options = [
            RequestOptions::QUERY => [
                'appid' => $_ENV['ROUTINE_APPID'],
                'secret' => $miniSecret,
                'js_code' => $code,
                'grant_type' => 'authorization_code'
            ],
        ];
        $responseArr = $this->getResponseContent('GET', '/sns/jscode2session', $options);
        if (!isset($responseArr['session_key'])) {
            $this->setReturnMsg($responseArr['errmsg']);
            $this->httpLogger->error($this->getReturnMsg());
            throw new ErrorException($this->getReturnMsg());
        }
        return $responseArr;
    }

    /**
     * @param $response
     * @return array
     */
    public function afterResponse($response): array
    {
        $responseArr = json_decode($response, true);
        return $responseArr;
    }


}