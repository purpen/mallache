<?php
/**
 * 设计公司相关操作控制器
 */

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\DesignGetItemListTransformer;
use App\Http\Transformer\DesignItemListTransformer;
use App\Http\Transformer\ItemTransformer;
use App\Models\DesignCompanyModel;
use App\Models\Item;
use App\Models\ItemRecommend;
use Illuminate\Http\Request;
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
                        ->where('design_company_status', '!=', -1)
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

    /**
     * @api {get} /design/item/{item_id} 设计公司获取项目详细信息
     * @apiVersion 1.0.0
     * @apiName design item
     * @apiGroup design
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
            "data": {
                "item": {
                    "id": 13,
                    "type": 1,
                    "type_value": "产品设计类型",
                    "design_type": 2,
                    "design_type_value": "产品设计",
                    "status": 5,
                    "field": 2,
                    "field_value": "消费电子",
                    "industry": 2,
                    "industry_value": "消费零售",
                    "name": "api UI",
                    "product_features": "亮点",
                    "competing_product": "竞品",
                    "cycle": 1,
                    "cycle_value": "1个月内",
                    "design_cost": 2,
                    "design_cost_value": "1-5万之间",
                    "city": 2,
                    "image": [],
                    "price": 200000,
                    "company_name": null,  //公司名称
                    "company_abbreviation": null, //简称
                    "company_size": null, //公司规模；1...
                    "company_web": null,  //公司网址
                    "company_province": null, //省份
                    "company_city": null,  //城市
                    "company_area": null,   //区县
                    "address": null,    //详细地址
                    "contact_name": null,   //联系人
                    "phone": "172734923",
                    "email": "qq@qq.com"
                },
            "quotation": null, //报价单信息
            "contract": null   //合同
            },
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    public function item($item_id)
    {
        if(!$item = Item::find(intval($item_id))){
            return $this->response->array($this->apiSuccess());
        }
        //验证设计公司查看项目权限
        $design_arr = explode(',',$item->recommend ?? '');
        if(!in_array($this->auth_user_id, $design_arr)){
            return $this->response->array($this->apiError('not found!', 404));
        }

        if(!$item){
            return $this->response->array($this->apiError());
        }
        return $this->response->item($item, new ItemTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /design/cooperationLists 已确定合作项目列表
     * @apiVersion 1.0.0
     * @apiName design cooperationLists
     * @apiGroup design
     *
     * @apiParam {string} token
     * @apiParam {integer} type 0:全部（默认）；1.
     * @apiParam {integer} per_page 分页数量
     * @apiParam {integer} page 页码
     * @apiParam {int} 0:升序；1.降序(默认)
     *
     * @apiSuccessExample 成功响应:
     * {
     * "data": [
     * {
     * "item": {
     * "id": 13,
     * "type": 2,
     * "type_value": "UI UX设计类型",
     * "design_type": 2,
     * "design_type_value": "网页设计",
     * "status": 4,
     * "status_value": "等待设计公司接单",
     * "design_status_value": "提交报价单",
     * "name": "esisd",
     * "stage": 1,
     * "stage_value": "已有app／网站，需重新设计",
     * "complete_content": [
     * "哈哈",
     * "嘿嘿"
     * ],
     * "other_content": "w",
     * "design_cost": 1,
     * "design_cost_value": "1万以下",
     * "province": 1,
     * "city": 2,
     * "province_value": "",
     * "city_value": "",
     * "image": [],
     * "price": 200000,
     * "stage_status": 2,
     * "cycle": 1,
     * "cycle_value": "1个月内",
     * "company_name": "nicai",
     * "company_abbreviation": "ni",
     * "company_size": 1,
     * "company_size_value": "10人以下",
     * "company_web": "www.baidu.com",
     * "company_province": 1,
     * "company_city": 2,
     * "company_area": 2,
     * "company_province_value": "",
     * "company_city_value": "",
     * "company_area_value": "",
     * "address": "niisadiasd",
     * "contact_name": "11",
     * "phone": "1877678",
     * "email": "www@qq.com",
     * "created_at": "2017-04-13"
     * }
     * }
     * ],
     * "meta": {
     * "message": "Success",
     * "status_code": 200,
     * "pagination": {
     * "total": 1,
     * "count": 1,
     * "per_page": 10,
     * "current_page": 1,
     * "total_pages": 1,
     * "links": []
     * }
     * }
     * }
     */
    public function cooperationLists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $type = $request->input('type') ?? 0;

        switch ($type){
//            case 1:
//                $where_in = [1,2,3,4,5,6,7,8];
//                break;
            default:
                $where_in = [];
        }

        if($request->input('sort') === 0)
        {
            $sort = 'asc';
        }
        else
        {
            $sort = 'desc';
        }

        $user = $this->auth_user;
        $design_company_id = $user->designCompany->id;

        $query = Item::where('design_company_id', $design_company_id);

        if(!empty($where_in)){
            $query = $query->whereIn('status', $where_in);
        }

        $lists = $query->orderBy('id', $sort)->paginate($per_page);

        return $this->response->paginator($lists, new DesignItemListTransformer)->setMeta($this->apiMeta());
    }

}
