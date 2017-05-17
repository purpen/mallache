<?php
/**
 * 项目需求控制器
 *
 * @User llh
 * @time 2017-4-6
 */
namespace App\Http\Controllers\Api\V1;

use App\Events\ItemStatusEvent;
use App\Helper\Tools;
use App\Http\Transformer\DemandCompanyTransformer;
use App\Http\Transformer\DesignCompanyShowTransformer;
use App\Http\Transformer\ItemDesignListTransformer;
use App\Http\Transformer\ItemListTransformer;
use App\Http\Transformer\ItemTransformer;
use App\Http\Transformer\RecommendListTransformer;
use App\Jobs\Recommend;
use App\Models\Contract;
use App\Models\DemandCompany;
use App\Models\DesignCompanyModel;
use App\Models\Item;
use App\Models\ItemRecommend;
use App\Models\PayOrder;
use App\Models\ProductDesign;
use App\Models\UDesign;
use App\Models\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DemandController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
     * @api {post} /demand 添加项目类型、领域
     * @apiVersion 1.0.0
     * @apiName demand store
     * @apiGroup demandType
     *
     * @apiParam {string} token
     * @apiParam {string} type 设计类型：1.产品设计；2.UI UX 设计
     * @apiParam {integer} design_type 产品设计（1.产品策略；2.产品设计；3.结构设计；）UXUI设计（1.app设计；2.网页设计；）
     * @apiParam {integer} field 所属领域
     * @apiParam {integer} industry 行业
     *
     * @apiParam {string} company_name 公司名称
     * @apiParam {string} company_abbreviation 公司简称
     * @apiParam {integer} company_size 公司规模：1.10以下；2.10-50；3.50-100；4.100以上;5.初创公司；
     * @apiParam {string} company_web 公司网站
     * @apiParam {integer} company_province 省份
     * @apiParam {integer} company_city 城市
     * @apiParam {integer} company_area 区域
     * @apiParam {string} address 详细地址
     * @apiParam {string} contact_name 联系人
     * @apiParam {integer} phone 手机
     * @apiParam {string} email 邮箱
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "data": {
                "id": 13,
                "type": 1,
                "type_value": "产品设计类型",
                "design_type": 2,
                "design_type_value": "产品设计",
                "status": 5,
                "field": 1,
                "field_value": "智能硬件",
                "industry": 2,
                "industry_value": "消费零售",
                "name": "api UI",
                "product_features": "亮点",
                "competing_product": "竞品",
                "cycle": 1,
                "cycle_value": "1个月内",
                "design_cost": 2,
                "design_cost_value": "1-5万之间",
                "province": 2,
                "city": 2,
                "image": [],
                "price": 200000
            },
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function store(Request $request)
    {
        $type = (int)$request->input('type');

        //产品设计
        if($type === 1){

            $rules = [
                'type' => 'required|integer',
                'design_type' => 'required|integer',
                'field' => 'required|integer',
                'industry' => 'required|integer',
            ];

            $all = $request->only(['type','design_type','field', 'industry']);

            $validator = Validator::make($all, $rules);
            if($validator->fails()){
                throw new StoreResourceFailedException('Error', $validator->errors());
            }

            $all['user_id'] = $this->auth_user_id;
            $all['status'] = 1;



            try{
                $item = Item::create($all);

                $product_design = ProductDesign::create([
                    'item_id' => intval($item->id),
                    'field' => $request->input('field'),
                    'industry' => $request->input('industry')
                ]);
            }
            catch (\Exception $e){
                return $this->response->array($this->apiError('Error', 500));
            }

            return $this->response->item($item, new ItemTransformer)->setMeta($this->apiMeta());

        }
        //UX UI设计
        elseif ($type === 2){
            $rules = [
                'type' => 'require|integer',
                'design_type' => 'required|integer',
//                'system' => 'required|integer',
//                'design_content' => 'required|integer',
            ];

            $all = $request->only(['type', 'design_type']);

            $validator = Validator::make($all, $rules);
            if($validator->fails()){
                throw new StoreResourceFailedException('Error', $validator->errors());
            }

            $all['user_id'] = $this->auth_user_id;
            $all['status'] = 1;

            try{
                $item = Item::create($all);

                $u_design = UDesign::create([
                    'item_id' => intval($item->id),
//                    'system' => $request->input('system'),
//                    'design_content' => $request->input('design_content')
                ]);
            }
            catch (\Exception $e){
                return $this->response->array($this->apiError('Error', 500));
            }

            return $this->response->item($item, new ItemTransformer)->setMeta($this->apiMeta());
        }else{
            return $this->response->array($this->apiError('not found', 404));
        }

    }


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
            "data": {
                "item": {
                    "id": 13,
                    "type": 1,
                    "type_value": "产品设计类型",
                    "design_type": 2,
                    "design_type_value": "产品设计",
                    "status": 5,  //-2.无设计接单关闭；-1.用户关闭；1.填写资料；2.人工干预；3.推送设计公司；4.等待设计公司接单(报价)；5.等待设计公司提交合同（提交合同）；6.确认合同（已提交合同）；7.已确定合同；8.托管项目资金；11.项目进行中；15.项目已完成；18.已项目验收。20.项目交易成功；22.已评价
                    "field": 2,
                    "field_value": "消费电子",
                    "industry": 2,
                    "industry_value": "消费零售",
                    "name": "api UI",
                    "product_features": "亮点",
                    "competing_product": "竞品",
                    "cycle": 1,
                    "cycle_value": "1个月内",
                    "design_cost": 2,
                    "design_cost_value": "1-5万之间",
                    "city": 2,
                    "image": [],
                    "price": 200000,
                    "company_name": null,  //公司名称
                    "company_abbreviation": null, //简称
                    "company_size": null, //公司规模；1...
                    "company_web": null,  //公司网址
                    "company_province": null, //省份
                    "company_city": null,  //城市
                    "company_area": null,   //区县
                    "address": null,    //详细地址
                    "contact_name": null,   //联系人
                    "phone": "172734923",
                    "email": "qq@qq.com",
 *                  "stage_status":0 //资料填写阶段；1.项目类型；2.需求信息；3.公司信息
                },
                "quotation": null, //报价单信息
                "contract": null   //合同
            },
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(!$item = Item::find(intval($id))){
            return $this->response->array($this->apiSuccess());
        }
        //验证是否是当前用户对应的项目
        if($item->user_id !== $this->auth_user_id){
            return $this->response->array($this->apiError('not found!', 404));
        }

        if(!$item){
            return $this->response->array($this->apiError());
        }
        return $this->response->item($item, new ItemTransformer)->setMeta($this->apiMeta());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * @api {put} /demand/{id} 更改项目类型、领域
     * @apiVersion 1.0.0
     * @apiName demand update
     * @apiGroup demandType
     *
     * @apiParam {string} token
     * @apiParam {integer} stage_status //阶段；1.项目类型；2.需求信息；3.公司信息
     * @apiParam {string} type 设计类型：1.产品设计；2.UI UX 设计
     * @apiParam {integer} design_type 产品设计（1.产品策略；2.产品设计；3.结构设计；）UXUI设计（1.app设计；2.网页设计；）
     * @apiParam {integer} field 所属领域
     * @apiParam {integer} industry 行业
     * @apiParam {string} company_name 公司名称
     * @apiParam {string} company_abbreviation 公司简称
     * @apiParam {integer} company_size 公司规模
     * @apiParam {string} company_web 公司网站
     * @apiParam {integer} company_province 省份
     * @apiParam {integer} company_city 城市
     * @apiParam {integer} company_area 区域
     * @apiParam {string} address 详细地址
     * @apiParam {string} contact_name 联系人
     * @apiParam {integer} phone 手机
     * @apiParam {string} email 邮箱
     *
     * @apiSuccessExample 成功响应:
     *   {
            "data": {
                "item": {
                    "id": 13,
                    "type": 1,
                    "type_value": "产品设计类型",
                    "design_type": 2,
                    "design_type_value": "产品设计",
                    "status": 5,
                    "field": 2,
                    "field_value": "消费电子",
                    "industry": 2,
                    "industry_value": "消费零售",
                    "name": "api UI",
                    "product_features": "亮点",
                    "competing_product": "竞品",
                    "cycle": 1,
                    "cycle_value": "1个月内",
                    "design_cost": 2,
                    "design_cost_value": "1-5万之间",
                    "city": 2,
                    "image": [],
                    "price": 200000,
                    "company_name": null,  //公司名称
                    "company_abbreviation": null, //简称
                    "company_size": null, //公司规模；1...
                    "company_web": null,  //公司网址
                    "company_province": null, //省份
                    "company_city": null,  //城市
                    "company_area": null,   //区县
                    "address": null,    //详细地址
                    "contact_name": null,   //联系人
                    "phone": "172734923",
                    "email": "qq@qq.com"
 *                  "stage_status":0 //资料填写阶段；1.项目类型；2.需求信息；3.公司信息
                },
                "quotation": null, //报价单信息
                "contract": null   //合同
            },
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $type = (int)$request->input('type');

        //产品设计
        if($type === 1){

            $rules = [
                'type' => 'required|integer',
                'design_type' => 'required|integer',
                'field' => 'required|integer',
                'industry' => 'required|integer',
            ];

            $all = $request->only(['stage_status','type', 'design_type', 'field', 'industry']);

            $validator = Validator::make($all, $rules);
            if($validator->fails()){
                throw new StoreResourceFailedException('Error', $validator->errors());
            }

            try{

                if(!$item = Item::find(intval($id))){
                    return $this->response->array($this->apiError('not found!', 404));
                }
                //验证是否是当前用户对应的项目
                if($item->user_id !== $this->auth_user_id || 1 != $item->status){
                    return $this->response->array($this->apiError('not found!', 404));
                }

                if(empty($all['stage_status'])){
                    unset($all['stage_status']);
                }
                $item->update($all);

                $product_design = ProductDesign::firstOrCreate(['item_id' => intval($item->id)]);
                $product_design->field = $request->input('field');
                $product_design->industry = $request->input('industry');
                $product_design->save();
            }
            catch (\Exception $e){
                Log::error($e->getMessage());
                return $this->response->array($this->apiError('Error', 500));
            }

            return $this->response->item($item, new ItemTransformer)->setMeta($this->apiMeta());

        }
        //UX UI设计
        elseif ($type === 2){
            $rules = [
                'type' => 'required|integer',
                'design_type' => ['required', 'integer'],
            ];
            $all = $request->only(['stage_status', 'type', 'design_type']);

            $validator = Validator::make($all, $rules);
            if($validator->fails()){
                throw new StoreResourceFailedException('Error', $validator->errors());
            }

            try{
                if(!$item = Item::find(intval($id))){
                    return $this->response->array($this->apiError('not found!', 404));
                }
                //验证是否是当前用户对应的项目
                if($item->user_id !== $this->auth_user_id || 1 != $item->status){
                    return $this->response->array($this->apiError('无编辑权限或当前状态禁止编辑!', 404));
                }

                if(empty($all['stage_status'])){
                    unset($all['stage_status']);
                }

                $item->update($all);

                $product_design = UDesign::firstOrCreate(['item_id' => intval($item->id)]);
            }
            catch (\Exception $e){
                Log::error($e->getMessage());
                return $this->response->array($this->apiError('Error', 500));
            }

            return $this->response->item($item, new ItemTransformer)->setMeta($this->apiMeta());
        }else{
            $rules = [
                'company_name' => 'nullable|min:1|max:50',
                'company_abbreviation' => 'nullable|min:1|max:50',
                'company_size' => 'nullable|integer',
                'company_web' => 'nullable|min:1|max:50',
                'company_province' => 'nullable|integer',
                'company_city' => 'nullable|integer',
                'company_area' => 'nullable|integer',
                'address' => 'nullable|min:1|max:50',
                'contact_name' => 'nullable|min:1|max:20',
                'email' => 'nullable|email',
            ];

            $all = $request->only(['stage_status', 'company_name','company_abbreviation', 'company_size', 'company_web', 'company_province', 'company_city', 'company_area', 'address', 'contact_name', 'phone', 'email']);

            $validator = Validator::make($all, $rules);
            if($validator->fails()){
                throw new StoreResourceFailedException('Error', $validator->errors());
            }

            try{

                if(!$item = Item::find(intval($id))){
                    return $this->response->array($this->apiError('not found!', 404));
                }
                //验证是否是当前用户对应的项目
                if($item->user_id !== $this->auth_user_id || 1 != $item->status){
                    return $this->response->array($this->apiError('not found!', 404));
                }
                $all['company_name'] = $request->input('company_name') ?? '';
                $all['company_abbreviation'] = $request->input('company_abbreviation') ?? '';
                $all['company_size'] = $request->input('company_size') ?? 0;
                $all['company_web'] = $request->input('company_web') ?? '';
                $all['company_province'] = $request->input('company_province') ?? 0;
                $all['company_city'] = $request->input('company_city') ?? 0;
                $all['company_area'] = $request->input('company_area') ?? 0;
                $all['address'] = $request->input('address') ?? '';
                $all['contact_name'] = $request->input('contact_name') ?? '';
                $all['phone'] = $request->input('phone') ?? '';
                $all['email'] = $request->input('email') ?? '';

                if(empty($all['stage_status'])){
                    unset($all['stage_status']);
                }
                $item->update($all);

            }
            catch (\Exception $e){
                Log::error($e->getMessage());
                return $this->response->array($this->apiError('Error', 500));
            }

            return $this->response->item($item, new ItemTransformer)->setMeta($this->apiMeta());
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @api {post} /demand/release 发布项目
     * @apiVersion 1.0.0
     * @apiName demand release
     * @apiGroup demandType
     *
     * @apiParam {string} token
     * @apiParam {integer} id 项目ID
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function release(Request $request)
    {
        $id = (int)$request->input('id');
        if(!$item = Item::find($id)){
            return $this->response->array($this->apiError('not found', 404));
        }

        //验证是否是当前用户对应的项目
        if($item->user_id !== $this->auth_user_id){
            return $this->response->array($this->apiError('not found!', 404));
        }

        try{
            $item->status = 2;
            $item->save();
        }
        catch (\Exception $e){
            return $this->response->array($this->apiError('Error', 500));
        }
        $auth_user = $this->auth_user;
        if(!$auth_user){
            return $this->response->array($this->apiError('not found!', 404));
        }
        $demand_company = DemandCompany::where('id' , $auth_user->demand_company_id)->first();
        if(!$demand_company){
            return $this->response->array($this->apiError('not found demandCompany!', 404));
        }
        dispatch(new Recommend($item));
        return $this->response->item($demand_company, new DemandCompanyTransformer())->setMeta($this->apiMeta());

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
    {
    "data": [
        {
            {
                "id": 1,
                "user_id": 1, //用户表ID (唯一索引)
                "company_type": 1, //企业类型：1.普通；2.多证合一；
                "company_name": "测试设计公司", //公司名称
                "registration_number": "12344556", //注册号
                "province": 1,
                "city": 2,
                "area": 3,
                "address": "北京朝阳",
                "contact_name": "小王", //联系人姓名
                "position": "老总", //职位
                "phone": "18629493220",
                "email": "qq@qq.com",
                "company_size": 4, //公司规模：1.10以下；2.10-50；3.50-100；4.100以上;
                "branch_office": 1, //分公司：1.有；2.无；
                "item_quantity": 2, //曾服务项目：1.10以下；2.10-50；3.50-100；4.100-200;5.200以上
                "company_profile": "一家有价值的公司",
                "good_field": "1,2,3", //good_field
                "web": "www.tai.com", //公司网站
                "establishment_time": "2013-12-10",
                "professional_advantage": "专业", //专业优势
                "awards": "就是专业", //荣誉奖项
                "created_at": "2017-04-11 14:54:24",
                "updated_at": "2017-04-11 14:59:36",
                "deleted_at": null,
                "score": 70,
                "status": 0, //设计公司状态：-1.禁用; 0.正常；
                "company_abbreviation": "", //简称
                "is_recommend": 0, //推荐
                "verify_status": 1 //审核状态
 *              "logo": [],
                "license_image": [],
                "unique_id": ""
            }
    },
        "meta": {
            "message": "Success",
            "status_code": 200
        }
    }
     */
    public function recommendList($item_id)
    {
        if(!$item = Item::find($item_id)){
            return $this->response->array($this->apiError('not found', 404));
        }

        //验证是否是当前用户对应的项目
        if($item->user_id !== $this->auth_user_id || $item->status !== 3){
            return $this->response->array($this->apiSuccess());
        }

        $recommend_arr = explode(',', $item->recommend);

        //如果推荐为空，则返回
        if(empty($recommend_arr)){
            return $this->response->array($this->apiSuccess('Success', 200, []));
        }

        $design_company = DesignCompanyModel::whereIn('id', $recommend_arr)->get();

        return $this->response->collection($design_company, new DesignCompanyShowTransformer)->setMeta($this->apiMeta());
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
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }


        try{

            $item = Item::find($all['item_id']);
            if(!empty(array_diff($all['design_company_id'], explode(',', $item->recommend)))){
                return $this->response->array($this->apiError('选择设计公司不符合要求', 403));
            };
            if($item->user_id != $this->auth_user_id || $item->status != 3){
                return $this->response->array($this->apiError('无操作权限或当前状态不可操作', 403));
            }
            //修改项目状态为：等待设计公司接单(报价)
            $item->status = 4;
            $item->save();
            //触发事件
            event(new ItemStatusEvent($item, $all['design_company_id']));

            //遍历插入推荐表
            foreach($all['design_company_id'] as $design_company_id)
            {
                ItemRecommend::create(['item_id' => $all['item_id'], 'design_company_id' => $design_company_id]);
            }

        }
        catch (\Exception $e){
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
     * @apiParam {integer} type 0:全部；1.进行中
     * @apiParam {integer} per_page 分页数量
     * @apiParam {integer} page 页码
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200,
                "pagination": {
                    "total": 3,
                    "count": 1,
                    "per_page": 1,
                    "current_page": 1,
                    "total_pages": 3,
                    "links": {
                    "next": "http://saas.me/demand/itemList?page=2"
                }
     *      }
     *      "data": [
                {
                    "item": {
                        "id": 1,
                        "type": 2, //1.产品设计；2.UI UX 设计；
                        "design_type": 1, //UXUI设计（1.app设计；2.网页设计；）
                        "status": 2, //状态：-2.匹配失败；-1.用户关闭；1.填写资料；2.人工干预；3.推送设计公司；4.等待设计公司接单(报价)；5.等待设计公司提交合同（提交合同）；6.确认合同（已提交合同）；7.已确定合同；8.托管项目资金；11.项目进行中；15.项目已完成；18.已项目验收。20.项目交易成功；22.已评价
     *                  "status_value": "填写资料", //状态说明（需求公司）
     *                  "design_status_value": ""  //状态说明 （设计公司））
                        "system": 1, 系统：1.ios；2.安卓；
                        "design_content": 0, //设计内容：1.视觉设计；2.交互设计；
                        "name": "", //项目名称
                        "stage": 0, //阶段：1、已有app／网站，需重新设计；2、没有app／网站，需要全新设计；
                        "complete_content": 0, //已完成设计内容：1.流程图；2.线框图；3.页面内容；4.产品功能需求点；5.其他
                        "other_content": "",  //其他设计内容
                        "design_cost": 0, //设计费用：1、1万以下；2、1-5万；3、5-10万；4.10-20；5、20-30；6、30-50；7、50以上
                        "province": 0,
                        "city": 0,
                        "image": []
                    },
                    "purpose_count": 0 //意向接单数量
                }
                {
                    "item": {
                        "id": 13,
                        "type": 1, //1.产品设计；2.UI UX 设计；
                        "design_type": 2, //产品设计（1.产品策略；2.产品设计；3.结构设计；）
                        "status": 2,
                        "field": 1, //所属领域ID
                        "industry": 2, //所属行业
                        "name": "api UI",
                        "product_features": "亮点", //产品功能或亮点
                        "competing_product": "竞品", //竞品
                        "cycle": 1, //设计周期：1.1个月内；2.1-2个月；3.2个月；4.2-4个月；5.其他
                        "design_cost": 2, //设计费用：1、1万以下；2、1-5万；3、5-10万；4.10-20；5、20-30；6、30-50；7、50以上
                        "province": 2,
                        "city": 2,
                        "image": []
                    },
                    "purpose_count": 0
                    }
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
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $per_page = $request->input('per_page') ?? $this->per_page;
        $type = $request->input('type') ?? 0;

        switch ($type){
            case 0:
                $where_in = [];
                break;
//            case 1:
//                $where_in = [1,2,3,4,5,6,7,8];
//                break;
            default:
                $where_in = [];
        }

        $items = Item::where('user_id', $this->auth_user_id);

        if(!empty($where_in)){
            $items = $items->whereIn('status', $where_in);
        }

        $items = $items->orderBy('id', 'desc')->paginate($per_page);
        if($items->isEmpty()){
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
                {
     *              "status": 5, //1.已选择其他设计公司;2.设计公司已拒绝;3.未操作；4.设计公司有意接单；5.选定该设计公司；
                    "status_value": "选定设计公司",
                    "item_status": 1, //需求方项目状态：-1.拒绝；0.待操作；1.选定设计公司；
                    "item_status_value": "选定设计公司",
                    "design_company_status": 0, //设计公司状态: -1.拒绝；0.待操作；1.一键成交；2.有意向报价;
                    "design_company_status_value": "待操作",
                    "design_company": {
                        "id": 1,
                        "user_id": 1, //用户表ID
                        "company_type": 1, //企业类型：1.普通；2.多证合一；
                        "company_name": "测试设计公司", //公司名称
                        "registration_number": "12344556", //注册号
                        "province": 1,
                        "city": 2,
                        "area": 3,
                        "address": "北京朝阳",
                        "contact_name": "小王", //联系人姓名
                        "position": "老总", //职位
                        "phone": "18629493220",
                        "email": "qq@qq.com",
                        "company_size": 4, //公司规模：1.10以下；2.10-50；3.50-100；4.100以上;
                        "branch_office": 1, //分公司：1.有；2.无；
                        "item_quantity": 2, //曾服务项目：1.10以下；2.10-50；3.50-100；4.100-200;5.200以上
                        "company_profile": "一家有价值的公司",
                        "good_field": "1,2,3", //good_field
                        "web": "www.tai.com", //公司网站
                        "establishment_time": "2013-12-10",
                        "professional_advantage": "专业", //专业优势
                        "awards": "就是专业", //荣誉奖项
                        "created_at": "2017-04-11 14:54:24",
                        "updated_at": "2017-04-11 14:59:36",
                        "deleted_at": null,
                        "score": 70,
                        "status": 0, //设计公司状态：-1.禁用; 0.正常；
                        "company_abbreviation": "", //简称
                        "is_recommend": 0, //推荐
                        "verify_status": 1 //审核状态
                    },
                    "quotation": null  //设计公司报价单
                },
            ],
     *  }
     */
    public function itemDesignList($item_id)
    {
        if(!$item = Item::find($item_id)){
            return $this->response->array($this->apiSuccess());
        }

        if($item->user_id !== $this->auth_user_id){
            return $this->response->array($this->apiError('not found!', 404));
        }

        $item_recommends = $item->itemRecommend;
        if($item_recommends->isEmpty()){
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
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        if(!$item = Item::find($all['item_id'])){
            return $this->response->array($this->apiError('not found', 404));
        }
        if($item->user_id !== $this->auth_user_id || $item->status !== 4){
            return $this->response->array($this->apiError('not found', 404));
        }

        try{
            DB::beginTransaction();
            $item_recommend = ItemRecommend::where(['item_id' => $all['item_id'], 'design_company_id' => $all['design_company_id']])->first();
            if(!$item_recommend){
                DB::rollBack();
                return $this->response->array($this->apiError('not found', 404));
            }

            //修改推荐关联表中需求方的状态
            $item_recommend->item_status = 1;
            $item_recommend->save();

            //拒绝其他设计公司
            $item_recommend_qt = ItemRecommend::where('item_id', '=', $all['item_id'])
                ->where('design_company_id', '!=' , $all['design_company_id'])
                ->where('design_company_status', '!=', -1)
                ->get();
            if(!$item_recommend_qt->isEmpty()){
                foreach($item_recommend_qt as $qt){
                    $qt->item_status = -1;
                    $qt->save();
                }
            }

            //将设计公司ID、项目金额写入需求item中，修改item状态为已选定设计公司；
            if(!$quotation = $item_recommend->quotation){
                DB::rollBack();
                return $this->response->array($this->apiError());
            }
            //修改报价单状态为已确认
            $quotation->status = 1;
            $quotation->save();

            $item->design_company_id = $all['design_company_id'];
            $item->price = $quotation->price;
            $item->quotation_id = $quotation->id;
            $item->status = 5;
            $item->save();

            //触发项目状态事件
            $design_company_id = $item_recommend_qt->pluck('design_company_id')->all();
            event(new ItemStatusEvent($item,['yes' => $all['design_company_id'], 'no' => $design_company_id]));

            DB::commit();
            return $this->response->array($this->apiSuccess());

        }catch(\Exception $e){
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
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        if(!$item = Item::find($all['item_id'])){
            return $this->response->array($this->apiError('not found', 404));
        }
        if($item->user_id != $this->auth_user_id || $item->status != 4){
            return $this->response->array($this->apiError('无权限', 403));
        }

        $item_recommend = ItemRecommend::where(['item_id' => $all['item_id'], 'design_company_id' => $all['design_company_id']])->first();
        if(!$item_recommend){
            return $this->response->array($this->apiError('not found', 404));
        }

        //修改推荐关联表中需求方的状态
        $item_recommend->item_status = -1;
        if(!$item_recommend->save()){
            return $this->response->array($this->apiError('状态修改失败', 500));
        }

        //消息通知
        $design = DesignCompanyModel::find($all['design_company_id']);
        $tools = new Tools();
        $tools->message($design->user_id, '【' . ($item->itemInfo())['name'] . '】' . '已选择其他设计公司');

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
        if(!$item = Item::find($item_id)){
            return $this->response->array($this->apiError());
        }
        if($item->user_id !== $this->auth_user_id || $item->status !== 6){
            return $this->response->array($this->apiError());
        }
        try{
            DB::beginTransaction();
            $item->status = 7;
            $item->save();

            //修改合同状态为已确认
            $contract = $item->contract;
            $contract->status = 1;
            $contract->save();

            //触发项目状态变更事件
            event(new ItemStatusEvent($item));

            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            Log::error($e);
            return $this->response->array($this->apiError('Error', 500));
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {post} /demand/closeItem 用户关闭需求项目
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

        if(!$item = Item::find($item_id)){
            return $this->response->array($this->apiError('not found item', 404));
        }

        if($item->user_id != $this->auth_user_id){
            return $this->response->array($this->apiError('Permission denied', 403));
        }

        if($item->status != -2){
            return $this->response->array($this->apiError('当前状态不能修改', 403));
        }

        //修改为用户已关闭
        $item->status = -1;
        if(!$item->save()){
            return $this->response->array($this->apiError('Error', 500));
        }

        //解冻保证金
        $pay_order = PayOrder::where([
            'user_id' => $this->auth_user_id,
            'item_id' => $item_id,
            'type' => 1,
            'status' => 1,
            ])->first();
        if($pay_order){
            $user = $this->auth_user;
            $user->price_frozen -= $pay_order->amount;
            if($user->price_frozen < 0){
                $user->price_frozen = 0;
            }
            $user->save();
        }

        return $this->response->array($this->apiSuccess());
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

        if(!$item = Item::find($item_id)){
            return $this->response->array($this->apiError('not found item', 404));
        }

        if($item->user_id != $this->auth_user_id){
            return $this->response->array($this->apiError('Permission denied', 403));
        }

        if($item->status != -2){
            return $this->response->array($this->apiError('当前状态不能修改', 403));
        }

        //清除推荐公司Id，追加至曾推荐公司ID字段
        $item->ord_recommend = $item->ord_recommend ? ($item->ord_recommend . ',' . $item->recommend) : $item->recommend;
        $item->recommend = '';
        $item->status = 1; //填写资料阶段
        if(!$item->save()){
            return $this->response->array($this->apiError('Error', 500));
        }

        return $this->response->array($this->apiSuccess());
    }
}
