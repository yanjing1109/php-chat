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

    // 保存链接对象 到redis
    public function saveConnections()
    {
        $this->setValueByRedis('ws:socket:connect', $this->userConnecntions);
        $this->setValueByRedis('ws:socket:connect:fd', $this->resourceConnecntions);
    }

    public function open($data)
    {
        $this->resourceConnecntions[$data['fd']] = $data['token'];
        $this->userConnecntions[$data['token']] = $data['fd'];
        $this->saveConnections();
    }




}
