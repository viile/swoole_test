<?php
class ajax extends Swoole\Controller
{
    public $is_ajax = true;

    function test()
    {
        return array('json' => 'swoole');
    }
}