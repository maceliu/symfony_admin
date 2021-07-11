<?php


namespace SymfonyAdmin\Exception\Base;


/**
 * 代码类阻断抛出的异常，例如数据库连接失败、Redis连接失败、外部接口调用失败等
 */
class ErrorException extends BaseException
{
    protected $code = 20;
}