<?php
/**
 * 需求公司基本信息控制器
 *
 * @User llh
 * @time 2017-3-31
 */

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\DemandCompanyTransformer;
use App\Models\AssetModel;
use App\Models\DemandCompany;
use App\Models\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DemandCompanyController extends BaseController
{
    /**
     * @api {get} /demandCompany 获取需求用户信息
     * @apiVersion 1.0.0
     * @apiName demandCompany show
     * @apiGroup demandCompany
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "data": {
     *          "id": 1,
     *          "company_name": "nihao",
     *          "company_size": 1,
     *          "company_web": "http://www.baidu.com",
     *          "province": 1,
     *          "city": 4,
     *          "area": 5,
     *          "address": "beijing",
     *          "contact_name": "lisna",
     *          "phone": 18629493221,
     *          "email": "qq@qq.com",
     *          "logo_image": [],
     *          "verify_status": 0, //审核状态
     *          "license_image": [],  //营业执照附件
     *          "position": "",     //职位
     *          "company_type": 0,  // 企业类型：1.普通；2.多证合一（不含社会统一信用代码）；3.多证合一（含社会统一信用代码）
     *          "company_type_value": "",
     *          "registration_number": "",  //注册号
     *          "legal_person": "",         //法人姓名
     *          "document_type": 0,        //法人证件类型：1.身份证；2.港澳通行证；3.台胞证；4.护照；
     *          "document_type_value": "",
     *          "document_number": "",     //证件号码
     *          "company_property": 0,     //企业性质：1.初创企业、2.私企、3.国有企业、4.事业单位、5.外资、6.合资、7.上市公司
     *          "company_property_value": ""
     *          "document_image":[],  //法人证件
     *          "verify_summary": '',  // 审核备注
     *      },
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function show()
    {
        $demand = DemandCompany::where('user_id', $this->auth_user_id)->first();
        if (!$demand) {
            return $this->response->array($this->apiSuccess('Success', 200, []));
        }

        return $this->response->item($demand, new DemandCompanyTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /demandCompany 更新需求用户信息
     * @apiVersion 1.0.0
     * @apiName demandCompany update
     * @apiGroup demandCompany
     *
     * @apiParam {string} company_name 公司名称
     * @apiParam {string} company_abbreviation 公司简称
     * @apiParam {integer} company_size 公司规模
     * @apiParam {string} company_web 公司网站
     * @apiParam {integer} province 省份
     * @apiParam {integer} city 城市
     * @apiParam {integer} area 区域
     * @apiParam {string} address 详细地址
     * @apiParam {string} position 职位
     * @apiParam {string} contact_name 联系人
     * @apiParam {integer} phone 手机
     * @apiParam {string} email 邮箱
     * @apiParam {integer} company_type 企业类型：1.普通；2.多证合一（不含社会统一信用代码）；3.多证合一（含社会统一信用代码）
     * @apiParam {string} registration_number 注册号
     * @apiParam {string}   legal_person 法人姓名
     * @apiParam {integer}   document_type 法人证件类型：1.身份证；2.港澳通行证；3.台胞证；4.护照；
     * @apiParam {string}   document_number 证件号码
     * @apiParam {integer}   company_property 企业性质：1.初创企业、2.私企、3.国有企业、4.事业单位、5.外资、6.合资、7.上市公司
     * @apiParam {string} account_name 银行开户名
     * @apiParam {string} bank_name 开户支行名称
     * @apiParam {string} account_number 银行账号
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *     "meta": {
     *       "message": "",
     *       "status_code": 200
     *     },
     *      "data": {
     *          "id": 1,
     *          "company_name": "nihao",
     *          "company_size": 1,
     *          "company_web": "http://www.baidu.com",
     *          "province": 1,
     *          "city": 4,
     *          "area": 5,
     *          "address": "beijing",
     *          "contact_name": "lisna",
     *          "phone": 18629493221,
     *          "email": "qq@qq.com",
     *          "logo_image": [],
     *          "verify_status": 0, //审核状态
     *          "license_image": [],  //营业执照附件
     *          "position": "",     //职位
     *          "company_type": 0,  // 企业类型：1.普通；2.多证合一（不含社会统一信用代码）；3.多证合一（含社会统一信用代码）
     *          "company_type_value": "",
     *          "registration_number": "",  //注册号
     *          "legal_person": "",         //法人姓名
     *          "document_type": 0,        //法人证件类型：1.身份证；2.港澳通行证；3.台胞证；4.护照；
     *          "document_type_value": "",
     *          "document_number": "",     //证件号码
     *          "company_property": 0,     //企业性质：1.初创企业、2.私企、3.国有企业、4.事业单位、5.外资、6.合资、7.上市公司
     *          "company_property_value": ""
     *          "document_image":[],  //法人证件，
     *          "verify_summary": '',  // 审核备注
     *          'account_name' => '', // 银行开户名
     *          'bank_name' => ', // 银行分行名称
     *          'account_number' => '2342323232', // 银行卡号
     *      },
     *   }
     *  }
     */
    public function update(Request $request)
    {
        $rules = [
            'company_name' => 'max:50',
            'company_abbreviation' => 'max:50',
            'company_size' => 'integer',
            'company_web' => 'max:50',
            'province' => 'integer',
            'city' => 'integer',
            'area' => 'integer',
            'address' => 'max:50',
            'contact_name' => 'max:20',
            'email' => 'email',
            'position' => 'max:20',
            'company_type' => 'integer',
            'registration_number' => 'min:15|max:18',
            'legal_person' => 'max:20',
            'document_type' => 'integer',
            'document_number' => 'max:20',
            'company_property' => 'integer',
            'account_name' => 'max:30',
            'bank_name' => 'max:30',
            'account_number' => 'max:50',
        ];
        $all = $request->except(['token']);

        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        foreach ($all as $k => $v) {
            if (empty($v) && $v !== 0 && $v !== "0")
                unset($all[$k]);
        }

        // 判断是否修改需要审核的信息
        $verify = [
            'company_name',
            'document_type',
            'registration_number',
            'legal_person',
            'document_type',
            'document_number',
            'province',
            'city',
            'area',
            'position',
            'contact_name',
            'phone',
            'email',
            'account_name',
            'bank_name',
            'account_number',
        ];
        if (!empty(array_intersect($verify, array_keys($all)))) {
            $all['verify_status'] = 3;
        }

        $demand = DemandCompany::where('user_id', $this->auth_user_id)->first();
        if (!$demand) {
            $demand = DemandCompany::createCompany($this->auth_user);
        }
        $demand->update($all);
        if (!$demand) {
            return $this->response->array($this->apiError());
        }

        return $this->response->item($demand, new DemandCompanyTransformer)->setMeta($this->apiMeta());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @api {get} /demandCompany/saveTradeFair 修改交易会权限
     * @apiVersion 1.0.0
     * @apiName save tradeFair
     * @apiGroup demandCompany
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *     "meta":{
     *         "message": "Success",
     *         "status_code": 200
     *     }
     * }
     */
    public function saveTradeFair()
    {
        $user_id = $this->auth_user_id;
        //需求公司信息
        $demand_company = DemandCompany::where('user_id', $user_id)->first();
        if(!$demand_company){
            return $this->apiError('不是需求公司', 404);
        }
        $demand_company->is_trade_fair = 1;
        if($demand_company->save()){
            return $this->apiSuccess('Success', 200,$demand_company);
        }
        return $this->apiError('Error', 400);
    }

}
