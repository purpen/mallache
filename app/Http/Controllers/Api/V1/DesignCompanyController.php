<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\DesignCompanyShowTransformer;
use App\Http\Transformer\DesignCompanyTransformer;
use App\Http\Transformer\DesignItemTransformer;
use App\Models\AssetModel;
use App\Models\DesignItemModel;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\DesignCompanyModel;
use Symfony\Component\HttpKernel\Exception\HttpException;


class DesignCompanyController extends BaseController
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
     */
    public function create()
    {
        //
    }

    /**
     * @api {post} /designCompany 保存设计公司信息
     * @apiVersion 1.0.0
     * @apiName designCompany store
     * @apiGroup designCompany
     * @apiParam {string} company_name 公司名称
     * @apiParam {string} company_abbreviation 公司简称
     * @apiParam {integer} company_type 企业类型：1.普通；2.多证合一；
     * @apiParam {string} registration_number 注册号
     * @apiParam {string} establishment_time 成立时间
     * @apiParam {integer} company_size 公司规模 1.10以下；2.10-50；3.50-100；4.100以上;
     * @apiParam {integer} province 省份
     * @apiParam {integer} city 城市
     * @apiParam {integer} area 区域
     * @apiParam {string} address 详细地址
     * @apiParam {string} contact_name 联系人姓名
     * @apiParam {string} position 职位
     * @apiParam {string} phone 手机
     * @apiParam {string} email 邮箱
     * @apiParam {integer} branch_office 分公司
     * @apiParam {integer} item_quantity 服务项目 1.10以下；2.10-50；3.50-100；4.100-200;5.200以上
     * @apiParam {string} good_field 擅长领域
     * @apiParam {string} web 公司网站
     * @apiParam {string} company_profile 公司简介
     * @apiParam {string} professional_advantage 专业优势
     * @apiParam {string} awards 荣誉奖项
     * @apiParam {string} random 随机数
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "data": {
     *          "id": 4,
     *          "user_id": 1,
     *          "company_type": 0,
     *          "company_name": "",
     *          "company_abbreviation": "",
     *          "registration_number": "",
     *          "province": 0,
     *          "city": 0,
     *          "area": 0,
     *          "address": "",
     *          "contact_name": "",
     *          "position": "",
     *          "phone": 0,
     *          "email": "",
     *          "company_size": 0,
     *          "branch_office": 0,
     *          "item_quantity": 0,
     *          "good_field": "",
     *          "web": "",
     *          "company_profile": "",
     *          "establishment_time": "",
     *          "professional_advantage": "",
     *          "awards": "",
     *          "status": 0,
     *          "is_recommend": 0,
     *          "verify_status": 0,
     *          "item": null
     *      },
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function store(Request $request)
    {
        // 验证规则
        $rules = [
            'design_type'  => 'nullable|max:50',
            'company_name'  => 'required|max:50',
            'company_abbreviation'  => 'required|max:50',
            'province'  => 'required|integer',
            'address'  => 'required|max:50',
            'contact_name'  => 'required|max:20',
            'phone'  => 'required',
            'email'  => 'required|email',
            'company_size'  => 'nullable|integer',
            'branch_office'  => 'nullable|integer',
            'item_quantity'  => 'nullable|integer',
            'web'  => 'max:50',
            'company_profile'  => 'max:500',
            'establishment_time'  => 'nullable|date',
            'good_field'  => 'max:50',
            'professional_advantage'  => 'max:500',
            'awards'  => 'max:500'
        ];
        $messages = [
            'design_type.max' => '产品设计不能超过50个字',
            'company_name.required' => '公司名称不能为空',
            'company_abbreviation.required' => '公司简称不能为空',
            'company_abbreviation.max' => '公司简称不能超过50个字',
            'company_name.max' => '公司名称不能超过50个字',
            'province.required' => '省份不能为空',
            'province.integer' => '省份必须为整形',
            'address.required' => '详细地址不能为空',
            'address.max' => '详细地址不能超过50个字',
            'contact_name.required' => '联系人姓名不能为空',
            'contact_name.max' => '联系人姓名不能超过20个字',
            'phone.required' => '手机号不能为空',
            'email.required' => '邮箱不能为空',
            'email.email' => '邮箱格式不正确',
            'company_size.integer' => '公司规模必须是整形',
            'branch_office.integer' => '分公司必须是整形',
            'item_quantity.integer' => '服务项目必须是整形',
            'web.max' => '公司网站不能超过50个字',
            'establishment_time.date' => '公司成立时间格式不正确',
            'good_field.max' => '擅长领域不能超过50个字',
            'professional_advantage.max' => '专业优势不能超过500个字',
            'awards.max' => '荣誉奖项不能超过500个字'
        ];
        $all = $request->all();
        $city = $request->input('city');
        $area = $request->input('area');
        $branch_office = $request->input('branch_office');
        $goodField = $request->input('good_field');
        if(!empty($goodField)){
            //合并擅长领域
            $good_field = implode(',' , $goodField);
            $all['good_field'] = $good_field;
        }

        //如果城市为空，默认为0
        if($city == null){
            $all['city'] = 0 ;
        }
        //如果地区为空，默认为0
        if($area == null){
            $all['area'] = 0 ;
        }
        //如果分公司为空，默认为0
        if($branch_office == null){
            $all['branch_office'] = 0 ;
        }
        $all['user_id'] = $this->auth_user_id;
        $validator = Validator::make($all , $rules, $messages);

        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        try{
            //检查用户的唯一性
            $design = DesignCompanyModel::where('user_id' , $this->auth_user_id)->count();
            if($design > 0){
                return $this->response->array($this->apiError('已存在该设计公司'));
            }else{
                $design = DesignCompanyModel::create($all);
                $random = $request->input('random');
                AssetModel::setRandom($design->id , $random);
            }
        }
        catch (\Exception $e){
            return $this->response->array($this->apiError());
        }
        return $this->response->item($design, new DesignCompanyTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /designCompany  设计公司展示
     * @apiVersion 1.0.0
     * @apiName designCompany show
     * @apiGroup designCompany
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *     "data": {
     *          "id": 8,
     *          "company_type": 1, 1.普通；2.多证合一；
     *          "company_name": "",
     *          "company_abbreviation": "",
     *          "registration_number": "",
     *          "province": 0,
     *          "city": 0,
     *          "area": 0,
     *          "address": "",
     *          "contact_name": "",
     *          "position": "",
     *          "phone": 0,
     *          "email": "",
     *          "company_size": 1,  1.10以下；2.10-50；3.50-100；4.100以上;
     *          "branch_office": 0,
     *          "item_quantity": 1,  1.10以下；2.10-50；3.50-100；4.100-200;5.200以上
     *          "good_field": "",
     *          "web": "",
     *          "company_profile": "",
     *          "design_type": "2",  1.产品策略；2.产品设计；3.结构设计；ux设计：4.app设计；5.网页设计；
     *          "establishment_time": "",
     *          "professional_advantage": "",
     *          "awards": "",
     *          "score": 0,
     *          "status": 1
     *          "is_recommend": 0,
     *          "verify_status": 0,
     *      },
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function show(Request $request)
    {
        $user_id = intval($this->auth_user_id);

        $design = DesignCompanyModel::where('user_id', $user_id)->first();
        if(!empty($design)){
            $design->good_field = explode(',' , $design['good_field']);
        }
        if(!$design){
            return $this->response->array($this->apiSuccess());
        }
        return $this->response->item($design , new DesignCompanyShowTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /designCompany/otherShow/{id} 其它公司查看根据设计公司id查看信息
     * @apiVersion 1.0.0
     * @apiName designCompany show
     * @apiGroup designCompany
     *
     * @apiParam {string} token
     */
    public function otherShow($id)
    {
        $design = DesignCompanyModel::where('id', $id)->first();
        if(!empty($design)){
            $design->good_field = explode(',' , $design['good_field']);
        }
        $design->item->type = DesignItemModel::where('user_id', $design->user_id)->get();
        Log::info($design->item->type);
        if(!$design){
            return $this->response->array($this->apiSuccess());
        }
        return $this->response->item($design, new DesignCompanyShowTransformer())->setMeta($this->apiMeta());

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DesignCompanyModel  $designCompanyModel
     * @return \Illuminate\Http\Response
     */
    public function edit(DesignCompanyModel $designCompanyModel)
    {
        //
    }

    /**
     * @api {put} /designCompany 更新设计公司信息
     * @apiVersion 1.0.0
     * @apiName designCompany update
     * @apiGroup designCompany
     *
     * @apiParam {string} company_name 公司名称
     * @apiParam {string} company_abbreviation 公司简称
     * @apiParam {integer} company_type 企业类型：1.普通；2.多证合一；
     * @apiParam {string} registration_number 注册号
     * @apiParam {string} establishment_time 成立时间
     * @apiParam {integer} company_size 公司规模 1.10以下；2.10-50；3.50-100；4.100以上;
     * @apiParam {integer} province 省份
     * @apiParam {integer} city 城市
     * @apiParam {integer} area 区域
     * @apiParam {string} address 详细地址
     * @apiParam {string} contact_name 联系人
     * @apiParam {string} position 职位
     * @apiParam {string} phone 手机
     * @apiParam {string} email 邮箱
     * @apiParam {integer} branch_office 分公司
     * @apiParam {integer} item_quantity 服务项目 1.10以下；2.10-50；3.50-100；4.100-200;5.200以上
     * @apiParam {string} good_field 擅长领域
     * @apiParam {string} web 公司网站
     * @apiParam {string} company_profile 公司简介
     * @apiParam {string} professional_advantage 专业优势
     * @apiParam {string} awards 荣誉奖项
     * @apiParam {string} token
     * @apiSuccessExample 成功响应:
     *   {
     *      "data": {
     *          "id": 4,
     *          "user_id": 1,
     *          "company_type": 0,
     *          "company_name": "",
     *          "company_abbreviation": "",
     *          "registration_number": "",
     *          "province": 0,
     *          "city": 0,
     *          "area": 0,
     *          "address": "",
     *          "contact_name": "",
     *          "position": "",
     *          "phone": 0,
     *          "email": "",
     *          "company_size": 0,
     *          "branch_office": 0,
     *          "item_quantity": 0,
     *          "good_field": "",
     *          "web": "",
     *          "company_profile": "",
     *          "establishment_time": "",
     *          "professional_advantage": "",
     *          "awards": "",
     *          "status": 0,
     *          "is_recommend": 0,
     *          "verify_status": 0,
     *          "item": null
     *      },
     *     "meta": {
     *       "message": "",
     *       "status_code": 200
     *     }
     *   }
     *  }
     */
    public function update(Request $request)
    {
        $user_id = $this->auth_user_id;
        // 验证规则
        $rules = [
            'design_type'  => 'nullable|max:50',
            'company_name'  => 'required|max:50',
            'company_abbreviation'  => 'required|max:50',
            'province'  => 'required|integer',
            'address'  => 'required|max:50',
            'contact_name'  => 'required|max:20',
            'phone'  => 'required',
            'email'  => 'required|email',
            'company_size'  => 'nullable|integer',
            'branch_office'  => 'nullable|integer',
            'item_quantity'  => 'nullable|integer',
            'web'  => 'max:50',
            'company_profile'  => 'max:500',
            'establishment_time'  => 'nullable|date',
            'good_field'  => 'max:50',
            'professional_advantage'  => 'max:500',
            'awards'  => 'max:500'
        ];
        $messages = [
            'design_type.max' => '产品设计不能超过50个字',
            'company_name.required' => '公司名称不能为空',
            'company_name.max' => '公司名称不能超过50个字',
            'company_abbreviation.required' => '公司简称不能为空',
            'company_abbreviation.max' => '公司简称不能超过50个字',
            'province.required' => '省份不能为空',
            'province.integer' => '省份必须为整形',
            'address.required' => '详细地址不能为空',
            'address.max' => '详细地址不能超过50个字',
            'contact_name.required' => '联系人姓名不能为空',
            'contact_name.max' => '联系人姓名不能超过20个字',
            'phone.required' => '手机号不能为空',
            'email.required' => '邮箱不能为空',
            'email.email' => '邮箱格式不正确',
            'company_size.integer' => '公司规模必须是整形',
            'branch_office.integer' => '分公司必须是整形',
            'item_quantity.integer' => '服务项目必须是整形',
            'web.max' => '公司网站不能超过50个字',
            'establishment_time.date' => '公司成立时间格式不正确',
            'good_field.max' => '擅长领域不能超过50个字',
            'professional_advantage.max' => '专业优势不能超过500个字',
            'awards.max' => '荣誉奖项不能超过500个字'
        ];
        $all = $request->except(['token']);
        $validator = Validator::make($all , $rules, $messages);

        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        //擅长领域合并成字符串
        $goodField = $request->input('good_field');
        if(!empty($goodField)){
            //合并擅长领域
            $good_field = implode(',' , [$goodField]);
            $all['good_field'] = $good_field;
        }
        $design = DesignCompanyModel::where('user_id', $user_id)->first();
        $design->update($all);
        if(!$design){
            return $this->response->array($this->apiError());
        }
        return $this->response->item($design, new DesignCompanyTransformer())->setMeta($this->apiMeta());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DesignCompanyModel  $designCompanyModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(DesignCompanyModel $designCompanyModel)
    {
        //
    }


    /**
     * @api {put} /designCompany/verifyStatus 设计公司通过审核
     * @apiVersion 1.0.0
     * @apiName designCompany verifyStatus
     * @apiGroup designCompany
     *
     * @apiParam {integer} id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *  "meta": {
     *    "code": 200,
     *    "message": "Success.",
     *  }
     * }
     */
    public function verifyStatus(Request $request)
    {
        $id = $request->input('id');
        $design_company = DesignCompanyModel::where('id' , $id)->first();
        if(!$design_company){
            return $this->response->array($this->apiSuccess('设计公司不存在' , 200));
        }
        $design = DesignCompanyModel::verifyStatus($id , 1);
        if(!$design){
            return $this->response->array($this->apiSuccess());
        }
        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {put} /designCompany/unVerifyStatus 设计公司审核中
     * @apiVersion 1.0.0
     * @apiName designCompany unVerifyStatus
     * @apiGroup designCompany
     *
     * @apiParam {integer} id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *  "meta": {
     *    "code": 200,
     *    "message": "Success.",
     *  }
     * }
     */
    public function unVerifyStatus(Request $request)
    {
        $id = $request->input('id');
        $design_company = DesignCompanyModel::where('id' , $id)->first();
        if(!$design_company){
            return $this->response->array($this->apiSuccess('设计公司不存在' , 200));
        }
        $design = DesignCompanyModel::verifyStatus($id , 0);
        if(!$design){
            return $this->response->array($this->apiSuccess());
        }
        return $this->response->array($this->apiSuccess());
    }


    /**
     * @api {put} /designCompany/status 设计公司正常
     * @apiVersion 1.0.0
     * @apiName designCompany status
     * @apiGroup designCompany
     *
     * @apiParam {integer} id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *  "meta": {
     *    "code": 200,
     *    "message": "Success.",
     *  }
     * }
     */
    public function okStatus(Request $request)
    {
        $id = $request->input('id');
        $design_company = DesignCompanyModel::where('id' , $id)->first();
        if(!$design_company){
            return $this->response->array($this->apiSuccess('设计公司不存在' , 200));
        }
        $design = DesignCompanyModel::unStatus($id , 0);
        if(!$design){
            return $this->response->array($this->apiSuccess());
        }
        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {put} /designCompany/unStatus 设计公司禁用
     * @apiVersion 1.0.0
     * @apiName designCompany unStatus
     * @apiGroup designCompany
     *
     * @apiParam {integer} id
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *  "meta": {
     *    "code": 200,
     *    "message": "Success.",
     *  }
     * }
     */
    public function unStatus(Request $request)
    {
        $id = $request->input('id');
        $design_company = DesignCompanyModel::where('id' , $id)->first();
        if(!$design_company){
            return $this->response->array($this->apiSuccess('设计公司不存在' , 200));
        }
        $design = DesignCompanyModel::unStatus($id , -1);
        if(!$design){
            return $this->response->array($this->apiSuccess());
        }
        return $this->response->array($this->apiSuccess());
    }
}
