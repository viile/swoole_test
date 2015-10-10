<?php
namespace App\Controller;

use Swoole;

class Event extends Swoole\Controller
{
    function test()
    {
        $res = $this->event->dispatch("App\\Test::hello", "hello world");
        if ($res)
        {
            echo "dispatch success\n";
        }
    }
}