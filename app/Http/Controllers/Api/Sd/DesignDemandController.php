<?php
/**
 * 设计需求控制器
 *
 * @User yht
 * @time 2018-10-22
 */

namespace App\Http\Controllers\Api\Sd;

use Illuminate\Http\Request;
use Dingo\Api\Exception\StoreResourceFailedException;
use App\Models\DemandCompany;
use App\Models\DesignCompanyModel;
use App\Models\DesignDemand;

class DesignDemandController extends BaseController
{
    /**
     * @api {get} /sd/demand/demandList 需求列表
     * @apiVersion 1.0.0
     * @apiName sdDemand demandList
     * @apiGroup sdDemandType
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *       "data": [
     *          {
     * "id": 1,                    //需求ID
     * "user_id": 12,              //用户ID
     * "demand_company_id": 1,     //需求公司ID
     * "status": 0,                //状态：-1.关闭 0.待发布 1. 审核中 2.已发布
     * "type": 1,                  //设计类型：1.产品设计；2.UI UX 设计；3. 平面设计 4.H5 5.包装设计 6.插画设计
     * "design_types": "\"[1,2]\"",//设计类别：产品设计（1.产品策略；2.产品设计；3.结构设计 4.其他；[1,2]
     * "name": "测试1",            //项目名称
     * "cycle": 1,                 //设计周期：1.1个月内；2.1-2个月；3.2-3个月；4.3-4个月；5.4个月以上
     * "design_cost": 1,           //设计费用：1、1-5万；2、5-10万；3.10-20；4、20-30；5、30-50；6、50以上
     * "follow_count": 0,          //关注数量
     * "created_at": 1540281300,
     * "updated_at": 1540281300
     * }
     *       ]
     *  }
     */
    public function demandList(Request $request)
    {
        // 用户ID
        $user_id = $this->auth_user_id;
        // 需求公司ID
        $demand_company_id = $this->auth_user->demand_company_id;

        if ($this->auth_user->type != 1 || !$demand_company_id) {
            return $this->response->array($this->apiError('此用户不是需求公司', 403));
        }
        $demand_company = DemandCompany::where('id', $demand_company_id)->first();
        if (!$demand_company->isVerify()) {
            return $this->response->array($this->apiError('需求公司没有认证', 403));
        }
        // 获取需求列表
        $design_demand = DesignDemand::getDemandList($user_id, $demand_company_id);
        return $this->response->array($this->apiSuccess('Success', 200, $design_demand));
    }

