<?php
/**
 * Created by PhpStorm.
 * User: cailiguang
 * Date: 2018/10/10
 * Time: 下午3:18
 */
namespace App\Http\Controllers\Api\Wx;


use App\Helper\Tools;
use Illuminate\Http\Request;

class BaiDuVoiceController extends BaseController
{
    /**
     * @api {post} /wechat/voice  语音转文字
     * @apiVersion 1.0.0
     * @apiName WxVoice voice
     * @apiGroup Wx
     *
     * @apiParam {file} file 文件  只支持wav格式的文件
     *
     * @apiSuccessExample 成功响应:
    {
    "meta": {
    "message": "Success",
    "status_code": 200
    },
    "data": {
    "corpus_no": "6610636590409593688",
    "err_msg": "success.",
    "err_no": 0,
    "result": [
    "北京科技馆"  //转换的文字
    ],
    "sn": "174349361691539158772"
    }
    }
     */
    public function voice(Request $request)
    {
        if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
            return $this->response->array($this->apiError('上传失败', 412));
        }
        $file = $request->file('file');
        $filePath = $file->getRealPath();
        //文件记录表保存
        $fileName = $file->getClientOriginalName();
        $file_type = explode('.', $fileName);
        $mime = $file_type[1];
        if(!in_array($mime , ["wav"])){
            return $this->response->array($this->apiError('文件格式不对', 412));


        }
        //填写网页上申请的appkey 如 $apiKey="g8eBUMSokVB1BHGmgxxxxxx"
        $apiKey = config('baiduapi.yai.api_key');
        //填写网页上申请的APP SECRET 如 $secretKey="94dc99566550d87f8fa8ece112xxxxx"
        $secretKey = config('baiduapi.yai.secret_key');
        //需要识别的文件
        $audio_file = $filePath;
        //文件格式
        $format = "wav"; // 文件后缀 pcm/wav/amr
        //根据文档填写PID，选择语言及识别模型
        $dev_pid = 1536; //  1537 表示识别普通话，使用输入法模型。1536表示识别普通话，使用搜索模型
        $cuid = Tools::microsecondUniqueStr();
        //采样率
        $rate = 16000;  // 固定值
        /** 公共模块获取token开始 */
        $auth_url = "https://openapi.baidu.com/oauth/2.0/token?grant_type=client_credentials&client_id=".$apiKey."&client_secret=".$secretKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $auth_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //信任任何证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 检查证书中是否设置域名,0不验证
        $res = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($res, true);

        $token = $response['access_token'];

        /** 拼接参数开始 **/
        $audio = file_get_contents($audio_file);
        $base_data = base64_encode($audio);
        $params = array(
            "dev_pid" => $dev_pid,
            "format" => $format,
            "rate" => $rate,
            "token" => $token,
            "cuid"=> $cuid,
            "speech" => $base_data,
            "len" => strlen($audio),
            "channel" => 1,
        );
        $json_array = json_encode($params);
        $headers[] = "Content-Length: ".strlen($json_array);
        $headers[] = 'Content-Type: application/json; charset=utf-8';


        /** 拼接参数结束 **/
        $url = "http://vop.baidu.com/server_api";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); // 识别时长不超过原始音频
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_array);
        $res = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($res, true);

        return $this->response->array($this->apiSuccess('Success', 200, $response));
    }
}