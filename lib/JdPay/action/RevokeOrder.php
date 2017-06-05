<?php
namespace com\jdjr\pay\demo\action;
use com\jdjr\pay\demo\common\ConfigUtil;
use com\jdjr\pay\demo\common\HttpUtils;
use com\jdjr\pay\demo\common\XMLUtil;
include '../common/ConfigUtil.php';
include '../common/HttpUtils.php';
include '../common/XMLUtil.php';
class RevokeOrder{
	public function execute(){
		
		$param["version"]=$_POST["version"];
		$param["merchant"]=$_POST["merchant"];
		$param["tradeNum"]=$_POST["tradeNum"];
		$param["oTradeNum"]=$_POST["oTradeNum"];
		$param["amount"]=$_POST["amount"];
		$param["tradeTime"]=$_POST["tradeTime"];
		$param["note"]=$_POST["note"];
		$param["currency"]=$_POST["currency"];
		$reqXmlStr = XMLUtil::encryptReqXml($param);
		//echo $reqXmlStr;
		$url = ConfigUtil::get_val_by_key("revokeUrl");
		//echo "请求地址：".$url."<br>";
		//echo "----------------------------------------------------------------------------------------------";
		$httputil = new HttpUtils();
		list ( $return_code, $return_content )  = $httputil->http_post_data($url, $reqXmlStr);
		//echo $return_content."<br>";
		$resData;
		$flag=XMLUtil::decryptResXml($return_content,$resData);
		//echo var_dump($resData);
		if($flag){
				
			$status = $resData['status'];
			if($status=="1"){
				$resData['status']="成功";
			}else if ($status=="2"){
				$resData['status']="失败";
			}
			$_SESSION["refund"]=$resData;
			header("location:../page/refundResult.php");
		}else{
			echo "验签失败";
		}
	}
}
error_reporting(0);
$m = new RevokeOrder();
$m->execute();
?>