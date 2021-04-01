<?php


namespace SymfonyAdmin\Response;


use SymfonyAdmin\Exception\Base\ErrorException;
use SymfonyAdmin\Exception\Base\ServiceException;
use SymfonyAdmin\Exception\NotExistException;
use SymfonyAdmin\Exception\NotLoginException;
use SymfonyAdmin\Exception\RedirectException;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse extends JsonResponse
{
    const CODE = 'code';
    const MESSAGE = "message";
    const DATA = "data";


    /**
     * @param array|string $content
     * @param string $message
     * @return ApiResponse
     */
    public static function success($content = [], string $message = 'success'): ApiResponse
    {
        return new self([
            self::CODE => 200,
            self::MESSAGE => $message,
            self::DATA => $content,
        ], 200);
    }

    /**
     * @param string $message
     * @param int $status
     * @return ApiResponse
     */
    public static function fail(string $message, $status = 501): ApiResponse
    {
        return new self([
            self::CODE => $status,
            self::MESSAGE => $message,
            self::DATA => '',
        ], 200);
    }

    /**
     * @param Exception $e
     * @param LoggerInterface $errorLogger
     * @return ApiResponse
     */
    public static function exception(Exception $e, LoggerInterface $errorLogger): ApiResponse
    {
        $errorDetail = 'Message : ' . $e->getMessage()
            . ' | File : ' . $e->getFile()
            . ' | On Line : ' . $e->getLine();

        $r = [
            self::CODE => 500,
            self::MESSAGE => $e->getMessage(),
            self::DATA => '',
        ];

        if ($e instanceof NotLoginException) {
            # 未登录
            $r = [
                self::CODE => 4000,
                self::MESSAGE => $e->getMessage() ?? '用户未登录',
                self::DATA => '',
            ];
        } elseif ($e instanceof NotExistException) {
            # 不存在，返回首页
            $r[self::CODE] = 301;
        } elseif ($e instanceof RedirectException) {
            # 跳转错误
            $r[self::CODE] = 302;
            $r[self::DATA] = $e->getGoPath();
        } elseif ($e instanceof ServiceException) {
            # 业务类错误处理
            $r[self::CODE] = 400;
        } else if ($e instanceof ErrorException) {
            # 代码类错误处理
            $r[self::DATA] = $_ENV['IS_DEBUG'] ? $errorDetail : '';
        } else {
            # 其他未预期的错误处理
            $r[self::DATA] = $_ENV['IS_DEBUG'] ? $errorDetail : '';
            $errorDetail .= ' | Trace : ' . $e->getTraceAsString();
            $errorLogger->error($errorDetail);
        }

        return new self($r, 200);
    }

}
