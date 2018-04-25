<?php
/**
 * Copyright (c) 2017,上海二三四五网络科技股份有限公司
 * 作    者: 步迎飞.
 * 修改日期: 2018/4/24 16:14
 */

use Illuminate\Support\Facades\Log;

define('BASEPATH', __DIR__);

define('SWOOLE_STOP_FILE', BASEPATH . '/../Console/Commands/run/swoole.stop');

/**
 * 输入日志
 *
 * @param minx $data $data
 *
 * @return void
 */
if (!function_exists('output_log'))
{
    function output_log($data)
    {
        if (config('app.env') != 'production')
        {
            echo '-------------------------------------------------------' . PHP_EOL;
            echo date('Y-m-d H:i:s') . PHP_EOL;
            var_dump($data);
            echo PHP_EOL . PHP_EOL . PHP_EOL;
            echo '-------------------------------------------------------' . PHP_EOL;
        }
    }
}

/**
 * Undocumented function
 *
 * @return void
 */
function write_process_pid($pidPath)
{
    $pid = getmypid();
    file_put_contents($pidPath, $pid);
}

/**
 * check_swoole_process_exist function
 *
 * @return void
 */
function check_swoole_process_exist($pidPath)
{
//    $pidFile = $pidPath;
//    if (file_exists($pidFile))
//    {
//        $pid = file_get_contents($pidFile);
//        $lsof = config('swoole.lsof_path');
//        $cmd = $lsof . ' -p ' . $pid . ' > /dev/null 2>&1';
//        system($cmd);
        $res = system('ps -fe |grep "push_wenzi_swoole" | grep -v "grep" | wc -l', $exist);
        output_log('当前进程数量： '.$res);
        if (0 == $exist && $res > 1)
        {
            Log::info('消息推送websocket 进程已经存在');
            return true;
        }
//    }
    return false;
}


if (!function_exists('stop_file_exist'))
{
    /**
     * 是否停止文件
     *
     * @param string $stopFile
     *
     * @return boolean
     */
    function stop_file_exist($stopFile)
    {
        if (file_exists($stopFile))
        {
            return true;
        }

        return false;
    }
}

/**
 * check_process function
 *
 * @param swoole_websocket_server swoole_websocket_server $wsServer
 *
 * @return void
 */
function check_process_stop($wsServer, $file)
{
    if (stop_file_exist($file))
    {
        Log::error('WebSocket Server End Run . files :' .$file);
        // 关闭swoole 服务
        $wsServer->shutdown();
        exit();
    }
}

