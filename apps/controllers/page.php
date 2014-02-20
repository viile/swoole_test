<?php
class page extends Swoole\Controller
{
    //hello world
    function index()
    {
        return "default page";
    }

    //数据库测试
    function db_test()
    {
        $result = $this->swoole->db->query("show tables");
        var_dump($result->fetchall());
    }

    //缓存获取
    function cache_get()
    {
        $result = $this->swoole->cache->get("swoole_var_1");
        var_dump($result);
    }

    //缓存设置
    function cache_set()
    {
        $result = $this->swoole->cache->set("swoole_var_1", "swoole");
        if($result)
        {
            echo "cache set success. Key=swoole_var_1";
        }
        else
        {
            echo "cache set failed.";
        }
    }

    //使用smarty引擎
    function tpl_test()
    {
        $this->swoole->tpl->assign('my_var', 'swoole use smarty');
        $this->swoole->tpl->display('tpl_test.html');
    }

    //使用php直接作为模板
    function view_test()
    {
        $this->assign('my_var', 'swoole view');
        $this->display('view_test.tpl.php');
    }

    //class autoload
    function class_load()
    {
        App\Test::hello();
    }
}