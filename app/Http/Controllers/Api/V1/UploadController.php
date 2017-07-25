<?php

namespace App\Http\Controllers\Api\V1;

use App\Helper\QiniuApi;
use App\Models\AssetModel;
use App\Models\DemandCompany;
use App\Models\DesignCompanyModel;
use App\Models\User;
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
     * @apiParam {type} x:type 附件类型: 1.默认；2.用户头像；3.企业法人营业执照；4.需求项目设计附件；5.案例图片;6.设计公司logo；7.需求公司logo；8.项目阶段附件;9.需求公司营业执照；10.设计公司法人图片；11.需求公司法人证件;12.栏目位；
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

        return $this->response->array($this->apiSuccess('Success', 200, compact('upToken', 'upload_url', 'random')));
    }


    //七牛回调方法
    public function callback(Request $request)
    {
        if (!$request->input('type')) {
            return $this->response->array([
                'payload' => [
                    'success' => 0,
                    'message' => 'type not empty',
                ]
            ]);
        }
        $upload = $request->all();
        foreach ($upload as &$value) {
            if (empty($value)) {
                unset($value);
            }
        }
        $upload['domain'] = config('filesystems.disks.qiniu.domain');
        $key = uniqid();
        $upload['path'] = config('filesystems.disks.qiniu.domain') . '/' . date("Ymd") . '/' . $key;

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
            if ($asset->save()) {
                $id = $asset->id;

                //修改 用户头像、需求公司logo、设计公司logo
                if (!empty($upload['target_id'])) {
                    $this->changeLogo($upload['target_id'], $upload['type'], $id);
                }

                $callBackDate = [
                    'key' => $asset->path,
                    'payload' => [
                        'success' => 1,
                        'name' => $asset->name,
                        'file' => config('filesystems.disks.qiniu.url') . $asset->path,
                        'small' => config('filesystems.disks.qiniu.url') . $asset->path . config('filesystems.disks.qiniu.small'),
                        'big' => config('filesystems.disks.qiniu.url') . $asset->path . config('filesystems.disks.qiniu.big'),
                        'logo' => config('filesystems.disks.qiniu.url') . $asset->path . config('filesystems.disks.qiniu.logo'),
                        'asset_id' => $id,
                        'created_at' => $asset->created_at,

                    ]
                ];
                return $this->response->array($callBackDate);
            }
        } else {
            $callBackDate = [
                'error' => 2,
                'message' => '上传失败'
            ];
            return $this->response->array($callBackDate);
        }
    }

    /**
     * @api {delete} /upload/deleteFile/{asset_id}  删除图片
     * @apiVersion 1.0.0
     * @apiName upload deleteFile
     *
     * @apiGroup Upload
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *  }
     */
    public function deleteFile($asset_id)
    {
        if (!$file = AssetModel::find((int)$asset_id)) {
            return $this->response->array($this->apiSuccess());
        }

        if ($file->user_id !== $this->auth_user_id) {
            return $this->response->array($this->apiError());
        }

        $accessKey = config('filesystems.disks.qiniu.access_key');
        $secretKey = config('filesystems.disks.qiniu.secret_key');
        //初始化Auth状态
        $auth = new Auth($accessKey, $secretKey);
        //初始化BucketManager
        $bucketMgr = new BucketManager($auth);
        //你要测试的空间， 并且这个key在你空间中存在
        $bucket = config('filesystems.disks.qiniu.bucket');
        $key = $file->path;

        //删除$bucket 中的文件 $key
        $err = $bucketMgr->delete($bucket, $key);
        if ($err !== null) {
            return $this->response->array($this->apiError('Error', 500));
        } else {
            //删除附件表中的信息
            $file->delete();
            return $this->response->array($this->apiSuccess());
        }
    }

    //修改 用户头像、需求公司logo、设计公司logo

    /**
     * @param int $target_id 目标ID
     * @param int $type 附件类型
     * @param int $id 附件ID
     */
    public function changeLogo(int $target_id, int $type, int $id)
    {
        switch ($type) {
            case 2:
                if ($user = User::find($target_id)) {
                    $user->logo = $id;
                    $user->save();
                }
                break;
            case 6:
                if ($design = DesignCompanyModel::find($target_id)) {
                    $design->logo = $id;
                    $design->save();

                    $user = User::find($design->user_id);
                    $user->logo = $id;
                    $user->save();
                }
                break;
            case 7:
                if ($demand = DemandCompany::find($target_id)) {
                    $demand->logo = $id;
                    $demand->save();

                    $user = User::find($demand->user_id);
                    $user->logo = $id;
                    $user->save();
                }
                break;
        }
    }
}
