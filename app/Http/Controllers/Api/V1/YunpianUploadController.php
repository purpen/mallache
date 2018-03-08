<?php

namespace App\Http\Controllers\Api\V1;

use App\Helper\QiniuApi;
use App\Models\PanFile;
use Illuminate\Http\Request;
use Qiniu\Auth;

class YunpianUploadController extends BaseController
{
    /**
     * @api {post} http://upload.qiniu.com  上传图片
     * @apiVersion 1.0.0
     * @apiName yunpan upload
     *
     * @apiGroup yunpan
     * @apiParam {string} token 图片上传upToken
     * @apiParam {string} x:pan_director_id 上级文件目录ID （顶层文件传'0'）
     * @apiParam {integer} x:open_set 文件设置：1.公开 2.个人
     * @apiParam {integer} x:group_id 所属项目ID （没有传'0'）
     */


    /**
     * @api {get} /upload/yunpanUpToken  生成云盘上传upToken
     * @apiVersion 1.0.0
     * @apiName yunpan asset
     *
     * @apiGroup yunpan
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
     *      }
     *  }
     */
    public function upToken()
    {
        $upload_url = config('filesystems.disks.qiniu.upload_url');
        $user_id = $this->auth_user_id;
        $upToken = QiniuApi::yunPanUpToken($user_id);

        return $this->response->array($this->apiSuccess('Success', 200, compact('upToken', 'upload_url')));
    }


    // 云盘上传七牛回调接口
    public function yunpanCallback(Request $request)
    {
        $accessKey = config('filesystems.disks.qiniu.access_key');
        $secretKey = config('filesystems.disks.qiniu.secret_key');
        $auth = new Auth($accessKey, $secretKey);
        //获取回调的body信息
        $callbackBody = file_get_contents('php://input');
        //回调的contentType
        $contentType = 'application/x-www-form-urlencoded';
        //回调的签名信息，可以验证该回调是否来自七牛
        $authorization = $_SERVER['HTTP_AUTHORIZATION'];
        //七牛回调的url，具体可以参考
        $url = config('filesystems.disks.yunpan_qiniu.call_back_url');
        $isQiniuCallback = $auth->verifyCallback($contentType, $authorization, $url, $callbackBody);

        if (!$isQiniuCallback) {  //验证失败
            $callBackDate = [
                'error' => 1,
                'message' => '回调签名验证失败'
            ];
            return $this->response->array($callBackDate);
        } else {
            $open_set = $request->input('open_set');
            $pan_director_id = $request->input('pan_director_id');
            $group_id = $request->input('group_id');

            if (!in_array($open_set, [1, 2])) {
                $callBackDate = [
                    'error' => 1,
                    'message' => 'open_set参数不正确'
                ];
                return $this->response->array($callBackDate);
            }

            /**
             * name=$(fname)&size=$(fsize)&mime=$(mimeType)&width=$(imageInfo.width)&height=$(imageInfo.height)&type=$(x:type)&pan_director_id=$(x:pan_director_id)&open_set=$(x:open_set)&group_id=$(x:group_id)&uid=
             */
            $key = uniqid();
            $path = config('filesystems.disks.yunpan_qiniu.domain') . '/' . date("Ymd") . '/' . $key;

            $pan_file = new PanFile();
            $pan_file->name = $request->input('name');
            $pan_file->size = $request->input('size');
            $pan_file->width = $request->input('width');
            $pan_file->height = $request->input('height');
            $pan_file->mime_type = $request->input('mime');
            $pan_file->user_id = $request->input('uid');
            $pan_file->count = 1;
            $pan_file->status = 0;
            $pan_file->url = $path;
            $pan_file->save();

            if($open_set == 1){  //公开文件



            }elseif($open_set == 2){ // 个人文件

            }

            $callBackDate = [
                'key' => $pan_file->url,
                'payload' => [
                    'success' => 1,
                    'name' => $pan_file->name,
                    'file' => config('filesystems.disks.yunpan_qiniu.url') . $pan_file->url,

                    'created_at' => $asset->created_at,

                ]
            ];
        }
    }


}