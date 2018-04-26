<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;

class SwooleController extends BaseController
{

    use \App\Traits\IpTrait;
    private $allowIps = [];

    public function __construct()
    {
        // 这里可以做用户认证，进行权限控制
//        if (!isset($_GET['user']) || $_GET['user'] != "buyingfei")
//        {
//            exit('user Error');
//        }
        // 这里做ip 限制
        $this->checkIpBlack(config('swoole.all_ip'));
    }
    /**
     * actionStopSwoole function
     *
     * @return void
     */
    public function stopSwoole()
    {
        if (!file_exists(SWOOLE_STOP_FILE))
        {
            touch(SWOOLE_STOP_FILE);
        }
    }

    /**
     * actionStartSwoole function
     *
     * @return void
     */
    public function startSwoole()
    {
        $value = session('key');
        // 指定默认值...
        $value = session('key', 'default');
        var_dump(session('key'),$value);
        // 存储数据到session...
        session(['key' => 'value']);
        var_dump(session('key'));
        die();
        if (file_exists(SWOOLE_STOP_FILE))
        {
            unlink(SWOOLE_STOP_FILE);
        }
    }

    /**
     * 打开swoole 链接
     * @param Request $request
     */
    public function open(Request $request, $data)
    {

    }
}
