<?php
define('DEBUG', 'on');
define("WEBPATH", realpath(__DIR__ . '/../'));
require __DIR__ . '/../../libs/lib_config.php';

Swoole\Loader::setRootNS('WebIM', __DIR__.'/server/');

$webim = new WebIM\Server();
$webim->loadSetting(__DIR__."/../swoole.ini"); //加载配置文件
$webim->setLogger(new \Swoole\Log\EchoLog(true)); //Logger
/**
 * 使用文件或redis存储聊天信息
 */
$webim->setStore(new WebIM\Store\File(__DIR__.'/data/'));

/**
 * webim必须使用swoole扩展
 */
$server = new \Swoole\Network\Server('0.0.0.0', 9503);
$server->setProtocol($webim);
//$server->daemonize(); //作为守护进程
$server->run(array(
    'worker_num' => 4,
    'max_request' => 100000,
    'task_worker_num' => 1,
));
