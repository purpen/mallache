<?php

use Illuminate\Http\Request;

/**
 *
 */
$api = app('Dingo\Api\Routing\Router');

//管理员权限
$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\Wx'], function ($api) {
        // 获取用户信息
        $api->get('/wechat/token', [
            'as' => 'wechat.token', 'uses' => 'AuthenticateController@token'
        ]);
});
