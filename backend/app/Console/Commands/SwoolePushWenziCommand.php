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
        //
        echo 11;
    }
}
