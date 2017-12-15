import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '../store/index'
import * as types from '../store/mutation-types'
import {
  calcImgSize
} from 'assets/js/common'

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
    component: resolve => require(['@/components/pages/home/Home'], resolve)
  },
  {
    path: '/test',
    name: 'test',
    meta: {
      title: '首页'
    },
    component: resolve => require(['@/components/pages/home/Home'], resolve)
  },
  {
    path: '/about',
    name: 'about',
    meta: {
      title: '关于'
    },
    component: resolve => require(['@/components/pages/home/About'], resolve)
  },
  {
    path: '/server',
    name: 'server',
    meta: {
      title: '服务'
    },
    component: resolve => require(['@/components/pages/home/Server'], resolve)
  },
  {
    path: '/server_design',
    name: 'serverDesign',
    meta: {
      title: '服务-设计公司'
    },
    component: resolve => require(['@/components/pages/home/ServerDesign'], resolve)
  },
  {
    path: '/stuff',
    name: 'stuff',
    meta: {
      title: '灵感',
      requireAuth: false
    },
    component: resolve => require(['@/components/pages/home/Stuff'], resolve)
  },
  // 联系我们
  {
    path: '/contact',
    name: 'contact',
    meta: {
      title: '联系我们',
      requireAuth: false
    },
    component: resolve => require(['@/components/pages/home/Contact'], resolve)
  },
  // 服务条款
  {
    path: '/item',
    name: 'item',
    meta: {
      title: '服务条款',
      requireAuth: false
    },
    component: resolve => require(['@/components/pages/home/Item'], resolve)
  },
  // 常见问题
  {
    path: '/question',
    name: 'question',
    meta: {
      title: '常见问题',
      requireAuth: false
    },
    component: resolve => require(['@/components/pages/home/Question'], resolve)
  },
  // 交易保障
  {
    path: '/trade',
    name: 'trade',
    meta: {
      title: '交易保障',
      requireAuth: false
    },
    component: resolve => require(['@/components/pages/home/Trade'], resolve)
  },
  {
    path: '/apply',
    name: 'apply',
    meta: {
      title: '申请入驻',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/home/Apply'], resolve)
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
    component: resolve => require(['@/components/pages/subject/Zj'], resolve)
  },
  // 浙江设计再造--杭州良渚
  {
    path: '/subject/zj_lz',
    name: 'subject_zj_lz',
    meta: {
      requireAuth: false,
      title: '浙江“传统产业设计再造”对接会'
    },
    component: resolve => require(['@/components/pages/subject/ZjLz'], resolve)
  },
  // 浙江设计再造--金华永康
  {
    path: '/subject/zj_yk',
    name: 'subject_zj_yk',
    meta: {
      requireAuth: false,
      title: '浙江“传统产业设计再造”对接会'
    },
    component: resolve => require(['@/components/pages/subject/ZjYk'], resolve)
  },
  // RCIP衍生创新峰会
  {
    path: '/subject/rcip',
    name: 'subject_rcip',
    meta: {
      requireAuth: false,
      title: 'RCIP衍生创新峰会'
    },
    component: resolve => require(['@/components/pages/subject/Rcip'], resolve)
  },
  // 羽泉的礼物
  {
    path: '/subject/YuQuanGifts',
    name: 'YuQuanGifts',
    meta: {
      requireAuth: false,
      title: '羽泉的礼物'
    },
    component: resolve => require(['@/components/pages/subject/YuQuanGifts'], resolve)
  },
  {
    path: '/v_center/match_case/submit',
    name: 'vcenterMatchCaseSubmit',
    meta: {
      requireAuth: true,
      title: '上传作品'
    },
    component: resolve => require(['@/components/pages/v_center/match_case/uploadwork'], resolve)
  },
  // 企业招募
  {
    path: '/subject/EnterpriseRecruit',
    name: 'EnterpriseRecruit',
    meta: {
      requireAuth: false,
      title: '企业招募'
    },
    component: resolve => require(['@/components/pages/subject/EnterpriseRecruit'], resolve)
  },
  // 产品招募
  {
    path: '/subject/ProductRecruit',
    name: 'ProductRecruit',
    meta: {
      requireAuth: false,
      title: '产品招募'
    },
    component: resolve => require(['@/components/pages/subject/ProductRecruit'], resolve)
  },

  // 专题列表
  {
    path: '/subject/list',
    name: 'subjectList',
    meta: {
      title: '专题列表',
      requireAuth: false
    },
    component: resolve => require(['@/components/pages/subject/List'], resolve)
  },

  // 文章列表
  {
    path: '/article/list',
    name: 'articleList',
    meta: {
      title: '文章列表',
      requireAuth: false
    },
    component: resolve => require(['@/components/pages/article/List'], resolve)
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
    component: resolve => require(['@/components/pages/auth/Login'], resolve)
  },
  {
    path: '/logout',
    name: 'logout',
    meta: {
      title: '登出'
    },
    component: resolve => require(['@/components/pages/auth/Logout'], resolve)
  },
  {
    path: '/identity',
    name: 'identity',
    meta: {
      title: '注册'
    },
    component: resolve => require(['@/components/pages/auth/choiceIdentity'], resolve)
  },
  {
    path: '/register',
    name: 'register',
    meta: {
      title: '注册'
    },
    component: resolve => require(['@/components/pages/auth/Register'], resolve)
  },
  // 找回密码
  {
    path: '/forget',
    name: 'forget',
    meta: {
      title: '找回密码'
    },
    component: resolve => require(['@/components/pages/auth/Forget'], resolve)
  },

  // 发布需求(第一步) 支付
  {
    path: '/item/submit_one',
    name: 'itemSubmitOne',
    meta: {
      title: '发布需求',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/item/SubmitOne'], resolve)
  },

  // 发布需求(第二步) 选择领域
  {
    path: '/item/submit_type/:id',
    name: 'itemSubmitTwo',
    meta: {
      title: '选择类型',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/item/SubmitTwo'], resolve)
  },

  // 发布需求(第三步) 添写基本信息(产品设计)
  {
    path: '/item/submit_base/:id',
    name: 'itemSubmitThree',
    meta: {
      title: '基本信息',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/item/SubmitThree'], resolve)
  },

  // 发布需求(第三步) 添写基本信息(UI设计)
  {
    path: '/item/submit_ui_base/:id',
    name: 'itemSubmitUIThree',
    meta: {
      title: '基本信息',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/item/SubmitUIThree'], resolve)
  },

  // 发布需求(第四步) 添写公司信息
  {
    path: '/item/submit_company/:id',
    name: 'itemSubmitFour',
    meta: {
      title: '补全公司信息',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/item/SubmitFour'], resolve)
  },

  // 发布需求(第五步) 检查并发布
  {
    path: '/item/submit_check/:id',
    name: 'itemSubmitFive',
    meta: {
      title: '检查并发布',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/item/SubmitFive'], resolve)
  },

  // 发布需求(第五步) 检查并发布
  {
    path: '/item/publish',
    name: 'itemPublish',
    meta: {
      title: '发布',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/item/Publish'], resolve)
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
    component: resolve => require(['@/components/pages/pay/AlipayCallback'], resolve)
  },
  // 京东回调
  {
    path: '/jdpay/callback',
    name: 'jdpayCallback',
    meta: {
      title: '京东-支付结果',
      requireAuth: false
    },
    component: resolve => require(['@/components/pages/pay/JdCallback'], resolve)
  },
  // 微信回调
  {
    path: '/wxpay/callback',
    name: 'wxpayCallback',
    meta: {
      title: '微信-支付结果',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/pay/wxCallback'], resolve)
  },
  // 支付项目资金
  {
    path: '/item/pay_fund/:item_id',
    name: 'itemPayFund',
    meta: {
      title: '支付项目资金',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/item/PayFund'], resolve)
  },
  // 自定义输出页面
  {
    path: '/blank',
    name: 'blank',
    meta: {
      title: '',
      requireAuth: false
    },
    component: resolve => require(['@/components/block/Blank'], resolve)
  },

  // 个人主页
  {
    path: '/user/:id',
    name: 'userShow',
    meta: {
      title: '个人主页',
      requireAuth: false
    },
    component: resolve => require(['@/components/pages/user/Show'], resolve)
  },

  // 控制面板
  {
    path: '/vcenter/control',
    name: 'vcenterControl',
    meta: {
      title: '控制面板',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/common/Control'], resolve)
  },

  // 公司主页
  {
    path: '/company/:id',
    name: 'companyShow',
    meta: {
      title: '公司主页',
      requireAuth: false
    },
    component: resolve => require(['@/components/pages/company/Show'], resolve)
  },

  // 公司基本设置(remove)
  {
    path: '/vcenter/company/profile',
    name: 'vcenterComputerProfile',
    meta: {
      title: '完善公司信息',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/company/Profile'], resolve)
  },
  // 公司基本设置
  {
    path: '/vcenter/company/base',
    name: 'vcenterComputerBase',
    meta: {
      title: '公司基本设置',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/company/Base'], resolve)
  },
  // 公司接单设置
  {
    path: '/vcenter/company/taking',
    name: 'vcenterComputerTaking',
    meta: {
      title: '接单设置',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/company/Taking'], resolve)
  },
  // 公司认证
  {
    path: '/vcenter/company/accreditation',
    name: 'vcenterComputerAccreditation',
    meta: {
      title: '公司认证',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/company/Accreditation'], resolve)
  },
  // 公司认证-编辑
  {
    path: '/vcenter/company/identification',
    name: 'vcenterComputerIdentification',
    meta: {
      title: '公司认证',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/company/Identification'], resolve)
  },
  // 公司认证-编辑
  {
    path: '/vcenter/exhibition',
    name: 'vcenterExhibition',
    meta: {
      title: '日历',
      requireAuth: false
    },
    component: resolve => require(['@/components/pages/v_center/exhibition/exhibition'], resolve)
  },
  // 项目动态
  {
    path: '/vcenter/remind/list',
    name: 'vcenterRemindList',
    meta: {
      title: '项目动态',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/item/List'], resolve)
  },
  // 订单列表
  {
    path: '/vcenter/order/list',
    name: 'vcenterOrderList',
    meta: {
      title: '订单列表',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/order/List'], resolve)
  },
  // 订单详情
  {
    path: '/vcenter/order/show/:id',
    name: 'vcenterOrderShow',
    meta: {
      title: '订单详情',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/order/Show'], resolve)
  },
  // 消息列表
  {
    path: '/vcenter/message/list',
    name: 'vcenterMessageList',
    meta: {
      title: '消息列表',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/message/List'], resolve)
  },
  // 作品列表
  {
    path: '/vcenter/design_case',
    name: 'vcenterDesignCaseList',
    meta: {
      title: '作品列表',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/design_case/List'], resolve)
  },
  // 参赛作品
  {
    path: '/vcenter/match_case',
    name: 'vcenterMatchCaseList',
    meta: {
      title: '参赛作品',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/match_case/match_case'], resolve)
  },
  // 作品详情
  {
    path: '/design_case/show/:id',
    name: 'vcenterDesignCaseShow',
    meta: {
      title: '作品详情',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/design_case/Show'], resolve)
  },
  // 参赛作品详情
  {
    path: '/match_case/show/:id',
    name: 'vcenterMatchCaseShow',
    meta: {
      title: '参赛作品详情',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/match_case/Show'], resolve)
  },
  // 添加作品
  {
    path: '/vcenter/design_case/add',
    name: 'vcenterDesignCaseAdd',
    meta: {
      title: '添加作品',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/design_case/Submit'], resolve)
  },
  // 编辑作品
  {
    path: '/vcenter/design_case/edit/:id',
    name: 'vcenterDesignCaseEdit',
    meta: {
      title: '编辑作品',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/design_case/Submit'], resolve)
  },
  // 我的项目列表(需求方)
  {
    path: '/vcenter/item/list',
    name: 'vcenterItemList',
    meta: {
      title: '项目列表',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/item/List'], resolve)
  },
  // 项目详情--需求方
  {
    path: '/vcenter/item/show/:id',
    name: 'vcenterItemShow',
    meta: {
      title: '项目详情',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/item/Show'], resolve)
  },
  // 项目详情--公司方
  {
    path: '/vcenter/citem/show/:id',
    name: 'vcenterCItemShow',
    meta: {
      title: '项目详情',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/c_item/Show'], resolve)
  },

  // 我的项目列表(设计公司) -- 待合作
  {
    path: '/vcenter/citem/list',
    name: 'vcenterCItemList',
    meta: {
      title: '项目列表',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/c_item/List'], resolve)
  },
  // 我的项目列表(设计公司) -- 已合作
  {
    path: '/vcenter/citem/true_list',
    name: 'vcenterTrueCItemList',
    meta: {
      title: '项目列表',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/c_item/TrueList'], resolve)
  },
  // 在线合同预览
  {
    path: '/vcenter/contract/show/:unique_id',
    name: 'vcenterContractView',
    meta: {
      title: '合同预览',
      requireAuth: true
    },
    // 按需加载
    component: (resolve) => {
      require(['@/components/pages/v_center/contract/View'], resolve)
    }
  },
  // 在线合同编辑
  {
    path: '/vcenter/contract/submit/:item_id',
    name: 'vcenterContractSubmit',
    meta: {
      title: '在线合同编辑',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/contract/Submit'], resolve)
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
    component: (resolve) => {
      require(['@/components/pages/v_center/contract/Down'], resolve)
    }
  },
  // 基本设置
  {
    path: '/vcenter/profile',
    name: 'vcenterProfile',
    meta: {
      title: '设置',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/company/Profile'], resolve)
  },
  {
    path: '/remind',
    name: 'remind',
    meta: {
      title: '提醒',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/home/Apply'], resolve)
  },
  // 添加阶段
  {
    path: '/vcenter/stage/add/:item_id',
    name: 'vcenterDesignStageAdd',
    meta: {
      title: '添加阶段',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/stage/Submit'], resolve)
  },
  // 编辑阶段
  {
    path: '/vcenter/stage/edit/:id',
    name: 'vcenterDesignStageEdit',
    meta: {
      title: '编辑阶段',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/stage/Submit'], resolve)
  },
  // 预览阶段
  {
    path: '/vcenter/stage/show/:id',
    name: 'vcenterDesignStageShow',
    meta: {
      title: '预览阶段',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/stage/Show'], resolve)
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
    component: resolve => require(['@/components/pages/v_center/d_company/Base'], resolve)
  },
  // 公司认证
  {
    path: '/vcenter/d_company/accreditation',
    name: 'vcenterDCompanyAccreditation',
    meta: {
      title: '公司认证',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/d_company/Accreditation'], resolve)
  },
  // 公司认证 -- 编辑
  {
    path: '/vcenter/d_company/identification',
    name: 'vcenterDCompanyIdentification',
    meta: {
      title: '公司认证',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/d_company/Identification'], resolve)
  },
  // 我的钱包列表
  {
    path: '/vcenter/wallet/list',
    name: 'vcenterWalletList',
    meta: {
      title: '我的钱包',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/wallet/List'], resolve)
  },
  // 我的银行卡列表
  {
    path: '/vcenter/bank/list',
    name: 'vcenterBankList',
    meta: {
      title: '我的银行卡',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/bank/List'], resolve)
  },

  // ### 安全设置 #####
  // 修改密码
  {
    path: '/vcenter/modify_pwd',
    name: 'modifyPwd',
    meta: {
      title: '修改密码',
      requireAuth: true
    },
    component: resolve => require(['@/components/pages/v_center/account/ModifyPwd'], resolve)
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
    component: resolve => require(['@/components/admin/DashBoard'], resolve)
  },
  // 添加栏目
  {
    path: '/admin/column/add',
    name: 'adminColumnAdd',
    meta: {
      title: '添加栏目',
      requireAuth: true
    },
    component: resolve => require(['@/components/admin/column/Submit'], resolve)
  },
  // 编辑栏目
  {
    path: '/admin/column/edit/:id',
    name: 'adminColumnEdit',
    meta: {
      title: '编辑栏目',
      requireAuth: true
    },
    component: resolve => require(['@/components/admin/column/Submit'], resolve)
  },
  // 栏目列表
  {
    path: '/admin/column/list',
    name: 'adminColumnList',
    meta: {
      title: '栏目列表',
      requireAuth: true
    },
    component: resolve => require(['@/components/admin/column/List'], resolve)
  },
  // 项目列表
  {
    path: '/admin/item/list',
    name: 'adminItemList',
    meta: {
      title: '项目列表',
      requireAuth: true
    },
    component: resolve => require(['@/components/admin/item/List'], resolve)
  },
  // 项目详情
  {
    path: '/admin/item/show/:id',
    name: 'adminItemShow',
    meta: {
      title: '项目详情',
      requireAuth: true
    },
    component: resolve => require(['@/components/admin/item/Show'], resolve)
  },
  // 项目匹配公司
  {
    path: '/admin/item/match/:id',
    name: 'adminItemMatch',
    meta: {
      title: '项目匹配',
      requireAuth: true
    },
    component: resolve => require(['@/components/admin/item/Match'], resolve)
  },
  // 订单列表
  {
    path: '/admin/order/list',
    name: 'adminOrderList',
    meta: {
      title: '订单列表',
      requireAuth: true
    },
    component: resolve => require(['@/components/admin/order/List'], resolve)
  },
  // 提现列表
  {
    path: '/admin/with_draw/list',
    name: 'adminWithDrawList',
    meta: {
      title: '提现列表',
      requireAuth: true
    },
    component: resolve => require(['@/components/admin/with_draw/List'], resolve)
  },
  // 设计公司列表
  {
    path: '/admin/company/list',
    name: 'adminCompanyList',
    meta: {
      title: '设计公司列表',
      requireAuth: true
    },
    component: resolve => require(['@/components/admin/company/List'], resolve)
  },
  // 设计公司详情
  {
    path: '/admin/company/show/:id',
    name: 'adminCompanyShow',
    meta: {
      title: '设计公司详情',
      requireAuth: true
    },
    component: resolve => require(['@/components/admin/company/Show'], resolve)
  },
  // 需求公司列表
  {
    path: '/admin/demand_company/list',
    name: 'adminDemandCompanyList',
    meta: {
      title: '需求公司列表',
      requireAuth: true
    },
    component: resolve => require(['@/components/admin/demand_company/List'], resolve)
  },
  // 需求公司详情
  {
    path: '/admin/demand_company/show/:id',
    name: 'adminDemandCompanyShow',
    meta: {
      title: '需求公司详情',
      requireAuth: true
    },
    component: resolve => require(['@/components/admin/demand_company/Show'], resolve)
  },
  // 案例列表
  {
    path: '/admin/design_case/list',
    name: 'adminDesignCaseList',
    meta: {
      title: '案例列表',
      requireAuth: true
    },
    component: resolve => require(['@/components/admin/design_case/List'], resolve)
  },
  // 用户列表
  {
    path: '/admin/user/list',
    name: 'adminUserList',
    meta: {
      title: '用户列表',
      requireAuth: true
    },
    component: resolve => require(['@/components/admin/user/List'], resolve)
  },
  // 用户编辑
  {
    path: '/admin/user/submit',
    name: 'adminUserSubmit',
    meta: {
      title: '用户编辑',
      requireAuth: true
    },
    component: resolve => require(['@/components/admin/user/Submit'], resolve)
  },
  // 分类列表
  {
    path: '/admin/category/list',
    name: 'adminCategoryList',
    meta: {
      title: '分类列表',
      requireAuth: true
    },
    component: resolve => require(['@/components/admin/category/List'], resolve)
  },
  // 编辑分类
  {
    path: '/admin/category/submit',
    name: 'adminCategorySubmit',
    meta: {
      title: '分类编辑',
      requireAuth: true
    },
    component: resolve => require(['@/components/admin/category/Submit'], resolve)
  },
  // 分类详情
  {
    path: '/admin/category/show/:id',
    name: 'adminCategoryShow',
    meta: {
      title: '分类详情',
      requireAuth: true
    },
    component: resolve => require(['@/components/admin/category/Show'], resolve)
  },
  // 文章列表
  {
    path: '/admin/article/list',
    name: 'adminArticleList',
    meta: {
      title: '文章列表',
      requireAuth: true
    },
    component: resolve => require(['@/components/admin/article/List'], resolve)
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
    component: resolve => require(['@/components/admin/article/Show'], resolve)
  },
  // 作品列表
  {
    path: '/admin/works/list',
    name: 'adminWorksList',
    meta: {
      title: '作品列表',
      requireAuth: true
    },
    component: resolve => require(['@/components/admin/works/List'], resolve)
  },
  // 作品详情
  {
    path: '/admin/works/show/:id',
    name: 'adminWorksShow',
    meta: {
      title: '作品详情',
      requireAuth: true
    },
    component: resolve => require(['@/components/admin/works/Show'], resolve)
  },
  // 日历列表
  {
    path: '/admin/awards/list',
    name: 'adminAwardsList',
    meta: {
      title: '日历列表',
      requireAuth: true
    },
    component: require ('@/components/admin/awards/List')
  },
  // 编辑日历
  {
    path: '/admin/awards/submit',
    name: 'adminAwardsSubmit',
    meta: {
      title: '日历编辑',
      requireAuth: true
    },
    component: require ('@/components/admin/awards/Submit')
  },
  // 404
  {
    path: '*',
    name: '404',
    meta: {
      title: '找不到此页面'
    },
    component: resolve => require(['components/pages/notfind/notfind'], resolve) // webpack 设置新的别名 components
  }
]

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
      store.commit(types.PREV_URL_NAME, to.name)
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
