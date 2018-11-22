<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\ColumnTransformer;
use App\Models\Column;
use Illuminate\Http\Request;

class ColumnController extends BaseController
{
    /**
     * @api {get} /column 栏目文章详情
     * @apiVersion 1.0.0
     * @apiName column columnShow
     * @apiGroup column
     *
     * @apiParam {integer} id 文章id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "data": {
     *          "type": 1,
     *          "type_value": "灵感",
     *          "title": "这是第一篇",
     *          "content": "这是第一篇",
     *          "url": "www.baidu.com",
     *          "status": 0,
     *          "cover_id": 1,
     *          "cover": null,
     *          "image": []
     *      },
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     * }
     */
    public function show(Request $request)
    {
        $id = (int)$request->input('id');

        $column = Column::where(['status' => 1, 'id' => $id])->first();
        if (!$column) {
            return $this->response->array($this->apiError('not found', 404));
        }

        return $this->response->item($column, new ColumnTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /column/lists 栏目文章列表
     * @apiVersion 1.0.0
     * @apiName column columnLists
     * @apiGroup column
     *
     * @apiParam {integer} type 类型；1.灵感 2.轮播图
     * @apiParam {integer} page 页数
     * @apiParam {integer} per_page 页面条数
     * @apiParam {integer} facility 设备；1.pc端 2.手机端
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
    public function lists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $type = $request->type;
        $facility = $request->facility;

        $query = Column::query()->where('status', 1);

        if ($type != 0) {
            $query->where('type', (int)$type);
        }
        if ($facility != 0) {
            $query->where('facility', (int)$facility);
        }
        $lists = $query->orderBy('sort', 'desc')->paginate($per_page);

        return $this->response->paginator($lists, new ColumnTransformer)->setMeta($this->apiMeta());
    }

}
