<?php
namespace Lib\YunPianSdk\Lib;

use Lib\YunPianSdk\Yunpian;

/**
 * 语音发送
 *
 * @User llh
 */

class VoiceOperator
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

    public function encrypt(&$data)
    {

    }

    public function send($data=array())
    {
        if (!array_key_exists('mobile', $data))
            return new Result($error = 'mobile 为空');
        if (!array_key_exists('code', $data))
            return new Result($error = 'code 为空');
        $data['apikey'] = $this->apikey;
        return HttpUtil::PostCURL($this->yunpian_config['URI_SEND_VOICE_SMS'], $data);
    }

    public function pull_status($data=array())
    {
        $data['apikey'] = $this->apikey;
        return HttpUtil::PostCURL($this->yunpian_config['URI_PULL_VOICE_STATUS'], $data);
    }
}
