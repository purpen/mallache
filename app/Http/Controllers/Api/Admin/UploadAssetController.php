<?php

namespace App\Http\Controllers\Api\Admin;


use App\Http\AdminTransformer\AssetsTransformer;
use App\Models\AssetModel;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Qiniu\Auth;
use Qiniu\Config;
use Qiniu\Storage\BucketManager;
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
     * @apiParam {integer} type 类型
     * @apiParam {integer} target_id 目标id
     * @apiParam {string} token
     */
    public function urlUpload(Request $request)
    {
        $url = $request->input('url');
        $accessKey = config('filesystems.disks.qiniu.access_key');
        $secretKey = config('filesystems.disks.qiniu.secret_key');
        $auth = new Auth($accessKey, $secretKey);

        $bucket = config('filesystems.disks.qiniu.bucket');

        $token = $auth->uploadToken($bucket);
        $filePath = file_get_contents($url);
        $key = 'saas/'.date("Ymd").'/'.uniqid();
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 put 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->put($token, $key, $filePath);
        if($ret !== null){
            $asset = new AssetModel();
            $asset->type = $request->input('type') ? $request->input('type') : 1;
            $asset->name = '';
            $asset->domain = 'saas';
            $asset->target_id = $request->input('target_id') ? $request->input('target_id') : 0;
            $asset->path = $key;
            $asset->user_id = $this->auth_user_id;
            $asset->save();
            $data = AssetModel::getOneImage($asset->id);
            return $this->response->array($this->apiSuccess('success', 200 , $data));
        }else{
            return $this->response->array($this->apiError('上传失败', 416));
        }



    }

    /**
     * @api {get} /admin/assets 附件列表
     * @apiName AdminAssets assets
     * @apiGroup AdminUrlUpload
     *
     * @apiParam {integer} type 类型0.全部；1.默认；2.用户头像；3.企业法人营业执照；4.需求项目设计附件；5.案例图片；6.设计公司logo；7.需求公司logo；8.项目阶段附件；9.需求公司营业执照；10.设计公司法人图片 11.需求公司法人 ；12.栏目位；13.文章封面；14.文章图片；15.大赛作品；16.趋势报告封面图；17.趋势报告pdf；18.常用网站图片
     * @apiParam {integer} page 页数
     * @apiParam {integer} per_page 页面条数
     * @apiParam {string} token
     */
    public function lists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $type = $request->input('type') ? $request->input('type') : 0;
        if($type !== 0){
            $assets = AssetModel::where('type' , $type)->paginate($per_page);
            ;
        }else{
            $assets = AssetModel::orderBy('id', 'desc')
                ->paginate($per_page);;
        }

        return $this->response->paginator($assets, new AssetsTransformer())->setMeta($this->apiMeta());

    }


}
