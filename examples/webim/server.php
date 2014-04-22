<?php
define('DEBUG', 'on');
define("WEBPATH", realpath(__DIR__ . '/../'));
require __DIR__ . '/../../libs/lib_config.php';
require __DIR__ . '/server/Store/File.php';
require __DIR__ . '/server/Server.php';

$AppSvr = new WebIM\Server();
$AppSvr->loadSetting(__DIR__."/../swoole.ini"); //加载配置文件
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
$server->run(array('worker_num' => 4, 'max_request' => 0));