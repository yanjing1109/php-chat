<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class SwoolePushWenziCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SwoolePushWenzi';

    // 异步协成redis 客户端
    private $coroutineRedisObj = null;

    // websocket server 对象
    private $wsServer = null;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '视频直播右侧发送文字';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }



    /**
     * Execute the console command.
     * 启动swoole 服务
     * @return mixed
     */
    public function handle()
    {
        cli_set_process_title('push_wenzi_swoole');
        $pidpath = __DIR__.'/run/pushwenzi.pid';
        if (check_swoole_process_exist($pidpath))
        {
            exit();
        }
//        write_process_pid($pidpath);
        $port = config('swoole.push_wenzi_port');

        //
        $server = new \swoole_websocket_server("0.0.0.0", $port);

        $server->on('open', [$this, 'onOpen']);

        $server->on('message', [$this, 'onMessage']);

        $server->on('close', function ($ser, $fd) {
            echo "client {$fd} closed\n";
        });

        // swoole 配置项 ： 参考https://wiki.swoole.com/wiki/page/274.html
        $server->set([
                         'worker_num' => 4,
                         'daemonize' => true,
                         'backlog' => 128,
                         'reactor_num'=>4
                     ]);

        $server->on('WorkerStart', function ($server) {
            $server->tick(60 * 1000, function () use ($server) {
                check_process_stop($server, SWOOLE_STOP_FILE);
            });
        });

        $server->on('start', function ($server) {
            startWork();
        });

        $server->start();
    }

    // websocket  建立链接时发送token
    public function onOpen(\swoole_websocket_server $server, $request)
    {
        $this->wsServer = $server;
        $token = uniqid('', false);
        $this->responseWebSocket($request->fd, SWOOLE_OPEN,[ 'token' => $token ]);
    }

    public function onMessage(\swoole_websocket_server $server, $frame)
    {
        $data = json_decode($frame->data,true);
        $action = $data['action'];
        if (!$data['token'])
        {
            $this->responseWebSocket($frame->fd, SWOOLE_UNUSEFULL,['message' => '缺少token']);
        }
        $data['fd'] = $frame->fd;
        $swooleModel = new \App\Models\SwooleModel($this->getCoroutineRedis());
        $swooleModel->$action($data);
    }



    /**
     * 获取协成redis 客户端
     * getCoroutineRedis function
     * 参考链接： https://wiki.swoole.com/wiki/page/589.html
     * @return \Swoole\Coroutine\Redis
     */
    public function getCoroutineRedis()
    {
        if (null == $this->coroutineRedisObj)
        {
            $this->coroutineRedisObj = new \Swoole\Coroutine\Redis();
            var_dump(config('database.redis.default.host'));
            $this->coroutineRedisObj->connect(config('database.redis.default.host'), config('database.redis.default.port'));
//            var_dump($this->coroutineRedisObj);
            //            if (isset($config['auth']) && $config['auth'])
            //            {
            //                $this->redis->auth($config['auth']);
            //            }
        }

        return $this->coroutineRedisObj;
    }


    /**
     * 回应 websocket 响应
     * 参考链接： https://wiki.swoole.com/wiki/page/15.html
     * @param integer $fd
     * @param string $action
     * @param array|string $data
     * @param string $messageType
     *
     * @return void
     */
    public function responseWebSocket($fd, $action, $data = [], $messageType = '')
    {
        // 资源对象是否存在
        if (!$this->wsServer->exist($fd))
        {
            return;
        }

        $ret = [
            'action' => $action,
            'data' => $data,
            'type' => $messageType
        ];

        $retStr = json_encode($ret);
        // 回复消息
        $this->wsServer->push($fd, $retStr);
    }
}
