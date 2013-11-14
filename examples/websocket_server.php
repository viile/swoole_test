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
Swoole\Error::$echo_html = false;

$AppSvr = new WebSocket();
$AppSvr->loadSetting(__DIR__."/swoole.ini"); //加载配置文件
$AppSvr->setLogger(new \Swoole\Log\EchoLog(true)); //Logger

/**
 * 如果你没有安装swoole扩展，这里还可选择
 * BlockTCP 阻塞的TCP，支持windows平台
 * SelectTCP 使用select做事件循环，支持windows平台
 * EventTCP 使用libevent，需要安装libevent扩展
 */
$server = new \Swoole\Network\Server('0.0.0.0', 9503);
$server->setProtocol($AppSvr);
//$server->daemonize(); //作为守护进程
$server->run(array('worker_num' => 1, 'max_request' => 5000));
