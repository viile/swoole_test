<?php
namespace Swoole\Log;
/**
 * 文件日志类
 * @author Tianfeng.Han
 *
 */
class FileLog extends \Swoole\Log implements \Swoole\IFace\Log
{
    protected $log_file;
    protected $fp;

	function __construct($conf)
    {
        if (isset($conf['file']))
        {
            $this->log_file = $conf['file'];
        }
        else
        {
            throw new \Exception(__CLASS__.": require \$conf[file]");
        }

        $this->fp = fopen($this->log_file, 'a+');
        if (!$this->fp)
        {
            throw new \Exception(__CLASS__.": can not open log_file[$this->log_file]");
        }
    }

	/**
	 * 写入日志
	 * @param $type 事件类型
	 * @param $msg  信息
	 * @return bool
	 */
    function put($msg, $level = self::INFO)
    {
    	$msg = self::format($msg, $level);
    	return fputs($this->fp, $msg);
    }
}
