<?php
namespace WebIM;

class Server extends \Swoole\Network\Protocol\WebSocket
{
    protected $chat;
    function __construct($config = array())
    {
        parent::__construct($config);
        $this->chat = new Store\File;
    }

    /**
     * 下线时，通知所有人
     */
    function onClose($serv, $client_id, $from_id)
    {
        $userInfo = $this->chat->getUser($client_id);
        $resMsg = array(
            'cmd' => 'offline',
            'fd' => $client_id,
            'from' => 0,
            'channal' => 0,
            'data' => $userInfo['name'] . "下线了。。",
        );
        //将下线消息发送给所有人
        $this->log("onOffline: " . $client_id);

        $this->chat->logout($client_id);
        $this->broadcastJson($client_id, $resMsg);
        parent::onClose($serv, $client_id, $from_id);
    }

    /**
     * 获取在线列表
     */
    function cmd_getOnline($client_id, $msg)
    {
        $resMsg = array(
            'cmd' => 'getOnline',
        );
        $users = $this->chat->getOnlineUsers();
        $info = $this->chat->getUsers(array_slice($users, 0, 100));
        $resMsg['users'] = $users;
        $resMsg['list'] = $info;
        $this->sendJson($client_id, $resMsg);
    }

    /**
     * 登录
     * @param $client_id
     * @param $msg
     */
    function cmd_login($client_id, $msg)
    {
        $info['name'] = $msg['name'];
        $info['avatar'] = $msg['avatar'];

        //回复给登录用户
        $resMsg = array(
            'cmd' => 'login',
            'fd' => $client_id,
            'name' => $msg['name'],
            'avatar' => $msg['avatar'],
        );
        $this->chat->login($client_id, $resMsg);
        $this->sendJson($client_id, $resMsg);

        //广播给其它在线用户
        $resMsg['cmd'] = 'newUser';
        //将上线消息发送给所有人
        $this->broadcastJson($client_id, $resMsg);
        //用户登录消息
        $loginMsg = array(
            'cmd' => 'fromMsg',
            'from' => 0,
            'channal' => 0,
            'data' => $msg['name'] . "上线鸟。。",
        );
        $this->broadcastJson($client_id, $loginMsg);
    }

    /**
     * 发送信息请求
     */
    function cmd_message($client_id, $msg)
    {
        $resMsg = $msg;
        $resMsg['cmd'] = 'fromMsg';

        //表示群发
        if ($msg['channal'] == 0)
        {
            foreach ($this->connections as $clid => $info)
            {
                $this->sendJson($clid, $resMsg);
            }
        }
        //表示私聊
        elseif ($msg['channal'] == 1)
        {
            $this->sendJson($msg['to'], $resMsg);
            $this->sendJson($msg['from'], $resMsg);
        }
    }

    /**
     * 接收到消息时
     * @see WSProtocol::onMessage()
     */
    function onMessage($client_id, $ws)
    {
        $this->log("onMessage: " . $ws['message']);
        $msg = json_decode($ws['message'], true);
        if (empty($msg['cmd']))
        {
            $this->sendErrorMessage($client_id, 101, "invalid command");
            return;
        }
        $func = 'cmd_'.$msg['cmd'];
        $this->$func($client_id, $msg);
    }

    /**
     * 发送错误信息
    * @param $client_id
    * @param $code
    * @param $msg
     */
    function sendErrorMessage($client_id, $code, $msg)
    {
        $this->sendJson($client_id, array('cmd' => 'error', 'code' => $code, 'msg' => $msg));
    }

    /**
     * 发送JSON数据
     * @param $client_id
     * @param $array
     */
    function sendJson($client_id, $array)
    {
        $msg = json_encode($array);
        $this->send($client_id, $msg);
    }

    /**
     * 广播JSON数据
     * @param $client_id
     * @param $array
     */
    function broadcastJson($client_id, $array)
    {
        $msg = json_encode($array);
        $this->broadcast($client_id, $msg);
    }

    function broadcast($client_id, $msg)
    {
        if (extension_loaded('swoole'))
        {
            $sw_serv = $this->getSwooleServer();
            $start_fd = 0;
            while(true)
            {
                $conn_list = $sw_serv->connection_list($start_fd, 10);
                if($conn_list === false)
                {
                    break;
                }
                $start_fd = end($conn_list);
                foreach($conn_list as $fd)
                {
                    if($fd === $client_id) continue;
                    $this->send($fd, $msg);
                }
            }
        }
        else
        {
            foreach ($this->connections as $fd => $info)
            {
                if ($client_id != $fd)
                {
                    $this->send($fd, $msg);
                }
            }
        }
    }
}
