/**
 ** ###### 个人中心管理 ##########
 */
module.exports = [
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
      requireAuth: false
    },
    component: require('@/components/pages/v_center/tools/veerImage')
  },
  // 公司工具 => 趋势/报告
  {
    path: '/vcenter/trend_report',
    name: 'vcenterTrendReport',
    meta: {
      title: '趋势/报告',
      requireAuth: false
    },
    component: require('@/components/pages/v_center/tools/trendReport')
  },
  {
    path: '/vcenter/trend_report/show/:id',
    name: 'trendReportShow',
    meta: {
      title: '趋势/报告',
      requireAuth: false
    },
    component: (resolve) => {
      require(['@/components/pages/v_center/tools/trendReportShow'], resolve)
    }
  },
  // 公司工具 => 常用网站
  {
    path: '/vcenter/commonly_sites',
    name: 'vcentercommonlySites',
    meta: {
      title: '常用网站',
      requireAuth: false
    },
    component: require('@/components/pages/v_center/tools/commonlySites')
  },
  // 公司工具 => 展览
  {
    path: '/vcenter/exhibition',
    name: 'vcenterExhibition',
    meta: {
      title: '设计日历',
      requireAuth: false
    },
    component: (resolve) => {
      require(['@/components/pages/v_center/tools/exhibition'], resolve)
    }
  },
  {
    path: '/vcenter/calendar',
    redirect: '/vcenter/exhibition'
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
    path: '/vcenter/message',
    name: 'vcenterMessageList',
    meta: {
      title: '消息列表',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/message/List')
  },
  // 系统通知
  {
    path: '/vcenter/notice',
    name: 'systemMessageList',
    meta: {
      title: '系统通知',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/message/SystemMessageList')
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
  // 提交作品
  {
    path: '/vcenter/match_case',
    name: 'vcenterMatchCaseList',
    meta: {
      title: '提交作品',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/match_case/MatchCase')
  },
  {
    path: '/v_center/match_case/submit',
    name: 'vcenterMatchCaseSubmit',
    meta: {
      requireAuth: true,
      title: '上传作品'
    },
    component: require('@/components/pages/v_center/match_case/UploadWork')
  }
]
