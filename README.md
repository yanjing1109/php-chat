# phpCrawlers
laravel + swoole + vue 实现新闻爬虫

###项目实现目标
开发一套新闻管理系统，主要内容包括以下几个功能：新闻添加、新闻删除、新闻编辑、新闻列表（不需要分页），表（news）有以下四个字段 ID 标题 内容 时间
采集地址为 http://roll.news.sina.com.cn/news/gnxw/zs-pl/index_1.shtml（只采集这个页面），分析出本页面每篇新闻的 标题 内容 插入到表news中。

这是刚进入公司，老大让用php socket 实现的一个项目，当初用公司框架简单实现

#### 这次采用前后端分离方式单独实现
* 后端采用laravel 框架 ，使用swoole 进行新闻爬取 
* 前端采用vue.js + vuex + vue router + ele UI + webpack 实现 

