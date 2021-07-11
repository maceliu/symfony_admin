<?php


namespace SymfonyAdmin\Exception;


use SymfonyAdmin\Exception\Base\ServiceException;

/**
 * 没有权限访问
 * Class NoAuthException
 * @package App\Exception
 */
class NoAuthException extends ServiceException
{
    protected $code = 10500;
}