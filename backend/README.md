# backend

> A laravel project

## Build Setup

``` bash
# install dependencies
composer install

#启动swoole 
cd backend
php artisan SwoolePushWenzi

#  定时脚本配置, 做到无人值守100%可用
* * * * * /usr/bin/php /home/buyf/go/src/phpCrawlers/backend/artisan schedule:run >> /dev/null 2>&1

# 停止swoole
http://phplive.com:88/swoole/stopSwoole

# 开启swoole
http://phplive.com:88/swoole/startSwoole
```
