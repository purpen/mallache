<?php

namespace App\Http\Controllers\Api\V1;

use App\Helper\QiniuApi;
use App\Models\AssetModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Qiniu\Auth;
use Qiniu\Storage\BucketManager;

class UploadController extends BaseController
{
    /**
     * @api {get} /upload/upToken  生成上传图片upToken
     * @apiVersion 1.0.0
     * @apiName upload asset
     * @apiGroup Upload
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     },
     *     "data": {
     *       "upToken": "AWTEpwVNmNcVjsIL-vS1hOabJ0NgIfNDzvTbDb4i:csOk9LcG2lM0_3qvbDqmEUa87V8=:eyJjYWxsYmFja1VybCI6bnVsbCwiY2FsbGJhY2tGZXRjaEtleSI6MSwiY2FsbGJhY2tCb2R5IjoibmFtZT0kKGZuYW1lKSZzaXplPSQoZnNpemUpJm1pbWU9JChtaW1lVHlwZSkmd2lkdGg9JChpbWFnZUluZm8ud2lkdGgpJmhlaWdodD0kKGltYWdlSW5mby5oZWlnaHQpJnJhbmRvbT0kKHg6cmFuZG9tKSZ1c2VyX2lkPSQoeDp1c2VyX2lkKSZ0YXJnZXRfaWQ9JCh4OnRhcmdldF9pZCkiLCJzY29wZSI6bnVsbCwiZGVhZGxpbmUiOjE0OTA3NTUyMDh9"
     *       "upload_url": "https://up-z1.qbox.me"
     *      }
     *  }
     */
    public function upToken()
    {
        $upload_url = config('filesystems.disks.qiniu.upload_url');
        $upToken = QiniuApi::upToken();
        return $this->response->array($this->apiSuccess('Success', 200, compact('upToken' , 'upload_url')));
    }


    //七牛回调方法
    public function callback(Request $request)
    {
//        $post = $request->all();
//        \Log::error($post);
//        $imageData = [];
//        $imageData['user_id'] = $post['user_id'];
//        $imageData['name'] = $post['name'];
//        $imageData['random'] = $post['random'];
//        $imageData['size'] = $post['size'];
//        $imageData['width'] = $post['width'];
//        $imageData['height'] = $post['height'];
//        $imageData['mime'] = $post['mime'];
//        $imageData['domain'] = config('filesystems.disks.qiniu.domain');
//        $imageData['target_id'] = $post['target_id'];
//        $key = uniqid();
//        $imageData['path'] = config('filesystems.disks.qiniu.domain') . '/' .date("Ymd") . '/' . $key;
//        if($asset = AssetModel::create($imageData)){
//            $id = $asset->id;
//            $callBackDate = [
//                'key' => $asset->path,
//                'payload' => [
//                    'success' => 1,
//                    'name' => config('filesystems.disks.qiniu.url').$asset->path,
//                    'small' => config('filesystems.disks.qiniu.url').$asset->path.config('filesystems.disks.qiniu.small'),
//                    'asset_id' => $id
//                ]
//            ];
//            return $this->response->array($callBackDate);
//        }
        $post = $request->all();

        $accessKey = config('filesystems.disks.qiniu.access_key');
        $secretKey = config('filesystems.disks.qiniu.secret_key');
        $auth = new Auth($accessKey, $secretKey);
        //获取回调的body信息
        $callbackBody = file_get_contents('php://input');
        $body = json_decode($callbackBody, true);
        //回调的contentType
        $contentType = 'application/x-www-form-urlencoded';
        //回调的签名信息，可以验证该回调是否来自七牛
        $authorization = $_SERVER['HTTP_AUTHORIZATION'];
        //七牛回调的url，具体可以参考
        $url = config('filesystems.disks.qiniu.call_back_url');

        $asset = new AssetModel();
        $asset->name = $post['name'];
        $asset->size = $post['size'];
        $asset->width = $post['width'];
        $asset->height = $post['height'];
        $asset->random = $post['random'];
        $asset->user_id = $post['user_id'];
        $asset->target_id = $post['target_id'];
        $asset->domain = config('filesystems.disks.qiniu.domain');
        $key = uniqid();
        $asset->path =  config('filesystems.disks.qiniu.domain') . '/' .date("Ymd") . '/' . $key;
        $asset->save();
        Log::info($asset);

        $isQiniuCallback = $auth->verifyCallback($contentType, $authorization, $url, $callbackBody);

        if ($isQiniuCallback) {
                $id = $asset->id;
                $callBackDate = [
                    'key' => $asset->path,
                    'payload' => [
                        'success' => 1,
                        'name' => config('filesystems.disks.qiniu.url') . $asset->path,
                        'small' => config('filesystems.disks.qiniu.url') . $asset->path . config('filesystems.disks.qiniu.small'),
                        'asset_id' => $id
                    ]
                ];
                return $this->response->array($callBackDate);
        } else {
            $resp = array('ret' => 'failed');
        }
    }
}
