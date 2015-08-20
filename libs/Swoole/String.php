<?php
namespace Swoole;

class String
{
    protected $string;

    function __construct($string)
    {
        $this->string = $string;
    }

    function __toString()
    {
        return $this->string;
    }

    function pos($find_str)
    {
        return strpos($this->string, $find_str);
    }

    function rpos($find_str)
    {
        return strrpos($this->string, $find_str);
    }

    function substr($offset, $length = null)
    {
        return new String(substr($this->string, $offset, $length));
    }

    function  startWith($needle)
    {
        return strpos($this->string, $needle) === 0;
    }

    function endWith($needle)
    {
        $length = strlen($needle);
        if ($length == 0)
        {
            return true;
        }
        return (substr($this->string, -$length) === $needle);
    }
}