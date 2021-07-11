<?php


namespace SymfonyAdmin\Exception;


use SymfonyAdmin\Exception\Base\ServiceException;

class DataDuplicationException extends ServiceException
{
    protected $code = 100200;
}