<?php

use Illuminate\Http\Request;

/**
 *
 */
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\Admin'], function ($api) {
    $api->group(['middleware' => ['jwt.auth']], function ($api){
        //项目列表
        $api->get('/admin/item/lists', 'ItemActionController@itemList');
        //为项目添加推荐公司
        $api->post('/admin/item/addDesignToItem', 'ItemActionController@addDesignToItem');
        //确认项目给推荐的设计公司
        $api->post('/admin/item/trueItem', 'ItemActionController@trueItem');
    });

});