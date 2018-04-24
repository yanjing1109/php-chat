<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SwooleCrawler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SwooleCrawler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Swoole 多线程爬虫';

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
        //
        \Swoole\Async::dnsLookup("swoole.com", function ($domainName, $ip) {
            $cli = new \swoole_http_client($ip, 80);
            $cli->setHeaders([
                                 'Host' => $domainName
                             ]);
            $cli->get('/roll.news.sina.com.cn/news/gnxw/zs-pl/index_1.shtml', function ($cli) {
                echo "Length: " . strlen($cli->body) . "\n";
                echo $cli->body;
            });
        });
        sleep(20);
    }
}
