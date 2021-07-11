<?php


namespace SymfonyAdmin\Exception;


use SymfonyAdmin\Exception\Base\ServiceException;

class ExceedLimitException extends ServiceException
{
    protected $code = 10300;
}