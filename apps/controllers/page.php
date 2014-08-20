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
        $result = $this->db->query("show tables");
        var_dump($result->fetchall());
    }

    //缓存获取
    function cache_get()
    {
        $result = $this->cache->get("swoole_var_1");
        var_dump($result);
    }

    //缓存设置
    function cache_set()
    {
        $result = $this->cache->set("swoole_var_1", "swoole");
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
        $this->tpl->assign('my_var', 'swoole use smarty');
        $this->tpl->display('tpl_test.html');
    }

    function session_test()
    {
        $this->session->start();
        $_SESSION['hello'] = 'swoole';
    }

    function redirect()
    {
        $this->http->redirect('http://www.baidu.com');
        $_SESSION['hello'] = 'swoole';
    }

    function http_header()
    {
        $this->http->status(302);
        $this->http->header('Location', 'http://www.baidu.com');
        $_SESSION['hello'] = 'swoole';
    }

    function session_read()
    {
        $this->session->start();
        var_dump($_SESSION);
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

    function post()
    {
        var_dump($_POST);
    }

    //exit or die
    function exit_php()
    {
        $this->http->finish("die.");
    }

    function js_shell()
    {
        echo <<<HTML
<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>多玩模调统计平台</title>
    <script src="http://www.duowan.com/public/assets/sys/js/jquery.js"></script>
    <script type="text/javascript" src="http://www.duowan.com/public/assets/sys/js/udb.v1.0.js"></script>
    <script type="text/javascript">$(function () {
            Navbar.login("/page/login/");
        });</script>
</head>
<body></body>
</html>
HTML;
    }
}