<?php
/**
 * 用户登录注册
 *
 * @user llh
 */
namespace App\Http\Controllers\Api\V1;

use App\Helper\Tools;
use App\Jobs\SendOneSms;
use App\Models\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
     * @apiParam {integer} sms_code 短信验证码
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
            'password' => ['required', 'min:6'],
            'sms_code' => ['required', 'regex:/^[0-9]{6}$/']
        ];

        $payload = $request->only('account', 'password', 'sms_code');
        $validator = Validator::make($payload, $rules);
        if($validator->fails()){
            throw new StoreResourceFailedException('新用户注册失败！', $validator->errors());
        }

        //验证手机验证码
        $key = 'sms_code:' . strval($payload['account']);
        $sms_code_value = Cache::get($key);
        if(intval($payload['sms_code']) !== intval($sms_code_value)){
            return $this->response->array($this->apiError('验证码错误', 412));
        }else{
            Cache::forget($key);
        }

        // 创建用户
        $res = User::create([
            'account' => $payload['account'],
            'phone' => $payload['account'],
            'status' => 1,
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
     *      "token": "token"
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

            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return $this->response->array($this->apiError('invalid_credentials', 401));
            }
        } catch (JWTException $e) {
            return $this->response->array($this->apiError('could_not_create_token', 500));
        }
        // return the token
        return $this->response->array($this->apiSuccess('登录成功！', 200, compact('token')));
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
        JWTAuth::invalidate(JWTAuth::getToken());

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
     *       "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9"
     *    }
     *   }
     */
    public function upToken()
    {
        $token = JWTAuth::refresh();
        return $this->response->array($this->apiSuccess('更新Token成功！', 200, compact('token')));
    }

    /**
     * @api {post} /auth/sms 获取手机验证码
     * @apiVersion 1.0.0
     * @apiName user sms_code
     * @apiGroup User
     *
     * @apiParam {integer} phone
     *
     * @apiSuccessExample 成功响应:
     * {
     *     "meta": {
     *       "message": "请求成功！",
     *       "status_code": 200
     *     },
     *   }
     */
    public function getSmsCode(Request $request)
    {
        $rules = [
            'phone' => ['required','regex:/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/'],
        ];

        $payload = $request->only('phone');
        $validator = app('validator')->make($payload, $rules);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
        }

        $phone = $payload['phone'];

        //短信验证码加入缓存
        $key = 'sms_code:' . strval($phone);
        $sms_code = Tools::randNumber();
        Cache::put($key, $sms_code, 10);

        $text = ' 【太火鸟】验证码：' . $sms_code . '，切勿泄露给他人，如非本人操作，建议及时修改账户密码。';
        //插入单条短信发送队列
        $this->dispatch(new SendOneSms($phone,$text));

        return $this->response->array($this->apiSuccess('请求成功！', 200));
    }

    /**
     * @api {post} /auth/changePassword 修改密码
     * @apiVersion 1.0.0
     * @apiName user changePassword
     * @apiGroup User
     *
     * @apiParam {string} password
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *     "meta": {
     *       "message": "请求成功！",
     *       "status_code": 200
     *     },
     *     "data": {
     *       "token": "sdfs1sfcd"
     *    }
     *   }
     */
    public function changePassword(Request $request)
    {
        $newPassword = $request->input('password');
        $user  =  JWTAuth::parseToken()->authenticate();

        $user->password = bcrypt($newPassword);
        if($user->save()){
            $token = JWTAuth::refresh();
            return $this->response->array($this->apiSuccess('请求成功', 200, compact('token')));
        }else{
            return $this->response->array($this->apiError('Error', 500));
        }
    }

}