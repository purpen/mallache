<?php

return [

    /**
     * 超级管理员 20
     */
    'root' => [
        '/admin/item/lists',         // 项目列表
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

        //更新需求公司审核状态
        '/admin/demandCompany/verifyStatus',
        '/admin/demandCompany/unVerifyStatus',
        '/admin/demandCompany/noVerifyStatus',
        '/admin/demandCompany/lists',

        /**
         * 用户相关路由
         */
        //用户列表
        '/admin/user/lists',
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
    ],

    /**
     * 管理员加强版--可进行操作 15
     */
    'admin_plus' => [
        '/admin/item/lists',         // 项目列表
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

        //更新需求公司审核状态
        '/admin/demandCompany/verifyStatus',
        '/admin/demandCompany/unVerifyStatus',
        '/admin/demandCompany/noVerifyStatus',
        '/admin/demandCompany/lists',

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
    ],

    /**
     * 管理员--可查看 10
     */
    'admin' => [
        '/admin/item/lists',         // 项目列表
        '/admin/designCompany/lists',    // 设计公司列表
        '/admin/demandCompany/lists',    // 需求公司列表
        //支付单列表
        '/admin/payOrder/lists',
        // 提现项目列表
        '/admin/withdrawOrder/lists',
    ],
];