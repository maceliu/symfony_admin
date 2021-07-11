<?php


namespace SymfonyAdmin\Exception;


use SymfonyAdmin\Exception\Base\ServiceException;

class WrongStatusException extends ServiceException
{
    protected $code = 10800;
}