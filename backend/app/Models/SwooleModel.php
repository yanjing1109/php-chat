<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Model;

class SwooleModel
{
    //
    private $redisObj = null;

    // 保存所有资源连接符
    private $resourceConnecntions = [];

    // 保存所有用户为单位链接
    private $userConnecntions = [];

    public function __construct($redisObj)
    {
        $this->redisObj = $redisObj;
    }

    /**
     * 通过异步redis 获取redis 值
     * @param $key
     * @return mixed|string
     */
    public function getValueByRedis($key)
    {
        $value = $this->redisObj ->get($key);
        if (!$value)
        {
            $value = '[]';
        }
        $value = json_decode($value, true);
        return $value;
    }

    /**
     * 通过异步redis 设置redis 值
     * @param $key
     * @param $val
     * @param $timeout
     */
    public function setValueByRedis($key, $val, $timeout = 0)
    {
        $redis = $this->redisObj;
        $value = json_encode($val);
        if ($timeout)
        {
            $redis->set($key, $value, $timeout);
        }
        else
        {
            $redis->set($key, $value);
        }
    }


    //发送open action 保存链接
    public function open($data, $server)
    {
        $oldConnnetion = $this->getValueByRedis('ws:socket:connect');
        $oldConnnetion[$data['fd']] = $data['token'];

        $oldResouceConnection = $this->getValueByRedis('ws:socket:connect:fd');
        $oldResouceConnection[$data['token']] = $data['fd'];

        $this->setValueByRedis('ws:socket:connect', $oldConnnetion);
        $this->setValueByRedis('ws:socket:connect:fd', $oldResouceConnection);
    }

    // 回复消息
    public function sendMessage($data, $server)
    {
        $fds = $this->getValueByRedis('ws:socket:connect:fd');
        if ($fds)
        {
            //将消息发送给所有已有链接
            foreach ($fds as $fd)
            {
                if ($fd != $data['fd'])
                {
                    // 将消息发送给其它人
                    $data['action'] = SWOOLE_REPLY_MESSAGE;
                    responseWebSocket($server, $fd, SWOOLE_REPLY_MESSAGE, $data);
                    output_log(['fd' =>$fd, 'data' => $data]);
                }
            }
        }
    }
}
