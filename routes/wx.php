<?php

use Illuminate\Http\Request;

/**
 *
 */
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\Wx'], function ($api) {
        // 获取token
        $api->get('/wechat/token', [
            'as' => 'wechat.token', 'uses' => 'AuthenticateController@token'
        ]);
        //绑定用户
        $api->post('/wechat/bindingUser', [
            'as' => 'wechat.bindingUser', 'uses' => 'AuthenticateController@bindingUser'
        ]);
        //解密信息
        $api->get('/wechat/decryptionMessage', [
            'as' => 'wechat.decryptionMessage', 'uses' => 'AuthenticateController@decryptionMessage'
        ]);
});
