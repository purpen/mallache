<?php
/**
 * 项目需求控制器
 *
 * @User llh
 * @time 2017-4-6
 */

namespace App\Http\Controllers\Api\V1;

use App\Events\ItemStatusEvent;
use App\Helper\ItemCommissionAction;
use App\Helper\Recommend;
use App\Helper\Tools;
use App\Http\Transformer\EvaluateTransformer;
use App\Http\Transformer\ItemDesignListTransformer;
use App\Http\Transformer\ItemListTransformer;
use App\Http\Transformer\ItemTransformer;
use App\Http\Transformer\RecommendDesignCompanyTransformer;
use App\Http\Transformer\RecommendListTransformer;
use App\Models\AssetModel;
use App\Models\DemandCompany;
use App\Models\DesignCompanyModel;
use App\Models\DesignItemModel;
use App\Models\Evaluate;
use App\Models\Item;
use App\Models\ItemRecommend;
use App\Models\PayOrder;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DemandController extends BaseController
{
    /**
     * @api {get} /demand/{id} 需求公司获取项目详细信息
     * @apiVersion 1.0.0
     * @apiName demand show
     * @apiGroup demandType
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     * "data": {
     * "item": {
     * "id": 13,
     * "type": 1,
     * "type_value": "产品设计类型",
     * "design_type": 2, (停用)
     * "design_type_value": "产品设计", （停用）
     * "design_types": [
     *      2
     *  ],
     * "design_types_value": [
     *      "网页设计"
     *  ],
     * "status": 5,  //-2.无设计接单关闭；-1.用户关闭；1.填写资料；2.人工干预；3.推送设计公司；4.等待设计公司接单(报价)；5.等待设计公司提交合同（提交合同）；6.确认合同（已提交合同）；7.已确定合同；8.托管项目资金；11.项目进行中；15.项目已完成；18.已项目验收。；22.已评价
     * "field": 2,
     * "field_value": "消费电子",
     * "industry": 2,
     * "industry_value": "消费零售",
     * "name": "api UI",
     * "product_features": "亮点",
     * "competing_product": "竞品",
     * "cycle": 1,
     * "cycle_value": "1个月内",
     * "design_cost": 2,
     * "design_cost_value": "1-5万之间",
     * "city": 2,
     * "image": [],
     * "price": 200000,
     * "company_name": null,  //公司名称
     * "company_abbreviation": null, //简称
     * "company_size": null, //公司规模；1...
     * "company_web": null,  //公司网址
     * "company_province": null, //省份
     * "company_city": null,  //城市
     * "company_area": null,   //区县
     * "address": null,    //详细地址
     * "contact_name": null,   //联系人
     * "phone": "172734923",
     * "email": "qq@qq.com",
     *                  "stage_status":0 //资料填写阶段；1.项目类型；2.需求信息；3.公司信息
     * },
     * "quotation": null, //报价单信息
     * "contract": null   //合同
     * "evaluate": null  //评价
     * },
     * "meta": {
     * "message": "Success",
     * "status_code": 200
     * }
     * }
     */
    public function show($id)
    {
        if (!$item = Item::find(intval($id))) {
            return $this->response->array($this->apiSuccess());
        }
        //验证是否是当前用户对应的项目
        if ($item->user_id !== $this->auth_user_id) {
            return $this->response->array($this->apiError('not found!', 404));
        }

        if (!$item) {
            return $this->response->array($this->apiError());
        }
        return $this->response->item($item, new ItemTransformer)->setMeta($this->apiMeta());
    }


    /**
     * @api {post} /demand/create 需求公司创建需求项目
     * @apiVersion 1.0.0
     * @apiName demand create
     * @apiGroup demandType
     *
     * @apiParam {string} token
     * @apiParam {string} name 项目名称
     *
     * @apiSuccessExample 成功响应:
     * {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function create(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:100',
        ];
        $all = $request->all();
        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }


        if ($this->auth_user->type != 1) {
            return $this->response->array($this->apiError('error: not demand', 403));
        }
        $source = $request->header('source-type') ?? 0;
        $name = $request->input('name');
        $item = Item::createItem($this->auth_user_id, $name, $source);
        if (!$item) {
            return $this->response->array($this->apiError());
        }

        return $this->response->item($item, new ItemTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /demand/{id} 更改项目类型
     * @apiVersion 1.0.0
     * @apiName demand update
     * @apiGroup demandType
     *
     * @apiParam {string} token
     * @apiParam {string} type 设计类型：1.产品设计；2.UI UX 设计
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "data": {
     *      "item": {
     *      "id": 13,
     *      "type": 1,
     *      "type_value": "产品设计类型",
     *      "design_types": [
     *           2
     *       ],
     *      "design_types_value": [
     *           "网页设计"
     *       ],
     *      "status": 5,
     *      "field": 2,
     *      "field_value": "消费电子",
     *      "industry": 2,
     *      "industry_value": "消费零售",
     *      "name": "api UI",
     *      "product_features": "亮点",
     *      "competing_product": "竞品",
     *      "cycle": 1,
     *      "cycle_value": "1个月内",
     *      "design_cost": 2,
     *      "design_cost_value": "1-5万之间",
     *      "city": 2,
     *      "image": [],
     *      "price": 200000,
     *      "company_name": null,  //公司名称
     *      "company_abbreviation": null, //简称
     *      "company_size": null, //公司规模；1...
     *      "company_web": null,  //公司网址
     *      "company_province": null, //省份
     *      "company_city": null,  //城市
     *      "company_area": null,   //区县
     *      "address": null,    //详细地址
     *      "contact_name": null,   //联系人
     *      "phone": "172734923",
     *      "email": "qq@qq.com"
     *      "stage_status":0 //资料填写阶段；1.项目类型；2.需求信息；3.公司信息
     *      "source": 0 //"source": 0, // 来源字段 0.默认 1.京东众创
     * },
     * "quotation": null, //报价单信息
     * "contract": null   //合同
     * },
     * "meta": {
     * "message": "Success",
     * "status_code": 200
     * }
     * }
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'type' => 'required|integer',
        ];

        $all = $request->only(['type']);

        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        try {

            if (!$item = Item::find(intval($id))) {
                return $this->response->array($this->apiError('not found!', 404));
            }
            //验证是否是当前用户对应的项目
            if ($item->user_id !== $this->auth_user_id || 1 != $item->status) {
                return $this->response->array($this->apiError('not found!', 404));
            }

            // 需求公司信息是否认证
            $demand_company = $this->auth_user->demandCompany;
            if ($demand_company->verify_status == 1) {
                $all['company_name'] = $demand_company->company_name;
                $all['company_abbreviation'] = $demand_company->company_abbreviation;
                $all['company_size'] = $demand_company->company_size;
                $all['company_web'] = $demand_company->company_web;
                $all['company_province'] = $demand_company->province;
                $all['company_city'] = $demand_company->city;
                $all['company_area'] = $demand_company->area;
                $all['address'] = $demand_company->address;
                $all['contact_name'] = $demand_company->contact_name;
                $all['phone'] = $demand_company->phone;
                $all['email'] = $demand_company->email;
                $all['position'] = $demand_company->position;
            }
            $all['stage_status'] = 2;

            $item->update($all);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->response->array($this->apiError('Error', 500));
        }

        return $this->response->item($item, new ItemTransformer)->setMeta($this->apiMeta());
    }


    /**
     * @api {delete} /demand/{id} 删除项目
     * @apiVersion 1.0.0
     * @apiName demand delete
     * @apiGroup demandType
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function destroy($id)
    {
        $item = $this->checkItemStatusAndAuth($id);

        if ($item->status != -2) {
            return $this->response->array($this->apiError('当前项目状态不能删除！', 403));
        }

        $item->delete();

        return $this->response->array($this->apiSuccess('Success', 200));
    }

    /**
     * @api {post} /demand/release 发布项目
     * @apiVersion 1.0.0
     * @apiName demand release
     * @apiGroup demandType
     *
     * @apiParam {string} token
     * @apiParam {integer} cycle 设计周期：1.1个月内；2.1-2个月；3.2-3个月；4.3-4个月；5.4个月以上
     * @apiParam {integer} design_cost 设计费用：1、1-5万；2、5-10万；3.10-20；4、20-30；5、30-50；6、50以上
     * @apiParam {integer} industry 所属行业 1.制造业,2 .消费零售,3 .信息技术,4 .能源,5 .金融地产,6 .服务业,7 .医疗保健,8 .原材料,9 .工业制品,10 .军工,11 .公用事业
     * @apiParam {integer} item_province 省份
     * @apiParam {integer} item_city 城市
     * @apiParam {string} random 随机数
     * @apiParam {integer} id 项目ID
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *       "data": [
     *           0 //没有审核或者没有找到该需求公司
     *       ]
     *  }
     */
    public function release(Request $request)
    {
        $id = (int)$request->input('id');
        if (!$item = Item::find($id)) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if ($item->status != 1) {
            return $this->response->array($this->apiError('项目已发布', 403));
        }

        //验证是否是当前用户对应的项目
        if ($item->user_id !== $this->auth_user_id) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        $auth_user = $this->auth_user;
        if (!$auth_user) {
            return $this->response->array($this->apiError('not found!', 404));
        }

        $demand_company = $this->auth_user->demandCompany;
        if (!$demand_company) {
            return $this->response->array($this->apiError('not found demandCompany!', 404));
        }
        $item->cycle = $request->input('cycle') ?? 0;
        $item->design_cost = $request->input('design_cost') ?? 0;
        $item->industry = $request->input('industry') ?? 0;
        $item->item_province = $request->input('item_province') ?? 0;
        $item->item_city = $request->input('item_city') ?? 0;
        if($item->save()){
            if ($random = $request->input('random')) {
                AssetModel::setRandom($item->id, $random);
            }
        }

        // 同步调用匹配方法
        $recommend = new Recommend($item);
        $recommend->handle();

        $demand_company = DemandCompany::find($auth_user->demand_company_id);
        if (!$demand_company || $demand_company->verify_status != 1) {
            $verify_status = 0;
        } else {
            $verify_status = 1;
        }

        return $this->response->array($this->apiSuccess('Success', 200, ['verify_status' => $verify_status]));
    }

    /**
     * @api {get} /demand/recommendList/{item_id} 需求公司当前项目获取推荐的设计公司
     * @apiVersion 1.0.0
     * @apiName demand recommendList
     * @apiGroup demandType
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     * "data": [
     * {
     * {
     * "id": 1,
     * "user_id": 1, //用户表ID (唯一索引)
     * "company_type": 1, //企业类型：1.普通；2.多证合一；
     * "company_name": "测试设计公司", //公司名称
     * "registration_number": "12344556", //注册号
     * "province": 1,
     * "city": 2,
     * "area": 3,
     * "address": "北京朝阳",
     * "contact_name": "小王", //联系人姓名
     * "position": "老总", //职位
     * "phone": "18629493220",
     * "email": "qq@qq.com",
     * "company_size": 4, //公司规模：1.10以下；2.10-50；3.50-100；4.100以上;
     * "branch_office": 1, //分公司：1.有；2.无；
     * "item_quantity": 2, //曾服务项目：1.10以下；2.10-50；3.50-100；4.100-200;5.200以上
     * "company_profile": "一家有价值的公司",
     * "good_field": "1,2,3", //good_field
     * "web": "www.tai.com", //公司网站
     * "establishment_time": "2013-12-10",
     * "professional_advantage": "专业", //专业优势
     * "awards": "就是专业", //荣誉奖项
     * "created_at": "2017-04-11 14:54:24",
     * "updated_at": "2017-04-11 14:59:36",
     * "deleted_at": null,
     * "score": 70,
     * "status": 0, //设计公司状态：-1.禁用; 0.正常；
     * "company_abbreviation": "", //简称
     * "is_recommend": 0, //推荐
     * "verify_status": 1 //审核状态
     *              "logo": [],
     * "license_image": [],
     * "unique_id": ""
     * }
     * },
     * "meta": {
     * "message": "Success",
     * "status_code": 200
     * }
     * }
     */
    public function recommendList($item_id)
    {
        if (!$item = Item::find($item_id)) {
            return $this->response->array($this->apiError('not found', 404));
        }

        //验证是否是当前用户对应的项目
        if ($item->user_id !== $this->auth_user_id || $item->status !== 3) {
            return $this->response->array($this->apiError('拒绝操作', 403));
        }

        $recommend_arr = explode(',', $item->recommend);

        //如果推荐为空，则返回
        if (empty($recommend_arr)) {
            return $this->response->array($this->apiSuccess('Success', 200, []));
        }

        $design_company = DesignCompanyModel::whereIn('id', $recommend_arr)->get();

        return $this->response->collection($design_company, new RecommendListTransformer($item))->setMeta($this->apiMeta());
    }

    /**
     * @api {post} /demand/push 选定设计公司推送项目需求
     * @apiVersion 1.0.0
     * @apiName demand pushDemand
     * @apiGroup demandType
     *
     * @apiParam {string} token
     * @apiParam {integer} item_id 项目ID
     * @apiParam {array} design_company_id 设计公司ID
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function push(Request $request)
    {
        $rules = [
            'item_id' => 'required|integer',
            'design_company_id' => 'required|array',
        ];

        $all = $request->only(['item_id', 'design_company_id']);

        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }


        try {
            DB::beginTransaction();

            $item = Item::find($all['item_id']);
            if (!empty(array_diff($all['design_company_id'], explode(',', $item->recommend)))) {
                return $this->response->array($this->apiError('选择设计公司不符合要求', 403));
            };
            if ($item->user_id != $this->auth_user_id || $item->status != 3) {
                return $this->response->array($this->apiError('无操作权限或当前状态不可操作', 403));
            }
            //修改项目状态为：等待设计公司接单(报价)
            $item->status = 4;
            $item->save();
            //触发事件
            event(new ItemStatusEvent($item, $all['design_company_id']));

            //遍历插入推荐表
            foreach ($all['design_company_id'] as $design_company_id) {
                ItemRecommend::create(['item_id' => $all['item_id'], 'design_company_id' => $design_company_id]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return $this->response->array($this->apiError('Error', 500));
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {get} /demand/itemList 用户项目信息列表
     * @apiVersion 1.0.0
     * @apiName demand itemList
     * @apiGroup demandType
     *
     * @apiParam {string} token
     * @apiParam {integer} type 0:全部；1.填写资料中；2.进行中;3.已完成
     * @apiParam {integer} per_page 分页数量
     * @apiParam {integer} page 页码
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200,
     * "pagination": {
     * "total": 3,
     * "count": 1,
     * "per_page": 1,
     * "current_page": 1,
     * "total_pages": 3,
     * "links": {
     * "next": "http://saas.me/demand/itemList?page=2"
     * }
     *      }
     *      "data": [
     * {
     * "item": {
     * "id": 1,
     * "type": 2, //1.产品设计；2.UI UX 设计；
     * "design_type": 1, //UXUI设计（1.app设计；2.网页设计；）
     * "status": 2, //状态：-2.匹配失败；-1.用户关闭；1.填写资料；2.人工干预；3.推送设计公司；4.等待设计公司接单(报价)；5.等待设计公司提交合同（提交合同）；6.确认合同（已提交合同）；7.已确定合同；8.托管项目资金；11.项目进行中；15.项目已完成；18.已项目验收。20.项目交易成功；22.已评价
     *                  "status_value": "填写资料", //状态说明（需求公司）
     *                  "design_status_value": ""  //状态说明 （设计公司））
     * "system": 1, 系统：1.ios；2.安卓；
     * "design_content": 0, //设计内容：1.视觉设计；2.交互设计；
     * "name": "", //项目名称
     * "stage": 0, //阶段：1、已有app／网站，需重新设计；2、没有app／网站，需要全新设计；
     * "complete_content": 0, //已完成设计内容：1.流程图；2.线框图；3.页面内容；4.产品功能需求点；5.其他
     * "other_content": "",  //其他设计内容
     * "design_cost": 0, //设计费用：1、1万以下；2、1-5万；3、5-10万；4.10-20；5、20-30；6、30-50；7、50以上
     * "province": 0,
     * "city": 0,
     * "image": []
     * },
     * "purpose_count": 0 //意向接单数量
     * }
     * {
     * "item": {
     * "id": 13,
     * "type": 1, //1.产品设计；2.UI UX 设计；
     * "design_type": 2, //产品设计（1.产品策略；2.产品设计；3.结构设计；）
     * "status": 2,
     * "field": 1, //所属领域ID
     * "industry": 2, //所属行业
     * "name": "api UI",
     * "product_features": "亮点", //产品功能或亮点
     * "competing_product": "竞品", //竞品
     * "cycle": 1, //设计周期：1.1个月内；2.1-2个月；3.2个月；4.2-4个月；5.其他
     * "design_cost": 2, //设计费用：1、1万以下；2、1-5万；3、5-10万；4.10-20；5、20-30；6、30-50；7、50以上
     * "province": 2,
     * "city": 2,
     * "image": []
     * },
     * "purpose_count": 0
     * }
     *      ],
     *  }
     */
    public function itemList(Request $request)
    {
        $rules = [
            'type' => 'nullable|integer',
            'per_page' => 'nullable|integer',
        ];

        $all = $request->only(['type', 'per_page']);

        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $per_page = $request->input('per_page') ?? $this->per_page;
        $type = $request->input('type') ?? 0;

        switch ($type) {
            case 0:
                $where_in = [];
                break;
            case 1:
                $where_in = [1];
                break;
            case 2:
                $where_in = [-2, -1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 15, 18, 22];
                break;
            case 3:
                $where_in = [18, 22];
                break;
            default:
                $where_in = [];
        }

        $items = Item::where('user_id', $this->auth_user_id);

        if (!empty($where_in)) {
            $items = $items->whereIn('status', $where_in);
        }

        $items = $items->orderBy('id', 'desc')->paginate($per_page);
        if ($items->isEmpty()) {
            return $this->response->array($this->apiSuccess());
        }

        return $this->response->paginator($items, new ItemListTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /demand/itemDesignList/{item_id} 项目推送设计公司状态列表
     * @apiVersion 1.0.0
     * @apiName demand itemDesignList
     * @apiGroup demandType
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *      "data": [
     *      {
     *          "status": 5, //1.已选择其他设计公司;2.设计公司已拒绝;3.未操作；4.设计公司有意接单；5.选定该设计公司；
     *          "status_value": "选定设计公司",
     *          "item_status": 1, //需求方项目状态：-1.拒绝；0.待操作；1.选定设计公司；
     *          "item_status_value": "选定设计公司",
     *          "design_company_status": 0, //设计公司状态: -1.拒绝；0.待操作；1.一键成交；2.有意向报价;
     *          "design_company_status_value": "待操作",
     *          "design_company": {
     *              "id": 1,
     *              "user_id": 1, //用户表ID
     *              "company_type": 1, //企业类型：1.普通；2.多证合一；
     *              "company_name": "测试设计公司", //公司名称
     *              "registration_number": "12344556", //注册号
     *              "province": 1,
     *              "city": 2,
     *              "area": 3,
     *              "address": "北京朝阳",
     *              "contact_name": "小王", //联系人姓名
     *              "position": "老总", //职位
     *              "phone": "18629493220",
     *              "email": "qq@qq.com",
     *              "company_size": 4, //公司规模：1.10以下；2.10-50；3.50-100；4.100以上;
     *              "branch_office": 1, //分公司：1.有；2.无；
     *              "item_quantity": 2, //曾服务项目：1.10以下；2.10-50；3.50-100；4.100-200;5.200以上
     *              "company_profile": "一家有价值的公司",
     *              "good_field": "1,2,3", //good_field
     *              "web": "www.tai.com", //公司网站
     *              "establishment_time": "2013-12-10",
     *              "professional_advantage": "专业", //专业优势
     *              "awards": "就是专业", //荣誉奖项
     *              "created_at": "2017-04-11 14:54:24",
     *              "updated_at": "2017-04-11 14:59:36",
     *              "deleted_at": null,
     *              "score": 70,
     *              "status": 0, //设计公司状态：-1.禁用; 0.正常；
     *              "company_abbreviation": "", //简称
     *              "is_recommend": 0, //推荐
     *              "verify_status": 1 //审核状态
     *          },
     *          "quotation": null  //设计公司报价单
     *          },
     *          ],
     *  }
     */
    public function itemDesignList($item_id)
    {
        if (!$item = Item::find($item_id)) {
            return $this->response->array($this->apiSuccess());
        }

        if ($item->user_id !== $this->auth_user_id) {
            return $this->response->array($this->apiError('not found!', 404));
        }

        $item_recommends = $item->itemRecommend;
        if ($item_recommends->isEmpty()) {
            return $this->response->array($this->apiSuccess());
        }

        return $this->response->collection($item_recommends, new ItemDesignListTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {post} /demand/trueDesign 确定合作的设计公司
     * @apiVersion 1.0.0
     * @apiName demand 确定合作的设计公司
     * @apiGroup demandType
     *
     * @apiParam {string} token
     * @apiParam {integer} item_id 项目ID
     * @apiParam {integer} design_company_id 设计公司ID
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function trueDesign(Request $request)
    {
        $rules = [
            'item_id' => 'required|integer',
            'design_company_id' => 'required|integer',
        ];
        $all = $request->only(['item_id', 'design_company_id']);

        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        if (!$item = Item::find($all['item_id'])) {
            return $this->response->array($this->apiError('not found', 404));
        }
        if ($item->user_id !== $this->auth_user_id || $item->status !== 4) {
            return $this->response->array($this->apiError('not found', 404));
        }

        try {
            DB::beginTransaction();
            $item_recommend = ItemRecommend::where(['item_id' => $all['item_id'], 'design_company_id' => $all['design_company_id']])->first();
            if (!$item_recommend) {
                DB::rollBack();
                return $this->response->array($this->apiError('not found', 404));
            }

            //修改推荐关联表中需求方的状态
            $item_recommend->item_status = 1;
            $item_recommend->save();

            //拒绝其他设计公司
            $item_recommend_qt = ItemRecommend::where('item_id', '=', $all['item_id'])
                ->where('design_company_id', '!=', $all['design_company_id'])
                ->where('design_company_status', '!=', -1)
                ->get();
            if (!$item_recommend_qt->isEmpty()) {
                foreach ($item_recommend_qt as $qt) {
                    $qt->item_status = -1;
                    $qt->save();
                }
            }

            //将设计公司ID、项目金额写入需求item中，修改item状态为已选定设计公司；
            if (!$quotation = $item_recommend->quotation) {
                DB::rollBack();
                return $this->response->array($this->apiError());
            }
            //修改报价单状态为已确认
            $quotation->status = 1;
            $quotation->save();

            // 获取设计公司佣金比例
            $result_data = ItemCommissionAction::getCommissionRate($all['design_company_id']);
            $item->commission_rate = $result_data['rate'];
            $item->preferential_type = $result_data['type'];


            $item->design_company_id = $all['design_company_id'];
            $item->price = $quotation->price;
            $item->quotation_id = $quotation->id;
            $item->status = 5;
            $item->save();

            // 将项目需求ID写入合作的设计公司的项目管理中
            if ($design_project = $quotation->designProject) {
                $design_project->item_demand_id = $item->id;
                $design_project->save();
            }

            //触发项目状态事件
            $design_company_id = $item_recommend_qt->pluck('design_company_id')->all();
            event(new ItemStatusEvent($item, ['yes' => $all['design_company_id'], 'no' => $design_company_id]));

            DB::commit();
            return $this->response->array($this->apiSuccess());

        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            return $this->response->array($this->apiError('Error', 500));
        }
    }

    /**
     * @api {post} /demand/falseDesign 拒绝设计公司报价
     * @apiVersion 1.0.0
     * @apiName demand 拒绝设计公司报价
     * @apiGroup demandType
     *
     * @apiParam {string} token
     * @apiParam {integer} item_id 项目ID
     * @apiParam {integer} design_company_id 设计公司ID
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function falseDesign(Request $request)
    {
        $rules = [
            'item_id' => 'required|integer',
            'design_company_id' => 'required|integer',
        ];
        $all = $request->only(['item_id', 'design_company_id']);

        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        if (!$item = Item::find($all['item_id'])) {
            return $this->response->array($this->apiError('not found', 404));
        }
        if ($item->user_id != $this->auth_user_id || $item->status != 4) {
            return $this->response->array($this->apiError('无权限', 403));
        }

        $item_recommend = ItemRecommend::where(['item_id' => $all['item_id'], 'design_company_id' => $all['design_company_id']])->first();
        if (!$item_recommend) {
            return $this->response->array($this->apiError('not found', 404));
        }

        //修改推荐关联表中需求方的状态
        $item_recommend->item_status = -1;
        if (!$item_recommend->save()) {
            return $this->response->array($this->apiError('状态修改失败', 500));
        }

        //消息通知
        $design = DesignCompanyModel::find($all['design_company_id']);
        $tools = new Tools();

        $title = '项目报价被拒';
        $content = '【' . ($item->itemInfo())['name'] . '】' . '项目需求方已选择其他设计公司';;
        $tools->message($design->user_id, $title, $content, 1, null);

        //项目是否匹配失败
        $item->itemIsFail($all['item_id']);

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {post} /demand/trueContract 确定合作合同
     * @apiVersion 1.0.0
     * @apiName demand 确定合作合同
     * @apiGroup demandType
     *
     * @apiParam {string} token
     * @apiParam {integer} item_id 项目ID
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function trueContract(Request $request)
    {
        $item_id = (int)$request->input('item_id');
        if (!$item = Item::find($item_id)) {
            return $this->response->array($this->apiError());
        }
        if ($item->user_id !== $this->auth_user_id || $item->status !== 6) {
            return $this->response->array($this->apiError());
        }
        try {
            DB::beginTransaction();
            $item->status = 7;
            $item->save();

            //修改合同状态为已确认
            $contract = $item->contract;
            $contract->status = 1;
            $contract->true_time = time();

            $contract->save();

            //触发项目状态变更事件
            event(new ItemStatusEvent($item));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->response->array($this->apiError('Error', 500));
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {post} /demand/closeItem 用户关闭需求项目 -2.匹配失败；1.填写资料；2.人工干预；3.匹配设计公司；4.等待设计公司接单(报价)；
     * @apiVersion 1.0.0
     * @apiName demand closeItem
     * @apiGroup demandType
     *
     * @apiParam {string} token
     * @apiParam {integer} item_id 项目ID
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function closeItem(Request $request)
    {
        $item_id = (int)$request->input('item_id');

        if (!$item = Item::find($item_id)) {
            return $this->response->array($this->apiError('not found item', 404));
        }

        if ($item->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('Permission denied', 403));
        }

        if (!in_array($item->status, [-2, 1, 2, 3, 4])) {
            return $this->response->array($this->apiError('当前状态不能修改', 403));
        }

        //修改为用户已关闭
        $item->status = -1;
        if (!$item->save()) {
            return $this->response->array($this->apiError('Error', 500));
        }

        // 触发项目变更状态
        event(new ItemStatusEvent($item));

        //解冻保证金 (发布项目保证金已经取消，为保证历史数据正常处理 这段代码保留)
        $pay_order = PayOrder::query()->where([
            'user_id' => $this->auth_user_id,
            'item_id' => $item_id,
            'type' => 1,
            'status' => 1,
        ])->first();
        if ($pay_order) {
            //支付单改为退款
            $pay_order->status = 2;  //退款
            $pay_order->save();

            $user = $this->auth_user;
            $user->price_frozen = bcsub($user->price_frozen, $pay_order->amount, 2);
            if ($user->price_frozen < 0) {
                $user->price_frozen = 0;
            }
            $user->save();
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * 验证项目是否属于当前登陆用户
     * @param $item_id
     * @return Item
     */
    protected function checkItemStatusAndAuth($item_id)
    {
        if (!$item = Item::find($item_id)) {
            return $this->response->array($this->apiError('not found item', 404));
        }

        if ($item->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('Permission denied', 403));
        }

        return $item;
    }

    /**
     * @api {post} /demand/itemRestart 修改项目重新匹配
     * @apiVersion 1.0.0
     * @apiName demand itemRestart
     * @apiGroup demandType
     *
     * @apiParam {string} token
     * @apiParam {integer} item_id 项目ID
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function itemRestart(Request $request)
    {
        $item_id = (int)$request->input('item_id');

        if (!$item = Item::find($item_id)) {
            return $this->response->array($this->apiError('not found item', 404));
        }

        if ($item->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('Permission denied', 403));
        }

        if ($item->status != -2) {
            return $this->response->array($this->apiError('当前状态不能修改', 403));
        }

        //清除推荐公司Id，追加至曾推荐公司ID字段
        $item->ord_recommend = $item->ord_recommend ? ($item->ord_recommend . ',' . $item->recommend) : $item->recommend;
        $item->recommend = '';
        $item->status = 1; //填写资料阶段
        if (!$item->save()) {
            return $this->response->array($this->apiError('Error', 500));
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {post} /demand/trueItemDone/{item_id} 需求公司验收项目已完成
     * @apiVersion 1.0.0
     * @apiName demand trueItemDone
     * @apiGroup demandType
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function trueItemDone($item_id)
    {
        if (!$item = Item::find($item_id)) {
            return $this->response->array($this->apiError('not found item', 404));
        }

        if ($item->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('Permission denied', 403));
        }

        if ($item->status != 15) {
            return $this->response->array($this->apiError('当前状态不能修改', 403));
        }

        $item->status = 18;
        $item->save();

        // 项目状态变更事件
        event(new ItemStatusEvent($item));

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {post} /demand/matchingCount/ 获取当前信息匹配到的公司数量
     * @apiVersion 1.0.0
     * @apiName demand matchingCount
     * @apiGroup demandType
     *
     * @apiParam {string} token
     * @apiParam {integer} type 设计类型：1.产品设计；2.UI UX 设计；
     * @apiParam {integer} design_type 设计类别：产品设计（1.产品策略；2.产品外观设计；3.结构设计；）UXUI设计（1.app设计；2.网页设计；）(停用)
     * @apiParam {json} design_types 设计类别：产品设计（1.产品策略；2.产品外观设计；3.结构设计；）UXUI设计（1.app设计；2.网页设计；）[1,2]
     * @apiParam {integer} cycle 设计周期：1.1个月内；2.1-2个月；3.2-3个月；4.3-4个月；5.4个月以上
     * @apiParam {integer} design_cost 设计费用：1、1-5万；2、5-10万；3.10-20；4、20-30；5、30-50；6、50以上
     * @apiParam {integer} province 省份ID
     * @apiParam {integer} city 城市ID
     * @apiParam {integer} item_id 项目ID
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }，
     *      "date": {
     *          "count": 2,
     *      }
     *  }
     */
    public function matchingCount(Request $request)
    {
        $type = $request->input('type') ?? null;
        $design_types = $request->input('design_types') ?? null;
        $design_cost = $request->input('design_cost') ?? null;
//        $cycle = $request->input('cycle') ?? null;
//        $province = $request->input('province') ?? null;
//        $city = $request->input('city') ?? null;
        $item_id = $request->input('item_id') ?? null;

        $query = DesignItemModel::select('user_id');
        if ($type) {
            $query->where('type', $type);
        }

        if ($design_types) {
            $design_types = json_decode($design_types, true);
            $query->whereIn('design_type', $design_types);
        }

        if ($design_cost) {
            $max = $this->cost($design_cost);
            $query->where('min_price', '<=', $max);
        }

//        if ($cycle) {
//            $query->where('project_cycle', $cycle);
//        }

        //设计公司用户ID
        $design_id_arr = $query->get()->pluck('user_id')->all();

        $design = DesignCompanyModel::select(['id', 'user_id'])
            ->where(['status' => 1, 'verify_status' => 1, 'is_test_data' => 0]);

//        if ($province && $province != -1) {
//            $design->where('province', $province);
//        }
//
//        if ($city && $city != -1) {
//            $design->where('city', $city);
//        }

        if ($item_id) {
            if ($item = Item::find($item_id)) {
                if (!empty($item->ord_recommend)) {
                    //已推荐的设计公司ID 数组
                    $old_design_arr = explode(',', $item->ord_recommend);
                    $design->whereNotIn('id', $old_design_arr);

                }
            }
        }

        $count = $design->whereIn('user_id', $design_id_arr)->count();


        return $this->response->array($this->apiSuccess('Success', 200, compact('count')));
    }

    /**
     * 设计费用转换
     *
     * @param $design_cost
     * @return int
     */
    protected function cost($design_cost)
    {
        //设计费用：1、1-5万；2、5-10万；3.10-20；4、20-30；5、30-50；6、50以上
        $max = 10000;
        switch ($design_cost) {
            case 1:
                $max = 50000;
                break;
            case 2:
                $max = 100000;
                break;
            case 3:
                $max = 200000;
                break;
            case 4:
                $max = 300000;
                break;
            case 5:
                $max = 500000;
                break;
            case 6:
                $max = 500000;
                break;
        }

        return $max;
    }

    /**
     * @api {post} /demand/evaluate 需求公司评价
     * @apiVersion 1.0.0
     * @apiName demand evaluate
     * @apiGroup demandType
     *
     * @apiParam {string} token
     * @apiParam {integer} item_id
     * @apiParam {string} content 内容
     * @apiParam {integer} score 评分
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *      "data": {
     *
     *       }
     *  }
     */
    public function evaluate(Request $request)
    {
        /*
        字段	类型	空	默认值	注释
        id	int(10)	否
        demand_company_id	int(10)	否		需求公司ID
        design_company_id	int(10)	否		设计公司ID
        item_id	int(10)	否		项目ID
        content	varchar(500)	否		内容
        score	tinyint(4)	否		评分:
        status	tinyint(4)	否	0	状态*/

        $rules = [
            'item_id' => 'required|integer',
            'content' => 'required|max:500',
            'score' => 'required|int',
        ];
        $all = $request->only(['item_id', 'content', 'score']);

        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        if (!$item = Item::find($all['item_id'])) {
            return $this->response->array($this->apiError('not found item', 404));
        }

        if ($item->user_id != $this->auth_user_id || $item->status != 18) {
            return $this->response->array($this->apiError('Permission denied', 403));
        }

        $all['demand_company_id'] = $this->auth_user->demand_company_id;
        $all['design_company_id'] = $item->design_company_id;

        try {
            DB::beginTransaction();
            $evaluate = Evaluate::create($all);

            $item->status = 22;
            $item->save();

            event(new ItemStatusEvent($item));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->response->array($this->apiError('database error', 500));
        }

        return $this->response->item($evaluate, new EvaluateTransformer)->setMeta($this->apiMeta());
    }

}
