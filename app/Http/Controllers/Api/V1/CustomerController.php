<?php

/**
 * 客户接口
 */
namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\CustomerTransformer;
use App\Models\Customer;
use App\Models\DesignCompanyModel;
use App\Models\User;
use Dingo\Api\Contract\Http\Request;
use Illuminate\Support\Facades\Validator;
use Dingo\Api\Exception\StoreResourceFailedException;


class CustomerController extends BaseController
{

    /**
     * @api {post} /customers 创建
     * @apiVersion 1.0.0
     * @apiName  customers store
     * @apiGroup customers
     * @apiParam {string} company_name 公司名称
     * @apiParam {string} address 详细地址
     * @apiParam {string} contact_name 联系人姓名
     * @apiParam {string} phone 手机
     * @apiParam {string} position 职位
     * @apiParam {string} summary 备注
     * @apiParam {integer} status 状态
     * @apiParam {integer} sort 排序
     * @apiParam {integer} province 省份
     * @apiParam {integer} city 城市
     * @apiParam {integer} area 地区
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
        {
            "data": {
                "id": 1,
                "company_name": "test公司名称",
                "address": "公司地址",
                "contact_name": "联系人",
                "position": "",
                "phone": "15810295774",
                "summary": "",
                "design_company_id": 2,
                "province": 0,
                "city": 0,
                "area": 0,
                "status": 0,
                "sort": 0,
                "created_at": 1520491609
            },
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    public function store(Request $request)
    {
        // 验证规则
        $rules = [
            'company_name' => 'required|max:50',
            'address' => 'required|max:50',
            'contact_name' => 'required|max:20',
            'phone' => ['required','regex:/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/'],
        ];
        $messages = [
            'company_name.required' => '公司名称不能为空',
            'company_name.max' => '公司名称最多50字符',
            'address.required' => '详细地址不能为空',
            'address.max' => '详细地址最多50字符',
            'contact_name.required' => '联系人姓名不能为空',
            'contact_name.max' => '联系人姓名最多20字符',

        ];

        $summary = $request->input('summary') ? (int)$request->input('summary') : '';
        $position = $request->input('position') ? (int)$request->input('position') : '';
        $province = $request->input('province') ? (int)$request->input('province') : 0;
        $city = $request->input('city') ? (int)$request->input('city') : 0;
        $area = $request->input('area') ? (int)$request->input('area') : 0;

        $params = array(
            'company_name' => $request->input('company_name'),
            'address' => $request->input('address'),
            'contact_name' => $request->input('contact_name'),
            'phone' => $request->input('phone'),
            'position' => $position,
            'province' => $province,
            'city' => $city,
            'area' => $area,
            'summary' => $summary,
            'user_id' => $this->auth_user_id
        );

        $validator = Validator::make($params, $rules, $messages);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }
        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);
        $params['design_company_id'] = $design_company_id;

        try{
            $bank = Customer::create($params);
        }
        catch (\Exception $e){
            return $this->response->array($this->apiError());
        }

        return $this->response->item($bank, new CustomerTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /customers  客户列表
     * @apiVersion 1.0.0
     * @apiName customers index
     * @apiGroup customers
     *
     * @apiParam {string} token
     * @apiSuccessExample 成功响应:
        {
            "data": {
                "id": 1,
                "company_name": "test公司名称",
                "address": "公司地址",
                "contact_name": "联系人",
                "position": "",
                "phone": "15810295774",
                "summary": "",
                "design_company_id": 2,
                "province": 0,
                "city": 0,
                "area": 0,
                "status": 0,
                "sort": 0,
                "created_at": 1520491609
            },
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    public function index()
    {
        $user_id = $this->auth_user_id;
        $design_company_id = User::designCompanyId($user_id);

        $customers = Customer::where('design_company_id' , $design_company_id)->orderBy('id', 'desc')->get();
        return $this->response->collection($customers, new CustomerTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /customers/detection  检测客户是否创建
     * @apiVersion 1.0.0
     * @apiName customers detection
     * @apiGroup customers
     *
     * @apiParam {string} company_name 客户企业名称
     * @apiParam {string} token
     */
    public function detection(Request $request)
    {
        $company_name = $request->input('company_name');
        $customers = Customer::where('company_name' , $company_name)->first();
        $data = ['summary'=>'可以创建'];
        if(!$customers){
            return $this->response()->array($this->apiSuccess('Success', 200 , $data));
        }else{
            return $this->response()->array($this->apiError('Error', 400));
        }

    }

