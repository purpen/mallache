<?php

namespace App\Http\Controllers\Api\V1;

use App\Helper\QiniuApi;
use App\Models\PanDirector;
use App\Models\PanFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
     *
     * @apiSuccessExample 成功响应:
     * {
     * "info": {
     *      "created_at": 1520825077,   // 创建时间
     *      "group_id": "0",            // 分组ID
     *      "id": 1,                    //
     *      "mime_type": "image/jpeg",  // 文件类型
     *      "name": "路飞.jpg",
     *      "pan_director_id": "0",     //上级文件ID
     *      "size": "18863",            // 大小 （字节byte）
     *      "type": 2,                  // 类型：1.文件夹、2.文件
     *      "url": "?e=1520828677&token=AWTEpwVNmNcVjsIL-vS1hOabJ0NgIfNDzvTbDb4i:zh_0glW22GT9S2DBZacMLC4Dp24=",
     *      "user_id": "2",
     *      "user_name": ""
     * },
     * "success": 1
     * }
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
                'payload' => [
                    'success' => 0,
                    'message' => '回调签名验证失败',
                ]
            ];
            Log::info($callBackDate);
            return $this->response->array($callBackDate);
        } else {
            $open_set = $request->input('open_set');
            $pan_director_id = $request->input('pan_director_id');
            $group_id = $request->input('group_id');
            $user_id = $request->input('uid');

            if (!in_array($open_set, [1, 2])) {
                $callBackDate = [
                    'payload' => [
                        'success' => 0,
                        'message' => 'open_set参数不正确',
                    ]
                ];
                Log::info($callBackDate);
                return $this->response->array($callBackDate);
            }


            $design_company_id = User::designCompanyId($user_id);
            // 判断上传参数是否正确
            if (!PanDirector::isCreate($user_id, $pan_director_id, $open_set, $design_company_id, $group_id)) {
                $callBackDate = [
                    'payload' => [
                        'success' => 0,
                        'message' => '上传文件参数不合法',
                    ]
                ];
                Log::info($callBackDate);
                return $this->response->array($callBackDate);
            }

            $key = uniqid();
            $path = config('filesystems.disks.yunpan_qiniu.domain') . '/' . date("Ymd") . '/' . $key;

            // 保存源文件
            $pan_file = new PanFile();
            $pan_file->name = $request->input('name');
            $pan_file->size = $request->input('size');
            $pan_file->width = $request->input('width');
            $pan_file->height = $request->input('height');
            $pan_file->mime_type = $request->input('mime');
            $pan_file->user_id = $user_id;
            $pan_file->count = 1;
            $pan_file->status = 0;
            $pan_file->url = $path;
            $pan_file->save();

            // 保存目录文件
            $pan_director = new PanDirector();
            $pan_director->open_set = $open_set;
            $pan_director->group_id = $group_id;
            $pan_director->company_id = $design_company_id;
            $pan_director->pan_director_id = $pan_director_id;
            $pan_director->type = 2;
            $pan_director->name = $pan_file->name;
            $pan_director->size = $pan_file->size;
            $pan_director->sort = 0;
            $pan_director->mime_type = $pan_file->mime_type;
            $pan_director->pan_file_id = $pan_file->id;
            $pan_director->user_id = $user_id;
            $pan_director->status = 1;
            $pan_director->url = $pan_file->url;
            $pan_director->save();


            $callBackDate = [
                'key' => $pan_file->url,
                'payload' => [
                    'success' => 1,
                    'info' => $pan_director->info(),

                ]
            ];
            Log::info($callBackDate);
            return $this->response->array($callBackDate);
        }
    }


}