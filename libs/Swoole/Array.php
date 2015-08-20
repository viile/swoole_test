<?php
namespace Swoole;

class ArrayObject implements \Countable
{
    protected $array;

    function __construct($array)
    {
        $this->array = $array;
    }

    function contains($val)
    {
        return in_array($val, $this->array);
    }

    function join($str)
    {
        return new String(implode($str, $this->array));
    }

    function insert($offset, $val)
    {
        if ($offset > count($this->array))
        {
            return false;
        }
        return array_splice($this->array, $offset, 0, $val);
    }

    function search($find)
    {
        return array_search($find, $this->array);
    }

    function count()
    {
        return count($this->array);
    }

    function append($val)
    {
        return array_push($this->array, $val);
    }

    function prepend($val)
    {
        return array_unshift($this->array, $val);
    }

    function slice($offset, $lenth = null)
    {
        return new ArrayObject(array_slice($this->array, $offset, $lenth));
    }

    function toArray()
    {
        return $this->array;
    }
}

$ar = new ArrayObject(['a', 'b', 'c']);
$ar->insert(1, '_');
var_dump($ar->toArray());

