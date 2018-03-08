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
     * 云盘七牛上传生成token
     */
    static public function yunPanUpToken(int $uid)
    {
        $accessKey = config('filesystems.disks.qiniu.access_key');
        $secretKey = config('filesystems.disks.qiniu.secret_key');
        $auth = new Auth($accessKey, $secretKey);

        $bucket = config('filesystems.disks.yunpan_qiniu.bucket');

        // 上传文件到七牛后， 七牛将callbackBody设置的信息回调给业务服务器
        $policy = array(
            'callbackUrl' => config('filesystems.disks.yunpan_qiniu.call_back_url'),
            'callbackFetchKey' => 1,
            'callbackBody' => 'name=$(fname)&size=$(fsize)&mime=$(mimeType)&width=$(imageInfo.width)&height=$(imageInfo.height)&type=$(x:type)&pan_director_id=$(x:pan_director_id)&open_set=$(x:open_set)&group_id=$(x:group_id)&uid=' . $uid,
        );
        $upToken = $auth->uploadToken($bucket, null, 3600, $policy);
        return $upToken;
    }

    /**
     * 云盘私有资源连接生成
     *
     * @param string $baseUrl 私有链接
     * @return string
     */
    static public function yunPanDownloadUrl(string $baseUrl)
    {
        $accessKey = config('filesystems.disks.qiniu.access_key');
        $secretKey = config('filesystems.disks.qiniu.secret_key');
        $auth = new Auth($accessKey, $secretKey);

        // 对链接进行签名
        $signedUrl = $auth->privateDownloadUrl($baseUrl);
        return $signedUrl;
    }

}