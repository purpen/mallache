<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\DesignCompanyTransformer;
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
     * @apiParam {string} registration_number 公司注册号
     * @apiParam {string} establishment_time 成立时间
     * @apiParam {integer} company_size 公司规模 1.10以下；2.10-50；3.50-100；4.100以上;
     * @apiParam {integer} province 省份
     * @apiParam {integer} city 城市
     * @apiParam {integer} area 区域
     * @apiParam {string} address 详细地址
     * @apiParam {string} contact_name 联系人姓名
     * @apiParam {string} position 职位
     * @apiParam {integer} phone 手机
     * @apiParam {string} email 邮箱
     * @apiParam {integer} branch_office 分公司
     * @apiParam {integer} item_quantity 服务项目 1.10以下；2.10-50；3.50-100；4.100-200;5.200以上
     * @apiParam {integer} company_type 公司类型 	企业类型：1.普通；2.多证合一；
     * @apiParam {string} good_field 擅长领域
     * @apiParam {string} web 公司网站
     * @apiParam {string} company_profile 公司简介
     * @apiParam {string} professional_advantage 专业优势
     * @apiParam {string} awards 荣誉奖项
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
            'company_type'  => 'required|integer',
            'company_name'  => 'required|max:50',
            'company_abbreviation'  => 'required|max:50',
            'registration_number'  => 'required|max:15',
            'province'  => 'required|integer',
            'city'  => 'required|integer',
            'area'  => 'required|integer',
            'address'  => 'required|max:50',
            'contact_name'  => 'required|max:20',
            'position'  => 'required|max:20',
            'phone'  => ['required','regex:/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/'],
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
            'company_type.integer' => '企业类型必须为整形',
            'company_type.required' => '企业类型不能为空',
            'company_name.required' => '公司名称不能为空',
            'company_abbreviation.required' => '公司简称不能为空',
            'company_abbreviation.max' => '公司简称不能超过50个字',
            'company_name.max' => '公司名称不能超过50个字',
            'registration_number.required' => '注册号不能为空',
            'registration_number.max' => '注册号不能超过50个字',
            'province.required' => '省份不能为空',
            'province.integer' => '省份必须为整形',
            'city.required' => '城市不能为空',
            'city.integer' => '城市必须为整形',
            'area.required' => '区域不能为空',
            'area.integer' => '区域必须为整形',
            'address.required' => '详细地址不能为空',
            'address.max' => '详细地址不能超过50个字',
            'contact_name.required' => '联系人姓名不能为空',
            'contact_name.max' => '联系人姓名不能超过20个字',
            'position.required' => '职位不能为空',
            'position.max' => '职位不能超过20个字',
            'phone.required' => '手机号不能为空',
            'phone.regex' => '手机号格式不正确',
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
            }
        }
        catch (\Exception $e){
            return $this->response->array($this->apiError());
        }
        return $this->response->item($design, new DesignCompanyTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /designCompany/{user_id}  根据用户id设计公司展示
     * @apiVersion 1.0.0
     * @apiName designCompany show
     * @apiGroup designCompany
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
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
     *          "created_at": "2017-04-05 18:55:54",
     *          "updated_at": "2017-04-05 19:40:14",
     *          "deleted_at": null,
     *          "item": [
     *          {
     *              "id": 1,
     *              "user_id": 1,
     *              "good_field": 1,
     *              "project_cycle": 1,
     *              "min_price": "1.00",
     *              "max_price": "1.00",
     *              "created_at": "2017-04-07 16:50:30",
     *              "updated_at": "2017-04-07 17:54:37",
     *              "deleted_at": null
     *          },
     *          {
     *              "id": 2,
     *              "user_id": 1,
     *              "good_field": 22,
     *              "project_cycle": 22,
     *              "min_price": "22.00",
     *              "max_price": "22.00",
     *              "created_at": "2017-04-07 17:07:12",
     *              "updated_at": "2017-04-07 17:54:05",
     *              "deleted_at": null
     *          }
     *      ]
     *   }
     */
    public function show(Request $request)
    {
        $user_id = intval($this->auth_user_id);
        $design = DesignCompanyModel::where('user_id', $user_id)->first();
        $design->item = DesignItemModel::where('user_id' , $user_id)->get();
        if(!$design){
            return $this->response->array($this->apiError());
        }
        return $this->response->array($design->toArray());
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
     * @api {put} /designCompany/{user_id} 根据用户id更新设计公司信息
     * @apiVersion 1.0.0
     * @apiName designCompany update
     * @apiGroup designCompany
     *
     * @apiParam {string} company_name 公司名称
     * @apiParam {string} company_abbreviation 公司简称
     * @apiParam {string} registration_number 公司注册号
     * @apiParam {string} establishment_time 成立时间
     * @apiParam {integer} company_size 公司规模 1.10以下；2.10-50；3.50-100；4.100以上;
     * @apiParam {integer} province 省份
     * @apiParam {integer} city 城市
     * @apiParam {integer} area 区域
     * @apiParam {string} address 详细地址
     * @apiParam {string} contact_name 联系人
     * @apiParam {string} position 职位
     * @apiParam {integer} phone 手机
     * @apiParam {string} email 邮箱
     * @apiParam {integer} branch_office 分公司
     * @apiParam {integer} item_quantity 服务项目 1.10以下；2.10-50；3.50-100；4.100-200;5.200以上
     * @apiParam {integer} company_type 公司类型 	企业类型：1.普通；2.多证合一；
     * @apiParam {string} good_field 擅长领域
     * @apiParam {string} web 公司网站
     * @apiParam {string} company_profile 公司简介
     * @apiParam {string} professional_advantage 专业优势
     * @apiParam {string} awards 荣誉奖项
     * @apiParam {string} token
     * @apiSuccessExample 成功响应:
     *   {
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
            'company_type'  => 'required|integer',
            'company_name'  => 'required|max:50',
            'company_abbreviation'  => 'required|max:50',
            'registration_number'  => 'required|max:15',
            'province'  => 'required|integer',
            'city'  => 'required|integer',
            'area'  => 'required|integer',
            'address'  => 'required|max:50',
            'contact_name'  => 'required|max:20',
            'position'  => 'required|max:20',
            'phone'  => ['required','regex:/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/'],
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
            'company_type.integer' => '企业类型必须为整形',
            'company_type.required' => '企业类型不能为空',
            'company_name.required' => '公司名称不能为空',
            'company_name.max' => '公司名称不能超过50个字',
            'company_abbreviation.required' => '公司简称不能为空',
            'company_abbreviation.max' => '公司简称不能超过50个字',
            'registration_number.required' => '注册号不能为空',
            'registration_number.max' => '注册号不能超过50个字',
            'province.required' => '省份不能为空',
            'province.integer' => '省份必须为整形',
            'city.required' => '城市不能为空',
            'city.integer' => '城市必须为整形',
            'area.required' => '区域不能为空',
            'area.integer' => '区域必须为整形',
            'address.required' => '详细地址不能为空',
            'address.max' => '详细地址不能超过50个字',
            'contact_name.required' => '联系人姓名不能为空',
            'contact_name.max' => '联系人姓名不能超过20个字',
            'position.required' => '职位不能为空',
            'position.max' => '职位不能超过20个字',
            'phone.required' => '手机号不能为空',
            'phone.regex' => '手机号格式不正确',
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
        $design = DesignCompanyModel::where('user_id', $user_id)->update($all);
        if(!$design){
            return $this->response->array($this->apiError());
        }
        return $this->response->array($this->apiSuccess());
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
     * @api {put} /designCompany/1/upStatus 更新设计公司审核状态
     * @apiVersion 1.0.0
     * @apiName designCompany upStatus
     * @apiGroup designCompany
     *
     * @apiParam {Integer} id 设计公司ID.
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
    public function upStatus($id)
    {
        $design = DesignCompanyModel::upStatus($id);
        if(!$design){
            return $this->response->array($this->apiError());
        }
        return $this->response->array($this->apiSuccess());
    }
}
