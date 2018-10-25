<?php

use Illuminate\Http\Request;

/**
 *
 */
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\Sd'], function ($api) {
    /**
     *
     * 需验证用户token
     */
    $api->group(['middleware' => ['jwt.auth']], function ($api) {
        /**
         * 发布需求
         */
        //需求列表
        $api->get('/sd/demand/demandList', [
            'as' => 'sd.demandDemandList', 'uses' => 'DesignDemandController@demandList'
        ]);
        //发布需求
        $api->post('/sd/demand/release', [
            'as' => 'sd.demandRelease', 'uses' => 'DesignDemandController@release'
        ]);
        //查看某个需求详情
        $api->get('/sd/demand/demandInfo', [
            'as' => 'sd.demandInfo', 'uses' => 'DesignDemandController@demandInfo'
        ]);
        //关闭某个需求
        $api->post('/sd/demand/demandShut', [
            'as' => 'sd.demandShut', 'uses' => 'DesignDemandController@demandShut'
        ]);
        //更改某个需求
        $api->post('/sd/demand/demandUpdate', [
            'as' => 'sd.demandUpdate', 'uses' => 'DesignDemandController@demandUpdate'
        ]);
        //设计公司查看需求列表
        $api->get('/sd/demand/designDemandList', [
            'as' => 'sd.designDemandList', 'uses' => 'DesignDemandController@designDemandList'
        ]);
        //设计公司查看某个需求详情
        $api->get('/sd/demand/designDemandInfo', [
            'as' => 'sd.designDemandInfo', 'uses' => 'DesignDemandController@designDemandInfo'
        ]);


        //设计公司收藏某个需求
        $api->post('/sd/design/collectDemand', [
            'as' => 'sd.collectDemand', 'uses' => 'DesignCollectDemandController@collectDemand'
        ]);
        //设计公司收藏列表
        $api->get('/sd/design/designCollectList', [
            'as' => 'sd.designCollectList', 'uses' => 'DesignCollectDemandController@designCollectList'
        ]);
        //设计公司收藏列表
        $api->post('/sd/design/cancelCollectDemand', [
            'as' => 'sd.cancelCollectDemand', 'uses' => 'DesignCollectDemandController@cancelCollectDemand'
        ]);
        //设计公司联系需求方
        $api->get('/sd/design/contactDemand', [
            'as' => 'sd.contactDemand', 'uses' => 'DesignCollectDemandController@contactDemand'
        ]);

    });

});
