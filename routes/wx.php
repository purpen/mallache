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
    //直接绑定用户
    $api->post('/wechat/bindingUser', [
        'as' => 'wechat.bindingUser', 'uses' => 'AuthenticateController@bindingUser'
    ]);
    //新用户绑定
    $api->post('/wechat/newBindingUser', [
        'as' => 'wechat.newBindingUser', 'uses' => 'AuthenticateController@newBindingUser'
    ]);
    //解密信息
    $api->get('/wechat/decryptionMessage', [
        'as' => 'wechat.decryptionMessage', 'uses' => 'AuthenticateController@decryptionMessage'
    ]);
    //检测手机号是否注册了
    $api->get('/wechat/phone', [
        'as' => 'wechat.phone', 'uses' => 'AuthenticateController@phone'
    ]);
    //获取验证码
    $api->post('/wechat/sms', [
        'as' => 'wechat.sms', 'uses' => 'AuthenticateController@sms'
    ]);
    //修改密码
    $api->post('/wechat/changePassword', [
        'as' => 'wechat.changePassword', 'uses' => 'AuthenticateController@changePassword'
    ]);
    //发送手机验证码，找回密码
    $api->post('/wechat/findPassword', [
        'as' => 'wechat.findPassword', 'uses' => 'AuthenticateController@findPassword'
    ]);
    /**
     * 发布项目
     */
    //选择类型
    $api->post('/wechat/demand/create', [
        'as' => 'wechat.demandCreate', 'uses' => 'DemandController@create'
    ]);
    //发布项目
    $api->post('/wechat/demand/release', [
        'as' => 'wechat.demandRelease', 'uses' => 'DemandController@release'
    ]);
    //获取推荐的设计公司
    $api->post('/wechat/demand/recommendList/{item_id}', [
        'as' => 'wechat.demandRecommendList', 'uses' => 'DemandController@recommendList'
    ]);
});
