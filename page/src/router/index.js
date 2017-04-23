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
    path: '/vcenter/item/list',
    name: 'vcenterItemList',
    meta: {
      requireAuth: true
    },
    component: require('@/components/pages/v_center/computer/Profile')
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
