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
     * @apiParam {integer} status 状态 -1.禁用；0.全部；1.启用；
     * @apiParam {integer} type 分类
     * @apiParam {integer} page 页数
     * @apiParam {integer} per_page 页面条数
     * @apiParam {string} token
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
        $per_page = $request->input('per_page') ?? $this->per_page;
        $type = $request->input('type') ? (int)$request->input('type') : 0;
        $status = $request->input('status') ? (int)$request->input('status') : 0;

        switch ($status) {
            case -1:
                $status = 0;
                break;
            case 0:
                $status = null;
                break;
            case 1:
                $status = 1;
                break;
            default:
                $status = 1;
        }

        $query = array();
        if ($type) $query['type'] = $type;
        if ($status) $query['status'] = $status;

        $lists = CommonlyUsedUrl::where($query)
            ->orderBy('id', 'desc')
            ->paginate($per_page);

        return $this->response->paginator($lists, new CommonlyUsedUrlTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /commonlyUsedUrls 常用网站详情
     * @apiVersion 1.0.0
     * @apiName CommonlyUsedUrl CommonlyUsedUrl
     * @apiGroup CommonlyUsedUrl
     *
     * @apiParam {integer} id 常用网站id
     * @apiParam {string} token
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

