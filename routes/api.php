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

    // 获取验证码url
    $api->get('/captcha/getCaptcha', [
        'as' => 'captcha.getCaptcha', 'uses' => 'ToolsController@getCaptcha'
    ]);
    // 验证码图片资源
    $api->get('/captcha/{str}', [
        'as' => 'captcha', 'uses' => 'ToolsController@captcha'
    ]);
    //验证注册手机号
    $api->get('/auth/phoneState/{phone}', [
        'as' => 'auth.phoneState', 'uses' => 'AuthenticateController@phoneState'
    ]);
    //用户注册
    $api->post('/auth/register', [
        'as' => 'auth.register', 'uses' => 'AuthenticateController@register'
    ]);
    //设计公司子用户注册
    $api->post('/auth/childRegister', [
        'as' => 'auth.childRegister', 'uses' => 'AuthenticateController@childRegister'
    ]);
    //用户登录
    $api->post('/auth/login', [
        'as' => 'auth.login', 'uses' => 'AuthenticateController@login'
    ]);
    //获取手机验证码
    $api->post('/auth/sms', ['as' => 'auth.sms', 'uses' => 'AuthenticateController@getSmsCode']);

    // 七牛图片上传回调地址
    $api->post('/asset/callback', [
        'as' => 'upload.callback', 'uses' => 'UploadController@callback'
    ]);

    // 云盘七牛上传回调地址
    $api->post('/yunpanCallback', [
        'as' => 'yunpanCallback', 'uses' => 'YunpianUploadController@yunpanCallback'
    ]);

    //支付宝异步回调接口
    $api->post('/pay/aliPayNotify', 'PayController@aliPayNotify');
    // 微信异步回调接口
    $api->post('/pay/wxPayNotify', 'PayController@wxPayNotify');
    // 京东支付异步回调接口
    $api->post('/pay/jdPayNotify', 'PayController@jdPayNotify');

    //忘记密码修改密码
    $api->post('/auth/forgetPassword', ['uses' => 'AuthenticateController@forgetPassword']);

    // 设计案例推荐列表
    $api->get('/designCase/openLists', 'DesignCaseController@openLists');
    // 公司案例ID查看详情
    $api->get('/designCase/{case_id}', 'DesignCaseController@show');
    //公司ID查看设计公司信息
    $api->get('/designCompany/otherIndex/{id}', ['as' => 'designCompany.otherIndex', 'uses' => 'DesignCompanyController@otherIndex']);
    //设计公司案例
    $api->get('/designCase/designCompany/{design_company_id}', 'DesignCaseController@lists');

    /**
     * 栏目位
     */
    // 栏目文章详情
    $api->get('/column', 'ColumnController@show');
    // 栏目文章列表
    $api->get('/column/lists', 'ColumnController@lists');

    /**
     * 分类列表
     */
    $api->get('/classification/list', 'ClassificationController@index');

    /**
     * 文章
     */
    // {get} /article/list 文章列表
    $api->get('/article/list', 'ArticleController@index');
    // 详情
    $api->get('/article', 'ArticleController@edit');

    /**
     * 奖项案例
     */
    // 保存
    $api->post('/awardCase', 'AwardCaseController@store');
    // 详情
    $api->get('/awardCase', 'AwardCaseController@show');
    // 列表
    $api->get('/awardCase/list', 'AwardCaseController@lists');


    /**
     * block
     */
    $api->get('/block', 'BlockController@show');

    /**
     * 常用网站
     */
    // 常用网站详情
    $api->get('/commonlyUsedUrls', 'CommonlyUsedUrlController@show');
    // 常用网站列表
    $api->get('/commonlyUsedUrls/list', 'CommonlyUsedUrlController@lists');

    /**
     * 趋势报告
     */
    //趋势报告列表
    $api->get('/trendReports/lists', 'TrendReportsController@lists');
    //趋势报告详情
    $api->get('/trendReports', 'TrendReportsController@show');

    /**
     * veer图片列表
     */
    $api->get('/veerImage/list', 'VeerController@lists');

    /**
     * 日历列表详情
     */
    //日历详情
    $api->get('/dateOfAward', 'DateOfAwardController@show');
    //日历周
    $api->get('/dateOfAward/week', 'DateOfAwardController@week');
    //日历月
    $api->get('/dateOfAward/month', 'DateOfAwardController@month');

    /**
     * 没登录前，根据字符串，查看用户id和设计公司id
     */
    //根据随机字符串查看用户id
    $api->get('/urlValue', 'UrlKeyValueController@urlValue');

    /**
     * 需验证用户token
     */
    $api->group(['middleware' => ['jwt.auth']], function ($api) {
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

        //修改用户资料
        $api->post('/auth/updateUser/{id}', [
            'as' => 'auth.updateUser', 'uses' => 'AuthenticateController@updateUser'
        ]);

        //用户钱包信息
        $api->get('/auth/fundInfo', ['as' => 'auth.fundInfo', 'uses' => 'AuthenticateController@fundInfo']);

        /**
         * 公共接口
         */
        //获取城市列表
        $api->get('/city', [
            'as' => 'city', 'uses' => 'CommonController@city'
        ]);
        //生产七牛token
        $api->get('/upload/upToken', [
            'as' => 'upload.token', 'uses' => 'UploadController@upToken'
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
//        $api->post('/demandCompany', ['as' => 'demandCompany.store', 'uses' => 'DemandCompanyController@store']);

        /**
         * 项目需求相关路由
         */
        /**
         * 发布需求保证金支付
         */
        //发布需求保证金支付---支付宝
        $api->get('/pay/demandAliPay', ['as' => 'pay.demandAliPay', 'uses' => 'PayController@demandAliPay']);
        // 发布需求保证金支付-微信
        $api->get('/pay/demandWxPay', 'PayController@demandWxPay');
        // 发布需求保证金支付-京东
        $api->get('/pay/demandJdPay', 'PayController@demandJdPay');

        /**
         * 支付项目尾款
         */
        // 支付宝支付项目尾款-支付宝
        $api->get('/pay/itemAliPay/{pay_order_id}', ['as' => 'pay.itemAliPay', 'uses' => 'PayController@itemAliPay']);
        //银行转账支付项目尾款
        $api->get('/pay/itemBankPay/{pay_order_id}', 'PayController@itemBankPay');
        // 支付项目尾款-微信
        $api->get('/pay/itemWxPay/{pay_order_id}', 'PayController@itemWxPay');
        // 支付项目尾款-京东
        $api->get('/pay/itemJdPay/{pay_order_id}', 'PayController@itemJdPay');

        //创建尾款支付订单
        $api->get('/pay/endPayOrder/{item_id}', 'PayController@endPayOrder');
        //查询支付状态
        $api->get('/pay/getPayStatus/{uid}', 'PayController@getPayStatus');

        //用户提现
        $api->post('/withdraw/create', 'WithdrawOrderController@create');
        $api->get('/withdraw/lists', 'WithdrawOrderController@lists');

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
        //拒绝设计公司报价
        $api->post('/demand/falseDesign', ['as' => 'demand.falseDesign', 'uses' => 'DemandController@falseDesign']);
        //确认合同
        $api->post('/demand/trueContract', ['as' => 'demand.trueContract', 'uses' => 'DemandController@trueContract']);
        //修改项目重新匹配
        $api->post('/demand/itemRestart', ['as' => 'demand.itemRestart', 'uses' => 'DemandController@itemRestart']);
        // 关闭项目
        $api->post('/demand/closeItem', 'DemandController@closeItem');
        // 需求公司验收项目已完成
        $api->post('/demand/trueItemDone/{item_id}', 'DemandController@trueItemDone');
        // 需求公司评价
        $api->post('/demand/evaluate', 'DemandController@evaluate');
        // 发布需求方确认项目阶段
        $api->post('/itemStage/demandFirmItemStage', 'ItemStageController@demandFirmItemStage');
        // {delete} /demand/{id} 删除项目
        $api->delete('/demand/trueItemDone/{id}', 'DemandController@destroy');

        // 获取当前信息匹配到的公司数量
        $api->post('/demand/matchingCount/', 'DemandController@matchingCount');
        //项目类型、领域
        $api->resource('/demand', 'DemandController');
        //UX UI 设计详情
        $api->resource('/UDesign', 'UDesignInfoController');
        //产品设计详情
        $api->resource('/ProductDesign', 'ProductDesignInfoController');
        //合同
        $api->resource('/contract', 'ContractController');
        $api->post('/contract/ok', 'ContractController@okContract');

        //设计公司信息
//        $api->resource('/designCompany', 'DesignCompanyController');
        $api->get('/designCompany', ['as' => 'designCompany.show', 'uses' => 'DesignCompanyController@show']);
        $api->put('/designCompany', ['as' => 'designCompany.update', 'uses' => 'DesignCompanyController@update']);
        $api->post('/designCompany', ['as' => 'designCompany.store', 'uses' => 'DesignCompanyController@store']);


        // 设计案例图片添加描述
        $api->put('/designCase/imageSummary', 'DesignCaseController@imageSummary');
        $api->resource('/designCase', 'DesignCaseController');


        //报价
        $api->resource('/quotation', 'QuotationController');
        //项目类型
        $api->resource('/designItem', 'DesignItemController');

        //分类
        $api->resource('/category', 'CategoryController');
        //项目阶段
        $api->resource('/itemStage', 'ItemStageController');
        //设计公司项目展示
        $api->get('/itemStage/designCompany/lists', 'ItemStageController@designLists');
        //需求公司项目展示
        $api->get('/itemStage/demand/lists', ['as' => 'itemStage.demandLists', 'uses' => 'ItemStageController@demandLists']);

        //更新项目阶段发布状态
        $api->put('/itemStage/ok/status', [
            'as' => 'itemStage.okStatus', 'uses' => 'ItemStageController@okStatus'
        ]);
        $api->put('/itemStage/un/status', [
            'as' => 'itemStage.unStatus', 'uses' => 'ItemStageController@unStatus'
        ]);


        /**
         * 设计公司相关操作
         */
        //获取系统推荐的设计项目
        $api->get('/design/itemList', ['as' => 'design.itemList', 'uses' => 'DesignController@itemList']);
        //拒绝设计项目
        $api->get('/design/refuseItem/{item_id}', ['as' => 'design.refuseItem', 'uses' => 'DesignController@refuseItem']);
        //设计公司获取项目信息
        $api->get('/design/item/{item_id}', 'DesignController@item');
        //已确定合作项目列表
        $api->get('/design/cooperationLists', 'DesignController@cooperationLists');
        //项目开始
        $api->post('/design/itemStart/{item_id}', 'DesignController@itemStart');
        // 确认项目已完成
        $api->post('/design/itemDone/{item_id}', 'DesignController@itemDone');
        //设计公司成员列表
        $api->get('/design/members', 'DesignController@members');
        //设计公司设置成管理员
        $api->get('/design/is_admin', 'DesignController@is_admin');

        /**
         * 通知消息相关路由
         */
        //获取系统新通知数量
        $api->get('/message/getMessageQuantity', 'MessageController@getMessageQuantity');
        //获取系统通知列表
        $api->get('/message/getMessageList', 'MessageController@getMessageList');
        //新消息数量确认阅读
        $api->put('/message/trueRead', 'MessageController@trueRead');

        /**
         * 资金流水记录列表
         */
        $api->resource('/fundLogList', 'FundLogController');

        /**
         * 银行
         */
        // 设置默认银行卡
        $api->put('/bank/setDefaultBank', 'BankController@setDefaultBank');
        // 获取默认的银行卡信息
        $api->get('/bank/getDefaultBank', 'BankController@getDefaultBank');
        //更新银行状态
        $api->put('/bank/un/status', [
            'as' => 'bank.unStatus', 'uses' => 'BankController@unStatus'
        ]);
        $api->resource('/bank', 'BankController');


        /**
         * 概况
         */
        // 设计公司信息概况
        $api->get('/survey/designCompanySurvey', 'SurveyController@designCompanySurvey');
        // 需求公司信息概况
        $api->get('/survey/demandCompanySurvey', 'SurveyController@demandCompanySurvey');

        // 公司作品详情
        $api->get('/works/{id}', 'WorksController@show');
        // 公司大赛作品
        $api->resource('/works', 'WorksController');


        /**
         * 系统通知
         */
        // 详情
        $api->get('/notice', 'NoticeController@show');
        // 列表
        $api->get('/notice/list', 'NoticeController@lists');


        /**
         * 客户接口
         */
        $api->resource('/customers', 'CustomerController');
        //检测客户是否存在
        $api->get('/customers/detection', ['as' => 'customers.detection', 'uses' => 'CustomerController@detection']);


        /**
         * 用户id生成string
         */
        //根据用户id为key查看随机字符串
        $api->get('/urlKey', 'UrlKeyValueController@urlKey');


        /**
         * 云盘
         */
        // 获取云盘上传文件token
        $api->get('/upload/yunpanUpToken', 'YunpianUploadController@upToken');

        // {post} /yunpan/createDir  创建文件夹
        $api->post('/yunpan/createDir', 'YunpianUploadController@createDir');

        /**
         * 用户群组
         */
        // {get} /group/lists  获取公司所有自己创建的群组列表（设计公司管理员）
        $api->get('/group/lists', 'GroupController@lists');
        // {post} /group/create  创建群组（设计公司管理员）
        $api->post('/group/create', 'GroupController@create');
        // {put} /group/addUser  向群组添加用户（设计公司管理员）
        $api->put('/group/addUser', 'GroupController@addUser');
        // {put} /group/removeUser  群组移除用户（设计公司管理员）
        $api->put('/group/removeUser', 'GroupController@removeUser');
        // {delete} /group/delete  删除群组（设计公司管理员）
        $api->delete('/group/delete', 'GroupController@delete');
        // {get} /group/userGroupLists  获取某用户所在的群组列表
        $api->get('/group/userGroupLists', 'GroupController@userGroupLists');
        // {get} /group/groupUserLists  获取一个群组的成员信息
        $api->get('/group/groupUserLists', 'GroupController@groupUserLists');


        /**
         * 项目级别配置
         */
        $api->resource('/itemLevels', 'ItemLevelController');


    });
});
