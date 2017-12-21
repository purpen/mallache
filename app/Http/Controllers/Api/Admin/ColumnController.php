<?php

namespace App\Http\Controllers\Api\Admin;


use App\Http\AdminTransformer\AdminColumnListsTransformer;
use App\Http\AdminTransformer\AdminColumnTransformer;
use App\Models\AssetModel;
use App\Models\Column;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ColumnController extends BaseController
{
    /**
     * @api {post} /admin/column 添加栏目文章
     * @apiVersion 1.0.0
     * @apiName column columnStore
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

        $all['url'] = $request->input('url') ?? '';
        $all['status'] = 0;
        $all['user_id'] = $this->auth_user_id;
        if (!$column = Column::create($all)) {
            return $this->response->array($this->apiError('添加失败', 500));
        }

        if($random = $request->input('random')){
            AssetModel::setRandom($column->id, $random);
        }


        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {put} /admin/column 更新栏目文章
     * @apiVersion 1.0.0
     * @apiName column columnUpdate
     * @apiGroup AdminColumn
     *
     * @apiParam {integer} id 文章ID
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
     * @api {get} /admin/column 栏目文章详情
     * @apiVersion 1.0.0
     * @apiName column columnShow
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
     * @api {get} /admin/column/lists 栏目文章列表
     * @apiVersion 1.0.0
     * @apiName column columnLists
     * @apiGroup AdminColumn
     *
     * @apiParam {integer} type 类型；1.灵感
     * @apiParam {integer} status 状态 -1.未发布；0.全部；1.发布；
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



        $query = Column::query();
        if ($type != 0) {
            $query->where('type', (int)$type);
        }
        if ($status != null) {
            $query->where('status', (int)$status);
        }

        $lists = $query->paginate($per_page);

        return $this->response->paginator($lists, new AdminColumnListsTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /admin/column/changeStatus 栏目文章变更状态
     * @apiVersion 1.0.0
     * @apiName column changeStatus
     * @apiGroup AdminColumn
     *
     * @apiParam {integer} id 栏目文章ID
     * @apiParam {integer} status 状态 0.未发布；1.发布；
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
    public function changeStatus(Request $request)
    {
        $id = $request->input("id");
        $status = $request->input("status");

        $column = Column::find($id);
        if (!$column) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if ($status) {
            $column->status = 1;
        } else {
            $column->status = 0;
        }
        if (!$column->save()) {
            return $this->response->array($this->apiError('Error', 500));
        } else {
            return $this->response->array($this->apiSuccess());
        }
    }

    /**
     * @api {delete} /admin/column 栏目文章删除
     * @apiVersion 1.0.0
     * @apiName column delete
     * @apiGroup AdminColumn
     *
     * @apiParam {integer} id 栏目文章ID
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
        Column::destroy($id);

        return $this->response->array($this->apiSuccess());
    }

}
