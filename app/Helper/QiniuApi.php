<?php
/**
 * Created by PhpStorm.
 * User: cailiguang
 * Date: 2017/3/28
 * Time: 下午3:25
 */

namespace App\Helper;

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class QiniuApi
{
    /**
     * 生成七牛Token
     */
    static public function upToken()
    {
        $accessKey = config('filesystems.disks.qiniu.access_key');
        $secretKey = config('filesystems.disks.qiniu.secret_key');
        $auth = new Auth($accessKey, $secretKey);

        $bucket = config('filesystems.disks.qiniu.bucket');

        // 上传文件到七牛后， 七牛将callbackBody设置的信息回调给业务服务器
        $policy = array(
            'callbackUrl' => config('filesystems.disks.qiniu.call_back_url'),
            'callbackFetchKey' => 1,
            'callbackBody' => 'name=$(fname)&size=$(fsize)&mime=$(mimeType)&width=$(imageInfo.width)&height=$(imageInfo.height)&random=$(x:random)&user_id=$(x:user_id)&target_id=$(x:target_id)&type=$(x:type)',
        );
        $upToken = $auth->uploadToken($bucket, null, 3600, $policy);
        return $upToken;
    }


    /**
     * 安全的Url编码urlsafe_base64_encode函数
     */
    function urlsafe_base64_encode($data)
    {
        $data = base64_encode($data);
        $data = str_replace(array('+', '/'), array('-', '_'), $data);

        return $data;
    }


}