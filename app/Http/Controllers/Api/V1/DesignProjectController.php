<?php

namespace App\Http\Controllers\Api\V1;

use App\Helper\MassageException;
use App\Http\Middleware\OperationLogs;
use App\Http\Transformer\DesignProjectStatisticalTransformer;
use App\Http\Transformer\DesignProjectTransformer;
use App\Models\AssetModel;
use App\Models\CollectItem;
use App\Models\Contract;
use App\Models\DesignCompanyModel;
use App\Models\DesignProject;
use App\Models\DesignStage;
use App\Models\ItemUser;
use App\Models\OperationLog;
use App\Models\PanDirector;
use App\Models\ProductDesign;
use App\Models\Task;
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
     * @apiParam {int} user_status 创建状态：0.默认 1.创建的项目
     * @apiParam {int} collect 收藏状态：0.默认 1.收藏
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
        $collect = $request->collect ?? 0;
        $user_status = $request->user_status ?? 0;

        // 获取所在的所有项目ID
        $arr = ItemUser::projectId($this->auth_user_id);

        //获取收藏的所有项目id
        $collectId = CollectItem::collectId($this->auth_user_id , $status , $collect);

        if ($collect == 1){
            $lists = DesignProject::where('status', $status)
                ->whereIn('id', $arr)
                ->whereIn('id', $collectId)
                ->paginate($per_page);
        } else {
            if ($user_status == 1){
                $lists = DesignProject::where('user_id' , $this->auth_user_id)
                    ->where('status', 1)
                    ->paginate($per_page);
            } else {
                $lists = DesignProject::where('status', $status)
                    ->whereIn('id', $arr)
                    ->paginate($per_page);
            }
        }
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
            //添加判断项目名称是否已经存在
            $item = DesignProject::where('name' , $request->input('name'))
                ->where('design_company_id' , $design_company_id)
                ->where('project_type' , 2)
                ->where('status' , 1)
                ->first();
            if($item){
                throw new MassageException('该项目名称已经存在，请换个名称', 412);
            }
            $design_project->save();

            // 将创建者添加入项目人员
            if (!ItemUser::addItemUser($design_project->id, $user_id, 1)) {
                throw new MassageException('项目成员添加失败', 403);
            }

            $design_company = DesignCompanyModel::find($design_company_id);
            // 自动创建项目云盘目录
            PanDirector::createProjectDir($design_company, $design_project);

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

    /**
     * @api {get} /designProject/contracts 项目中的合同列表
     * @apiVersion 1.0.0
     * @apiName designProject contracts
     * @apiGroup designProject
     *
     * @apiParam {int} item_id 项目id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *      "data": {
     *          "info": {   // 线上合同信息
     *              "id": 45,
     *              "item_demand_id": 120,
     *              "design_company_id": 20,
     *              "demand_company_name": "不一样的烟火",
     *              "demand_company_address": "北京 验货区",
     *              "demand_company_phone": "387822234",
     *              "demand_company_legal_person": "大傻",
     *              "design_company_name": "北京一劳永逸科技有限公司",
     *              "design_company_address": "电通创业园111222",
     *              "design_company_phone": "194585839312",
     *              "design_company_legal_person": "太火鸟",
     *              "total": "50000.00",
     *              "status": 1,
     *              "unique_id": "ht5af2cba0601b2",    // 合同unique_id
     *              "item_name": null,
     *              "title": "我是专门测试报价单接口修改的",  // 合同名称
     *              "warranty_money": "5000.00",
     *              "first_payment": "20000.00",
     *              "warranty_money_proportion": "0.10",
     *              "first_payment_proportion": "0.40"
     *          },
     *          "assets": []        // 线下合同附件
     *      }
     *   }
     */
    public function contracts(Request $request)
    {
        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);
        try {
            $item_id = $request->input('item_id');
            $design_project = DesignProject::where(['id' => $item_id, 'status' => 1])->first();
            if (!$design_project) {
                throw new MassageException('不存在', 404);
            }
            $contracts = AssetModel::getImageUrl($item_id, 32, 1);
        } catch (MassageException $e) {
            return $this->response->array($this->apiError($e->getMessage(), $e->getCode()));
        }

        $info = null;
        if ($design_project->project_type == 1) {
            $contract = Contract::query()
                ->where([
                    'item_demand_id' => $design_project->item_demand_id,
                    'design_company_id' => $design_company_id
                ])->first();
            if ($contract) {
                $info = $contract->info();
            }
        }
        $data = [
            'info' => $info,
            'assets' => $contracts,
        ];

        return $this->response->array($this->apiSuccess('Success', 200, $data));

    }

    /**
     * @api {get} /designProject/userStatistical 个人项目统计
     * @apiVersion 1.0.0
     * @apiName designProject userStatistical
     * @apiGroup designProject
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     * "data": [
     * {
     * "id": 1,
     * "name": "测试",
     * "start_time": 123456789,
     * "ok_stage_percentage": 25
     * },
     * {
     * "id": 2,
     * "name": "测试2",
     * "start_time": 123456789,
     * "ok_stage_percentage": 33
     * }
     * ],
     * "meta": {
     * "message": "Success",
     * "status_code": 200
     * }
     * }
     */
    public function userStatistical()
    {
        $user_id = $this->auth_user_id;
        //项目成员，查看参与的项目
        $item_users = ItemUser::where('user_id', $user_id)->get();
        if (!empty($item_users)) {
            $item_id_array = [];
            foreach ($item_users as $item_user) {
                $item_id_array[] = $item_user->item_id;
            }
            //查看所有参与的项目
            $designProducts = DesignProject::whereIn('id', $item_id_array)->where('status', 1)->get();
            if (empty($designProducts)) {
                return $this->response->array($this->apiError('没有参加任何任务', 404));
            }
            foreach ($designProducts as $designProduct) {
                //总数量
                $total_count = Task::where('item_id', $designProduct->id)->count();
                //完成的数量
                $ok_stage = Task::where('item_id', $designProduct->id)->where('stage', 2)->count();
                if ($total_count != 0) {
                    //百分比
                    $ok_stage_percentage = round(($ok_stage / $total_count) * 100, 0);
                } else {
                    $ok_stage_percentage = 0;
                }


                $designProduct['ok_stage_percentage'] = $ok_stage_percentage;
            }
            return $this->response->collection($designProducts, new DesignProjectStatisticalTransformer())->setMeta($this->apiMeta());

        } else {
            return $this->response->array($this->apiError('没有参加任何任务', 404));
        }


    }

    /**
     * @api {get} /designProject/dynamic 项目动态
     * @apiVersion 1.0.0
     * @apiName designProject dynamic
     * @apiGroup designProject
     *
     * @apiParam {int} item_id 项目id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
    {
    "meta": {
    "message": "Success",
    "status_code": 200
    },
    "data": [
    {
    "id": 48,
    "user_id": 1,
    "action_type": 2,
    "type": 1,
    "model_id": 1,
    "target_type": 1,
    "target_id": 27,
    "title": "test111 创建了子任务",
    "content": "test",
    "created_at": 1525337806,
    "user_name": "test111"
    },
    {
    "id": 39,
    "user_id": 1,
    "action_type": 1,
    "type": 1,
    "model_id": 1,
    "target_type": 1,
    "target_id": 22,
    "title": "test111 创建了任务",
    "content": "test",
    "created_at": 1525225273,
    "user_name": "test111"
    }
    ]
    }
     */
    public function dynamic(Request $request)
    {
        $item_id = $request->input('item_id');
        $design_project = DesignProject::where(['id' => $item_id, 'status' => 1])->first();
        if (!$design_project) {
            return $this->response->array($this->apiError('该项目不存在', 404));
        }
        $dynamics = OperationLog::where('model_id' , $item_id)->where('type' , 1)->orderBy('id', 'desc')->get();

        $resp_data = [];
        foreach ($dynamics as $dynamic) {
            $resp_data[] = $dynamic->info();
        }
        return $this->response->array($this->apiSuccess('Success', 200, $resp_data));
    }

    /**
     * @api {put} /designProject/collect 设计工具项目收藏
     * @apiVersion 1.0.0
     * @apiName designProject collect
     * @apiGroup designProject
     *
     * @apiParam {int} item_id 项目id
     * @apiParam {int} collect 0.默认 1.收藏
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
    public function collect(Request $request)
    {
        $item_id = $request->input('item_id');
        $collect = $request->input('collect');
        $user_id = $this->auth_user_id;

        if (!$design_company_id = User::designCompanyId($user_id)) {
            return $this->response->array($this->apiError('没有找到设计公司', 404));

        }

        $design_project = DesignProject::where(['id' => $item_id, 'status' => 1])->first();
        if (!$design_project) {
            return $this->response->array($this->apiError('没有找到该项目', 404));
        }

        $collect_item = CollectItem::where('item_id' , $item_id)->where('user_id' , $user_id)->first();
        if($collect_item) {
            $collect_item->collect = $collect;
            if($collect_item->save()){
                return $this->response->array($this->apiSuccess());
            }
        }else{

            $collectItem = new CollectItem();
            $collectItem->item_id = $item_id;
            $collectItem->user_id = $user_id;
            $collectItem->collect = $collect;

            if($collectItem->save()){
                return $this->response->array($this->apiSuccess());
            }
        }

    }

    /**
     * @api {get} /designProject/statistical  项目统计
     * @apiVersion 1.0.0
     * @apiName designProject statistical
     * @apiGroup designProject
     *
     * @apiParam {integer} item_id 项目id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
    {
        "meta": {
            "message": "获取成功",
            "status_code": 200
        },
        "data": {
            "designStageCount": 1,       //阶段数量
            "durations": 2,    //投入时间
            "okDesignStage": 22,    //项目进度
        }
    }
     */
    public function statistical(Request $request)
    {
        $item_id = $request->input('item_id');
        //查看项目是否存在
        $designProduct = DesignProject::find($item_id);
        if (!$designProduct) {
            return $this->response->array($this->apiError('没有找到该项目', 404));
        }
        //项目阶段
        $designStages = DesignStage::where('design_project_id' , $item_id)->get();
        //项目阶段数量
        $designStageCount = $designStages->count();

        //项目完成数量
        $designStageStatus = DesignStage::where('design_project_id' , $item_id)->where('status' , 1)->count();

        //项目进度
        if($designStageCount != 0){
            $okDesignStage = round(($designStageStatus / $designStageCount) * 100, 0);
        }else{
            $okDesignStage = 0;
        }
        //项目投入时间
        $durations = 0;
        foreach ($designStages as $designStage){
            $durations += $designStage->duration;
        }

        $statistical = [];
        //阶段数量
        $statistical['designStageCount'] = $designStageCount;
        //投入时间
        $statistical['durations'] = $durations;
        //项目进度
        $statistical['okDesignStage'] = $okDesignStage;

        return $this->response->array($this->apiSuccess('获取成功' , 200 , $statistical));

    }
}
