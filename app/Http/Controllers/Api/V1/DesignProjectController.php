<?php

namespace App\Http\Controllers\Api\V1;

use App\Helper\MassageException;
use App\Http\Transformer\DesignProjectTransformer;
use App\Models\AssetModel;
use App\Models\DesignCompanyModel;
use App\Models\DesignProject;
use App\Models\ItemUser;
use App\Models\PanDirector;
use App\Models\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * 设计项目管理
 * Class DesignProjectController
 * @package App\Http\Controllers\Api\V1
 */
class DesignProjectController extends BaseController
{
    /**
     * @api {get} /designProject/lists 设计工具个人参与项目展示
     * @apiVersion 1.0.0
     * @apiName designProject lists
     * @apiGroup designProject
     *
     * @apiParam {int} status 状态：1.正常 2.回收站
     * @apiParam {integer} page 页数
     * @apiParam {integer} per_page 页面条数
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": [{
     *          "id": 2,
     *          "name": "xianmuguanli",  // 项目名称
     *          "description": "我会描述",  // 项目描述
     *          "level": "1",               // 项目等级 1.普通 2.紧急 3.非常紧急
     *          "business_manager": null,       // 商务经理ID
     *          "business_manager_value": null, // 商务经理
     *          "leader": null,  // 项目负责人
     *          "leader_value": null,  // 项目负责人
     *          "cost": null,  // 项目费用
     *          "workplace": null, // 工作地点
     *          "field": null,  // 产品所属领域
     *          "field_value": "",
     *          "industry": null,   // 产品所属行业
     *          "industry_value": "", //
     *          "start_time": null,   // 项目开始时间
     *          "project_duration": null,  // 项目工期
     *          "company_name": null,     // 客户名称
     *          "contact_name": null,       // 联系人姓名
     *          "position": null,    // 职位
     *          "phone": null,       // 手机
     *          "province": null,    // 省份
     *          "province_value": null,
     *          "city_value": null,  // 城市
     *          "area_value": null,  // 地区
     *          "address": null,    // 详细地址
     *          "user_id": 6
     *       },]
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *           "pagination": {
     *           "total": 0,
     *           "count": 0,
     *           "per_page": 10,
     *           "current_page": 1,
     *           "total_pages": 0,
     *           "links": []
     *           }
     *       }
     *   }
     */
    public function lists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $status = $request->status ?? 1;

        // 获取所在的所有项目ID
        $arr = ItemUser::projectId($this->auth_user_id);

        $lists = DesignProject::where('status', $status)
            ->whereIn('id', $arr)
            ->paginate($per_page);

