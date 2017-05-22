<?php
/**
 * 提现相关操作
 * User: llh
 * Date: 2017/5/22
 * Time: 14:01
 */

namespace app\Http\Controllers\Api\V1;


use App\Helper\Tools;
use App\Models\WithdrawOrder;
use Illuminate\Http\Request;

class WithdrawOrderController extends BaseController
{
    /*uid	varchar(50)	否		单号
        user_id	int(10)	否		用户ID
        type	tinyint(4)	否		类型：1.银行转账；
        amount	decimal(10,2)	否		提现金额
        company_name	varchar(30)	否		公司名称
        account_name	varchar(30)	否		开户名
        account_number	varchar(50)	否		账号
        bank_id	tinyint(4)	是	‘’	开户行
        branch_name	varchar(30)	是	‘’	支行名称
        status	tinyint(4)	否	0	状态：0.申请；1.同意；
        summary	varchar(200)	是	‘‘	备注*/

    //创建提现单
    /**
     * @api {post} /auth/register 用户注册
     * @apiVersion 1.0.0
     * @apiName user register
     * @apiGroup User
     *
     * @apiParam {integer} type 用户类型：1.需求公司；2.设计公司；
     * @apiParam {string} account 用户账号(手机号)
     * @apiParam {string} password 设置密码
     * @apiParam {integer} sms_code 短信验证码
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *     "data": {
     *          "token": ""
     *      }
     *   }
     */
    public function create(Request $request)
    {
        $amount = (float)$request->input('amount');

        //可提现金额
        $cash = $this->auth_user->cash;
        if($amount > $cash){
            return $this->response->array($this->apiError('可提现金额不足', 403));
        }

        WithdrawOrder::create([
            'uid' => Tools::orderId($this->auth_user_id),
            'type' => 1,
            'amount' => $amount,
            'company_name' =>'',

        ]);
    }

}