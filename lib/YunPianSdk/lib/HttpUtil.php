<?php
namespace Lib\YunPianSdk\lib;

use Illuminate\Support\Facades\Log;
use Lib\YunPianSdk\Yunpian;

/**
 * Use: 使用curl方式发送请求
 */

class HttpUtil
{
    public static function PostCURL($url,$post_data){
        Log::info(4443);
        $yunpian = new Yunpian();
        $yunpian_config = $yunpian->config;
        Log::info(4446);
        $ch = curl_init();

        /* 设置验证方式 */

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8', 'Content-Type:application/x-www-form-urlencoded','charset=utf-8'));

        /* 设置返回结果为流 */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        /* 设置超时时间*/
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        /* 设置通信方式 */
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        Log::info(4447);
        $retry=0;
        // 若执行失败则重试
        Log::info(4449);
        do{
            Log::info(555);
            $output = curl_exec($ch);
            Log::info(556);
            $retry++;
            Log::info(4448);
        }while((curl_errno($ch) !== 0) && $retry<$yunpian_config['RETRY_TIMES']);
        Log::info(4445);
        if (curl_errno($ch) !== 0) {
            $r = new Result(null, $post_data, null,curl_error($ch));
            curl_close($ch);
            Log::info(4444);
            return $r;
        }
        $output = trim($output, "\xEF\xBB\xBF");
        $statusCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        $ret = new Result($statusCode,$post_data,json_decode($output,true),null);
        curl_close($ch);

        Log::info(4445);
        return $ret;
    }
}