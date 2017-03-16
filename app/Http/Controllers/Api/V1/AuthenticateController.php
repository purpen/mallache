<?php
/**
 * 用户登录注册
 */
namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticateController extends BaseController
{
    /**
     * @api {post} /auth/register 用户注册
     * @apiVersion 1.0.0
     * @apiName user register
     * @apiGroup User
     *
     * @apiParam {string} account 用户账号(手机号)
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
            'account' => ['required', 'unique:users', 'regex:/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/'],
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
     *   "data": {
     *      "token": "token",
     *      "status": 0
     *    }
     *  }
     */
    public function authenticate (Request $request)
    {
        $credentials = $request->only('account', 'password');

        try {
            // 验证规则
            $rules = [
                'account' => ['required','regex:/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/'],
                'password' => ['required', 'min:6']
            ];

            $payload = app('request')->only('account', 'password');
            $validator = app('validator')->make($payload, $rules);

            // 验证格式
            if ($validator->fails()) {
                throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
            }

            // 验证是否首次登录
            $user = User::where('account', $request->input('account'))->first();
            if (empty($user)) {
                return $this->response->array($this->apiError('此账号不存在', 404));
            }
            // 首次登录
            $status = $user->status;

            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return $this->response->array($this->apiError('invalid_credentials', 401));
            }
        } catch (JWTException $e) {
            return $this->response->array($this->apiError('could_not_create_token', 500));
        }

        // 更新用户登录状态
        if (!$status) {
            $user->status = 1;
            $user->save();
        }

        // return the token
        return $this->response->array($this->apiSuccess('登录成功！', 200, compact('token', 'status')));
    }

    /**
     * @api {post} /auth/logout 退出登录
     *
     * @apiVersion 1.0.0
     * @apiName user logout
     * @apiGroup User
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "A token is required",
     *       "status_code": 500
     *     }
     *  }
     */
    public function logout()
    {
        // 强制Token失效
        $res = JWTAuth::invalidate(JWTAuth::getToken());

        return $this->response->array($this->apiSuccess());
    }

    /**
     * @api {post} /auth/upToken 更新或换取新Token
     * @apiVersion 1.0.0
     * @apiName user token
     * @apiGroup User
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *     "meta": {
     *       "message": "更新Token成功！",
     *       "status_code": 200
     *     },
     *     "data": {
     *       "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjIsImlzcyI6Imh0dHA6XC9cL2ZpZmlzaC5tZVwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTQ2OTg4NjExNCwiZXhwIjoxNDY5ODg5NzE0LCJuYmYiOjE0Njk4ODYxMTQsImp0aSI6IjAxN2JhNTRjNjJjMWU0ZWM4OTU1YzExYjg0MDk0YjIxIn0.G25OQH2047nC9_DLyfc6s88cm_5IdYuhIVxYgXGsDjk"
     *    }
     *   }
     */
    public function upToken()
    {
        $token = JWTAuth::refresh();
        return $this->response->array($this->apiSuccess('更新Token成功！', 200, compact('token')));
    }

}