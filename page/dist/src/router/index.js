'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _vue = require('vue');

var _vue2 = _interopRequireDefault(_vue);

var _vueRouter = require('vue-router');

var _vueRouter2 = _interopRequireDefault(_vueRouter);

var _index = require('../store/index');

var _index2 = _interopRequireDefault(_index);

var _mutationTypes = require('../store/mutation-types');

var types = _interopRequireWildcard(_mutationTypes);

var _common = require('assets/js/common');

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) newObj[key] = obj[key]; } } newObj.default = obj; return newObj; } }

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

_vue2.default.use(_vueRouter2.default);

if (window.localStorage.getItem('token')) {
  _index2.default.commit(types.USER_SIGNIN, JSON.parse(window.localStorage.getItem('token')));
}

var routes = [{
  path: '/',
  redirect: '/home'
}, {
  path: '/home',
  name: 'home',
  meta: {
    title: '首页'
  },
  component: require('@/components/pages/home/Home')
}, {
  path: '/test',
  name: 'test',
  meta: {
    title: '首页'
  },
  component: require('@/components/pages/home/Home')
}, {
  path: '/about',
  name: 'about',
  meta: {
    title: '关于'
  },
  component: require('@/components/pages/home/About')
}, {
  path: '/server',
  name: 'server',
  meta: {
    title: '服务'
  },
  component: require('@/components/pages/home/Server')
}, {
  path: '/server_design',
  name: 'serverDesign',
  meta: {
    title: '服务-设计公司'
  },
  component: require('@/components/pages/home/ServerDesign')
}, {
  path: '/stuff',
  name: 'stuff',
  meta: {
    title: '灵感'
  },
  component: require('@/components/pages/home/Stuff')
}, {
  path: '/contact',
  name: 'contact',
  meta: {
    title: '联系我们',
    requireAuth: false
  },
  component: require('@/components/pages/home/Contact')
}, {
  path: '/item',
  name: 'item',
  meta: {
    title: '服务条款',
    requireAuth: false
  },
  component: require('@/components/pages/home/Item')
}, {
  path: '/question',
  name: 'question',
  meta: {
    title: '常见问题',
    requireAuth: false
  },
  component: require('@/components/pages/home/Question')
}, {
  path: '/trade',
  name: 'trade',
  meta: {
    title: '交易保障',
    requireAuth: false
  },
  component: require('@/components/pages/home/Trade')
}, {
  path: '/apply',
  name: 'apply',
  meta: {
    title: '申请入驻',
    requireAuth: true
  },
  component: require('@/components/pages/home/Apply')
}, {
  path: '/subject/zj',
  name: 'subject_zj',
  meta: {
    requireAuth: false,
    title: '浙江“传统产业设计再造”对接会'
  },
  component: require('@/components/pages/subject/Zj')
}, {
  path: '/subject/zj_lz',
  name: 'subject_zj_lz',
  meta: {
    requireAuth: false,
    title: '浙江“传统产业设计再造”对接会'
  },
  component: require('@/components/pages/subject/ZjLz')
}, {
  path: '/subject/zj_yk',
  name: 'subject_zj_yk',
  meta: {
    requireAuth: false,
    title: '浙江“传统产业设计再造”对接会'
  },
  component: require('@/components/pages/subject/ZjYk')
}, {
  path: '/subject/rcip',
  name: 'subject_rcip',
  meta: {
    requireAuth: false,
    title: 'RCIP衍生创新峰会'
  },
  component: require('@/components/pages/subject/Rcip')
}, {
  path: '/subject/EnterpriseRecruit',
  name: 'EnterpriseRecruit',
  meta: {
    requireAuth: false,
    title: '企业招募'
  },
  component: require('@/components/pages/subject/EnterpriseRecruit')
}, {
  path: '/subject/ProductRecruit',
  name: 'ProductRecruit',
  meta: {
    requireAuth: false,
    title: '产品招募'
  },
  component: require('@/components/pages/subject/ProductRecruit')
}, {
  path: '/subject/list',
  name: 'subjectList',
  meta: {
    title: '专题列表',
    requireAuth: false
  },
  component: require('@/components/pages/subject/List')
}, {
  path: '/article/list',
  name: 'articleList',
  meta: {
    title: '文章列表',
    requireAuth: false
  },
  component: require('@/components/pages/article/List')
}, {
  path: '/article/show/:id',
  name: 'articleShow',
  meta: {
    title: '文章详情',
    requireAuth: false
  },

  component: function component(resolve) {
    require(['@/components/pages/article/Show'], resolve);
  }
}, {
  path: '/login',
  name: 'login',
  meta: {
    title: '登录'
  },
  component: require('@/components/pages/auth/Login')
}, {
  path: '/logout',
  name: 'logout',
  meta: {
    title: '登出'
  },
  component: require('@/components/pages/auth/Logout')
}, {
  path: '/identity',
  name: 'identity',
  meta: {
    title: '注册'
  },
  component: require('@/components/pages/auth/choiceIdentity')
}, {
  path: '/register',
  name: 'register',
  meta: {
    title: '注册'
  },
  component: require('@/components/pages/auth/Register')
}, {
  path: '/forget',
  name: 'forget',
  meta: {
    title: '找回密码'
  },
  component: require('@/components/pages/auth/Forget')
}, {
  path: '/item/submit_one',
  name: 'itemSubmitOne',
  meta: {
    title: '发布需求',
    requireAuth: true
  },
  component: require('@/components/pages/item/SubmitOne')
}, {
  path: '/item/submit_type/:id',
  name: 'itemSubmitTwo',
  meta: {
    title: '选择类型',
    requireAuth: true
  },
  component: require('@/components/pages/item/SubmitTwo')
}, {
  path: '/item/submit_base/:id',
  name: 'itemSubmitThree',
  meta: {
    title: '基本信息',
    requireAuth: true
  },
  component: require('@/components/pages/item/SubmitThree')
}, {
  path: '/item/submit_ui_base/:id',
  name: 'itemSubmitUIThree',
  meta: {
    title: '基本信息',
    requireAuth: true
  },
  component: require('@/components/pages/item/SubmitUIThree')
}, {
  path: '/item/submit_company/:id',
  name: 'itemSubmitFour',
  meta: {
    title: '补全公司信息',
    requireAuth: true
  },
  component: require('@/components/pages/item/SubmitFour')
}, {
  path: '/item/submit_check/:id',
  name: 'itemSubmitFive',
  meta: {
    title: '检查并发布',
    requireAuth: true
  },
  component: require('@/components/pages/item/SubmitFive')
}, {
  path: '/item/publish',
  name: 'itemPublish',
  meta: {
    title: '发布',
    requireAuth: true
  },
  component: require('@/components/pages/item/Publish')
}, {
  path: '/item/payment',
  name: 'itemPayment',
  meta: {
    title: '支付',
    requireAuth: true
  },

  component: function component(resolve) {
    require(['@/components/pages/item/Payment'], resolve);
  }
}, {
  path: '/alipay/callback',
  name: 'alipayCallback',
  meta: {
    title: '支付宝-支付结果',
    requireAuth: false
  },
  component: require('@/components/pages/pay/AlipayCallback')
}, {
  path: '/jdpay/callback',
  name: 'jdpayCallback',
  meta: {
    title: '京东-支付结果',
    requireAuth: false
  },
  component: require('@/components/pages/pay/JdCallback')
}, {
  path: '/wxpay/callback',
  name: 'wxpayCallback',
  meta: {
    title: '微信-支付结果',
    requireAuth: true
  },
  component: require('@/components/pages/pay/wxCallback')
}, {
  path: '/item/pay_fund/:item_id',
  name: 'itemPayFund',
  meta: {
    title: '支付项目资金',
    requireAuth: true
  },
  component: require('@/components/pages/item/PayFund')
}, {
  path: '/blank',
  name: 'blank',
  meta: {
    title: '',
    requireAuth: false
  },
  component: require('@/components/block/Blank')
}, {
  path: '/user/:id',
  name: 'userShow',
  meta: {
    title: '个人主页',
    requireAuth: true
  },
  component: require('@/components/pages/user/Show')
}, {
  path: '/vcenter/control',
  name: 'vcenterControl',
  meta: {
    title: '控制面板',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/common/Control')
}, {
  path: '/company/:id',
  name: 'companyShow',
  meta: {
    title: '公司主页',
    requireAuth: true
  },
  component: require('@/components/pages/company/Show')
}, {
  path: '/vcenter/company/profile',
  name: 'vcenterComputerProfile',
  meta: {
    title: '完善公司信息',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/company/Profile')
}, {
  path: '/vcenter/company/base',
  name: 'vcenterComputerBase',
  meta: {
    title: '公司基本设置',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/company/Base')
}, {
  path: '/vcenter/company/taking',
  name: 'vcenterComputerTaking',
  meta: {
    title: '接单设置',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/company/Taking')
}, {
  path: '/vcenter/company/accreditation',
  name: 'vcenterComputerAccreditation',
  meta: {
    title: '公司认证',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/company/Accreditation')
}, {
  path: '/vcenter/company/identification',
  name: 'vcenterComputerIdentification',
  meta: {
    title: '公司认证',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/company/Identification')
}, {
  path: '/vcenter/remind/list',
  name: 'vcenterRemindList',
  meta: {
    title: '项目动态',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/item/List')
}, {
  path: '/vcenter/order/list',
  name: 'vcenterOrderList',
  meta: {
    title: '订单列表',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/order/List')
}, {
  path: '/vcenter/order/show/:id',
  name: 'vcenterOrderShow',
  meta: {
    title: '订单详情',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/order/Show')
}, {
  path: '/vcenter/message/list',
  name: 'vcenterMessageList',
  meta: {
    title: '消息列表',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/message/List')
}, {
  path: '/vcenter/design_case',
  name: 'vcenterDesignCaseList',
  meta: {
    title: '作品列表',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/design_case/List')
}, {
  path: '/design_case/show/:id',
  name: 'vcenterDesignCaseShow',
  meta: {
    title: '作品详情',
    requireAuth: false
  },
  component: require('@/components/pages/design_case/Show')
}, {
  path: '/vcenter/design_case/add',
  name: 'vcenterDesignCaseAdd',
  meta: {
    title: '添加作品',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/design_case/Submit')
}, {
  path: '/vcenter/design_case/edit/:id',
  name: 'vcenterDesignCaseEdit',
  meta: {
    title: '编辑作品',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/design_case/Submit')
}, {
  path: '/vcenter/item/list',
  name: 'vcenterItemList',
  meta: {
    title: '项目列表',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/item/List')
}, {
  path: '/vcenter/item/show/:id',
  name: 'vcenterItemShow',
  meta: {
    title: '项目详情',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/item/Show')
}, {
  path: '/vcenter/citem/show/:id',
  name: 'vcenterCItemShow',
  meta: {
    title: '项目详情',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/c_item/Show')
}, {
  path: '/vcenter/citem/list',
  name: 'vcenterCItemList',
  meta: {
    title: '项目列表',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/c_item/List')
}, {
  path: '/vcenter/citem/true_list',
  name: 'vcenterTrueCItemList',
  meta: {
    title: '项目列表',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/c_item/TrueList')
}, {
  path: '/vcenter/contract/show/:unique_id',
  name: 'vcenterContractView',
  meta: {
    title: '合同预览',
    requireAuth: true
  },

  component: function component(resolve) {
    require(['@/components/pages/v_center/contract/View'], resolve);
  }
}, {
  path: '/vcenter/contract/submit/:item_id',
  name: 'vcenterContractSubmit',
  meta: {
    title: '在线合同编辑',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/contract/Submit')
}, {
  path: '/vcenter/contract/download/:unique_id',
  name: 'vcenterContractDown',
  meta: {
    title: '合同下载',
    requireAuth: true
  },

  component: function component(resolve) {
    require(['@/components/pages/v_center/contract/Down'], resolve);
  }
}, {
  path: '/vcenter/profile',
  name: 'vcenterProfile',
  meta: {
    title: '设置',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/company/Profile')
}, {
  path: '/remind',
  name: 'remind',
  meta: {
    title: '提醒',
    requireAuth: true
  },
  component: require('@/components/pages/home/Apply')
}, {
  path: '/vcenter/stage/add/:item_id',
  name: 'vcenterDesignStageAdd',
  meta: {
    title: '添加阶段',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/stage/Submit')
}, {
  path: '/vcenter/stage/edit/:id',
  name: 'vcenterDesignStageEdit',
  meta: {
    title: '编辑阶段',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/stage/Submit')
}, {
  path: '/vcenter/stage/show/:id',
  name: 'vcenterDesignStageShow',
  meta: {
    title: '预览阶段',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/stage/Show')
}, {
  path: '/vcenter/d_company/base',
  name: 'vcenterDComputerBase',
  meta: {
    title: '公司基本设置',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/d_company/Base')
}, {
  path: '/vcenter/d_company/accreditation',
  name: 'vcenterDCompanyAccreditation',
  meta: {
    title: '公司认证',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/d_company/Accreditation')
}, {
  path: '/vcenter/d_company/identification',
  name: 'vcenterDCompanyIdentification',
  meta: {
    title: '公司认证',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/d_company/Identification')
}, {
  path: '/vcenter/wallet/list',
  name: 'vcenterWalletList',
  meta: {
    title: '我的钱包',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/wallet/List')
}, {
  path: '/vcenter/bank/list',
  name: 'vcenterBankList',
  meta: {
    title: '我的银行卡',
    requireAuth: true
  },
  component: require('@/components/pages/v_center/bank/List')
}, {
  path: '/vcenter/modify_pwd',
  name: 'modifyPwd',
  meta: {
    title: '修改密码'
  },
  component: require('@/components/pages/v_center/account/ModifyPwd')
}, {
  path: '/admin',
  name: 'adminDashBoard',
  meta: {
    title: '控制台',
    requireAuth: true
  },
  component: require('@/components/admin/DashBoard')
}, {
  path: '/admin/column/add',
  name: 'adminColumnAdd',
  meta: {
    title: '添加栏目',
    requireAuth: true
  },
  component: require('@/components/admin/column/Submit')
}, {
  path: '/admin/column/edit/:id',
  name: 'adminColumnEdit',
  meta: {
    title: '编辑栏目',
    requireAuth: true
  },
  component: require('@/components/admin/column/Submit')
}, {
  path: '/admin/column/list',
  name: 'adminColumnList',
  meta: {
    title: '栏目列表',
    requireAuth: true
  },
  component: require('@/components/admin/column/List')
}, {
  path: '/admin/item/list',
  name: 'adminItemList',
  meta: {
    title: '项目列表',
    requireAuth: true
  },
  component: require('@/components/admin/item/List')
}, {
  path: '/admin/item/show/:id',
  name: 'adminItemShow',
  meta: {
    title: '项目详情',
    requireAuth: true
  },
  component: require('@/components/admin/item/Show')
}, {
  path: '/admin/item/match/:id',
  name: 'adminItemMatch',
  meta: {
    title: '项目匹配',
    requireAuth: true
  },
  component: require('@/components/admin/item/Match')
}, {
  path: '/admin/order/list',
  name: 'adminOrderList',
  meta: {
    title: '订单列表',
    requireAuth: true
  },
  component: require('@/components/admin/order/List')
}, {
  path: '/admin/with_draw/list',
  name: 'adminWithDrawList',
  meta: {
    title: '提现列表',
    requireAuth: true
  },
  component: require('@/components/admin/with_draw/List')
}, {
  path: '/admin/company/list',
  name: 'adminCompanyList',
  meta: {
    title: '设计公司列表',
    requireAuth: true
  },
  component: require('@/components/admin/company/List')
}, {
  path: '/admin/company/show/:id',
  name: 'adminCompanyShow',
  meta: {
    title: '设计公司详情',
    requireAuth: true
  },
  component: require('@/components/admin/company/Show')
}, {
  path: '/admin/demand_company/list',
  name: 'adminDemandCompanyList',
  meta: {
    title: '需求公司列表',
    requireAuth: true
  },
  component: require('@/components/admin/demand_company/List')
}, {
  path: '/admin/demand_company/show/:id',
  name: 'adminDemandCompanyShow',
  meta: {
    title: '需求公司详情',
    requireAuth: true
  },
  component: require('@/components/admin/demand_company/Show')
}, {
  path: '/admin/design_case/list',
  name: 'adminDesignCaseList',
  meta: {
    title: '案例列表',
    requireAuth: true
  },
  component: require('@/components/admin/design_case/List')
}, {
  path: '/admin/user/list',
  name: 'adminUserList',
  meta: {
    title: '用户列表',
    requireAuth: true
  },
  component: require('@/components/admin/user/List')
}, {
  path: '/admin/category/list',
  name: 'adminCategoryList',
  meta: {
    title: '分类列表',
    requireAuth: true
  },
  component: require('@/components/admin/category/List')
}, {
  path: '/admin/category/submit',
  name: 'adminCategorySubmit',
  meta: {
    title: '分类编辑',
    requireAuth: true
  },
  component: require('@/components/admin/category/Submit')
}, {
  path: '/admin/category/show/:id',
  name: 'adminCategoryShow',
  meta: {
    title: '分类详情',
    requireAuth: true
  },
  component: require('@/components/admin/category/Show')
}, {
  path: '/admin/article/list',
  name: 'adminArticleList',
  meta: {
    title: '文章列表',
    requireAuth: true
  },
  component: require('@/components/admin/article/List')
}, {
  path: '/admin/article/submit',
  name: 'adminArticleSubmit',
  meta: {
    title: '文章编辑',
    requireAuth: true
  },

  component: function component(resolve) {
    require(['@/components/admin/article/Submit'], resolve);
  }
}, {
  path: '/admin/article/show/:id',
  name: 'adminArticleShow',
  meta: {
    title: '文章详情',
    requireAuth: true
  },
  component: require('@/components/admin/category/Show')
}, {
  path: '*',
  name: '404',
  meta: {
    title: '找不到此页面'
  },
  component: require('components/pages/notfind/notfind') }];

var router = new _vueRouter2.default({
  mode: 'history',
  linkActiveClass: 'is-active',
  routes: routes
});

router.beforeEach(function (to, from, next) {
  if (to.meta.title) {
    if (to.meta.title === '首页') {
      document.title = '铟果-中国领先的产品创新SaaS平台';
    } else {
      document.title = to.meta.title + '-铟果-中国领先的产品创新SaaS平台';
    }
  } else {
    document.title = '铟果-中国领先的产品创新SaaS平台';
  }
  if (to.matched.some(function (r) {
    return r.meta.requireAuth;
  })) {
    if (_index2.default.state.event.token) {
      next();
    } else {
      _index2.default.commit(types.PREV_URL_NAME, to.name);
      next({
        name: 'login'
      });
    }
  } else {
    next();
  }
});

router.afterEach(function (to, from) {
  (0, _common.calcImgSize)();
});

exports.default = router;
//# sourceMappingURL=index.js.map