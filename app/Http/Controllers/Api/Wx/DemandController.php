<?php

namespace App\Http\Controllers\Api\Wx;

use App\Http\WxTransformer\ItemTransformer;
use App\Http\WxTransformer\RecommendListTransformer;
use App\Models\DemandCompany;
use App\Models\DesignCompanyModel;
use App\Models\Item;
use App\Models\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Helper\Recommend;

class DemandController extends BaseController
{

    /**
     * @api {post} /wechat/demand/create 需求公司创建项目
     * @apiVersion 1.0.0
     * @apiName wechatDemand create
     * @apiGroup wechatDemandType
     *
     * @apiParam {string} token
     * @apiParam {integer} type 设计类型：1.产品设计；2.UI UX 设计；3. 平面设计 4.H5 5.包装设计 6.插画设计
     */
    public function create(Request $request)
    {
        $rules = [
            'type' => 'required|integer',
        ];

        $all = $request->only(['type']);

        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        if ($this->auth_user->type != 1) {
            return $this->response->array($this->apiError('error: not demand', 403));
        }

        try {
            $source = $request->header('source-type') ?? 0;
            $name = '';
            $item = Item::createItem($this->auth_user_id, $name, $source);
            if (!$item) {
                return $this->response->array($this->apiError());
            }

            // 需求公司信息是否认证
            $demand_company = $this->auth_user->demandCompany;
            if ($demand_company->verify_status == 1) {
                $all['company_name'] = $demand_company->company_name;
                $all['company_abbreviation'] = $demand_company->company_abbreviation;
                $all['company_size'] = $demand_company->company_size;
                $all['company_web'] = $demand_company->company_web;
                $all['company_province'] = $demand_company->province;
                $all['company_city'] = $demand_company->city;
                $all['company_area'] = $demand_company->area;
                $all['address'] = $demand_company->address;
                $all['contact_name'] = $demand_company->contact_name;
                $all['phone'] = $demand_company->phone;
                $all['email'] = $demand_company->email;
                $all['position'] = $demand_company->position;
            }
            $all['stage_status'] = 1;
            $all['from_app'] = 1;
            $all['design_types'] = json_encode([1]);

            $item->update($all);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->response->array($this->apiError('Error', 500));
        }
        dd($item);
        return $this->response->item($item, new ItemTransformer())->setMeta($this->apiMeta());

    }

    /**
     * @api {post} /wechat/demand/release 发布项目
     * @apiVersion 1.0.0
     * @apiName wechatDemand release
     * @apiGroup wechatDemandType
     *
     * @apiParam {integer} id 项目ID
     * @apiParam {string} name 项目名称
     * @apiParam {string} company_name 公司名称
     * @apiParam {string} contact_name 联系人
     * @apiParam {string} position 职位
     * @apiParam {string} phone 手机号
     * @apiParam {integer} design_cost 设计费用：1、1-5万；2、5-10万；3.10-20；4、20-30；5、30-50；6、50以上
     * @apiParam {string} token
     */
    public function release(Request $request)
    {
        $id = (int)$request->input('id');
        if (!$item = Item::find($id)) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if ($item->status != 1) {
            return $this->response->array($this->apiError('项目已发布', 403));
        }

        //验证是否是当前用户对应的项目
        if ($item->user_id !== $this->auth_user_id) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        $auth_user = $this->auth_user;
        if (!$auth_user) {
            return $this->response->array($this->apiError('not found!', 404));
        }

        $demand_company = $this->auth_user->demandCompany;
        if (!$demand_company) {
            return $this->response->array($this->apiError('not found demandCompany!', 404));
        }
        $item->design_cost = $request->input('design_cost') ?? 0;
        $item->company_name = $request->input('company_name') ?? '';
        $item->contact_name = $request->input('contact_name') ?? '';
        $item->position = $request->input('position') ?? '';
        $item->phone = $request->input('phone') ?? '';
        $item->name = $request->input('name') ?? '';
        $item->save();
        // 同步调用匹配方法
        $recommend = new Recommend($item);
        $recommend->handle();

        $demand_company = DemandCompany::find($auth_user->demand_company_id);
        if (!$demand_company || $demand_company->verify_status != 1) {
            $verify_status = 0;
        } else {
            $verify_status = 1;
        }

        return $this->response->array($this->apiSuccess('Success', 200, ['verify_status' => $verify_status]));
    }

    /**
     * @api {get} /wechat/demand/recommendList/{item_id} 需求公司当前项目获取推荐的设计公司
     * @apiVersion 1.0.0
     * @apiName wechatDemand recommendList
     * @apiGroup wechatDemandType
     *
     * @apiParam {string} token
     */
    public function recommendList($item_id)
    {
        if (!$item = Item::find($item_id)) {
            return $this->response->array($this->apiError('not found', 404));
        }

        //验证是否是当前用户对应的项目
        if ($item->user_id !== $this->auth_user_id) {
            return $this->response->array($this->apiError('拒绝操作', 403));
        }

        $recommend_arr = explode(',', $item->recommend);

        //如果推荐为空，则返回
        if (empty($recommend_arr)) {
            return $this->response->array($this->apiSuccess('Success', 200, []));
        }

        $design_company = DesignCompanyModel::whereIn('id', $recommend_arr)->get();

        return $this->response->collection($design_company, new RecommendListTransformer($item))->setMeta($this->apiMeta());
    }

    /**
     * @api {put} /wechat/demand/update 需求公司更改设计类型
     * @apiVersion 1.0.0
     * @apiName wechatDemand updateType
     * @apiGroup wechatDemandType
     *
     * @apiParam {string} token
     * @apiParam {integer} id 项目ID
     * @apiParam {integer} type 设计类型：1.产品设计；2.UI UX 设计；3. 平面设计 4.H5 5.包装设计 6.插画设计
     */
    public function update(Request $request)
    {
        $id = (int)$request->input('id');
        $type = (int)$request->input('type');
        if (!$item = Item::find($id)) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if ($item->status != 1) {
            return $this->response->array($this->apiError('项目已发布', 403));
        }

        //验证是否是当前用户对应的项目
        if ($item->user_id !== $this->auth_user_id) {
            return $this->response->array($this->apiError('不是当前用户创建的!', 403));
        }
        $auth_user = $this->auth_user;
        if (!$auth_user) {
            return $this->response->array($this->apiError('not found!', 404));
        }
        $item->type = $type;

        if($item->save()){
            return $this->response->item($item, new ItemTransformer())->setMeta($this->apiMeta());
        }

    }
}