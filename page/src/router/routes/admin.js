/**
 ** ###### 后台管理 ##########
 */
module.exports = [
  // 控制台
  {
    path: '/admin',
    name: 'adminDashBoard',
    meta: {
      title: '控制台',
      requireAuth: true
    },
    component: require('@/components/admin/DashBoard')
  },
  // 添加栏目
  {
    path: '/admin/column/add',
    name: 'adminColumnAdd',
    meta: {
      title: '添加栏目',
      requireAuth: true
    },
    component: require('@/components/admin/column/Submit')
  },
  // 编辑栏目
  {
    path: '/admin/column/edit/:id',
    name: 'adminColumnEdit',
    meta: {
      title: '编辑栏目',
      requireAuth: true
    },
    component: require('@/components/admin/column/Submit')
  },
  // 栏目列表
  {
    path: '/admin/column/list',
    name: 'adminColumnList',
    meta: {
      title: '栏目列表',
      requireAuth: true
    },
    component: require('@/components/admin/column/List')
  },
  // 项目列表
  {
    path: '/admin/item/list',
    name: 'adminItemList',
    meta: {
      title: '项目列表',
      requireAuth: true
    },
    component: require('@/components/admin/item/List')
  },
  // 项目详情
  {
    path: '/admin/item/show/:id',
    name: 'adminItemShow',
    meta: {
      title: '项目详情',
      requireAuth: true
    },
    component: require('@/components/admin/item/Show')
  },
  // 项目匹配公司
  {
    path: '/admin/item/match/:id',
    name: 'adminItemMatch',
    meta: {
      title: '项目匹配',
      requireAuth: true
    },
    component: require('@/components/admin/item/Match')
  },
  // 订单列表
  {
    path: '/admin/order/list',
    name: 'adminOrderList',
    meta: {
      title: '订单列表',
      requireAuth: true
    },
    component: require('@/components/admin/order/List')
  },
  // 提现列表
  {
    path: '/admin/with_draw/list',
    name: 'adminWithDrawList',
    meta: {
      title: '提现列表',
      requireAuth: true
    },
    component: require('@/components/admin/with_draw/List')
  },
  // 设计公司列表
  {
    path: '/admin/company/list',
    name: 'adminCompanyList',
    meta: {
      title: '设计公司列表',
      requireAuth: true
    },
    component: require('@/components/admin/company/List')
  },
  // 设计公司详情
  {
    path: '/admin/company/show/:id',
    name: 'adminCompanyShow',
    meta: {
      title: '设计公司详情',
      requireAuth: true
    },
    component: require('@/components/admin/company/Show')
  },
  // 需求公司列表
  {
    path: '/admin/demand_company/list',
    name: 'adminDemandCompanyList',
    meta: {
      title: '需求公司列表',
      requireAuth: true
    },
    component: require('@/components/admin/demand_company/List')
  },
  // 需求公司详情
  {
    path: '/admin/demand_company/show/:id',
    name: 'adminDemandCompanyShow',
    meta: {
      title: '需求公司详情',
      requireAuth: true
    },
    component: require('@/components/admin/demand_company/Show')
  },
  // 案例列表
  {
    path: '/admin/design_case/list',
    name: 'adminDesignCaseList',
    meta: {
      title: '案例列表',
      requireAuth: true
    },
    component: require('@/components/admin/design_case/List')
  },
  // 用户列表
  {
    path: '/admin/user/list',
    name: 'adminUserList',
    meta: {
      title: '用户列表',
      requireAuth: true
    },
    component: require('@/components/admin/user/List')
  },
  // 用户编辑
  {
    path: '/admin/user/submit',
    name: 'adminUserSubmit',
    meta: {
      title: '用户编辑',
      requireAuth: true
    },
    component: require('@/components/admin/user/Submit')
  },
  // 分类列表
  {
    path: '/admin/category/list',
    name: 'adminCategoryList',
    meta: {
      title: '分类列表',
      requireAuth: true
    },
    component: require('@/components/admin/category/List')
  },
  // 编辑分类
  {
    path: '/admin/category/submit',
    name: 'adminCategorySubmit',
    meta: {
      title: '分类编辑',
      requireAuth: true
    },
    component: require('@/components/admin/category/Submit')
  },
  // 分类详情
  {
    path: '/admin/category/show/:id',
    name: 'adminCategoryShow',
    meta: {
      title: '分类详情',
      requireAuth: true
    },
    component: require('@/components/admin/category/Show')
  },
  // 文章列表
  {
    path: '/admin/article/list',
    name: 'adminArticleList',
    meta: {
      title: '文章列表',
      requireAuth: true
    },
    component: require('@/components/admin/article/List')
  },
  // 编辑文章
  {
    path: '/admin/article/submit',
    name: 'adminArticleSubmit',
    meta: {
      title: '文章编辑',
      requireAuth: true
    },
    // 按需加载
    component: (resolve) => {
      require(['@/components/admin/article/Submit'], resolve)
    }
  },
  // 文章详情
  {
    path: '/admin/article/show/:id',
    name: 'adminArticleShow',
    meta: {
      title: '文章详情',
      requireAuth: true
    },
    component: require('@/components/admin/article/Show')
  },
  // 作品列表
  {
    path: '/admin/works/list',
    name: 'adminWorksList',
    meta: {
      title: '作品列表',
      requireAuth: true
    },
    component: require('@/components/admin/works/List')
  },
  // 作品详情
  {
    path: '/admin/works/show/:id',
    name: 'adminWorksShow',
    meta: {
      title: '作品详情',
      requireAuth: true
    },
    component: require('@/components/admin/works/Show')
  },
  // 日历列表
  {
    path: '/admin/awards/list',
    name: 'adminAwardsList',
    meta: {
      title: '日历列表',
      requireAuth: true
    },
    component: require('@/components/admin/awards/List')
  },
  // 编辑日历
  {
    path: '/admin/awards/submit',
    name: 'adminAwardsSubmit',
    meta: {
      title: '日历编辑',
      requireAuth: true
    },
    component: require('@/components/admin/awards/Submit')
  },
  // 添加趋势报告
  {
    path: '/admin/trend_report/add',
    name: 'adminTrendReportAdd',
    meta: {
      title: '添加趋势报告',
      requireAuth: true
    },
    component: require('@/components/admin/trend_report/Submit')
  },
  // 编辑趋势报告
  {
    path: '/admin/trend_report/edit/:id',
    name: 'adminTrendReportEdit',
    meta: {
      title: '编辑趋势报告',
      requireAuth: true
    },
    component: require('@/components/admin/trend_report/Submit')
  },
  // 趋势报告列表
  {
    path: '/admin/trend_report/list',
    name: 'adminTrendReportList',
    meta: {
      title: '趋势报告列表',
      requireAuth: true
    },
    component: require('@/components/admin/trend_report/List')
  },
  // 添加常用网站
  {
    path: '/admin/commonly_site/add',
    name: 'adminCommonlySiteAdd',
    meta: {
      title: '添加常用网站',
      requireAuth: true
    },
    component: require('@/components/admin/commonly_site/Submit')
  },
  // 编辑常用网站
  {
    path: '/admin/commonly_site/edit/:id',
    name: 'adminCommonlySiteEdit',
    meta: {
      title: '编辑常用网站',
      requireAuth: true
    },
    component: require('@/components/admin/commonly_site/Submit')
  },
  // 常用网站列表
  {
    path: '/admin/commonly_site/list',
    name: 'adminCommonlySiteList',
    meta: {
      title: '常用网站列表',
      requireAuth: true
    },
    component: require('@/components/admin/commonly_site/List')
  },
  // 添加奖项案例
  {
    path: '/admin/award_case/add',
    name: 'adminAwardCaseAdd',
    meta: {
      title: '添加奖项案例',
      requireAuth: true
    },
    // 按需加载
    component: (resolve) => {
      require(['@/components/admin/award_case/Submit'], resolve)
    }
  },
  // 编辑奖项案例
  {
    path: '/admin/award_case/edit/:id',
    name: 'adminAwardCaseEdit',
    meta: {
      title: '编辑奖项案例',
      requireAuth: true
    },
    // 按需加载
    component: (resolve) => {
      require(['@/components/admin/award_case/Submit'], resolve)
    }
  },
  // 奖项案例列表
  {
    path: '/admin/award_case/list',
    name: 'adminAwardCaseList',
    meta: {
      title: '奖项案例列表',
      requireAuth: true
    },
    component: require('@/components/admin/award_case/List')
  },
  // 添加系统通知
  {
    path: '/admin/notice/add',
    name: 'adminNoticeAdd',
    meta: {
      title: '添加系统通知',
      requireAuth: true
    },
    component: require('@/components/admin/notice/Submit')
  },
  // 编辑系统通知
  {
    path: '/admin/notice/edit/:id',
    name: 'adminNoticeEdit',
    meta: {
      title: '编辑系统通知',
      requireAuth: true
    },
    component: require('@/components/admin/notice/Submit')
  },
  // 系统通知列表
  {
    path: '/admin/notice/list',
    name: 'adminNoticeList',
    meta: {
      title: '系统通知列表',
      requireAuth: true
    },
    component: require('@/components/admin/notice/List')
  },
  // 添加区块
  {
    path: '/admin/block/add',
    name: 'adminBlockAdd',
    meta: {
      title: '添加区块',
      requireAuth: true
    },
    component: require('@/components/admin/block/Submit')
  },
  // 编辑区块
  {
    path: '/admin/block/edit/:id',
    name: 'adminBlockEdit',
    meta: {
      title: '编辑区块',
      requireAuth: true
    },
    component: require('@/components/admin/block/Submit')
  },
  // 区块列表
  {
    path: '/admin/block/list',
    name: 'adminBlockList',
    meta: {
      title: '区块列表',
      requireAuth: true
    },
    component: require('@/components/admin/block/List')
  }
]
