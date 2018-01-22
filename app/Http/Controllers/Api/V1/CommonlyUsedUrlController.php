<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\CommonlyUsedUrlTransformer;
use App\Models\CommonlyUsedUrl;
use Illuminate\Http\Request;


class CommonlyUsedUrlController extends BaseController
{

    /**
     * @api {get} /commonlyUsedUrls/list 常用网站列表
     * @apiVersion 1.0.0
     * @apiName CommonlyUsedUrls lists
     * @apiGroup CommonlyUsedUrl
     *
     *
     * @apiSuccessExample 成功响应:
        {
            "data": [
                {
                    "id": 1,
                    "type": 2,
                    "type_value": "创意灵感",
                    "url": "http://www.baidu.com",
                    "title": "2test",
                    "user_id": 1,
                    "cover_id": 2,
                    "cover": null,
                    "summary": "2test",
                    "status": 1,
                    "created_at": 1513762928
                }
            ],
            "meta": {
                "message": "Success",
                "status_code": 200,
                "pagination": {
                    "total": 1,
                    "count": 1,
                    "per_page": 10,
                    "current_page": 1,
                    "total_pages": 1,
                    "links": []
                }
            }
        }
     *
     */
    public function lists(Request $request)
    {

        $lists = CommonlyUsedUrl::get();
        return $this->response->collection($lists, new CommonlyUsedUrlTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /commonlyUsedUrls 常用网站详情
     * @apiVersion 1.0.0
     * @apiName CommonlyUsedUrl CommonlyUsedUrl
     * @apiGroup CommonlyUsedUrl
     *
     * @apiParam {integer} id 常用网站id
     *
     * @apiSuccessExample 成功响应:
        {
            "data": [
                {
                    "id": 1,
                    "type": 2,
                    "type_value": "创意灵感",
                    "url": "http://www.baidu.com",
                    "title": "2test",
                    "user_id": 1,
                    "cover_id": 2,
                    "cover": null,
                    "summary": "2test",
                    "status": 1,
                    "created_at": 1513762928
                }
            ],
            "meta": {
                "message": "Success",
                "status_code": 200,
                "pagination": {
                    "total": 1,
                    "count": 1,
                    "per_page": 10,
                    "current_page": 1,
                    "total_pages": 1,
                    "links": []
                }
            }
        }
     *
     */
    public function show(Request $request)
    {
        $id = $request->input('id');

        $CommonlyUsedUrl = CommonlyUsedUrl::where('id' , $id)->first();
        if (!$CommonlyUsedUrl) {
            return $this->response->array($this->apiError('not found', 404));
        }

        return $this->response->item($CommonlyUsedUrl, new CommonlyUsedUrlTransformer())->setMeta($this->apiMeta());
    }

}

