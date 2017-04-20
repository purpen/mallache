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
        //删除图片
        $api->delete('/upload/deleteFile/{asset_id}', ['as' => 'upload.deleteFile', 'uses' => 'UploadController@deleteFile']);

        //获取领域列表
        $api->get('/field', ['as' => 'field', 'uses' => 'CommonController@field']);
        //获取行业列表
        $api->get('/industry', ['as' => 'industry', 'uses' => 'CommonController@industry']);

        //需求公司信息
        $api->get('/demandCompany', ['as' => 'demandCompany.show', 'uses' => 'DemandCompanyController@show']);
        $api->put('/demandCompany', ['as' => 'demandCompany.update', 'uses' => 'DemandCompanyController@update']);
        $api->post('/demandCompany', ['as' => 'demandCompany.store', 'uses' => 'DemandCompanyController@store']);
        /**
         * 项目需求相关路由
         */
        //发布需求
        $api->post('/demand/release', ['as' => 'demand.release', 'uses' => 'DemandController@release']);
        //项目ID获取推荐的设计公司
        $api->get('/demand/recommendList/{item_id}', ['as' => 'demand.recommendList', 'uses' => 'DemandController@recommendList']);
        //选定设计公司推送项目需求
        $api->post('/demand/push', ['as' => 'demand.push', 'uses' => 'DemandController@push']);
        //用户项目信息列表
        $api->get('/demand/itemList', ['as' => 'demand.itemList', 'uses' => 'DemandController@itemList']);
        //项目推荐设计公司状态列表
        $api->get('/demand/itemDesignList/{item_id}', ['as' => 'demand.itemDesignList', 'uses' => 'DemandController@itemDesignList']);
        //确定合作的设计公司
        $api->post('/demand/trueDesign', ['as' => 'demand.trueDesign', 'uses' => 'DemandController@trueDesign']);
        //
        $api->post('/demand/trueContract', ['as' => 'demand.trueContract', 'uses' => 'DemandController@trueContract']);
        //项目类型、领域
        $api->resource('/demand', 'DemandController');
        //UX UI 设计详情
        $api->resource('/UDesign', 'UDesignInfoController');
        //产品设计详情
        $api->resource('/ProductDesign', 'ProductDesignInfoController');


        //设计公司信息
//        $api->resource('/designCompany', 'DesignCompanyController');
        $api->get('/designCompany', ['as' => 'designCompany.show', 'uses' => 'DesignCompanyController@show']);
        $api->put('/designCompany', ['as' => 'designCompany.update', 'uses' => 'DesignCompanyController@update']);
        $api->post('/designCompany', ['as' => 'designCompany.store', 'uses' => 'DesignCompanyController@store']);
        $api->get('/designCompany/otherIndex/{id}', ['as' => 'designCompany.otherIndex', 'uses' => 'DesignCompanyController@otherIndex']);
        //更新设计公司审核状态
        $api->put('designCompany/verifyStatus', [
            'as' => 'designCompany.verifyStatus', 'uses' => 'DesignCompanyController@verifyStatus'
        ]);
        $api->put('designCompany/unVerifyStatus', [
            'as' => 'designCompany.unVerifyStatus', 'uses' => 'DesignCompanyController@unVerifyStatus'
        ]);
        //更新设计公司状态
        $api->put('designCompany/okStatus', [
            'as' => 'designCompany.okStatus', 'uses' => 'DesignCompanyController@okStatus'
        ]);
        $api->put('designCompany/unStatus', [
            'as' => 'designCompany.unStatus', 'uses' => 'DesignCompanyController@unStatus'
        ]);

        //设计公司案例
        $api->resource('/designCase', 'DesignCaseController');
        //报价
        $api->resource('/quotation', 'QuotationController');
        //项目类型
        $api->resource('/designItem', 'DesignItemController');
        //栏目位
        $api->resource('/column', 'ColumnController');
        //分类
        $api->resource('/category', 'CategoryController');


    });
});
