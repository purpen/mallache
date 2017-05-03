import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '../store/index'
import * as types from '../store/mutation-types'

Vue.use(VueRouter)

// 页面刷新时，重新赋值token
if (window.localStorage.getItem('token')) {
  store.commit(types.USER_SIGNIN, JSON.parse(window.localStorage.getItem('token')))
}

const routes = [
  {
    path: '/',
    redirect: '/home'
  },
  {
    path: '/home',
    name: 'home',
    component: require('@/components/pages/home/Home')
  },
  {
    path: '/about',
    name: 'about',
    component: require('@/components/pages/home/About')
  },
  {
    path: '/server',
    name: 'server',
    component: require('@/components/pages/home/Server')
  },
  {
    path: '/stuff',
    name: 'stuff',
    component: require('@/components/pages/home/Stuff')
  },
  {
    path: '/apply',
    name: 'apply',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/home/Apply')
  },
  {
    path: '/login',
    name: 'login',
    component: require('@/components/pages/auth/Login')
  },
  {
    path: '/logout',
    name: 'logout',
    component: require('@/components/pages/auth/Logout')
  },
  {
    path: '/register',
    name: 'register',
    component: require('@/components/pages/auth/Register')
  },

  // 发布需求(第一步) 支付
  {
    path: '/item/submit_one',
    name: 'itemSubmitOne',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/item/SubmitOne')
  },

  // 发布需求(第二步) 选择领域
  {
    path: '/item/submit_type/:id',
    name: 'itemSubmitTwo',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/item/SubmitTwo')
  },

  // 发布需求(第三步) 添写基本信息(产品设计)
  {
    path: '/item/submit_base/:id',
    name: 'itemSubmitThree',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/item/SubmitThree')
  },

  // 发布需求(第三步) 添写基本信息(UI设计)
  {
    path: '/item/submit_ui_base/:id',
    name: 'itemSubmitUIThree',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/item/SubmitUIThree')
  },

  // 发布需求(第四步) 添写公司信息
  {
    path: '/item/submit_company/:id',
    name: 'itemSubmitFour',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/item/SubmitFour')
  },

  // 发布需求(第五步) 检查并发布
  {
    path: '/item/submit_check/:id',
    name: 'itemSubmitFive',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/item/SubmitFive')
  },

  // 发布需求(第五步) 检查并发布
  {
    path: '/item/publish',
    name: 'itemPublish',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/item/Publish')
  },

  // 支付
  {
    path: '/item/payment',
    name: 'itemPayment',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/item/Payment')
  },
  // 支付宝回调
  {
    path: '/alipay/callback',
    name: 'alipayCallback',
    meta: {
      requireAuth: false
    },
    component: require('@/components/pages/pay/AlipayCallback')
  },
  // 自定义输出页面
  {
    path: '/blank',
    name: 'blank',
    meta: {
      requireAuth: false
    },
    component: require('@/components/block/Blank')
  },

  // 个人/公司主页
  {
    path: '/user/:id',
    name: 'userShow',
    meta: {
      requireAuth: false
    },
    component: require('@/components/pages/home/Apply')
  },

  // 公司基本设置
  {
    path: '/vcenter/computer/profile',
    name: 'vcenterComputerProfile',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/v_center/computer/Profile')
  },
  // 公司接单设置
  {
    path: '/vcenter/computer/taking',
    name: 'vcenterComputerTaking',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/v_center/computer/Taking')
  },
  // 公司认证
  {
    path: '/vcenter/computer',
    name: 'vcenterComputerAccreditation',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/v_center/computer/Accreditation')
  },
  // 项目动态
  {
    path: '/vcenter/remind/list',
    name: 'vcenterRemindList',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/v_center/item/List')
  },
  // 订单列表
  {
    path: '/vcenter/order/list',
    name: 'vcenterOrderList',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/v_center/computer/Profile')
  },
  // 作品列表
  {
    path: '/vcenter/design_case',
    name: 'vcenterDesignCaseList',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/v_center/design_case/List')
  },
  // 作品详情
  {
    path: '/vcenter/design_case/show/:id',
    name: 'vcenterDesignCaseShow',
    meta: {
      requireAuth: false
    },
    component: require('@/components/pages/design_case/Show')
  },
  // 添加作品
  {
    path: '/vcenter/design_case/add',
    name: 'vcenterDesignCaseAdd',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/v_center/design_case/Submit')
  },
  // 编辑作品
  {
    path: '/vcenter/design_case/edit/:id',
    name: 'vcenterDesignCaseEdit',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/v_center/design_case/Submit')
  },
  // 我的钱包
  {
    path: '/vcenter/wallet/list',
    name: 'vcenterWalletList',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/v_center/computer/Profile')
  },
  // 我的项目列表(需求方)
  {
    path: '/vcenter/item/list',
    name: 'vcenterItemList',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/v_center/item/List')
  },
  // 我的项目列表(设计公司)
  {
    path: '/vcenter/citem/list',
    name: 'vcenterCItemList',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/v_center/c_item/List')
  },
  // 基本设置
  {
    path: '/vcenter/profile',
    name: 'vcenterProfile',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/v_center/computer/Profile')
  },
  {
    path: '/remind',
    name: 'remind',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/home/Apply')
  }
]

const router = new VueRouter({
  mode: 'history',
  linkActiveClass: 'is-active', // 这是链接激活时的class
  routes
})

router.beforeEach((to, from, next) => {
  if (to.matched.some(r => r.meta.requireAuth)) {
    if (store.state.event.token) {
      next()
    } else {
      next({
        path: '/login'
        // query: {redirect: to.fullPath}
      })
    }
  } else {
    next()
  }
})

export default router
