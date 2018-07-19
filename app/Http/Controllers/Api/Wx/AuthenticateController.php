<?php
/**
 * 用户登录注册
 *
 * @user llh
 */

namespace App\Http\Controllers\Api\Wx;

use App\Helper\Tools;
use App\Http\Transformer\UserTransformer;
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
    public function openid(Request $request)
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
                $wxUser->save();
            } else {
                //随机码
                $randomNumber = Tools::randNumber();
                // 创建用户
                User::query()
                    ->create([
                        'account' => $randomNumber,
                        'phone' => $randomNumber,
                        'username' => $randomNumber,
                        'type' => 1,
                        'password' => $randomNumber,
                        'child_account' => 0,
                        'company_role' => 0,
                        'source' => 0,
                        'from_app' => 1,
                        'wx_open_id' => $openid,
                        'session_key' => $new_mini->session_key,
                    ]);
            }
            //生成token
            $token = JWTAuth::fromUser($new_mini);
            return $this->response->array($this->apiSuccess('获取成功', 200, compact('token')));
        } else {
            return $this->response->array($this->apiError('code 已经失效', 412));
        }
    }
}