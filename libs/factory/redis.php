<?php
global $php;
$redis = new Redis();
$redis->connect($php->config['redis']['master']["host"],$php->config['redis']['master']["port"]);
