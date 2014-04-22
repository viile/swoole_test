<?php
define('DEBUG', 'on');
require __DIR__ . '/../../libs/lib_config.php';
/**
 * 如果想要支持IE浏览器，需要开启flash-websocket
 */
$AppSvr = new Swoole\Network\Protocol\FlashPolicy();
/**
 * 如果你没有安装swoole扩展，这里还可选择
 * Swoole\Network\BlockTCP 阻塞的TCP，支持windows平台 (c1)
 * Swoole\Network\SelectTCP 使用select做事件循环，支持windows平台 (c1000)
 * Swoole\Network\EventTCP 使用libevent，需要安装libevent扩展 (c10000)
 */
$server = new \Swoole\Network\Server('0.0.0.0', 843);
$server->setProtocol($AppSvr);
$server->daemonize(); //作为守护进程
$server->run(array('worker_num' => 1, 'max_request' => 5000));