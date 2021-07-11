<?php


namespace SymfonyAdmin\Exception;


use SymfonyAdmin\Exception\Base\ServiceException;

class NotExistException extends ServiceException
{
    protected $code = 10600;
}