<?php

return [
    'push_wenzi_port' => env('SWOOLE_PUSH_WENZI_PORT',9666),
    'lsof_path' => env('LSOF_PATH', '/usr/sbin/lsof'),
    'all_ip' => env('ALL_IP', '172.17.20.197,192.168.132.1'),
];
