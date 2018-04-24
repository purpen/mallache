<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\NodesTransformer;
use App\Models\Nodes;
use App\Models\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NodesController extends BaseController
{
    /**
     * @api {post} /nodes/create 设计工具--创建节点
     * @apiVersion 1.0.0
     * @apiName nodes create
     * @apiGroup nodes
     *
     * @apiParam {string} name 节点名称
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": {
     *          "id": 1,
     *          "name": "项目开始的阶段",   // 节点名称
     *          "user_id": 6,             // 操作用户
     *       },
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function create(Request $request)
    {
        $rules = [
            'name' => 'required|max:100',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('errors', $validator->errors());
        }
        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);
        if (!$design_company_id) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $name = $request->input('name');

        $count = Nodes::where(['name' => $name, 'design_company_id' => $design_company_id])->count();
        if ($count > 0) {
            return $this->response->array($this->apiError('名称重复', 403));
        }

        $nodes = new Nodes();
        $nodes->name = $name;
        $nodes->design_company_id = $design_company_id;
        $nodes->user_id = $user_id;
        $nodes->save();

        return $this->response->item($nodes, new NodesTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /nodes/update 设计工具--更新节点
     * @apiVersion 1.0.0
     * @apiName nodes update
     * @apiGroup nodes
     *
     * @apiParam {integer} id 节点ID
     * @apiParam {string} name 节点名称
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": {
     *          "id": 1,
     *          "name": "项目开始的阶段",   // 节点名称
     *          "user_id": 6,             // 操作用户
     *       },
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function update(Request $request)
    {
        $rules = [
            'id' => 'required|integer',
            'name' => 'required|max:100',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('errors', $validator->errors());
        }

        $nodes = Nodes::find($request->input('id'));
        if (!$nodes) {
            return $this->response->array($this->apiError('not found', 404));
        }

        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);
        if (!$design_company_id || ($design_company_id != $nodes->design_company_id)) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $name = $request->input('name');

        $count = Nodes::where(['name' => $name, 'design_company_id' => $design_company_id])->count();
        if ($count > 0) {
            return $this->response->array($this->apiError('名称重复', 403));
        }

        $nodes->name = $name;
        $nodes->user_id = $user_id;
        $nodes->save();

        return $this->response->item($nodes, new NodesTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {delete} /nodes/delete 设计工具--删除节点
     * @apiVersion 1.0.0
     * @apiName nodes delete
     * @apiGroup nodes
     *
     * @apiParam {integer} id 节点ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function delete(Request $request)
    {
        $nodes = Nodes::find($request->input('id'));
        if (!$nodes) {
            return $this->response->array($this->apiError('not found', 404));
        }

        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);
        if (!$design_company_id || ($design_company_id != $nodes->design_company_id)) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $nodes->delete();

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {get} /nodes/lists 设计工具--节点列表
     * @apiVersion 1.0.0
     * @apiName nodes lists
     * @apiGroup nodes
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": [{
     *          "id": 1,
     *          "name": "项目开始的阶段",   // 节点名称
     *          "user_id": 6,             // 操作用户
     *       }],
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function lists()
    {
        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);
        if (!$design_company_id) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $lists = Nodes::where(['design_company_id' => $design_company_id])->get();

        return $this->response->collection($lists, new NodesTransformer())->setMeta($this->apiMeta());
    }

}
