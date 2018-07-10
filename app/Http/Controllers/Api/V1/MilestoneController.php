<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\DesignSubstageTransformer;
use App\Http\Transformer\MilestoneTransformer;
use App\Models\AssetModel;
use App\Models\DesignProject;
use App\Models\DesignStage;
use App\Models\DesignSubstage;
use App\Models\ItemUser;
use App\Models\Milestone;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MilestoneController extends BaseController
{
    /**
     * id    int(10)    否
     * design_project_id    int(10)            项目ID
     * design_stage_id    int(10)            设计阶段ID
     * name    varchar(100)    否        名称
     * start_time    int(10)            开始时间
     * summary    varchar(500)            描述
     * user_id    int(10)            操作用户ID
     * status    tinyint(4)            状态：0.未完成 1.完成
     */

    /**
     * @api {post} /milestone/create 设计工具--创建里程碑
     * @apiVersion 1.0.0
     * @apiName milestone create
     * @apiGroup milestone
     *
     * @apiParam {integer} design_stage_id 阶段ID
     * @apiParam {integer} start_time 开始时间
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": {
     *          "id": 1,
     *          "design_project_id": "4",  // 项目ID
     *          "design_stage_id": "1",    // 父阶段ID
     *          "name": "项目开始的阶段",   // 阶段名称
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
            'start_time' => 'required|integer',
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
        $start_time = $request->input('start_time');
        //检查里程碑是否创建过
        $milestones = Milestone::where('design_stage_id' , $design_stage_id)->where('start_time' , $start_time)->first();
        if($milestones){
            return $this->response->array($this->apiError('该里程碑已经存在', 412));
        }else{
            $milestone = new Milestone();
            $milestone->design_project_id = $design_project_id;
            $milestone->design_stage_id = $design_stage_id;
            $milestone->name = $request->input('name') ?? '';
            $milestone->start_time = $request->input('start_time');
            $milestone->summary = $request->input('summary') ?? '';
            $milestone->user_id = $this->auth_user_id;
            $milestone->status = 0;
            $milestone->save();

            return $this->response->item($milestone, new MilestoneTransformer())->setMeta($this->apiMeta());
        }



    }

    /**
     * @api {put} /milestone/update 设计工具--更新里程碑
     * @apiVersion 1.0.0
     * @apiName milestone update
     * @apiGroup milestone
     *
     * @apiParam {integer} id 里程碑ID
     * @apiParam {string} name 阶段名称
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
            'name' => 'max:100',
            'summary' => 'max:500',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('errors', $validator->errors());
        }

        $id = $request->input('id');
        $milestone = Milestone::find($id);
        if (!$milestone) {
            return $this->response->array($this->apiError('not found', 404));
        }

        $design_project_id = $milestone->design_project_id;
        $design_project = DesignProject::find($design_project_id);
        if (!$design_project) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if (!$design_project->isPower($this->auth_user_id)) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $arr = array_diff($request->all(), [null]);

        $milestone->update($arr);

        return $this->response->item($milestone, new MilestoneTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /milestone 设计工具--里程碑详情
     * @apiVersion 1.0.0
     * @apiName milestone show
     * @apiGroup milestone
     *
     * @apiParam {integer} milestone_id 里程碑ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": {
     *          "id": 1,
     *          "design_project_id": "4",  // 项目ID
     *          "design_stage_id": "1",    // 父阶段ID
     *          "name": "项目开始的阶段",   // 阶段名称
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
        $milestone_id = $request->input('milestone_id');
        $milestone = Milestone::find($milestone_id);
        if (!$milestone) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if (!ItemUser::checkUser($milestone->design_project_id, $this->auth_user_id)) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        return $this->response->item($milestone, new MilestoneTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {delete} /milestone/delete 设计工具--删除里程碑
     * @apiVersion 1.0.0
     * @apiName milestone delete
     * @apiGroup milestone
     *
     * @apiParam {integer} milestone_id 里程碑ID
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
        $milestone_id = $request->input('milestone_id');
        $milestone = Milestone::find($milestone_id);
        if (!$milestone) {
            return $this->response->array($this->apiError('not found', 404));
        }

        $design_project_id = $milestone->design_project_id;
        $design_project = DesignProject::find($design_project_id);
        if (!$design_project) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if (!$design_project->isPower($this->auth_user_id)) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $milestone->delete();

        return $this->response->array($this->apiSuccess());
    }


    /**
     * @api {put} /milestone/completes 设计工具--里程碑完成与未完成
     * @apiVersion 1.0.0
     * @apiName milestone completes
     * @apiGroup milestone
     *
     * @apiParam {integer} milestone_id 里程碑ID
     * @apiParam {integer} status 0.未完成 1.完成
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": {
     *          "id": 1,
     *          "design_project_id": "4",  // 项目ID
     *          "design_stage_id": "1",    // 父阶段ID
     *          "name": "项目开始的阶段",   // 阶段名称
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
    public function completes(Request $request)
    {
        $milestone_id = $request->input('milestone_id' , 0);
        $status = $request->input('status');
        $milestone = Milestone::find($milestone_id);

        $milestone->status = $status;

        if($milestone->save()){
            return $this->response->item($milestone, new MilestoneTransformer())->setMeta($this->apiMeta());
        }

    }


}
