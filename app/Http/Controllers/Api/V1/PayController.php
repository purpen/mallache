<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\ItemStatusEvent;
use App\Events\PayOrderEvent;
use App\Http\Transformer\PayOrderTransformer;
use App\Models\Item;
use App\Models\PayOrder;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Lib\AliPay\Alipay;
use Qiniu\Http\Request;

class PayController extends BaseController
{
    /**
     * @api {get} /pay/demandAliPay 发布需求保证金支付-支付宝
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
     *          'html_text': ""
     *      }
     *  }
     */
    public function demandAliPay()
    {
        //总金额
        $total_fee = config('constant.item_price');

        $pay_order = $this->createPayOrder('发布需求保证金', $total_fee);
        $alipay = new Alipay();
        $html_text = $alipay->alipayApi($pay_order->uid, '发布需求保证金', $total_fee);

        return $this->response->array($this->apiSuccess('Success', 200, compact('html_text')));
    }

    /**
     * 创建需求 支付单
     * @param int $type 支付类型：1.预付押金;2.项目款
     * @param float $amount 支付金额
     * @param int $item_id 目标ID
     * @param int $user_id 用户ID
     * @param string $summary 备注
     * @return mixed
     */
    protected function createPayOrder($summary = '', $amount, $type = 1, $item_id = 0)
    {
        $pay_order = PayOrder::where(['type' => $type, 'user_id' => $this->auth_user_id, 'status' => 0, 'item_id' => $item_id])
            ->first();
        if($pay_order){
            return $pay_order;
        }

        $uid = uniqid('zf');

        $pay_order = PayOrder::create([
            'uid' => $uid,
            'user_id' => $this->auth_user_id,
            'type' => $type,
            'summary' => $summary,
            'item_id' => $item_id,
            'amount' => $amount,
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

                    //判断是否业务已处理
                    if($pay_order->status === 0){
                        $pay_order->pay_type = 2; //支付宝
                        $pay_order->pay_no = $trade_no;
                        $pay_order->status = 1; //支付成功
                        $pay_order->save();

                        event(new PayOrderEvent($pay_order));
                    }
                }
                catch (\Exception $e){
                    Log::error($e);
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

    /**
     * @api {get} /pay/getPayStatus/{out_trade_no} 查看支付状态
     * @apiVersion 1.0.0
     * @apiName pay getPayStatus
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
     *          "id": 1,
                "uid": "zf59006e63b3445",  //支付单号
                "user_id": 2,              //用户ID
                "type": 1,                 //支付类型：1.预付押金；2.项目款；
                "item_id": 0,               //项目ID
                "status": 1,                //状态：0.未支付；1.支付成功；
                "summary": "发布需求保证金",  //备注
                "pay_type": 1,              //支付方式；1.自平台；2.支付宝；3.微信；4：京东；5.银行转账
                "pay_no": "2017042621001004550211582926"  //平台交易号
     *      }
     *  }
     */
    public function getPayStatus($uid){
        $pay_order = PayOrder::where('uid', $uid)->first();
        if(!$pay_order){
            return $this->response->array($this->apiError('not found', 404));
        }

        return $this->response->item($pay_order, new PayOrderTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /pay/endPayOrder/{item_id} 创建尾款支付订单
     * @apiVersion 1.0.0
     * @apiName pay endPayOrder
     * @apiGroup pay
     *
     * @apiParam {string} token
     * @apiParam {int} item_id  项目ID
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *      "data": {
     *          "id": 1,
                "uid": "zf59006e63b3445",  //支付单号
                "user_id": 2,              //用户ID
                "type": 1,                 //支付类型：1.预付押金；2.项目款；
                "item_id": 0,               //项目ID
                "status": 1,                //状态：0.未支付；1.支付成功；
                "summary": "发布需求保证金",  //备注
                "pay_type": 1,              //支付方式；1.自平台；2.支付宝；3.微信；4：京东；5.银行转账
                "pay_no": "2017042621001004550211582926",  //平台交易号
     *          "amount"：123， //应支付金额
     *          "total": 22, //总金额
     *          "first_pay": 11, //已支付金额
     *      }
     *  }
     */
    public function endPayOrder($item_id)
    {

        $pay_order = PayOrder::where(['item_id' => $item_id, 'type' => 2])->first();
        if($pay_order){
            return $this->response->item($pay_order, new PayOrderTransformer)->setMeta($this->apiSuccess());
        }

        if(!$item = Item::find($item_id)){
            return $this->response->array("not found item", 404);
        }
        if($item->user_id != $this->auth_user_id || $item->status != 7){
            return $this->response->array($this->apiError("无操作权限", 403));
        }

        //查询项目押金的金额
        $first_pay_order = PayOrder::where([
            'user_id' => $this->auth_user_id,
            'item_id' => $item_id,
            'type' => 1,
            'status' => 1,
        ])->first();

        //计算应付金额
        $price = $item->price - $first_pay_order->amount;

        //支付说明
        $summary = '项目尾款';

        $pay_order = $this->createPayOrder($summary, $price,2, $item_id);

        //修改项目状态为8，等待托管项目金额
        $item->status = 8;
        $item->save();

        event(new ItemStatusEvent($item));

        $pay_order->total_price = $item->price;
        $pay_order->first_pay = $pay_order->amount;



        return $this->response->item($pay_order, new PayOrderTransformer)->setMeta($this->apiSuccess());
    }

    /**
     * @api {get} /pay/itemAliPay/{pay_order_id} 支付宝支付项目尾款-支付宝
     * @apiVersion 1.0.0
     * @apiName pay itemAliPay
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
     *          'html_text': ""
     *      }
     *  }
     */
    public function itemAliPay($pay_order_id)
    {
        $pay_order = PayOrder::find((int)$pay_order_id);
        if(!$pay_order || $pay_order->user_id != $this->auth_user_id){
            return $this->response->array($this->apiError('无操作权限', 403));
        }

        $alipay = new Alipay();
        $html_text = $alipay->alipayApi($pay_order->uid, $pay_order->summary, $pay_order->amount);

        return $this->response->array($this->apiSuccess('Success', 200, compact('html_text')));
    }
}

