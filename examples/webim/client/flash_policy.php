<?php
define('DEBUG', 'on');
require __DIR__ . '/../../libs/lib_config.php';
$AppSvr = new Swoole\Network\Protocol\FlashPolicy();
$server = new \Swoole\Network\Server('0.0.0.0', 843);
$server->setProtocol($AppSvr);
$server->daemonize(); //作为守护进程
$server->run(array('worker_num' => 1, 'max_request' => 5000));