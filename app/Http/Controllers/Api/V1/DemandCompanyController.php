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
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

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
            throw new HttpException('Error');
        }

        return $this->response->item($demand, new DemandCompanyTransformer)->setMeta($this->apiMeta());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
