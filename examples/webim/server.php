<?php
define('DEBUG', 'on');
define("WEBPATH", realpath(__DIR__ . '/../'));
require __DIR__ . '/../../libs/lib_config.php';

class WebIM extends Swoole\Network\Protocol\WebSocket
{
    /**
     * 下线时，通知所有人
     */
    function onClose($serv, $client_id, $from_id)
    {
        $resMsg = array(
            'cmd' => 'offline',
            'fd' => $client_id,
            'from' => 0,
            'channal' => 0,
            'data' => $this->connections[$client_id]['name'] . "下线了。。",
        );
        //将下线消息发送给所有人
        $this->log("onOffline: " . $client_id);
        $this->broadcast_json($client_id, $resMsg);
        parent::onClose($serv, $client_id, $from_id);
    }

    /**
     * 接收到消息时
     * @see WSProtocol::onMessage()
     */
    function onMessage($client_id, $ws)
    {
        $this->log("onMessage: " . $ws['message']);
        $msg = json_decode($ws['message'], true);

        if ($msg['cmd'] == 'login')
        {
            $this->connections[$client_id]['name'] = $msg['name'];
            $this->connections[$client_id]['avatar'] = $msg['avatar'];

            //回复给登录用户
            $resMsg = array(
                'cmd' => 'login',
                'fd' => $client_id,
                'name' => $msg['name'],
                'avatar' => $msg['avatar'],
            );
            $this->send_json($client_id, $resMsg);

            //广播给其它在线用户
            $resMsg['cmd'] = 'newUser';
            //将上线消息发送给所有人
            $this->broadcast_json($client_id, $resMsg);
            //用户登录消息
            $loginMsg = array(
                'cmd' => 'fromMsg',
                'from' => 0,
                'channal' => 0,
                'data' => $msg['name'] . "上线鸟。。",
            );
            $this->broadcast_json($client_id, $loginMsg);
        }
        /**
         * 获取在线列表
         */
        elseif ($msg['cmd'] == 'getOnline')
        {
            $resMsg = array(
                'cmd' => 'getOnline',
            );
            foreach ($this->connections as $clid => $info) {
                $resMsg['list'][] = array(
                    'fd' => $clid,
                    'name' => $info['name'],
                    'avatar' => $info['avatar'],
                );
            }
            $this->send_json($client_id, $resMsg);
        } /**
         * 发送信息请求
         */
        elseif ($msg['cmd'] == 'message')
        {
            $resMsg = $msg;
            $resMsg['cmd'] = 'fromMsg';

            //表示群发
            if ($msg['channal'] == 0)
            {
                foreach ($this->connections as $clid => $info) {
                    $this->send_json($clid, $resMsg);
                }

            } //表示私聊
            elseif ($msg['channal'] == 1)
            {
                $this->send_json($msg['to'], $resMsg);
                $this->send_json($msg['from'], $resMsg);
            }
        }
    }

    /**
     * 发送JSON数据
     * @param $client_id
     * @param $array
     */
    function send_json($client_id, $array)
    {
        $msg = json_encode($array);
        $this->send($client_id, $msg);
    }

    /**
     * 广播JSON数据
     * @param $client_id
     * @param $array
     */
    function broadcast_json($client_id, $array)
    {
        $msg = json_encode($array);
        $this->broadcast($client_id, $msg);
    }

    function broadcast($client_id, $msg)
    {
        foreach ($this->connections as $clid => $info)
        {
            if ($client_id != $clid)
            {
                $this->send($clid, $msg);
            }
        }
    }
}

$AppSvr = new WebIM();
$AppSvr->loadSetting(__DIR__."/../swoole.ini"); //加载配置文件
$AppSvr->setLogger(new \Swoole\Log\EchoLog(true)); //Logger

/**
 * 如果你没有安装swoole扩展，这里还可选择
 * BlockTCP 阻塞的TCP，支持windows平台
 * SelectTCP 使用select做事件循环，支持windows平台
 * EventTCP 使用libevent，需要安装libevent扩展
 */
$server = new \Swoole\Network\Server('0.0.0.0', 9503);
$server->setProtocol($AppSvr);
//$server->daemonize(); //作为守护进程
$server->run(array('worker_num' => 1, 'max_request' => 0));