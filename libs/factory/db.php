<?php
global $php;
$db = new Swoole\Database($php->config['db'][$php->factory_key]);
$db->connect();
return $db;
