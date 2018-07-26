<?php
/**
 * 用户登录注册
 *
 * @user llh
 */

namespace App\Http\Controllers\Api\Wx;

use App\Helper\Tools;
use App\Http\Transformer\UserTransformer;
use App\Jobs\SendOneSms;
use App\Models\DemandCompany;
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
     * @api {get} /wechat/token 获取token，并添加openid,session_key到用户表
     * @apiVersion 1.0.0
     * @apiName WxToken token
     * @apiGroup Wx
     *
     * @apiParam {string} code
     */
    public function token(Request $request)
    {
        $code = $request->input('code');
        if(empty($code)){
            return $this->response->array($this->apiError('code 不能为空', 412));
        }
        $config = config('wechat.mini_program.default');
        $mini = Factory::miniProgram($config);

        //获取openid和session_key
        $new_mini = $mini->auth->session($code);
        $openid = $new_mini['openid'] ?? '';
        if (!empty($openid)) {
            $wxUser = User::where('wx_open_id' , $openid)->first();
            //检测是否有openid,有创建，没有的话新建
            if ($wxUser) {
                $wxUser->session_key = $new_mini['session_key'];
                if($wxUser->save()){
                    //生成token
                    $token = JWTAuth::fromUser($wxUser);
                    return $this->response->array($this->apiSuccess('获取成功', 200, compact('token')));
                }
            } else {
                //随机码
                $randomNumber = Tools::randNumber();
                // 创建用户
                $user = User::query()
                    ->create([
                        'account' => $randomNumber,
                        'phone' => $randomNumber,
                        'username' => $randomNumber,
                        'type' => 1,
                        'password' => bcrypt($randomNumber),
                        'child_account' => 0,
                        'company_role' => 0,
                        'source' => 0,
                        'from_app' => 1,
                        'wx_open_id' => $openid,
                        'session_key' => $new_mini['session_key'],
                        'union_id' => $new_mini['unionId'] ?? '',
                    ]);
                if($user){
                    //创建需求公司
                    DemandCompany::createCompany($user->id);
                    //生成token
                    $token = JWTAuth::fromUser($user);
                    return $this->response->array($this->apiSuccess('获取成功', 200, compact('token')));
                }
            }

        }
    }

    /**
     * @api {get} /wechat/decryptionMessage 解密信息
     * @apiVersion 1.0.0
     * @apiName WxDecryptionMessage decryptionMessage
     * @apiGroup Wx
     *
     * @apiParam {string} iv
     * @apiParam {string} encryptData
     * @apiParam {string} token
     *
     */
    public function decryptionMessage(Request $request)
    {
        $rules = [
            'iv' => 'required',
            'encryptData' => 'required',
        ];

        $payload = $request->only('iv', 'encryptData');
        $validator = app('validator')->make($payload, $rules);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
        }

        $iv = $request->input('iv');
        $encryptData = $request->input('encryptData');

        $config = config('wechat.mini_program.default');
        $mini = Factory::miniProgram($config);

        $user = $this->auth_user;

        $session = [
            'session_key' => $user->session_key,
            'openid' => $user->openid
        ];

        $decryptedData = $mini->encryptor->decryptData($session, $iv, $encryptData);

        return $this->response->array($this->apiSuccess('解密成功', 200, $decryptedData));

    }

    /**
     * @api {post} /wechat/bindingUser 直接绑定用户
     * @apiVersion 1.0.0
     * @apiName WxBindingUser bindingUser
     * @apiGroup Wx
     *
     * @apiParam {string} phone 手机号
     * @apiParam {string} password 密码
     * @apiParam {string} token
     *
     */
    public function bindingUser(Request $request)
    {
        // 验证规则
        $rules = [
            'phone' => ['required', 'regex:/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/'],
            'password' => ['required', 'min:6']
        ];


        $payload = app('request')->only('phone', 'password');
        $validator = app('validator')->make($payload, $rules);

        // 验证格式
        if ($validator->fails()) {
            throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
        }

        $phone = $request->input('phone');
        $password = $request->input('password');
        //当前登陆的用户
        $loginUser = $this->auth_user;
        if ($loginUser){
            $loginUser->account = $phone;
            $loginUser->phone = $phone;
            $loginUser->username = $phone;
            $loginUser->password = bcrypt($password);
            if ($loginUser->save()){
                return $this->response->array($this->apiSuccess('绑定成功', 200));
            }

        } else {
            return $this->response->array($this->apiError('没有找到用户', 404));
        }

    }

    /**
     * @api {post} /wechat/newBindingUser 新用户，绑定
     * @apiVersion 1.0.0
     * @apiName WxNewBindingUser newBindingUser
     * @apiGroup Wx
     *
     * @apiParam {string} phone 手机号
     * @apiParam {string} password 密码
     * @apiParam {integer} sms_code 短信验证码
     * @apiParam {string} token
     *
     */
    public function newBindingUser(Request $request)
    {
        // 验证规则
        $rules = [
            'phone' => ['required', 'regex:/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/'],
            'sms_code' => ['required', 'regex:/^[0-9]{6}$/'],
            'password' => ['required', 'min:6']
        ];


        $payload = app('request')->only('phone', 'password', 'sms_code');
        $validator = app('validator')->make($payload, $rules);

        // 验证格式
        if ($validator->fails()) {
            throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
        }

        $phone = $request->input('phone');
        $password = $request->input('password');
        //验证手机验证码
        $key = 'sms_code:' . strval($payload['account']);
        $sms_code_value = Cache::get($key);
        if (intval($payload['sms_code']) !== intval($sms_code_value)) {
            return $this->response->array($this->apiError('验证码错误', 412));
        } else {
            Cache::forget($key);
        }

        //当前登陆的用户
        $loginUser = $this->auth_user;
        if ($loginUser){
            $loginUser->account = $phone;
            $loginUser->phone = $phone;
            $loginUser->username = $phone;
            $loginUser->password = bcrypt($password);
            if ($loginUser->save()){
                return $this->response->array($this->apiSuccess('绑定成功', 200));
            }

        } else {
            return $this->response->array($this->apiError('没有找到用户', 404));
        }

    }

    /**
     * @api {get} /wechat/phone 查看手机号是否绑定
     * @apiVersion 1.0.0
     * @apiName WxPhone phone
     * @apiGroup Wx
     *
     * @apiParam {string} phone 手机号
     * @apiParam {string} token
     *
     */
    public function phone(Request $request)
    {
        // 验证规则
        $rules = [
            'phone' => ['required', 'regex:/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/'],
        ];


        $payload = app('request')->only('phone');
        $validator = app('validator')->make($payload, $rules);

        // 验证格式
        if ($validator->fails()) {
            throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
        }

        $phone = $request->input('phone');
        $user = User::where('account' , $phone)->first();
        if ($user) {
            return $this->response->array($this->apiSuccess('已经存在,可以直接绑定', 200));
        } else {
            return $this->response->array($this->apiError('该账户不存在,需要从新绑定', 404));
        }
    }


    /**
     * @api {post} /wechat/sms 获取手机验证码
     * @apiVersion 1.0.0
     * @apiName WxSmsCode sms_code
     * @apiGroup Wx
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
            'phone' => ['required', 'regex:/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/'],
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
        $this->dispatch(new SendOneSms($phone, $text));

        return $this->response->array($this->apiSuccess('请求成功！', 200));
    }

    /**
     * @api {post} /wechat/changePassword 修改密码
     * @apiVersion 1.0.0
     * @apiName WxChangePassword changePassword
     * @apiGroup Wx
     *
     * @apiParam {string} password     新密码
     * @apiParam {string} repeatPassword     重复密码
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
            'repeatPassword' => 'required',
            'password' => 'required',
        ]);

        $repeatPassword = $request->input('repeatPassword');
        $newPassword = $request->input('password');

        if ($repeatPassword != $newPassword) {
            return $this->response->array($this->apiError('两次密码不一致', 412));
        }

        $user = JWTAuth::parseToken()->authenticate();

        $user->password = bcrypt($newPassword);
        if ($user->save()) {
            $token = JWTAuth::refresh();
            return $this->response->array($this->apiSuccess('请求成功', 200, compact('token')));
        } else {
            return $this->response->array($this->apiError('Error', 500));
        }
    }


}