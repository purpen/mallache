<?php

namespace App\Console\Commands;

use App\Models\AwardCase;
use Illuminate\Console\Command;
use App\Models\AssetModel;
use Qiniu\Auth;
use Qiniu\Config;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;

class UpdateAwardCase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:award_case {--evt=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新案例获奖作品: --evt=1  [默认1 1.自动上传封面；2.状态自动启用；]';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $evt = (int)$this->option('evt');
        echo "currnet evt : $evt.\n";
        if ($evt === 1) {
          return $this->updateCover();
        }elseif($evt === 2) {
          return $this->updateStatus();
        } else {
          echo "error params evt.\n";
        }

    }

    /**
     * 更新封面
     */
    protected function updateCover()
    {
        // 奖项作品
        $page = 1;
        $size = 200;
        $total = 0;
        while(true){
            $offset = ($page - 1) * $size;
            $list = AwardCase::select('id','random','status','images_url','cover_id')
                ->where('status', 0)
                ->skip($offset)
                ->limit($size)
                ->get();

            if(empty($list)){
                echo "get awardCase list is null,exit......\n";
                break;
            }
            $max = count($list);
            for ($i=0; $i < $max; $i++) {
                if ($list[$i]->cover_id) {
                  continue;
                }
                if (!$list[$i]->images_url) {
                  continue;
                }

                $id = $list[$i]->id;
                echo "set awardCase[". $id ."]..........\n";
                $img_arr = explode('@@', $list[$i]->images_url);

                $param = [
                    'target_id' => $id,
                    'user_id' => $list[$i]->user_id,
                    'type' => 25,
                ];
                $asset_result = $this->urlUpload($img_arr[0], $param);

                if (!$asset_result['success']) {
                    echo "img upload fail: ".$asset_result['message']."\n";
                    continue;
                }
                $asset_id = $asset_result['data']['id'];
                echo "get asset_id: $asset_id.\n";
                $ok = true;
                $ok = AwardCase::where('id', $id)->update(['cover_id'=>$asset_id]);
                if($ok) {
                    $total++;
                    echo "update success $id..\n";
                }
            }
            if($max < $size){
                echo "awardCase list is end!!!!!!!!!,exit.\n";
                break;
            }
            $page++;
            echo "page [$page] updated---------\n";
        }
        echo "awardCase update cover count: $total.....";
    
    }

    /**
     * 更新状态
     */
    protected function updateStatus()
    {
        // 奖项作品
        $page = 1;
        $size = 200;
        $is_end = false;
        $total = 0;
        while(!$is_end){
            $offset = ($page - 1) * $size;
            $list = AwardCase::select('id','random','status','cover_id')
                ->where('status', 0)
                ->skip($offset)
                ->limit($size)
                ->get();

            if(empty($list)){
                echo "get awardCase list is null,exit......\n";
                break;
            }
            $max = count($list);
            for ($i=0; $i < $max; $i++) {
                $id = $list[$i]->id;
                echo "set awardCase[". $id ."]..........\n";
                if (!$list[$i]->cover_id) {
                    continue;
                }
                $ok = true;
                //$ok = AwardCase::where('id', $id)->update(['status'=>1]);
                if($ok) $total++;
            }
            if($max < $size){
                echo "awardCase list is end!!!!!!!!!,exit.\n";
                break;
            }
            $page++;
            echo "page [$page] updated---------\n";
        }
        echo "awardCase update status count: $total.....";
    
    }

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
    protected function urlUpload($url, $options=array())
    {
        $result = array('success'=> 0, 'message'=> '');
        if (!$url) {
            $result['message'] = '缺少请求参数！';
            return $result;
        }
        $accessKey = config('filesystems.disks.qiniu.access_key');
        $secretKey = config('filesystems.disks.qiniu.secret_key');

        $bucket = config('filesystems.disks.qiniu.bucket');

        try {
            $auth = new Auth($accessKey, $secretKey);
            $token = $auth->uploadToken($bucket);
            $filePath = @file_get_contents($url);
            if(strlen($filePath) == 0){
                $result['message'] = '文件找不到！';
                return $result;
            }
            $key = 'saas/'.date("Ymd").'/'.uniqid();
            // 初始化 UploadManager 对象并进行文件的上传。
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 put 方法进行文件的上传。
            list($ret, $err) = $uploadMgr->put($token, $key, $filePath);
            if($ret !== null){
                $asset = new AssetModel();
                $asset->type = $options['type'] ?? 1;
                $asset->name = basename($url);
                $asset->domain = 'saas';
                $asset->target_id = $options['target_id'] ?? 0;
                $asset->path = $key;
                $asset->user_id = $options['user_id'] ?? 0;
                $ok = $asset->save();
                if (!$ok) {
                  $result['message'] = '保存附件失败！';
                  return $result;
                }
                $data = AssetModel::getOneImage($asset->id);
                $result['success'] = 1;
                $result['data'] = $data;
                return $result;
            }else{
                $result['message'] = '上传失败！';
                return $result;
            }
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
            return $result;     
        }

    }
}
