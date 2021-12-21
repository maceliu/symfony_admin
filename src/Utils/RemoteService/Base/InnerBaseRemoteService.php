<?php

namespace SymfonyAdmin\Utils\RemoteService\Base;

use SymfonyAdmin\Logger\SessionRequestProcessor;
use Psr\Log\LoggerInterface;

class InnerBaseRemoteService extends BaseRemoteService
{
    /**
     * 内网请求基类
     * @param string $baseUri
     * @param LoggerInterface $httpLogger
     * @param int $timeout
     */
    public function __construct(string $baseUri, LoggerInterface $httpLogger, int $timeout = 5)
    {
        parent::__construct($baseUri, $httpLogger, $timeout);
        $this->preQueryParam = [
            '_trace' => SessionRequestProcessor::getRequestId()
        ];
    }
}
