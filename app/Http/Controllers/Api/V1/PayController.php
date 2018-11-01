<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\ItemStatusEvent;
use App\Events\PayOrderEvent;
use App\Helper\Tools;
use App\Http\Transformer\PayOrderTransformer;
use App\Http\Transformer\PayDesignResultTransformer;
use App\Models\AssetModel;
use App\Models\DesignResult;
use App\Models\Item;
use App\Models\ItemStage;
use App\Models\PayOrder;
use App\Service\Pay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Lib\AliPay\Alipay;
use Lib\JdPay\JdPay;
use Lib\WxPay\PayNotifyCallBack;
use Lib\WxPay\WxPay;

class PayController extends BaseController
{
    /**
     * 创建需求 支付单
     * @param int $type 支付类型：1.预付押金;2.项目款；3.首付款 4.阶段款
     * @param float $amount 支付金额
     * @param int $item_id 项目ID
     * @param int $user_id 用户ID
     * @param string $summary 备注
     * @param int $pay_type 支付方式； 1.自平台；2.支付宝；3.微信；4：京东；5.银行转账
     * @param int $item_stage_id 项目阶段ID
     * @return mixed
     */
    protected function createPayOrder($summary = '', $amount, $type = 1, $item_id = 0, $pay_type = 0, $item_stage_id = null)
    {
        $query = PayOrder::query()
            ->where(['type' => $type, 'user_id' => $this->auth_user_id, 'status' => 0, 'item_id' => $item_id]);

        if ($type == 4 && $item_stage_id) {
            $query = $query->where('item_stage_id', $item_stage_id);
        }

        $pay_order = $query->first();
        if ($pay_order) {
            return $pay_order;
        }

        $item = Item::find($item_id);

        $uid = Tools::orderId($this->auth_user_id);

        $pay_order = PayOrder::query()->create([
            'uid' => $uid,
            'user_id' => $this->auth_user_id,
            'type' => $type,
            'summary' => $summary,
            'item_id' => $item_id,
            'amount' => $amount,
            'pay_type' => $pay_type,
            'item_stage_id' => $item_stage_id,
            'source' => $item->source,  // 添加来源
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
//        Log::info($_POST);
        $alipay = new Alipay();
        //支付成功
        if ($alipay->notifyUrl()) {
            //商户订单号
            $out_trade_no = $_POST['out_trade_no'];

            //支付宝交易号
            $trade_no = $_POST['trade_no'];

            //交易状态
            $trade_status = $_POST['trade_status'];

            //判断是否支付完成
            if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                try {
                    $pay_order = PayOrder::where('uid', $out_trade_no)->first();

                    //判断是否业务已处理
                    if ($pay_order->status == 0) {
                        DB::beginTransaction();
                        $pay_order->pay_type = 2; //支付宝
                        $pay_order->pay_no = $trade_no;
                        $pay_order->status = 1; //支付成功
                        if (!$pay_order->save()) {
                            throw new \Exception("支付宝业务处理失败");
                        }

                        // 支付成功需要处理的业务
                        $pay = new Pay($pay_order);
                        $pay->paySuccess();

                        DB::commit();
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return;
                }
            } //三个月后不可退款状态通知
            elseif ($_POST['trade_status'] == 'TRADE_FINISHED') {

            }

            //处理成功
            $alipay->trueNotifyUrl();
        } //支付失败
        else {
            Log::error('支付验证失败');
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
     * "uid": "zf59006e63b3445",  //支付单号
     * "user_id": 2,              //用户ID
     * "type": 1,                 //支付类型：1.预付押金；2.项目款；
     * "item_id": 0,               //项目ID
     * "status": 1,                //状态：-1.关闭；0.未支付；1.支付成功；2.退款
     * "summary": "发布需求保证金",  //备注
     * "pay_type": 1,              //支付方式；1.自平台；2.支付宝；3.微信；4：京东；5.银行转账
     * "pay_no": "2017042621001004550211582926",  //平台交易号
     *          "item_name": ""  //项目名称
     *          "company_name": "公司名称"  //公司名称
     *          "created_at": {
     * "date": "2017-04-26 16:24:21.000000",
     * "timezone_type": 3,
     * "timezone": "Asia/Shanghai"
     * }
     *      }
     *  }
     */
    public function getPayStatus($uid)
    {
        $pay_order = PayOrder::where('uid', $uid)->first();
        if (!$pay_order) {
            return $this->response->array($this->apiError('not found', 404));
        }

        return $this->response->item($pay_order, new PayOrderTransformer)->setMeta($this->apiMeta());
    }


    /**
     * @api {get} /pay/firstPayOrder/{item_id} 创建首付款支付订单
     * @apiVersion 1.0.0
     * @apiName pay firstPayOrder
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
     *          "uid": "zf59006e63b3445",  //支付单号
     *          "user_id": 2,              //用户ID
     *          "type": 1,                 //支付类型：1.预付押金；2.项目款；3.首付款 4.阶段款
     *          "item_id": 0,               //项目ID
     *          "status": 1,                //状态：-1.关闭；0.未支付；1.支付成功；2.退款；
     *          "summary": "发布需求保证金",  //备注
     *          "pay_type": 1,              //支付方式；1.自平台；2.支付宝；3.微信；4：京东；5.银行转账
     *          "pay_no": "2017042621001004550211582926",  //平台交易号
     *          "amount"：123， //应支付金额
     *          "total": 22, //总金额
     *          "item_name": ""  //项目名称
     *          "company_name": "公司名称"  //公司名称
     *          "first_pay": 11, //已支付金额
     *      }
     *  }
     */
    public function firstPayOrder($item_id)
    {
        // 支付首付款类型
        $pay_type = 3;
        if (!$item = Item::find($item_id)) {
            return $this->response->array("not found item", 404);
        }
        if ($item->user_id != $this->auth_user_id || !in_array($item->status, [7, 8])) {
            return $this->response->array($this->apiError("无操作权限", 403));
        }

        // 合同
        $contract = $item->contract;

        // 合同不存在或合同版本不正确
        if (!$contract || $contract->version != 1) {
            return $this->response->array($this->apiError("not found", 404));
        }

        $pay_order = PayOrder::where(['item_id' => $item_id, 'type' => $pay_type])->where('status', '!=', -1)->first();

        if ($pay_order) {
            $pay_order->total_price = $contract->total;
            return $this->response->item($pay_order, new PayOrderTransformer)->setMeta($this->apiMeta());
        }

        //查询项目押金的金额(兼容历史数据)
        $first_pay_order = PayOrder::query()->where([
            'user_id' => $this->auth_user_id,
            'item_id' => $item_id,
            'type' => 1,
            'status' => 1,
        ])->first();
        // 当存在项目发布押金时
        if ($first_pay_order) {
            //计算应付金额
            $price = bcsub($contract->first_payment, $first_pay_order->amount, 2);
            $first_pay = $first_pay_order->amount;
        } else {
            $price = $contract->first_payment;
            $first_pay = 0;
        }


        //支付说明
        $summary = '项目首付款';

        $pay_order = $this->createPayOrder($summary, $price, $pay_type, $item_id);
        $pay_order->total_price = $contract->total;

        //修改项目状态为8，等待支付首付款
        $item->status = 8;
        $item->save();

        event(new ItemStatusEvent($item));

        return $this->response->item($pay_order, new PayOrderTransformer)->setMeta($this->apiMeta());
    }


    /**
     * @api {get} /pay/stagePayOrder/{item_stage_id} 创建项目阶段支付单
     * @apiVersion 1.0.0
     * @apiName pay stagePayOrder
     * @apiGroup pay
     *
     * @apiParam {string} token
     * @apiParam {int} item_stage_id  项目阶段ID
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *      "data": {
     *          "id": 1,
     *          "uid": "zf59006e63b3445",  //支付单号
     *          "user_id": 2,              //用户ID
     *          "type": 1,                 //支付类型：1.预付押金；2.项目款；3.首付款 4.阶段款
     *          "item_id": 0,               //项目ID
     *          "status": 1,                //状态：-1.关闭；0.未支付；1.支付成功；2.退款；
     *          "summary": "发布需求保证金",  //备注
     *          "pay_type": 1,              //支付方式；1.自平台；2.支付宝；3.微信；4：京东；5.银行转账
     *          "pay_no": "2017042621001004550211582926",  //平台交易号
     *          "amount"：123， //应支付金额
     *          "total": 22, //总金额
     *          "item_name": ""  //项目名称
     *          "company_name": "公司名称"  //公司名称
     *          "first_pay": 11, //已支付金额
     *          "item_stage_id": 1, // 项目阶段ID
     *      }
     *  }
     */
    public function createItemStagePayOrder($item_stage_id)
    {
        // 支付项目阶段款类型
        $pay_type = 4;

        $pay_order = PayOrder::where(['item_stage_id' => $item_stage_id, 'type' => $pay_type])->where('status', '!=', -1)->first();
        if ($pay_order) {
            return $this->response->item($pay_order, new PayOrderTransformer)->setMeta($this->apiMeta());
        }


        if (!$item_stage = ItemStage::find($item_stage_id)) {
            return $this->response->array($this->apiError("not found item stage", 404));
        }

        if (!$item = $item_stage->item) {
            return $this->response->array($this->apiError("not found item", 404));
        }

        if ($item->user_id != $this->auth_user_id || $item_stage->pay_status == 1) {
            return $this->response->array($this->apiError("无操作权限", 403));
        }

        //支付说明
        $summary = '项目阶段款';

        $pay_order = $this->createPayOrder($summary, $item_stage->amount, $pay_type, $item->id, 0, (int)$item_stage_id);

        return $this->response->item($pay_order, new PayOrderTransformer)->setMeta($this->apiMeta());
    }


    /**
     * @api {get} /pay/itemAliPay/{pay_order_id} 支付项目款-支付宝
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
        if (!$pay_order || $pay_order->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('无操作权限', 403));
        }

        $pay_order->pay_type = 2;  // 支付宝
        $pay_order->save();

        $alipay = new Alipay();
        $html_text = $alipay->alipayApi($pay_order->uid, $pay_order->summary, $pay_order->amount);

        return $this->response->array($this->apiSuccess('Success', 200, compact('html_text')));
    }

    /**
     * @api {get} /pay/itemBankPay/{pay_order_id} 支付项目款--公对公银行转账
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
        if (!$pay_order || $pay_order->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('无操作权限', 403));
        }

        $pay_order->pay_type = 5;
        $pay_order->save();

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {get} /pay/itemWxPay/{pay_order_id} 支付项目款-微信
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
        if (!$pay_order || $pay_order->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('无操作权限', 403));
        }

        $pay_order->pay_type = 3; // 微信
        $pay_order->save();

        $wx_pay = new WxPay();
        $code_url = $wx_pay->wxPayApi($pay_order->summary, $pay_order->uid, $pay_order->amount * 100, 1);

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


    /*array (
  'version' => 'V2.0',
  'merchant' => '22294531',
  'result' =>
  array (
    'code' => '000000',
    'desc' => 'success',
  ),
  'tradeNum' => '2017060516592500000002479561',
  'tradeType' => '0',
  'sign' => 'U5DwNh9atht6f8JfZu3iSUJQ4BWafc5v4Fg0JzVEtu4E07VO9+RDhwxB66vHkFPi0D7JTBTCiPhlddmu+YctZdH9Tc/LHJz74k1NA3zLUE8htCDs9iT4hhaAHMRM1+v3MkqQ42+CxxXJnq/W1/34hbSB/8WTs3nNVUGVbimE2fE=',
  'amount' => '1',
  'status' => '2',
  'payList' =>
  array (
    'pay' =>
    array (
      'payType' => '0',
      'amount' => '1',
      'currency' => 'CNY',
      'tradeTime' => '20170606104119',
      'detail' =>
      array (
        'cardHolderName' => '*梁恒',
        'cardHolderMobile' => '186****3221',
        'cardHolderType' => '0',
        'cardHolderId' => '****1733',
        'cardNo' => '621483****3605',
        'bankCode' => 'CMB',
        'cardType' => '1',
      ),
    ),
  ),
)*/
    //京东支付异步回调接口
    public function jdPayNotify()
    {
//        Log::info($_SERVER);
        $jdpay = new JdPay();
        $resData = $jdpay->asynNotify();
        if ($resData && 000000 == $resData['result']['code']) {
//            Log::info($resData);
            try {
                // 支付单号
                $uid = $resData['tradeNum'];
                // 交易类型 0-消费 1-退款
                $trade_type = $resData['tradeType'];

                if ($trade_type == 0) {
                    $pay_order = PayOrder::where('uid', $uid)->first();
                    //判断是否业务已处理
                    if ($pay_order->status === 0) {
                        DB::beginTransaction();
                        $pay_order->pay_type = 4; //京东
                        $pay_order->pay_no = $uid;
                        $pay_order->status = 1; //支付成功
                        $pay_order->save();

                        // 支付成功需要处理的业务
                        $pay = new Pay($pay_order);
                        $pay->paySuccess();
                        DB::commit();
                    }
                    echo "ok";
                } else {
                    //退款
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);
                return;
            }
        } else {
            echo "error";
        }
    }

    /**
     * @api {get} /pay/itemJdPay/{pay_order_id} 支付项目款-京东
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
        if (!$pay_order || $pay_order->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('无操作权限', 403));
        }

        $pay_order->pay_type = 4; // 京东
        $pay_order->save();

        $jdpay = new JdPay();
        $html_text = $jdpay->payApi($pay_order->uid, $pay_order->amount, $pay_order->summary);

        return $this->response->array($this->apiSuccess('Success', 200, compact('html_text')));
    }


    /**
     * @api {put} /pay/bankTransfer/{pay_order_id} 银行转账凭证上传确认
     * @apiVersion 1.0.0
     * @apiName pay bankTransfer
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
    public function bankTransfer($pay_order_id)
    {
        $pay_order = PayOrder::where(['id' => $pay_order_id, 'pay_type' => 5, 'status' => 0])->first();  // 查询银行转账的支付单
        if (!$pay_order || $pay_order->user_id != $this->auth_user_id) {
            return $this->response->array($this->apiError('无操作权限', 403));
        }

        $count = AssetModel::where(['type' => 33, 'target_id' => $pay_order_id])->count();
        if ($count > 0) {
            $pay_order->bank_transfer = 1; // 已上传用户凭证
            $pay_order->save();
            return $this->response->array($this->apiSuccess());
        } else {
            return $this->response->array($this->apiError('未上传转账凭证附件!', 403));
        }

    }

    /**
     * @api {get} /pay/designResults/{design_result_id} 创建设计成果支付订单
     * @apiVersion 1.0.0
     * @apiName payDesignResults
     * @apiGroup pay
     *
     * @apiParam {string} token
     * @apiParam {int} design_result_id  设计成果ID
     *
     * @apiSuccessExample 成功响应:
     *   {
     *      "meta": {
     *          "message": "Success",
     *          "status_code": 200
     *      }
     *      "data": {
     *          "id": 1,
     *          "uid": "zf59006e63b3445",  //支付单号
     *          "user_id": 2,              //用户ID
     *          "type": 1,                 //支付类型：1.预付押金；2.项目款；3.首付款 4.阶段款 5.设计成果
     *          "item_id": 0,               //项目ID
     *          "status": 1,                //状态：-1.关闭；0.未支付；1.支付成功；2.退款；
     *          "summary": "发布需求保证金",  //备注
     *          "pay_type": 1,              //支付方式；1.自平台；2.支付宝；3.微信；4：京东；5.银行转账
     *          "pay_no": "2017042621001004550211582926",  //平台交易号
     *          "amount"：123， //应支付金额
     *          "total": 22, //总金额
     *          "item_name": ""  //项目名称
     *          "company_name": "公司名称"  //公司名称
     *          "first_pay": 11, //已支付金额
     *      }
     *  }
     */
    public function payDesignResults($design_result_id)
    {
        // 设计成果类型
        $type = 5;
        $design_result = DesignResult::find($design_result_id);
        if (!$design_result) {
            return $this->response->array('设计成果不存在',404);
        }
        if ($design_result->status != 3) {
            return $this->response->array('设计成果未上架',403);
        }
        if ($design_result->sell > 0) {
            return $this->response->array('设计成果已出售',403);
        }

        //支付说明
        $summary = '设计成果付款';

        $pay_order = $this->designResultsPayOrder($summary, $design_result->price, $type, 0, $design_result_id);
        $pay_order->total_price = $design_result->price;
        return $this->response->item($pay_order, new PayDesignResultTransformer)->setMeta($this->apiMeta());
    }

    /**
     * 创建设计成果支付单
     * @param int $type 支付类型：1.预付押金;2.项目款；3.首付款 4.阶段款
     * @param float $amount 支付金额
     * @param int $item_id 项目ID
     * @param int $user_id 用户ID
     * @param string $summary 备注
     * @param int $pay_type 支付方式； 1.自平台；2.支付宝；3.微信；4：京东；5.银行转账
     * @param int $item_stage_id 项目阶段ID
     * @return mixed
     */
    protected function designResultsPayOrder($summary = '',$amount,$type = 1,$pay_type = 0,$design_result_id = 0)
    {
        $pay_order = PayOrder::query()->where([
            'type' => $type,
            'user_id' => $this->auth_user_id,
            'status' => 0,
            'design_result_id' => $design_result_id])
            ->first();
        if ($pay_order) {
            return $pay_order;
        }
        $uid = Tools::orderId($this->auth_user_id);
        $pay_order = PayOrder::query()->create([
            'uid' => $uid,
            'user_id' => $this->auth_user_id,
            'type' => $type,
            'summary' => $summary,
            'item_id' => 0,
            'amount' => $amount,
            'pay_type' => $pay_type,
            'design_result_id' => $design_result_id,
            'source' => 0,  // 添加来源
        ]);
        return $pay_order;
    }

}

