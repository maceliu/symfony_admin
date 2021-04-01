<?php


namespace SymfonyAdmin\Request\Base;


abstract class BaseRequest
{
    /**
     * @return string
     */
    public function __toString(): string
    {
        $str = '';
        foreach ($this as $v) {
            $str .= '_' . $v;
        }
        return $str;
    }
}