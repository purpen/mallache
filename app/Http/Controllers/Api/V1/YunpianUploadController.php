<?php

namespace App\Http\Controllers\Api\V1;

use App\Helper\QiniuApi;
use App\Http\Transformer\YunpanListTransformer;
use App\Models\Group;
use App\Models\PanDirector;
use App\Models\PanFile;
use App\Models\RecycleBin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
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
        Log::info($callbackBody);
        Log::info($_SERVER);
        //回调的contentType
        $contentType = 'application/x-www-form-urlencoded';
        //回调的签名信息，可以验证该回调是否来自七牛
        $authorization = $_SERVER['HTTP_AUTHORIZATION'];
        //七牛回调的url，具体可以参考
        $url = config('filesystems.disks.yunpan_qiniu.call_back_url');
        $isQiniuCallback = $auth->verifyCallback($contentType, $authorization, $url, $callbackBody);

        if (!$isQiniuCallback) {  //验证失败
            $callBackDate = [
                'success' => 0,
                'message' => '回调签名验证失败',
            ];
            Log::info($callBackDate);
            return $this->response->array($callBackDate);
        } else {
            $pan_director_id = $request->input('pan_director_id') ?? 0;
            $user_id = $request->input('uid');

            $user = User::find($user_id);
            if (!$user) {
                return null;
            }

            if (PanDirector::isSameFile($pan_director_id, trim($request->input('name')), $user_id)) {
                $callBackDate = [
                    'success' => 0,
                    'message' => '存在同名文件',
                ];
                Log::info($callBackDate);
                return $this->response->array($callBackDate);
            }

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
                $pan_file->name = trim($request->input('name'));
                $pan_file->size = $request->input('size');
                $pan_file->width = $request->input('width');
                $pan_file->height = $request->input('height');
                $pan_file->mime_type = $request->input('mime');
                $pan_file->user_id = $user_id;
                $pan_file->count = 1;
                $pan_file->status = 0;
                $pan_file->url = $path;
                $pan_file->save();

                if ($pan_director_id != 0) {
                    //上级目录信息
                    $pan_dir = PanDirector::where(['id' => $pan_director_id, 'type' => 1])->first();
                    if (!$pan_dir) {
                        throw new \Exception('not found dir');
                    }
                }

                // 判断是否在企业根目录下创建或公开的
                if ($pan_director_id == 0 || ($pan_dir->open_set == 1 && $pan_dir->group_id == null && $pan_dir->item_id == null)) {

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
                    $pan_director->width = $pan_file->width;
                    $pan_director->height = $pan_file->height;
                    $pan_director->save();

                }
//                else if (($item_id = $pan_dir->item_id && $pan_dir->open_set == 1 && $pan_dir->group_id == null) || $user->isDesignAdmin()) { // 判断上级文件夹是否是项目文件夹
//                    // 判断用户是否在这个项目中
//                    // 项目管理未完成
//                    throw new \Exception('项目管理未完成');
//
//                }
                else if ($pan_dir->group_id !== null && $pan_dir->open_set == 1 && $pan_dir->item_id == null) {       // 判断上级文件夹是否是属于群组
                    $user_group_id_list = Group::userGroupIDList($user_id);
                    if (!empty(array_intersect(json_decode($pan_dir->group_id, true), $user_group_id_list)) || $user->isDesignAdmin()) {
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
                        $pan_director->width = $pan_file->width;
                        $pan_director->height = $pan_file->height;
                        $pan_director->save();

                    }
                } else if (($pan_dir->open_set == 2 && $pan_dir->user_id == $user_id) || $user->isDesignAdmin()) {  // 判断上级目录是不是私有的
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
                    $pan_director->width = $pan_file->width;
                    $pan_director->height = $pan_file->height;
                    $pan_director->save();

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
                    'success' => 0,
                    'message' => $e->getMessage(),
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
     * @apiParam {integer} open_set 隐私设置：1.公开 2.私有 （open_set=1、group_id_arr=[] 是公开）（open_set=2、group_id_arr=[] 是私有）open_set=1、group_id_arr=[1,2] 对应权限组）
     * @apiParam {array} group_id_arr 所属群组ID数组
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     },
     * "data": [
     *      "id": 2,
     *      "pan_director_id": 1, //上级文件ID
     *      "type": 1, // 类型：1.文件夹、2.文件
     *      "name": "第二层",
     *      "size": 0, // 大小 （字节byte）
     *      "mime_type": "", // 文件类型
     *      "url_small": "?e=1521433712&token=AWTEpwVNmNcVjsIL-vS1hOabJ0NgIfNDzvTbDb4i:wjmgGPJxVxlBAiLSzxCips7XKo4=",
     *      "url_file": "http://p593eqdrg.bkt.clouddn.com/?e=1521433712&token=AWTEpwVNmNcVjsIL-vS1hOabJ0NgIfNDzvTbDb4i:eZmbk-HFZAaAjmwf8i3lh9fAld0=",
     *      "user_id": 1,
     *      "user_name": "",
     *      "group_id": null,  // 分组ID(json数组)
     *      "created_at": 1521430098, // 创建时间
     *      "open_set": 1
     * ],
     *  }
     */
    public function createDir(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'pan_director_id' => 'required|integer',
        ]);

        $name = trim($request->input('name'));
        $pan_director_id = $request->input('pan_director_id');
        $user_id = $this->auth_user_id;

        if (PanDirector::isSameFile($pan_director_id, $name, $user_id)) {
            return $this->response->array($this->apiError('存在同名文件', 403));
        }

        $company_id = User::designCompanyId($user_id);
        if (!$company_id) {
            return null;
        }

        // 判断是否在企业根目录下创建
        if ($pan_director_id == 0) {
            $this->validate($request, [
                'open_set' => 'required|integer|in:1,2',
                'group_id_arr' => 'array'
            ]);

            try {
                DB::beginTransaction();

                $pan_director = new PanDirector();
                $pan_director->open_set = 1;
                $pan_director->group_id = json_encode($request->input('group_id_arr'));
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

                $open_set = $request->input('open_set');
                $group_id_arr = $request->input('group_id_arr') ?? [];

                $result = $this->rootSetPermission($pan_director, $open_set, $group_id_arr);
                if ($result[0] != 'ok') {
                    throw new \Exception($result[0], $result[1]);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage());
                return $this->response->array($this->apiError($e->getMessage(), $e->getCode()));
            }


            return $this->response->item($pan_director, new YunpanListTransformer())->setMeta($this->apiMeta());
        }

        //上级目录信息
        $pan_dir = PanDirector::where(['id' => $pan_director_id, 'type' => 1])->first();
        if (!$pan_dir) {
            return $this->response->array($this->apiError('not found dir!', 404));
        }
        // 判断是否公开的
        if ($pan_dir->open_set == 1 && $pan_dir->group_id == null && $pan_dir->item_id == null) {

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

            return $this->response->item($pan_director, new YunpanListTransformer())->setMeta($this->apiMeta());
        }

        // 判断上级文件夹是否是项目文件夹
        if (($pan_dir->open_set == 1 && $pan_dir->group_id == null && $item_id = $pan_dir->item_id) || $this->auth_user->isDesignAdmin()) {
            // 判断用户是否在这个项目中
            // 项目管理未完成
        }

        // 判断上级文件夹是否是属于群组
        if ($pan_dir->group_id !== null && $pan_dir->open_set == 1 && $pan_dir->item_id == null) {
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

                return $this->response->item($pan_director, new YunpanListTransformer())->setMeta($this->apiMeta());
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

            return $this->response->item($pan_director, new YunpanListTransformer())->setMeta($this->apiMeta());
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
     * @apiParam {integer} page 页数
     * @apiParam {integer} per_page 页面条数
     * @apiParam {integer} type 类型：1.文件夹 2.文件
     * @apiParam {integer} order_by 排序 1.时间 2.大小 3.名称
     * @apiParam {integer} ascend 排序类型： 1.正序 -1.倒序
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *          "message": "Success",
     *          "status_code": 200,
     *          "pagination": {
     *              "total": 1,
     *              "count": 1,
     *              "per_page": 10,
     *              "current_page": 1,
     *              "total_pages": 1,
     *              "links": []
     *          }
     *     }
     * "data": [
     *  {
     *      "id": 2,
     *      "pan_director_id": 1, //上级文件ID
     *      "type": 1, // 类型：1.文件夹、2.文件
     *      "name": "第二层",
     *      "size": 0, // 大小 （字节byte）
     *      "mime_type": "", // 文件类型
     *      "url_small": "?e=1521433712&token=AWTEpwVNmNcVjsIL-vS1hOabJ0NgIfNDzvTbDb4i:wjmgGPJxVxlBAiLSzxCips7XKo4=",
     *      "url_file": "http://p593eqdrg.bkt.clouddn.com/?e=1521433712&token=AWTEpwVNmNcVjsIL-vS1hOabJ0NgIfNDzvTbDb4i:eZmbk-HFZAaAjmwf8i3lh9fAld0=",
     *      "user_id": 1,
     *      "user_name": "",
     *      "group_id": null,  // 分组ID(json数组)
     *      "created_at": 1521430098, // 创建时间
     *      "open_set": 1
     *  }
     * ],
     *  }
     */
    public function lists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $pan_director_id = (int)$request->input('pan_director_id');
        $type = $request->input('type');

        $order_by = $request->input('order_by') ?? 1;
        $ascend = $request->input('ascend') ?? 1;
        switch ($order_by) {
            case 1:
                $order_by_str = 'id';
                break;
            case 2:
                $order_by_str = 'size';
                break;
            case 3:
                $order_by_str = 'name';
                break;
        }
        switch ($ascend) {
            case 1:
                $ascend_str = 'asc';
                break;
            case -1:
                $ascend_str = 'desc';
                break;
        }


        $user_id = $this->auth_user_id;
        $company_id = User::designCompanyId($user_id);

        if ($pan_director_id != 0) {
            $dir = PanDirector::find($pan_director_id);
            if (!$dir) {
                return $this->response->array($this->apiError('not found dir!', 404));
            }
        }


        if ($this->auth_user->isDesignAdmin()) {        // 管理员忽略权限限制
            $query = PanDirector::query();
            if ($type) {
                $query = $query->where('type', $type);
            }
            $list = $query->where('status', 1)
                ->where('company_id', $company_id)
                ->where('pan_director_id', $pan_director_id)
                ->where(function ($query) use ($user_id) {
                    $query->where('open_set', 1)
                        ->orWhere(['open_set' => 2, 'user_id' => $user_id]);
                })
                ->orderBy('type', 'asc')
                ->orderBy($order_by_str, $ascend_str)
                ->paginate($per_page);
        } else {
            // 用户所有用户组集合
            $group_id_arr = Group::userGroupIDList($user_id);

            // 用户 所属项目ID数组
            $item_id_arr = [];  // 暂无

            $list = PanDirector::query()
                // 组管理文件
                ->where(function ($query) use ($group_id_arr, $pan_director_id, $company_id, $type) {
                    if ($type) {
                        $query = $query->where('type', $type);
                    }
                    $query->where('status', 1)
                        ->where('company_id', $company_id)
                        ->where('pan_director_id', $pan_director_id)
                        ->where('open_set', 1)
                        ->where(DB::raw('json_contains(group_id,\'' . json_encode($group_id_arr) . '\')'));
                })
                // 项目文件
                ->orWhere(function ($query) use ($item_id_arr, $pan_director_id, $company_id, $type) {
                    if ($type) {
                        $query = $query->where('type', $type);
                    }
                    $query->where('status', 1)
                        ->where('company_id', $company_id)
                        ->where('pan_director_id', $pan_director_id)
                        ->where('open_set', 1)
                        ->whereIn('item_id', $item_id_arr);
                })
                // 私人文件
                ->orWhere(function ($query) use ($user_id, $pan_director_id, $company_id, $type) {
                    if ($type) {
                        $query = $query->where('type', $type);
                    }
                    $query->where('status', 1)
                        ->where('company_id', $company_id)
                        ->where('pan_director_id', $pan_director_id)
                        ->where('open_set', 2)
                        ->where('user_id', $user_id);
                })
                // 公共文件
                ->orWhere(function ($query) use ($user_id, $pan_director_id, $company_id, $type) {
                    if ($type) {
                        $query = $query->where('type', $type);
                    }
                    $query->where('status', 1)
                        ->where('company_id', $company_id)
                        ->where('pan_director_id', $pan_director_id)
                        ->where('open_set', 1)
                        ->where('group_id', null);
                })
                ->orderBy('type', 'asc')
                ->orderBy($order_by_str, $ascend_str)
                ->paginate($per_page);
        }

        // 获取当前文件夹信息
        if (isset($dir) && $dir instanceof PanDirector) {
            $p_info = $dir->info();
        } else {
            $p_info = [];
        }

        return $this->response->paginator($list, new YunpanListTransformer())->setMeta($this->apiMeta('Success', 200, ['info' => $p_info]));
    }


    /**
     * @api {get} /yunpan/typeLists  资源分类展示
     * @apiVersion 1.0.0
     * @apiName yunpan typeLists
     *
     * @apiGroup yunpan
     * @apiParam {string} token
     * @apiParam {integer} page 页数
     * @apiParam {integer} per_page 页面条数
     * @apiParam {integer} resource_type 资源分类展示 1.图片 2.视频 3.音频 4.文档 5.电子表格 6.演示文稿 7.PDF
     * @apiParam {integer} order_by 排序 1.时间 2.大小 3.名称
     * @apiParam {integer} ascend 排序类型： 1.正序 -1.倒序
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *          "message": "Success",
     *          "status_code": 200,
     *          "pagination": {
     *              "total": 1,
     *              "count": 1,
     *              "per_page": 10,
     *              "current_page": 1,
     *              "total_pages": 1,
     *              "links": []
     *          }
     *     }
     * "data": [
     *  {
     *      "id": 2,
     *      "pan_director_id": 1, //上级文件ID
     *      "type": 1, // 类型：1.文件夹、2.文件
     *      "name": "第二层",
     *      "size": 0, // 大小 （字节byte）
     *      "mime_type": "", // 文件类型
     *      "url_small": "?e=1521433712&token=AWTEpwVNmNcVjsIL-vS1hOabJ0NgIfNDzvTbDb4i:wjmgGPJxVxlBAiLSzxCips7XKo4=",
     *      "url_file": "http://p593eqdrg.bkt.clouddn.com/?e=1521433712&token=AWTEpwVNmNcVjsIL-vS1hOabJ0NgIfNDzvTbDb4i:eZmbk-HFZAaAjmwf8i3lh9fAld0=",
     *      "user_id": 1,
     *      "user_name": "",
     *      "group_id": null,  // 分组ID(json数组)
     *      "created_at": 1521430098, // 创建时间
     *      "open_set": 1
     *  }
     * ],
     *  }
     */
    public function typeLists(Request $request)
    {
        $this->validate($request, [
            'resource_type' => 'required|in:1,2,3,4,5,6,7',
        ]);
        $per_page = $request->input('per_page') ?? $this->per_page;
        $resource_type = $request->input('resource_type');

        $order_by = $request->input('order_by') ?? 1;
        $ascend = $request->input('ascend') ?? 1;
        switch ($order_by) {
            case 1:
                $order_by_str = 'id';
                break;
            case 2:
                $order_by_str = 'size';
                break;
            case 3:
                $order_by_str = 'name';
                break;
        }
        switch ($ascend) {
            case 1:
                $ascend_str = 'asc';
                break;
            case -1:
                $ascend_str = 'desc';
                break;
        }

        // 文件类型正则
        $mime_type_regexp = null;
        switch ($resource_type) {
            case 1:
                $mime_type_regexp = config('yunpan.mime_type.image');
                break;
            case 2:
                $mime_type_regexp = config('yunpan.mime_type.video');
                break;
            case 3:
                $mime_type_regexp = config('yunpan.mime_type.audio');
                break;
            case 4:
                $mime_type_regexp = config('yunpan.mime_type.document');
                break;
            case 5:
                $mime_type_regexp = config('yunpan.mime_type.sheet');
                break;
            case 6:
                $mime_type_regexp = config('yunpan.mime_type.powerpoint');
                break;
            case 7:
                $mime_type_regexp = config('yunpan.mime_type.pdf');
                break;
        }

        $user_id = $this->auth_user_id;
        $company_id = User::designCompanyId($user_id);

        if ($this->auth_user->isDesignAdmin()) {        // 管理员忽略权限限制
            $query = PanDirector::query();
            $list = $query->where('status', 1)
                ->where('company_id', $company_id)
                ->where(function ($query) use ($user_id) {
                    $query->where('open_set', 1)
                        ->orWhere(['open_set' => 2, 'user_id' => $user_id]);
                })
                ->whereRaw("mime_type REGEXP '" . $mime_type_regexp . "'")
                ->orderBy($order_by_str, $ascend_str)
                ->paginate($per_page);
        } else {
            // 用户所有用户组集合
            $group_id_arr = Group::userGroupIDList($user_id);

            // 用户 所属项目ID数组
            $item_id_arr = [];  // 暂无

            $list = PanDirector::query()
                // 组管理文件
                ->where(function ($query) use ($group_id_arr, $company_id, $mime_type_regexp) {
                    $query->where('status', 1)
                        ->where('company_id', $company_id)
                        ->where('open_set', 1)
                        ->where(DB::raw('json_contains(group_id,\'' . json_encode($group_id_arr) . '\')'))
                        ->whereRaw(DB::raw("mime_type REGEXP '" . $mime_type_regexp . "'"));
                })
                // 项目文件
                ->orWhere(function ($query) use ($item_id_arr, $company_id, $mime_type_regexp) {

                    $query->where('status', 1)
                        ->where('company_id', $company_id)
                        ->where('open_set', 1)
                        ->whereIn('item_id', $item_id_arr)
                        ->whereRaw(DB::raw("mime_type REGEXP '" . $mime_type_regexp . "'"));
                })
                // 私人文件
                ->orWhere(function ($query) use ($user_id, $company_id, $mime_type_regexp) {

                    $query->where('status', 1)
                        ->where('company_id', $company_id)
                        ->where('open_set', 2)
                        ->where('user_id', $user_id)
                        ->whereRaw(DB::raw("mime_type REGEXP '" . $mime_type_regexp . "'"));
                })
                // 公共文件
                ->orWhere(function ($query) use ($user_id, $company_id, $mime_type_regexp) {

                    $query->where('status', 1)
                        ->where('company_id', $company_id)
                        ->where('open_set', 1)
                        ->where('group_id', null)
                        ->whereRaw(DB::raw("mime_type REGEXP '" . $mime_type_regexp . "'"));
                })
                ->orderBy($order_by_str, $ascend_str)
                ->paginate($per_page);
        }


        return $this->response->paginator($list, new YunpanListTransformer())->setMeta($this->apiMeta());

    }


    /**
     * @api {put} /yunpan/setPermission  设置权限
     * @apiVersion 1.0.0
     * @apiName yunpan 设置权限
     *
     * @apiGroup yunpan
     *
     * @apiParam {string} token
     * @apiParam {integer} pan_director_id 文件ID
     * @apiParam {integer} open_set 隐私设置：1.公开 2.私有 （open_set=1、group_id_arr=[] 是公开）（open_set=2、group_id_arr=[] 是私有）open_set=1、group_id_arr=[1,2] 对应权限组）
     * @apiParam {array} group_id_arr 所属群组ID数组
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *  }
     */
    public function setPermission(Request $request)
    {
        $this->validate($request, [
            'pan_director_id' => 'required|integer',
            'open_set' => 'required|integer|in:1,2',
            'group_id_arr' => 'array'
        ]);

        $pan_director_id = $request->input('pan_director_id');
        $open_set = $request->input('open_set');
        $group_id_arr = $request->input('group_id_arr') ?? [];

        $pan_dir = PanDirector::find($pan_director_id);

        try {
            DB::beginTransaction();
            $result = $this->rootSetPermission($pan_dir, $open_set, $group_id_arr);
            if ($result[0] != 'ok') {
                throw new \Exception($result[0], $result[1]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return $this->response->array($this->apiError($e->getMessage(), $e->getCode()));
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * 设置根目录文件（文件夹）权限
     * @param PanDirector $pan_dir
     * @param int $open_set
     * @param array $group_id_arr
     * @return array
     */
    public function rootSetPermission(PanDirector $pan_dir, int $open_set, array $group_id_arr)
    {
        // 文件 不存在、系统创建、非二级目录 均不能修改权限
        if (!$pan_dir || $pan_dir->isAuto() || $pan_dir->pan_director_id != 0) {
            return ['not found dir!', 404];
        }

        // 判断是否是设计公司管理员
        if ($this->auth_user->isDesignAdmin()) {
            // 如果设为私有
            if ($open_set == 2) {
                if ($pan_dir->user_id != $this->auth_user_id) {
                    return ['无权限', 403];
                }
                $pan_dir->setPrivate();
            } else if ($open_set == 1 && empty($group_id_arr)) { //设置为公开
                $pan_dir->setPublic();
            } else if ($open_set == 1 && !empty($group_id_arr)) { // 设置权限组
                $pan_dir->setGroup(json_encode($group_id_arr));
            } else {
                return ['未知错误1', 403];
            }

        } else {  // 成员
            if ($pan_dir->user_id != $this->auth_user_id) {
                return ['未知错误2', 403];
            }
            // 如果设为私有
            if ($open_set == 2) {
                $pan_dir->setPrivate();
            } else if ($open_set == 1 && empty($group_id_arr)) { //设置为公开
                $pan_dir->setPublic();
            } else {
                return ['未知错误3', 403];
            }
        }

        return ['ok', 200];
    }


    /**
     * @api {put} /yunpan/delete  放入回收站
     * @apiVersion 1.0.0
     * @apiName yunpan 放入回收站
     *
     * @apiGroup yunpan
     *
     * @apiParam {string} token
     * @apiParam {array} id_arr 文件ID
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *  }
     */
    public function delete(Request $request)
    {
        $this->validate($request, [
            'id_arr' => 'required|array',
        ]);

        $pan_director_id_arr = $request->input('id_arr');

        DB::beginTransaction();
        try {
            foreach ($pan_director_id_arr as $pan_director_id) {
                $pan_dir = PanDirector::find($pan_director_id);

                // 文件不存在或当前用户没有权限不能删除
                if (!$pan_dir || !$pan_dir->isPermission($this->auth_user)) {
                    throw new \Exception('not found dir!');
                }

                // 修改文件状态为删除中
                if (!$pan_dir->deletingDir()) {
                    continue;
                }
                // 创建回收站记录
                RecycleBin::addRecycle($pan_dir, $this->auth_user_id);

            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return $this->response->array($this->apiError($e->getMessage()));
        }

        return $this->response->array($this->apiSuccess());
    }


    /**
     * @api {put} /yunpan/copy  文件复制
     * @apiVersion 1.0.0
     * @apiName yunpan copy
     *
     * @apiGroup yunpan
     *
     * @apiParam {string} token
     * @apiParam {array} from_id_arr 需要复制的文件ID数组
     * @apiParam {integer} to_id 目标文件夹ID
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *  }
     */
    public function copy(Request $request)
    {
        $this->validate($request, [
            'from_id_arr' => 'required|array',
            'to_id' => 'required|integer'
        ]);

        $from_id_arr = $request->input('from_id_arr');
        $to_id = $request->input('to_id');

        try {
            // 接收文件夹ID==0时不做验证
            if ($to_id != 0) {
                $to_pan_director = PanDirector::find($to_id);
                if (!$to_pan_director) {
                    throw new \Exception('not found to_id', 404);
                }

                if ($to_pan_director->isChild($from_id_arr)) {
                    throw new \Exception('目录复制操作错误', 403);
                }

                // 判断用户有无在该文件夹下创建文件的权限
                if (!$to_pan_director->isReceivePermission($this->auth_user)) {
                    throw new \Exception('not permission', 404);
                }
            }

            $from_pan_directors = PanDirector::whereIn('id', $from_id_arr)->get();
            if (!$from_pan_directors) {
                throw new \Exception('not found from_id', 404);
            }

            DB::beginTransaction();
            foreach ($from_pan_directors as $from_pan_director) {
                // 判断用户是否有移动该文件的权限
                if (!$from_pan_director->isPermission($this->auth_user)) {
                    throw new \Exception('not permission', 403);
                }

                if ($new_dir = $from_pan_director->copyDir($to_id)) {
                    //设置复制文件的权限
                    if ($to_id == 0) {  //公开
                        $new_dir->setPermission(1, null, null, $this->auth_user_id);
                    } else {
                        $new_dir->setPermission($to_pan_director->open_set, $to_pan_director->group_id, $to_pan_director->item_id, $this->auth_user_id);
                    }
                } else {
                    throw new \Exception('变更上级目录ID错误');
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return $this->response->array($this->apiError($e->getMessage(), $e->getCode()));
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {put} /yunpan/move  文件移动
     * @apiVersion 1.0.0
     * @apiName yunpan move
     *
     * @apiGroup yunpan
     *
     * @apiParam {string} token
     * @apiParam {array} from_id_arr 需要移动的文件ID数组
     * @apiParam {integer} to_id 目标文件夹ID
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *  }
     */
    public function move(Request $request)
    {
        $this->validate($request, [
            'from_id_arr' => 'required|array',
            'to_id' => 'required|integer'
        ]);

        $from_id_arr = $request->input('from_id_arr');
        $to_id = $request->input('to_id');

        try {
            // 接收文件夹ID==0时不做验证
            if ($to_id != 0) {
                $to_pan_director = PanDirector::find($to_id);
                if (!$to_pan_director) {
                    throw new \Exception('not found to_id', 404);
                }

                if ($to_pan_director->isChild($from_id_arr)) {
                    throw new \Exception('目录移动操作错误', 403);
                }

                // 判断用户有无在该文件夹下创建文件的权限
                if (!$to_pan_director->isReceivePermission($this->auth_user)) {
                    throw new \Exception('not permission', 404);
                }
            }

            $from_pan_directors = PanDirector::whereIn('id', $from_id_arr)->get();
            if (!$from_pan_directors) {
                throw new \Exception('not found from_id', 404);
            }

            DB::beginTransaction();
            foreach ($from_pan_directors as $from_pan_director) {
                // 判断用户是否有移动该文件的权限
                if (!$from_pan_director->isPermission($this->auth_user)) {
                    throw new \Exception('not permission', 403);
                }
                // 变更原文件上级目录ID
                $from_pan_director->pan_director_id = $to_id;
                if ($from_pan_director->save()) {
                    //设置复制文件的权限
                    if ($to_id == 0) {  //公开
                        $from_pan_director->setPermission(1, null, null, $this->auth_user_id);
                    } else {
                        $from_pan_director->setPermission($to_pan_director->open_set, $to_pan_director->group_id, $to_pan_director->item_id, $this->auth_user_id);
                    }
                } else {
                    throw new \Exception('变更上级目录ID错误');
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return $this->response->array($this->apiError($e->getMessage(), $e->getCode()));
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {put} /yunpan/editName  修改文件名称
     * @apiVersion 1.0.0
     * @apiName yunpan editName
     *
     * @apiGroup yunpan
     *
     * @apiParam {string} token
     * @apiParam {integer} id 文件ID
     * @apiParam {string} name 文件名称
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *  }
     */
    public function editName(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'name' => 'required'
        ]);

        $id = $request->input('id');
        $name = $request->input('name');

        $pan_dir = PanDirector::find($id);

        if (!$pan_dir || !$pan_dir->isPermission($this->auth_user)) {
            return $this->response->array($this->apiError('not found', 404));
        }

        if (PanDirector::isSameFile($pan_dir->pan_director_id, $name, $this->auth_user_id)) {
            return $this->response->array($this->apiError('存在同名文件', 403));
        }

        $pan_dir->name = trim($name);
        $pan_dir->save();

        return $this->response->array($this->apiSuccess());
    }


    /**
     * @api {get} /yunpan/search  全局搜索
     * @apiVersion 1.0.0
     * @apiName yunpan search
     *
     * @apiGroup yunpan
     * @apiParam {string} token
     * @apiParam {integer} page 页数
     * @apiParam {integer} per_page 页面条数
     * @apiParam {string} name 搜索名
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *          "message": "Success",
     *          "status_code": 200,
     *          "pagination": {
     *              "total": 1,
     *              "count": 1,
     *              "per_page": 10,
     *              "current_page": 1,
     *              "total_pages": 1,
     *              "links": []
     *          }
     *     }
     * "data": [
     *  {
     *      "id": 2,
     *      "pan_director_id": 1, //上级文件ID
     *      "type": 1, // 类型：1.文件夹、2.文件
     *      "name": "第二层",
     *      "size": 0, // 大小 （字节byte）
     *      "mime_type": "", // 文件类型
     *      "url_small": "?e=1521433712&token=AWTEpwVNmNcVjsIL-vS1hOabJ0NgIfNDzvTbDb4i:wjmgGPJxVxlBAiLSzxCips7XKo4=",
     *      "url_file": "http://p593eqdrg.bkt.clouddn.com/?e=1521433712&token=AWTEpwVNmNcVjsIL-vS1hOabJ0NgIfNDzvTbDb4i:eZmbk-HFZAaAjmwf8i3lh9fAld0=",
     *      "user_id": 1,
     *      "user_name": "",
     *      "group_id": null,  // 分组ID(json数组)
     *      "created_at": 1521430098, // 创建时间
     *      "open_set": 1
     *  }
     * ],
     *  }
     */
    public function search(Request $request)
    {
        $name = $request->input('name');
        $per_page = $request->input('per_page') ?? $this->per_page;

        $user_id = $this->auth_user_id;
        $company_id = User::designCompanyId($user_id);

        if ($this->auth_user->isDesignAdmin()) {        // 管理员忽略权限限制
            $list = PanDirector::query()
                ->where('name', 'like', '%' . $name . '%')
                ->where('status', 1)
                ->where('company_id', $company_id)
                ->where('open_set', 1)
                ->paginate($per_page);
        } else {
            // 用户所有用户组集合
            $group_id_arr = Group::userGroupIDList($user_id);

            // 用户 所属项目ID数组
            $item_id_arr = [];  // 暂无

            $list = PanDirector::query()
                // 组管理文件
                ->where(function ($query) use ($group_id_arr, $company_id, $name) {
                    $query
                        ->where('name', 'like', '%' . $name . '%')
                        ->where('status', 1)
                        ->where('company_id', $company_id)
                        ->where('open_set', 1)
                        ->where(DB::raw('json_contains(group_id,\'' . json_encode($group_id_arr) . '\')'));
                })
                // 项目文件
                ->orWhere(function ($query) use ($item_id_arr, $company_id, $name) {
                    $query->where('name', 'like', '%' . $name . '%')
                        ->where('status', 1)
                        ->where('company_id', $company_id)
                        ->where('open_set', 1)
                        ->whereIn('item_id', $item_id_arr);
                })
                // 私人文件
                ->orWhere(function ($query) use ($user_id, $company_id, $name) {
                    $query->where('name', 'like', '%' . $name . '%')
                        ->where('status', 1)
                        ->where('company_id', $company_id)
                        ->where('open_set', 2)
                        ->where('user_id', $user_id);
                })
                // 公共文件
                ->orWhere(function ($query) use ($user_id, $company_id, $name) {
                    $query->where('name', 'like', '%' . $name . '%')
                        ->where('status', 1)
                        ->where('company_id', $company_id)
                        ->where('open_set', 1)
                        ->where('group_id', null);
                })
                ->paginate($per_page);
        }

        return $this->response->paginator($list, new YunpanListTransformer())->setMeta($this->apiMeta());
    }

    const recentUseFile = 'recentUseFile:';


    /**
     * @api {post} /yunpan/recentUseLog  最近使用文件打点
     * @apiVersion 1.0.0
     * @apiName yunpan recentUseLog
     *
     * @apiGroup yunpan
     *
     * @apiParam {string} token
     * @apiParam {integer} id 文件ID
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *  }
     */
    public function recentUseLog(Request $request)
    {
        $id = $request->input('id');
        $file = PanDirector::where(['id' => $id, 'type' => 2])->first();
        if ($file) {
            if ($file->isPermission($this->auth_user)) {

                $key = self::recentUseFile . $this->auth_user_id;

                Redis::zadd($key, time(), $id);

                Redis::ZREMRANGEBYRANK($key, 0, -100);
            }
        }

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {get} /yunpan/recentUseFile  获取最近使用文件列表
     * @apiVersion 1.0.0
     * @apiName yunpan recentUseFile
     *
     * @apiGroup yunpan
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *   "data": [
     *  {
     *      "id": 2,
     *      "pan_director_id": 1, //上级文件ID
     *      "type": 1, // 类型：1.文件夹、2.文件
     *      "name": "第二层",
     *      "size": 0, // 大小 （字节byte）
     *      "mime_type": "", // 文件类型
     *      "url_small": "?e=1521433712&token=AWTEpwVNmNcVjsIL-vS1hOabJ0NgIfNDzvTbDb4i:wjmgGPJxVxlBAiLSzxCips7XKo4=",
     *      "url_file": "http://p593eqdrg.bkt.clouddn.com/?e=1521433712&token=AWTEpwVNmNcVjsIL-vS1hOabJ0NgIfNDzvTbDb4i:eZmbk-HFZAaAjmwf8i3lh9fAld0=",
     *      "user_id": 1,
     *      "user_name": "",
     *      "group_id": null,  // 分组ID(json数组)
     *      "created_at": 1521430098, // 创建时间
     *      "open_set": 1
     *  }
     * ],
     *  }
     */
    public function recentUseFile()
    {
        $key = self::recentUseFile . $this->auth_user_id;

        $data = Redis::ZREVRANGE($key, 0, -1);

        $list = PanDirector::whereIn('id', $data)
            ->orderBy(DB::raw('field(id,' . implode(',', $data) . ')'))->get();

        return $this->response->collection($list, new YunpanListTransformer())->setMeta($this->apiMeta());
    }

}






















