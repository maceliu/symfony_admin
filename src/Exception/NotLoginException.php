<?php


namespace SymfonyAdmin\Exception;


use SymfonyAdmin\Exception\Base\ServiceException;

class NotLoginException extends ServiceException
{
    protected $code = 10900;
}