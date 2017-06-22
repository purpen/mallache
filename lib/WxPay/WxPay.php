<?php
namespace Lib\WxPay;

use Lib\WxPay\lib\WxPayApi;
use Lib\WxPay\lib\WxPayUnifiedOrder;
include_once __DIR__ . "/lib/WxPayData.php";

/**
 *
 * 微信扫码支付实现类
 * @author widyhu
 *
 */
class WxPay
{
    /**
     *
     * 生成直接支付url，支付url有效期为2小时,模式二
     * @param UnifiedOrderInput $input
     */
    public function GetPayUrl($input)
    {
        if($input->GetTrade_type() == "NATIVE")
        {
            $result = WxPayApi::unifiedOrder($input);
            return $result;
        }
    }

    /**
     * 微信支付扫码支付
     *
     * @param string $body 商品描述
     * @param string $order_no 商户订单号
     * @param float $amount 订单总金额
     * @param string $product_id  商品ID
     * @return mixed （返回二维码链接，需要自己生成）
     */
    public function wxPayApi(string $body, $order_no, $amount, $product_id)
    {
        $input = new WxPayUnifiedOrder();
        $input->SetBody($body); //商品描述
//        $input->SetAttach("test");  //附加数据，在查询API和支付通知中原样返回，可作为自定义参数使用。
        $input->SetOut_trade_no($order_no);  //商户订单号
        $input->SetTotal_fee($amount); //订单总金额，单位为分
        $input->SetTime_start(date("YmdHis")); //订单生成时间
        $input->SetTime_expire(date("YmdHis", time() + 600));  //订单失效时间
//        $input->SetGoods_tag("test");  //订单优惠标记
        // 异步接收微信支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数。
        $input->SetNotify_url(config('wxpay.notify_url'));
        $input->SetTrade_type("NATIVE");  //交易类型
        $input->SetProduct_id($product_id);  //商品ID

        $result = $this->GetPayUrl($input);
        dd($result);
        $url = $result["code_url"];
        return $url;
    }


}