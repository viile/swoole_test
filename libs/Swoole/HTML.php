<?php
namespace Swoole;
/**
 * HTML DOM处理器
 * 用于处理HTML的内容，提供类似于javascript DOM一样的操作
 * 例如getElementById getElementsByTagName createElement等
 * @author Tianfeng.Han
 * @package SwooleSystem
 * @subpackage HTML
 *
 */
class HTML
{
    /**
     * 删除注释
     * @param $content
     * @return mixed
     */
    static function removeComment($content)
	{
	    return preg_replace('#<!--[^>]*-->#','',$content);
	}

    /**
     * 解析相对路径
     * @param $current
     * @param $url
     * @return string
     */
    static function  parseRelativePath($current, $url)
    {
        //非HTTP开头的，相对路径
        if (substr($url, 0, 7) !== 'http://' and substr($url, 0, 8) !== 'https://' )
        {
            //以/开头的
            if ($url[0] == '/')
            {
                $_u = parse_url($current);
                return $_u['scheme'].'://'.$_u['host'].$url;
            }
            else
            {
                $n = strrpos($current, '/');
                return substr($current, 0, $n + 1).$url;
            }
        }
        else
        {
            return $url;
        }
    }

    /**
     * 删除HTML中的某些标签
     * @param $html
     * @param array $rules
     * @return mixed
     */
    static function  removeTag($html, $rules = array('script', 'style'))
    {
        foreach($rules as $r)
        {
            $regx =  "~<{$r}[^>]*>.*</{$r}>~si";
            $html = preg_replace($regx, '', $html);
        }
        return $html;
    }

    /**
     * 删除HTML中的tag属性
     * @param $html
     * @param array $remove_attrs
     * @return mixed
     */
    static function removeAttr($html, $remove_attrs = array())
    {
        //删除所有属性
        if (!is_array($remove_attrs) or count($remove_attrs) == 0)
        {
            return preg_replace('~<([a-z]+)[^>]*>~i','<$1>', $html);
        }
        //删除部分指定的属性
        else
        {
            foreach($remove_attrs as $attr)
            {
                $regx = '~<([a-z]+[^>]*)('.$attr.'\s?=\s?[\'"]?[^\'^"]*[\'"]?)([^>]*)>~i';
                $html = preg_replace($regx,'<$1 $3>', $html);
            }
            return $html;
        }
    }
}