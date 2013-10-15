<?php
define('DEBUG', 'on');
define("WEBPATH", str_replace("\\","/", __DIR__));
require __DIR__ . '/../libs/lib_config.php';

class WebSocket extends Swoole\Network\Protocol\WebSocket
{
    function onMessage($client_id, $ws)
    {
        $this->log("onMessage: ".$ws['message']);
        $this->send($client_id, "Hello world");
    }
}

//require __DIR__'/phar://swoole.phar';
Swoole\Config::$debug = true;
$AppSvr = new WebSocket();
$AppSvr->loadSetting("./swoole.ini"); //加载配置文件
$AppSvr->setLogger(new \Swoole\Log\EchoLog(true)); //Logger

Swoole\Error::$echo_html = false;
$server = new \Swoole\Network\Server('0.0.0.0', 9501);
$server->setProtocol($AppSvr);
//$server->daemonize(); //作为守护进程
$server->run(array('worker_num' => 1, 'max_request' => 5000));

