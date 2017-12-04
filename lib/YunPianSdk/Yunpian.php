<?php
namespace Lib\YunPianSdk;

use Lib\YunPianSdk\lib\FlowOperator;
use Lib\YunPianSdk\lib\SmsOperator;
use Lib\YunPianSdk\lib\UserOperator;
use Lib\YunPianSdk\lib\VoiceOperator;


/**
 * Use: 云片短信服务商SDK
 */

class Yunpian
{
    /*
     * 云片验证配置
     */
    public $config = [];
    
    /**
     * 初始化接口
     *
     * @return void
     */
    public function __construct()
    {
        $this->config['APIKEY'] = config('yunpian.api_key');
        $this->config['API_SECRET'] = config('yunpian.api_secret');
        $this->config['SMS_HOST'] = config('yunpian.sms_host');
        $this->config['VOICE_HOST'] = config('yunpian.voice_host');
        $this->config['FLOW_HOST'] = config('yunpian.flow_host');
        $this->config['VERSION'] = config('yunpian.version');
        
        // retry times
        $this->config['RETRY_TIMES'] = 5;
        
        // 短信
        $this->config['URI_SEND_SINGLE_SMS'] = $this->config['SMS_HOST'] . $this->config['VERSION'] . "/sms/single_send.json";
        $this->config['URI_SEND_BATCH_SMS'] = $this->config['SMS_HOST'] . $this->config['VERSION'] . "/sms/batch_send.json";
        $this->config['URI_SEND_MULTI_SMS'] = $this->config['SMS_HOST'] . $this->config['VERSION'] . "/sms/multi_send.json";
        $this->config['URI_SEND_TPL_SMS'] = $this->config['SMS_HOST'] . $this->config['VERSION'] . '/sms/tpl_send.json';
        $this->config['URI_PULL_SMS_STATUS'] = $this->config['SMS_HOST'] . $this->config['VERSION'] . "/sms/pull_status.json";
        
        # 获取回复短信
        $this->config['URI_PULL_SMS_REPLY'] = $this->config['SMS_HOST'] . $this->config['VERSION'] . "/sms/pull_reply.json";
        
        # 查询回复短信
        $this->config['URI_GET_SMS_REPLY'] = $this->config['SMS_HOST'] . $this->config['VERSION'] . "/sms/get_reply.json";
        
        # 查短信发送记录
        $this->config['URI_GET_SMS_RECORD'] = $this->config['SMS_HOST'] . $this->config['VERSION'] . "/sms/get_record.json";
        
        // 语音
        $this->config['URI_SEND_VOICE_SMS'] = $this->config['VOICE_HOST'] . $this->config['VERSION'] . "/voice/send.json";
        $this->config['URI_PULL_VOICE_STATUS'] = $this->config['VOICE_HOST'] . $this->config['VERSION'] . "/voice/pull_status.json";
        
        // 流量
        $this->config['URI_GET_FLOW_PACKAGE'] = $this->config['FLOW_HOST'] . $this->config['VERSION'] . "/flow/get_package.json";
        $this->config['URI_PULL_FLOW_STATUS'] = $this->config['FLOW_HOST'] . $this->config['VERSION'] . "/flow/pull_status.json";
        $this->config['URI_RECHARGE_FLOW'] = $this->config['FLOW_HOST'] . $this->config['VERSION'] . "/flow/recharge.json";
        
        // 用户操作
        $this->config['URI_GET_USER_INFO'] = $this->config['SMS_HOST'] . $this->config['VERSION'] . "/user/get.json";
        $this->config['URI_SET_USER_INFO'] = $this->config['SMS_HOST'] . $this->config['VERSION'] . "/user/set.json";
        
        // 模板操作
        $this->config['URI_GET_DEFAULT_TEMPLATE'] = $this->config['SMS_HOST'] . $this->config['VERSION'] . "/tpl/get_default.json";
        $this->config['URI_GET_TEMPLATE'] = $this->config['SMS_HOST'] . $this->config['VERSION'] . "/tpl/get.json";
        $this->config['URI_ADD_TEMPLATE'] = $this->config['SMS_HOST'] . $this->config['VERSION'] . "/tpl/add.json";
        $this->config['URI_UPD_TEMPLATE'] = $this->config['SMS_HOST'] . $this->config['VERSION'] . "/tpl/update.json";
        $this->config['URI_DEL_TEMPLATE'] = $this->config['SMS_HOST'] . $this->config['VERSION'] . "/tpl/del.json";
    }
    
