<?php
namespace WebIM\Store;

class File
{
    static $shm_dir = '/dev/shm/swoole_webim/';
    protected $online_dir;
    static $save_dir;

    static function clearDir($dir)
    {
        $n = 0;
        if ($dh = opendir($dir))
        {
            while (($file = readdir($dh)) !== false)
            {
                if ($file == '.' or $file == '..')
                {
                    continue;
                }
                if (is_file($dir . $file)) {
                    unlink($dir . $file);
                    //echo "delete ".$dir . $file.PHP_EOL;
                    $n++;
                }
                if (is_dir($dir . $file)) {
                    self::clearDir($dir . $file . '/');
                    $n++;
                    //echo "rmdir ".$dir . $file . PHP_EOL;
                    //rmdir($dir . $file . '/');
                }
            }
        }
        closedir($dh);
        return $n;
    }

    function __construct()
    {
        if (!is_dir(self::$shm_dir))
        {
            if (!mkdir(self::$shm_dir, 0777, true))
            {
                rw_deny:
                trigger_error("can not read/write dir[".self::$shm_dir."]", E_ERROR);
                return;
            }
        }
        else
        {
            self::clearDir(self::$shm_dir);
            $this->online_dir = self::$shm_dir.'/online/';
            if (!is_dir($this->online_dir))
            {
                if (!mkdir($this->online_dir, 0777, true))
                {
                    goto rw_deny;
                }
            }
        }
    }

    function login($client_id, $info)
    {
        file_put_contents($this->online_dir.$client_id, serialize($info));
    }

    function logout($client_id)
    {
        unlink($this->online_dir.$client_id);
    }

    function getOnlineUsers()
    {
        $online_users = array_slice(scandir($this->online_dir), 2);
        return $online_users;
    }

    function getUsers($users)
    {
        $ret = array();
        foreach($users as $v)
        {
            $ret[] = $this->getUser($v);
        }
        return $ret;
    }

    function getUser($userid)
    {
        $ret = file_get_contents($this->online_dir.$userid);
        $info = unserialize($ret);
        return $info;
    }
}