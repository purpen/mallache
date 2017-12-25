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
      title: '灵感',
      requireAuth: false
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
  {
    path: '/v_center/match_case/submit',
    name: 'vcenterMatchCaseSubmit',
    meta: {
      requireAuth: true,
      title: '上传作品'
    },
    component: require('@/components/pages/v_center/match_case/uploadwork')
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
  // 参赛作品
  {
    path: '/vcenter/match_case',
    name: 'vcenterMatchCaseList',
    meta: {
      title: '参赛作品',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/match_case/match_case')
  },
  // 作品详情
  {
    path: '/design_case/show/:id',
    name: 'vcenterDesignCaseShow',
    meta: {
      title: '作品详情',
      requireAuth: true
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
      title: '修改密码',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/account/ModifyPwd')
  },
  // 公司工具 => 图片素材
  {
    path: '/vcenter/veer_image',
    name: 'vcenterVeerImage',
    meta: {
      title: '图片素材',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/Tools/veerImage')
  },
  // 公司工具 => 趋势/报告
  {
    path: '/vcenter/trend_report',
    name: 'vcenterTrendReport',
    meta: {
      title: '趋势/报告',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/Tools/trendReport')
  },
  {
    path: '/vcenter/trend_report/show/:id',
    name: 'trendReportShow',
    meta: {
      title: '趋势/报告',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/Tools/trendReportShow')
  },
  // 公司工具 => 常用网站
  {
    path: '/vcenter/commonly_sites',
    name: 'vcentercommonlySites',
    meta: {
      title: '常用网站',
      requireAuth: false
    },
    component: require('@/components/pages/v_center/Tools/commonlySites')
  },
  // 公司工具 => 展览
  {
    path: '/vcenter/exhibition',
    name: 'vcenterExhibition',
    meta: {
      title: '展览',
      requireAuth: true
    },
    component: (resolve) => {
      require(['@/components/pages/v_center/Tools/exhibition'], resolve)
    }
  },
  {
    path: '/vcenter/calendar',
    redirect: '/vcenter/exhibition'
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
    component: require('@/components/admin/award_case/Submit')
  },
  // 编辑奖项案例
  {
    path: '/admin/award_case/edit/:id',
    name: 'adminAwardCaseEdit',
    meta: {
      title: '编辑奖项案例',
      requireAuth: true
    },
    component: require('@/components/admin/award_case/Submit')
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
  // 404
  {
    path: '*',
    name: '404',
    meta: {
      title: '找不到此页面'
    },
    component: require('components/pages/notfind/notfind') // webpack 设置新的别名 components
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
