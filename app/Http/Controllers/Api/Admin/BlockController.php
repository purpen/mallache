<?php

namespace App\Http\Controllers\Api\Admin;



use App\Http\AdminTransformer\BlockTransformer;
use App\Models\Block;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlockController extends BaseController
{
    /**
     * @api {post} /admin/block 添加
     * @apiVersion 1.0.0
     * @apiName block store
     * @apiGroup AdminBlock
     *
     * @apiParam {string} name
     * @apiParam {string} mark
     * @apiParam {text} content
     * @apiParam {text} code
     * @apiParam {integer} type
     * @apiParam {integer} count
     * @apiParam {string} summary
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
            'name' => 'required|max:50',
            'mark' => 'required|max:20',
            'summary' => 'max:1000',
        ];

        $all = $request->all();
        $validator = Validator::make($all, $rules);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        $all['user_id'] = $this->auth_user_id;
        if (!$block = Block::create($all)) {
            return $this->response->array($this->apiError('添加失败', 500));
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {put} /admin/block 更改
     * @apiVersion 1.0.0
     * @apiName block update
     * @apiGroup AdminBlock
     *
     * @apiParam {integer} id
     * @apiParam {string} name
     * @apiParam {string} mark
     * @apiParam {text} content
     * @apiParam {text} code
     * @apiParam {integer} type
     * @apiParam {integer} count
     * @apiParam {string} summary
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
            'name' => 'required|max:50',
            'mark' => 'required|max:20',
            'summary' => 'max:1000',
        ];

        $all = $request->all();
        $validator = Validator::make($all, $rules);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $block = Block::find($request->input('id'));
        if (!$block) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if (!$block = $block->update($all)) {
            return $this->response->array($this->apiError('更新失败', 500));
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {get} /admin/block 详情
     * @apiVersion 1.0.0
     * @apiName block show
     * @apiGroup AdminBlock
     *
     * @apiParam {integer} id 文章id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *
     * {
     *      "data": {
                "id": 1,
                "name": "test",
                "type": 1,
                "status": 0,
                "user_id": 0,
                "code": 0,
                "content": 0,
                "summary": 0,
                "count": 0
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

        $block = Block::find($id);
        if (!$block) {
            return $this->response->array($this->apiError('not found', 404));
        }

        return $this->response->item($block, new BlockTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /admin/block/list 列表
     * @apiVersion 1.0.0
     * @apiName block list
     * @apiGroup AdminBlock
     *
     * @apiParam {integer} status 状态 0.禁用；1.启用；
     * @apiParam {integer} page 页数
     * @apiParam {integer} per_page 页面条数
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
            {
                "data": [
                    {
                        "id": 1,
                        "name": "test",
                        "type": 1,
                        "status": 0,
                        "user_id": 0,
                        "code": 0,
                        "content": 0,
                        "summary": 0,
                        "count": 0
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
     */
    public function lists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $status = $request->input('status') ? (int)$request->input('status') : 0;

        $query = array();
        if ($status) $query['status'] = $status;


        $lists = Block::where($query)
            ->orderBy('id', 'desc')
            ->paginate($per_page);

        return $this->response->paginator($lists, new BlockTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /admin/block/changeStatus 变更状态
     * @apiVersion 1.0.0
     * @apiName block changeStatus
     * @apiGroup AdminBlock
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
    public function changeStatus(Request $request)
    {
        $id = $request->input("id");
        $status = $request->input("status");

        $block = Block::find($id);
        if (!$block) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if ($status) {
            $block->status = 1;
        } else {
            $block->status = 0;
        }
        if (!$block->save()) {
            return $this->response->array($this->apiError('Error', 500));
        } else {
            return $this->response->array($this->apiSuccess());
        }
    }

    /**
     * @api {delete} /admin/block 删除
     * @apiVersion 1.0.0
     * @apiName block delete
     * @apiGroup AdminBlock
     *
     * @apiParam {integer} id ID
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
        Block::destroy($id);

        return $this->response->array($this->apiSuccess());
    }

}
