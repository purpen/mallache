<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\DesignSubstageTransformer;
use App\Models\DesignProject;
use App\Models\DesignStage;
use App\Models\DesignSubstage;
use App\Models\ItemUser;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DesignSubstageController extends BaseController
{
    /**
     * id    int(10)    否
     * design_project_id    int(10)            项目ID
     * design_stage_id    int(10)            设计阶段ID
     * name    varchar(100)    否        名称
     * execute_user_id    int(10)            执行人 (跟任务是否同步)
     * duration    int(10)            投入时间
     * start_time    int(10)            开始时间
     * summary    varchar(500)            描述
     * stage_node_id    int(10)        null    节点ID
     * user_id    int(10)            操作用户ID
     * status    tinyint(4)            状态：0.未完成 1.完成
     */

    /**
     * @api {post} /designSubstage/create 设计工具--创建子阶段
     * @apiVersion 1.0.0
     * @apiName designSubstage create
     * @apiGroup designSubstage
     *
     * @apiParam {integer} design_stage_id 阶段ID
     * @apiParam {integer} execute_user_id 执行人
     * @apiParam {string} name 阶段名称
     * @apiParam {integer} duration 投入时间
     * @apiParam {integer} start_time 开始时间
     * @apiParam {string} summary 描述
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": {
     *          "id": 1,
     *          "design_project_id": "4",  // 项目ID
     *          "design_stage_id": "1",    // 父阶段ID
     *          "name": "项目开始的阶段",   // 阶段名称
     *          "execute_user_id": null,  // 执行人
     *          "duration": "1",           // 投入时间
     *          "start_time": "1234567",   // 开始时间
     *          "summary": "我是描述",     // 交付内容
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
            'design_stage_id' => 'required|integer',
            'execute_user_id' => 'integer',
            'name' => 'required|max:100',
            'duration' => 'required|integer',
            'start_time' => 'required|integer',
            'summary' => 'max:500',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('errors', $validator->errors());
        }

        $design_stage_id = $request->input('design_stage_id');
        $design_stage = DesignStage::find($design_stage_id);
        if (!$design_stage) {
            return $this->response->array($this->apiError('not found', 404));
        }

        $design_project_id = $design_stage->design_project_id;
        $design_project = DesignProject::find($design_project_id);
        if (!$design_project) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if (!$design_project->isPower($this->auth_user_id)) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $design_substage = new DesignSubstage();
        $design_substage->design_project_id = $design_project_id;
        $design_substage->design_stage_id = $design_stage_id;
        $design_substage->name = $request->input('name');
        $design_substage->duration = $request->input('duration');
        $design_substage->start_time = $request->input('start_time');
        $design_substage->summary = $request->input('summary') ?? '';
        $design_substage->user_id = $this->auth_user_id;
        $design_substage->status = 0;
        $design_substage->save();

        return $this->response->item($design_substage, new DesignSubstageTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /designSubstage/update 设计工具--更新子阶段
     * @apiVersion 1.0.0
     * @apiName designSubstage update
     * @apiGroup designSubstage
     *
     * @apiParam {integer} id 子阶段ID
     * @apiParam {integer} execute_user_id 执行人
     * @apiParam {string} name 阶段名称
     * @apiParam {integer} duration 投入时间
     * @apiParam {integer} start_time 开始时间
     * @apiParam {string} summary 描述
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": {
     *          "id": 1,
     *          "design_project_id": "4",  // 项目ID
     *          "design_stage_id": "1",    // 父阶段ID
     *          "name": "项目开始的阶段",   // 阶段名称
     *          "execute_user_id": null,  // 执行人
     *          "duration": "1",           // 投入时间
     *          "start_time": "1234567",   // 开始时间
     *          "summary": "我是描述",     // 交付内容
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
            'execute_user_id' => 'integer',
            'name' => 'max:100',
            'duration' => 'integer',
            'start_time' => 'integer',
            'summary' => 'max:500',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('errors', $validator->errors());
        }

        $id = $request->input('id');
        $design_substage = DesignSubstage::find($id);
        if (!$design_substage) {
            return $this->response->array($this->apiError('not found', 404));
        }

        $design_project_id = $design_substage->design_project_id;
        $design_project = DesignProject::find($design_project_id);
        if (!$design_project) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if (!$design_project->isPower($this->auth_user_id)) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $arr = array_diff($request->all(), [null]);

        $design_substage->update($arr);

        return $this->response->item($design_substage, new DesignSubstageTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /designSubstage 设计工具--子阶段详情
     * @apiVersion 1.0.0
     * @apiName designSubstage show
     * @apiGroup designSubstage
     *
     * @apiParam {integer} design_substage_id 子阶段ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": {
     *          "id": 1,
     *          "design_project_id": "4",  // 项目ID
     *          "design_stage_id": "1",    // 父阶段ID
     *          "name": "项目开始的阶段",   // 阶段名称
     *          "execute_user_id": null,  // 执行人
     *          "duration": "1",           // 投入时间
     *          "start_time": "1234567",   // 开始时间
     *          "summary": "我是描述",     // 交付内容
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
        $design_substage_id = $request->input('design_substage_id');
        $design_substage = DesignSubstage::find($design_substage_id);
        if (!$design_substage) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if (!ItemUser::checkUser($design_substage->design_project_id, $this->auth_user_id)) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        return $this->response->item($design_substage, new DesignSubstageTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {delete} /designSubstage/delete 设计工具--删除子阶段
     * @apiVersion 1.0.0
     * @apiName designSubstage delete
     * @apiGroup designSubstage
     *
     * @apiParam {integer} design_substage_id 子阶段ID
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
        $design_substage_id = $request->input('design_substage_id');
        $design_substage = DesignSubstage::find($design_substage_id);
        if (!$design_substage) {
            return $this->response->array($this->apiError('not found', 404));
        }

        $design_project_id = $design_substage->design_project_id;
        $design_project = DesignProject::find($design_project_id);
        if (!$design_project) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if (!$design_project->isPower($this->auth_user_id)) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $design_substage->deleteSubstage();

        return $this->response->array($this->apiSuccess());
    }


}
