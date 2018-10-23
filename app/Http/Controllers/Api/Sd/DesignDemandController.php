<?php
/**
 * 设计需求控制器
 *
 * @User yht
 * @time 2018-10-22
 */

namespace App\Http\Controllers\Api\Sd;

use Illuminate\Http\Request;
use APP\Models\DesignDemand;

class DesignDemandController extends BaseController
{
    /**
     * @api {post} /demand/release 发布项目
     * @apiVersion 1.0.0
     * @apiName demand release
     * @apiGroup demandType
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
     *       "data": [
     *           0 //没有审核或者没有找到该需求公司
     *       ]
     *  }
     */
    public function release(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:100',
            'design_types' => 'JSON',
            'cycle' => 'required|integer|regex:/[1-5]/',
            'design_cost' => 'required|integer|regex:/[1-6]/',
            'field' => 'required|integer|regex:/[1-6]/',
            'industry' => 'required|integer|regex:/[1-11]/',
            'content' => 'required|string',
        ];
        $all = $request->all();
        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Error', $validator->errors());
        }

        if ($this->auth_user->type != 1) {
            return $this->response->array($this->apiError('此用户不是需求公司', 403));
        }

        // 需求公司信息是否认证
        $demand_company = $this->auth_user->demandCompany;
        if(!$demand_company){
            return $this->response->array($this->apiError('需求公司没有认证', 412));
        }

        $demand_name = DesignDemand::where(['user_id'=>$this->auth_user_id,'demand_company_id'=>$this->auth_user->demand_company_id,'name'=>$all['name']])->first();
        if(!$demand_name){
            return $this->response->array($this->apiError('项目名称已存在', 400));
        }

        $design_demand = new DesignDemand;
        $design_demand->name = $all['name'];
        $design_demand->user_id = $this->auth_user_id;
        $design_demand->demand_company_id = $this->auth_user->demand_company_id;
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
}
