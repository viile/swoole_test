<?php
namespace App\Controller;
use Swoole;

class Db extends Swoole\Controller
{
    function apt_test()
    {
        $apt = new Swoole\SelectDB($this->db);
        $apt->from('users');
        $apt->equal('id', 1);
        $res = $apt->getall();
        var_dump($res);
    }

    function put()
    {
        $model = Model('User');
        $model->put(array('name' => 'swoole', 'level' => 5, 'mobile' => '19999990000'));
    }
}