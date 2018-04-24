<?php
/**
 * Copyright (c) 2017,上海二三四五网络科技股份有限公司
 * 作    者: 步迎飞.
 * 修改日期: 2018/4/24 16:14
 */

/**
 * 输入日志
 *
 * @param minx $data $data
 *
 * @return void
 */

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