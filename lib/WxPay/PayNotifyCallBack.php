<?php

namespace Lib\WxPay;

use App\Events\PayOrderEvent;
use App\Models\PayOrder;
use App\Servers\Pay;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Lib\WxPay\lib\WxPayApi;
use Lib\WxPay\lib\WxPayNotify;
use Lib\WxPay\lib\WxPayOrderQuery;

/**
 * 微信回调类
 *
 * Class PayNotifyCallBack
 * @package Lib\WxPay
 */
class PayNotifyCallBack extends WxPayNotify
{
    //查询订单
    public function Queryorder($transaction_id)
    {
        $input = new WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = WxPayApi::orderQuery($input);
        if (array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS") {
            return true;
        }
        return false;
    }

    //重写回调处理函数
    public function NotifyProcess($data, &$msg)
    {
        $notfiyOutput = array();

        if (!array_key_exists("transaction_id", $data)) {
            $msg = "输入参数不正确";
            return false;
        }
        //查询订单，判断订单真实性
        if (!$this->Queryorder($data["transaction_id"])) {
            $msg = "订单查询失败";
            return false;
        }

        //网站业务处理
        if ($data['result_code'] === 'SUCCESS') {
            try {
                $pay_order = PayOrder::where('uid', $data['out_trade_no'])->first();

                //判断是否业务已处理
                if ($pay_order->status === 0) {
                    DB::beginTransaction();

                    $pay_order->pay_type = 3; //微信
                    $pay_order->pay_no = $data['transaction_id'];
                    $pay_order->status = 1; //支付成功
                    $pay_order->save();

                    // 支付成功需要处理的业务
                    $pay = new Pay($pay_order);
                    $pay->paySuccess();

                    DB::commit();
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);
                $msg = "业务处理失败";
                return false;
            }
        }

        return true;
    }
}