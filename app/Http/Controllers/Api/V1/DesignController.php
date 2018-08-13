<?php
/**
 * 设计公司相关操作控制器
 */

namespace App\Http\Controllers\Api\V1;

use App\Events\ItemStatusEvent;
use App\Helper\Tools;
use App\Http\Transformer\DesignGetItemListTransformer;
use App\Http\Transformer\DesignItemListTransformer;
use App\Http\Transformer\DesignShowItemTransformer;
use App\Http\Transformer\ItemTransformer;
use App\Http\Transformer\UserTransformer;
use App\Models\DesignCompanyModel;
use App\Models\DesignProject;
use App\Models\Item;
use App\Models\ItemRecommend;
use App\Models\ItemStage;
use App\Models\ItemUser;
use App\Models\PanDirector;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

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
     * "data": [
     * {
     * "status": 5,
     * "status_value": "选定设计公司",
     * "item_status": 1, //需求公司转台
     * "item_status_value": "选定设计公司",
     * "design_company_status": 0, //设计公司状态
     * "design_company_status_value": "待操作",
     * "item": {
     * "id": 13,
     * "type": 1,
     * "type_value": "产品设计类型",
     * "design_type": 2,
     * "design_type_value": "产品设计",
     * "status": 5,
     * "field": 1,
     * "field_value": "智能硬件",
     * "industry": 2,
     * "industry_value": "消费零售",
     * "name": "api UI",
     * "product_features": "亮点",
     * "competing_product": "竞品",
     * "cycle": 1,
     * "cycle_value": "1个月内",
     * "design_cost": 2,
     * "design_cost_value": "1-5万之间",
     * "province": 2,
     * "city": 2,
     * "image": [],
     * "price": 200000
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
     */
    public function itemList(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        if (!$design_company = $this->auth_user->designCompany) {
            return $this->response->array($this->apiSuccess('Success', 200, []));
        }
        if ($request->input('sort') == 0 && $request->input('sort') !== null) {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $query = ItemRecommend
            ::where(['design_company_id' => $design_company->id])
            ->where('item_status', '=', 0)
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
     * @apiParam {array} refuse_types 1不擅长 2排期紧张 10其他 [排期紧张,不擅长]
     * @apiParam {string} summary 拒单原因
     *
     * @apiSuccessExample 成功响应:
     *  {
     * "meta": {
     * "message": "Success",
     * "status_code": 200
     * }
     * }
     */
    public function refuseItem(Request $request , $item_id)
    {
        $item_id = (int)$item_id;
        $design_company = $this->auth_user->designCompany;
        if (!$design_company) {
            return $this->response->array($this->apiError());
        }
        if (!$item = Item::find($item_id)) {
            return $this->response->array($this->apiError('not found', 404));
        }
        $summary = $request->input('summary') ? $request->input('summary') : '';
        $refuse_types = $request->input('refuse_types') ? implode(',' , $request->input('refuse_types')) : '';

        try {
            $item_recommend = ItemRecommend
                ::where(['item_id' => $item_id, 'design_company_id' => $design_company->id])
                ->first();
            if (!$item_recommend) {
                return $this->response->array($this->apiError());
            }
            $item_recommend->design_company_status = -1;
            $item_recommend->summary = $summary;
            $item_recommend->refuse_types = $refuse_types;
            $item_recommend->save();

            $tools = new Tools();

            $title = '项目报价被拒';
            if (empty($refuse_types)){
                $content = '【' . ($item->itemInfo())['name'] . '】' . '设计公司拒单原因.'.$refuse_types . $summary;
            } else {
                $content = '【' . ($item->itemInfo())['name'] . '】' . '设计公司拒单原因.'.$refuse_types .'.'. $summary;
            }
            $tools->message($item->user_id, $title, $content, 1, null);

            //项目是否匹配失败
            $item = new Item();
            $item->itemIsFail($item_id);

            return $this->response->array($this->apiSuccess());
        } catch (\Exception $e) {
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
     * "data": {
     * "item": {
     * "id": 13,
     * "type": 1,
     * "type_value": "产品设计类型",
     * "design_type": 2,
     * "design_type_value": "产品设计",
     * "status": 5,
     * "field": 2,
     * "field_value": "消费电子",
     * "industry": 2,
     * "industry_value": "消费零售",
     * "name": "api UI",
     * "product_features": "亮点",
     * "competing_product": "竞品",
     * "cycle": 1,
     * "cycle_value": "1个月内",
     * "design_cost": 2,
     * "design_cost_value": "1-5万之间",
     * "city": 2,
     * "image": [],
     * "price": 200000,
     * "commission": 20000,  // 佣金金额
     * "commission_rate": 12, // 佣金比例
     * "company_name": null,  //公司名称
     * "company_abbreviation": null, //简称
     * "company_size": null, //公司规模；1...
     * "company_web": null,  //公司网址
     * "company_province": null, //省份
     * "company_city": null,  //城市
     * "company_area": null,   //区县
     * "address": null,    //详细地址
     * "contact_name": null,   //联系人
     * "phone": "172734923",
     * "email": "qq@qq.com",
     *              "warranty_money": ,  // 项目完成时 支付金额
     *              "warranty_money_proportion": ,  // 项目完成时 支付金额比例
     *              "first_payment": ,   // 项目首付款
     *              "first_payment_proportion"  //首付款比例
     *              "other_money": ,     // 阶段总金额
     * },
     *      "quotation": {      //报价单信息
     * "id": 19,
     * "item_demand_id": 38,
     * "design_company_id": 14,
     * "price": "0.02",
     * "summary": "aaaaaa",
     * "status": 1,     // 状态： 0.未确认 1.已确认
     * "created_at": 1495511081,
     * "updated_at": 1495511109,
     * "deleted_at": null,
     * "user_id": 30
     * },
     * "contract": null   //合同
     *           "evaluate": null  //评价
     * },
     * "meta": {
     * "message": "Success",
     * "status_code": 200
     * }
     * }
     */
    public function item($item_id)
    {
        if (!$item = Item::find(intval($item_id))) {
            return $this->response->array($this->apiError('not found item', 404));
        }

        $design_company = $this->auth_user->designCompany;
        if (!$design_company) {
            return $this->response->array($this->apiError('not found design company', 404));
        }
        //验证设计公司查看项目权限
        $design_arr = explode(',', $item->recommend ?? '');
        if (!in_array($design_company->id, $design_arr)) {
            return $this->response->array($this->apiError('not found!', 404));
        }

        if (!$item) {
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

        switch ($type) {
//            case 1:
//                $where_in = [1,2,3,4,5,6,7,8];
//                break;
            default:
                $where_in = [];
        }

        if ($request->input('sort') == 0 && $request->input('sort') !== null) {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $design_company = $this->auth_user->designCompany;
        if (!$design_company) {
            return $this->response->array($this->apiError('not found design company', 404));
        }
        $design_company_id = $design_company->id;

        $query = Item::where('design_company_id', $design_company_id);

        if (!empty($where_in)) {
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
     * "meta": {
     * "message": "Success",
     * "status_code": 200
     * }
     * }
     */
    public function itemStart($item_id)
    {
        if (!$item = Item::find(intval($item_id))) {
            return $this->response->array($this->apiError('not found item', 404));
        }

        if ($item->design_company_id != $this->auth_user->design_company_id || $item->status != 9) {
            return $this->response->array($this->apiError('无权操作', 403));
        }

        $item->status = 11;  //项目开始
        $item->save();

        // 如果需求项目和设计公司
        if ($design_project = DesignProject::where(['item_demand_id' => $item_id, 'design_company_id' => $item->design_company_id])->first()) {

            // 设计管理项目取消隐藏状态
            $design_project->status = 1;
            $design_project->save();

            $design_company = DesignCompanyModel::find($item->design_company_id);
            // 自动创建项目云盘目录
            PanDirector::createProjectDir($design_company, $design_project);
        }


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
     * "meta": {
     * "message": "Success",
     * "status_code": 200
     * }
     * }
     */
    public function itemDone($item_id)
    {
        if (!$item = Item::find(intval($item_id))) {
            return $this->response->array($this->apiError('not found item', 404));
        }

        if ($item->design_company_id !== $this->auth_user->design_company_id || $item->status !== 11) {
            return $this->response->array($this->apiError('无权操作', 403));
        }

        //验证项目阶段信息都已确认
        $item_stage_count = ItemStage::where(['item_id' => $item->id, 'confirm' => 0])->count();

        if ($item_stage_count) {  //如果存在未确认的阶段，不能确认完成
            return $this->response->array($this->apiError('项目目前阶段未完成，不能确认完成', 403));
        }
        // 验证付款
        $item_stage_pay_count = ItemStage::where(['item_id' => $item->id, 'pay_status' => 0])->count();
        if ($item_stage_pay_count) {  //如果存在未确认的阶段，不能确认完成
            return $this->response->array($this->apiError('有阶段款未支付，不能确认完成', 403));
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
     * {
     * "data": [
     * {
     * "id": 2,
     * "type": 2,
     * "account": "18132382134",
     * "username": "",
     * "email": null,
     * "phone": "18132382134",
     * "status": 0,
     * "item_sum": 0,
     * "price_total": "0.00",
     * "price_frozen": "0.00",
     * "cash": "0.00",
     * "logo_image": null,
     * "design_company_id": 51,
     * "role_id": 0,
     * "demand_company_id": 0,
     * "realname": null,
     * "design_company_name": "222"
     * }
     * ],
     * "meta": {
     * "message": "Success",
     * "status_code": 200
     * }
     * }
     */
    public function members(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        if ($request->input('sort') == 0 && $request->input('sort') !== null) {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $user_id = $this->auth_user_id;
        $user = User::where('id', $user_id)->first();
        if ($user->isDesignAdmin() == false) {
            return $this->response->array($this->apiError('该用户不是管理员或者超级管理员', 403));
        }
        $design_company_id = $user->design_company_id;
        if ($design_company_id == 0) {
            return $this->response->array($this->apiError('该用户不是设计公司', 404));
        }
        $users = User::where('design_company_id', $design_company_id)->orderBy('id', $sort)->paginate($per_page);

        return $this->response->paginator($users, new UserTransformer())->setMeta($this->apiMeta());

    }

    /**
     * @api {put} /design/isAdmin 设计公司设置成管理员,恢复成成员
     *
     * @apiVersion 1.0.0
     * @apiName design isAdmin
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
    public function isAdmin(Request $request)
    {
        $user_id = $this->auth_user_id;
        $user = User::where('id', $user_id)->first();
        if (!$user) {
            return $this->response->array($this->apiError('没有找到主账户', 404));
        }
        if ($user->isChildAccount() == true) {
            return $this->response->array($this->apiError('用户不是主账户', 403));
        }
        if ($user->isDesignSuperAdmin() == false) {
            return $this->response->array($this->apiError('该用户不是超级管理员', 403));
        }
        $set_user_id = $request->input('set_user_id');
        if ($user_id == $set_user_id) {
            return $this->response->array($this->apiError('不能把自己设置成管理员或成员', 403));
        }
        $company_role = $request->input('company_role');
        $set_user = User::where('id', $set_user_id)->first();
        if (!$set_user) {
            return $this->response->array($this->apiError('没有找到被设置的用户', 404));
        }
        $set_user->company_role = $company_role;
        $set_user->save();

        return $this->response->array($this->apiSuccess());

    }

    /**
     * @api {put} /design/deleteMember 移除成员
     *
     * @apiVersion 1.0.0
     * @apiName design deleteMember
     * @apiGroup design
     *
     * @apiParam {integer} delete_user_id 被移除的用户id
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
    public function deleteMember(Request $request)
    {
        $user_id = $this->auth_user_id;
        $delete_user_id = $request->input('delete_user_id');
        $user = User::where('id', $user_id)->first();
        if (!$user) {
            return $this->response->array($this->apiError('没有找到该账户', 404));
        }

        if ($user->isDesignAdmin() == false) {
            return $this->response->array($this->apiError('该用户不是管理员或者超级管理员', 403));
        }
        $design_company_id = $user->design_company_id;
        if ($design_company_id == 0) {
            return $this->response->array($this->apiError('该用户不是设计公司', 404));
        }
        //查看属于该公司的成员列表
        $users = User::where('design_company_id', $design_company_id)->get();
        $user_id_arr = [];
        foreach ($users as $new_user) {
            $user_id_arr[] = $new_user->id;
        }
        $new_user_id_arr = $user_id_arr;
        //判断是否是该公司的成员
        if (in_array($user_id, $new_user_id_arr)) {
            $del_user = User::find(intval($delete_user_id));
            if (!$del_user) {
                return $this->response->array($this->apiError('没有找到移除的用户', 404));
            } else {
                //如果是超级管理员不能被移除
                if ($del_user->company_role == 20) {
                    return $this->response->array($this->apiError('该用户是超级管理员，不能移除', 403));
                }
                try {

                    DB::beginTransaction();
                    $del_user->design_company_id = 0;
                    $del_user->invite_user_id = 0;
                    //用户表的设计公司id，邀请人的id都变成0
                    if ($del_user->save()) {
                        $item_users = ItemUser::where('user_id', $delete_user_id)->get();
                        //移除项目成员中用户id为delete_user_id
                        foreach ($item_users as $item_user) {
                            $item_user->delete();
                        }
                        //减少子账户数量
                        if ($user->child_count > 0) {
                            $user->child_count -= 1;
                            $user->save();
                        }

                        DB::commit();
                        return $this->response->array($this->apiSuccess());
                    } else {
                        DB::rollBack();
                        return $this->response->array($this->apiError('更改用户失败', 412));
                    }

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                }
            }

        } else {
            return $this->response->array($this->apiError('没有权限移除该用户', 403));
        }

    }

    /**
     * @api {get} /design/members/search 设计公司成员搜索
     *
     * @apiVersion 1.0.0
     * @apiName design membersSearch
     * @apiGroup design
     *
     * @apiParam {string} realname
     * @apiParam {token} token
     * @apiSuccessExample 成功响应:
     * {
     * "data": [
     * {
     * "id": 2,
     * "type": 2,
     * "account": "18132382134",
     * "username": "",
     * "email": null,
     * "phone": "18132382134",
     * "status": 0,
     * "item_sum": 0,
     * "price_total": "0.00",
     * "price_frozen": "0.00",
     * "cash": "0.00",
     * "logo_image": null,
     * "design_company_id": 51,
     * "role_id": 0,
     * "demand_company_id": 0,
     * "realname": null,
     * "design_company_name": "222"
     * }
     * ],
     * "meta": {
     * "message": "Success",
     * "status_code": 200
     * }
     * }
     */
    public function membersSearch(Request $request)
    {
        $user_id = $this->auth_user_id;
        $design_id = User::designCompanyId($user_id);
        $realname = $request->input('realname');
        $user = User::where('realname', 'like', '%' . $realname . '%')->where('design_company_id', $design_id)->get();
        if ($user) {
            return $this->response->collection($user, new UserTransformer())->setMeta($this->apiMeta());
        }
    }

    /**
     * @api {put} /design/restoreMember 恢复成员
     *
     * @apiVersion 1.0.0
     * @apiName design restoreMember
     * @apiGroup design
     *
     * @apiParam {string} rand_string 随机字符串
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
    public function restoreMember(Request $request)
    {
        $rand_string = $request->input('rand_string');
        $master_user_id = Redis::get($rand_string);
        if ($master_user_id == null) {
            return $this->response->array($this->apiError('该链接已过期，请联系邀请人', 403));
        }
        //主账户设计公司
        $master_user = User::where('id', $master_user_id)->first();
        if (!$master_user) {
            return $this->response->array($this->apiError('该链接已过期，请联系邀请人', 404));

        }
        //判断子账户的数量
        if ($master_user->child_count >= config('constant.child_count')) {
            return $this->response->array($this->apiError('当前只能邀请10个用户', 403));
        }
        $design_company_id = $master_user->design_company_id;
        //被恢复的成员
        $user_id = $this->auth_user_id;
        $user = User::where('id', $user_id)->first();
        //原来的设计公司id
        $old_design_company_id = $user->design_company_id;
        //原来的主账户
        $old_user = User::where('design_company_id', $old_design_company_id)->where('child_account', 0)->first();
        if (!$old_user) {
            return $this->response->array($this->apiError('没有找到原来的主账户', 404));
        }
        //判断被改变的账户是需求公司或者是主账户的话，不让更改
        if ($user->type == 1 || $user->child_account == 0) {
            return $this->response->array($this->apiError('被恢复的用户是需求公司，或者是主账户', 403));
        }
        if (!$user_id) {
            return $this->response->array($this->apiError('被恢复的用户不存在', 404));
        }

        $user->design_company_id = $design_company_id;
        $user->invite_user_id = $master_user_id;
        $user->company_role = 0;
        if ($user->save()) {
            $item_users = ItemUser::where('user_id', $user_id)->get();
            //移除项目成员中用户id为user_id
            foreach ($item_users as $item_user) {
                $_POST['id'] = $item_user->id;
                $_POST['user_id'] = $user_id;
                $_POST['item_id'] = $item_user->item_id;
                $_POST['company_id'] = $old_design_company_id;
                $item_user->delete();
            }
            //减少原来公司子账户数量-1
            if ($old_user->child_count > 0) {
                $old_user->child_count -= 1;
                $old_user->save();
            }

            //新的主账户子账户数量+1
            $master_user->child_count += 1;
            $master_user->save();
            return $this->response->array($this->apiSuccess());

        }

    }

}
