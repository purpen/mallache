<?php

use Illuminate\Http\Request;

/**
 *
 */
$api = app('Dingo\Api\Routing\Router');

//管理员权限
$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\Admin'], function ($api) {
    $api->group(['middleware' => ['jwt.auth', 'role']], function ($api) {

        //项目列表
        $api->get('/admin/item/lists', 'ItemActionController@itemList');
        //为项目添加推荐公司
        $api->post('/admin/item/addDesignToItem', 'ItemActionController@addDesignToItem');
        //确认项目给推荐的设计公司
        $api->post('/admin/item/trueItem', 'ItemActionController@trueItem');
        // 项目详情
        $api->get('/admin/item/show', 'ItemActionController@show');
        // 后台强制关闭当前项目并分配项目款
        $api->post('/admin/item/closeItem', 'ItemActionController@closeItem');


        //更新设计公司审核状态
        $api->put('/admin/designCompany/verifyStatus', [
            'as' => 'AdminDesignCompany.verifyStatus', 'uses' => 'AdminDesignCompanyController@verifyStatus'
        ]);
        $api->put('/admin/designCompany/unVerifyStatus', [
            'as' => 'AdminDesignCompany.unVerifyStatus', 'uses' => 'AdminDesignCompanyController@unVerifyStatus'
        ]);
        $api->put('/admin/designCompany/noVerifyStatus', [
            'as' => 'AdminDesignCompany.noVerifyStatus', 'uses' => 'AdminDesignCompanyController@noVerifyStatus'
        ]);
        //更新设计公司状态
        $api->put('/admin/designCompany/okStatus', [
            'as' => 'AdminDesignCompany.okStatus', 'uses' => 'AdminDesignCompanyController@okStatus'
        ]);
        $api->put('/admin/designCompany/unStatus', [
            'as' => 'AdminDesignCompany.unStatus', 'uses' => 'AdminDesignCompanyController@unStatus'
        ]);
        $api->get('/admin/designCompany/lists', [
            'as' => 'AdminDesignCompany.lists', 'uses' => 'AdminDesignCompanyController@lists'
        ]);
        // 设计公司详细信息
        $api->get('/admin/designCompany/show', [
            'as' => 'AdminDesignCompany.show', 'uses' => 'AdminDesignCompanyController@show'
        ]);
        // 公开或关闭设计公司资料
        $api->put('/admin/designCompany/openInfo', [
            'as' => 'AdminDesignCompany.openInfo', 'uses' => 'AdminDesignCompanyController@openInfo'
        ]);
        // api {put} /admin/designCompany/isTest 设置设计公司是否是测试数据
        $api->put('/admin/designCompany/isTest', 'AdminDesignCompanyController@setIsTestData');

        // 设计公司案例列表
        $api->get('/admin/designCase/lists', [
            'as' => 'DesignCase.lists', 'uses' => 'DesignCaseController@lists'
        ]);
        // 开放设计案例
        $api->put('/admin/designCase/openInfo', [
            'as' => 'DesignCase.openInfo', 'uses' => 'DesignCaseController@openInfo'
        ]);


        //更新需求公司审核状态
        $api->put('/admin/demandCompany/verifyStatus', [
            'as' => 'AdminDemandCompany.verifyStatus', 'uses' => 'AdminDemandCompanyController@verifyStatus'
        ]);
        $api->put('/admin/demandCompany/unVerifyStatus', [
            'as' => 'AdminDemandCompany.unVerifyStatus', 'uses' => 'AdminDemandCompanyController@unVerifyStatus'
        ]);
        $api->put('/admin/demandCompany/noVerifyStatus', [
            'as' => 'AdminDemandCompany.noVerifyStatus', 'uses' => 'AdminDemandCompanyController@noVerifyStatus'
        ]);
        $api->get('/admin/demandCompany/lists', [
            'as' => 'AdminDemandCompany.lists', 'uses' => 'AdminDemandCompanyController@lists'
        ]);
        // 需求公司详情
        $api->get('/admin/demandCompany/show', [
            'as' => 'AdminDemandCompany.show', 'uses' => 'AdminDemandCompanyController@show'
        ]);

        /**
         * 用户相关路由
         */
        //用户列表
        $api->get('/admin/user/lists', 'UserActionController@lists');
        //用户详情
        $api->get('/admin/user/show', 'UserActionController@show');
        //修改用户基本信息
        $api->post('/admin/user/edit', 'UserActionController@edit');
        //修改用户角色
        $api->post('/admin/user/changeRole', 'UserActionController@changeRole');
        //修改用户状态
        $api->post('/admin/user/changeStatus', 'UserActionController@changeStatus');
        //修改来源管理员
        $api->post('/admin/user/changeSourceAdmin', 'UserActionController@changeSourceAdmin');
        /**
         * 支付单相关路由
         */
        //支付单列表
        $api->get('/admin/payOrder/lists', 'PayOrderActionController@lists');
        //后台确认项目支付单付款
        $api->post('/admin/payOrder/truePay', 'PayOrderActionController@truePay');

        /**
         * 提现相关
         */
        // 提现项目列表
        $api->get('/admin/withdrawOrder/lists', 'WithdrawOrderActionController@lists');
        // 确认提现单已提现
        $api->post('/admin/withdrawOrder/trueWithdraw', 'WithdrawOrderActionController@trueWithdraw');

        /**
         * 控制中心
         */
        // 后台控制台信息
        $api->get('/admin/survey/index', 'SurveyController@index');

        /**
         * 栏目位相关路由
         */
        // 保存栏目位文章
        $api->post('/admin/column', 'ColumnController@store');
        // 更新栏目位文章
        $api->put('/admin/column', 'ColumnController@update');
        // 文章详情
        $api->get('/admin/column', 'ColumnController@show');
        // 栏目文章列表
        $api->get('/admin/column/lists', 'ColumnController@lists');
        // 栏目文章变更状态
        $api->put('/admin/column/changeStatus', 'ColumnController@changeStatus');
        // 栏目文章删除
        $api->delete('/admin/column', 'ColumnController@delete');


        /**
         * 文章分类
         */
        // 添加分类
        $api->post('/admin/classification', 'ClassificationController@store');
        // 分类信息详情
        $api->get('/admin/classification', 'ClassificationController@edit');
        // 修改分类
        $api->put('/admin/classification', 'ClassificationController@update');
        // 分类列表
        $api->get('/admin/classification/list', 'ClassificationController@index');
        // {put} /admin/classification/changeStatus 变更分类状态
        $api->put('/admin/classification/changeStatus', 'ClassificationController@changeStatus');

        /**
         * 文章
         */
        // 文章列表
        $api->get('/admin/article/list', 'ArticleController@index');
        // 添加文章
        $api->post('/admin/article', 'ArticleController@store');
        // 文章详情
        $api->get('/admin/article', 'ArticleController@edit');
        // {put} /admin/article 修改文章
        $api->put('/admin/article', 'ArticleController@update');
        // {put} /admin/article/verifyStatus 文章审核
        $api->put('/admin/article/verifyStatus', 'ArticleController@verifyStatus');
        // {put} /admin/article/recommend 文章推荐
        $api->put('/admin/article/recommend', 'ArticleController@recommend');
        // 删除文章
        $api->delete('/admin/article/delete', 'ArticleController@destroy');

        /**
         * 日期奖项
         */
        // 列表
        $api->get('/admin/dateOfAward/list', 'DateOfAwardController@index');
        // 添加
        $api->post('/admin/dateOfAward', 'DateOfAwardController@store');
        // 日期奖项详情
        $api->get('/admin/dateOfAward', 'DateOfAwardController@show');
        // 更改日期奖项
        $api->put('/admin/dateOfAward', 'DateOfAwardController@update');
        // 日期奖项周
        $api->get('/admin/dateOfAward/week', 'DateOfAwardController@week');
        // 日期奖项月
        $api->get('/admin/dateOfAward/month', 'DateOfAwardController@month');
        // 日期奖项删除
        $api->delete('/admin/dateOfAward', 'DateOfAwardController@delete');
        // {put} /admin/dateOfAward/verifyStatus 状态变更
        $api->put('/admin/dateOfAward/changeStatus', 'DateOfAwardController@changeStatus');


        /**
         * 大赛作品
         */
        // 列表
        $api->get('/admin/works/list', 'WorksController@index');
        // 添加
        $api->post('/admin/works', 'WorksController@store');
        // 详情
        $api->get('/admin/works', 'WorksController@show');
        // {put} /admin/works 更新
        $api->put('/admin/works', 'WorksController@update');
        // {put} /admin/works/verifyStatus 审核
        $api->put('/admin/works/verifyStatus', 'WorksController@verifyStatus');
        // {put} /admin/works/published 发布
        $api->put('/admin/works/published', 'WorksController@published');
        // 删除文章
        $api->delete('/admin/works', 'WorksController@destroy');


        /**
         * 趋势报告
         */
        // 保存趋势报告
        $api->post('/admin/trendReports', 'TrendReportsController@store');
        // 更新趋势报告
        $api->put('/admin/trendReports', 'TrendReportsController@update');
        // 趋势报告详情
        $api->get('/admin/trendReports', 'TrendReportsController@show');
        // 趋势报告列表
        $api->get('/admin/trendReports/lists', 'TrendReportsController@lists');
        // 栏目文章删除
        $api->delete('/admin/trendReports', 'TrendReportsController@delete');
        //启用禁用
        $api->put('/admin/trendReports/verifyStatus', 'TrendReportsController@verifyStatus');

        /**
         * 常用网站
         */
        // 保存常用网站
        $api->post('/admin/commonlyUsedUrls', 'CommonlyUsedUrlController@store');
        // 更新常用网站
        $api->put('/admin/commonlyUsedUrls', 'CommonlyUsedUrlController@update');
        // 常用网站详情
        $api->get('/admin/commonlyUsedUrls', 'CommonlyUsedUrlController@show');
        // 常用网站列表
        $api->get('/admin/commonlyUsedUrls/list', 'CommonlyUsedUrlController@lists');
        // 常用网站删除
        $api->delete('/admin/commonlyUsedUrls', 'CommonlyUsedUrlController@delete');
        //启用禁用
        $api->put('/admin/commonlyUsedUrls/verifyStatus', 'CommonlyUsedUrlController@verifyStatus');

        /**
         * 奖项案例
         */
        // 保存
        $api->post('/admin/awardCase', 'AwardCaseController@store');
        // 更新
        $api->put('/admin/awardCase', 'AwardCaseController@update');
        // 详情
        $api->get('/admin/awardCase', 'AwardCaseController@show');
        // 删除
        $api->delete('/admin/awardCase', 'AwardCaseController@delete');
        // 列表
        $api->get('/admin/awardCase/list', 'AwardCaseController@lists');
        //启用禁用
        $api->put('/admin/awardCase/changeStatus', 'AwardCaseController@changeStatus');
        //推荐/取消推荐
        $api->put('/admin/awardCase/changeRecommended', 'AwardCaseController@changeRecommended');

        /**
         * 系统通知
         */
        // 保存
        $api->post('/admin/notice', 'NoticeController@store');
        // 更新
        $api->put('/admin/notice', 'NoticeController@update');
        // 详情
        $api->get('/admin/notice', 'NoticeController@show');
        // 删除
        $api->delete('/admin/notice', 'NoticeController@delete');
        // 列表
        $api->get('/admin/notice/list', 'NoticeController@lists');
        //启用禁用
        $api->put('/admin/notice/changeStatus', 'NoticeController@changeStatus');

        /**
         * url上传七牛
         */
        //直接上传七牛
        $api->get('/admin/urlUpload', 'UploadAssetController@urlUpload');
        //附件列表
        $api->get('/admin/assets', 'UploadAssetController@lists');


        /**
         * block
         */
        // 保存
        $api->post('/admin/block', 'BlockController@store');
        // 更新
        $api->put('/admin/block', 'BlockController@update');
        // 详情
        $api->get('/admin/block', 'BlockController@show');
        // 删除
        $api->delete('/admin/block', 'BlockController@delete');
        // 列表
        $api->get('/admin/block/list', 'BlockController@lists');
        //启用禁用
        $api->put('/admin/block/changeStatus', 'BlockController@changeStatus');

        //api {get} /itemCommission/lists 收取项目佣金列表
        $api->get('/admin/itemCommission/lists', 'ItemCommissionController@lists');

        /**
         * 佣金优惠
         */
        //添加设计公司佣金免费次数
        $api->post('/admin/commissionCount/add', 'CommissionCountController@addCount');
        // 设计公司优惠次数列表
        $api->get('/admin/commissionCount/lists', 'CommissionCountController@lists');

        // 需要设计公司给平台开具发票列表
        $api->get('/admin/invoice/pullLists', 'InvoiceController@pullLists');
        // 需要平台给需求公司开具发票列表
        $api->get('/admin/invoice/pushLists', 'InvoiceController@pushLists');
        // 确认收到设计公司发票
        $api->put('/admin/invoice/trueInvoice', 'InvoiceController@trueInvoice');
        // 确认给需求公司的发票已开
        $api->put('/admin/invoice/trueDemandInvoice', 'InvoiceController@trueDemandInvoice');

        /**
         * 权重
         */
        //保存权重
        $api->post('/admin/weight/save', 'WeightController@save');
        //权重详情
        $api->get('/admin/weight/show', 'WeightController@show');

        /**
         * 设计公司数据统计
         */
        //人工干预分值修改
        $api->post('/admin/saveIntervene', 'DesignStatisticsController@saveIntervene');
        //设计公司信息列表
        $api->get('/admin/statistics/list', 'DesignStatisticsController@statisticsList');
        //测试设计公司匹配
        $api->get('/admin/test/matching', 'DesignStatisticsController@testMatching');
        /**
         * 评价
         */
        //平台评价项目
        $api->post('/admin/platform/evaluate', 'AdminEvaluateController@platformEvaluate');
    });

});
