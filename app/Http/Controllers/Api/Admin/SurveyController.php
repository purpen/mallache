<?php
namespace App\Http\Controllers\Api\Admin;

use App\Models\DemandCompany;
use App\Models\DesignCompanyModel;
use App\Models\Item;
use App\Models\PayOrder;
use App\Models\User;
use App\Models\WithdrawOrder;

class SurveyController extends BaseController
{
    /**
     * @api {get} /admin/survey/index 后台控制台信息
     * @apiVersion 1.0.0
     * @apiName survey index
     * @apiGroup AdminSurvey
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      },
     *      "data": {
     *          "demand_user": 6,               // 需求公司用户
                "design_user": 1,               // 设计公司用户
                "user_count": 7,                // 总用户
                "close_item": 0,                // 关闭的项目数
                "processing_item": 0,           // 进行中的项目数
                "finish_item": 3,               // 完成的项目数
                "item_count": 3,                // 项目总数量
                "not_pay": 1,                   // 未支付的支付订单
                "pay_order": 1,                 // 支付订单总数量
                "bank_pay": 0,                  // 需要银行转账的支付单
                "withdraw_order": 0,            // 提现单数量
                "not_withdraw": 0,              // 待提现数量
                "not_design": 1,                // 待审核的设计公司
                "not_demand": 5                 // 待审核的需求公司
     *      }
     *  }
     */
    public function index()
    {
        // 用户数量
        $demand_user = User::query()->where('type', 1)->count();
        $design_user = User::query()->where('type', 2)->count();
        $user_count = $demand_user + $design_user;

        // 项目数量
        $close_item = Item::query()->where('status', -1)->count();
        $processing_item = Item::query()->whereIn('status', [18,22])->count();
        $finish_item = Item::query()->whereNotIn('status', [-1, 18 ,22])->count();
        $item_count = $close_item + $processing_item + $finish_item;

        // 支付单
        $not_pay = PayOrder::query()->where('status', 0)->count();
        $pay_order = PayOrder::query()->count();
        $bank_pay = PayOrder::query()->where(['pay_type' => 5, 'status' => 0])->count();

        // 提现单
        $withdraw_order = WithdrawOrder::query()->count();
        $not_withdraw = WithdrawOrder::query()->where('status', 0)->count();

        // 设计公司待审核数量
        $not_design = DesignCompanyModel::query()->where('verify_status', 0)->count();
        // 需求公司待审核数量
        $not_demand = DemandCompany::query()->where('verify_status', 0)->count();

        $data = compact('demand_user', 'design_user', 'user_count', 'close_item', 'processing_item', 'finish_item', 'item_count', 'not_pay', 'pay_order', 'bank_pay', 'withdraw_order', 'not_withdraw', 'not_design', 'not_demand');

        return $this->response->array($this->apiSuccess('Success', 200, $data));
    }
}