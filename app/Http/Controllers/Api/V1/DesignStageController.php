<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\DesignStageTransformer;
use App\Models\DesignProject;
use App\Models\DesignStage;
use App\Models\ItemUser;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * 设计项目阶段规划
 *
 * Class DesignStageController
 * @package App\Http\Controllers\Api\V1
 */
class DesignStageController extends BaseController
{

    /**
     * @api {post} /designStage/create 设计工具--创建项目阶段
     * @apiVersion 1.0.0
     * @apiName designStage create
     * @apiGroup designStage
     *
     * @apiParam {integer} design_project_id 项目ID
     * @apiParam {string} name 阶段名称
     * @apiParam {integer} duration 投入时间
     * @apiParam {integer} start_time 开始时间
     * @apiParam {string} content 交付内容
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": {
     *          "id": 1,
     *          "design_project_id": "4",  // 项目ID
     *          "name": "项目开始的阶段",   // 阶段名称
     *          "duration": "1",           // 投入时间
     *          "start_time": "1234567",   // 开始时间
     *          "content": "我是描述",     // 交付内容
     *          "user_id": 6,             // 操作用户
     *          "status": 0               // 状态：0.未完成 1.完成
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
            'design_project_id' => 'required|integer',
            'name' => 'required|max:100',
            'duration' => 'required|integer',
            'start_time' => 'required|integer',
            'content' => 'max:500',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('errors', $validator->errors());
        }

        $design_project_id = $request->input('design_project_id');
        $design_project = DesignProject::find($design_project_id);
        if (!$design_project) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if (!$design_project->isPower($this->auth_user_id)) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $design_stage = new DesignStage();
        $design_stage->design_project_id = $design_project_id;
        $design_stage->name = $request->input('name');
        $design_stage->duration = $request->input('duration');
        $design_stage->start_time = $request->input('start_time');
        $design_stage->content = $request->input('content') ?? '';
        $design_stage->user_id = $this->auth_user_id;
        $design_stage->status = 0;
        $design_stage->save();

        return $this->response->item($design_stage, new DesignStageTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /designStage/update 设计工具--更新项目阶段
     * @apiVersion 1.0.0
     * @apiName designStage update
     * @apiGroup designStage
     *
     * @apiParam {integer} id 阶段ID
     * @apiParam {string} name 阶段名称
     * @apiParam {integer} duration 投入时间
     * @apiParam {integer} start_time 开始时间
     * @apiParam {string} content 交付内容
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": {
     *          "id": 1,
     *          "design_project_id": "4",  // 项目ID
     *          "name": "项目开始的阶段",   // 阶段名称
     *          "duration": "1",           // 投入时间
     *          "start_time": "1234567",   // 开始时间
     *          "content": "我是描述",     // 交付内容
     *          "user_id": 6,             // 操作用户
     *          "status": 0               // 状态：0.未完成 1.完成
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
            'name' => 'max:100',
            'duration' => 'integer',
            'start_time' => 'integer',
            'content' => 'max:500',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('errors', $validator->errors());
        }

        $id = $request->input('id');
        $design_stage = DesignStage::find($id);
        if (!$design_stage) {
            return $this->response->array($this->apiError('not found', 404));
        }
        $design_project = DesignProject::find($design_stage->design_project_id);
        if (!$design_project) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if (!$design_project->isPower($this->auth_user_id)) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $design_stage->update($request->all());

        return $this->response->item($design_stage, new DesignStageTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /designStage 设计工具--项目阶段详情
     * @apiVersion 1.0.0
     * @apiName designStage show
     * @apiGroup designStage
     *
     * @apiParam {integer} id 阶段ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": {
     *          "id": 1,
     *          "design_project_id": "4",  // 项目ID
     *          "name": "项目开始的阶段",   // 阶段名称
     *          "duration": "1",           // 投入时间
     *          "start_time": "1234567",   // 开始时间
     *          "content": "我是描述",     // 交付内容
     *          "user_id": 6,             // 操作用户
     *          "status": 0               // 状态：0.未完成 1.完成
     *       },
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function show(Request $request)
    {
        $id = $request->input('id');
        $design_stage = DesignStage::find($id);
        if (!$design_stage) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if (!ItemUser::checkUser($design_stage->design_project_id, $this->auth_user_id)) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        return $this->response->item($design_stage, new DesignStageTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {delete} /designStage/delete 设计工具--项目阶段删除
     * @apiVersion 1.0.0
     * @apiName designStage delete
     * @apiGroup designStage
     *
     * @apiParam {integer} id 阶段ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": {
     *          "id": 1,
     *          "design_project_id": "4",  // 项目ID
     *          "name": "项目开始的阶段",   // 阶段名称
     *          "duration": "1",           // 投入时间
     *          "start_time": "1234567",   // 开始时间
     *          "content": "我是描述",     // 交付内容
     *          "user_id": 6,             // 操作用户
     *          "status": 0               // 状态：0.未完成 1.完成
     *       },
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function delete(Request $request)
    {
        $id = $request->input('id');
        $design_stage = DesignStage::find($id);
        if (!$design_stage) {
            return $this->response->array($this->apiError('not found', 404));
        }
        $design_project = DesignProject::find($design_stage->design_project_id);
        if (!$design_project) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if (!$design_project->isPower($this->auth_user_id)) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $design_stage->deleteStage();
        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {get} /designStage/lists 设计工具--项目阶段列表
     * @apiVersion 1.0.0
     * @apiName designStage lists
     * @apiGroup designStage
     *
     * @apiParam {integer} design_project_id 项目ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": [{
     *          "id": 1,
     *          "design_project_id": "4",  // 项目ID
     *          "name": "项目开始的阶段",   // 阶段名称
     *          "duration": "1",           // 投入时间
     *          "start_time": "1234567",   // 开始时间
     *          "content": "我是描述",     // 交付内容
     *          "user_id": 6,             // 操作用户
     *          "status": 0               // 状态：0.未完成 1.完成
     *          "design_substage": [{     // 子阶段信息
     *              "id": 1,                // 子阶段ID
     *              "design_project_id": 4,     // 项目ID
     *              "design_stage_id": 1,       // 父阶段ID
     *              "name": "我狮子阶段",        // 子阶段名称
     *              "execute_user_id": null,    // 执行人
     *              "duration": 1,              // 投入时间
     *              "start_time": 123479,
     *              "summary": "面熟面熟",          // 描述
     *              "user_id": 6,
     *              "status": 0,
     *              "design_stage_node": {      // 节点信息
     *                  "id": 1,
     *                  "name": "第一个节点编辑",
     *                  "time": 12,                 // 节点时间
     *                  "is_owner": 0,              // 甲方参与：0.否 1.是
     *                  "design_substage_id": 1,    // 子阶段ID
     *                  "design_project_id": 4,
     *                  "status": 0,
     *                  "asset": []                 // 附件
     *              }
     *          }]
     *       }],
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function lists(Request $request)
    {
        $design_project_id = $request->input('design_project_id');

        if (!ItemUser::checkUser($design_project_id, $this->auth_user_id)) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $lists = DesignStage::with('designSubstage')
            ->where('design_project_id', $design_project_id)
            ->get();

        return $this->response->collection($lists, new DesignStageTransformer())->setMeta($this->apiMeta());
    }
}
