<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Transformer\RecycleBinTransformer;
use App\Models\RecycleBin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecycleBinController extends BaseController
{
    /**
     * @api {get} /recycleBin/lists 回收站列表
     * @apiVersion 1.0.0
     * @apiName recycleBin 回收站列表
     * @apiGroup recycleBin
     *
     * @apiParam {integer} page 页数
     * @apiParam {integer} per_page 页面条数
     * @apiParam {string} token
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
     *      "pan_director_id": 1, //文件ID
     *      "type": 1, // 类型：1.文件夹、2.文件
     *      "name": "第二层",
     *      "size": 0, // 大小 （字节byte）
     *      "mime_type": "", // 文件类型
     *      "user_id": 1,    // 操作人ID
     *      "user_name": "", // 操作人名称
     *      "created_at": 1521430098, // 删除时间
     *  }
     * ],
     *  }
     */
    public function lists(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;

        $user = $this->auth_user;
        $company_id = User::designCompanyId($this->auth_user_id);
        if ($user->isDesignAdmin()) {
            $lists = RecycleBin::where(['company_id' => $company_id])
                ->orderBy('id', 'desc')
                ->paginate($per_page);
        } else {
            $lists = RecycleBin::where(['user_id' => $this->auth_user_id, 'company_id' => $company_id])
                ->orderBy('id', 'desc')
                ->paginate($per_page);
        }


        return $this->response->paginator($lists, new RecycleBinTransformer())->setMeta($this->apiMeta());
    }

    /**
     * @api {delete} /recycleBin/delete 彻底删除文件（文件夹）
     * @apiVersion 1.0.0
     * @apiName recycleBin 彻底删除文件（文件夹）
     * @apiGroup recycleBin
     *
     * @apiParam {array} id_arr 回收站ID数组
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
    public function delete(Request $request)
    {
        $this->validate($request, [
            'id_arr' => 'required|array',
        ]);

        $id_arr = $request->input('id_arr');

        try {
            DB::beginTransaction();

            $company_id = User::designCompanyId($this->auth_user_id);
            foreach ($id_arr as $id) {
                if ($this->auth_user->isDesignAdmin()) {
                    $recycle_bin = RecycleBin::where(['id' => $id, 'company_id' => $company_id])->first();
                } else {
                    $recycle_bin = RecycleBin::where(['id' => $id, 'user_id' => $this->auth_user_id, 'company_id' => $company_id])->first();
                }

                if (!$recycle_bin) {
                    continue;
                }


                // 管理员和文件所属用户可永久删除
                if ($recycle_bin->panDirector && (($recycle_bin->panDirector->user_id == $this->auth_user_id) || $this->auth_user->isDesignAdmin())) {
                    //彻底删除文件（文件夹）并删除回收站记录
                    if (!$recycle_bin->deleteRecycle()) {
                        throw new \Exception("id=" . $id . ":删除失败");
                    }
                } else if (!$recycle_bin->panDirector) {
                    //彻底删除文件（文件夹）并删除回收站记录
                    if (!$recycle_bin->deleteRecycle()) {
                        throw new \Exception("id=" . $id . ":删除失败");
                    }
                } else {
                    continue;
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
     * @api {put} /recycleBin/restore 恢复文件（文件夹）
     * @apiVersion 1.0.0
     * @apiName recycleBin 恢复文件（文件夹）
     * @apiGroup recycleBin
     *
     * @apiParam {array} id_arr 回收站ID数组
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
    public function restore(Request $request)
    {
        $this->validate($request, [
            'id_arr' => 'required|array',
        ]);

        $id_arr = $request->input('id_arr');
        $company_id = User::designCompanyId($this->auth_user_id);
        try {
            DB::beginTransaction();

            foreach ($id_arr as $id) {
                $recycle_bin = RecycleBin::where(['id' => $id, 'company_id' => $company_id])->first();
                if (!$recycle_bin) {
                    throw new \Exception('id=' . $id . ': not found');
                }
                // 恢复文件（文件夹）并删除回收站记录
                if (!$recycle_bin->restoreRecycle()) {
                    throw new \Exception('id=' . $id . ': 恢复失败');
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

}