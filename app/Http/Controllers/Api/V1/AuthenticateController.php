<?php
/**
 * 用户登录注册
 */
namespace App\Http\Controllers\Api\V1;

use App\Http\ApiHelper;
use App\Models\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticateController extends BaseController
{
    use Helpers;
    use ApiHelper;

    /**
     * @api {post} /auth/register 用户注册
     * @apiVersion 1.0.0
     * @apiName user register
     * @apiGroup User
     *
     * @apiParam {string} account 用户账号
     * @apiParam {string} password 设置密码
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *   }
     */
    public function register (Request $request)
    {
        // 验证规则
        $rules = [
            'account' => ['required', 'unique:users'],
            'password' => ['required', 'min:6']
        ];

        $payload = $request->only('account', 'password');
        $validator = Validator::make($payload, $rules);
        if($validator->fails()){
            throw new StoreResourceFailedException('新用户注册失败！', $validator->errors());
        }
        // 创建用户
        $res = User::create([
            'account' => $payload['account'],
            'phone' => $payload['account'],
            'email' => $payload['account'],
            'name' => $payload['account'],
            'type' => 0,
            'password' => bcrypt($payload['password']),
        ]);

        if ($res) {
            return $this->response->array($this->apiSuccess());
        } else {
            return $this->response->array($this->apiError('注册失败，请重试!', 412));
        }
    }

    /**
     * Aliases authenticate
     */
    public function login (Request $request) {
        return $this->authenticate($request);
    }

    /**
     * @api {post} /auth/login 用户登录
     * @apiVersion 1.0.0
     * @apiName user login
     * @apiGroup User
     *
     * @apiParam {string} account 用户账号
     * @apiParam {string} password 设置密码
     *
     * @apiSuccessExample 成功响应:
     *   {
     *     "meta": {
     *       "message": "登录成功！",
     *       "status_code": 200
     *     }
     *   }
     */
    public function authenticate (Request $request)
    {
        $credentials = $request->only('account', 'password');

        try {
            // 验证规则
            $rules = [
                'account' => ['required'],
                'password' => ['required', 'min:6']
            ];

            $payload = app('request')->only('account', 'password');
            $validator = app('validator')->make($payload, $rules);

            // 验证格式
            if ($validator->fails()) {
                throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
            }

            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return $this->response->array($this->apiError('invalid_credentials', 401));
            }
        } catch (JWTException $e) {
            return $this->response->array($this->apiError('could_not_create_token', 500));
        }

        // return the token
        return $this->response->array($this->apiSuccess('登录成功！', 200, compact('token', 'first_login')));
    }

}