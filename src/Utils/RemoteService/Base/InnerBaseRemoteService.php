<?php

namespace SymfonyAdmin\Utils\RemoteService\Base;

use SymfonyAdmin\Logger\SessionRequestProcessor;

class InnerBaseRemoteService extends BaseRemoteService
{
    protected function beforeRequest()
    {
        $this->preQueryParam = [
            '_trace' => SessionRequestProcessor::getRequestId()
        ];
        parent::beforeRequest();
    }
}
