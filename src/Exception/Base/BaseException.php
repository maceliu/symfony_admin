<?php


namespace SymfonyAdmin\Exception\Base;

use Exception;

/**
 * 代码类阻断抛出的异常，例如数据库连接失败、Redis连接失败、外部接口调用失败等
 */
class BaseException extends Exception
{
    protected $code = 10000;
}