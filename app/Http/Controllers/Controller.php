<?php

namespace App\Http\Controllers;

use App\Http\ApiHelper;
use Dingo\Api\Routing\Helpers;
use Dompdf\Exception;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    use Helpers;
    use ApiHelper;

    //当前登陆用户
    protected $auth_user;

    //当前登陆用户ID
    protected $auth_user_id;

    /**
     * 默认页数
     */
    public $page = 1;

    /**
     * 默认每页数量
     */
    public $per_page = 10;

    public $big_per_page = 50;


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

                if ($user->status == -1) {
                    throw new Exception('禁止登录', 403);
                }
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
