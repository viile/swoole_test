<?php
$config = !empty(Swoole::$php->config['cache']['master']) ? Swoole::$php->config['cache']['master'] : array('type' => 'FileCache', 'cache_dir' => WEBPATH . '/cache/filecache');
$class = '\\Swoole\\Cache\\' . $config['type'];
$cache = new $class($config);