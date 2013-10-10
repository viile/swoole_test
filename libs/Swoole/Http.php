<?php
namespace Swoole;

if(defined('SWOOLE_SERVER'))
{
    \Swoole\Error::$stop = false;
    \Swoole_js::$return = true;

    class Http
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
            http_finish();
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
}
else
{
    class Http
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
}
