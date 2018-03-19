<?php

namespace App\Http\Controllers\Api\V1;

use App\Helper\QiniuApi;
use App\Http\Transformer\YunpanListTransformer;
use App\Models\Group;
use App\Models\PanDirector;
use App\Models\PanFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Qiniu\Auth;

class YunpianUploadController extends BaseController
{
    /**
     * @api {post} http://upload.qiniu.com  上传资源
     * @apiVersion 1.0.0
     * @apiName yunpan upload
     *
     * @apiGroup yunpan
     * @apiParam {string} token 图片上传upToken
     * @apiParam {string} x:pan_director_id 上级文件目录ID （顶层文件传'0'）
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
            $pan_director_id = $request->input('pan_director_id');
            $user_id = $request->input('uid');


            $company_id = User::designCompanyId($user_id);
            if (!$company_id) {
                return null;
            }

            $key = uniqid();
            $path = config('filesystems.disks.yunpan_qiniu.domain') . '/' . date("Ymd") . '/' . $key;

            try {
                DB::beginTransaction();

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

                //上级目录信息
                $pan_dir = PanDirector::where(['id' => $pan_director_id, 'type' => 1])->first();
                if (!$pan_dir) {
                    throw new \Exception('not found dir');
                }

                // 判断是否在企业根目录下创建或公开的
                if ($pan_director_id === 0 || $pan_dir->open_set == 1) {

                    $pan_director = new PanDirector();
                    $pan_director->open_set = 1;
                    $pan_director->group_id = null;
                    $pan_director->company_id = $company_id;
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

                } else if ($item_id = $pan_dir->item_id || $this->auth_user->isDesignAdmin()) { // 判断上级文件夹是否是项目文件夹
                    // 判断用户是否在这个项目中
                    // 项目管理未完成
                    throw new \Exception('项目管理未完成');

                } else if ($pan_dir->group_id !== null) {       // 判断上级文件夹是否是属于群组
                    $user_group_id_list = Group::userGroupIDList($user_id);
                    if (!empty(array_intersect(json_decode($pan_dir->group_id, true), $user_group_id_list)) || $this->auth_user->isDesignAdmin()) {
                        $pan_director = new PanDirector();
                        $pan_director->open_set = $pan_dir->open_set;
                        $pan_director->group_id = $pan_dir->group_id;
                        $pan_director->company_id = $pan_dir->company_id;
                        $pan_director->pan_director_id = $pan_director_id;
                        $pan_director->type = $pan_dir->type;
                        $pan_director->name = $pan_file->name;
                        $pan_director->size = $pan_file->size;
                        $pan_director->sort = 0;
                        $pan_director->mime_type = $pan_file->mime_type;
                        $pan_director->pan_file_id = $pan_file->id;
                        $pan_director->user_id = $user_id;
                        $pan_director->status = 1;
                        $pan_director->url = $pan_file->url;
                        $pan_director->save();

                    }
                } else if (($pan_dir->open_set == 2 && $pan_dir->user_id == $user_id) || $this->auth_user->isDesignAdmin()) {  // 判断上级目录是不是私有的
                    $pan_director = new PanDirector();
                    $pan_director->open_set = $pan_dir->open_set;
                    $pan_director->group_id = $pan_dir->group_id;
                    $pan_director->company_id = $pan_dir->company_id;
                    $pan_director->pan_director_id = $pan_director_id;
                    $pan_director->type = $pan_dir->type;
                    $pan_director->name = $pan_file->name;
                    $pan_director->size = $pan_file->size;
                    $pan_director->sort = 0;
                    $pan_director->mime_type = $pan_file->mime_type;
                    $pan_director->pan_file_id = $pan_file->id;
                    $pan_director->user_id = $user_id;
                    $pan_director->status = 1;
                    $pan_director->url = $pan_file->url;
                    $pan_director->save();

                    return $this->response->array($this->apiSuccess());
                } else {
                    throw new \Exception('未知错误');
                }

                DB::commit();
                $callBackDate = [
                    'key' => $pan_file->url,
                    'payload' => [
                        'success' => 1,
                        'info' => $pan_director->info(),

                    ]
                ];
                Log::info($callBackDate);
                return $this->response->array($callBackDate);
            } catch (\Exception $e) {
                DB::rollBack();
                $callBackDate = [
                    'payload' => [
                        'success' => 0,
                        'message' => $e->getMessage(),
                    ]
                ];
                Log::info($callBackDate);
                return $this->response->array($callBackDate);
            }

        }
    }


    /**
     * @api {post} /yunpan/createDir  创建文件夹
     * @apiVersion 1.0.0
     * @apiName yunpan createDir
     *
     * @apiGroup yunpan
     * @apiParam {string} token
     * @apiParam {string} name 文件夹名称
     * @apiParam {integer} pan_director_id 上级文件夹ID
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *  }
     */
    public function createDir(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'pan_director_id' => 'required|integer'
        ]);

        $name = $request->input('name');
        $pan_director_id = $request->input('pan_director_id');
        $user_id = $this->auth_user_id;
        $company_id = User::designCompanyId($user_id);
        if (!$company_id) {
            return null;
        }

        // 判断是否在企业根目录下创建
        if ($pan_director_id == 0) {
            $pan_director = new PanDirector();
            $pan_director->open_set = 1;
            $pan_director->group_id = null;
            $pan_director->company_id = $company_id;
            $pan_director->pan_director_id = $pan_director_id;
            $pan_director->type = 1;
            $pan_director->name = $name;
            $pan_director->size = 0;
            $pan_director->sort = 0;
            $pan_director->mime_type = '';
            $pan_director->pan_file_id = 0;
            $pan_director->user_id = $user_id;
            $pan_director->status = 1;
            $pan_director->url = '';
            $pan_director->save();

            return $this->response->array($this->apiSuccess());
        }

        //上级目录信息
        $pan_dir = PanDirector::where(['id' => $pan_director_id, 'type' => 1])->first();
        if (!$pan_dir) {
            return $this->response->array($this->apiError('not found dir!', 404));
        }
        // 判断是否公开的
        if ($pan_dir->open_set == 1) {

            $pan_director = new PanDirector();
            $pan_director->open_set = 1;
            $pan_director->group_id = null;
            $pan_director->company_id = $company_id;
            $pan_director->pan_director_id = $pan_director_id;
            $pan_director->type = 1;
            $pan_director->name = $name;
            $pan_director->size = 0;
            $pan_director->sort = 0;
            $pan_director->mime_type = '';
            $pan_director->pan_file_id = 0;
            $pan_director->user_id = $user_id;
            $pan_director->status = 1;
            $pan_director->url = '';
            $pan_director->save();

            return $this->response->array($this->apiSuccess());
        }

        // 判断上级文件夹是否是项目文件夹
        if ($item_id = $pan_dir->item_id || $this->auth_user->isDesignAdmin()) {
            // 判断用户是否在这个项目中
            // 项目管理未完成
        }

        // 判断上级文件夹是否是属于群组
        if ($pan_dir->group_id !== null) {
            $user_group_id_list = Group::userGroupIDList($user_id);
            if (!empty(array_intersect(json_decode($pan_dir->group_id, true), $user_group_id_list)) || $this->auth_user->isDesignAdmin()) {
                $pan_director = new PanDirector();
                $pan_director->open_set = $pan_dir->open_set;
                $pan_director->group_id = $pan_dir->group_id;
                $pan_director->company_id = $pan_dir->company_id;
                $pan_director->pan_director_id = $pan_director_id;
                $pan_director->type = $pan_dir->type;
                $pan_director->name = $name;
                $pan_director->size = $pan_dir->size;
                $pan_director->sort = $pan_dir->sort;
                $pan_director->mime_type = $pan_dir->mime_type;
                $pan_director->pan_file_id = $pan_dir->pan_file_id;
                $pan_director->user_id = $user_id;
                $pan_director->status = $pan_dir->status;
                $pan_director->url = $pan_dir->url;
                $pan_director->save();

                return $this->response->array($this->apiSuccess());
            }
        }

        // 判断上级目录是不是私有的
        if (($pan_dir->open_set == 2 && $pan_dir->user_id == $user_id) || $this->auth_user->isDesignAdmin()) {
            $pan_director = new PanDirector();
            $pan_director->open_set = $pan_dir->open_set;
            $pan_director->group_id = $pan_dir->group_id;
            $pan_director->company_id = $pan_dir->company_id;
            $pan_director->pan_director_id = $pan_director_id;
            $pan_director->type = $pan_dir->type;
            $pan_director->name = $name;
            $pan_director->size = $pan_dir->size;
            $pan_director->sort = $pan_dir->sort;
            $pan_director->mime_type = $pan_dir->mime_type;
            $pan_director->pan_file_id = $pan_dir->pan_file_id;
            $pan_director->user_id = $user_id;
            $pan_director->status = $pan_dir->status;
            $pan_director->url = $pan_dir->url;
            $pan_director->save();

            return $this->response->array($this->apiSuccess());
        }

        return $this->response->array($this->apiError('error', 500));
    }


    /**
     * @api {get} /yunpan/lists  网盘列表
     * @apiVersion 1.0.0
     * @apiName yunpan lists
     *
     * @apiGroup yunpan
     * @apiParam {string} token
     * @apiParam {integer} pan_director_id 上级文件夹ID
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     * "data": [
     * {
     * "id": 2,
     * "pan_director_id": 1, //上级文件ID
     * "type": 1, // 类型：1.文件夹、2.文件
     * "name": "第二层",
     * "size": 0, // 大小 （字节byte）
     * "mime_type": "", // 文件类型
     * "url_small": "?e=1521433712&token=AWTEpwVNmNcVjsIL-vS1hOabJ0NgIfNDzvTbDb4i:wjmgGPJxVxlBAiLSzxCips7XKo4=",
     * "url_file": "http://p593eqdrg.bkt.clouddn.com/?e=1521433712&token=AWTEpwVNmNcVjsIL-vS1hOabJ0NgIfNDzvTbDb4i:eZmbk-HFZAaAjmwf8i3lh9fAld0=",
     * "user_id": 1,
     * "user_name": "",
     * "group_id": null,  // 分组ID(json数组)
     * "created_at": 1521430098, // 创建时间
     * "open_set": 1
     * }
     * ],
     *  }
     */
    public function lists(Request $request)
    {
        $pan_director_id = (int)$request->input('pan_director_id');

        $user_id = $this->auth_user_id;
        $company_id = User::designCompanyId($user_id);


        // 用户所有用户组集合
        $group_id_arr = Group::userGroupIDList($user_id);

        // 用户 所属项目ID数组
        $item_id_arr = [];  // 暂无

        if ($pan_director_id != 0) {
            $dir = PanDirector::find($pan_director_id);
            if (!$dir) {
                return $this->response->array($this->apiError('not found dir!', 404));
            }
        }

        $list = PanDirector::query()
            // 组管理文件
            ->where(function ($query) use ($group_id_arr, $pan_director_id, $company_id) {
                $query->where('status', 1)
                    ->where('company_id', $company_id)
                    ->where('pan_director_id', $pan_director_id)
                    ->where('open_set', 1)
                    ->where(DB::raw('json_contains(group_id,\'' . json_encode($group_id_arr) . '\')'));
            })
            // 项目文件
            ->orWhere(function ($query) use ($item_id_arr, $pan_director_id, $company_id) {
                $query->where('status', 1)
                    ->where('company_id', $company_id)
                    ->where('pan_director_id', $pan_director_id)
                    ->where('open_set', 1)
                    ->whereIn('item_id', $item_id_arr);
            })
            // 私人文件
            ->orWhere(function ($query) use ($user_id, $pan_director_id, $company_id) {
                $query->where('status', 1)
                    ->where('company_id', $company_id)
                    ->where('pan_director_id', $pan_director_id)
                    ->where('open_set', 2)
                    ->where('user_id', $user_id);
            })
            ->get();

        return $this->response->collection($list, new YunpanListTransformer())->setMeta($this->apiSuccess());
    }
}