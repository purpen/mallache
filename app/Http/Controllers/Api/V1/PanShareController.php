<?php

namespace App\Http\Controllers\Api\V1;

use App\Helper\Tools;
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

        $pan_directors = PanShare::whereIn('pan_director_id_arr', $pan_director_id_arr)->get();

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
        $pan_share->url_code = $url_code;
        if ($type == 2) { // 密码分享
            $password = Tools::createStr(4);
            $pan_share->password = $password;
        }
        $pan_share->save();

        return $this->response->array($this->apiSuccess('Success.', 200, $pan_share->info()));
    }

    public function isOpen(Request $request)
    {
        //
    }

    //查看分享
    public function show(Request $request)
    {
        //
    }

}