<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\ItemStatusEvent;
use App\Events\PayOrderEvent;
use App\Helper\Tools;
use App\Http\Transformer\PayOrderTransformer;
use App\Models\Item;
use App\Models\PayOrder;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Lib\AliPay\Alipay;
use Lib\JdPay\JdPay;
use Lib\WxPay\PayNotifyCallBack;
use Lib\WxPay\WxPay;
use Qiniu\Http\Request;

class PayController extends BaseController
{
    // 发布需求保证金---交易名称
    protected $demand_pay_title = '发布需求保证金';

    // 发布需求保证金---交易描述信息
    protected $demand_pay_content = '';
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
        //验证是否是需求用户
        if($this->auth_user->type != 1){
            return $this->response->array($this->apiError('设计公司不能发布项目',403));
        }
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

        $uid = Tools::orderId($this->auth_user_id);

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
                "status": 1,                //状态：-1.关闭；0.未支付；1.支付成功；2.退款
                "summary": "发布需求保证金",  //备注
                "pay_type": 1,              //支付方式；1.自平台；2.支付宝；3.微信；4：京东；5.银行转账
                "pay_no": "2017042621001004550211582926",  //平台交易号
     *          "item_name": ""  //项目名称
     *          "company_name": "公司名称"  //公司名称
     *          "created_at": {
                    "date": "2017-04-26 16:24:21.000000",
                    "timezone_type": 3,
                    "timezone": "Asia/Shanghai"
                }
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
                "status": 1,                //状态：-1.关闭；0.未支付；1.支付成功；2.退款；
                "summary": "发布需求保证金",  //备注
                "pay_type": 1,              //支付方式；1.自平台；2.支付宝；3.微信；4：京东；5.银行转账
                "pay_no": "2017042621001004550211582926",  //平台交易号
     *          "amount"：123， //应支付金额
     *          "total": 22, //总金额
     *          "item_name": ""  //项目名称
     *          "company_name": "公司名称"  //公司名称
     *          "first_pay": 11, //已支付金额
     *      }
     *  }
     */
    public function endPayOrder($item_id)
    {

        $pay_order = PayOrder::where(['item_id' => $item_id, 'type' => 2])->first();
        if($pay_order){
            return $this->response->item($pay_order, new PayOrderTransformer)->setMeta($this->apiMeta());
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



        return $this->response->item($pay_order, new PayOrderTransformer)->setMeta($this->apiMeta());
    }

    /**
     * @api {get} /pay/itemAliPay/{pay_order_id} 支付项目尾款-支付宝
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

    /**
     * @api {get} /pay/itemBankPay/{pay_order_id} 支付项目尾款--公对公银行转账
     * @apiVersion 1.0.0
     * @apiName pay itemBankPay
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
     *  }
     */
    public function itemBankPay($pay_order_id)
    {
        $pay_order = PayOrder::find((int)$pay_order_id);
        if(!$pay_order || $pay_order->user_id != $this->auth_user_id){
            return $this->response->array($this->apiError('无操作权限', 403));
        }

        $pay_order->pay_type = 5;
        $pay_order->save();

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {get} /pay/demandWxPay 发布需求保证金支付-微信
     * @apiVersion 1.0.0
     * @apiName pay demandWxPay
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
     *          'code_url': ""  //二维码链接
     *      }
     *  }
     */
    public function demandWxPay()
    {
        //验证是否是需求用户
        if($this->auth_user->type != 1){
            return $this->response->array($this->apiError('设计公司不能发布项目',403));
        }
        //总金额
        $total_fee = config('constant.item_price');

        $pay_order = $this->createPayOrder('发布需求保证金', $total_fee);

        $wx_pay = new WxPay();
        $code_url = $wx_pay->wxPayApi('发布需求保证金', $pay_order->uid, $total_fee, 1);

        return $this->response->array($this->apiSuccess('Success', 200, compact('code_url')));
    }

    /**
     * @api {get} /pay/itemWxPay/{pay_order_id} 支付项目尾款-微信
     * @apiVersion 1.0.0
     * @apiName pay itemWxPay
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
     *          'code_url': ""  //二维码链接
     *      }
     *  }
     */
    public function itemWxPay($pay_order_id)
    {
        $pay_order = PayOrder::find((int)$pay_order_id);
        if(!$pay_order || $pay_order->user_id != $this->auth_user_id){
            return $this->response->array($this->apiError('无操作权限', 403));
        }

        $wx_pay = new WxPay();
        $code_url = $wx_pay->wxPayApi($pay_order->summary, $pay_order->uid, $pay_order->amount, 1);

        return $this->response->array($this->apiSuccess('Success', 200, compact('code_url')));
    }

    /**
     * @api {post} /pay/wxPayNotify  微信异步回调接口
     * @apiVersion 1.0.0
     * @apiName pay wxPayNotify
     * @apiGroup pay
     */
    public function wxPayNotify()
    {
        /*【小提示】微信sdk NotifyProcess()方法 中有回调时处理付款状态等业务的代码*/
        $notify = new PayNotifyCallBack();
        $notify->Handle(false);
    }

    /**
     * @api {get} /pay/demandJdPay 发布需求保证金支付-京东
     * @apiVersion 1.0.0
     * @apiName pay demandJdPay
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
    public function demandJdPay()
    {
        //验证是否是需求用户
        if($this->auth_user->type != 1){
            return $this->response->array($this->apiError('设计公司不能发布项目',403));
        }
        //总金额
        $total_fee = config('constant.item_price');

        $pay_order = $this->createPayOrder('发布需求保证金', $total_fee);

        $jdpay = new JdPay();
        $html_text = $jdpay->payApi($pay_order->uid, $total_fee, '发布需求保证金');

        return $this->response->array($this->apiSuccess('Success', 200, compact('html_text')));
    }

    //京东支付异步回调接口
    public function jdPayNotify()
    {
        $jdpay = new JdPay();
        $resData = $jdpay->asynNotify();
        if($resData && 000000 == $resData['result']['code']){
            try{
                // 支付单号
                $uid = $resData["result"]['tradeNum'];
                // 交易类型 0-消费 1-退款
                $trade_type =$resData['result']['tradeType'];

                if($trade_type == 0){
                    $pay_order = PayOrder::where('uid', $uid)->first();
                    //判断是否业务已处理
                    if($pay_order->status === 0){
                        $pay_order->pay_type = 4; //京东
                        $pay_order->pay_no = $uid;
                        $pay_order->status = 1; //支付成功
                        $pay_order->save();

                        event(new PayOrderEvent($pay_order));
                    }
                    echo "ok";
                }else{
                    //退款
                }
            }
            catch (\Exception $e){
                Log::error($e);
                return;
            }
        }else{
            echo "error";
        }
    }

    /**
     * @api {get} /pay/itemJdPay/{pay_order_id} 支付项目尾款-京东
     * @apiVersion 1.0.0
     * @apiName pay itemJdPay
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
    public function itemJdPay($pay_order_id)
    {
        $pay_order = PayOrder::find((int)$pay_order_id);
        if(!$pay_order || $pay_order->user_id != $this->auth_user_id){
            return $this->response->array($this->apiError('无操作权限', 403));
        }

        $jdpay = new JdPay();
        $html_text = $jdpay->payApi($pay_order->uid, $pay_order->amount, $pay_order->summary);

        return $this->response->array($this->apiSuccess('Success', 200, compact('html_text')));
    }

}

