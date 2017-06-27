<?php

namespace Lib\JdPay;

use Illuminate\Support\Facades\Log;
use Lib\JdPay\common\ConfigUtil;
use Lib\JdPay\common\SignUtil;
use Lib\JdPay\common\TDESUtil;
use Lib\JdPay\common\XMLUtil;

class JdPay
{
    /**
     * 京东支付api
     *
     * @param string $tradeNum 订单ID
     * @param float $amount
     * @param string $tradeName
     * @param string $tradeDesc
     * @return string
     */
    public function payApi(string $tradeNum, float $amount, string $tradeName, string $tradeDesc = '')
    {
        $param = [];
        $param["version"] = 'V2.0';
        $param["merchant"] = ConfigUtil::get_val_by_key('merchantNum');
        $param["device"] = '';
        $param["tradeNum"] = $tradeNum;    // 商户提供的唯一交易流水号
        $param["tradeName"] = $tradeName;  // 商户提供的订单的标题/商品名称/关键字等
        $param["tradeDesc"] = $tradeDesc;  // 商品描述
        $param["tradeTime"] = date('YmdHis');
        $param["amount"] = (string)((int)($amount * 100));            // 商户提供的订单的资金总额，单位：分，大于0。
        $param["currency"] = 'CNY';             // 货币类型，固定填CNY
        $param["note"] = '';        // 商户备注信息
        $param["callbackUrl"] = ConfigUtil::get_val_by_key('callbackUrl');  // 支付成功后跳转的URL
        $param["notifyUrl"] = ConfigUtil::get_val_by_key('notifyUrl');      // 支付完成后，异步通知商户服务相关支付结果
        $param["ip"] = '';
        $param["userType"] = '';
        $param["userId"] = '';
        $param["expireTime"] = '';
        $param["orderType"] = '1';             // 0-实物，1-虚拟
        $param["industryCategoryCode"] = '';  // 订单业务类型
        $param["specCardNo"] = '';
        $param["specId"] = '';
        $param["specName"] = '';


        $unSignKeyList = array ("sign");
        $desKey = ConfigUtil::get_val_by_key("desKey");
        $sign = SignUtil::signWithoutToHex($param, $unSignKeyList);
        //echo $sign."<br/>";
        $param["sign"] = $sign;
        $keys = base64_decode($desKey);

        if($param["device"] != null && $param["device"]!=""){
            $param["device"]=TDESUtil::encrypt2HexStr($keys, $param["device"]);
        }
        $param["tradeNum"]=TDESUtil::encrypt2HexStr($keys, $param["tradeNum"]);
        if($param["tradeName"] != null && $param["tradeName"]!=""){
            $param["tradeName"]=TDESUtil::encrypt2HexStr($keys, $param["tradeName"]);
        }
        if($param["tradeDesc"] != null && $param["tradeDesc"]!=""){
            $param["tradeDesc"]=TDESUtil::encrypt2HexStr($keys, $param["tradeDesc"]);
        }

        $param["tradeTime"]=TDESUtil::encrypt2HexStr($keys, $param["tradeTime"]);
        $param["amount"]=TDESUtil::encrypt2HexStr($keys, $param["amount"]);
        $param["currency"]=TDESUtil::encrypt2HexStr($keys, $param["currency"]);
        $param["callbackUrl"]=TDESUtil::encrypt2HexStr($keys, $param["callbackUrl"]);
        $param["notifyUrl"]=TDESUtil::encrypt2HexStr($keys, $param["notifyUrl"]);
        $param["ip"]=TDESUtil::encrypt2HexStr($keys, $param["ip"]);



        if($param["note"] != null && $param["note"]!=""){
            $param["note"]=TDESUtil::encrypt2HexStr($keys, $param["note"]);
        }
        if($param["userType"] != null && $param["userType"]!=""){
            $param["userType"]=TDESUtil::encrypt2HexStr($keys, $param["userType"]);
        }
        if($param["userId"] != null && $param["userId"]!=""){
            $param["userId"]=TDESUtil::encrypt2HexStr($keys, $param["userId"]);
        }
        if($param["expireTime"] != null && $param["expireTime"]!=""){
            $param["expireTime"]=TDESUtil::encrypt2HexStr($keys, $param["expireTime"]);
        }
        if($param["orderType"] != null && $param["orderType"]!=""){
            $param["orderType"]=TDESUtil::encrypt2HexStr($keys, $param["orderType"]);
        }
        if($param["industryCategoryCode"] != null && $param["industryCategoryCode"]!=""){
            $param["industryCategoryCode"]=TDESUtil::encrypt2HexStr($keys, $param["industryCategoryCode"]);
        }
        if($param["specCardNo"] != null && $param["specCardNo"]!=""){
            $param["specCardNo"]=TDESUtil::encrypt2HexStr($keys, $param["specCardNo"]);
        }
        if($param["specId"] != null && $param["specId"]!=""){
            $param["specId"]=TDESUtil::encrypt2HexStr($keys, $param["specId"]);
        }
        if($param["specName"] != null && $param["specName"]!=""){
            $param["specName"]=TDESUtil::encrypt2HexStr($keys, $param["specName"]);
        }

        // 京东支付api
        $payUrl = ConfigUtil::get_val_by_key('serverPayUrl');

        return $this->buildRequestFrom($payUrl, $param);
    }

    //构建返回的提交表单
    protected function buildRequestFrom($url, $param)
    {
        $sHtml = "<form id='batchForm' name='batchForm' action='". $url ."' method='post'>";
        foreach ($param as $key => $val){
            $sHtml.= "<input type='text' name='" . $key . "' value='".$val."'/>";
        }
        $sHtml = $sHtml."</form>";

        $sHtml = $sHtml."<script>document.forms['batchForm'].submit();</script>";
        return $sHtml;
    }

    // 京东服务器异步回调处理方法
    public function asynNotify()
    {
        $xml = file_get_contents("php://input");
//        Log::info($xml);
        $falg = XMLUtil::decryptResXml($xml, $resdata);

        if($falg){
            return $resdata;
        }else{
            return false;
        }
    }

    // 解码京东回调参数
    public static function deStr($str)
    {
        $desKey = ConfigUtil::get_val_by_key("desKey");
        $keys = base64_decode($desKey);

        return TDESUtil::decrypt4HexStr($keys, $_POST["tradeNum"]);
    }

}