        return $this->response->paginator($lists, new DesignProjectTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {post} /designProject/create 设计工具创建项目
     * @apiVersion 1.0.0
     * @apiName designProject create
     * @apiGroup designProject
     *
     * @apiParam {string} name 项目名称
     * @apiParam {string} description 项目描述
     * @apiParam {integer} level 项目等级
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": {
     *          "id": 2,
     *          "name": "xianmuguanli",  // 项目名称
     *          "description": "我会描述",  // 项目描述
     *          "level": "1",               // 项目等级 1.普通 2.紧急 3.非常紧急
     *          "business_manager": null,       // 商务经理ID
     *          "business_manager_value": null, // 商务经理
     *          "leader": null,  // 项目负责人
     *          "leader_value": null,  // 项目负责人
     *          "cost": null,  // 项目费用
     *          "workplace": null, // 工作地点
     *          "field": null,  // 产品所属领域
     *          "field_value": "",
     *          "industry": null,   // 产品所属行业
     *          "industry_value": "", //
     *          "start_time": null,   // 项目开始时间
     *          "project_duration": null,  // 项目工期
     *          "company_name": null,     // 客户名称
     *          "contact_name": null,       // 联系人姓名
     *          "position": null,    // 职位
     *          "phone": null,       // 手机
     *          "province": null,    // 省份
     *          "province_value": null,
     *          "city_value": null,  // 城市
     *          "area_value": null,  // 地区
     *          "address": null,    // 详细地址
     *          "user_id": 6
     *       },
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function create(Request $request)
    {
        try {
            $user_id = $this->auth_user_id;

            if (!$this->auth_user->isDesignAdmin()) {
                throw new MassageException('无权限', 403);
            }
            if (!$design_company_id = User::designCompanyId($user_id)) {
                throw new MassageException('无权限', 403);
            }

            $this->validate($request, [
                'name' => 'required|max:100',
                'description' => 'max:6500',
                'level' => 'required|integer|in:1,2,3',
            ]);

            DB::beginTransaction();
            $design_project = new DesignProject();
            $design_project->name = $request->input('name');
            $design_project->description = $request->input('description');
            $design_project->level = intval($request->input('level'));
            $design_project->user_id = $user_id;
            $design_project->design_company_id = $design_company_id;
            $design_project->status = 1;
            $design_project->project_type = 2;
            $design_project->save();

            // 将创建者添加入项目人员
            if (!ItemUser::addItemUser($design_project->id, $user_id, 1)) {
                throw new MassageException('项目成员添加失败', 403);
            }

            $design_company = DesignCompanyModel::find($design_company_id);
            // 自动创建项目云盘目录
            PanDirector::createProjectDir($design_company, $design_project, $user_id);

            DB::commit();
        } catch (MassageException $e) {
            DB::rollBack();
            return $this->response->array($this->apiError($e->getMessage(), $e->getCode()));
        }

        return $this->response->item($design_project, new DesignProjectTransformer())->setMeta($this->apiMeta());
    }


