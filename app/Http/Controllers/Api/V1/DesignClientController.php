<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\DesignClientTransformer;
use App\Models\DesignClient;
use App\Models\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DesignClientController extends Controller
{
    /**
     * @api {get} /designClient/lists 客户信息列表
     * @apiVersion 1.0.0
     * @apiName designClient lists
     * @apiGroup designClient
     *
     * @apiParam {int} per_page 页面条数
     * @apiParam {int} page 页数
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": [{
     *          "id": 2,
     *          "company_name": null,     // 客户名称
     *          "contact_name": null,       // 联系人姓名
     *          "position": null,    // 职位
     *          "phone": null,       // 手机
     *          "province": null,    // 省份
     *          "province_value": null,
     *          "city_value": null,  // 城市
     *          "area_value": null,  // 地区
     *          "address": null,    // 详细地址
     *          "user_id": 6
     *       },],
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function index(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $design_company_id = User::designCompanyId($this->auth_user_id);
        $lists = DesignClient::where(['design_company_id' => $design_company_id])->paginate($per_page);

        return $this->response->paginator($lists, new DesignClientTransformer())->setMeta($this->apiMeta());
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
     * @api {post} /designClient/create 创建客户信息
     * @apiVersion 1.0.0
     * @apiName designClient create
     * @apiGroup designClient
     *
     * @apiParam {string} company_name    varchar(100) 客户名称
     * @apiParam {string} contact_name    varchar(20) 联系人姓名
     * @apiParam {string} position  varchar(50) 职位
     * @apiParam {string} phone  varchar(20) 手机
     * @apiParam {int} province  省份
     * @apiParam {int} city  城市
     * @apiParam {int} area 地区
     * @apiParam {string} address varchar(100) 详细地址
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": {
     *          "id": 2,
     *          "company_name": null,     // 客户名称
     *          "contact_name": null,       // 联系人姓名
     *          "position": null,    // 职位
     *          "phone": null,       // 手机
     *          "province": null,    // 省份
     *          "province_value": null,
     *          "city_value": null,  // 城市
     *          "area_value": null,  // 地区
     *          "address": null,    // 详细地址
     *          "user_id": 6
     *       },
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function store(Request $request)
    {
        $rules = [
            'company_name' => 'required|max:100',
            'contact_name' => 'required|max:20',
            'position' => 'required|max:50',
            'phone' => 'required|max:20',
            'province' => 'required|integer',
            'city' => 'required|integer',
            'area' => 'required|integer',
            'address' => 'required|string|max:200'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('errors', $validator->errors());
        }

        $count = DesignClient::where('company_name', $request->input('company_name'))->count();
        if ($count > 0) {
            return $this->response->array($this->apiError('已存在', 403));
        }

        $all = $request->all();
        $all['design_company_id'] = User::designCompanyId($this->auth_user_id);
        $all['user_id'] = $this->auth_user_id;
        $design_client = DesignClient::create($all);

        return $this->response->item($design_client, new DesignClientTransformer())->setMeta($this->apiMeta());

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
     * @api {get} /designClient/search 搜索客户信息
     * @apiVersion 1.0.0
     * @apiName designClient search
     * @apiGroup designClient
     *
     * @apiParam {string} name 客户名称
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *       "data": [{
     *          "id": 2,
     *          "company_name": null,     // 客户名称
     *          "contact_name": null,       // 联系人姓名
     *          "position": null,    // 职位
     *          "phone": null,       // 手机
     *          "province": null,    // 省份
     *          "province_value": null,
     *          "city_value": null,  // 城市
     *          "area_value": null,  // 地区
     *          "address": null,    // 详细地址
     *          "user_id": 6
     *       },],
     *       "meta": {
     *           "message": "Success",
     *           "status_code": 200
     *       }
     *   }
     */
    public function search(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
        ]);

        $name = $request->input('name');
        $design_company_id = User::designCompanyId($this->auth_user_id);
        $design_clients = DesignClient::where('design_company_id', $design_company_id)
            ->where('company_name', 'like', "%$name%")
            ->get();

        return $this->response->collection($design_clients, new DesignClientTransformer())->setMeta($this->apiMeta());
    }
}
