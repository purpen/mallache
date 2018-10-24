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
use App\Models\DesignCompanyModel;
use App\Models\DesignDemand;
use App\Models\Follow;

class DesignCollectDemandController extends BaseController
{
    /**
     * @api {get} /sd/design/designCollectList 设计公司收藏列表
     * @apiVersion 1.0.0
     * @apiName sdDesign designCollectList
     * @apiGroup sdDesignType
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      },
     *          "data": [
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
    public function designCollectList(Request $request)
    {
        // 设计公司ID
        $design_company_id = $this->auth_user->design_company_id;
        if ($this->auth_user->type != 2 || !$design_company_id) {
            return $this->response->array($this->apiError('此用户不是设计公司', 403));
        }

        $design_company = DesignCompanyModel::where('id',$design_company_id)->first();
        if(!$design_company->isVerify()){
            return $this->response->array($this->apiError('设计公司没有认证', 403));
        }

        $demand_info = Follow::showDemandList($design_company_id);
        return $this->response->array($this->apiSuccess('Success', 200, $demand_info));
    }

    /**
     * @api {post} /sd/design/collectDemand 设计公司收藏某个需求
     * @apiVersion 1.0.0
     * @apiName sdDesign collectDemand
     * @apiGroup sdDesignType
     *
     * @apiParam {string} token
     * @apiParam {integer} design_demand_id 设计需求ID
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function collectDemand(Request $request)
    {
        $rules = [
            'design_demand_id' => 'required|integer',
            ];

        $payload = $request->only('design_demand_id');
        $validator = app('validator')->make($payload, $rules);

        // 验证格式
        if ($validator->fails()) {
            throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
        }

        // 需求ID
        $design_demand_id = $request->input('design_demand_id');
        // 设计公司ID
        $design_company_id = $this->auth_user->design_company_id;
        if ($this->auth_user->type != 2 || !$design_company_id) {
            return $this->response->array($this->apiError('此用户不是设计公司', 403));
        }

        $design_company = DesignCompanyModel::where('id',$design_company_id)->first();
        if(!$design_company->isVerify()){
            return $this->response->array($this->apiError('设计公司没有认证', 403));
        }
        // 查找需求对应的需求公司
        $demand_company_id = DesignDemand::where('id',$design_demand_id)->first();
        $design_follow = new Follow;
        $design_follow->type = 1;
        $design_follow->design_demand_id = $design_demand_id;
        $design_follow->demand_company_id = $demand_company_id->demand_company_id;
        $design_follow->design_company_id = $design_company_id;
        if($design_follow->save()){
            return $this->response->array($this->apiSuccess('Success', 200));
        }
    }

    /**
     * @api {post} /sd/design/cancelCollectDemand 设计公司取消收藏某个需求
     * @apiVersion 1.0.0
     * @apiName sdDesign cancelCollectDemand
     * @apiGroup sdDesignType
     *
     * @apiParam {string} token
     * @apiParam {integer} design_demand_id 设计需求ID
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *  }
     */
    public function cancelCollectDemand(Request $request)
    {
        $rules = [
            'design_demand_id' => 'required|integer',
        ];

        $payload = $request->only('design_demand_id');
        $validator = app('validator')->make($payload, $rules);

        // 验证格式
        if ($validator->fails()) {
            throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
        }

        // 需求ID
        $design_demand_id = $request->input('design_demand_id');
        // 设计公司ID
        $design_company_id = $this->auth_user->design_company_id;
        if ($this->auth_user->type != 2 || !$design_company_id) {
            return $this->response->array($this->apiError('此用户不是设计公司', 403));
        }

        $design_company = DesignCompanyModel::where('id',$design_company_id)->first();
        if(!$design_company->isVerify()){
            return $this->response->array($this->apiError('设计公司没有认证', 403));
        }

        $follow = Follow::where('design_demand_id',$design_demand_id)->first();
        if($follow){
            $follow->delete();
            return $this->response->array($this->apiSuccess('Success', 200));
        }
        return $this->response->array($this->apiError('没有找到收藏的设计需求', 404));
    }
}