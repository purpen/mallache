<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\DesignStageNodeTransformer;
use App\Models\DesignProject;
use App\Models\DesignStageNode;
use App\Models\DesignSubstage;
use App\Models\ItemUser;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DesignStageNodeController extends BaseController
{
    /**
     * name    varchar(100)            名称
     * time    int(10)            截止时间
     * status    tinyint(4)            状态：1.未完成 2.已完成
     * is_owner    tinyint(4)            甲方参与：0.否 1.是
     * design_project_id    int(10)            项目ID
     * design_substage_id    int(10)            子阶段ID
     */
    /**
     * @api {post} /designStageNode/create 设计工具--创建阶段节点
     * @apiVersion 1.0.0
     * @apiName designStageNode create
     * @apiGroup designStageNode
     *
     * @apiParam {integer} design_substage_id 子阶段ID
     * @apiParam {string} name 阶段名称
     * @apiParam {integer} time 截止时间
     * @apiParam {integer} is_owner 甲方参与：0.否 1.是
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": {
     *
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
            'design_substage_id' => 'required|integer',
            'name' => 'required|max:100',
            'time' => 'required|integer',
            'is_owner' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('errors', $validator->errors());
        }

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

        $design_stage_node = new DesignStageNode();
        $design_stage_node->design_substage_id = $design_substage_id;
        $design_stage_node->design_project_id = $design_project_id;
        $design_stage_node->name = $request->input('name');
        $design_stage_node->time = $request->input('time');
        $design_stage_node->is_owner = $request->input('is_owner');
        $design_stage_node->save();

        return $this->response->item($design_stage_node, new DesignStageNodeTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /designStageNode/update 设计工具--编辑阶段节点
     * @apiVersion 1.0.0
     * @apiName designStageNode update
     * @apiGroup designStageNode
     *
     * @apiParam {integer} stage_node_id 节点ID
     * @apiParam {string} name 阶段名称
     * @apiParam {integer} time 截止时间
     * @apiParam {integer} is_owner 甲方参与：0.否 1.是
     * @apiParam {integer} status 状态：1.未完成 2.已完成
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": {
     *
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
            'stage_node_id' => 'required|integer',
            'name' => 'max:100',
            'time' => 'integer',
            'is_owner' => 'integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('errors', $validator->errors());
        }

        $stage_node_id = $request->input('stage_node_id');
        $stage_node = DesignStageNode::find($stage_node_id);
        if (!$stage_node) {
            return $this->response->array($this->apiError('not found', 404));
        }

        $design_project_id = $stage_node->design_project_id;
        $design_project = DesignProject::find($design_project_id);
        if (!$design_project) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if (!$design_project->isPower($this->auth_user_id)) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $stage_node->update($request->all());

        return $this->response->item($stage_node, new DesignStageNodeTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /designStageNode 设计工具--阶段节点详情
     * @apiVersion 1.0.0
     * @apiName designStageNode show
     * @apiGroup designStageNode
     *
     * @apiParam {integer} stage_node_id 节点ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": {
     *
     *       },
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function show(Request $request)
    {
        $stage_node_id = $request->input('stage_node_id');
        $stage_node = DesignStageNode::find($stage_node_id);
        if (!$stage_node) {
            return $this->response->array($this->apiError('not found', 404));
        }

        $design_project_id = $stage_node->design_project_id;
        if (!ItemUser::checkUser($design_project_id, $this->auth_user_id)) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        return $this->response->item($stage_node, new DesignStageNodeTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {delete} /designStageNode/delete 设计工具--删除阶段节点
     * @apiVersion 1.0.0
     * @apiName designStageNode delete
     * @apiGroup designStageNode
     *
     * @apiParam {integer} stage_node_id 节点ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": {
     *
     *       },
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function delete(Request $request)
    {
        $stage_node_id = $request->input('stage_node_id');
        $stage_node = DesignStageNode::find($stage_node_id);
        if (!$stage_node) {
            return $this->response->array($this->apiError('not found', 404));
        }

        $design_project_id = $stage_node->design_project_id;
        $design_project = DesignProject::find($design_project_id);
        if (!$design_project) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if (!$design_project->isPower($this->auth_user_id)) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $stage_node->delete();

        return $this->response->array($this->apiSuccess());
    }

}
