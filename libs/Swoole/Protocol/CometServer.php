<?php
namespace Swoole\Protocol;
use Swoole;

abstract class CometServer extends WebSocket
{
    /**
     * 某个请求超过最大时间后，务必要返回内容
     * @var int
     */
    protected $request_timeout = 50;

    protected $origin;

    /**
     * Comet连接的信息
     * @var array
     */
    protected $sessions = array();

    /**
     * 等待数据
     * @var array
     */
    protected $wait_requests = array();

    protected $fd_session_map = array();

    /**
     * @param $serv \swoole_server
     */
    function onStart($serv, $worker_id = 0)
    {
        $serv->addTimer(1000);
        parent::onStart($serv, $worker_id);
    }

    function createNewSession()
    {
        $session = new CometSession();
        $this->sessions[$session->id] = $session;
        return $session;
    }

    /**
     * Http请求回调
     * @param Swoole\Request $request
     */
    function onHttpRequest(Swoole\Request $request)
    {
        if (!isset($request->post['type']))
        {
            return false;
        }
        //新连接
        if (empty($request->post['session_id']))
        {
            $session = $this->createNewSession();
            $response = new Swoole\Response;
            $response->setHeader('Access-Control-Allow-Origin', $this->origin);
            $response->body = json_encode(array('success' => 1, 'session_id' => $session->id));
            return $response;
        }

        $session_id = $request->post['session_id'];
        $session = $this->getSession($session_id);

        if ($request->post['type'] == 'pub')
        {
            $response = new Swoole\Response;
            $response->setHeader('Access-Control-Allow-Origin', $this->origin);
            $response->body = json_encode(array('success' => 1, 'session_id' => $session->id));
            $this->response($request, $response);
            $this->onMessage($session_id, $request->post);
        }
        elseif($request->post['type'] == 'sub')
        {
            $this->wait_requests[$session_id] = $request;
            $this->fd_session_map[$request->fd] = $session_id;
            if ($session->getMessageCount() > 0)
            {
                $this->sendMessage($session);
            }
        }
    }

    /**
     * @param $session_id
     * @return bool | CometSession
     */
    function getSession($session_id)
    {
        if (!isset($this->sessions[$session_id]))
        {
            $this->log("CometSesesion #$session_id no exists");
            return false;
        }
        return $this->sessions[$session_id];
    }

    /**
     * 向浏览器发送数据
     * @param int    $session_id
     * @param string $data
     * @return bool
     */
    function send($session_id, $data, $opcode = self::OPCODE_TEXT_FRAME, $end = true)
    {
        //WebSocket
        if (isset($this->connections[$session_id]))
        {
            return parent::send($session_id, $data, $opcode, $end);
        }
        //CometSession
        else
        {
            $session = $this->getSession($session_id);
            if (!$session)
            {
                return false;
            }
            else
            {
                $session->pushMessage($data);
            }

            //有等待的Request可以直接发送数据
            if (isset($this->wait_requests[$session_id]))
            {
                return $this->sendMessage($session);
            }
        }
    }

    /**
     * 发送数据到sub通道
     * @param CometSession $session
     * @return bool
     */
    function sendMessage(CometSession $session)
    {
        $request = $this->wait_requests[$session->id];
        $response = new Swoole\Response;
        $response->setHeader('Access-Control-Allow-Origin', $this->origin);
        $response->body = json_encode(array('success' => 1, 'data' => $session->popMessage()));
        unset($this->wait_requests[$session->id]);
        return $this->response($request, $response);
    }

    /**
     * 定时器，检查某些连接是否已超过最大时间
     * @param $serv
     * @param $interval
     */
    function onTimer($serv, $interval)
    {
        $now = time();
        //echo "timer $interval\n";
        foreach($this->wait_requests as $id => $request)
        {
            if ($request->time < $now - $this->request_timeout)
            {
                $response = new Swoole\Response;
                $response->setHeader('Access-Control-Allow-Origin', $this->origin);
                $response->body = json_encode(array('success' => 0, 'text' => 'timeout'));
                $this->response($request, $response);
                unset($this->wait_requests[$id]);
            }
        }
    }

    final function onClose($serv, $fd, $reactor_id)
    {
        if (isset($this->fd_session_map[$fd]))
        {
            $session_id = $this->fd_session_map[$fd];
            unset($this->fd_session_map[$fd], $this->wait_requests[$session_id], $this->sessions[$session_id]);
            //再执行一次
            $this->onExit($session_id);
        }
        parent::onClose($serv, $fd, $reactor_id);
    }
}

class CometSession
{
    public $request;
    public $id;

    static $round_id = 1;

    /**
     * @var \SplQueue
     */
    protected $msg_queue;

    function __construct()
    {
        $this->id = self::$round_id++;
        $this->msg_queue = new \SplQueue;
    }

    function getMessageCount()
    {
        return count($this->msg_queue);
    }

    function pushMessage($msg)
    {
        return $this->msg_queue->enqueue($msg);
    }

    function popMessage()
    {
        return $this->msg_queue->dequeue();
    }
}