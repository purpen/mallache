<?php
/**
 * 设计公司相关操作控制器
 */

namespace App\Http\Controllers\Api\V1;

use App\Events\ItemStatusEvent;
use App\Http\Transformer\DesignGetItemListTransformer;
use App\Http\Transformer\DesignItemListTransformer;
use App\Http\Transformer\DesignShowItemTransformer;
use App\Http\Transformer\ItemTransformer;
use App\Http\Transformer\UserTransformer;
use App\Models\DesignCompanyModel;
use App\Models\Item;
use App\Models\ItemRecommend;
use App\Models\ItemStage;
use App\Models\User;
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
     * @apiParam {integer} per_page 分页数量
     * @apiParam {integer} page 页码
     * @apiParam {int} sort 0:升序；1.降序(默认)
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
              "status_code": 200,
              "pagination": {
                  "total": 1,
                  "count": 1,
                  "per_page": 10,
                  "current_page": 1,
                  "total_pages": 1,
                  "links": []
              }
        }
     */
    public function itemList(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        if(!$design_company = $this->auth_user->designCompany){
            return $this->response->array($this->apiSuccess('Success', 200, []));
        }
        if($request->input('sort') == 0 && $request->input('sort') !== null)
        {
            $sort = 'asc';
        }
        else
        {
            $sort = 'desc';
        }
        $query = ItemRecommend
                        ::where(['design_company_id' => $design_company->id])
                        ->where( 'item_status', '=' , 0)
                        ->where('design_company_status', '!=', -1);
        $lists = $query->orderBy('id', $sort)->paginate($per_page);

        return $this->response->paginator($lists, new DesignGetItemListTransformer)->setMeta($this->apiMeta());
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

            //项目是否匹配失败
            $item = new Item();
            $item->itemIsFail($item_id);

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
                    "email": "qq@qq.com",
     *              "warranty_money": ,  // 项目完成时 支付金额
     *              "warranty_money_proportion": ,  // 项目完成时 支付金额比例
     *              "first_payment": ,   // 项目首付款
     *              "first_payment_proportion"  //首付款比例
     *              "other_money": ,     // 阶段总金额
                },
     *      "quotation": {      //报价单信息
                "id": 19,
                "item_demand_id": 38,
                "design_company_id": 14,
                "price": "0.02",
                "summary": "aaaaaa",
                "status": 1,     // 状态： 0.未确认 1.已确认
                "created_at": 1495511081,
                "updated_at": 1495511109,
                "deleted_at": null,
                "user_id": 30
            },
            "contract": null   //合同
 *           "evaluate": null  //评价
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
            return $this->response->array($this->apiError('not found item', 404));
        }

        $design_company = $this->auth_user->designCompany;
        if(!$design_company){
            return $this->response->array($this->apiError('not found design company', 404));
        }
        //验证设计公司查看项目权限
        $design_arr = explode(',',$item->recommend ?? '');
        if(!in_array($design_company->id, $design_arr)){
            return $this->response->array($this->apiError('not found!', 404));
        }

        if(!$item){
            return $this->response->array($this->apiError());
        }

        return $this->response->item($item, new DesignShowItemTransformer($design_company->id))->setMeta($this->apiMeta());
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
     * @apiParam {int} sort 0:升序；1.降序(默认)
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
     * },
     * "is_contract": 0  //0：未添加合同；1.已添加合同
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

        if($request->input('sort') == 0 && $request->input('sort') !== null)
        {
            $sort = 'asc';
        }
        else
        {
            $sort = 'desc';
        }

        $design_company = $this->auth_user->designCompany;
        if(!$design_company){
            return $this->response->array($this->apiError('not found design company', 404));
        }
        $design_company_id = $design_company->id;

        $query = Item::where('design_company_id', $design_company_id);

        if(!empty($where_in)){
            $query = $query->whereIn('status', $where_in);
        }

        $lists = $query->orderBy('id', $sort)->paginate($per_page);

        return $this->response->paginator($lists, new DesignItemListTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {post} /design/itemStart/{item_id} 确认项目开始设计
     * @apiVersion 1.0.0
     * @apiName design itemStart
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
    public function itemStart($item_id)
    {
        if(!$item = Item::find(intval($item_id))){
            return $this->response->array($this->apiError('not found item', 404));
        }

        if($item->design_company_id != $this->auth_user->design_company_id || $item->status != 9){
            return $this->response->array($this->apiError('无权操作', 403));
        }

        $item->status = 11;  //项目开始
        $item->save();

        event(new ItemStatusEvent($item));

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {post} /design/itemDone/{item_id} 确认项目已完成
     * @apiVersion 1.0.0
     * @apiName design itemDone
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
    public function itemDone($item_id)
    {
        if(!$item = Item::find(intval($item_id))){
            return $this->response->array($this->apiError('not found item', 404));
        }

        if($item->design_company_id !== $this->auth_user->design_company_id || $item->status !== 11){
            return $this->response->array($this->apiError('无权操作', 403));
        }

        //验证项目阶段信息都已确认
        $item_stage_count = ItemStage::where(['item_id' => $item->id, 'confirm' => 0])->count();
        if($item_stage_count){  //如果存在未确认的阶段，不能确认完成
            return $this->response->array($this->apiError('项目目前阶段未完成，不能确认完成', 403));
        }

        $item->status = 15;  //项目已完成
        $item->save();

        event(new ItemStatusEvent($item));

        return $this->response->array($this->apiSuccess());
    }


    /**
     * @api {get} /design/members 设计公司查看成员列表
     *
     * @apiVersion 1.0.0
     * @apiName design members
     * @apiGroup design
     *
     * @apiParam {integer} per_page 分页数量
     * @apiParam {integer} page 页码
     * @apiParam {int} sort 0:升序；1.降序(默认)
     * @apiParam {token} token
     *
     * @apiSuccessExample 成功响应:
        {
            "data": [
                {
                "id": 2,
                "type": 2,
                "account": "18132382134",
                "username": "",
                "email": null,
                "phone": "18132382134",
                "status": 0,
                "item_sum": 0,
                "price_total": "0.00",
                "price_frozen": "0.00",
                "cash": "0.00",
                "logo_image": null,
                "design_company_id": 51,
                "role_id": 0,
                "demand_company_id": 0,
                "realname": null,
                "design_company_name": "222"
                }
            ],
            "meta": {
                "message": "Success",
                "status_code": 200
            }
        }
     */
    public function members(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        if($request->input('sort') == 0 && $request->input('sort') !== null) {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $user_id = $this->auth_user_id;
        $user = User::where('id' , $user_id)->first();
        if($user->child_account == 0){
            return $this->response->array($this->apiError('该用户不是主账户', 403));
        }
        if(in_array($user->company_role,[0,10])){
            return $this->response->array($this->apiError('该用户不是超级管理员', 403));
        }
        $design_company_id = $user->design_company_id;
        if($design_company_id == 0){
            return $this->response->array($this->apiError('该用户不是设计公司', 404));
        }
        $users = User::where('design_company_id' , $design_company_id)->orderBy('id', $sort)->paginate($per_page);

        return $this->response->paginator($users, new UserTransformer())->setMeta($this->apiMeta());

    }

    /**
     * @api {put} /design/is_admin 设计公司设置成管理员,恢复成成员
     *
     * @apiVersion 1.0.0
     * @apiName design is_admin
     * @apiGroup design
     *
     * @apiParam {integer} set_user_id 被设置的用户id
     * @apiParam {integer} company_role 10.管理员；0.成员；
     * @apiParam {token} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *     "meta": {
     *       "message": "",
     *       "status_code": 200
     *     }
     *   }
     *
     */
    public function is_admin(Request $request)
    {
        $user_id = $this->auth_user_id;
        $user = User::where('id' , $user_id)->first();
        if(!$user){
            return $this->response->array($this->apiError('没有找到主账户', 404));
        }
        if($user->child_account == 0){
            return $this->response->array($this->apiError('该用户不是主账户', 403));
        }
        if(in_array($user->company_role , [0 , 10])){
            return $this->response->array($this->apiError('该用户不是超级管理员', 403));
        }
        $set_user_id = $request->input('set_user_id');
        $company_role = $request->input('company_role');
        $set_user = User::where('id' , $set_user_id)->first();
        if(!$set_user){
            return $this->response->array($this->apiError('没有找到被设计的用户', 404));
        }
        $set_user->company_role = $company_role;
        $set_user->save();

        return $this->response->array($this->apiSuccess());

    }

}