    /**
     * @api {post} /sd/demand/release 发布需求
     * @apiVersion 1.0.0
     * @apiName sdDemand release
     * @apiGroup sdDemandType
     *
     * @apiParam {string} token
     * @apiParam {string} name  项目名称
     * @apiParam {json} design_types 设计类型：1.产品策略；2.产品设计；3.结构设计 4.其他；[1,2]
     * @apiParam {integer} cycle 设计周期：1.1个月内；2.1-2个月；3.2-3个月；4.3-4个月；5.4个月以上
     * @apiParam {integer} design_cost 设计费用：1、1-5万；2、5-10万；3.10-20；4、20-30；5、30-50；6、50以上
     * @apiParam {integer} field 产品类型：1、1-5万；2、5-10万；3.10-20；4、20-30；5、30-50；6、50以上
     * @apiParam {integer} industry 所属行业 1.制造业,2 .消费零售,3 .信息技术,4 .能源,5 .金融地产,6 .服务业,7 .医疗保健,8 .原材料,9 .工业制品,10 .军工,11 .公用事业
     * @apiParam {integer} item_province 省份
     * @apiParam {integer} item_city 城市
     * @apiParam {string} content 产品描述
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function release(Request $request)
    {
        $rules = ['name' => 'required|string|max:100', 'design_types' => 'JSON', 'cycle' => 'required|integer|regex:/[1-5]/', 'design_cost' => 'required|integer|regex:/[1-6]/', 'field' => 'required|integer|regex:/[1-6]/', 'industry' => 'required|integer|regex:/[1-11]/', 'content' => 'required|string',];
        $payload = $request->only('name', 'design_types', 'cycle', 'design_cost', 'field', 'industry', 'content');
        $validator = app('validator')->make($payload, $rules);

        // 验证格式
        if ($validator->fails()) {
            throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
        }

        // 获取数据
        $all = $request->all();
        // 需求公司ID
        $demand_company_id = $this->auth_user->demand_company_id;

        if ($this->auth_user->type != 1) {
            return $this->response->array($this->apiError('此用户不是需求公司', 403));
        }

        $demand_company = DemandCompany::where('id', $demand_company_id)->first();
        if (!$demand_company->isVerify()) {
            return $this->response->array($this->apiError('需求公司没有认证', 403));
        }

        $demand_name = DesignDemand::where(['user_id' => $this->auth_user_id, 'demand_company_id' => $this->auth_user->demand_company_id, 'name' => $all['name']])->first();
        if ($demand_name) {
            return $this->response->array($this->apiError('项目名称已存在', 400));
        }
        $design_demand = new DesignDemand;
        $design_demand->name = $all['name'];
        $design_demand->user_id = $this->auth_user_id;
        $design_demand->demand_company_id = $this->auth_user->demand_company_id;
        $design_demand->status = 1;
        $design_demand->type = 1;
        $design_demand->design_types = $all['design_types'];
        $design_demand->cycle = $all['cycle'];
        $design_demand->design_cost = $all['design_cost'];
        $design_demand->field = $all['field'];
        $design_demand->industry = $all['industry'];
        $design_demand->item_province = $all['item_province'];
        $design_demand->item_city = $all['item_city'];
        $design_demand->content = $all['content'];
        if ($design_demand->save()) {
            return $this->response->array($this->apiSuccess('Success', 200));
        }
    }

    /**
     * @api {get} /sd/demand/demandInfo 查看某个需求详情
     * @apiVersion 1.0.0
     * @apiName sdDemand demandInfo
     * @apiGroup sdDemandType
     *
     * @apiParam {string} token
     * @apiParam {integer} demand_id //需求ID
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      },
     *       "data": [
     *          {
     *              "id": 1,                    //需求ID
     *              "user_id": 12,              //用户ID
     *              "demand_company_id": 1,     //需求公司ID
     *              "status": 0,                //状态：-1.关闭 0.待发布 1. 审核中 2.已发布
     *              "type": 1,                  //设计类型：1.产品设计；2.UI UX 设计；3. 平面设计 4.H5 5.包装设计 6.插画设计
     *              "design_types": "\"[1,2]\"",//设计类别：产品设计（1.产品策略；2.产品设计；3.结构设计 4.其他；[1,2]
     *              "name": "测试1",            //项目名称
     *              "cycle": 1,                 //设计周期：1.1个月内；2.1-2个月；3.2-3个月；4.3-4个月；5.4个月以上
     *              "design_cost": 1,           //设计费用：1、1-5万；2、5-10万；3.10-20；4、20-30；5、30-50；6、50以上
     *              "item_province": 1,         //省份
     *              "item_city": 1,             //城市
     *              "field": 1,                 //产品类别：'智能硬件','消费电子'等
     *              "industry": 1,              //所属行业 1.制造业,2 .消费零售,3 .信息技术,4 .能源,5 .金融地产,6 .服务业,7 .医疗保健,8 .原材料,9 .工业制品,10 .军工,11 .公用事业
     *              "content": "测试1",         //需求描述
     *              "follow_count": 0,          //关注数量
     *              "created_at": 1540281300,
     *              "updated_at": 1540281300
     *          }
     *       ]
     *  }
     */
    public function demandInfo(Request $request)
    {
        $rules = ['demand_id' => 'required|integer',];

        $payload = $request->only('demand_id');
        $validator = app('validator')->make($payload, $rules);

        // 验证格式
        if ($validator->fails()) {
            throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
        }

        // 获取需求ID
        $demand_id = $request->input('demand_id');
        // 需求公司ID
        $demand_company_id = $this->auth_user->demand_company_id;

        if ($this->auth_user->type != 1) {
            return $this->response->array($this->apiError('此用户不是需求公司', 403));
        }

        $demand_company = DemandCompany::where('id', $demand_company_id)->first();
        if (!$demand_company->isVerify()) {
            return $this->response->array($this->apiError('需求公司没有认证', 403));
        }

        $demand_info = DesignDemand::where(['id'=>$demand_id,'demand_company_id'=>$demand_company_id])->first();
        if (!$demand_info) {
            return $this->response->array($this->apiError('没有找到该需求', 404));
        }
        return $this->response->array($this->apiSuccess('Success', 200, $demand_info));
    }

