<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\RequestException;
use App\Models\News;

class CrawlersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Crawler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'phpCrawler 单线程 爬虫脚本';

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
        $start = microtime(true);
        $url         = "http://roll.news.sina.com.cn/news/gnxw/zs-pl/index_1.shtml";
        $client = new \GuzzleHttp\Client();
        $request = new \GuzzleHttp\Psr7\Request('GET', $url);
        $self = new self();

        $promise = $client->sendAsync($request)->then(function($response) use ($self) {
            $contents = $response->getBody()->getContents();
            // 将gbk 编码转化为utf8 编码
            $contents = iconv( 'gbk', 'UTF-8', $contents);
            $self->ParseNewsList($contents);
        }, function (RequestException $e) use ($url) {
            Log::error("get {$url} exception :".$e->getMessage());
        });
        $promise->wait();
        $timeUsed = microtime(true) - $start;
        echo round($timeUsed, 3)." seconds";
    }

    /**
     * 解析新闻列表
     * @param string $contentList
     */
    public function ParseNewsList($contentList)
    {
        //新闻列表正则匹配表达式
        $pattern = "/<li><a href=\"(.*?)\" target=\"_blank\">(.*?)<\/a><span>\((.*?)\)<\/span><\/li>/";
        preg_match_all($pattern, $contentList, $matchs);
        $this->ParseNewsDetail($matchs);
    }

    /**
     * 解析信呢详情
     * @param array $matchs
     */
    public function ParseNewsDetail($matchs)
    {
        $client = new \GuzzleHttp\Client();
        //获取新闻详情链接、标题、时间
        list($all, $urls, $titles, $dates) = $matchs;
        $newsModel = new News();
        for ($i = 0; $i < count($urls); $i ++)
        {
            $res = $client->request('GET', $urls[$i]);
            //获取新闻内容
            $contentDetail = $res->getBody()->getContents();
            //新闻内容正则匹配表达式
            $patternContent = "/id=\"article\">([\s\S]*?)<p class=\"show_author/";
            preg_match($patternContent, $contentDetail, $contentDetailMatchs);

            //去除多余字符
            $content = trim(rtrim(ltrim($contentDetailMatchs[0], "id=\"article\">"), "<p class=\"show_author"));

            $title      = $titles[$i];
            $created_at = $dates[$i];

            $vArray = array(
                "title"      => $title,
                "content"    => $content,
                "news_time" => $created_at,
            );

            // 保存新闻
            $newsModel->saveNews($vArray);
        };
    }
}
