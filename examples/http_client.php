<?php
define('DEBUG', 'on');
define('WEBPATH', dirname(__DIR__));
require WEBPATH . '/libs/lib_config.php';

$client = new Swoole\Async\HttpClient('http://www.baidu.com/');
$client->onReady(function($cli, $body, $header){
    var_dump($body, $header);
});
$client->execute();