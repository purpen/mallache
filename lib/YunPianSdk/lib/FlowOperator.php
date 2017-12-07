<?php
namespace Lib\YunPianSdk\lib;

use Lib\YunPianSdk\Yunpian;

/**
 * Use: 流量发送方式
 */

class FlowOperator
{
    public $apikey;
    public $api_secret;
    public $yunpian_config;

    public function __construct($apikey = null, $api_secret = null)
    {
        $yunpian = new Yunpian();
        $this->yunpian_config = $yunpian->config;
        if ($api_secret == null)
            $this->api_secret = $this->yunpian_config['API_SECRET'];
        else
            $this->api_secret = $api_secret;
        if ($apikey == null)
            $this->apikey = $this->yunpian_config['APIKEY'];
        else
            $this->apikey = $apikey;
    }
    
    public static function decrypt(&$data)
    {
        // decrypt
    }
    
    // 查询流量包
    public function get_package($data=array())
    {
        $data['apikey'] = $this->apikey;
        return HttpUtil::PostCURL($this->yunpian_config['URI_GET_FLOW_PACKAGE'], $data);
    }
    
    // 获取状态报告
    public function pull_status($data=array())
    {
        $data['apikey'] = $this->apikey;
        return HttpUtil::PostCURL($this->yunpian_config['URI_PULL_FLOW_STATUS'], $data);
    }
    
    // 充值流量
    public function recharge($data=array())
    {
        if (!array_key_exists('mobile', $data))
            return new Result(null,$data,null,$error = 'mobile 为空');

        $data['apikey'] = $this->apikey;
        return HttpUtil::PostCURL($this->yunpian_config['URI_RECHARGE_FLOW'], $data);
    }
}
