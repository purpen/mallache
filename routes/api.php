<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\V1'], function ($api) {

    //用户注册
    $api->post('/auth/register', [
        'as' => 'auth.register', 'uses' => 'AuthenticateController@register'
    ]);
    //用户登录
    $api->post('/auth/login', [
        'as' => 'auth.login', 'uses' => 'AuthenticateController@login'
    ]);
    //获取手机验证码
    $api->post('/auth/sms', ['as' => 'auth.sms', 'uses' => 'AuthenticateController@getSmsCode']);

    //生产七牛token
    $api->get('/upload/upToken' , [
        'as' => 'upload.token' , 'uses' => 'UploadController@upToken'
    ]);

    // 七牛图片上传回调地址
    $api->post('/asset/callback',[
        'as' => 'upload.callback', 'uses' => 'UploadController@callback'
    ]);


    /**
     * 需验证用户token
     */
    $api->group(['middleware' => ['jwt.auth']], function ($api){
        //用户退出
        $api->post('/auth/logout', [
            'as' => 'auth.logout', 'uses' => 'AuthenticateController@logout'
        ]);
        //刷新token
        $api->post('/auth/upToken', [
            'as' => 'auth.upToken', 'uses' => 'AuthenticateController@upToken'
        ]);
        //修改密码
        $api->post('/auth/changePassword', [
            'as' => 'auth.changePassword', 'uses' => 'AuthenticateController@changePassword'
        ]);

        //获取城市列表
        $api->get('/city', [
            'as' => 'city', 'uses' => 'CommonController@city'
        ]);

        //需求公司信息
        $api->resource('/demandCompany', 'DemandCompanyController');

    });
});
