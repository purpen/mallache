<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\PayOrder;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Lib\AliPay\Alipay;
use Qiniu\Http\Request;

class PayController extends BaseController
{
    //发布需求保证金支付
    /**
     * @api {get} /pay/demandAliPay/ 发布需求保证金支付
     * @apiVersion 1.0.0
     * @apiName pay demandAliPay
     * @apiGroup pay
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *      "data": {
     *          'html_test': ""
     *      }
     *  }
     */
    public function demandAliPay()
    {
        $pay_order = $this->createPayOrder('发布需求保证金');
        $alipay = new Alipay();
        $html_text = $alipay->alipayApi($pay_order->uid, '发布需求保证金', 99);

        return $this->response->array($this->apiSuccess('Success', 200, compact('html_text')));
    }

    /**
     * 创建需求 支付单
     * @param int $type 支付类型：1.预付押金;
     * @param int $item_id 目标ID
     * @param int $user_id 用户ID
     * @param string $summary 备注
     * @return mixed
     */
    protected function createPayOrder($summary = '', $type = 1)
    {
        $pay_order = PayOrder::where(['type' => $type, 'user_id' => $this->auth_user_id, 'status' => 0])
            ->first();
        if($pay_order){
            return $pay_order;
        }

        $uid = uniqid('zf');

        $pay_order = PayOrder::create([
            'uid' => $uid,
            'user_id' => $this->auth_user_id,
            'type' => $type,
            'summary' => $summary
        ]);
        return $pay_order;
    }

    /**
     * @api {post} /pay/aliPayNotify  支付宝异步回调接口
     * @apiVersion 1.0.0
     * @apiName pay aliPayNotify
     * @apiGroup pay
     *
     * @apiSuccessExample 成功响应:
     *   "success"
     */
    public function aliPayNotify()
    {
        $alipay = new Alipay();
        //支付成功
        if($alipay->notifyUrl()){
            //商户订单号
            $out_trade_no = $_POST['out_trade_no'];

            //支付宝交易号
            $trade_no = $_POST['trade_no'];

            //交易状态
            $trade_status = $_POST['trade_status'];

            //判断是否支付完成
            if($_POST['trade_status'] == 'TRADE_SUCCESS') {
                try{
                    $pay_order = PayOrder::where('uid', $out_trade_no)->first();
                    $pay_order->pay_type = 1; //支付宝
                    $pay_order->pay_no = $trade_no;
                    $pay_order->status = 1; //支付成功
                    $pay_order->save();
                }
                catch (\Exception $e){
                    Log::error('支付订单操作失败');
                    return;
                }
            }
            //三个月后不可退款状态通知
            elseif ($_POST['trade_status'] == 'TRADE_FINISHED'){

            }

            //处理成功
            $alipay->trueNotifyUrl();
        }
        //支付失败
        else{
            $alipay->flaseNotifyUrl();
        }

    }

}

