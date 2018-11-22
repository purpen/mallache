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


    // api {post} /setNewVersion 设计版本号
    $api->post('/setNewVersion', 'VersionController@setNewVersion');

    // @api {get} /getVersion 设计版本号
    $api->get('/getVersion', 'VersionController@getVersion');

    /**
     * Opalus
     */
    // 获取设计企业排行榜
    $api->get('/opalus/company_record/list', 'OpalusController@getCompanyRecord');

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
    //用户注册
    $api->post('/auth/errCount', [
        'as' => 'auth.errCount', 'uses' => 'AuthenticateController@errCount'
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
    // 设计案例指定产品领域推荐列表
    $api->get('/designCase/stickFieldList', 'DesignCaseController@stickFieldList');
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

    //云盘分享查看 {get} /yunpan/shareShow 查看分享
    $api->get('/yunpan/shareShow', 'PanShareController@show');

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
        $api->post('/auth/updateUser', [
            'as' => 'auth.updateUser', 'uses' => 'AuthenticateController@updateUser'
        ]);

        // api {put} /auth/setUserType 用户设置账户类型
        $api->post('/auth/setUserType', [
            'as' => 'auth.setUserType', 'uses' => 'AuthenticateController@setUserType'
        ]);

        //用户钱包信息
        $api->get('/auth/fundInfo', ['as' => 'auth.fundInfo', 'uses' => 'AuthenticateController@fundInfo']);
        //根据用户id获取用户信息
        $api->get('/auth/userId', ['as' => 'auth.userId', 'uses' => 'AuthenticateController@userId']);
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
//        $api->get('/pay/endPayOrder/{item_id}', 'PayController@endPayOrder');

        // api {get} /pay/firstPayOrder/{item_id} 创建首付款支付订单
        $api->get('/pay/firstPayOrder/{item_id}', 'PayController@firstPayOrder');

        // {get} /pay/stagePayOrder/{item_stage_id} 创建项目阶段支付单
        $api->get('/pay/stagePayOrder/{item_stage_id}', 'PayController@createItemStagePayOrder');

        //查询支付状态
        $api->get('/pay/getPayStatus/{uid}', 'PayController@getPayStatus');

        // api {put} /pay/bankTransfer/{pay_order_id} 银行转账凭证上传确认
        $api->put('/pay/bankTransfer/{pay_order_id}', 'PayController@bankTransfer');

        //用户提现
        $api->post('/withdraw/create', 'WithdrawOrderController@create');
        $api->get('/withdraw/lists', 'WithdrawOrderController@lists');

        // @api {post} /demand/create 需求公司创建需求项目
        $api->post('/demand/create', 'DemandController@create');

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
        //用户评价项目
        $api->post('/demand/users/evaluate', 'DemandController@userEvaluate');
        // 发布需求方确认项目阶段
        $api->post('/itemStage/demandFirmItemStage', 'ItemStageController@demandFirmItemStage');
        // {delete} /demand/{id} 删除项目
        $api->delete('/demand/trueItemDone/{id}', 'DemandController@destroy');

        // 获取当前信息匹配到的公司数量
        $api->post('/demand/matchingCount/', 'DemandController@matchingCount');
        //项目类型、领域
        $api->resource('/demand', 'DemandController');
        //更改项目名称
        $api->put('/updateName/demand', 'DemandController@updateName');

        //UX UI 设计详情
        $api->resource('/UDesign', 'UDesignInfoController');
        //产品设计详情
        $api->resource('/ProductDesign', 'ProductDesignInfoController');
        //平面设计详情
        $api->resource('/GraphicDesign', 'GraphicDesignInfoController');
        //H5设计详情
        $api->resource('/H5Design', 'H5DesignInfoController');
        //包装设计详情
        $api->resource('/PackDesign', 'PackDesignInfoController');
        //插画设计详情
        $api->resource('/IllustrationDesign', 'IllustrationDesignInfoController');
        //合同
        $api->resource('/contract', 'ContractController');
        $api->post('/contract/ok', 'ContractController@okContract');

        //设计公司信息
//        $api->resource('/designCompany', 'DesignCompanyController');
        $api->get('/designCompany', ['as' => 'designCompany.show', 'uses' => 'DesignCompanyController@show']);
        $api->put('/designCompany', ['as' => 'designCompany.update', 'uses' => 'DesignCompanyController@update']);
        $api->post('/designCompany', ['as' => 'designCompany.store', 'uses' => 'DesignCompanyController@store']);
        //子公司展示
        $api->get('/designCompany/child', ['as' => 'designCompany.childShow', 'uses' => 'DesignCompanyController@childShow']);


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
        $api->put('/design/isAdmin', 'DesignController@isAdmin');
        //移除成员
        $api->put('/design/deleteMember', 'DesignController@deleteMember');
        //设计公司成员列表
        $api->get('/design/members/search', 'DesignController@membersSearch');
        //恢复成员
        $api->put('/design/restoreMember', 'DesignController@restoreMember');

        /**
         * 通知消息相关路由
         */
        //获取系统新通知数量
        $api->get('/message/getMessageQuantity', 'MessageController@getMessageQuantity');
        //获取系统通知列表
        $api->get('/message/getMessageList', 'MessageController@getMessageList');
        //新消息数量确认阅读
        $api->put('/message/trueRead', 'MessageController@trueRead');
        //获取项目通知数量
        $api->get('/message/getMessageProjectNotice', 'MessageController@getMessageProjectNotice');

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
        // {get} /yunpan/lists  网盘列表
        $api->get('/yunpan/lists', 'YunpianUploadController@lists');
        // {put} /yunpan/setPermission  设置权限
        $api->put('/yunpan/setPermission', 'YunpianUploadController@setPermission');
        // {put} /yunpan/delete  放入回收站
        $api->put('/yunpan/delete', 'YunpianUploadController@delete');
        // {put} /yunpan/copy  文件复制
        $api->put('/yunpan/copy', 'YunpianUploadController@copy');
        // api {put} /yunpan/move  文件移动
        $api->put('/yunpan/move', 'YunpianUploadController@move');
        // {put} /yunpan/editName  修改文件名称
        $api->put('/yunpan/editName', 'YunpianUploadController@editName');
        // {get} /yunpan/search  全局搜索
        $api->get('/yunpan/search', 'YunpianUploadController@search');
        // {post} /yunpan/recentUseLog  最近使用文件打点
        $api->post('/yunpan/recentUseLog', 'YunpianUploadController@recentUseLog');
        // {get} /yunpan/recentUseFile  获取最近使用文件列表
        $api->get('/yunpan/recentUseFile', 'YunpianUploadController@recentUseFile');
        // {get} /yunpan/typeLists  资源分类展示
        $api->get('/yunpan/typeLists', 'YunpianUploadController@typeLists');

        //  {get} /yunpan/shareCreate  创建文件分享
        $api->get('/yunpan/shareCreate', 'PanShareController@create');


        // {get} /recycleBin/lists 回收站列表
        $api->get('/recycleBin/lists', 'RecycleBinController@lists');
        //  {delete} /recycleBin/delete 彻底删除文件（文件夹）
        $api->delete('/recycleBin/delete', 'RecycleBinController@delete');
        // {put} /recycleBin/restore 恢复文件（文件夹）
        $api->put('/recycleBin/restore', 'RecycleBinController@restore');

        // {get} /yunpan/myFiles
        $api->get('/yunpan/myFiles', 'YunpianUploadController@myFiles');

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
        // {put} /group/updateName  修改群组名称（设计公司管理员）
        $api->put('/group/updateName', 'GroupController@updateName');

        /**
         * 项目级别配置
         */
        $api->resource('/itemLevels', 'ItemLevelController');


        /**
         * 任务
         */
        $api->resource('/tasks', 'TaskController');
        //主任务完成与未完成
        $api->put('/isStage/tasks', 'TaskController@stage');
        //子任务删除
        $api->delete('/tasks/childDelete/{id}', ['as' => 'tasks.childDelete', 'TaskController@childDelete']);
        //领取任务
        $api->post('/tasks/executeUser', 'TaskController@executeUser');
        //任务统计
        $api->get('/statistical/tasks', 'TaskController@statistical');
        //个人任务统计
        $api->get('/statistical/userTasks', 'TaskController@userStatistical');
        //我的任务
        $api->get('/myTasks', 'TaskController@myTasks');
        /**
         * 项目用户
         */
        $api->resource('/itemUsers', 'ItemUserController');
        $api->delete('/itemUsers/delete', 'ItemUserController@destroy');

        /**
         * 任务成员
         */
        $api->resource('/taskUsers', 'TaskUserController');
        //保存任务成员
        $api->post('/taskUsers/newStore', 'TaskUserController@newStore');
        $api->delete('/taskUsers/delete', 'TaskUserController@destroy');

        /**
         * 标签
         */
        $api->resource('/tags', 'TagController');
        //标签id查看任务
        $api->get('/tags/task/{id} ', 'TagController@tagTask');

        /**
         * 阶段
         */
        $api->resource('/stages', 'StageController');

        /**
         * 沟通纪要表
         */
        $api->resource('/communeSummaries', 'CommuneSummaryController');

        /**
         * 沟通纪要成员表
         */
        $api->resource('/communeSummaryUser', 'CommuneSummaryUserController');
        $api->delete('/communeSummaryUser/delete', 'CommuneSummaryUserController@destroy');


        /**
         * 设计工具项目路由
         */
        // api {get} /designProject/lists 设计工具个人参与项目展示
        $api->get('/designProject/lists', 'DesignProjectController@lists');
        // @api {post} /designProject/create 设计工具创建项目
        $api->post('/designProject/create', 'DesignProjectController@create');
        // api {put} /designProject/update 设计工具项目编辑
        $api->put('/designProject/update', 'DesignProjectController@update');
        // api {get} /designProject 设计工具项目详情展示
        $api->get('/designProject', 'DesignProjectController@show');
        // api {put} /designProject/delete 设计工具项目删除
        $api->delete('/designProject/delete', 'DesignProjectController@delete');
        // api {put} /designProject/collect 设计工具项目收藏
        $api->put('/designProject/collect', 'DesignProjectController@collect');
        // api {put} /designProject/pigeonhole 设计工具项目归档
        $api->put('/designProject/pigeonhole', 'DesignProjectController@pigeonhole');

        //设计公司客户
        // api {get} /designClient/lists 客户信息列表
        $api->get('/designClient/lists', 'DesignClientController@index');
        // api {post} /designClient/create 创建客户信息
        $api->post('/designClient/create', 'DesignClientController@store');
        // api {get} /designClient/search 搜索客户信息
        $api->get('/designClient/search', 'DesignClientController@search');
        // api {put} /designClient/update 创建客户信息
        $api->put('/designClient/update', 'DesignClientController@update');
        // api {delete} /designClient/delete 创建客户信息
        $api->delete('/designClient/delete', 'DesignClientController@delete');

        // 设计报价职位
        // api {post} /designPosition/create 添加职位
        $api->post('/designPosition/create', 'DesignPositionController@create');
        // api {get} /designPosition/lists 职位列表
        $api->get('/designPosition/lists', 'DesignPositionController@lists');
        // api {get} /designPosition/search 职位搜索
        $api->get('/designPosition/search', 'DesignPositionController@search');
        // api {put} /designPosition/update 编辑职位
        $api->put('/designPosition/update', 'DesignPositionController@update');
        // api {delete} /designPosition/delete 删除职位
        $api->delete('/designPosition/delete', 'DesignPositionController@delete');


        // 设计报价单
        // api {post} /designQuotation/create 设计工具-创建报价单
        $api->post('/designQuotation/create', 'DesignQuotationController@create');
        // api {put} /designQuotation/update 设计工具-更新报价单
        $api->put('/designQuotation/update', 'DesignQuotationController@update');
        // api {get} /designQuotation 设计工具-查看详情
        $api->get('/designQuotation', 'DesignQuotationController@show');

        // 项目阶段规划
        // api {post} /designStage/create 设计工具--创建项目阶段
        $api->post('/designStage/create', 'DesignStageController@create');
        // api {put} /designStage/update 设计工具--更新项目阶段
        $api->put('/designStage/update', 'DesignStageController@update');
        // api {get} /designStage 设计工具--项目阶段详情
        $api->get('/designStage', 'DesignStageController@show');
        // api {delete} /designStage/delete 设计工具--项目阶段删除
        $api->delete('/designStage/delete', 'DesignStageController@delete');
        // api {get} /designStage/lists 设计工具--项目阶段列表
        $api->get('/designStage/lists', 'DesignStageController@lists');
        // api {put} /designStage/completes 设计工具--更新项目阶段完成
        $api->put('/designStage/completes', 'DesignStageController@completes');

        // 项目规划子阶段
        // api {post} /designSubstage/create 设计工具--创建子阶段
        $api->post('/designSubstage/create', 'DesignSubstageController@create');
        $api->put('/designSubstage/update', 'DesignSubstageController@update');
        $api->get('/designSubstage', 'DesignSubstageController@show');
        $api->delete('/designSubstage/delete', 'DesignSubstageController@delete');
        $api->put('/designSubstage/updateDuration', 'DesignSubstageController@updateDuration');
        $api->put('/designSubstage/completes', 'DesignSubstageController@completes');


        // 阶段节点
        // api {post} /designStageNode/create 设计工具--创建阶段节点
        $api->post('/designStageNode/create', 'DesignStageNodeController@create');
        $api->put('/designStageNode/update', 'DesignStageNodeController@update');
        $api->get('/designStageNode', 'DesignStageNodeController@show');
        $api->delete('/designStageNode/delete', 'DesignStageNodeController@delete');
        $api->put('/designStageNode/completes', 'DesignStageNodeController@completes');

        //api {post} /nodes/create 设计工具--节点列表操作
        $api->post('/nodes/create', 'NodesController@create');
        $api->put('/nodes/update', 'NodesController@update');
        $api->delete('/nodes/delete', 'NodesController@delete');
        $api->get('/nodes/lists', 'NodesController@lists');

        // api {get} /designProject/payAssets 设计工具--交付内容
        $api->get('/designProject/payAssets', 'DesignProjectController@payAssets');
        //项目中合同列表
        $api->get('/designProject/contracts', 'DesignProjectController@contracts');
        //项目完成进度
        $api->get('/designProject/userStatistical', 'DesignProjectController@userStatistical');
        //项目动态
        $api->get('/designProject/dynamic', 'DesignProjectController@dynamic');
        //项目统计
        $api->get('/designProject/statistical', 'DesignProjectController@statistical');
        /**
         * 设计工具--消息管理
         */
        // api {get} /designNotice/lists 设计管理工具--消息通知列表
        $api->get('/designNotice/lists', 'DesignNoticeController@lists');
        // api {put} /designNotice/trueRead 设计管理工具--消息确认阅读
        $api->put('/designNotice/trueRead', 'DesignNoticeController@trueRead');
        // api {delete} /designNotice/delete 设计管理工具--消息删除
        $api->delete('/designNotice/delete', 'DesignNoticeController@delete');
        //阅读所有
        $api->put('/designNotice/allTrueRead', 'DesignNoticeController@allTrueRead');


        //设计公司确认发票已开出
        $api->put('/invoice/designTrueSend', 'InvoiceController@designTrueSend');
        // 需求公司确认收到发票
        $api->put('/invoice/demandTrueGet', 'InvoiceController@demandTrueGet');

        // 物流公司列表
        $api->get('/logisticsLists', 'ToolsController@logisticsLists');

        // 项目规划里程碑
        $api->post('/milestone/create', 'MilestoneController@create');
        $api->put('/milestone/update', 'MilestoneController@update');
        $api->get('/milestone', 'MilestoneController@show');
        $api->delete('/milestone/delete', 'MilestoneController@delete');
        $api->put('/milestone/completes', 'MilestoneController@completes');

        //线上项目设定项目营业额，项目完成数量
        $api->post('/designTarget/create', 'DesignTargetController@create');
        $api->get('/designTarget/show', 'DesignTargetController@show');
        //收入报表
        $api->get('/designTarget/incomeMonth', 'DesignTargetController@incomeMonth');
        //季度报表
        $api->get('/designTarget/incomeQuarter', 'DesignTargetController@incomeQuarter');
        //年报表
        $api->get('/designTarget/incomeYear', 'DesignTargetController@incomeYear');
        //项目收入排名
        $api->get('/designTarget/incomeRanked', 'DesignTargetController@incomeRanked');
        //项目类别
        $api->get('/designTarget/incomeType', 'DesignTargetController@incomeType');
        //项目详细类别
        $api->get('/designTarget/incomeDesignTypes', 'DesignTargetController@incomeDesignTypes');
        //项目行业
        $api->get('/designTarget/incomeIndustry', 'DesignTargetController@incomeIndustry');
        //收入金额阶段
        $api->get('/designTarget/incomeStage', 'DesignTargetController@incomeStage');
        //成员占比
        $api->get('/design/userPercentage', 'DesignTargetController@userPercentage');
        //职位占比
        $api->get('/design/positionPercentage', 'DesignTargetController@positionPercentage');
        //城市统计
        $api->get('/designTarget/incomeCity', 'DesignTargetController@incomeCity');
        //所有项目的任务统计
        $api->get('/design/itemTasks', 'DesignTargetController@itemTasks');

        /**
         * 交易会
         */
        //保存设计成果
        $api->post('/designResults/save', 'DesignResultController@saveDesignResults');
        //设计成果详情
        $api->get('/designResults/show', 'DesignResultController@designResultsShow');
        //设计成果列表
        $api->get('/designResults/list', 'DesignResultController@lists');
        //设计成果列表
        $api->get('/designResults/saveStatus', 'DesignResultController@saveStatus');
        //设计成果删除
        $api->post('/designResults/delete', 'DesignResultController@deleteDesignResult');
        //设计成果关注
        $api->get('/designResults/collectionOperation', 'DesignResultController@collectionOperation');
        //设计成果我的收藏列表
        $api->get('/designResults/myCollectionList', 'DesignResultController@myCollectionList');
        //已上架设计成果列表
        $api->get('/designResults/alLists', 'DesignResultController@alLists');
        //创建设计成果支付订单
        $api->get('/pay/designResults/{design_result_id}','PayController@payDesignResults');
        //设计成果价格修改
        $api->post('/designResults/savePrice','DesignResultController@saveDesignResultPrice');
        //设计成果订单列表
        $api->get('/pay/myOrderList','PayController@myOrderList');
        //设计成果确认文件
        $api->get('/pay/confirmFile','PayController@confirmFile');
        //关闭未支付订单
        $api->get('/pay/closeOrder','PayController@closeOrder');
        //删除设计成果已关闭订单
        $api->get('/pay/deleteOrder','PayController@deleteOrder');
        //设计成果订单详情
        $api->get('/pay/orderShow','PayController@orderShow');
        //修改交易会权限
        $api->get('/demandCompany/saveTradeFair','DemandCompanyController@saveTradeFair');
    });

});
