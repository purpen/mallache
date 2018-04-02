<?php

namespace App\Http\Controllers\Api\V1;

use App\Helper\Tools;
use App\Http\Transformer\YunpanListTransformer;
use App\Models\PanDirector;
use App\Models\PanShare;
use Illuminate\Http\Request;

/**
 * 云盘文件分享
 * Class PanShareController
 * @package App\Http\Controllers\Api\V1
 */
class PanShareController extends BaseController
{
    /**
     * @api {get} /yunpan/shareCreate  创建文件分享
     * @apiVersion 1.0.0
     * @apiName yunpan shareCreate
     *
     * @apiGroup yunpan
     *
     * @apiParam {string} token
     * @apiParam {array} pan_director_id_arr 文件ID数组
     * @apiParam {integer} type 分享类型：1.公开 2.私密
     * @apiParam {integer} share_time 有效时间：0.永久 7.七天 30.一个月
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     },
     * "data": {
     *      "id": 1,
     *      "type": "1", // 分享类型 1.公开 2.私密
     *      "url_code": "47d05e497c8a993757a3c853d1a21d39", // 分享唯一编码
     *      "password": "opwF", // 查看密码
     *      "share_time": "0"  有效时间（天）：0.永久 7.七天 30.一个月
     * }
     *  }
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'pan_director_id_arr' => 'required|array',
            'type' => 'required|in:1,2|integer',
            'share_time' => 'required|integer|in:0,7,30',
        ]);
        $pan_director_id_arr = $request->input('pan_director_id_arr');
        $type = $request->input('type');
        $share_time = $request->input('share_time');

        $pan_directors = PanDirector::whereIn('id', $pan_director_id_arr)->get();

        // 权限判断
        $data = [];
        foreach ($pan_directors as $pan_director) {
            if ($pan_director->isPermission($this->auth_user)) {
                $data[] = $pan_director->id;
            }
        }

        $pan_share = new PanShare();
        $pan_share->pan_director_id_arr = json_encode($data);
        $pan_share->user_id = $this->auth_user_id;
        $pan_share->type = $type;
        $pan_share->share_time = $share_time;

        $url_code = Tools::microsecondUniqueStr();
        $pan_share->url_code = $type . $url_code;  // 将分享类型拼接在随机随机字符串头部，用来判断分享类型
        if ($type == 2) { // 密码分享
            $password = Tools::createStr(4);
            $pan_share->password = $password;
        }
        $pan_share->save();

        return $this->response->array($this->apiSuccess('Success.', 200, $pan_share->info()));
    }


    /**
     * @api {get} /yunpan/shareShow 查看分享
     * @apiVersion 1.0.0
     * @apiName yunpan shareShow
     *
     * @apiGroup yunpan
     *
     * @apiParam {string} token
     * @apiParam {integer} page 页数
     * @apiParam {integer} per_page 页面条数
     * @apiParam {string} url_code 分享唯一编码
     * @apiParam {string} password 查看密码
     * @apiParam {integer} id 文件ID
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     },
     * "data": {
     *      "id": 29,
     *      "pan_director_id": 0,
     *      "type": 1,
     *      "name": "kiu8987",
     *      "size": 0,
     *      "mime_type": "",
     *      "url_small": null,
     *      "url_file": null,
     *      "user_id": 6,
     *      "user_name": null,
     *      "group_id": null,
     *      "created_at": 1521796620,
     *      "open_set": 1,
     *      "width": null,
     *      "height": null
     * }
     *  }
     */
    public function show(Request $request)
    {
        $per_page = $request->input('per_page') ?? $this->per_page;
        $url_code = $request->input('url_code');
        $password = $request->input('password');
        $id = $request->input('id') ?? null;

        try {
            $pan_share = PanShare::where('url_code', $url_code)->first();
            if (!$pan_share) {
                throw New \Exception('分享不存在', 404);
            }

            if ($pan_share->type == 2 && $pan_share->password != trim($password)) {
                throw New \Exception('查看密码不正确', 404);
            }

            $pan_director_id_arr = json_decode($pan_share->pan_director_id_arr, true);
            $pan_dir = null;
            if ($id) {
                $pan_dir = PanDirector::find($id);
                if ($pan_dir->isChild($pan_director_id_arr)) {
                    $lists = PanDirector::where(['pan_director_id' => $id, 'status' => 1])->paginate($per_page);
                } else {
                    throw New \Exception('分享不存在', 404);
                }

            } else {
                $lists = PanDirector::whereIn('id', $pan_director_id_arr)->where('status', 1)->paginate($per_page);
            }

            // 上级目录信息
            $pan_dir_info = $pan_dir ? $pan_dir->info() : null;

            return $this->response->paginator($lists, new YunpanListTransformer())->setMeta($this->apiMeta('Success.', 200, ['info' => $pan_dir_info]));
        } catch (\Exception $e) {
            return $this->response->array($this->apiError($e->getMessage(), $e->getCode()));
        }
    }

}