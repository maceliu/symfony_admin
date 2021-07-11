<?php


namespace SymfonyAdmin\Exception;


use SymfonyAdmin\Exception\Base\ServiceException;

class CheckFailException extends ServiceException
{
    protected $code = 10100;
}