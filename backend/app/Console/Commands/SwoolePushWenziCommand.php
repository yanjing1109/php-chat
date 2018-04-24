<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SwoolePushWenziCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SwoolePushWenzi';

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
     *
     * @return mixed
     */
    public function handle()
    {
        $port = config('swoole.push_wenzi_port');
        //
        $server = new \swoole_websocket_server("0.0.0.0", $port);

        $server->on('open', function (\swoole_websocket_server $server, $request) {
            echo "server: handshake success with fd{$request->fd}\n";
        });

        $server->on('message', function (\swoole_websocket_server $server, $frame) {
            echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
            $server->push($frame->fd, "this is server");
        });

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
        $server->start();
    }
}
