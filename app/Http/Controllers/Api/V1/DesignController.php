<?php
/**
 * 设计公司相关操作控制器
 */

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\DesignGetItemListTransformer;
use App\Models\Item;
use App\Models\ItemRecommend;
use Illuminate\Support\Facades\Log;

class DesignController extends BaseController
{
    /**
     * @api {get} /design/itemList 获取系统推荐的项目订单列表
     * @apiVersion 1.0.0
     * @apiName design itemList
     * @apiGroup design
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *  {
            "data": [
                {
                    "status": 5,
                    "status_value": "选定设计公司",
                    "item_status": 1, //需求公司转台
                    "item_status_value": "选定设计公司",
                    "design_company_status": 0, //设计公司状态
                    "design_company_status_value": "待操作",
                    "item": {
                        "id": 13,
                        "type": 1,
                        "type_value": "产品设计类型",
                        "design_type": 2,
                        "design_type_value": "产品设计",
                        "status": 5,
                        "field": 1,
                        "field_value": "智能硬件",
                        "industry": 2,
                        "industry_value": "消费零售",
                        "name": "api UI",
                        "product_features": "亮点",
                        "competing_product": "竞品",
                        "cycle": 1,
                        "cycle_value": "1个月内",
                        "design_cost": 2,
                        "design_cost_value": "1-5万之间",
                        "province": 2,
                        "city": 2,
                        "image": [],
                        "price": 200000
                    }
                }
            ],
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    public function itemList()
    {
        if(!$design_company = $this->auth_user->designCompany){
            return $this->response->array($this->apiSuccess());
        }

        $item_recommends = ItemRecommend
                        ::where(['design_company_id' => $design_company->id])
                        ->where( 'item_status', '!=' ,1)
                        ->get();

        return $this->response->collection($item_recommends, new DesignGetItemListTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /design/refuseItem/{item_id} 拒绝设计项目
     * @apiVersion 1.0.0
     * @apiName design refuseItem
     * @apiGroup design
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *  {
            "meta": {
            "message": "Success",
            "status_code": 200
            }
        }
     */
    public function refuseItem($item_id)
    {
        $item_id = (int)$item_id;
        $design_company = $this->auth_user->designCompany;
        if(!$design_company){
            return $this->response->array($this->apiError());
        }

        try{
            $item_recommend = ItemRecommend
                ::where(['item_id' => $item_id, 'design_company_id' => $design_company->id])
                ->first();
            if(!$item_recommend){
                return $this->response->array($this->apiError());
            }
            $item_recommend->design_company_status = -1;
            $item_recommend->save();

            return $this->response->array($this->apiSuccess());
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return $this->response->array($this->apiError());
        }
    }

}
