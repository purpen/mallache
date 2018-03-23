<?php

namespace App\Http\Controllers\Api\V1;

use App\Helper\Tools;
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
    // 创建文件分享
    /*public function create(Request $request)
    {
        $this->validate($request, [
            'pan_director_id' => 'required|integer',
            'type' => 'required|in:1,2|integer',
        ]);
        $pan_director_id = $request->input('pan_director_id');
        $type = $request->input('type');

        $pan_director = PanDirector::find($pan_director_id);
        if ($pan_director) {
            if ($pan_director->isPermission($this->auth_user)) {
                $pan_share = new PanShare();
                $pan_share->pan_director_id = $pan_director_id;
                $pan_share->user_id = $this->auth_user_id;
                $pan_share->type = $type;

                $url_code = Tools::microsecondUniqueStr();
                $pan_share->url_code = $url_code;
                if ($type == 1) { // 密码分享
                    $password = Tools::createStr(4);
                    $pan_share->password = $password;
                }
                $pan_share->save();

                return $this->response->array();
            }

        }

        return $this->response->array($this->apiError('not found', 404));
    }*/
}