    /**
     * @api {get} /customers/{id}  客户详情
     * @apiVersion 1.0.0
     * @apiName customers show
     * @apiGroup customers
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
        {
            "data": {
                "id": 1,
                "company_name": "test公司名称",
                "address": "公司地址",
                "contact_name": "联系人",
                "position": "",
                "phone": "15810295774",
                "summary": "",
                "design_company_id": 2,
                "province": 0,
                "city": 0,
                "area": 0,
                "status": 0,
                "sort": 0,
                "created_at": 1520491609
            },
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    public function show($id)
    {
        $customers = Customer::where('id' , $id)->first();
        if(!$customers){
            return $this->response()->array($this->apiError('没有找到该客户', 404));
        }else{
            return $this->response->item($customers, new CustomerTransformer())->setMeta($this->apiMeta());
        }

    }


    /**
     * @api {put} /customers/{id} 更改
     * @apiVersion 1.0.0
     * @apiName  customers update
     * @apiGroup customers
     * @apiParam {string} company_name 公司名称
     * @apiParam {string} address 详细地址
     * @apiParam {string} contact_name 联系人姓名
     * @apiParam {string} phone 手机
     * @apiParam {string} position 职位
     * @apiParam {string} summary 备注
     * @apiParam {integer} status 状态
     * @apiParam {integer} sort 排序
     * @apiParam {integer} province 省份
     * @apiParam {integer} city 城市
     * @apiParam {integer} area 地区
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
        {
            "data": {
                "id": 1,
                "company_name": "test公司名称",
                "address": "公司地址",
                "contact_name": "联系人",
                "position": "",
                "phone": "15810295774",
                "summary": "",
                "design_company_id": 2,
                "province": 0,
                "city": 0,
                "area": 0,
                "status": 0,
                "sort": 0,
                "created_at": 1520491609
            },
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    public function update(Request $request , $id)
    {
        // 验证规则
        $rules = [
            'company_name' => 'required|max:50',
            'address' => 'required|max:50',
            'contact_name' => 'required|max:20',
            'phone' => ['required','regex:/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/'],
        ];
        $messages = [
            'company_name.required' => '公司名称不能为空',
            'company_name.max' => '公司名称最多50字符',
            'address.required' => '详细地址不能为空',
            'address.max' => '详细地址最多50字符',
            'contact_name.required' => '联系人姓名不能为空',
            'contact_name.max' => '联系人姓名最多20字符',

        ];

        $summary = $request->input('summary') ? (int)$request->input('summary') : '';
        $position = $request->input('position') ? (int)$request->input('position') : '';
        $province = $request->input('province') ? (int)$request->input('province') : 0;
        $city = $request->input('city') ? (int)$request->input('city') : 0;
        $area = $request->input('area') ? (int)$request->input('area') : 0;

        $params = array(
            'company_name' => $request->input('company_name'),
            'address' => $request->input('address'),
            'contact_name' => $request->input('contact_name'),
            'phone' => $request->input('phone'),
            'position' => $position,
            'province' => $province,
            'city' => $city,
            'area' => $area,
            'summary' => $summary,
        );

        $validator = Validator::make($params, $rules, $messages);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        //检验是否有该客户
        $customers = Customer::find($id);
        if (!$customers) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        //检验是否是当前用户创建的客户
        if ($customers->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('不能更改!', 403));
        }

        $customers->update($params);
        if (!$customers) {
            return $this->response->array($this->apiError());
        }
        return $this->response->item($customers, new CustomerTransformer())->setMeta($this->apiMeta());
    }
}