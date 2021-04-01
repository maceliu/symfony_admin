<?php


namespace App\Utils\RemoteService;

use App\Exception\Base\ErrorException;
use App\Utils\RemoteService\Base\BaseRemoteService;
use Psr\Log\LoggerInterface;

class LanRemoteService extends BaseRemoteService
{
    /**
     * LanRemoteService constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct($_ENV['LAN_REMOTE_URL'], $logger);
    }

    /**
     * @return string
     * @throws ErrorException
     */
    public function getRemoteAccessToken(): string
    {

        $reponseArr = $this->getResponseContent('GET', '/accessToken/get?appName=SH_XL_CALENDAR', []);
        return $reponseArr['content']['accessToken'];
    }

    /**
     * @return array
     * @throws ErrorException
     */
    public function getMiniProgramSecret(): string
    {
        $responseArr = $this->getResponseContent('GET', '/appSecret/get?appName=SH_XL_CALENDAR', []);
        return $responseArr['content'];
    }

    /**
     * @param $response
     * @return array
     * @throws ErrorException
     */
    public function afterResponse($response): array
    {
        $responseArr = json_decode($response, true);
        if ($responseArr['status'] != 200 || empty($responseArr['content'])) {
            $this->setReturnMsg($responseArr['message']);
            $this->httpLogger->error($this->getReturnMsg());
            throw new ErrorException($responseArr['message']);
        }
        return $responseArr;
    }
}