<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\ColumnTransformer;
use App\Models\Column;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ColumnController extends BaseController
{
    /**
     * @api {get} /column 栏目文章详情
     * @apiVersion 1.0.0
     * @apiName bank columnShow
     * @apiGroup AdminColumn
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
        $id = $request->input('id');

        $column = Column::find($id);
        if (!$column) {
            return $this->response->array($this->apiError('not found', 404));
        }

        return $this->response->item($column, new AdminColumnTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /column/lists 栏目文章列表
     * @apiVersion 1.0.0
     * @apiName bank columnLists
     * @apiGroup AdminColumn
     *
     * @apiParam {integer} type 类型；1.灵感
     * @apiParam {integer} status 状态 0.默认；1.
     * @apiParam {integer} page 页数
     * @apiParam {integer} per_page 页面条数
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
        $status = $request->status;

        $query = Column::query();

        if ($type !== null) {
            $query->where('type', (int)$type);
        }
        if ($status !== null) {
            $query->where('status', (int)$status);
        }

        $lists = $query->paginate($per_page);

        return $this->response->paginator($lists, new AdminColumnListsTransformer)->setMeta($this->apiMeta());
    }

}