    /**
     * @api {post} /sd/demand/demandShut 关闭某个需求
     * @apiVersion 1.0.0
     * @apiName sdDemand demandShut
     * @apiGroup sdDemandType
     *
     * @apiParam {string} token
     * @apiParam {integer} demand_id //需求ID
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function demandShut(Request $request)
    {
        $rules = ['demand_id' => 'required|integer',];

        $payload = $request->only('demand_id');
        $validator = app('validator')->make($payload, $rules);

        // 验证格式
        if ($validator->fails()) {
            throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
        }

        // 获取需求ID
        $demand_id = $request->input('demand_id');
        // 需求公司ID
        $demand_company_id = $this->auth_user->demand_company_id;

        if ($this->auth_user->type != 1 || !$demand_company_id) {
            return $this->response->array($this->apiError('此用户不是需求公司', 403));
        }

        $demand_company = DemandCompany::where('id', $demand_company_id)->first();
        if (!$demand_company->isVerify()) {
            return $this->response->array($this->apiError('需求公司没有认证', 403));
        }

        $demand = DesignDemand::where(['id'=>$demand_id,'demand_company_id'=>$demand_company_id])->first();
        if (!$demand) {
            return $this->response->array($this->apiError('没有找到该需求', 404));
        } else {
            if ($demand->status == 1) {
                return $this->response->array($this->apiError('审核中无法关闭', 403));
            }
            DesignDemand::where('id', $demand_id)->delete();
            return $this->response->array($this->apiSuccess('Success', 200));
        }
    }

    /**
     * @api {post} /sd/demand/demandUpdate 更改需求
     * @apiVersion 1.0.0
     * @apiName sdDemand demandUpdate
     * @apiGroup sdDemandType
     *
     * @apiParam {string} token
     * @apiParam {integer} demand_id //需求ID
     * @apiParam {string} name  项目名称
     * @apiParam {json} design_types 设计类型：1.产品策略；2.产品设计；3.结构设计 4.其他；[1,2]
     * @apiParam {integer} cycle 设计周期：1.1个月内；2.1-2个月；3.2-3个月；4.3-4个月；5.4个月以上
     * @apiParam {integer} design_cost 设计费用：1、1-5万；2、5-10万；3.10-20；4、20-30；5、30-50；6、50以上
     * @apiParam {integer} field 产品类型：1、1-5万；2、5-10万；3.10-20；4、20-30；5、30-50；6、50以上
     * @apiParam {integer} industry 所属行业 1.制造业,2 .消费零售,3 .信息技术,4 .能源,5 .金融地产,6 .服务业,7 .医疗保健,8 .原材料,9 .工业制品,10 .军工,11 .公用事业
     * @apiParam {integer} item_province 省份
     * @apiParam {integer} item_city 城市
     * @apiParam {string} content 产品描述
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function demandUpdate(Request $request)
    {
        $rules = ['name' => 'required|string|max:100', 'design_types' => 'JSON', 'cycle' => 'required|integer|regex:/[1-5]/', 'design_cost' => 'required|integer|regex:/[1-6]/', 'field' => 'required|integer|regex:/[1-6]/', 'industry' => 'required|integer|regex:/[1-11]/', 'content' => 'required|string',];
        $payload = $request->only('name', 'design_types', 'cycle', 'design_cost', 'field', 'industry', 'content');
        $validator = app('validator')->make($payload, $rules);

        // 验证格式
        if ($validator->fails()) {
            throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
        }

        // 获取数据
        $all = $request->all();
        // 需求公司ID
        $demand_company_id = $this->auth_user->demand_company_id;

        if ($this->auth_user->type != 1 || !$demand_company_id) {
            return $this->response->array($this->apiError('此用户不是需求公司', 403));
        }

        $demand_company = DemandCompany::where('id', $demand_company_id)->first();
        if (!$demand_company->isVerify()) {
            return $this->response->array($this->apiError('需求公司没有认证', 403));
        }

        $demand = DesignDemand::where(['id'=>$all['demand_id'],'demand_company_id'=>$demand_company_id])->first();
        if (!$demand) {
            return $this->response->array($this->apiError('没有找到该项目', 404));
        }
        // 判断状态
        if ($demand->status != -1) {
            return $this->response->array($this->apiError('不是未通过状态无法编辑', 403));
        }
        $demand->name = $all['name'];
        $demand->design_types = $all['design_types'];
        $demand->cycle = $all['cycle'];
        $demand->status = 1;
        $demand->design_cost = $all['design_cost'];
        $demand->field = $all['field'];
        $demand->industry = $all['industry'];
        $demand->item_province = $all['item_province'];
        $demand->item_city = $all['item_city'];
        $demand->content = $all['content'];
        if ($demand->save()) {
            return $this->response->array($this->apiSuccess('Success', 200));
        }
    }

    /**
     * @api {get} /sd/demand/designDemandList 设计公司查看需求列表
     * @apiVersion 1.0.0
     * @apiName sdDemand designDemandList
     * @apiGroup sdDemandType
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      },
     *       "data": [
     *          {
     *              "id": 1,                    //需求ID
     *              "user_id": 12,              //用户ID
     *              "demand_company_id": 1,     //需求公司ID
     *              "design_types": "\"[1,2]\"",//设计类别：产品设计（1.产品策略；2.产品设计；3.结构设计 4.其他；[1,2]
     *              "name": "测试1",            //项目名称
     *              "cycle": 1,                 //设计周期：1.1个月内；2.1-2个月；3.2-3个月；4.3-4个月；5.4个月以上
     *              "design_cost": 1,           //设计费用：1、1-5万；2、5-10万；3.10-20；4、20-30；5、30-50；6、50以上
     *              "created_at": 1540281300,
     *              "updated_at": 1540281300
     *          }
     *       ]
     *  }
     */
    public function designDemandList(Request $request)
    {
        // 设计公司ID
        $design_company_id = $this->auth_user->design_company_id;;
        if ($this->auth_user->type != 2 || !$design_company_id) {
            return $this->response->array($this->apiError('此用户不是设计公司', 403));
        }

        $design_company = DesignCompanyModel::where('id',$design_company_id)->first();
        if(!$design_company->isVerify()){
            return $this->response->array($this->apiError('设计公司没有认证', 403));
        }

        // 设计公司获取需求列表
        $design_demand = DesignDemand::getDesignObtainDemand();
        return $this->response->array($this->apiSuccess('Success', 200, $design_demand));
    }

