<?php

use Illuminate\Http\Request;

/**
 *
 */
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\Admin'], function ($api) {
    $api->group(['middleware' => ['jwt.auth', 'role:1']], function ($api){

        //项目列表
        $api->get('/admin/item/lists', 'ItemActionController@itemList');
        //为项目添加推荐公司
        $api->post('/admin/item/addDesignToItem', 'ItemActionController@addDesignToItem');
        //确认项目给推荐的设计公司
        $api->post('/admin/item/trueItem', 'ItemActionController@trueItem');


        //更新设计公司审核状态
        $api->put('/admin/designCompany/verifyStatus', [
            'as' => 'AdminDesignCompany.verifyStatus', 'uses' => 'AdminDesignCompanyController@verifyStatus'
        ]);
        $api->put('/admin/designCompany/unVerifyStatus', [
            'as' => 'AdminDesignCompany.unVerifyStatus', 'uses' => 'AdminDesignCompanyController@unVerifyStatus'
        ]);
        //更新设计公司状态
        $api->put('/admin/designCompany/okStatus', [
            'as' => 'AdminDesignCompany.okStatus', 'uses' => 'AdminDesignCompanyController@okStatus'
        ]);
        $api->put('/admin/designCompany/unStatus', [
            'as' => 'AdminDesignCompany.unStatus', 'uses' => 'AdminDesignCompanyController@unStatus'
        ]);
        $api->get('/admin/designCompany/lists', [
            'as' => 'AdminDesignCompany.lists', 'uses' => 'AdminDesignCompanyController@lists'
        ]);


        /**
         * 用户相关路由
         */
        //用户列表
        $api->get('/admin/user/lists', 'UserActionController@lists');
        //修改用户角色
        $api->post('/admin/user/changeRole', 'UserActionController@changeRole');
        //修改用户状态
        $api->post('/admin/user/changeStatus', 'UserActionController@changeStatus');

        /**
         * 支付单相关路由
         */
        //支付单列表
        $api->get('/admin/payOrder/lists', 'PayOrderActionController@lists');
        //后台确认项目支付单付款
        $api->post('/admin/payOrder/truePay', 'PayOrderActionController@truePay');
    });

});