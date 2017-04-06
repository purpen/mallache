<?php

/**
 * api 基类
 */
namespace App\Http\Controllers\Api\V1;

use App\Http\ApiHelper;
use App\Http\Controllers\Controller;
use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class BaseController extends Controller
{
    use Helpers;
    use ApiHelper;

    //当前登陆用户
    protected $auth_user;

    //当前登陆用户ID
    protected  $auth_user_id;


    public function __construct(Request $request)
    {
        $this->getAuthUser();
    }

    public function getAuthUser()
    {
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $this->auth_user = $user;
                $this->auth_user_id = $user->id;
                return;
            }
        } catch (TokenExpiredException $e) {
            // skip
        } catch (TokenInvalidException $e) {
            // skip
        } catch (JWTException $e) {
            // skip
        }

        // 设置默认值
        $this->auth_user = [];
        $this->auth_user_id = null;
    }
}