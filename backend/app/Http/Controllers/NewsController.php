<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\News;

class NewsController extends BaseController
{
    /**
     * 获取新闻列表
     * @param Request $request
     * @return mixed
     */
    public function getList(Request $request)
    {
        $data = $request->all();
        $limit = isset($data['limit']) ? $data['limit'] : 10;
        $page = isset($data['page']) ? $data['page'] : 1;
        $offset = ($page - 1 ) * $limit;
        $list = News::offset($offset)->orderBy('id', 'desc')
            ->select('id','title','content','news_time')->limit($limit)
            ->get()->toArray();
        $total = News::count();
        return  $this->responseJSON(200,['list' => $list, 'total' => $total]);
    }

    /**
     * 获取新闻详情
     * @param Request $request
     * @return mixed
     */
    public function getDetail(Request $request)
    {
        $data = $request->all();
        $list = News::select('id','title','content','news_time')->where('id',$data['id'])->first()->toArray();
        return  $this->responseJSON(200,['list' => $list]);
    }

    /**
     * 保存新闻
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function modifyNews(Request $request)
    {
        $data = $request->all();
        $news = News::find($data['id']);
        foreach($data as $k => $v)
        {
            $news->$k = $v;
        }
        $res = $news->save();
        if ($res)
        {
            return  $this->responseJSON(200,['message' => '修改失败']);
        }
        else
        {
            return  $this->responseJSON(400,['message' => '修改成功']);
        }

    }
}