    /**
    *  发送单条短信
     *
    * @param  array $data
    * @return object
    */
    public function sendOneSms($mobile, $text)
    {
//        $data['mobile'] = '13300000000';
//        $data['text'] = '【云片网】您的验证码是1234';
        $smsOperator = new SmsOperator();

        $data['mobile'] = $mobile;
        $data['text'] = $text;
        return $smsOperator->single_send($data);
    }

    /**
     * 发送批量短信
     *
     * @param array $mobile
     * @param array $text
     * @return lib\Result
     */
    public function sendManySms(array $mobile, array $text){

//        $data['mobile'] = '13300000000,13300000001';
//        $data['text'] = '【云片网】您的验证码是1234';
        $smsOperator = new SmsOperator();
        $data['mobile'] = implode(',',$mobile);
        $data['text'] = implode(',',$text);
        return $smsOperator->batch_send($data);
    }
    
    /**
    *  发送个性化短信
    *
    * @param  array $data
    * @return object
    */
    public function sendIndividualSms($data = [])
    {
        //$data['mobile'] = '13000000000,13000000001,1,13000000003';
        //$data['text'] = '【云片网】您的验证码是1234,【云片网】您的验证码是6414,【云片网】您的验证码是0099,【云片网】您的验证码是3451';
        $smsOperator = new SmsOperator();
        $result = $smsOperator->multi_send($data);
        return $result;
    }
    
    /**
    *  查询流量包
    * @param  array $data
    * @return object
    */
    public function get_package($data = [])
    {
        $flowOperator = new FlowOperator();
        $result = $flowOperator->get_package($data);
        return $result;
    }
    
    /**
    *  充值流量
    * @param  array $data
    * @return object
    */
    public function recharge($data = [])
    {
        //$data['mobile'] = '15010585725';
        //$data['sn'] = '1008601';
        //$data['callback_url'] = 'http://www.taihuoniao.com';
        $flowOperator = new FlowOperator();
        $result = $flowOperator->recharge($data);
        return $result;
    }
    
    /**
    *  获取状态报告
    * @param  array $data
    * @return object
    */
    public function pull_status($data = [])
    {
        $flowOperator = new FlowOperator();
        $result = $flowOperator->pull_status($data);
        return $result;
    }
    
    /**
    *  发送语音信息
    * @param  array $data
    * @return object
    */
    public function sendVoice($data = [])
    {
        //$data['mobile'] = '15010585725';
        //$data['code'] = '5725';
        //$data['callback_url'] = 'http://www.taihuoniao.com';
        $voiceOperator = new VoiceOperator();
        $result = $voiceOperator->send($data);
        return $result;
    }
    
    /**
    *  获取状态报告
    * @param  array $data
    * @return object
    */
    public function vioceStatus($data = [])
    {
        //$data['page_size'] = 20;
        $voiceOperator = new VoiceOperator();
        $result = $voiceOperator->pull_status($data);
        return $result;
    }
    
    /**
    *  获取用户信息
    * @param  array $data
    * @return object
    */
    public function getUserInfo($data = [])
    {
        $userOperator = new UserOperator();
        $result = $userOperator->get($data);
        return $result;
    }
    
    /**
    *  设置用户信息
    * @param  array $data
    * @return object
    */
    public function setUserInfo($data = [])
    {
        $userOperator = new UserOperator();
        $result = $userOperator->set($data);
        return $result;
    }

}