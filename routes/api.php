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

    //验证注册手机号
    $api->get('/auth/phoneState/{phone}', [
        'as' => 'auth.phoneState', 'uses' => 'AuthenticateController@phoneState'
    ]);
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
        //获取用户信息
        $api->get('/auth/user', ['as' => 'auth.user', 'uses' => 'AuthenticateController@authUser']);


        /**
         * 公共接口
         */
        //获取城市列表
        $api->get('/city', [
            'as' => 'city', 'uses' => 'CommonController@city'
        ]);
        //生产七牛token
        $api->get('/upload/upToken' , [
            'as' => 'upload.token' , 'uses' => 'UploadController@upToken'
        ]);
        //获取领域列表
        $api->get('/field', ['as' => 'field', 'uses' => 'CommonController@field']);

        //需求公司信息
        $api->get('/demandCompany', ['as' => 'demandCompany.show', 'uses' => 'DemandCompanyController@show']);
        $api->put('/demandCompany', ['as' => 'demandCompany.update', 'uses' => 'DemandCompanyController@update']);
        $api->post('/demandCompany', ['as' => 'demandCompany.store', 'uses' => 'DemandCompanyController@store']);
        /**
         * 项目需求相关路由
         */
        //项目类型、领域
        $api->resource('/demand', 'DemandController');
        //UX UI 设计详情
        $api->resource('/UDesign', 'UDesignInfoController');
        //产品设计详情
        $api->resource('/ProductDesign', 'ProductDesignInfoController');
        //发布需求
        $api->post('/demand/release', ['as' => 'demand.release', 'uses' => 'DemandController@release']);

        //设计公司信息
        $api->resource('/designCompany', 'DesignCompanyController');
        //设计公司案例
        $api->resource('/designCase', 'DesignCaseController');
        //报价
        $api->resource('/quotation', 'QuotationController');
        //项目类型
        $api->resource('/designItem', 'DesignItemController');
        //栏目位
        $api->resource('/column', 'ColumnController');
        //更新设计公司审核状态
        $api->put('designCompany/{id}/upStatus', [
            'as' => 'designCompany.status', 'uses' => 'DesignCompanyController@upStatus'
        ]);

    });
});
