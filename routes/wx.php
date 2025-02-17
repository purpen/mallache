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
    //获取验证码
    $api->post('/wechat/sms', [
        'as' => 'wechat.sms', 'uses' => 'AuthenticateController@sms'
    ]);
    //智能报价
    $api->post('/wechat/quotationAdd',[
        'as' => 'wechat.quotationAdd', 'uses' => 'QuotationController@quotationAdd'
    ]);
    //解密信息
    $api->get('/wechat/decryptionMessage', [
        'as' => 'wechat.decryptionMessage', 'uses' => 'AuthenticateController@decryptionMessage'
    ]);
    //获取smallImage
    $api->get('/wechat/smallImage', [
        'as' => 'wechat.smallImage', 'uses' => 'AuthenticateController@smallImage'
    ]);
    //语音转换文字
    $api->post('/wechat/voice', [
        'as' => 'wechat.voice', 'uses' => 'BaiDuVoiceController@voice'
    ]);
    //名字发布项目
    $api->post('/wechat/demand/nameRelease', [
        'as' => 'wechat.demandRelease', 'uses' => 'SmallDemandController@release'
    ]);
    //更改项目
    $api->put('/wechat/demand/smallUpdate', [
        'as' => 'wechat.smallUpdate', 'uses' => 'SmallDemandController@update'
    ]);
    //人员列表
    $api->get('/wechat/demand/users', [
        'as' => 'wechat.users', 'uses' => 'SmallDemandController@users'
    ]);
    //人员数量
    $api->get('/wechat/demand/usersCount', [
        'as' => 'wechat.usersCount', 'uses' => 'SmallDemandController@usersCount'
    ]);
    //设计公司客户
    $api->post('/wechat/demand/designAdd', [
        'as' => 'wechat.designAdd', 'uses' => 'SmallDemandController@designAdd'
    ]);
    //获取微信项目案例
    $api->get('/wechat/designCase', [
        'as' => 'wechat.designCase', 'uses' => 'DesignCaseController@lists'
    ]);
    //生成海报
    $api->post('/wechat/poster', [
        'as' => 'wechat.poster', 'uses' => 'PosterController@poster'
    ]);
    /**
     *
     * 需验证用户token
     */
    $api->group(['middleware' => ['jwt.auth']], function ($api) {
        //直接绑定用户
        $api->post('/wechat/bindingUser', [
            'as' => 'wechat.bindingUser', 'uses' => 'AuthenticateController@bindingUser'
        ]);
        //新用户绑定
        $api->post('/wechat/newBindingUser', [
            'as' => 'wechat.newBindingUser', 'uses' => 'AuthenticateController@newBindingUser'
        ]);
        //检测手机号是否注册了
        $api->get('/wechat/phone', [
            'as' => 'wechat.phone', 'uses' => 'AuthenticateController@phone'
        ]);
        //修改密码
        $api->post('/wechat/changePassword', [
            'as' => 'wechat.changePassword', 'uses' => 'AuthenticateController@changePassword'
        ]);
        //发送手机验证码，找回密码
        $api->post('/wechat/findPassword', [
            'as' => 'wechat.findPassword', 'uses' => 'AuthenticateController@findPassword'
        ]);
        //检测账号是否注册
        $api->get('/wechat/checkAccount', [
            'as' => 'wechat.checkAccount', 'uses' => 'AuthenticateController@checkAccount'
        ]);
        /**
         * 发布项目
         */
        //选择类型
        $api->post('/wechat/demand/create', [
            'as' => 'wechat.demandCreate', 'uses' => 'DemandController@create'
        ]);
        //选择类型
        $api->put('/wechat/demand/update', [
            'as' => 'wechat.demandUpdate', 'uses' => 'DemandController@update'
        ]);
        //发布项目
        $api->post('/wechat/demand/release', [
            'as' => 'wechat.demandRelease', 'uses' => 'DemandController@release'
        ]);
        //获取推荐的设计公司
        $api->get('/wechat/demand/recommendList/{item_id}', [
            'as' => 'wechat.demandRecommendList', 'uses' => 'DemandController@recommendList'
        ]);

    });

});
