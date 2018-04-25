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

        $server->on('open', function (\swoole_websocket_server $server, $request) {
            echo "server: handshake success with fd{$request->fd}\n";
        });

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

    public function onMessage(\swoole_websocket_server $server, $frame)
    {
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        $server->push($frame->fd, "this is server");
        $swooleModel = new \App\Models\SwooleModel($this->getCoroutineRedis());
        $swooleModel->setValueByRedis('uid1', 1);

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
