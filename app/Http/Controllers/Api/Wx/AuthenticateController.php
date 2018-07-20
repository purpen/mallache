<?php
/**
 * 用户登录注册
 *
 * @user llh
 */

namespace App\Http\Controllers\Api\Wx;

use App\Helper\Tools;
use App\Http\Transformer\UserTransformer;
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
        $openid = $new_mini->openid ?? '';
        if (!empty($openid)) {
            $wxUser = User::where('wx_open_id' , $openid)->first();
            //检测是否有openid,有创建，没有的话新建
            if ($wxUser) {
                $wxUser->session_key = $new_mini->session_key;
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
                        'session_key' => $new_mini->session_key,
                        'union_id' => $new_mini->unionId ?? '',
                    ]);
                if($user){
                    //创建需求公司
                    DemandCompany::createCompany($user->id);
                    //生成token
                    $token = JWTAuth::fromUser($user);
                    return $this->response->array($this->apiSuccess('获取成功', 200, compact('token')));
                }
            }

        } else {
            return $this->response->array($this->apiError('code 已经失效', 412));
        }
    }

    /**
     * @api {post} /wechat/decryptionMessage 解密信息
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

        $iv = $request->inpit('iv');
        $encryptData = $request->inpit('encryptData');

        $config = config('wechat.mini_program.default');
        $mini = Factory::miniProgram($config);

        $user = $this->auth_user;

        $decryptedData = $mini->encryptor->decryptData($user->session_key, $iv, $encryptData);

        return $this->response->array($this->apiSuccess('解密成功', 200, $decryptedData));

    }

    /**
     * @api {post} /wechat/bindingUser 绑定用户
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
}