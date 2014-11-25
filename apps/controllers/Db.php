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

    function tables()
    {
        $tables = $this->db->query("show tables")->fetchall();
        var_dump($tables);
    }

    function put()
    {
        $model = Model('User');
        $id = $model->put(array('name' => 'swoole', 'level' => 5, 'mobile' => '19999990000'));
        echo "insert id = $id\n";
    }
}