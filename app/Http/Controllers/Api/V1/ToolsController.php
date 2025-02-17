<?php

namespace App\Http\Controllers\Api\V1;

use App\Helper\Tools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ToolsController extends BaseController
{
    // 返回图片验证码资源
    public function captcha(Request $request, $str)
    {
        if ($str) {
            Tools::captchaCreate($str);
        } else {
            echo 'error';
        }
    }

    /**
     * @api {get} /captcha/getCaptcha 获取验证码url
     * @apiVersion 1.0.0
     * @apiName captcha getCaptcha
     * @apiGroup Captcha
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *     "meta": {
     *       "message": "请求成功！",
     *       "status_code": 200
     *     },
     *     "data": {
     *          'url': '',    // 验证码图片资源url
     *          'str': 'sddfgrsdweads' // 随机字符串
     *      }
     *   }
     */
    public function getCaptcha(Request $request)
    {
        $str = Tools::microsecondUniqueStr();
        $url = route('captcha', $str);
        $data = [
            'url' => $url,
            'str' => $str
        ];

        return $this->response->array($this->apiSuccess('ok', 200, $data));
    }

    /**
     * @api {get} /logisticsLists 物流公司列表
     * @apiVersion 1.0.0
     * @apiName logistics logisticsLists
     * @apiGroup logistics
     *
     * @apiSuccessExample 成功响应:
     * {
     *     "meta": {
     *       "message": "请求成功！",
     *       "status_code": 200
     *     },
     *     "data": {
     *
     *      }
     *   }
     */
    public function logisticsLists()
    {
        $logistics = config('constant.logistics');
        return $this->response->array($this->apiSuccess('Success.', 200, $logistics));
    }
}