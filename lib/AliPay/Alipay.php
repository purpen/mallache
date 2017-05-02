<?php
namespace Lib\AliPay;

use Lib\AliPay\lib\AlipayNotify;
use Lib\AliPay\lib\AlipaySubmit;

class Alipay
{
    public function __construct()
    {
        $this->alipay_config = require_once('alipay.config.php');
    }

    //配置文件
    public $alipay_config = [];

    //功能：即时到账交易接口接入页

    /**
     * @param $out_trade_no //商户订单号，商户网站订单系统中唯一订单号，必填
     * @param $subject //订单名称，必填
     * @param $total_fee  //付款金额，必填
     * @param string $body  //商品描述，可空
     * @return string 提交表单HTML文本
     */
    public function alipayApi($out_trade_no, $subject, $total_fee, $body = '')
    {
        //获取配置项
        $alipay_config = $this->alipay_config;

        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service"       => $alipay_config['service'],
            "partner"       => $alipay_config['partner'],
            "seller_id"  => $alipay_config['seller_id'],
            "payment_type"	=> $alipay_config['payment_type'],
            "notify_url"	=> $alipay_config['notify_url'],
            "return_url"	=> $alipay_config['return_url'],

            "anti_phishing_key"=>$alipay_config['anti_phishing_key'],
            "exter_invoke_ip"=>$alipay_config['exter_invoke_ip'],
            "out_trade_no"	=> $out_trade_no,
            "subject"	=> $subject,
            "total_fee"	=> $total_fee,
            "body"	=> $body,
            "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
            //其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.kiX33I&treeId=62&articleId=103740&docType=1
            //如"参数名"=>"参数值"

        );

        //建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");

        return $html_text;
    }

    //异步回调验证
    public function notifyUrl()
    {
        //获取配置项
        $alipay_config = $this->alipay_config;

        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();

        if($verify_result){
            return true;
        }else{
            //验证失败
            return false;
        }
    }

    //异步验证通过
    public function trueNotifyUrl()
    {
        echo "success";
    }
    //异步验证失败
    public function flaseNotifyUrl()
    {
        echo "fail";
    }
}