    /**
     * @api {get} /sd/demand/designDemandInfo 设计公司查看某个需求详情
     * @apiVersion 1.0.0
     * @apiName sdDemand designDemandInfo
     * @apiGroup sdDemandType
     *
     * @apiParam {string} token
     * @apiParam {integer} demand_id
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      },
     *       "data": [
     *          {
     *              "id": 1,                    //需求ID
     *              "user_id": 12,              //用户ID
     *              "demand_company_id": 1,     //需求公司ID
     *              "status": 0,                //状态：-1.关闭 0.待发布 1. 审核中 2.已发布
     *              "type": 1,                  //设计类型：1.产品设计；2.UI UX 设计；3. 平面设计 4.H5 5.包装设计 6.插画设计
     *              "design_types": "\"[1,2]\"",//设计类别：产品设计（1.产品策略；2.产品设计；3.结构设计 4.其他；[1,2]
     *              "name": "测试1",            //项目名称
     *              "cycle": 1,                 //设计周期：1.1个月内；2.1-2个月；3.2-3个月；4.3-4个月；5.4个月以上
     *              "design_cost": 1,           //设计费用：1、1-5万；2、5-10万；3.10-20；4、20-30；5、30-50；6、50以上
     *              "item_province": 1,         //省份
     *              "item_city": 1,             //城市
     *              "field": 1,                 //产品类别：'智能硬件','消费电子'等
     *              "industry": 1,              //所属行业 1.制造业,2 .消费零售,3 .信息技术,4 .能源,5 .金融地产,6 .服务业,7 .医疗保健,8 .原材料,9 .工业制品,10 .军工,11 .公用事业
     *              "content": "测试1",         //需求描述
     *              "follow_count": 0,          //关注数量
     *              "created_at": 1540281300,
     *              "updated_at": 1540281300
     *          }
     *       ]
     *  }
     */
    public function designDemandInfo(Request $request)
    {
        $rules = ['demand_id' => 'required|integer',];

        $payload = $request->only('demand_id');
        $validator = app('validator')->make($payload, $rules);

        // 需求ID
        $demand_id = $request->input('demand_id');
        // 设计公司ID
        $design_company_id = $this->auth_user->design_company_id;;

        // 验证格式
        if ($validator->fails()) {
            throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
        }

        if ($this->auth_user->type != 2 || !$design_company_id) {
            return $this->response->array($this->apiError('此用户不是设计公司', 403));
        }

        $design_company = DesignCompanyModel::where('id',$design_company_id)->first();
        if(!$design_company->isVerify()){
            return $this->response->array($this->apiError('设计公司没有认证', 403));
        }

        $demand_info = DesignDemand::where('id', $demand_id)->first();
        if (!$demand_info) {
            return $this->response->array($this->apiError('没有找到该需求', 404));
        }
        return $this->response->array($this->apiSuccess('Success', 200, $demand_info));
    }
}