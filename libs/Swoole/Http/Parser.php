<?php
namespace Swoole\Http;

/**
 * Class ExtParser
 * 使用pecl_http扩展
 * @package Swoole\Http
 */
class Parser implements \Swoole\IFace\HttpParser
{
    /**
     * 头部解析
     * @param $data
     * @return array
     */
    function parseHeader($data)
    {
        $header = array();
        $header[0] = array();
        $meta = &$header[0];
        $parts = explode("\r\n\r\n", $data, 2);
        // parts[0] = HTTP头;
        // parts[1] = HTTP主体，GET请求没有body
        $headerLines = explode("\r\n", $parts[0]);
        // HTTP协议头,方法，路径，协议[RFC-2616 5.1]
        list($meta['method'], $meta['uri'], $meta['protocol']) = explode(' ', $headerLines[0], 3);

        //$this->log($headerLines[0]);
        //错误的HTTP请求
        if (empty($meta['method']) or empty($meta['uri']) or empty($meta['protocol']))
        {
            return false;
        }
        unset($headerLines[0]);
        //解析Header
        foreach ($headerLines as $_h)
        {
            $_h = trim($_h);
            if (empty($_h)) continue;
            list($key, $value) = explode(':', $_h, 2);
            $header[trim($key)] = trim($value);
        }
        return $header;
    }
    function parseBody($request)
    {
        $params = array();
        $cd = strstr($request->head['Content-Type'], 'boundary');
        if (isset($request->head['Content-Type']) and $cd !== false)
        {
            $this->parseFormData($request->body, $request, $cd);
        }
        else parse_str($request->body, $params);
        return $params;
    }
    /**
     * 解析Cookies
     * @param $request \Swoole\Request
     */
    function parseCookie($request)
    {
        $_cookies = array();
        $blocks = explode(";", $request->head['Cookie']);
        foreach ($blocks as $cookie)
        {
            list ($key, $value) = explode("=", $cookie);
            $_cookies[trim($key)] = trim($value, "\r\n \t\"");
        }
        return $_cookies;
    }

    /**
     * 解析form_data格式文件
     * @param $part
     * @param $request
     * @param $cd
     * @return unknown_type
     */
    protected function parseFormData($part, $request, $cd)
    {
        $cd = '--' . str_replace('boundary=', '', $cd);
        $form = explode($cd, $part);
        foreach ($form as $f)
        {
            if ($f === '') continue;
            $parts = explode("\r\n\r\n", $f);
            $head = $this->parseHeader($parts[0]);
            if (!isset($head['Content-Disposition'])) continue;
            $meta = $this->parseCookie($head['Content-Disposition']);
            if (!isset($meta['filename']))
            {
                //checkbox
                if (substr($meta['name'], -2) === '[]') $request->post[substr($meta['name'], 0, -2)][] = trim($parts[1]);
                else $request->post[$meta['name']] = trim($parts[1]);
            }
            else
            {
                $file = trim($parts[1]);
                $tmp_file = tempnam('/tmp', 'sw');
                file_put_contents($tmp_file, $file);
                if (!isset($meta['name'])) $meta['name'] = 'file';
                $request->file[$meta['name']] = array('name' => $meta['filename'],
                    'type' => $head['Content-Type'],
                    'size' => strlen($file),
                    'error' => UPLOAD_ERR_OK,
                    'tmp_name' => $tmp_file);
            }
        }
    }
}