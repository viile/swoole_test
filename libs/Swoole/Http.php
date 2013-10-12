<?php
namespace Swoole;

/**
 * Class Http_LAMP
 * @package Swoole
 */
class Http_PWS
{
    function header($k, $v)
    {
        $k = ucwords($k);
        \Swoole::$php->response->send_head($k,$v);
    }
    function status($code)
    {
        \Swoole::$php->response->send_http_status($code);
    }
    function response($content)
    {
        global $php;
        $php->response->body = $content;
        self::finish();
    }
    function redirect($url,$mode=301)
    {
        \Swoole::$php->response->send_http_status($mode);
        \Swoole::$php->response->send_head('Location',$url);
    }

    function finish()
    {
        \Swoole::$php->request->finish = 1;
        throw new \Exception;
    }
}

class Http_LAMP
{
    function header($k,$v)
    {
        header($k.':'.$v);
    }
    function status($code)
    {
        header('HTTP/1.1 '.\Swoole\Response::$HTTP_HEADERS[$code]);
    }
    function redirect($url,$mode=301)
    {
        \Swoole_client::redirect($url,$mode);
    }
    function finish()
    {
        if(function_exists('fastcgi_finish_request'))
        {
            fastcgi_finish_request();
        }
    }
}

class Http
{
    static function __callStatic($func, $params)
    {
        if(defined('SWOOLE_SERVER'))
        {
            return call_user_func_array("\\Swoole\\Http_PWS::{$func}", $params);
        }
        else
        {
            return call_user_func_array("\\Swoole\\Http_LAMP::{$func}", $params);
        }
    }
}

