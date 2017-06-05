<?php
namespace com\jdjr\pay\demo\action;

use com\jdjr\pay\demo\common\ConfigUtil;
use com\jdjr\pay\demo\common\SignUtil;
use com\jdjr\pay\demo\common\TDESUtil;
include '../common/ConfigUtil.php';
include '../common/SignUtil.php';
include '../common/TDESUtil.php';
class CustomerClientOrder{
	public function execute(){
		$basePayOrderInfo;
		$basePayOrderInfo["version"]=$_POST["version"];;
		$basePayOrderInfo["merchant"]=$_POST["merchant"];
		$basePayOrderInfo["device"]=$_POST["device"];
		$basePayOrderInfo["tradeNum"]=$_POST["tradeNum"];
		$basePayOrderInfo["tradeName"]=$_POST["tradeName"];
		$basePayOrderInfo["tradeDesc"]=$_POST["tradeDesc"];
		$basePayOrderInfo["tradeTime"]=$_POST["tradeTime"];
		$basePayOrderInfo["amount"]=$_POST["amount"];
		$basePayOrderInfo["currency"]=$_POST["currency"];
		$basePayOrderInfo["note"]=$_POST["note"];
		$basePayOrderInfo["callbackUrl"]=$_POST["callbackUrl"];
		$basePayOrderInfo["notifyUrl"]=$_POST["notifyUrl"];
		$basePayOrderInfo["ip"]=$_POST["ip"];
		$basePayOrderInfo["expireTime"]=$_POST["expireTime"];
		$basePayOrderInfo["orderType"]=$_POST["orderType"];
		$basePayOrderInfo["industryCategoryCode"]=$_POST["industryCategoryCode"];
		$basePayOrderInfo["tradeType"]=$_POST["tradeType"];
		$basePayOrderInfo["payMerchant"]=$_POST["payMerchant"];
		$basePayOrderInfo["riskInfo"]=$_POST["riskInfo"];
		
		$oriUrl = $_POST["saveUrl"];
		$desKey = ConfigUtil::get_val_by_key("desKey");
		$keys = base64_decode($desKey);
		$unSignKeyList = array ("sign");
		$sign = SignUtil::signWithoutToHex($basePayOrderInfo, $unSignKeyList);
		$basePayOrderInfo["sign"] = $sign;
		
		$basePayOrderInfo["tradeNum"]=TDESUtil::encrypt2HexStr($keys, $basePayOrderInfo["tradeNum"]);
		$basePayOrderInfo["tradeTime"]=TDESUtil::encrypt2HexStr($keys, $basePayOrderInfo["tradeTime"]);
		$basePayOrderInfo["amount"]=TDESUtil::encrypt2HexStr($keys, $basePayOrderInfo["amount"]);
		$basePayOrderInfo["currency"]=TDESUtil::encrypt2HexStr($keys, $basePayOrderInfo["currency"]);
		$basePayOrderInfo["callbackUrl"]=TDESUtil::encrypt2HexStr($keys, $basePayOrderInfo["callbackUrl"]);
		$basePayOrderInfo["notifyUrl"]=TDESUtil::encrypt2HexStr($keys, $basePayOrderInfo["notifyUrl"]);
		$basePayOrderInfo["ip"]=TDESUtil::encrypt2HexStr($keys, $basePayOrderInfo["ip"]);
		if($basePayOrderInfo["device"] != null && $basePayOrderInfo["device"]!=""){
			$basePayOrderInfo["device"]=TDESUtil::encrypt2HexStr($keys, $basePayOrderInfo["device"]);
		}
		if($basePayOrderInfo["tradeName"] != null && $basePayOrderInfo["tradeName"]!=""){
			$basePayOrderInfo["tradeName"]=TDESUtil::encrypt2HexStr($keys, $basePayOrderInfo["tradeName"]);
		}
		if($basePayOrderInfo["tradeDesc"] != null && $basePayOrderInfo["tradeDesc"]!=""){
			$basePayOrderInfo["tradeDesc"]=TDESUtil::encrypt2HexStr($keys, $basePayOrderInfo["tradeDesc"]);
		}
		if($basePayOrderInfo["note"] != null && $basePayOrderInfo["note"]!=""){
			$basePayOrderInfo["note"]=TDESUtil::encrypt2HexStr($keys, $basePayOrderInfo["note"]);
		}
		if($basePayOrderInfo["expireTime"] != null && $basePayOrderInfo["expireTime"]!=""){
			$basePayOrderInfo["expireTime"]=TDESUtil::encrypt2HexStr($keys, $basePayOrderInfo["expireTime"]);
		}
		if($basePayOrderInfo["orderType"] != null && $basePayOrderInfo["orderType"]!=""){
			$basePayOrderInfo["orderType"]=TDESUtil::encrypt2HexStr($keys, $basePayOrderInfo["orderType"]);
		}
		if($basePayOrderInfo["industryCategoryCode"] != null && $basePayOrderInfo["industryCategoryCode"]!=""){
			$basePayOrderInfo["industryCategoryCode"]=TDESUtil::encrypt2HexStr($keys, $basePayOrderInfo["industryCategoryCode"]);
		}
		if($basePayOrderInfo["cert"] != null && $basePayOrderInfo["cert"]!=""){
			$basePayOrderInfo["cert"]=TDESUtil::encrypt2HexStr($keys, $basePayOrderInfo["cert"]);
		}
		if($basePayOrderInfo["tradeType"] != null && $basePayOrderInfo["tradeType"]!=""){
			$basePayOrderInfo["tradeType"]=TDESUtil::encrypt2HexStr($keys, $basePayOrderInfo["tradeType"]);
		}
		if($basePayOrderInfo["payMerchant"] != null && $basePayOrderInfo["payMerchant"]!=""){
			$basePayOrderInfo["payMerchant"]=TDESUtil::encrypt2HexStr($keys, $basePayOrderInfo["payMerchant"]);
		}
		if($basePayOrderInfo["riskInfo"] != null && $basePayOrderInfo["riskInfo"]!=""){
			$basePayOrderInfo["riskInfo"]=TDESUtil::encrypt2HexStr($keys, $basePayOrderInfo["riskInfo"]);
		}
		$_SESSION['param'] = $basePayOrderInfo;
		$_SESSION['payUrl'] = $oriUrl;
		header("location:../page/customerAutoSubmit.php");
	}

}

error_reporting(0);
$m = new CustomerClientOrder();
$m->execute();
?>