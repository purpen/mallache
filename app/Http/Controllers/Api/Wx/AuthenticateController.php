<?php
/**
 * 用户登录注册
 *
 * @user llh
 */

namespace App\Http\Controllers\Api\Wx;

use App\Helper\Tools;
use App\Helper\Sso;
use App\Http\Transformer\UserTransformer;
use App\Jobs\SendOneSms;
use App\Models\DemandCompany;
use App\Models\User;
use Dingo\Api\Exception\StoreResourceFailedException;
use EasyWeChat\Factory;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
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
        $session_key = $new_mini['session_key'];
        $session_id = Tools::microsecondUniqueStr();
        Cache::put($session_id, $session_key, 10080);
        return $this->response->array($this->apiSuccess('获取成功', 200 , compact('session_id')));
    }

    /**
     * @api {get} /wechat/decryptionMessage 解密信息
     * @apiVersion 1.0.0
     * @apiName WxDecryptionMessage decryptionMessage
     * @apiGroup Wx
     *
     * @apiParam {string} iv
     * @apiParam {string} encryptData
     * @apiParam {string} session_id
     * @apiParam {integer} is_login 是否需要解密
     *
     */
    public function decryptionMessage(Request $request)
    {
        $rules = [
            'iv' => 'required',
            'encryptData' => 'required',
        ];

        $payload = $request->only('iv', 'encryptData' , 'is_login' , 'session_id');
        $validator = app('validator')->make($payload, $rules);

        if ($validator->fails()) {
            // throw new StoreResourceFailedException('请求参数格式不对！', $validator->errors());
            return $this->response->array($this->apiError('请求参数格式不对！', 500));
        }

        try {
            $iv = $request->input('iv');
            $encryptData = $request->input('encryptData');
            $isLogin = $request->input('is_login');
            $sessionId = $request->input('session_id');
            if($isLogin != 1){
                return $this->response->array($this->apiSuccess('不需要解密', 200));
            }

            $config = config('wechat.mini_program.default');
            $mini = Factory::miniProgram($config);

            //获取session_key
            $session_key = Cache::get($sessionId);
            //解密信息
            $decryptedData = $mini->encryptor->decryptData($session_key, $iv, $encryptData);
        } catch (\Exception $e) {
            return $this->response->array($this->apiError($e->getMessage(), 500));       
        }

        if (!empty($decryptedData['unionId'])){

            // 请求单点登录系统
            $ssoEnable = (int)config('sso.enable');
            if ($ssoEnable) {
                // 快捷登录或注册
                $ssoParam = array(
                    'name' => $decryptedData['unionId'],
                    'evt' => 5,
                    'device_to' => 3,
                    'wx_uid' => $decryptedData['openId'],
                );
                $ssoResult = Sso::request(3, $ssoParam);
                if (!$ssoResult['success']) {
                    return $this->response->array($this->apiError($ssoResult['message'], 412));
                }
            }

            //检测用户是否存在，存在返回存在的用户
            $oldUser = User::where('wx_open_id' , $decryptedData['openId'])->where('union_id' , $decryptedData['unionId'])->first();
            if($oldUser && strlen($oldUser->phone) == 11){
                // 获取token
                $token = JWTAuth::fromUser($oldUser);
                return $this->response->array($this->apiSuccess('获取成功', 200, compact('token' , 'decryptedData')));
            }
            //随机码
            $randomNumber = Tools::url_short(Tools::microsecondUniqueStr());
            // 创建用户
            $user = User::query()
                ->create([
                    'account' => $randomNumber,
                    'phone' => $randomNumber,
                    'username' => $randomNumber,
                    'type' => 0,
                    'password' => bcrypt($randomNumber),
                    'child_account' => 0,
                    'company_role' => 0,
                    'source' => 0,
                    'from_app' => 1,
                    'wx_open_id' => $decryptedData['openId'],
                    'session_key' => $session_key,
                    'union_id' => $decryptedData['unionId'],
                ]);


            //生成token
            $token = JWTAuth::fromUser($user);
        }
        return $this->response->array($this->apiSuccess('解密成功', 200, compact('token' , 'decryptedData')));
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

        //已经存在的用户
        $oldUser = User::where('account' , $phone)->first();

        // 请求单点登录系统
        $ssoEnable = (int)config('sso.enable');
        if ($ssoEnable) {
            // sso登录
            $ssoParam = array(
                'phone' => $phone,
                'password' => $payload['password'],
                'device_to' => 3,
            );
            $ssoResult = Sso::request(1, $ssoParam);
            if (!$ssoResult['success']) {
                return $this->response->array($this->apiError($ssoResult['message'], 412));
            }

            if (!$oldUser) {
                // 创建用户
                $oldUser = User::query()
                    ->create([
                        'account' => $phone,
                        'phone' => $phone,
                        'username' => $phone,
                        'password' => bcrypt($payload['password']),
                        'child_account' => 0,
                        'type' => 0,
                        'company_role' => 0,
                        'source' => 0,
                        'from_app' => 1,
                    ]);
                if ($oldUser->type == 1) {
                    //创建需求公司
                    DemandCompany::createCompany($oldUser);
                }

                if (!$oldUser) {
                    return $this->response->array($this->apiError('本地创建用户失败！', 500));
                }
            }
        } else {
            if (!$oldUser) {
                return $this->response->array($this->apiError('用户不存在！', 404));
            }
            if (!Hash::check($payload['password'], $oldUser->password)) {
                return $this->response->array($this->apiError('密码不正确', 403));
            }
        }

        $token = JWTAuth::fromUser($oldUser);

        //当前登陆的用户
        $loginUser = $this->auth_user;
        //登陆的用户信息，绑定到老用户信息上，删除登陆的用户
        if (!$loginUser->union_id) {
            return $this->response->array($this->apiError('union_id不存在！', 500));       
        }
        if ($ssoEnable) {
            // sso更新
            $ssoParam = array(
                'name' => $loginUser->union_id,
                'evt' => 5,
                'wx_union_id' => 1,
                'wx_uid' => 1,
            );
            $ssoResult = Sso::request(7, $ssoParam);
            if (!$ssoResult['success']) {
                return $this->response->array($this->apiError($ssoResult['message'], 413));
            }

            // sso更新
            $ssoParam = array(
                'name' => $phone,
                'evt' => 2,
                'wx_union_id' => $loginUser->union_id,
                'wx_uid' => $loginUser->wx_open_id,
            );

            $ssoResult = Sso::request(4, $ssoParam);
            if (!$ssoResult['success']) {
                return $this->response->array($this->apiError($ssoResult['message'], 414));
            }
        }
        
        $oldUser->wx_open_id = $loginUser->wx_open_id;
        $oldUser->session_key = $loginUser->session_key;
        $oldUser->union_id = $loginUser->union_id;
        if($oldUser->save()){
            //判断登陆的手机号是否是6位，是的话删除，不是的话清除open_id,session_key
            if(strlen($loginUser->phone) < 11){
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
        //
        // 请求单点登录系统
        $ssoEnable = (int)config('sso.enable');
        if ($ssoEnable) {
            // 查看是否存在账号
            $ssoParam = array(
                'phone' => (string)$phone
            );
            $ssoResult = Sso::request(6, $ssoParam);
            if ($ssoResult['success']) {
                return $this->response->array($this->apiError('该手机号已经注册过，请直接绑定!', 412));
            }
        }

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
            if ($ssoEnable) {
                // 更新
                $ssoParam = array(
                    'name' => $loginUser->union_id,
                    'evt' => 5,
                    'account' => $phone,
                    'phone' => $phone,
                    'password' => $password,
                );
                $ssoResult = Sso::request(4, $ssoParam);
                if (!$ssoResult['success']) {
                    return $this->response->array($this->apiError($ssoResult['message'], 500));
                }
            }

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

        // 请求单点登录系统
        $ssoEnable = (int)config('sso.enable');
        if ($ssoEnable) {
            // 查看是否存在账号
            $ssoParam = array(
                'phone' => $phone
            );
            $ssoResult = Sso::request(6, $ssoParam);
            if ($ssoResult['success']) {
                return $this->response->array($this->apiSuccess('已经存在,可以直接绑定!', 200));
            }
        } else {
            $user = User::where('account', $phone)->first();
            if ($user) {
                return $this->response->array($this->apiSuccess('已经存在,可以直接绑定', 200));
            }
        }
        return $this->response->array($this->apiError('该账户不存在,需要重新绑定', 404));

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

        $user = $this->auth_user;

        $user->password = bcrypt($newPassword);
        if ($user->save()) {
            $token = JWTAuth::fromUser($user);
            return $this->response->array($this->apiSuccess('请求成功', 200, compact('token')));
        } else {
            return $this->response->array($this->apiError('Error', 500));
        }
    }

    protected function phoneIsRegister($account)
    {
        // 请求单点登录系统
        $ssoEnable = (int)config('sso.enable');
        if ($ssoEnable) {
            // 查看是否存在账号
            $ssoParam = array(
                'phone' => (string)$account
            );
            $ssoResult = Sso::request(6, $ssoParam);
            if ($ssoResult['success']) {
                return true;
            }
        } else {
            if (User::where('account', intval($account))->count() > 0) {
                return true;
            }
        }
        return false;
    }


    /**
     * @api {post} /wechat/findPassword 发送手机验证码，找回密码
     * @apiVersion 1.0.0
     * @apiName WxFindPassword findPassword
     * @apiGroup Wx
     *
     * @apiParam {string} phone 手机号
     * @apiParam {integer} sms_code 短信验证码
     * @apiParam {string} password
     *
     */
    public function findPassword(Request $request)
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

        // 请求单点登录系统
        $ssoEnable = (int)config('sso.enable');
        if ($ssoEnable) {
            // 查看是否存在账号
            $ssoParam = array(
                'name' => $request->input('phone'),
                'evt' => 2,
                'password' => $request->input('password'),
                'device_to' => 3,
            );
            $ssoResult = Sso::request(4, $ssoParam);
            if (!$ssoResult['success']) {
                return $this->response->array($this->apiError($ssoResult['message'], 500));
            }
        }

        $user = User::where('phone', intval($request->input('phone')))->first();

        if ($ssoEnable) {
            // 用户不存在，则注册
            if (!$user) {
                $user = User::query()
                    ->create([
                        'account' => $request->input('phone'),
                        'phone' => $request->input('phone'),
                        'username' => $request->input('phone'),
                        'password' => bcrypt($request->input('password')),
                        'child_account' => 0,
                        'source' => 0,
                        'from_app' => 1,
                    ]);

                if (!$user) {
                    return $this->response->array($this->apiError('生成本地用户失败！', 500));
                }

            }
        } else {
            if (!$user) {
                return $this->response->array($this->apiError('手机号还没有注册过！', 404));
            }
        }

        if (!$ssoEnable) {
            $newPassword = $request->input('password');
            $user->password = bcrypt($newPassword);
            if ($user->save()) {
                return $this->response->array($this->apiSuccess('修改成功', 200));
            } else {
                return $this->response->array($this->apiError('修改失败', 500));
            }
        }
        return $this->response->array($this->apiSuccess('修改成功!', 200));
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
        if($loginUser->phone == 11){
            return $this->response->array($this->apiSuccess('你已绑定了账户', 200 , $loginUser->account));
        }
        return $this->response->array($this->apiSuccess('没有绑定小程序', 200));
    }

    /**
     * @api {get} /wechat/smallImage 生成小程序二维码
     * @apiVersion 1.0.0
     * @apiName WxSmallImage smallImage
     * @apiGroup Wx
     *
     * @apiParam {string} scene
     * @apiParam {string} page
     */
    public function smallImage(Request $request)
    {
        //get获取access_token
        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".config('wechat.mini_program.default.app_id')."&secret=".config('wechat.mini_program.default.secret');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $dataBlock = curl_exec($ch);//这是json数据
        curl_close($ch);
        $res = json_decode($dataBlock, true); //接受一个json格式的字符串并且把它转换为 PHP 变量
        $accessToken = $res['access_token'];

        //post获取小程序图片
        $scene = $request->input('scene');
        $page = $request->input('page');
        $post_usl = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$accessToken;
        Log::info($request->all());
        $lineColor = '255，255，255';
        $lineColor = str_replace('，', ',', $lineColor);
        $lineColorArr = explode(',', $lineColor);
        $lineColorRgb['r'] = $lineColorArr[0];
        $lineColorRgb['g'] = $lineColorArr[1];
        $lineColorRgb['b'] = $lineColorArr[2];
        $data = array(
            'scene' => $scene,
            'page' => $page,
            'width' => 430,
            'is_hyaline' => false,
            'auto_color' => false,
            'line_color' => $lineColorRgb
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $post_usl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $output = curl_exec($ch);
        curl_close($ch);

        $accessKey = config('filesystems.disks.qiniu.access_key');
        $secretKey = config('filesystems.disks.qiniu.secret_key');
        $auth = new Auth($accessKey, $secretKey);

        $bucket = config('filesystems.disks.qiniu.bucket');

        $token = $auth->uploadToken($bucket);
        $key = 'smallWx/'.date("Ymd").'/'.uniqid();
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 put 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->put($token, $key, $output);
        $smallImg = config('filesystems.disks.qiniu.url').$key;
        return $this->response->array($this->apiSuccess('获取成功', 200 , compact('smallImg')));

    }
}
