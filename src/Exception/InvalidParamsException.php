<?php


namespace SymfonyAdmin\Exception;


use SymfonyAdmin\Exception\Base\ServiceException;

class InvalidParamsException extends ServiceException
{
    protected $code = 100400;
}