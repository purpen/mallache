import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '../store/index'
import * as types from '../store/mutation-types'

Vue.use(VueRouter)

// 页面刷新时，重新赋值token
if (window.localStorage.getItem('token')) {
  // console.log(window.localStorage.getItem('token'))
  store.commit(types.USER_SIGNIN, JSON.parse(window.localStorage.getItem('token')))
}

const routes = [

  // ### 静态页面 #####
  {
    path: '/',
    redirect: '/home'
  },
  {
    path: '/home',
    name: 'home',
    meta: {
      title: '首页'
    },
    component: require('@/components/pages/home/Home')
  },
  {
    path: '/test',
    name: 'test',
    meta: {
      title: '首页'
    },
    component: require('@/components/pages/home/Home')
  },
  {
    path: '/about',
    name: 'about',
    meta: {
      title: '关于'
    },
    component: require('@/components/pages/home/About')
  },
  {
    path: '/server',
    name: 'server',
    meta: {
      title: '服务'
    },
    component: require('@/components/pages/home/Server')
  },
  {
    path: '/server_design',
    name: 'serverDesign',
    meta: {
      title: '服务-设计公司'
    },
    component: require('@/components/pages/home/ServerDesign')
  },
  {
    path: '/stuff',
    name: 'stuff',
    meta: {
      title: '灵感'
    },
    component: require('@/components/pages/home/Stuff')
  },
  // 联系我们
  {
    path: '/contact',
    name: 'contact',
    meta: {
      title: '联系我们',
      requireAuth: false
    },
    component: require('@/components/pages/home/Contact')
  },
  // 服务条款
  {
    path: '/item',
    name: 'item',
    meta: {
      title: '服务条款',
      requireAuth: false
    },
    component: require('@/components/pages/home/Item')
  },
  // 常见问题
  {
    path: '/question',
    name: 'question',
    meta: {
      title: '常见问题',
      requireAuth: false
    },
    component: require('@/components/pages/home/Question')
  },
  // 交易保障
  {
    path: '/trade',
    name: 'trade',
    meta: {
      title: '交易保障',
      requireAuth: false
    },
    component: require('@/components/pages/home/Trade')
  },
  {
    path: '/apply',
    name: 'apply',
    meta: {
      title: '申请入驻',
      requireAuth: true
    },
    component: require('@/components/pages/home/Apply')
  },
  {
    path: '/login',
    name: 'login',
    meta: {
      title: '登录'
    },
    component: require('@/components/pages/auth/Login')
  },
  {
    path: '/logout',
    name: 'logout',
    meta: {
      title: '登出'
    },
    component: require('@/components/pages/auth/Logout')
  },
  {
    path: '/register',
    name: 'register',
    meta: {
      title: '注册'
    },
    component: require('@/components/pages/auth/Register')
  },
  // 找回密码
  {
    path: '/forget',
    name: 'forget',
    meta: {
      title: '找回密码'
    },
    component: require('@/components/pages/auth/Forget')
  },

  // 发布需求(第一步) 支付
  {
    path: '/item/submit_one',
    name: 'itemSubmitOne',
    meta: {
      title: '发布需求',
      requireAuth: true
    },
    component: require('@/components/pages/item/SubmitOne')
  },

  // 发布需求(第二步) 选择领域
  {
    path: '/item/submit_type/:id',
    name: 'itemSubmitTwo',
    meta: {
      title: '选择类型',
      requireAuth: true
    },
    component: require('@/components/pages/item/SubmitTwo')
  },

  // 发布需求(第三步) 添写基本信息(产品设计)
  {
    path: '/item/submit_base/:id',
    name: 'itemSubmitThree',
    meta: {
      title: '基本信息',
      requireAuth: true
    },
    component: require('@/components/pages/item/SubmitThree')
  },

  // 发布需求(第三步) 添写基本信息(UI设计)
  {
    path: '/item/submit_ui_base/:id',
    name: 'itemSubmitUIThree',
    meta: {
      title: '基本信息',
      requireAuth: true
    },
    component: require('@/components/pages/item/SubmitUIThree')
  },

  // 发布需求(第四步) 添写公司信息
  {
    path: '/item/submit_company/:id',
    name: 'itemSubmitFour',
    meta: {
      title: '补全公司信息',
      requireAuth: true
    },
    component: require('@/components/pages/item/SubmitFour')
  },

  // 发布需求(第五步) 检查并发布
  {
    path: '/item/submit_check/:id',
    name: 'itemSubmitFive',
    meta: {
      title: '检查并发布',
      requireAuth: true
    },
    component: require('@/components/pages/item/SubmitFive')
  },

  // 发布需求(第五步) 检查并发布
  {
    path: '/item/publish',
    name: 'itemPublish',
    meta: {
      title: '发布',
      requireAuth: true
    },
    component: require('@/components/pages/item/Publish')
  },

  // 支付99预付款
  {
    path: '/item/payment',
    name: 'itemPayment',
    meta: {
      title: '支付',
      requireAuth: true
    },
    // 按需加载
    component: (resolve) => { require(['@/components/pages/item/Payment'], resolve) }
  },
  // 支付宝回调
  {
    path: '/alipay/callback',
    name: 'alipayCallback',
    meta: {
      title: '支付宝-支付结果',
      requireAuth: false
    },
    component: require('@/components/pages/pay/AlipayCallback')
  },
  // 京东回调
  {
    path: '/jdpay/callback',
    name: 'jdpayCallback',
    meta: {
      title: '京东-支付结果',
      requireAuth: false
    },
    component: require('@/components/pages/pay/JdCallback')
  },
  // 微信回调
  {
    path: '/wxpay/callback',
    name: 'wxpayCallback',
    meta: {
      title: '微信-支付结果',
      requireAuth: true
    },
    component: require('@/components/pages/pay/wxCallback')
  },
  // 支付项目资金
  {
    path: '/item/pay_fund/:item_id',
    name: 'itemPayFund',
    meta: {
      title: '支付项目资金',
      requireAuth: true
    },
    component: require('@/components/pages/item/PayFund')
  },
  // 自定义输出页面
  {
    path: '/blank',
    name: 'blank',
    meta: {
      title: '',
      requireAuth: false
    },
    component: require('@/components/block/Blank')
  },

  // 个人主页
  {
    path: '/user/:id',
    name: 'userShow',
    meta: {
      title: '个人主页',
      requireAuth: true
    },
    component: require('@/components/pages/user/Show')
  },

  // 控制面板
  {
    path: '/vcenter/control',
    name: 'vcenterControl',
    meta: {
      title: '控制面板',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/common/Control')
  },

  // 公司主页
  {
    path: '/company/:id',
    name: 'companyShow',
    meta: {
      title: '公司主页',
      requireAuth: true
    },
    component: require('@/components/pages/company/Show')
  },

  // 公司基本设置(remove)
  {
    path: '/vcenter/company/profile',
    name: 'vcenterComputerProfile',
    meta: {
      title: '完善公司信息',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/company/Profile')
  },
  // 公司基本设置
  {
    path: '/vcenter/company/base',
    name: 'vcenterComputerBase',
    meta: {
      title: '公司基本设置',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/company/Base')
  },
  // 公司接单设置
  {
    path: '/vcenter/company/taking',
    name: 'vcenterComputerTaking',
    meta: {
      title: '接单设置',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/company/Taking')
  },
  // 公司认证
  {
    path: '/vcenter/company/accreditation',
    name: 'vcenterComputerAccreditation',
    meta: {
      title: '公司认证',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/company/Accreditation')
  },
  // 公司认证-编辑
  {
    path: '/vcenter/company/identification',
    name: 'vcenterComputerIdentification',
    meta: {
      title: '公司认证',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/company/Identification')
  },
  // 项目动态
  {
    path: '/vcenter/remind/list',
    name: 'vcenterRemindList',
    meta: {
      title: '项目动态',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/item/List')
  },
  // 订单列表
  {
    path: '/vcenter/order/list',
    name: 'vcenterOrderList',
    meta: {
      title: '订单列表',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/order/List')
  },
  // 订单详情
  {
    path: '/vcenter/order/show/:id',
    name: 'vcenterOrderShow',
    meta: {
      title: '订单详情',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/order/Show')
  },
  // 消息列表
  {
    path: '/vcenter/message/list',
    name: 'vcenterMessageList',
    meta: {
      title: '消息列表',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/message/List')
  },
  // 作品列表
  {
    path: '/vcenter/design_case',
    name: 'vcenterDesignCaseList',
    meta: {
      title: '作品列表',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/design_case/List')
  },
  // 作品详情
  {
    path: '/design_case/show/:id',
    name: 'vcenterDesignCaseShow',
    meta: {
      title: '作品详情',
      requireAuth: false
    },
    component: require('@/components/pages/design_case/Show')
  },
  // 添加作品
  {
    path: '/vcenter/design_case/add',
    name: 'vcenterDesignCaseAdd',
    meta: {
      title: '添加作品',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/design_case/Submit')
  },
  // 编辑作品
  {
    path: '/vcenter/design_case/edit/:id',
    name: 'vcenterDesignCaseEdit',
    meta: {
      title: '编辑作品',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/design_case/Submit')
  },
  // 我的项目列表(需求方)
  {
    path: '/vcenter/item/list',
    name: 'vcenterItemList',
    meta: {
      title: '项目列表',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/item/List')
  },
  // 项目详情--需求方
  {
    path: '/vcenter/item/show/:id',
    name: 'vcenterItemShow',
    meta: {
      title: '项目详情',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/item/Show')
  },
  // 项目详情--公司方
  {
    path: '/vcenter/citem/show/:id',
    name: 'vcenterCItemShow',
    meta: {
      title: '项目详情',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/c_item/Show')
  },

  // 我的项目列表(设计公司) -- 待合作
  {
    path: '/vcenter/citem/list',
    name: 'vcenterCItemList',
    meta: {
      title: '项目列表',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/c_item/List')
  },
  // 我的项目列表(设计公司) -- 已合作
  {
    path: '/vcenter/citem/true_list',
    name: 'vcenterTrueCItemList',
    meta: {
      title: '项目列表',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/c_item/TrueList')
  },
  // 在线合同预览
  {
    path: '/vcenter/contract/show/:unique_id',
    name: 'vcenterContractView',
    meta: {
      title: '在线合同编辑',
      requireAuth: true
    },
    // 按需加载
    component: (resolve) => { require(['@/components/pages/v_center/contract/View'], resolve) }
  },
  // 在线合同编辑
  {
    path: '/vcenter/contract/submit/:item_id',
    name: 'vcenterContractSubmit',
    meta: {
      title: '在线合同编辑',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/contract/Submit')
  },
  // 合同下载
  {
    path: '/vcenter/contract/download/:unique_id',
    name: 'vcenterContractDown',
    meta: {
      title: '合同下载',
      requireAuth: true
    },
    // 按需加载
    component: (resolve) => { require(['@/components/pages/v_center/contract/Down'], resolve) }
  },
  // 基本设置
  {
    path: '/vcenter/profile',
    name: 'vcenterProfile',
    meta: {
      title: '设置',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/company/Profile')
  },
  {
    path: '/remind',
    name: 'remind',
    meta: {
      title: '提醒',
      requireAuth: true
    },
    component: require('@/components/pages/home/Apply')
  },
  // 添加阶段
  {
    path: '/vcenter/stage/add/:item_id',
    name: 'vcenterDesignStageAdd',
    meta: {
      title: '添加阶段',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/stage/Submit')
  },
  // 编辑阶段
  {
    path: '/vcenter/stage/edit/:id',
    name: 'vcenterDesignStageEdit',
    meta: {
      title: '编辑阶段',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/stage/Submit')
  },
  // 预览阶段
  {
    path: '/vcenter/stage/show/:id',
    name: 'vcenterDesignStageShow',
    meta: {
      title: '预览阶段',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/stage/Show')
  },

  // 需求方公司设置
  // 公司基本设置
  {
    path: '/vcenter/d_company/base',
    name: 'vcenterDComputerBase',
    meta: {
      title: '公司基本设置',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/d_company/Base')
  },
  // 公司认证
  {
    path: '/vcenter/d_company/accreditation',
    name: 'vcenterDCompanyAccreditation',
    meta: {
      title: '公司认证',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/d_company/Accreditation')
  },
  // 公司认证 -- 编辑
  {
    path: '/vcenter/d_company/identification',
    name: 'vcenterDCompanyIdentification',
    meta: {
      title: '公司认证',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/d_company/Identification')
  },
  // 我的钱包列表
  {
    path: '/vcenter/wallet/list',
    name: 'vcenterWalletList',
    meta: {
      title: '我的钱包',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/wallet/List')
  },
  // 我的银行卡列表
  {
    path: '/vcenter/bank/list',
    name: 'vcenterBankList',
    meta: {
      title: '我的银行卡',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/bank/List')
  },

  // ### 安全设置 #####
  // 修改密码
  {
    path: '/vcenter/modify_pwd',
    name: 'modifyPwd',
    meta: {
      title: '修改密码'
    },
    component: require('@/components/pages/v_center/account/ModifyPwd')
  },

  // ###### 后台管理 ##########
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
  }
]

const router = new VueRouter({
  mode: 'history',
  linkActiveClass: 'is-active', // 这是链接激活时的class
  routes
})

router.beforeEach((to, from, next) => {
  if (to.meta.title) {
    document.title = to.meta.title + '-铟果-中国领先的产品创新SaaS平台'
  } else {
    document.title = '毕方鸟 SaaS'
  }
  if (to.matched.some(r => r.meta.requireAuth)) {
    if (store.state.event.token) {
      next()
    } else {
      store.commit(types.PREV_URL_NAME, to.name)
      next({name: 'login'})
    }
  } else {
    next()
  }
})

export default router
