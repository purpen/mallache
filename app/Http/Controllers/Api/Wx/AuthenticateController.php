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
        if (empty($code)) {
            return $this->response->array($this->apiError('code 不能为空', 412));
        }
        $config = config('wechat.mini_program.default');
        $mini = Factory::miniProgram($config);

        //获取openid和session_key
        $new_mini = $mini->auth->session($code);
        Log::info($new_mini);
        $openid = $new_mini['openid'] ?? '';
        if (!empty($openid)) {
            $wxUser = User::where('wx_open_id', $openid)->first();
            //检测是否有openid,有创建，没有的话新建
            if ($wxUser) {
                $wxUser->session_key = $new_mini['session_key'];
                if ($wxUser->save()) {
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
                        'union_id' => $new_mini['unionid'] ?? '',
                    ]);
                if ($user->type == 1) {
                    //创建需求公司
                    DemandCompany::createCompany($user);
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

        $decryptedData = $mini->encryptor->decryptData($user->session_key, $iv, $encryptData);
        Log::info($decryptedData);
        if (!empty($decryptedData['unionid'])){
            $user->union_id = $decryptedData['unionid'];
            $user->save();
        }

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
        $credentials = $request->only('phone', 'password');
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

        if (!$this->phoneIsRegister($credentials['phone'])) {
            return $this->response->array($this->apiError('手机号未注册', 404));
        }

        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->response->array($this->apiError('账户名或密码错误', 412));
        }
        //已经存在的用户
        $oldUser = User::where('account' , $phone)->first();

        //当前登陆的用户
        $loginUser = $this->auth_user;
        //登陆的用户信息，绑定到老用户信息上，删除登陆的用户
        $oldUser->wx_open_id = $loginUser->wx_open_id;
        $oldUser->session_key = $loginUser->session_key;
        $oldUser->union_id = $loginUser->union_id;
        if($oldUser->save()){
            //判断登陆的手机号是否是6位，是的话删除，不是的话清除open_id,session_key
            if(strlen($loginUser->phone) == 6){
                $loginUser->delete();
            } else {
                $loginUser->wx_open_id = "";
                $loginUser->session_key = "";
                $loginUser->union_id = "";
                $loginUser->save();
            }
            return $this->response->array($this->apiSuccess('绑定成功', 200, compact('token')));


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
        //查看是否注册了
        $oldUser = User::where('account' , $phone)->first();
        if($oldUser){
            return $this->response->array($this->apiError('该手机号已经注册过，请直接绑定', 412));
        }
        $password = $request->input('password');
        //验证手机验证码
        $key = 'sms_code:' . strval($payload['phone']);
        $sms_code_value = Cache::get($key);
        if (intval($payload['sms_code']) !== intval($sms_code_value)) {
            return $this->response->array($this->apiError('验证码错误', 412));
        } else {
            Cache::forget($key);
        }

        //当前登陆的用户
        $loginUser = $this->auth_user;
        if ($loginUser) {
            $loginUser->account = $phone;
            $loginUser->phone = $phone;
            $loginUser->username = $phone;
            $loginUser->password = bcrypt($password);
            if ($loginUser->save()) {
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
        $user = User::where('account', $phone)->first();
        if ($user) {
            return $this->response->array($this->apiSuccess('已经存在,可以直接绑定', 200));
        } else {
            return $this->response->array($this->apiError('该账户不存在,需要重新绑定', 404));
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
    public function sms(Request $request)
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
            'password' => 'required',
        ]);

        $newPassword = $request->input('password');

        $user = JWTAuth::parseToken()->authenticate();

        $user->password = bcrypt($newPassword);
        if ($user->save()) {
            $token = JWTAuth::refresh();
            return $this->response->array($this->apiSuccess('请求成功', 200, compact('token')));
        } else {
            return $this->response->array($this->apiError('Error', 500));
        }
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
     * @api {post} /wechat/findPassword 发送手机验证码，找回密码
     * @apiVersion 1.0.0
     * @apiName WxFindPassword findPassword
     * @apiGroup Wx
     *
     * @apiParam {string} phone 手机号
     * @apiParam {integer} sms_code 短信验证码
     * @apiParam {string} token
     *
     */
    public function findPassword(Request $request)
    {
        // 验证规则
        $rules = [
            'phone' => ['required', 'regex:/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/'],
            'sms_code' => ['required', 'regex:/^[0-9]{6}$/'],
        ];


        $payload = app('request')->only('phone', 'sms_code');
        $validator = app('validator')->make($payload, $rules);

        // 验证格式
        if ($validator->fails()) {
            throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
        }

        $phone = $request->input('phone');
        //查看是否注册了
        $oldUser = User::where('account' , $phone)->first();
        if(!$oldUser){
            return $this->response->array($this->apiError('没有找到该用户，请重新绑定', 404));
        }
        //验证手机验证码
        $key = 'sms_code:' . strval($payload['phone']);
        $sms_code_value = Cache::get($key);
        if (intval($payload['sms_code']) !== intval($sms_code_value)) {
            return $this->response->array($this->apiError('验证码错误', 412));
        } else {
            Cache::forget($key);
            return $this->response->array($this->apiSuccess('获取成功', 200));
        }
    }

    /**
     * @api {get} /wechat/checkAccount 检测账户是否绑定
     * @apiVersion 1.0.0
     * @apiName WxCheckAccount checkAccount
     * @apiGroup Wx
     *
     * @apiParam {string} token
     */
    public function checkAccount()
    {
        //当前登陆的用户
        $loginUser = $this->auth_user;
        if(!empty($loginUser->wx_open_id)){
            return $this->response->array($this->apiSuccess('你已绑定了账户', 200 , $loginUser->account));
        }
        return $this->response->array($this->apiSuccess('没有绑定小程序', 200));
    }
}