<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\DesignCompanyOtherIndexOpenTransformer;
use App\Http\Transformer\DesignCompanyOtherIndexTransformer;
use App\Http\Transformer\DesignCompanyShowTransformer;
use App\Http\Transformer\DesignCompanyTransformer;
use App\Http\Transformer\DesignItemTransformer;
use App\Models\AssetModel;
use App\Models\DesignItemModel;
use App\Models\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\DesignCompanyModel;
use Symfony\Component\HttpKernel\Exception\HttpException;


class DesignCompanyController extends BaseController
{
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
     *          "good_field": "",
     *          "web": "",
     *          "company_profile": "",
     *          "design_type": "2",  1.产品策略；2.产品外观设计；3.结构设计；ux设计：1.app设计；2.网页设计；
     *          "establishment_time": "",
     *          "professional_advantage": "",
     *          "awards": "",
     *          "score": 0,
     *          "status": 1
     *          "is_recommend": 0,
     *          "verify_status": 0, // 审核状态：0.未审核；1.审核通过；2. 未通过审核，3.审核中
     *          "logo_image": ""，
     *          "license_image": ""，
     *          "unique_id": "58fdc5273db38"
     *          "verify_summary": '',  // 审核备注
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
        if (!$design) {
            $design = DesignCompanyModel::createDesign($user_id);
        }

