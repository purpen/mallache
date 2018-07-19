<?php
/**
 * 用户登录注册
 *
 * @user llh
 */

namespace App\Http\Controllers\Api\Wx;

use App\Http\Transformer\UserTransformer;
use App\Models\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticateController extends BaseController
{
    /**
     * @api {get} /wechat/login
     * @apiVersion 1.0.0
     * @apiName WxUser user
     * @apiGroup WxUser
     *
     *
     * @apiSuccessExample 成功响应:
     * {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *      "data": {
     *          "id": 1,
     *          "account": "18629493221",
     *          "username": "",
     *          "email": null,
     *          "phone": "18629493221",
     *          "status": 0, //状态：；-1：禁用；0.激活;
     *          "item_sum": 0, //项目数量
     *          "price_total": "0.00", //总金额
     *          "price_frozen": "0.00", //冻结金额
     *           "image": "",
     *          "design_company_id": 1,
     *          "role_id": 1    // 角色：0.用户；1.管理员；
     *          "design_company_name":
     *          "design_company_abbreviation": '',
     *          "verify_status": -1,
     *          "demand_company_name":
     *          "demand_company_abbreviation": '',
     *          "demand_verify_status": -1,
     *          "source": 0, // 来源字段 0.默认 1.京东众创
     * }
     *   }
     */
    public function login()
    {
        $user = session('wechat.oauth_user');
        $config = config('wechat.mini_program');
        $app = Factory::miniProgram($config);
        return [$app,$user];
//        return $this->response->item($this->auth_user, new UserTransformer)->setMeta($this->apiMeta());
    }
}