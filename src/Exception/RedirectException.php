<?php


namespace SymfonyAdmin\Exception;


use SymfonyAdmin\Exception\Base\ServiceException;

class RedirectException extends ServiceException
{
    private $goPath = '/';

    public function __construct($message = "", $goPath = '')
    {
        parent::__construct($message);
        if ($goPath) {
            $this->setGoPath($goPath);
        }
    }

    /**
     * @return string
     */
    public function getGoPath(): string
    {
        return $this->goPath;
    }

    /**
     * @param string $goPath
     */
    public function setGoPath(string $goPath): void
    {
        $this->goPath = $goPath;
    }


}