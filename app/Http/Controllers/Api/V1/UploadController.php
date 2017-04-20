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
     * @api {post} http://upload.qiniu.com  上传图片
     * @apiVersion 1.0.0
     * @apiName upload image
     *
     * @apiGroup Upload
     * @apiParam {string} token 图片上传upToken
     * @apiParam {string} x:random 随机数
     * @apiParam {integer} x:user_id
     * @apiParam {integer} x:target_id 目标ID
     * @apiParam {type} x:type 附件类型: 1.默认；2.用户头像；3.企业法人营业执照；4.需求项目设计附件；5.案例图片;6.公司logo；
     */

    /**
     * @api {get} /upload/upToken  生成上传图片upToken
     * @apiVersion 1.0.0
     * @apiName upload asset
     *
     * @apiGroup Upload
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     },
     *     "data": {
     *       "upToken": "AWTEpwVNmNcVjsIL-vS1hOabJ0NgIfNDzvTbDb4i:csOk9LcG2lM0_3qvbDqmEUa87V8=:eyJjYWxsYmFja1VybCI6bnVsbCwiY2FsbGJhY2tGZXRjaEtleSI6MSwiY2FsbGJhY2tCb2R5IjoibmFtZT0kKGZuYW1lKSZzaXplPSQoZnNpemUpJm1pbWU9JChtaW1lVHlwZSkmd2lkdGg9JChpbWFnZUluZm8ud2lkdGgpJmhlaWdodD0kKGltYWdlSW5mby5oZWlnaHQpJnJhbmRvbT0kKHg6cmFuZG9tKSZ1c2VyX2lkPSQoeDp1c2VyX2lkKSZ0YXJnZXRfaWQ9JCh4OnRhcmdldF9pZCkiLCJzY29wZSI6bnVsbCwiZGVhZGxpbmUiOjE0OTA3NTUyMDh9"
     *       "upload_url": "http://up-z1.qiniu.come",
 *           "random" : ""
     *      }
     *  }
     */
    public function upToken()
    {
        $upload_url = config('filesystems.disks.qiniu.upload_url');
        $upToken = QiniuApi::upToken();

        $random = uniqid('', true);

        return $this->response->array($this->apiSuccess('Success', 200, compact('upToken' , 'upload_url', 'random')));
    }


    //七牛回调方法
    public function callback(Request $request)
    {
        if(!$request->input('type')){
            return $this->response->array([
                'payload' => [
                        'success' => 0,
                        'message' => 'type not empty',
                    ]
            ]);
        }
        $upload = $request->all();
        foreach($upload as &$value){
            if(empty($value)){
                unset($value);
            }
        }
        $upload['domain'] = config('filesystems.disks.qiniu.domain');
        $key = uniqid();
        $upload['path'] =  config('filesystems.disks.qiniu.domain') . '/' .date("Ymd") . '/' . $key;

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
        $isQiniuCallback = $auth->verifyCallback($contentType, $authorization, $url, $callbackBody);

        if ($isQiniuCallback) {
            $asset = new AssetModel();
            $asset->fill($upload);
            if($asset->save()) {
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
            }
        } else {
            $callBackDate = [
                'error' => 2,
                'message' => '上传失败'
            ];
            return $this->response->array($callBackDate );
        }
    }
}
