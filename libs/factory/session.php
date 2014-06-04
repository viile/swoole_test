<?php
if (!empty($php->config['session']['use_swoole_sesion']) or defined('SWOOLE_SERVER'))
{
    $cache = \Swoole\Factory::getCache('session');
    $session = new Swoole\Session($cache);
    $session->use_php_session = false;
}
else
{
    $session = new Swoole\Session;
}
return $session;