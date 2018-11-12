<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

/**
 * 前端版本
 *
 * Class VersionController
 * @package App\Http\Controllers\Api\V1
 */
class VersionController extends BaseController
{
    /**
     * @api {post} /setNewVersion 设置版本号
     * @apiVersion 1.0.0
     * @apiName  version setNew
     * @apiGroup Version
     * @apiParam {string} code 请求秘钥
     * @apiParam {string} number 版本号
     *
     * @apiSuccessExample 成功响应:
     * {
     * "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     * }
     */
    public function setNewVersion(Request $request)
    {
        $code = $request->input('code');
        $number = $request->input('number');

        if ($code != "taihuoniao" || empty($number)) {
            return $this->apiError();
        }

        Redis::set("d3ingoVersion", $number);
        return $this->apiSuccess();
    }

    /**
     * @api {get} /getVersion 获取版本号
     * @apiVersion 1.0.0
     * @apiName  version getVersion
     * @apiGroup Version
     *
     * @apiSuccessExample 成功响应:
     * {
     * "meta": {
     *      "message": "Success",
     *      "status_code": 200
     *      }
     * "data": {
     *      "number": "string",
     * }
     * }
     */
    public function getVersion()
    {
        $result = Redis::get("d3ingoVersion");
        if ($result) {
            return $this->apiSuccess("Success", 200, ['number' => $result]);
        } else {
            return $this->apiSuccess("Success", 200, ['number' => null]);
        }

    }

}