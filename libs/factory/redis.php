<?php
global $php;

if ($php->factory_key == 'master')
{
    $config = $php->config['redis']['master'];

    if (empty($config['host']))
    {
        $config['host'] = '127.0.0.1';
    }
}
else
{
    $config = $php->config['redis'][$php->factory_key];
    if (empty($config) or empty($config['host']))
    {
        throw new Exception("redis require server host ip.");
    }
}

if (empty($config["port"]))
{
    $config["port"] = 6379;
}

$redis = new Redis();
$redis->connect($config["host"], $config["port"]);

if (!empty($config['database']))
{
    $redis->select($config['database']);
}
return $redis;