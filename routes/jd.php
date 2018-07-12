<?php

use Illuminate\Http\Request;

/**
 *
 */
$api = app('Dingo\Api\Routing\Router');

//管理员权限
$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\Jd'], function ($api) {
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
        $api->get('/jd/payOrder/show', [
            'as' => 'JdPayOrder.show', 'uses' => 'JdPayOrderController@show'
        ]);

    });

});
