<?php

return [

    /**
     * 超级管理员 20
     */
    'root' => [
        '/admin/item/lists',         // 项目列表
        '/admin/item/show',           // 项目详情
        '/admin/item/addDesignToItem',   // 为项目添加推荐公司
        '/admin/item/trueItem',          // //确认项目给推荐的设计公司

        //更新设计公司审核状态
        '/admin/designCompany/verifyStatus',
        '/admin/designCompany/unVerifyStatus',
        '/admin/designCompany/noVerifyStatus',

        //更新设计公司状态
        '/admin/designCompany/okStatus',
        '/admin/designCompany/unStatus',
        '/admin/designCompany/lists',
        // 设计公司详情
        '/admin/designCompany/show',
        // 公开或关闭设计公司资料
        '/admin/designCompany/openInfo',


        //更新需求公司审核状态
        '/admin/demandCompany/verifyStatus',
        '/admin/demandCompany/unVerifyStatus',
        '/admin/demandCompany/noVerifyStatus',
        '/admin/demandCompany/lists',
        // 需求公司详情
        '/admin/demandCompany/show',

        /**
         * 用户相关路由
         */
        //用户列表
        '/admin/user/lists',
        //用户详情
        '/admin/user/show',
        //用户编辑
        '/admin/user/edit',
        //修改用户角色
        '/admin/user/changeRole',
        //修改用户状态
        '/admin/user/changeStatus',

        /**
         * 支付单相关路由
         */
        //支付单列表
       '/admin/payOrder/lists',
        //后台确认项目支付单付款
        '/admin/payOrder/truePay',

        /**
         * 提现相关
         */
        // 提现项目列表
        '/admin/withdrawOrder/lists',
        // 确认提现单已提现
        '/admin/withdrawOrder/trueWithdraw',

        // 设计公司案例列表
        '/admin/designCase/lists',
        // 开放设计案例
        '/admin/designCase/openInfo',

        // 后台控制台信息
        '/admin/survey/index',

        // 栏目位
        '/admin/column',
        // 栏目文章列表
        '/admin/column/lists',
        // 栏目文章变更状态
        '/admin/column/changeStatus',

        // 文章分类
        '/admin/classification',
        // 分类列表
        '/admin/classification/list',
        // 文章分类状态变更
        '/admin/classification/changeStatus',

        // 文章列表
        '/admin/article/list',
        // 文章
        '/admin/article',
        // 文章审核
        '/admin/article/verifyStatus',
        // 文章推荐
        '/admin/article/recommend',
        // 文章删除
        '/admin/article/delete',

        //日期奖项
        '/admin/dateOfAward/store',
        //日期奖项详情
        '/admin/dateOfAward',
        //日期奖项更改
        '/admin/dateOfAward/update',
        //日期奖项周
        '/admin/dateOfAward/week',
        //日期奖项月
        '/admin/dateOfAward/month',
        //日期奖项删除
        '/admin/dateOfAward/delete',
    ],

    /**
     * 管理员加强版--可进行操作 15
     */
    'admin_plus' => [
        //用户列表
        '/admin/user/lists',
        //用户详情
        '/admin/user/show',
        //用户编辑
        '/admin/user/edit',
        //修改用户状态
        '/admin/user/changeStatus',

        '/admin/item/lists',         // 项目列表
        '/admin/item/show',           // 项目详情
        '/admin/item/addDesignToItem',   // 为项目添加推荐公司
        '/admin/item/trueItem',          // //确认项目给推荐的设计公司

        //更新设计公司审核状态
        '/admin/designCompany/verifyStatus',
        '/admin/designCompany/unVerifyStatus',
        '/admin/designCompany/noVerifyStatus',

        //更新设计公司状态
        '/admin/designCompany/okStatus',
        '/admin/designCompany/unStatus',
        '/admin/designCompany/lists',
        // 设计公司详情
        '/admin/designCompany/show',
        // 公开或关闭设计公司资料
        '/admin/designCompany/openInfo',

        //更新需求公司审核状态
        '/admin/demandCompany/verifyStatus',
        '/admin/demandCompany/unVerifyStatus',
        '/admin/demandCompany/noVerifyStatus',
        '/admin/demandCompany/lists',
        // 需求公司详情
        '/admin/demandCompany/show',

        /**
         * 支付单相关路由
         */
        //支付单列表
        '/admin/payOrder/lists',
        //后台确认项目支付单付款
        '/admin/payOrder/truePay',

        /**
         * 提现相关
         */
        // 提现项目列表
        '/admin/withdrawOrder/lists',
        // 确认提现单已提现
        '/admin/withdrawOrder/trueWithdraw',

        // 设计公司案例列表
        '/admin/designCase/lists',
        // 开放设计案例
        '/admin/designCase/openInfo',

        // 后台控制台信息
        '/admin/survey/index',

        // 栏目位
        '/admin/column',
        // 栏目文章列表
        '/admin/column/lists',
        // 栏目文章变更状态
        '/admin/column/changeStatus',

        // 文章分类
        '/admin/classification',
        // 分类列表
        '/admin/classification/list',
        // 文章分类状态变更
        '/admin/classification/changeStatus',

        // 文章列表
        '/admin/article/list',
        // 文章
        '/admin/article',
        // 文章审核
        '/admin/article/verifyStatus',
        // 文章推荐
        '/admin/article/recommend',
        // 文章删除
        '/admin/article/delete',

        //日期奖项
        '/admin/dateOfAward/store',
        //日期奖项详情
        '/admin/dateOfAward',
        //日期奖项更改
        '/admin/dateOfAward/update',
        //日期奖项周
        '/admin/dateOfAward/week',
        //日期奖项月
        '/admin/dateOfAward/month',
        //日期奖项删除
        '/admin/dateOfAward/delete',
    ],

    /**
     * 管理员--可查看 10
     */
    'admin' => [
        '/admin/item/lists',         // 项目列表
        '/admin/item/show',           // 项目详情

        '/admin/designCompany/lists',    // 设计公司列表
        // 设计公司详情
        '/admin/designCompany/show',

        '/admin/demandCompany/lists',    // 需求公司列表
        // 需求公司详情
        '/admin/demandCompany/show',
        //支付单列表
        '/admin/payOrder/lists',
        // 提现项目列表
        '/admin/withdrawOrder/lists',
        // 设计公司案例列表
        '/admin/designCase/lists',

        // 后台控制台信息
        '/admin/survey/index',

        // 栏目位
        '/admin/column',
        // 栏目文章列表
        '/admin/column/lists',

        // 文章分类
        '/admin/classification',
        // 分类列表
        '/admin/classification/list',
        // 文章分类状态变更
        '/admin/classification/changeStatus',

        // 文章列表
        '/admin/article/list',
        // 文章
        '/admin/article',
        // 文章审核
        '/admin/article/verifyStatus',
        // 文章推荐
        '/admin/article/recommend',
        // 文章删除
        '/admin/article/delete',
    ],
];
