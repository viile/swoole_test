<?php
define('DEBUG', 'on');
require __DIR__ . '/../libs/lib_config.php';
//require __DIR__'/phar://swoole.phar';
Swoole\Config::$debug = false;
$AppSvr = new Swoole\Network\Protocol\HttpServer();
$AppSvr->loadSetting(__DIR__.'/swoole.ini'); //加载配置文件
$AppSvr->setDocumentRoot(__DIR__);
$AppSvr->setLogger(new \Swoole\Log\EchoLog(true)); //Logger

Swoole\Error::$echo_html = false;

/**
 * 如果你没有安装swoole扩展，这里还可选择
 * BlockTCP 阻塞的TCP，支持windows平台
 * SelectTCP 使用select做事件循环，支持windows平台
 * EventTCP 使用libevent，需要安装libevent扩展
 */
$server = new \Swoole\Network\SelectTCP('0.0.0.0', 8888);

$server->setProtocol($AppSvr);
$server->daemonize(); //作为守护进程
$server->run(array('worker_num' => 8, 'max_request' => 5000, 'log_file' => '/tmp/swoole.log'));
