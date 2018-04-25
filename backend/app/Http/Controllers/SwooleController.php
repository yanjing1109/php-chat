<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\News;

class SwooleController extends BaseController
{
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
        if (file_exists(SWOOLE_STOP_FILE))
        {
            unlink(SWOOLE_STOP_FILE);
        }
    }
}
