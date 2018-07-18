<?php

use Illuminate\Http\Request;

/**
 *
 */
$api = app('Dingo\Api\Routing\Router');

//管理员权限
$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\Wx'], function ($api) {
    $api->group(['middleware' => ['jwt.auth']], function ($api) {

    });

});
