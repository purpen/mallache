<?php

use Illuminate\Http\Request;

/**
 *
 */
$api = app('Dingo\Api\Routing\Router');

//管理员权限
$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\Admin'], function ($api) {
    $api->group(['middleware' => ['jwt.auth', 'role']], function ($api){

        //项目列表
        $api->get('/admin/item/lists', 'ItemActionController@itemList');
        //为项目添加推荐公司
        $api->post('/admin/item/addDesignToItem', 'ItemActionController@addDesignToItem');
        //确认项目给推荐的设计公司
        $api->post('/admin/item/trueItem', 'ItemActionController@trueItem');
        // 项目详情
        $api->get('/admin/item/show', 'ItemActionController@show');


        //更新设计公司审核状态
        $api->put('/admin/designCompany/verifyStatus', [
            'as' => 'AdminDesignCompany.verifyStatus', 'uses' => 'AdminDesignCompanyController@verifyStatus'
        ]);
        $api->put('/admin/designCompany/unVerifyStatus', [
            'as' => 'AdminDesignCompany.unVerifyStatus', 'uses' => 'AdminDesignCompanyController@unVerifyStatus'
        ]);
        $api->put('/admin/designCompany/noVerifyStatus', [
            'as' => 'AdminDesignCompany.noVerifyStatus', 'uses' => 'AdminDesignCompanyController@noVerifyStatus'
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
        // 设计公司详细信息
        $api->get('/admin/designCompany/show', [
            'as' => 'AdminDesignCompany.show', 'uses' => 'AdminDesignCompanyController@show'
        ]);
        // 公开或关闭设计公司资料
        $api->put('/admin/designCompany/openInfo',[
            'as' => 'AdminDesignCompany.openInfo', 'uses' => 'AdminDesignCompanyController@openInfo'
        ]);

        // 设计公司案例列表
        $api->get('/admin/designCase/lists', [
            'as' => 'DesignCase.lists', 'uses' => 'DesignCaseController@lists'
        ]);
        // 开放设计案例
        $api->put('/admin/designCase/openInfo', [
            'as' => 'DesignCase.openInfo', 'uses' => 'DesignCaseController@openInfo'
        ]);


        //更新需求公司审核状态
        $api->put('/admin/demandCompany/verifyStatus', [
            'as' => 'AdminDemandCompany.verifyStatus', 'uses' => 'AdminDemandCompanyController@verifyStatus'
        ]);
        $api->put('/admin/demandCompany/unVerifyStatus', [
            'as' => 'AdminDemandCompany.unVerifyStatus', 'uses' => 'AdminDemandCompanyController@unVerifyStatus'
        ]);
        $api->put('/admin/demandCompany/noVerifyStatus', [
            'as' => 'AdminDemandCompany.noVerifyStatus', 'uses' => 'AdminDemandCompanyController@noVerifyStatus'
        ]);
        $api->get('/admin/demandCompany/lists', [
            'as' => 'AdminDemandCompany.lists', 'uses' => 'AdminDemandCompanyController@lists'
        ]);
        // 需求公司详情
        $api->get('/admin/demandCompany/show', [
            'as' => 'AdminDemandCompany.show', 'uses' => 'AdminDemandCompanyController@show'
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

        /**
         * 提现相关
         */
        // 提现项目列表
        $api->get('/admin/withdrawOrder/lists', 'WithdrawOrderActionController@lists');
        // 确认提现单已提现
        $api->post('/admin/withdrawOrder/trueWithdraw', 'WithdrawOrderActionController@trueWithdraw');

        /**
         * 控制中心
         */
        // 后台控制台信息
        $api->get('/admin/survey/index', 'SurveyController@index');

        /**
         * 栏目位相关路由
         */
        // 保存栏目位文章
        $api->post('/column', 'ColumnController@store');
        // 更新栏目位文章
        $api->put('/column', 'ColumnController@update');
        // 文章详情
        $api->get('/column', 'ColumnController@show');
        // 栏目文章列表
        $api->get('/column/lists', 'ColumnController@lists');

    });

});