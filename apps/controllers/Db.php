<?php
namespace App\Controller;
use Swoole;

class DB extends Swoole\Controller
{
    function apt_test()
    {
        $apt = new Swoole\SelectDB($this->db);
        $apt->from('users');
        $apt->equal('id', 1);
        $res = $apt->getall();
        var_dump($res);
    }
}