<?php
/**
 * 用户登录注册
 *
 * @user llh
 */

namespace App\Http\Controllers\Api\V1;

use App\Helper\Tools;
use App\Http\Transformer\UserTransformer;
use App\Jobs\SendOneSms;
use App\Models\DemandCompany;
use App\Models\DesignCompanyModel;
use App\Models\User;
use Dingo\Api\Exception\StoreResourceFailedException;
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
     * @api {post} /auth/register 用户注册
     * @apiVersion 1.0.0
     * @apiName user register
     * @apiGroup User
     *
     * @apiParam {integer} type 用户类型：1.需求公司；2.设计公司；
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
     *     "data": {
     *          "token": ""
     *      }
     *   }
     */
    public function register(Request $request)
    {
        // 验证规则
        $rules = [
            'account' => ['required', 'unique:users', 'regex:/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/'],
            'password' => ['required', 'min:6'],
            'sms_code' => ['required', 'regex:/^[0-9]{6}$/'],
            'type' => ['required', Rule::in([1, 2])],
        ];

        $payload = $request->only('account', 'password', 'sms_code', 'type');
        $validator = Validator::make($payload, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('新用户注册失败！', $validator->errors());
        }

        //验证手机验证码
        $key = 'sms_code:' . strval($payload['account']);
        $sms_code_value = Cache::get($key);
        if (intval($payload['sms_code']) !== intval($sms_code_value)) {
            return $this->response->array($this->apiError('验证码错误', 412));
        } else {
            Cache::forget($key);
        }

        if ($payload['type'] == 1) {
            $company_role = 0;
        } else if ($payload['type'] == 2) {
            $company_role = 20;
        }

        // 创建用户
        $user = User::query()
            ->create([
                'account' => $payload['account'],
                'phone' => $payload['account'],
                'username' => $payload['account'],
                'type' => $payload['type'],
                'password' => bcrypt($payload['password']),
                'child_account' => 0,
                'company_role' => $company_role,
                'source' => $request->header('source-type') ?? 0,
            ]);
        if ($user->type == 1) {
            DemandCompany::createCompany($user->id);
        } else if ($user->type == 2) {
            DesignCompanyModel::createDesign($user->id);
        }
        if ($user) {
            $token = JWTAuth::fromUser($user);
            return $this->response->array($this->apiSuccess('注册成功', 200, compact('token')));
        } else {
            return $this->response->array($this->apiError('注册失败，请重试!', 412));
        }
    }

    /**
     * Aliases authenticate
     */
    public function login(Request $request)
    {
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
    public function authenticate(Request $request)
    {
        $credentials = $request->only('account', 'password');

        try {
            // 验证规则
            $rules = [
                'account' => ['required', 'regex:/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/'],
                'password' => ['required', 'min:6']
            ];


            $payload = app('request')->only('account', 'password');
            $validator = app('validator')->make($payload, $rules);

            // 验证格式
            if ($validator->fails()) {
                throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
            }

            if (!$this->phoneIsRegister($credentials['account'])) {
                return $this->response->array($this->apiError('手机号未注册', 401));
            }
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->response->array($this->apiError('账户名或密码错误', 401));
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
     * @apiParam {string} str 随机码
     * @apiParam {string} captcha 验证码
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
            'phone' => ['required', 'regex:/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/'],
            'str' => 'required|string',
            'captcha' => 'required|string',
        ];

        $payload = $request->only('phone', 'str', 'captcha');
        $validator = app('validator')->make($payload, $rules);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
        }

        // 验证验证码
        $str = $request->input('str');
        $captcha = $request->input('captcha');

        if (!Tools::checkCaptcha($str, $captcha)) {
            return $this->response->array($this->apiSuccess('验证码错误', 403));
        }


        $phone = $payload['phone'];

        //短信验证码加入缓存
        $key = 'sms_code:' . strval($phone);
        $sms_code = Tools::randNumber();
        Cache::put($key, $sms_code, 10);

        $text = ' 【太火鸟】验证码：' . $sms_code . '，切勿泄露给他人，如非本人操作，建议及时修改账户密码。';
        //插入单条短信发送队列
        $this->dispatch(new SendOneSms($phone, $text));

        return $this->response->array($this->apiSuccess('请求成功！', 200));
    }

    /**
     * @api {post} /auth/changePassword 修改密码
     * @apiVersion 1.0.0
     * @apiName user changePassword
     * @apiGroup User
     *
     * @apiParam {string} old_password 原密码
     * @apiParam {string} password     新密码
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
        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required',
        ]);

        $old_password = $request->input('old_password');
        $newPassword = $request->input('password');

        $user = JWTAuth::parseToken()->authenticate();

        if (!Hash::check($old_password, $user->password)) {
            return $this->response->array($this->apiError('原密码不正确', 403));
        }

        $user->password = bcrypt($newPassword);
        if ($user->save()) {
            $token = JWTAuth::refresh();
            return $this->response->array($this->apiSuccess('请求成功', 200, compact('token')));
        } else {
            return $this->response->array($this->apiError('Error', 500));
        }
    }

    /**
     * @api {get} /auth/phoneState/18624343456 验证手机号
     * @apiVersion 1.0.0
     * @apiName user phoneState
     * @apiGroup User
     *
     * @apiParam {int} phone
     *
     * @apiSuccessExample 成功响应:
     * {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *   }
     */
    public function phoneState($phone)
    {
        if ($this->phoneIsRegister($phone)) {
            return $this->response->array($this->apiError('手机号已注册！', 412));
        };

        return $this->response->array($this->apiSuccess());
    }

    protected function phoneIsRegister($account)
    {
        if (User::where('account', intval($account))->count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @api {get} /auth/user 获取用户信息
     * @apiVersion 1.0.0
     * @apiName user user
     * @apiGroup User
     *
     * @apiParam {string} token
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
    public function AuthUser()
    {
        return $this->response->item($this->auth_user, new UserTransformer)->setMeta($this->apiMeta());
    }


    /**
     * @api {post} /auth/forgetPassword 忘记找回密码
     * @apiVersion 1.0.0
     * @apiName user forgetPassword
     * @apiGroup User
     *
     * @apiParam {string} phone
     * @apiParam {integer} sms_code 短信验证码
     * @apiParam {string} password
     */
    public function forgetPassword(Request $request)
    {
        // 验证规则
        $rules = [
            'phone' => ['required', 'regex:/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/'],
            'password' => ['required', 'min:6'],
            'sms_code' => ['required', 'regex:/^[0-9]{6}$/'],
        ];

        $payload = $request->only('phone', 'password', 'sms_code');
        $validator = Validator::make($payload, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('找回密码失败！', $validator->errors());
        }

        //验证手机验证码
        $key = 'sms_code:' . strval($payload['phone']);
        $sms_code_value = Cache::get($key);
        if (intval($payload['sms_code']) !== intval($sms_code_value)) {
            return $this->response->array($this->apiError('验证码错误', 412));
        } else {
            Cache::forget($key);
        }
        $user = User::where('phone', intval($request->input('phone')))->first();
        if (!$user) {
            return $this->response->array($this->apiError('手机号还没有注册过！', 404));
        }

        $newPassword = $request->input('password');
        $user->password = bcrypt($newPassword);
        if ($user->save()) {
            return $this->response->array($this->apiSuccess('修改成功', 200));
        } else {
            return $this->response->array($this->apiError('修改失败', 500));
        }
    }

    /**
     *
     * @api {post} /auth/updateUser 修改用户资料
     * @apiVersion 1.0.0
     * @apiName user updateUser
     * @apiGroup User
     *
     *
     * @apiParam {string} realname 姓名
     * @apiParam {string} position 职位
     * @apiParam {string} email 邮箱
     * @apiParam {string} token
     */
    public function updateUser(Request $request)
    {
        $email = $request->input('email');
        $user_id = $this->auth_user_id;


        $all = $request->except(['token']);
        $user = User::where('id', $user_id)->first();

        if (!$user) {
            return $this->response->array($this->apiError('没有该用户', 404));
        }
        //空的邮箱直接跳过
        if (!empty($email)) {
            $users = User::where('email', $email)->count();
            if ($users > 0) {
                return $this->response->array($this->apiError('邮箱已被占用', 412));
            }
        }
        //移除空的字段不更改
        $new_all = array_diff($all, array(null));
        $user->update($new_all);

        return $this->response->array($this->apiSuccess('修改成功', 200));

    }

    /**
     * @api {get} /auth/fundInfo 用户钱包信息
     * @apiVersion 1.0.0
     * @apiName user /auth/fundInfo
     * @apiGroup User
     *
     * @apiParam {string} token
     *
     * @apiSuccessExample 成功响应:
     * {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *      "data": {
     *          "price_total": 1000,   // 总金额
     *          "price_frozen": 500，  // 冻结金额
     *          "price": 500,           // 可提现金额
     *      }
     *   }
     */
    public function fundInfo()
    {
        $user = $this->auth_user;
        $price = sprintf("%01.2f", $user->price_total - $user->price_frozen);
        $data = ['price_total' => $user->price_total, 'price_frozen' => $user->price_frozen, 'price' => $price];
        return $this->response->array($this->apiSuccess('Success', 200, $data));
    }


    /**
     * @api {post} /auth/childRegister 子账户注册
     * @apiVersion 1.0.0
     * @apiName user childRegister
     * @apiGroup User
     *
     * @apiParam {integer} invite_user_id 邀请用户id
     * @apiParam {string} account 子账户账号(手机号)
     * @apiParam {string} realname 姓名
     * @apiParam {string} password 设置密码
     * @apiParam {integer} sms_code 短信验证码
     *
     * @apiSuccessExample 成功响应:
     *  {
     *     "meta": {
     *       "message": "Success",
     *       "status_code": 200
     *     }
     *     "data": {
     *          "token": ""
     *      }
     *   }
     */
    public function childRegister(Request $request)
    {
        //检测邀请的用户是否是管理员和主账户
        $invite_user_id = $request->input('invite_user_id');
        $user = User::where('id', $invite_user_id)->first();
        if ($user) {
            if ($user->company_role == 0) {
                return $this->response->array($this->apiError('邀请的用户不是管理员或超级管理员', 403));
            }

        } else {
            return $this->response->array($this->apiError('没有找到该用户', 404));
        }

        //检测邀请的设计公司是否存在
        $design_company_id = $user->designCompanyId($invite_user_id);
        $design_company = DesignCompanyModel::where('id', $design_company_id)->first();
        if (!$design_company) {
            return $this->response->array($this->apiError('没有找到设计公司', 404));
        }


        // 验证规则
        $rules = [
            'account' => ['required', 'unique:users', 'regex:/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/'],
            'password' => ['required', 'min:6'],
            'sms_code' => ['required', 'regex:/^[0-9]{6}$/'],
            'realname' => ['required'],
        ];

        $payload = $request->only('account', 'password', 'sms_code', 'realname');
        $validator = Validator::make($payload, $rules);
        if ($validator->fails()) {
            throw new StoreResourceFailedException('新用户注册失败！', $validator->errors());
        }

        //验证手机验证码
        $key = 'sms_code:' . strval($payload['account']);
        $sms_code_value = Cache::get($key);
        if (intval($payload['sms_code']) !== intval($sms_code_value)) {
            return $this->response->array($this->apiError('验证码错误', 412));
        } else {
            Cache::forget($key);
        }


        // 创建用户
        $users = User::create([
            'account' => $payload['account'],
            'phone' => $payload['account'],
            'username' => $payload['account'],
            'realname' => $payload['realname'],
            'password' => bcrypt($payload['password']),
            'design_company_id' => $design_company_id,
            'invite_user_id' => $invite_user_id,
            'type' => 2,
            'child_account' => 1,
        ]);

        if ($users) {
            $token = JWTAuth::fromUser($users);
            return $this->response->array($this->apiSuccess('注册成功', 200, compact('token')));
        } else {
            return $this->response->array($this->apiError('注册失败，请重试!', 412));
        }
    }

    /**
     * @api {get} /auth/userId 根据用户id获取用户信息
     * @apiVersion 1.0.0
     * @apiName user userId
     * @apiGroup User
     *
     * @apiParam {integer} user_id 用户id
     * @apiParam {string} token
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
     * "role_id": 1    // 角色：0.用户；1.管理员；
     * }
     *   }
     */
    public function userId(Request $request)
    {
        $user_id = $request->input('user_id');
        $user = User::where('id', $user_id)->first();
        if (!$user) {
            return $this->response->array($this->apiError('没有找到该用户!', 404));

        }
        return $this->response->item($user, new UserTransformer)->setMeta($this->apiMeta());
    }

}