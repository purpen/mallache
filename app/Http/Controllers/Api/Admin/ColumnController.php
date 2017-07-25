<?php

namespace App\Http\Controllers\Api\Admin;


use App\Http\AdminTransformer\AdminColumnListsTransformer;
use App\Http\AdminTransformer\AdminColumnTransformer;
use App\Models\Column;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ColumnController extends BaseController
{
    /**
     * @api {post} /column 添加栏目文章
     * @apiVersion 1.0.0
     * @apiName bank columnStore
     * @apiGroup AdminColumn
     *
     * @apiParam {integer} type *栏目类型：1.灵感；
     * @apiParam {string} title *文章标题
     * @apiParam {string} content *内容
     * @apiParam {string} url 链接
     * @apiParam {integer} sort 排序权重
     * @apiParam {integer} cover_id 封面图ID
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
    public function store(Request $request)
    {
        $rules = [
            'type' => 'required|integer',
            'title' => 'required|max:100',
            'content' => 'required|max:500',
            'url' => 'max:200',
            'sort' => 'integer',
            'cover_id' => 'required|integer',
        ];

        $all = $request->all();
        $validator = Validator::make($all, $rules);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $all['status'] = 0;
        if (!$column = Column::create($all)) {
            return $this->response->array($this->apiError('添加失败', 500));
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {put} /column 更新栏目文章
     * @apiVersion 1.0.0
     * @apiName bank columnUpdate
     * @apiGroup AdminColumn
     *
     * @apiParam {integer} type 栏目类型：1.灵感；
     * @apiParam {string} title 文章标题
     * @apiParam {string} content 内容
     * @apiParam {string} url 链接
     * @apiParam {integer} sort 排序权重
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
    public function update(Request $request)
    {
        $rules = [
            'id' => 'required|integer',
            'type' => 'integer',
            'title' => 'max:100',
            'content' => 'max:500',
            'url' => 'max:200',
            'sort' => 'integer',
            'cover_id' => 'integer',
        ];

        $all = $request->all();
        $validator = Validator::make($all, $rules);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $column = Column::find($request->input('id'));
        if (!$column) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if (!$column = $column->update($all)) {
            return $this->response->array($this->apiError('更新失败', 500));
        }

        return $this->response->array($this->apiSuccess());
    }

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