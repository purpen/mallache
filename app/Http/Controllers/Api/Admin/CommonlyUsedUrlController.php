<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\AdminTransformer\CommonlyUsedUrlTransformer;
use App\Models\CommonlyUsedUrl;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CommonlyUsedUrlController extends BaseController
{

    /**
     * @api {get} /admin/commonlyUsedUrls/list 常用网站列表
     * @apiVersion 1.0.0
     * @apiName AdminCommonlyUsedUrls lists
     * @apiGroup AdminCommonlyUsedUrl
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
     * @api {post} /admin/commonlyUsedUrls 添加常用网站
     * @apiVersion 1.0.0
     * @apiName AdminCommonlyUsedUrl store
     * @apiGroup AdminCommonlyUsedUrl
     *
     * @apiParam {integer} type 1.设计咨询；2.创意灵感；3.众筹；4.商业咨询；5.设计奖项
     * @apiParam {integer} cover_id 图片id
     * @apiParam {string} title 名称
     * @apiParam {string} summary 描述
     * @apiParam {string} url url
     * @apiParam {string} token token
     *
     * @apiSuccessExample 成功响应:
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function store(Request $request)
    {
        $rules = [
            'type' => 'required|integer',
            'title' => 'required|max:30',
            'cover_id' => 'integer',
            'summary' => 'max:100',
            'url' => 'required|max:100',

        ];

        $user_id = $this->auth_user_id;
        $params = array(
            'title' => $request->input('title'),
            'summary' => $request->input('summary') ? $request->input('summary') : '',
            'user_id' => $user_id,
            'type' => $request->input('type'),
            'cover_id' => $request->input('cover_id'),
            'url' => $request->input('url'),
        );

        $validator = Validator::make($params, $rules);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        $params['user_id'] = $this->auth_user_id;
        if (!$CommonlyUsedUrl = CommonlyUsedUrl::create($params)) {
            return $this->response->array($this->apiError('添加失败', 500));
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {get} /admin/commonlyUsedUrls 常用网站详情
     * @apiVersion 1.0.0
     * @apiName AdminCommonlyUsedUrl CommonlyUsedUrl
     * @apiGroup AdminCommonlyUsedUrl
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


    /**
     * @api {put} /admin/commonlyUsedUrls 更改常用网站
     * @apiVersion 1.0.0
     * @apiName AdminCommonlyUsedUrl update
     * @apiGroup AdminCommonlyUsedUrl
     *
     * @apiParam {integer} id 项目奖项id
     * @apiParam {integer} type 1.设计咨询；2.创意灵感；3.众筹；4.商业咨询；5.设计奖项
     * @apiParam {integer} cover_id 图片id
     * @apiParam {string} title 名称
     * @apiParam {string} summary 描述
     * @apiParam {string} url url
     * @apiParam {string} token token
     *
     * @apiSuccessExample 成功响应:
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function update(Request $request)
    {
        $rules = [
            'type' => 'required|integer',
            'cover_id' => 'integer',
            'title' => 'required|max:30',
            'summary' => 'max:100',
            'url' => 'required|max:100',

        ];

        $params = array(
            'title' => $request->input('title'),
            'summary' => $request->input('summary') ? $request->input('summary') : '',
            'type' => $request->input('type'),
            'url' => $request->input('url'),
            'cover_id' => $request->input('cover_id'),
        );

        $validator = Validator::make($params, $rules);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $CommonlyUsedUrl = CommonlyUsedUrl::find($request->input('id'));
        if (!$CommonlyUsedUrl) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if (!$CommonlyUsedUrl->update($params)) {
            return $this->response->array($this->apiError('更新失败', 500));
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {delete} /admin/commonlyUsedUrls 删除常用网站
     * @apiVersion 1.0.0
     * @apiName AdminCommonlyUsedUrl delete
     * @apiGroup AdminCommonlyUsedUrl
     *
     * @apiParam {integer} id 常用网站ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function delete(Request $request)
    {
        $id = (int)$request->input('id');

        if(!CommonlyUsedUrl::destroy($id)){
            return $this->response->array($this->apiError('删除失败', 412));
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {put} /admin/commonlyUsedUrls/verifyStatus 变更状态
     * @apiVersion 1.0.0
     * @apiName AdminCommonlyUsedUrl verifyStatus
     * @apiGroup AdminCommonlyUsedUrl
     *
     * @apiParam {integer} id ID
     * @apiParam {integer} status 状态 0.禁用；1.启用；
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function verifyStatus(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status') ? (int)$request->input('status') : 0;

        $commonlyUsedUrl = CommonlyUsedUrl::find($id);
        if (!$commonlyUsedUrl) {
            return $this->response->array($this->apiError('not found', 404));
        }

        $commonlyUsedUrl->status = $status;
        if (!$commonlyUsedUrl->save()) {
            return $this->response->array($this->apiError('Error', 500));
        } else {
            return $this->response->array($this->apiSuccess());
        }
    }

}

