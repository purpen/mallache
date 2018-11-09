<?php
/**
 * 设计需求控制器
 *
 * @User yht
 * @time 2018-10-22
 */

namespace App\Http\Controllers\Api\Sd;

use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Transformer\DesignDemandTransformer;
use App\Http\Transformer\DesignDemandListTransformer;
use Dingo\Api\Exception\StoreResourceFailedException;
use App\Models\DemandCompany;
use App\Models\DesignCompanyModel;
use App\Models\DesignDemand;
use App\Models\DesignResult;
use App\Models\ResultEvaluate;
use App\Models\PayOrder;

class DesignDemandController extends BaseController
{
    /**
     * @api {get} /sd/demand/demandList 需求列表
     * @author 于海涛
     * @apiVersion 1.0.0
     * @apiName sdDemand demandList
     * @apiGroup sdDemandType
     *
     * @apiParam {integer} per_page 分页数量
     * @apiParam {integer} page 页码
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
     *              "status": 0,                //状态：-1.关闭 0.待发布 1. 审核中 2.已发布
     *              "type": 1,                  //设计类型：1.产品设计；2.UI UX 设计；3. 平面设计 4.H5 5.包装设计 6.插画设计
     *              "design_types": "\"[1,2]\"",//设计类别：产品设计（1.产品策略；2.产品设计；3.结构设计 4.其他；[1,2]
     *              "name": "测试1",            //项目名称
     *              "cycle": 1,                 //设计周期：1.1个月内；2.1-2个月；3.2-3个月；4.3-4个月；5.4个月以上
     *              "design_cost": 1,           //设计费用：1、1-5万；2、5-10万；3.10-20；4、20-30；5、30-50；6、50以上
     *              "follow_count": 0,          //关注数量
     *              "created_at": 1540281300,
     *              "updated_at": 1540281300
     *          }
     *       ]
     *  }
     */
    public function demandList(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;

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
        $design_demand = DesignDemand::getDemandList($user_id, $demand_company_id,$per_page);
        return $this->response->paginator($design_demand, new DesignDemandListTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {post} /sd/demand/release 发布需求
     * @author 于海涛
     * @apiVersion 1.0.0
     * @apiName sdDemand release
     * @apiGroup sdDemandType
     *
     * @apiParam {string} token
     * @apiParam {string} name  项目名称
     * @apiParam {array} design_types 设计类型：1.产品策略；2.产品设计；3.结构设计 4.其他；[1,2]
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
        $rules = ['name' => 'required|string|max:100', 'design_types' => 'array', 'cycle' => 'required|integer|regex:/[1-5]/', 'design_cost' => 'required|integer|regex:/[1-6]/', 'field' => 'required|integer', 'industry' => 'required|integer', 'content' => 'required|string',];
        $payload = $request->only('name', 'design_types', 'cycle', 'design_cost', 'field', 'industry', 'content');
        $validator = app('validator')->make($payload, $rules);

        // 验证格式
        if ($validator->fails()) {
            throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
        }

        // 获取数据
        $all = $request->all();
        // 类型转成json
        $design_types = json_encode($all['design_types'],true);
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
        DB::beginTransaction();
        $design_demand = new DesignDemand;
        $design_demand->name = $all['name'];
        $design_demand->user_id = $this->auth_user_id;
        $design_demand->demand_company_id = $this->auth_user->demand_company_id;
        $design_demand->status = 1;
        $design_demand->type = 1;
        $design_demand->design_types = $design_types;
        $design_demand->cycle = $all['cycle'];
        $design_demand->design_cost = $all['design_cost'];
        $design_demand->field = $all['field'];
        $design_demand->industry = $all['industry'];
        $design_demand->item_province = $all['item_province'];
        $design_demand->item_city = $all['item_city'];
        $design_demand->content = $all['content'];
        if ($design_demand->save()) {
            DB::commit();
            return $this->response->item($design_demand, new DesignDemandTransformer)->setMeta($this->apiMeta());
        }
        DB::rollBack();
        return $this->response->array($this->apiError('发布失败', 500));

    }

    /**
     * @api {get} /sd/demand/demandInfo 查看某个需求详情
     * @author 于海涛
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

        return $this->response->item($demand_info, new DesignDemandTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {post} /sd/demand/demandShut 关闭某个需求
     * @author 于海涛
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
            $follow = Follow::where(['type'=>1,'design_demand_id'=>$demand_id])->get();
            if(!$follow->isEmpty()){
                Follow::where(['type'=>1,'design_demand_id'=>$demand_id])->delete();
            }
            return $this->response->array($this->apiSuccess('Success', 200));
        }
    }

    /**
     * @api {post} /sd/demand/demandUpdate 更改需求
     * @author 于海涛
     * @apiVersion 1.0.0
     * @apiName sdDemand demandUpdate
     * @apiGroup sdDemandType
     *
     * @apiParam {string} token
     * @apiParam {integer} demand_id //需求ID
     * @apiParam {string} name  项目名称
     * @apiParam {array} design_types 设计类型：1.产品策略；2.产品设计；3.结构设计 4.其他；[1,2]
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
        $rules = ['name' => 'required|string|max:100', 'design_types' => 'array', 'cycle' => 'required|integer|regex:/[1-5]/', 'design_cost' => 'required|integer|regex:/[1-6]/', 'field' => 'required|integer', 'industry' => 'required|integer', 'content' => 'required|string',];
        $payload = $request->only('name', 'design_types', 'cycle', 'design_cost', 'field', 'industry', 'content');
        $validator = app('validator')->make($payload, $rules);

        // 验证格式
        if ($validator->fails()) {
            throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
        }

        // 获取数据
        $all = $request->all();
        // 类型转成json
        $design_types = json_encode($all['design_types']);
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
            return $this->response->array($this->apiError('没有找到该需求', 404));
        }

        $demand_name = DesignDemand::where(['demand_company_id'=>$demand_company_id,'name'=>$all['name']])->whereNotIn('id', [$all['demand_id']])->get();
        if(!$demand_name->isEmpty()){
            return $this->response->array($this->apiError('项目名称已存在', 412));
        }
        DB::beginTransaction();
        $demand->name = $all['name'];
        $demand->design_types = $design_types;
        $demand->cycle = $all['cycle'];
        $demand->status = 1;
        $demand->design_cost = $all['design_cost'];
        $demand->field = $all['field'];
        $demand->industry = $all['industry'];
        $demand->item_province = $all['item_province'];
        $demand->item_city = $all['item_city'];
        $demand->content = $all['content'];
        if ($demand->save()) {
            DB::commit();
            return $this->response->item($demand, new DesignDemandTransformer)->setMeta($this->apiMeta());
        }
        DB::rollBack();
        return $this->response->array($this->apiError('更改失败', 500));
    }

    /**
     * @api {get} /sd/demand/designDemandList 设计公司查看需求列表
     * @author 于海涛
     * @apiVersion 1.0.0
     * @apiName sdDemand designDemandList
     * @apiGroup sdDemandType
     *
     * @apiParam {integer} per_page 分页数量
     * @apiParam {integer} page 页码
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
     *              "follow_status": 1,         //是否关注 1.已收藏 .2未收藏
     *              "created_at": 1540281300,
     *              "updated_at": 1540281300
     *          }
     *       ]
     *  }
     */
    public function designDemandList(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;

        // 设计公司ID
        $design_company_id = $this->auth_user->design_company_id;;
        if ($this->auth_user->type != 2 || !$design_company_id) {
            return $this->response->array($this->apiError('此用户不是设计公司', 403));
        }

        // 设计公司获取需求列表
        $demandIds = DesignDemand::getCollectDemandId($design_company_id);
        $design_demand = DesignDemand::query()
            ->with('DemandCompany','User')
            ->where('design_demand.status', 2)
            ->orderBy('created_at', 'desc')
            ->paginate($per_page);
        // 判断是否关注
        if(!$demandIds){
            foreach ($design_demand as $v){
                $v->follow_status = 2;
            }
        }else{
            foreach ($design_demand as $v) {
                if(in_array($v->id,$demandIds)){
                    $v->follow_status = 1;
                }else{
                    $v->follow_status = 2;
                }
            }
        }
        return $this->response->paginator($design_demand, new DesignDemandListTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /sd/demand/designDemandInfo 设计公司查看某个需求详情
     * @author 于海涛
     * @apiVersion 1.0.0
     * @apiName sdDemand designDemandInfo
     * @apiGroup sdDemandType
     *
     * @apiParam {string} token
     * @apiParam {integer} demand_id 需求ID
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

//        $design_company = DesignCompanyModel::where('id',$design_company_id)->first();
//        if(!$design_company->isVerify()){
//            return $this->response->array($this->apiError('设计公司没有认证', 403));
//        }

        $demand_info = DesignDemand::with('demandCompany','User')->where('id', $demand_id)->first();
        if (!$demand_info) {
            return $this->response->array($this->apiError('没有找到该需求', 404));
        }
        $follow = Follow::where(['design_demand_id'=>$demand_id,'design_company_id'=>$design_company_id])->first();
        // 判断是否关注
        if ($follow) {
            $demand_info->follow_status = 1;
        }else{
            $demand_info->follow_status = 2;
        }
        return $this->response->item($demand_info, new DesignDemandTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {post} /sd/demand/evaluateResult 需求公司评价设计成果
     * @author 于海涛
     * @apiVersion 1.0.0
     * @apiName sdDemand evaluateResult
     * @apiGroup sdDemandType
     *
     * @apiParam {string} token
     * @apiParam {integer} order_id 订单号
     * @apiParam {integer} design_level     设计水平 1-5
     * @apiParam {integer} response_speed   响应速度 1-5
     * @apiParam {integer} serve_attitude   服务态度 1-5
     * @apiParam {string} content           评价内容
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      },
     *  }
     */

    public function evaluateResult(Request $request)
    {
        $rules = [
            'order_id' => 'required|integer',
        ];
        $payload = $request->only('order_id');
        $validator = app('validator')->make($payload, $rules);

        // 验证格式
        if ($validator->fails()) {
            throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
        }

        $arr = [1,2,3,4,5];
        $design_level = in_array($request->input('design_level'), $arr) ? $request->input('design_level') : null;
        $response_speed = in_array($request->input('response_speed'), $arr) ? $request->input('response_speed') : null;
        $serve_attitude = in_array($request->input('serve_attitude'), $arr) ? $request->input('serve_attitude') : null;
        $content = $request->input('content');
        $demand_company_id = $this->auth_user->demand_company_id;
        $user_id = $this->auth_user_id;
        $order_id = $request->input('order_id');

        // 是否有此订单
        $order = PayOrder::where(['type'=>5,'user_id'=>$user_id,'uid'=>$order_id])->first();
        if(!$order){
            return $this->response->array($this->apiError('您没有此订单,无法评价', 404));
        }

        $design_result_id = $order->design_result_id;
        $result = DesignResult::where('id',$design_result_id)->first();
        // 判断有没有此设计成果
        if(!$result){
            return $this->response->array($this->apiError('没有找到此设计成果', 404));
        }

        // 判断交易状态
        if($result->sell !== 2){
            return $this->response->array($this->apiError('交易没有成功,无法评价', 403));
        }

        // 是否已经评价
        $is_evaluate = ResultEvaluate::where(['demand_company_id'=>$demand_company_id,'design_result_id'=>$design_result_id])->first();
        if($is_evaluate){
            return $this->response->array($this->apiError('已评价,无法重复评价', 412));
        }

        DB::beginTransaction();
        // 保存评价
        $evaluate = new ResultEvaluate;
        $evaluate->design_company_id = $result->design_company_id;
        $evaluate->design_result_id = $design_result_id;
        $evaluate->demand_company_id = $demand_company_id;
        $evaluate->design_level = $design_level;
        $evaluate->response_speed = $response_speed;
        $evaluate->serve_attitude = $serve_attitude;
        $evaluate->content = $content;
        if($evaluate->save()){
            DB::commit();
            return $this->response->array($this->apiSuccess('Success', 200,$evaluate));
        }
        DB::rollBack();
        return $this->response->array($this->apiError('评价失败', 500));

    }

    /**
     * @api {get} /sd/demand/evaluateInfo 设计成果评价详情
     * @author 于海涛
     * @apiVersion 1.0.0
     * @apiName sdDemand evaluateInfo
     * @apiGroup sdDemandType
     *
     * @apiParam {string} token
     * @apiParam {integer} order_id 订单号
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      },
     *  }
     */

    public function evaluateInfo(Request $request)
    {

        $rules = [
            'order_id' => 'required|integer',
        ];
        $payload = $request->only('order_id');
        $validator = app('validator')->make($payload, $rules);

        // 验证格式
        if ($validator->fails()) {
            throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
        }
        // 设计成果ID
        $order_id = $request->input('order_id');

        // 需求方
        if($this->auth_user->type == 1){
            $user_id = $this->auth_user_id;
            $order = PayOrder::where(['type'=>5,'user_id'=>$user_id,'uid'=>$order_id])->first();

            // 设计方
        }else if ($this->auth_user->type == 2) {
            $user_id = $this->auth_user_id;
            $order = PayOrder::where(['type'=>5,'design_user_id'=>$user_id,'uid'=>$order_id])->first();
        }

        // 是否有此订单
        if(!$order){
            return $this->response->array($this->apiError('您没有此订单', 404));
        }

        // 获取评价
        $evaluate = ResultEvaluate::where('design_result_id',$order->design_result_id)->get();
        return $this->response->array($this->apiSuccess('Success', 200, $evaluate));

    }
}