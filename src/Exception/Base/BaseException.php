<?php


namespace SymfonyAdmin\Exception\Base;

use Exception;
use Throwable;

/**
 * 代码类阻断抛出的异常，例如数据库连接失败、Redis连接失败、外部接口调用失败等
 */
class BaseException extends Exception
{
    protected $baseCode = 10;

    public function __construct($message = "", $code = 000, Throwable $previous = null)
    {
        parent::__construct($message, $this->baseCode . $code, $previous);
    }


    public function setExceptionCode($code)
    {
        $this->code = $this->baseCode . $code;
    }
}