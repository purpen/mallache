<?php

namespace App\Http\Controllers\Api\Jd;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Support\Facades\Cache;
use App\Models\DemandCompany;
use App\Helper\Sso;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;


class JdAccountController extends BaseController
{
    /**
     * @api {get} /jd/jdAccount 获取京东云access_token
     * @apiVersion 1.0.0
     * @apiName JdAccount account
     * @apiGroup JdAccount
     *
     * @apiParam {string} code
     */
    public function account(Request $request)
    {
        $code = $request->input('code');
        if(empty($code)){
            return $this->response->array($this->apiError('code值不能为空', 412));
        }
        $client_id = config('constant.client_id');
        $client_secret = config('constant.client_secret');
        $url = 'https://oauth2.jdcloud.com/token?client_id='.$client_id.'&grant_type=authorization_code&code='.$code.'&client_secret='.$client_secret;
        //获取access_token
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPGET, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $date = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($date, true);
        if(!empty($response['error'])){
            return $this->response->array($this->apiError($response['error_description'], 412));
        }
        $access_token = $response['access_token'];
        return $this->response->array($this->apiSuccess('获取成功', 200 , $access_token));

    }

    //获取京东用用户信息
    protected function jdAccount($access_token)
    {
        //拿token获取用户
        $account_url = 'https://oauth2.jdcloud.com/userinfo';
        $aHeader = array(
            'Authorization:Bearer '.$access_token,
        );
        $account_ch = curl_init();
        curl_setopt($account_ch, CURLOPT_HTTPGET, true);
        curl_setopt($account_ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($account_ch, CURLOPT_URL, $account_url);
        curl_setopt($account_ch, CURLOPT_HTTPHEADER, $aHeader);
        $account = curl_exec($account_ch);
        curl_close($account_ch);
        $response_account = json_decode($account, true);
        return $response_account['account'];

    }

    /**
     * @api {get} /jd/checkAccount 检查京东account是否存在
     * @apiVersion 1.0.0
     * @apiName JdAccount checkAccount
     * @apiGroup JdAccount
     *
     * @apiParam {string} access_token 京东云token
     */
    public function checkAccount(Request $request)
    {
        $access_token = $request->input('access_token');
        if(empty($access_token)){
            return $this->response->array($this->apiError('京东云token不能为空' , 412));
        }
        $jd_account = $this->jdAccount($access_token);

        $user = User::where('jd_account' , $jd_account)->first();
        if($user){
            $token = JWTAuth::fromUser($user);
            return $this->response->array($this->apiSuccess('获取成功', 200 , compact('token')));
        }else{
            return $this->response->array($this->apiError('用户没有绑定艺火账户', 404));
        }

    }

    /**
     * @api {post} /jd/bindingUser 已注册艺火，绑定京东云账户
     * @apiVersion 1.0.0
     * @apiName JdAccount bindingUser
     * @apiGroup JdAccount
     *
     * @apiParam {string} phone 手机号
     * @apiParam {string} password 密码
     * @apiParam {string} access_token 京东云token
     */
    public function bindingUser(Request $request)
    {
        try {

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
            //铟果的手机号直接绑定
            $access_token = $request->input('access_token');
            if(empty($access_token)){
                return $this->response->array($this->apiError('京东云token不能为空' , 412));
            }
            $jd_account = $this->jdAccount($access_token);


            if (!$this->phoneIsRegister($payload['phone'])) {
                return $this->response->array($this->apiError('手机号未注册', 401));
            }
            // 单点登录
            $ssoEnable = (int)config('sso.enable');
            if ($ssoEnable) {
                // 访问单点登录系统
                $ssoParam = array(
                    'account' => $payload['phone'],
                    'password' => $payload['password'],
                    'device_to' => 1,
                );
                $ssoResult = Sso::request(1, $ssoParam);
                if (!$ssoResult['success']) {
                    return $this->response->array($this->apiSuccess($ssoResult['message'], 403));
                }
            }

            $user = User::query()->where('account', $payload['phone'])->first();
            $source = $request->header('source-type') ?? 0;

            // 如果本地用户不存在，则创建
            if ($ssoEnable) {
                if (!$user) {
                    $user = User::query()
                        ->create([
                            'account' => $payload['phone'],
                            'phone' => $payload['phone'],
                            'username' => $payload['phone'],
                            'password' => bcrypt($payload['password']),
                            'child_account' => 0,
                            'source' => $source,
                            'jd_account' => $jd_account,
                            'type' => 1,
                        ]);

                    if (!$user) {
                        return $this->response->array($this->apiError('生成本地用户失败！', 500));
                    }
                }
            } else {
                if (!$user) {
                    return $this->response->array($this->apiError('用户不存在！', 404));
                }
                if (!Hash::check($payload['password'], $user->password)) {
                    return $this->response->array($this->apiSuccess('密码不正确', 403));
                }
            }
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            return $this->response->array($this->apiError('could_not_create_token', 500));
        }
        return $this->response->array($this->apiSuccess('绑定成功', 200 , compact('token')));
    }

    /**
     * @api {post} /jd/newBindingUser 新用户注册艺火，绑定京东云账户
     * @apiVersion 1.0.0
     * @apiName JdAccount newBindingUser
     * @apiGroup JdAccount
     *
     * @apiParam {string} phone 手机号
     * @apiParam {string} password 密码
     * @apiParam {integer} sms_code 短信验证码
     * @apiParam {string} access_token 京东云token
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

        $access_token = $request->input('access_token');
        if(empty($access_token)){
            return $this->response->array($this->apiError('京东云token不能为空' , 412));
        }
        $jd_account = $this->jdAccount($access_token);

        //验证手机验证码
        $key = 'sms_code:' . strval($payload['phone']);
        $sms_code_value = Cache::get($key);
        if (intval($payload['sms_code']) !== intval($sms_code_value)) {
            return $this->response->array($this->apiError('验证码错误', 412));
        } else {
            Cache::forget($key);
        }
        try {

            // 请求单点登录系统
            $ssoEnable = (int)config('sso.enable');
            if ($ssoEnable) {
                // 查看是否存在账号
                $ssoParam = array(
                    'phone' => $payload['phone']
                );
                $ssoResult = Sso::request(6, $ssoParam);
                if ($ssoResult['success']) {
                    return $this->response->array($this->apiError('用户系统已存在该账号!', 412));
                }
            }
            DB::beginTransaction();
            // 创建用户
            $user = User::query()
                ->create([
                    'account' => $payload['phone'],
                    'phone' => $payload['phone'],
                    'username' => $payload['phone'],
                    'type' => 1,
                    'password' => bcrypt($payload['password']),
                    'child_account' => 0,
                    'company_role' => 0,
                    'jd_account' => $jd_account,
                    'source' => $request->header('source-type') ?? 0,
                ]);
                DemandCompany::createCompany($user);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), $e->getCode());
            return $this->response->array($this->apiError('注册失败，请重试!', 412));
        }
        if ($user) {
            $token = JWTAuth::fromUser($user);
            if ($ssoEnable) {
                // 当前系统创建成功后再创建太火鸟用户
                $ssoParam = array(
                    'account' => $payload['phone'],
                    'phone' => $payload['phone'],
                    'password' => $payload['password'],
                    'device_to' => 1,
                    'status' => 1,
                );
                $ssoResult = Sso::request(2, $ssoParam);
                if (!$ssoResult['success']) {
                    return $this->response->array($this->apiError($ssoResult['message'], 412));
                }
            }
            return $this->response->array($this->apiSuccess('注册成功', 200, compact('token')));
        } else {
            return $this->response->array($this->apiError('注册失败，请重试!', 412));
        }

    }

    /**
     * @api {get} /jd/phoneState 检查手机号是否注册
     * @apiVersion 1.0.0
     * @apiName JdAccount phoneState
     * @apiGroup JdAccount
     *
     * @apiParam {string} phone 手机号
     */
    public function phoneState(Request $request)
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
            return $this->response->array($this->apiSuccess('已经存在,可以直接绑定', 412));
        }

        return $this->response->array($this->apiError('该账户不存在,需要重新绑定', 200));
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
}
