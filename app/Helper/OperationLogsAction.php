<?php

namespace App\Helper;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class OperationLogsAction
{

    // 请求对象
    protected $request = null;

    // 返回对象
    protected $response = null;

    // 用户model
    protected $auth_user = null;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->request = $response;
        $this->getAuthUser();
    }

    protected function getAuthUser()
    {
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $this->auth_user = $user;
                return;
            }
        } catch (\Exception $e) {
            // skip
        }
    }


    // 路由请求对应的log记录方法
    public function task()
    {
        Log::info($this->auth_user->phone . '我是一条记录');
    }

}