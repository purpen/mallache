<?php

use Illuminate\Http\Request;

/**
 *
 */
$api = app('Dingo\Api\Routing\Router');

//管理员权限
$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\Jd'], function ($api) {
    //京东帐号获取
    $api->get('/jd/jdAccount', [
        'as' => 'jdAccount.jdAccount', 'uses' => 'JdAccountController@account'
    ]);
    //检测帐号是否注册
    $api->get('/jd/checkAccount', [
        'as' => 'jdAccount.checkAccount', 'uses' => 'JdAccountController@checkAccount'
    ]);
    //老用户绑定
    $api->post('/jd/bindingUser', [
        'as' => 'jdAccount.bindingUser', 'uses' => 'JdAccountController@bindingUser'
    ]);
    //新用户绑定
    $api->post('/jd/newBindingUser', [
        'as' => 'jdAccount.newBindingUser', 'uses' => 'JdAccountController@newBindingUser'
    ]);
    //检测手机号是否注册艺火
    $api->get('/jd/phoneState', [
        'as' => 'jdAccount.phoneState', 'uses' => 'JdAccountController@phoneState'
    ]);

    $api->group(['middleware' => ['jwt.auth']], function ($api) {

        /**
         * 用户
         */
        //用户列表
        $api->get('/jd/user/lists', [
            'as' => 'JdUser.lists', 'uses' => 'UserJdController@lists'
        ]);
        //用户详情
        $api->get('/jd/user/show', [
            'as' => 'JdUser.show', 'uses' => 'UserJdController@show'
        ]);

        /**
         * 需求公司
         */
        $api->get('/jd/demandCompany/lists', [
            'as' => 'JdDemandCompany.lists', 'uses' => 'JdDemandCompanyController@lists'
        ]);
        // 需求公司详情
        $api->get('/jd/demandCompany/show', [
            'as' => 'JdDemandCompany.show', 'uses' => 'JdDemandCompanyController@show'
        ]);

        /**
         * 项目
         */
        //项目列表
        $api->get('/jd/item/lists', [
            'as' => 'JdItem.lists', 'uses' => 'JdItemController@itemList'
        ]);
        // 项目详情
        $api->get('/jd/item/show', [
            'as' => 'JdItem.show', 'uses' => 'JdItemController@show'
        ]);

        /**
         * 支付单
         */
        //支付单列表
        $api->get('/jd/payOrder/lists', [
            'as' => 'JdPayOrder.lists', 'uses' => 'JdPayOrderController@lists'
        ]);
        //详情
        $api->get('/jd/payOrder/show', [
            'as' => 'JdPayOrder.show', 'uses' => 'JdPayOrderController@show'
        ]);
        //确认支付单
        $api->post('/jd/payOrder/truePay', [
            'as' => 'JdPayOrder.truePay', 'uses' => 'JdPayOrderController@truePay'
        ]);

    });

});
