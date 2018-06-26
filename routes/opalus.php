<?php

use Illuminate\Http\Request;

/**
 *
 */
$api = app('Dingo\Api\Routing\Router');

// 内部接口加密
$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\Opalus'], function ($api) {
    #$api->group(['middleware' => []], function ($api){

        // 设计公司列表
        $api->get('/opalus/company/list', 'CompanyController@lists');

        // 设计公司详细信息
        $api->get('/opalus/company/show', [
            'as' => 'Company.show', 'uses' => 'CompanyController@show'
        ]);

        //更新设计公司的雷达值
        $api->put('/opalus/company/update', 'CompanyController@update');


        // 设计案例列表
        $api->get('/opalus/design_case/list', 'DesignCaseController@lists');
        // 设计案例详情
        $api->get('/opalus/design_case/show', 'DesignCaseController@show');
    #});

});