    /**
     * @api {put} /designProject/update 设计工具项目编辑
     * @apiVersion 1.0.0
     * @apiName designProject update
     * @apiGroup designProject
     *
     * @apiParam {int} id
     * @apiParam {string} name 项目名称
     * @apiParam {string} description 项目描述
     * @apiParam {int} level 项目等级 1.普通 2.紧急 3.非常紧急
     * @apiParam {int} business_manager  商务经理
     * @apiParam {int} leader 项目负责人
     * @apiParam {decimal} cost 项目费用
     * @apiParam {string} workplace  工作地点
     * @apiParam {integer} type  设计类型：1.产品设计；2.UI UX 设计；
     * @apiParam {json} design_types 设计类别：产品设计（1.产品策略；2.产品设计；3.结构设计；）UXUI设计（1.app设计；2.网页设计；）。[1,2]
     * @apiParam {int} field 产品所属领域
     * @apiParam {int} industry 产品所属行业
     * @apiParam {int} start_time  项目开始时间
     * @apiParam {int} project_duration 项目工期
     * @apiParam {string} company_name    varchar(100) 客户名称
     * @apiParam {string} contact_name    varchar(50) 联系人姓名
     * @apiParam {string} position  varchar(50) 职位
     * @apiParam {string} phone  varchar(20) 手机
     * @apiParam {int} province  省份
     * @apiParam {int} city  城市
     * @apiParam {int} area 地区
     * @apiParam {string} address varchar(100) 详细地址
     * @apiParam {string} project_demand  项目需求
     *
     * @apiParam {string} design_company_name    varchar(100) 设计名称
     * @apiParam {string} design_contact_name    varchar(50) 设计联系人姓名
     * @apiParam {string} design_position  varchar(50) 设计职位
     * @apiParam {string} design_phone  varchar(20) 设计手机
     * @apiParam {int} design_province  设计省份
     * @apiParam {int} design_city  设计城市
     * @apiParam {int} design_area 设计地区
     * @apiParam {string} design_address varchar(100) 设计详细地址
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": {
     *          "id": 2,
     *          "name": "xianmuguanli",  // 项目名称
     *          "description": "我会描述",  // 项目描述
     *          "level": "1",               // 项目等级 1.普通 2.紧急 3.非常紧急
     *          "business_manager": null,       // 商务经理ID
     *          "business_manager_value": null, // 商务经理
     *          "leader": null,  // 项目负责人
     *          "leader_value": null,  // 项目负责人
     *          "cost": null,  // 项目费用
     *          "workplace": null, // 工作地点
     *          "field": null,  // 产品所属领域
     *          "field_value": "",
     *          "industry": null,   // 产品所属行业
     *          "industry_value": "", //
     *          "start_time": null,   // 项目开始时间
     *          "project_duration": null,  // 项目工期
     *          "company_name": null,     // 客户名称
     *          "contact_name": null,       // 联系人姓名
     *          "position": null,    // 职位
     *          "phone": null,       // 手机
     *          "province": null,    // 省份
     *          "province_value": null,
     *          "city_value": null,  // 城市
     *          "area_value": null,  // 地区
     *          "address": null,    // 详细地址
     *          "user_id": 6
     *          "project_demand": "项目需求"  // 项目需求
     *       },
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function update(Request $request)
    {
        try {
            $id = $request->input('id');
            $user_id = $this->auth_user_id;
            $design_project = DesignProject::find($id);

            // 判断权限
            if (!$design_project->isPower($user_id)) {
                throw new MassageException('无权限', 403);
            }

            $rules = [
                'id' => 'required|integer',
                'name' => 'max:100',
                'description' => 'max:6500',
                'level' => 'integer|in:1,2,3',
                'business_manager' => 'integer',
                'leader' => 'integer',
                'cost' => 'numeric',
                'workplace' => 'max:50',
                'field' => 'integer',
                'industry' => 'integer',
                'start_time' => 'integer',
                'project_duration' => 'integer',
                'company_name' => 'max:100',
                'contact_name' => 'max:50',
                'position' => 'max:50',
                'phone' => 'max:20',
                'province' => 'integer',
                'city' => 'integer',
                'area' => 'integer',
                'address' => 'max:100',
                'design_company_name' => 'max:100',
                'design_contact_name' => 'max:50',
                'design_position' => 'max:50',
                'design_phone' => 'max:20',
                'design_province' => 'integer',
                'design_city' => 'integer',
                'design_area' => 'integer',
                'design_address' => 'max:100',
                'type' => 'integer',
                'design_types' => 'json',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                throw new StoreResourceFailedException('errors', $validator->errors());
            }

            $arr = $request->all();
            $arr = array_filter($arr, function ($v) {
                if ($v !== null) {
                    return true;
                }
                return false;
            });

            DB::beginTransaction();

            // 设置商务经理
            if (isset($arr['business_manager']) && $business_manager = $arr['business_manager']) {
                if ($business_manager_user = ItemUser::getItemUser($id, $business_manager)) {
                    $business_manager_user->changeLevel(5);
                }
            }

            // 设置项目经理
            if (isset($arr['leader']) && $leader = $arr['leader']) {
                if ($leader_user = ItemUser::getItemUser($id, $leader)) {
                    $leader_user->changeLevel(3);
                }
            }

            $design_project->update($arr);

            DB::commit();
        } catch (MassageException $e) {
            Log::error($e);
            DB::rollBack();
            return $this->response->array($this->apiError($e->getMessage(), $e->getCode()));
        }

        return $this->response->item($design_project, new DesignProjectTransformer())->setMeta($this->apiMeta());
    }


    /**
     * @api {get} /designProject 设计工具项目详情展示
     * @apiVersion 1.0.0
     * @apiName designProject show
     * @apiGroup designProject
     *
     * @apiParam {int} id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": {
     *          "id": 2,
     *          "name": "xianmuguanli",  // 项目名称
     *          "description": "我会描述",  // 项目描述
     *          "level": "1",               // 项目等级 1.普通 2.紧急 3.非常紧急
     *          "business_manager": null,       // 商务经理ID
     *          "business_manager_value": null, // 商务经理
     *          "leader": null,  // 项目负责人
     *          "leader_value": null,  // 项目负责人
     *          "cost": null,  // 项目费用
     *          "workplace": null, // 工作地点
     *          "field": null,  // 产品所属领域
     *          "field_value": "",
     *          "industry": null,   // 产品所属行业
     *          "industry_value": "", //
     *          "start_time": null,   // 项目开始时间
     *          "project_duration": null,  // 项目工期
     *          "company_name": null,     // 客户名称
     *          "contact_name": null,       // 联系人姓名
     *          "position": null,    // 职位
     *          "phone": null,       // 手机
     *          "province": null,    // 省份
     *          "province_value": null,
     *          "city_value": null,  // 城市
     *          "area_value": null,  // 地区
     *          "address": null,    // 详细地址
     *          "user_id": 6
     *          "project_demand": "项目需求"  // 项目需求
     *       },
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function show(Request $request)
    {
        try {
            $id = $request->input('id');

            if (!ItemUser::checkUser($id, $this->auth_user_id)) {
                throw new MassageException('无权限', 403);
            }

            $design_project = DesignProject::where(['id' => $id, 'status' => 1])->first();
            if (!$design_project) {
                throw new MassageException('不存在', 404);
            }
        } catch (MassageException $e) {
            return $this->response->array($this->apiError($e->getMessage(), $e->getCode()));
        }

        return $this->response->item($design_project, new DesignProjectTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /designProject/delete 设计工具项目放入回收站
     * @apiVersion 1.0.0
     * @apiName designProject delete
     * @apiGroup designProject
     *
     * @apiParam {int} id
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
        $id = $request->input('id');
        $user_id = $this->auth_user_id;

        if (!$this->auth_user->isDesignAdmin()) {
            throw new MassageException('无权限', 403);
        }
        if (!$design_company_id = User::designCompanyId($user_id)) {
            throw new MassageException('无权限', 403);
        }

        $design_project = DesignProject::where(['id' => $id, 'status' => 1, 'design_company_id' => $design_company_id])->first();
        if (!$design_project) {
            return $this->response->array($this->apiSuccess());
        }

        $design_project->status = 2;
        $design_project->save();

        return $this->response->array($this->apiSuccess());
    }


    /**
     * @api {get} /designProject/payAssets 设计工具--交付内容
     * @apiVersion 1.0.0
     * @apiName designProject payAssets
     * @apiGroup designProject
     *
     * @apiParam {int} id
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
    public function payAssets(Request $request)
    {
        $id = $request->input('id');

        if (!ItemUser::checkUser($id, $this->auth_user_id)) {
            throw new MassageException('无权限', 403);
        }

        $design_project = DesignProject::where(['id' => $id, 'status' => 1])->first();
        if (!$design_project) {
            throw new MassageException('不存在', 404);
        }

        $design_stage = $design_project->designStage;
        if (!$design_stage) {
            return $this->response->array($this->apiSuccess('Success.', 200, []));
        }

        $data = [];
        foreach ($design_stage as $stage) {
            $stage_data = [];
            $stage_data['id'] = $stage->id;
            $stage_data['name'] = $stage->name;
            $stage_data['content'] = $stage->content;

            $design_substage = $stage->designSubstage;
            if (!$design_substage) {
                continue;
            }

            $asset_data = [];
            foreach ($design_substage as $subStage) {
                $design_stage_node = $subStage->designStageNode;
                if (!$design_stage_node) {
                    continue;
                }
                $asset_data = array_merge($asset_data, AssetModel::getImageUrl($design_stage_node->id, 31));
            }

            $stage_data['assets'] = $asset_data;
            $data[] = $stage_data;
        }

        return $this->response->array($this->apiSuccess('Success', 200, $data));
    }

}
