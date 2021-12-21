<?php

namespace SymfonyAdmin\Logger;

use Symfony\Component\HttpFoundation\RequestStack;

class SessionRequestProcessor
{
    /** @var string */
    private static $requestId = '';

    /** @var null */
    private static $userId = null;

    /** @var RequestStack */
    private static $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        self::$requestStack = $requestStack;
    }

    /**
     * @param array $record
     * @return array|null
     */
    public function __invoke(array $record): ?array
    {
        $record['extra']['token'] = 'trace-' . self::getRequestId();
        $record['extra']['userId'] = 'userId-' . self::getUserId();
        return $record;
    }

    /**
     * @return string
     */
    public static function getRequestId(): string
    {
        if (empty(self::$requestId)) {
            if (self::$requestStack->getCurrentRequest()) {
                $traceId = self::$requestStack->getCurrentRequest()->get('_trace', '');
            }
            self::$requestId = !empty($traceId) ? $traceId : substr(md5(microtime() . rand(0, 1000)), 0, 20);
        }
        return self::$requestId;
    }

    /**
     * @return string
     */
    public static function getUserId(): string
    {
        if (is_null(self::$userId)) {
            self::$userId = self::$requestStack->getCurrentRequest() ? intval(self::$requestStack->getCurrentRequest()->get('_loginUserId', 0)) : 0;
        }
        return self::$userId;
    }
}