<?php
namespace com\jdjr\pay\demo\action;
use com\jdjr\pay\demo\common\ConfigUtil;
use com\jdjr\pay\demo\common\HttpUtils;
use com\jdjr\pay\demo\common\XMLUtil;
include '../common/ConfigUtil.php';
include '../common/HttpUtils.php';
include '../common/XMLUtil.php';
class PaymentCodePay{
	public function execute(){
		
		$param["token"]=$_POST["token"];
		$param["version"]=$_POST["version"];	
		$param["merchant"]=$_POST["merchant"];		
		$param["device"]=$_POST["device"];		
		$param["tradeNum"]=$_POST["tradeNum"];		
		$param["tradeName"]=$_POST["tradeName"];		
		$param["tradeDesc"]=$_POST["tradeDesc"];		
		$param["tradeTime"]=$_POST["tradeTime"];		
		$param["amount"]=$_POST["amount"];		
		$param["industryCategory"]=$_POST["industryCategory"];		
		$param["currency"]=$_POST["currency"];		
		$param["note"]=$_POST["note"];		
		$param["notifyUrl"]=$_POST["notifyUrl"];		
		$param["orderGoodsNum"]=$_POST["orderGoodsNum"];		
		$param["vendorId"]=$_POST["vendorId"];		
		$param["goodsInfoList"]=$_POST["goodsInfoList"];		
		$param["receiverInfo"]=$_POST["receiverInfo"];		
		$param["termInfo"]=$_POST["termInfo"];		
		$param["payMerchant"]=$_POST["payMerchant"];		
		$param["riskInfo"]=$_POST["riskInfo"];
		$reqXmlStr = XMLUtil::encryptReqXml($param);
		$url = ConfigUtil::get_val_by_key("fkmPayUrl");
		//echo "请求地址：".$url;
		//echo "----------------------------------------------------------------------------------------------";
		$httputil = new HttpUtils();
		list ( $return_code, $return_content )  = $httputil->http_post_data($url, $reqXmlStr);
		//echo $return_content."\n";
		$resData;
		$flag=XMLUtil::decryptResXml($return_content,$resData);
		//echo var_dump($resData);
		if($flag){
			
			$_SESSION["resultData"]=$resData;
			header("location:../page/paymentCodePayResult.php");
		}else{
			echo "验签失败";
		}
	}
	
}
error_reporting(0);
$m = new PaymentCodePay();
$m->execute();
?>