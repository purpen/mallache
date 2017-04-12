<?php
/**
 * 需求公司基本信息控制器
 *
 * @User llh
 * @time 2017-3-31
 */
namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\DemandCompanyTransformer;
use App\Models\DemandCompany;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DemandCompanyController extends BaseController
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
     * @api {post} /demandCompany 保存需求用户信息
     * @apiVersion 1.0.0
     * @apiName demandCompany store
     * @apiGroup demandCompany
     *
     * @apiParam {string} company_name 公司名称
     * @apiParam {integer} company_size 公司规模
     * @apiParam {string} company_web 公司网站
     * @apiParam {integer} province 省份
     * @apiParam {integer} city 城市
     * @apiParam {integer} area 区域
     * @apiParam {string} address 详细地址
     * @apiParam {string} contact_name 联系人
     * @apiParam {integer} phone 手机
     * @apiParam {string} email 邮箱
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
        //表单验证
        $rules = [
            'company_name' => 'required|max:50',
            'company_size' => 'required|integer',
            'company_web' => 'required|max:50',
            'province' => 'required|integer',
            'city' => 'required|integer',
            'area' => 'required|integer',
            'address' => 'required|max:50',
            'contact_name' => 'required|max:20',
            'phone' => ['required', 'regex:/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/'],
            'email' => 'required|email',
        ];
        $all = $request->all();
        $all['user_id'] = $this->auth_user_id;
        $validator = Validator::make($all, $rules);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        try{
            $demand = DemandCompany::create($all);
        }
        catch (\Exception $e){
            return $this->response->array($this->apiError('Error', 500));
        }

        return $this->response->item($demand, new DemandCompanyTransformer)->setMeta($this->apiMeta());
    }

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
     *          "email": "qq@qq.com"
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
        if(!$demand){
            return $this->response->array($this->apiError());
        }

        return $this->response->item($demand, new DemandCompanyTransformer)->setMeta($this->apiMeta());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * @api {put} /demandCompany 更新需求用户信息
     * @apiVersion 1.0.0
     * @apiName demandCompany update
     * @apiGroup demandCompany
     *
     * @apiParam {string} company_name 公司名称
     * @apiParam {integer} company_size 公司规模
     * @apiParam {string} company_web 公司网站
     * @apiParam {integer} province 省份
     * @apiParam {integer} city 城市
     * @apiParam {integer} area 区域
     * @apiParam {string} address 详细地址
     * @apiParam {string} contact_name 联系人
     * @apiParam {integer} phone 手机
     * @apiParam {string} email 邮箱
     * @apiParam {string} token
     *
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
        $rules = [
            'company_name' => 'max:50',
            'company_size' => 'integer',
            'company_web' => 'max:50',
            'province' => 'integer',
            'city' => 'integer',
            'area' => 'integer',
            'address' => 'max:50',
            'contact_name' => 'max:20',
            'phone' => ['regex:/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/'],
            'email' => 'email',
        ];
        $all = $request->except(['token']);

        $validator = Validator::make($all, $rules);
        if($validator->fails()){
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        $demand = DemandCompany::where('user_id', $this->auth_user_id)->update($all);
        if(!$demand){
            return $this->response->array($this->apiError());
        }

        return $this->response->array($this->apiSuccess());
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
}
