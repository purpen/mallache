<?php

namespace App\Http\Controllers\Api\Admin;


use App\Models\AssetModel;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class UploadAssetController extends BaseController
{

    /**
     * @api {get} /admin/urlUpload 地址上传
     * @apiVersion 1.0.0
     * @apiName AdminUrlUpload urlUpload
     * @apiGroup AdminUrlUpload
     *
     * @apiParam {string} url url
     * @apiParam {string} token
     */
    public function urlUpload(Request $request)
    {
        $url = $request->input('url');
        $accessKey = config('qiniu.access_key');
        $secretKey = config('qiniu.secret_key');
        $auth = new Auth($accessKey, $secretKey);

        $bucket = config('filesystems.disks.qiniu.bucket');

        $token = $auth->uploadToken($bucket);
        $filePath = file_get_contents($url);
        $key = 'url/'.date("Ymd").'/'.uniqid();
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 put 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->put($token, $key, $filePath);
        $data = config('filesystems.disks.qiniu.url').$key;
        return $this->response->array($this->apiSuccess('success' , 200 ,compact('data')));
    }

}
