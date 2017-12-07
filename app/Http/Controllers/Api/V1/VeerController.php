<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\ArticleListTransformer;
use App\Http\Transformer\ArticleTransformer;
use App\Http\Transformer\VeerImageTransformer;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class VeerController extends BaseController
{
    /**
     * @api {get} /veerImage/list veer图片列表
     * @apiVersion 1.0.0
     * @apiName VeerImage lists
     * @apiGroup VeerImage
     *
     * @apiParam {string} keyword 搜索词
     * @apiParam {integer} page 第几页.最小1.最大1000
     * @apiParam {integer} nums 每页数量.最大100.默认10
     * @apiParam {string} token

     */
    public function lists(Request $request)
    {
        $keyword = $request->input('keyword');
        $page = $request->input('page') ? $request->input('page') : 1;
        $nums = $request->input('nums') ? $request->input('nums') : 10;
        $client_id = config('veer.client_id');
        $access_token = Redis::get('access_token');
        $authorization = 'Bearer '.$access_token;
        $headers = array(
            "api-key: ".$client_id,
            "authorization: ".$authorization
        );

        $url = 'http://api-v1.vcg.com/api/search/creative/veer?keyword='.$keyword.'&page='.$page.'&nums='.$nums;
        //初始化
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);
        //打印获得的数据
        $output_array = json_decode($output,true);
        if(empty($output_array)){
            return $this->response->array($this->apiSuccess());
        }
        $veerImage = $output_array['data']['list'];
        $meta['pagination']['total'] = $output_array['data']['total_count'];
        $meta['pagination']['count'] = $request->input('nums') ? $request->input('nums') : 10;
        $meta['pagination']['per_page'] = $output_array['data']['per_page'];
        $meta['pagination']['current_page'] = $output_array['data']['cur_page'];
        $meta['pagination']['total_pages'] = $output_array['data']['total_page'];
        $meta['pagination']['links']['next'] = url('/veerImage/list?page='.($page+1)).'&keyword='.$keyword;
        return $this->response->array($this->apiSuccess('Success' , 200 ,$veerImage ,$meta));

    }

}