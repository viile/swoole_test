<?php
define('DEBUG', 'on');
define('WEBPATH', dirname(__DIR__));
require WEBPATH . '/libs/lib_config.php';

$config = array(
    'document_root' => WEBPATH,
    'worker_num' => 4,
    'max_request' => 5000,
    'log_file' => '/tmp/swoole.log',
);

Swoole::$php->runHttpServer('0.0.0.0', 9501, $config);
