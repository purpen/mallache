<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\DesignCompanyTransformer;
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
     * @apiParam {string} design_type 设计类型
     * @apiParam {string} company_name 公司名称
     * @apiParam {string} registration_number 公司注册号
     * @apiParam {string} establishment_time 成立时间
     * @apiParam {integer} company_size 公司规模
     * @apiParam {integer} company_size 公司规模
     * @apiParam {integer} company_size 公司规模
     * @apiParam {integer} province 省份
     * @apiParam {integer} city 城市
     * @apiParam {integer} area 区域
     * @apiParam {string} address 详细地址
     * @apiParam {string} contact_name 联系人
     * @apiParam {string} position 联系人
     * @apiParam {integer} phone 手机
     * @apiParam {string} email 邮箱
     * @apiParam {integer} branch_office 分公司
     * @apiParam {integer} item_quantity 服务项目
     * @apiParam {integer} company_type 公司类型
     * @apiParam {string} good_field 擅长领域
     * @apiParam {string} web 公司网站
     * @apiParam {string} company_profile 公司简介
     * @apiParam {string} professional_advantage 专业优势
     * @apiParam {string} awards 荣誉奖项
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *     "meta": {
     *       "message": "",
     *       "status_code": 200
     *     }
     *   }
     *   "data": {
     *
     *    }
     *  }
     */
    public function store(Request $request)
    {
        $all = $request->all();
        $all['user_id'] = $this->auth_user_id;
        // 验证规则
        $rules = [
            'design_type'  => 'required',
        ];
        $messages = [
            'design_type.required' => '设计类型不能为空',
        ];

        $validator = Validator::make($request->only('design_type'), $rules, $messages);

        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        try{
            $design = DesignCompanyModel::create($all);
        }
        catch (\Exception $e){
            throw new HttpException('Error');
        }

        return $this->response->item($design, new DesignCompanyTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /designCompany/1  设计公司展示
     * @apiVersion 1.0.0
     * @apiName designCompany show
     * @apiGroup designCompany
     *
     * @apiParam {integer} user_id 用户ID
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "data": {
     *          "id": 8,
     *          "company_type": 0,
     *          "company_name": "",
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
     *          "design_type": "12345",
     *          "establishment_time": "",
     *          "professional_advantage": "",
     *          "awards": ""
     *      },
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *   }
     */
    public function show(Request $request)
    {
        $user_id = intval($request->input('user_id'));
        $demand = DesignCompanyModel::where('user_id', $user_id)->first();
        if(!$demand){
            return $this->response->array($this->apiError());
        }
        return $this->response->item($demand, new DesignCompanyTransformer())->setMeta($this->apiMeta());
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
     * @api {put} /designCompany/1 更新设计公司信息
     * @apiVersion 1.0.0
     * @apiName designCompany update
     * @apiGroup designCompany
     *
     * @apiParam {string} design_type 设计类型
     * @apiParam {string} company_name 公司名称
     * @apiParam {string} registration_number 公司注册号
     * @apiParam {string} establishment_time 成立时间
     * @apiParam {integer} company_size 公司规模
     * @apiParam {integer} company_size 公司规模
     * @apiParam {integer} company_size 公司规模
     * @apiParam {integer} province 省份
     * @apiParam {integer} city 城市
     * @apiParam {integer} area 区域
     * @apiParam {string} address 详细地址
     * @apiParam {string} contact_name 联系人
     * @apiParam {string} position 联系人
     * @apiParam {integer} phone 手机
     * @apiParam {string} email 邮箱
     * @apiParam {integer} branch_office 分公司
     * @apiParam {integer} item_quantity 服务项目
     * @apiParam {integer} company_type 公司类型
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
    public function update(Request $request, $id)
    {
        $all = $request->except(['token']);

        $demand = DesignCompanyModel::where('user_id', intval($id))->update($all);
        if(!$demand){
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
}
