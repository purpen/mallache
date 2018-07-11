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

    });

});
