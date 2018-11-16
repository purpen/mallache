<?php

namespace App\Http\Controllers\Api\Jd;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Support\Facades\Cache;
use App\Models\DemandCompany;


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
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $date = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($date, true);
//        Log::info($response);
//        if(!empty($response['error'])){
//            return $this->response->array($this->apiError($response['error_description'], 412));
//        }
//        Log::info($response['access_token']);
//        $access_token = $response['access_token'];
        if(!empty($response['account'])){
            return $this->response->array($this->apiSuccess('获取成功', 200 , $response['account']));
        }
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
        $jd_account = $this->access_token($access_token);

        $user = User::where('jd_account' , $jd_account)->first();
        if($user){
            $token = JWTAuth::fromUser($user);
            return $this->response->array($this->apiSuccess('获取成功', 200 , compact('token')));
        }else{
            return $this->response->array($this->apiError('用户没有绑定艺火账户', 404));
        }

    }

    //获取京东云账户
    protected function access_token($access_token)
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
        //铟果的手机号直接绑定
        $phone = $request->input('phone');
        $access_token = $request->input('access_token');
        if(empty($access_token)){
            return $this->response->array($this->apiError('京东云token不能为空' , 412));
        }
        $jd_account = $this->access_token($access_token);
        //检测是否注册京东账户
        $jd_user = User::where('jd_account' , $jd_account)->first();
        if($jd_user){
            $token = JWTAuth::fromUser($jd_user);
            return $this->response->array($this->apiSuccess('绑定成功！', 200, compact('token')));
        }
        $user = User::where('account' , $phone)->first();
        if(!$user){
            return $this->response->array($this->apiError('还没有注册艺火', 404));
        }
        $data = [
            'phone' => $credentials['phone'],
            'password' => $credentials['password'],
        ];
        if (!$token = JWTAuth::attempt($data)) {
            return $this->response->array($this->apiError('账户名或密码错误', 412));
        }
        $user->jd_account = $jd_account;

        if($user->save()){
            return $this->response->array($this->apiSuccess('绑定成功', 200 , compact('token')));
        }
        return $this->response->array($this->apiError('绑定失败', 416));

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

        $phone = $request->input('phone');
        $access_token = $request->input('access_token');
        if(empty($access_token)){
            return $this->response->array($this->apiError('京东云token不能为空' , 412));
        }
        $user = User::where('account' , $phone)->first();
        if($user){
            return $this->response->array($this->apiError('该用户已注册', 412));
        }
        //验证手机验证码
        $key = 'sms_code:' . strval($payload['phone']);
        $sms_code_value = Cache::get($key);
        if (intval($payload['sms_code']) !== intval($sms_code_value)) {
            return $this->response->array($this->apiError('验证码错误', 412));
        } else {
            Cache::forget($key);
        }
        $jd_account = $this->access_token($access_token);
        //检测是否注册京东账户
        $jd_user = User::where('jd_account' , $jd_account)->first();
        if($jd_user){
            $token = JWTAuth::fromUser($jd_user);
            return $this->response->array($this->apiSuccess('绑定成功！', 200, compact('token')));
        }
        $user = User::query()
            ->create([
                'account' => $payload['phone'],
                'phone' => $payload['phone'],
                'username' => $payload['phone'],
                'type' => 1,
                'password' => bcrypt($payload['password']),
                'jd_account' => $request->input('jd_account'),
                'child_account' => 0,
                'company_role' => 0,
                'source' => 1,
            ]);
        $demand_company =  DemandCompany::createCompany($user);
        if($demand_company == true){
            $token = JWTAuth::fromUser($user);
            return $this->response->array($this->apiSuccess('绑定成功！', 200, compact('token')));
        }

        return $this->response->array($this->apiError('绑定失败', 412));

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
}