        if (!$design) {
            return $this->response->array($this->apiError('没有找到', 404));
        }
        return $this->response->item($design, new DesignCompanyShowTransformer())->setMeta($this->apiMeta());
    }


    /**
     * @api {get} /designCompany/otherIndex/{id} 其它公司查看根据设计公司id查看信息
     * @apiVersion 1.0.0
     * @apiName designCompanyItem index
     * @apiGroup designCompanyItem
     *
     * @apiParam {string} token
     */
    public function otherIndex($id)
    {
        $design = DesignCompanyModel::where('id', $id)->first();
        if (!$design) {
            return $this->response->array($this->apiError('没有找到', 404));
        }

        // 此参数用来判断是否返回设计公司的联系方式
        $is_phone = true;
        if (($this->auth_user_id == null) || !$design->isRead($this->auth_user_id, $id)) {
//            return $this->response->array($this->apiSuccess('没有权限访问' , 403));
            $is_phone = false;
        }

        $items = DesignItemModel::where('user_id', $design->user_id)->get();


        $design_type_val = [];
        foreach ($items as $item) {
            $design_type_val[] = $item->design_type_val;
        }
        $design->design_type_val = $design_type_val;

        if ($is_phone) {
            return $this->response->item($design, new DesignCompanyOtherIndexTransformer())->setMeta($this->apiMeta());
        } else {
            return $this->response->item($design, new DesignCompanyOtherIndexOpenTransformer())->setMeta($this->apiMeta());
        }


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
     * @apiParam {string} good_field 擅长领域
     * @apiParam {string} web 公司网站
     * @apiParam {string} company_profile 公司简介
     * @apiParam {string} professional_advantage 专业优势
     * @apiParam {string} awards 荣誉奖项
     * @apiParam {string} legal_person 法人
     * @apiParam {string} document_number 证件号码
     * @apiParam {integer} document_type 证件类型：1.身份证；2.港澳通行证；3.台胞证；4.护照
     * @apiParam {string} company_english 公司英文名
     * @apiParam {integer} revenue 公司营收 1.100万以下 2.100-500万 3.500-1000万 4.1000-2000万 5.3000-5000万 6.5000万以上
     * @apiParam {string} weixin_id 微信公众号ID
     * @apiParam {json} high_tech_enterprises 高新企业：1.市级；2.省级；3.国家级 [{'time': '2018-1-1','type': 1}]
     * @apiParam {json} industrial_design_center 工业设计中心：1.市级；2.省级；3.国家级 [{'time': '2018-1-1','type': 1}]
     * @apiParam {integer} investment_product 投资孵化产品 0.无；1.有；
     * @apiParam {json} own_brand 自有产品品牌 []
     * @apiParam {string} account_name 银行开户名
     * @apiParam {string} bank_name 开户支行名称
     * @apiParam {string} account_number 银行账号
     * @apiParam {int} taxable_type 纳税类型 1. 一般纳税人 2. 小额纳税人
     * @apiParam {int} invoice_type 发票类型 1. 专票 2. 普票
     *
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
     *          "good_field": "",
     *          "web": "",
     *          "company_profile": "",
     *          "establishment_time": "",
     *          "professional_advantage": "",
     *          "awards": "",
     *          "status": 0,
     *          "is_recommend": 0,
     *          "verify_status": 0,  // 设计公司审核信息 审核状态：0.未审核；1.审核通过；2. 未通过审核，3.审核中
     *          "logo_image": ""，
     *          "license_image": ""，
     *          "unique_id": "58fdc5273db38"
     *          "verify_summary": '',  // 审核备注
     *          "company_english": "english name",    // 公司英文名
     *          "revenue": "1",                      // 公司营收 1.100万以下 2.100-500万 3.500-1000万 4.1000-2000万 5.3000-5000万 6.5000万以上
     *          "revenue_value": "100万以下",         // 公司营收 1.100万以下 2.100-500万 3.500-1000万 4.1000-2000万 5.3000-5000万 6.5000万以上
     *          "weixin_id": "weixinongzhonghao",       // 微信公众号ID
     *          "high_tech_enterprises": [              // 高新企业：1.市级；2.省级；3.国家级
     *          {
     *              "time": "2018-1-1",
     *              "type": 1
     *          }
     *          ],
     *          "industrial_design_center": [           // 工业设计中心：1.市级；2.省级；3.国家级
     *          {
     *          "time": "2018-1-1",
     *          "type": 1
     *          }
     *          ],
     *          "investment_product": "1",              // 投资孵化产品 0.无；1.有
     *          "own_brand": [                          // 自有产品品牌
     *          "se"
     *          ],
     *          'account_name' => '', // 银行开户名
     *          'bank_name' => ', // 银行分行名称
     *          'account_number' => '2342323232', // 银行卡号
     *          'taxable_type' => 1, // 纳税类型 1. 一般纳税人 2. 小额纳税人
     *          'invoice_type' => 1,  // 发票类型 1. 专票 2. 普票
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
            'design_type' => 'nullable|max:50',
            'company_name' => 'nullable|max:50',
            'company_abbreviation' => 'nullable|max:50',
            'province' => 'nullable|integer',
            'city' => 'integer',
            'area' => 'integer',
            'address' => 'nullable|max:50',
            'contact_name' => 'nullable|max:20',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'company_size' => 'nullable|integer',
            'branch_office' => 'nullable|integer',
            'position' => 'nullable',
            'web' => 'nullable|max:50',
            'company_profile' => 'nullable|max:500',
            'establishment_time' => 'nullable|date',
            'good_field' => 'nullable|max:50',
            'professional_advantage' => 'nullable|max:500',
            'awards' => 'nullable|max:500',
            'registration_number' => 'nullable|min:15|max:18',
            'legal_person' => 'nullable|max:20',
            'document_type' => 'nullable|integer',
            'document_number' => 'nullable|max:20',
            'account_name' => 'max:30',
            'bank_name' => 'max:30',
            'account_number' => 'max:50',
            'taxable_type' => 'nullable|integer|in:1,2',
            'invoice_type' => 'nullable|integer|in:1,2',
        ];

        $messages = [
            'design_type.max' => '产品设计不能超过50个字',
            'company_abbreviation.max' => '公司简称不能超过50个字',
            'company_name.max' => '公司名称不能超过50个字',
            'province.integer' => '省份必须为整形',
            'address.max' => '详细地址不能超过50个字',
            'contact_name.max' => '联系人姓名不能超过20个字',
            'email.email' => '邮箱格式不正确',
            'company_size.integer' => '公司规模必须是整形',
            'branch_office.integer' => '分公司必须是整形',
            'web.max' => '公司网站不能超过50个字',
            'good_field.max' => '擅长领域不能超过50个字',
            'professional_advantage.max' => '专业优势不能超过500个字',
            'awards.max' => '荣誉奖项不能超过500个字',
            'company_profile.max' => '公司简介不能超过500个字',
            'registration_number.min' => '注册号不能低于15字符',
            'registration_number.max' => '注册号不能超过18字符',
            'legal_person.max' => '法人不能超过20个字符',
            'document_type.integer' => '证件类型必须是整形',
            'document_number.max' => '证件号码不能超过20个字符',

        ];
        $all = $request->except(['token']);
        $validator = Validator::make($all, $rules, $messages);

        if (isset($all['high_tech_enterprises']) && !$this->isPrizes($all['high_tech_enterprises'])) {
            return $this->response->array($this->apiError('高新企业high_tech_enterprises数据格式不正确', 403));
        }
        if (isset($all['industrial_design_center']) && !$this->isPrizes($all['industrial_design_center'])) {
            return $this->response->array($this->apiError('工业设计中心industrial_design_center数据格式不正确', 403));
        }

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        //擅长领域合并成字符串
        $goodField = $request->input('good_field');
        if ($goodField) {
            $data = [];
            foreach ($goodField as $v) {
                if ((int)$v) {
                    $data[] = (int)$v;
                }
            }
            if (!empty($data)) {
                //合并擅长领域
                $good_field = implode(',', $data);
                $all['good_field'] = $good_field;
            }
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
            'taxable_type',
            'invoice_type',
        ];
        if (!empty(array_intersect($verify, array_keys($all)))) {
            $all['verify_status'] = 3;
        }

        $design = DesignCompanyModel::where(['user_id' => $user_id])->first();
        if (!$design) {
            $design = DesignCompanyModel::createDesign($user_id);
        }

        $design->update($all);
        if (!$design) {
            return $this->response->array($this->apiError());
        }
        return $this->response->item($design, new DesignCompanyTransformer())->setMeta($this->apiMeta());
    }

    protected function isPrizes($value)
    {
        $data = json_decode($value, true);
        if (empty($data) || (isset($data[0]) && $data[0]['time'] && $data[0]['type'])) {
            return true;
        }

        return false;
    }


    /**
     * @api {get} /designCompany/child  子设计公司展示
     * @apiVersion 1.0.0
     * @apiName designCompany childShow
     * @apiGroup designCompany
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     * "data": {
     * "id": 49,
     * "user_id": 1,
     * "company_type": 1,
     * "company_type_val": "普通",
     * "company_name": "1",
     * "company_abbreviation": "1",
     * "registration_number": "111111111111111111",
     * "province": 1,
     * "city": 1,
     * "area": 1,
     * "province_value": "",
     * "city_value": "",
     * "area_value": "",
     * "address": "1",
     * "contact_name": "1",
     * "position": "1",
     * "phone": "11",
     * "email": "11@qq.com",
     * "company_size": 1,
     * "company_size_val": "20人以下",
     * "branch_office": 1,
     * "good_field": [],
     * "good_field_value": [],
     * "web": "1",
     * "company_profile": "1",
     * "design_type": "",
     * "establishment_time": "1991-01-20",
     * "professional_advantage": "1",
     * "awards": "1",
     * "score": 610,
     * "status": 1,
     * "is_recommend": 0,
     * "verify_status": 1,
     * "logo": 0,
     * "logo_image": null,
     * "license_image": [],
     * "design_type_val": null,
     * "unique_id": "59268453f207a",
     * "city_arr": [
     * "",
     * ""
     * ],
     * "company_english": "",
     * "revenue": null,
     * "revenue_value": null,
     * "weixin_id": "",
     * "high_tech_enterprises": null,
     * "industrial_design_center": null,
     * "investment_product": null,
     * "own_brand": null
     * },
     * "meta": {
     * "message": "Success",
     * "status_code": 200
     * }
     * }
     */
    public function childShow()
    {
        $user_id = intval($this->auth_user_id);
        $user = User::where('id', $user_id)->first();
        if (!$user) {
            return $this->response->array($this->apiError('没有找到该用户', 404));
        }
        $design_company_id = $user->design_company_id;
        $design = DesignCompanyModel::where('id', $design_company_id)->first();
        if (!$design) {
            return $this->response->array($this->apiError('没有找到设计公司', 404));
        }
        return $this->response->item($design, new DesignCompanyOtherIndexTransformer())->setMeta($this->apiMeta());

    }
}
