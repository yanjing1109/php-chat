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

        $server->on('close', [$this, 'onClose']);

        // swoole 配置项 ： 参考https://wiki.swoole.com/wiki/page/274.html
        $server->set([
                         'worker_num' => 4,
                         'daemonize' => false, // 是否以守护进程存在
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

        responseWebSocket($server, $request->fd, SWOOLE_OPEN,[ 'token' => $token ]);
    }

    // 发送消息
    public function onMessage(\swoole_websocket_server $server, $frame)
    {
        $data = json_decode($frame->data,true);
        $action = $data['action'];
        if (!$data['token'])
        {
            $this->responseWebSocket($server, $frame->fd, SWOOLE_UNUSEFULL,['message' => '缺少token']);
            return;
        }
        $data['fd'] = $frame->fd;
        $swooleModel = new \App\Models\SwooleModel($this->getCoroutineRedis());
        $swooleModel->$action($data,$server);
    }

    // 删除资源链接，此处不能使用异步操作
    public function onClose(\swoole_websocket_server $server, $fd)
    {
        $userConn = Redis::get('ws:socket:connect') ? json_decode(Redis::get('ws:socket:connect'),true) : [];
        $resourceConn = Redis::get('ws:socket:connect:fd') ? json_decode(Redis::get('ws:socket:connect:fd'),true) : [];

        // 删除链接资源
        unset($userConn[$fd]);
        Redis::set('ws:socket:connect', json_encode($userConn));

        if($resourceConn)
        {
            foreach ($resourceConn as  $token => &$resource)
            {
                if ($resource == $fd)
                {
                    unset($resourceConn[$token]);
                }
            }
        }
        Redis::set('ws:socket:connect:fd', json_encode($resourceConn));
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

}
