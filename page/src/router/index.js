import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '../store/index'
import * as types from '../store/mutation-types'
import baseRoute from './routes/base.js'
import adminRoute from './routes/admin.js'
import centerRoute from './routes/center.js'
import toolsRoute from './routes/tools.js'
import {
  calcImgSize
} from 'assets/js/common'

Vue.use(VueRouter)

// 页面刷新时，重新赋值token
if (window.localStorage.getItem('token')) {
  // console.log(window.localStorage.getItem('token'))
  store.commit(types.USER_SIGNIN, JSON.parse(window.localStorage.getItem('token')))
}

let routes = []
routes = [

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
      title: '测试'
    },
    component: require('@/components/pages/home/test')
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
    redirect: '/design_case/general_list'
  },
  {
    path: '/design_case/list',
    name: 'design_case',
    redirect: '/design_case/general_list'
  },
  {
    path: '/design_case/general_list',
    name: 'designGeneralList',
    meta: {
      title: '作品案例',
      requireAuth: false
    },
    component: require('@/components/pages/design_case/GeneralList')
  },
  {
    path: '/design_case/awards_list',
    name: 'designAwardsList',
    meta: {
      title: '设计奖项',
      requireAuth: false
    },
    component: require('@/components/pages/design_case/AwardsList')
  },
  {
    path: '/design_case/awards_show/:id',
    name: 'designAwardsShow',
    meta: {
      title: '案例详情',
      requireAuth: false
    },
    // 按需加载
    component: (resolve) => {
      require(['@/components/pages/design_case/AwardsShow'], resolve)
    }
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
  // #### 专题页 ######
  // 浙江设计再造--台州黄岩
  {
    path: '/subject/zj',
    name: 'subject_zj',
    meta: {
      requireAuth: false,
      title: '浙江“传统产业设计再造”对接会'
    },
    component: require('@/components/pages/subject/Zj')
  },
  // 浙江设计再造--杭州良渚
  {
    path: '/subject/zj_lz',
    name: 'subject_zj_lz',
    meta: {
      requireAuth: false,
      title: '浙江“传统产业设计再造”对接会'
    },
    component: require('@/components/pages/subject/ZjLz')
  },
  // 浙江设计再造--金华永康
  {
    path: '/subject/zj_yk',
    name: 'subject_zj_yk',
    meta: {
      requireAuth: false,
      title: '浙江“传统产业设计再造”对接会'
    },
    component: require('@/components/pages/subject/ZjYk')
  },
  // RCIP衍生创新峰会
  {
    path: '/subject/rcip',
    name: 'subject_rcip',
    meta: {
      requireAuth: false,
      title: 'RCIP衍生创新峰会'
    },
    component: require('@/components/pages/subject/Rcip')
  },
  // 羽泉的礼物
  {
    path: '/subject/YuQuanGifts',
    name: 'YuQuanGifts',
    meta: {
      requireAuth: false,
      title: '羽泉的礼物'
    },
    component: require('@/components/pages/subject/YuQuanGifts')
  },
  // 创新力指数
  {
    path: '/innovation_index',
    name: 'InnovationIndex',
    meta: {
      requireAuth: false,
      title: '中国设计企业创新力指数'
    },
    component: require('@/components/pages/home/InnovationIndex')
  },
  // 企业招募
  {
    path: '/subject/EnterpriseRecruit',
    name: 'EnterpriseRecruit',
    meta: {
      requireAuth: false,
      title: '企业招募'
    },
    component: require('@/components/pages/subject/EnterpriseRecruit')
  },
  // 产品招募
  {
    path: '/subject/ProductRecruit',
    name: 'ProductRecruit',
    meta: {
      requireAuth: false,
      title: '产品招募'
    },
    component: require('@/components/pages/subject/ProductRecruit')
  },

  // 专题列表
  {
    path: '/subject/list',
    name: 'subjectList',
    meta: {
      title: '专题列表',
      requireAuth: false
    },
    component: require('@/components/pages/subject/List')
  },

  // 文章列表
  {
    path: '/article/list',
    name: 'articleList',
    meta: {
      title: '文章列表',
      requireAuth: false
    },
    component: require('@/components/pages/article/List')
  },
  // 文章详情
  {
    path: '/article/show/:id',
    name: 'articleShow',
    meta: {
      title: '文章详情',
      requireAuth: false
    },
    // 按需加载
    component: (resolve) => {
      require(['@/components/pages/article/Show'], resolve)
    }
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
    path: '/identity',
    name: 'identity',
    meta: {
      title: '注册'
    },
    component: require('@/components/pages/auth/choiceIdentity')
  },
  {
    path: '/register',
    name: 'register',
    meta: {
      title: '注册'
    },
    component: require('@/components/pages/auth/Register')
  },
  {
    path: '/invite/:code',
    name: 'invite',
    meta: {
      title: '邀请注册'
    },
    component: require('@/components/pages/auth/Invite')
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
    component: (resolve) => {
      require(['@/components/pages/item/Payment'], resolve)
    }
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
      requireAuth: false
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
      requireAuth: false
    },
    component: require('@/components/pages/company/Show')
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
  // 参赛作品详情
  {
    path: '/match_case/show/:id',
    name: 'vcenterMatchCaseShow',
    meta: {
      title: '参赛作品详情',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/match_case/Show')
  },
  {
    path: '/remind',
    name: 'remind',
    meta: {
      title: '提醒',
      requireAuth: true
    },
    component: require('@/components/pages/home/Apply')
  }
]

routes = routes.concat(baseRoute)
routes = routes.concat(adminRoute)
routes = routes.concat(centerRoute)
routes = routes.concat(toolsRoute)

const router = new VueRouter({
  mode: 'history',
  linkActiveClass: 'is-active', // 这是链接激活时的class
  routes
})

router.beforeEach((to, from, next) => {
  if (to.meta.title) {
    if (to.meta.title === '首页') {
      document.title = '铟果-中国领先的产品创新SaaS平台'
    } else {
      document.title = to.meta.title + '-铟果-中国领先的产品创新SaaS平台'
    }
  } else {
    document.title = '铟果-中国领先的产品创新SaaS平台'
  }
  if (to.matched.some(r => r.meta.requireAuth)) {
    if (store.state.event.token) {
      next()
    } else {
      store.commit(types.PREV_URL_NAME, to.path)
      next({
        name: 'login'
      })
    }
  } else {
    next()
  }
})

router.afterEach((to, from) => {
  calcImgSize()
})

export default router
