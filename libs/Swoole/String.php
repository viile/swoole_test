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

    function ipos($find_str)
    {
        return stripos($this->string, $find_str);
    }

    function lower()
    {
        return new String(strtolower($this->string));
    }

    function upper()
    {
        return new String(strtoupper($this->string));
    }

    function len()
    {
        return strlen($this->string);
    }

    function substr($offset, $length = null)
    {
        return new String(substr($this->string, $offset, $length));
    }

    function replace($search, $replace, &$count = null)
    {
        return new String(str_replace($search, $replace, $this->string, $count));
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

    function split($sp, $limit = null)
    {
        return new ArrayObject(explode($sp, $limit));
    }

    function toArray($splitLength = 1)
    {
        return new ArrayObject(str_split($this->string, $splitLength));
    }
}
