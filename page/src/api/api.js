/**
 import axios from 'axios';

 // 使用代理
 // const HOST = process.env.API_ROOT;
 const HOST = '/api/';
 export function fetch(url) {
  return new Promise((resolve, reject) => {
    axios.get(HOST + url)
      .then(response => {
        resolve(response.data);
      })
  })
}
 **/

export default {
  login: '/auth/login', // 登录
  logout: '/auth/logout', // 退出登录
  register: '/auth/register', // 注册
  forget: '/auth/forgetPassword', // POST 找回密码
  modifyPwd: '/auth/changePassword', // POST 修改密码
  check_account: '/auth/phoneState/{0}',  // 验证手机号是否存在
  fetch_msm_code: '/auth/sms', // 获取手机验证码
  user: '/auth/user',  // 获取用户信息
  authFundInfo: '/auth/fundInfo', // GET 用户钱包信息
  fundLogList: '/fundLogList', // GET 交易记录
  withdrawCreate: '/withdraw/create', // POST 提现
  withdrawList: '/withdraw/lists', // GET 用户提现列表

  // 栏目
  columnList: '/column/lists', // GET 列表
  column: '/column', // GET 详情

  // 银行卡
  bank: '/bank', // GET/POST 查看列表 | 保存银行卡
  bankId: '/bank/{0}', // PUT 更新银行卡信息
  bankUnStatus: '/bank/un/status', // PUT 关闭银行卡

  // 支付
  demandAlipay: '/pay/demandAliPay',  // GET 支付保证金-支付宝
  demandJdPay: '/pay/demandJdPay',  // GET 支付保证金-京东
  demandWxPay: '/pay/demandWxPay',  // GET 支付保证金-微信
  endPayOrderItemId: '/pay/endPayOrder/{0}', // GET 创建尾款支付订单
  secondAlipayId: '/pay/itemAliPay/{0}',  // GET 支付尾款-支付宝
  payItemBankPayId: '/pay/itemBankPay/{0}',  // GET 支付项目尾款--公对公银行转账

  // 项目需求
  itemList: '/demand/itemList',  // 客户项目列表
  demand: '/demand',  // 添加项目领域
  demandId: '/demand/{0}',  // 更改项目领域
  ProductDesignId: '/ProductDesign/{0}', // 更改产品设计基本资料
  UDesignId: '/UDesign/{0}', // 更改UI设计基本资料
  release: '/demand/release', // 发布项目
  recommendListId: '/demand/recommendList/{0}',  // 项目获取推荐的设计公司
  demandPush: '/demand/push', // 选定系统推荐的设计公司
  demandItemDesignListItemId: '/demand/itemDesignList/{0}', // 选择已报价的设计公司
  refuseDesignPrice: '/demand/falseDesign', // 拒绝设计公司报价
  agreeDesignCompany: '/demand/trueDesign', // 同意合作的设计公司
  demandItemRestart: '/demand/itemRestart', // post 修改项目，重新匹配
  demandCloseItem: '/demand/closeItem', // post 用户关闭项目
  demandTrueItemDoneId: '/demand/trueItemDone/{0}', // POST 确认项目完成
  demandMatchingCount: '/demand/matchingCount', // POST 获取已匹配公司数量
  demandEvaluate: '/demand/evaluate', // POST 评价设计公司

  // 需求方公司管理
  demandCompany: '/demandCompany', // POST 保存需求方公司信息;  GET 获取信息

  // 公司项目接口
  // 设计公司获取项目订单
  designItemList: '/design/itemList',  // 设计公司获取项目列表
  addQuotation: '/quotation', // 添写报价单
  updateQuotation: '/quotation/{0}', // PUT 更新报价单
  companyRefuseItemId: '/design/refuseItem/{0}', // get 拒绝推送的项目报价
  designCooperationLists: '/design/cooperationLists', // 已确定合作的项目列表
  designItemId: '/design/item/{0}', // get 获取项目详细信息
  sendContract: '/contract/ok', // POST 发送合同
  designItemStartId: '/design/itemStart/{0}', // POST 确认项目开始设计
  designItemDoneId: '/design/itemDone/{0}', // POST 确认项目完成
  itemStageSend: '/itemStage/ok/status', // PUT 项目阶段发送

  // 合同
  contract: '/contract', // post 保存合同
  contractId: '/contract/{0}', // put/get 更新/查看合同
  demandTrueContract: '/demand/trueContract',  // POST 需求方确认项目合同

  // 阶段查看
  itemStageDemandLists: '/itemStage/demand/lists', // GET 需求方阶段查看
  itemStageDesignCompanyLists: '/itemStage/designCompany/lists', // GET 设计公司阶段查看
  itemStageId: '/itemStage/{0}', // GET 阶段详情 PUT 编辑
  itemStage: '/itemStage',  // POST 保存
  demandFirmItemStage: '/itemStage/demandFirmItemStage', // POST 需求方确认阶段完成

  // 订单
  orderId: '/pay/getPayStatus/{0}',  // GET 查看订单详情
  orderList: '',  // GET 查看订单列表

  // 公司接单设置
  saveDesignItem: '/designItem', // 接单信息保存
  designItems: '/designItem', // 查看接单列表
  designItem: '/designItem/{0}', // 查看/编辑/删除接单信息

  // 案例
  designCase: '/designCase',  // 作品案例
  designCaseId: '/designCase/{0}',  // 作品案例
  designCaseCompanyId: '/designCase/designCompany/{0}', // GET 通过公司ID查看案例
  designCaseOpenLists: '/designCase/openLists', // GET 案例列表

  // 添加作品
  work: 'works',
  workid: 'works/{0}',

  // 设计公司
  designCompanyId: 'designCompany/otherIndex/{0}', // 根据标识查看公司详情
  designCompany: '/designCompany', // POST 保存 /PUT 更新 / GET 设计公司基本资料

  surveyDemandCompanySurvey: '/survey/demandCompanySurvey', // GET 需求方控制面板
  surveyDesignCompanySurvey: '/survey/designCompanySurvey', // GET 设计公司控制面板

  // 项目提醒
  messageGetMessageQuantity: '/message/getMessageQuantity', // GET 获取数量
  messageGetMessageList: '/message/getMessageList', // GET 列表
  messageTrueRead: '/message/trueRead', // PUT 确认已读

  // 系统通知
  getNoticeList: '/notice/list', // GET 列表
  getNotice: '/notice',  // GET 详情

  // 文章
  articleList: '/article/list', // GET 列表
  article: '/article', // GET 详情

  // 分类管理
  categoryList: '/classification/list', // GET 分类列表

  // 附件操作
  asset: '/upload/deleteFile/{0}',  // 删除 | 查看图片
  upToken: '/upload/upToken',  // 获取上传token
  updateImageSummary: '/designCase/imageSummary', // PUT 更新图片备注

  /** 后台管理 */

  // 控制台
  adminSurveyIndex: '/admin/survey/index', // GET 控制台

  // 用户管理
  adminUserLists: '/admin/user/lists', // GET 用户列表
  adminUser: '/admin/user/show', // GET 用户详情
  adminUserEdit: '/admin/user/edit', // POST 修改用户信息
  adminUserSetStatus: '/admin/user/changeStatus', // POST 修改用户状态
  adminUserSetRole: '/admin/user/changeRole', // POST 修改用户角色

  // 项目管理
  adminItemList: '/admin/item/lists', // 项目列表
  adminItemShow: '/admin/item/show', // GET 项目详情
  addItemToCompany: '/admin/item/addDesignToItem',  // 给项目推荐公司
  ConfirmItemToCompany: '/admin/item/trueItem',  // 确认项目推荐公司

  // 需求公司管理
  adminDemandCompanyList: '/admin/demandCompany/lists', // GET 需求公司列表
  adminDemandCompanyShow: '/admin/demandCompany/show', // GET 需求公司详情
  adminDemandCompanyVerifyIng: '/admin/demandCompany/unVerifyStatus', // PUT 审核中
  adminDemandCompanyVerifyNo: '/admin/demandCompany/noVerifyStatus', // PUT 未能通过
  adminDemandCompanyVerifyOk: '/admin/demandCompany/verifyStatus', // PUT 通过审核

  // 设计公司管理
  adminCompanyList: '/admin/designCompany/lists', // GET 设计公司列表
  adminCompanyShow: '/admin/designCompany/show', // GET 设计公司详情
  adminCompanyStatusOk: '/admin/designCompany/okStatus', // PUT 启用
  adminCompanyStatusDisable: '/admin/designCompany/unStatus', // PUT 禁用
  adminCompanyVerifyOk: '/admin/designCompany/verifyStatus', // PUT 通过审核
  adminCompanyVerifyCancel: '/admin/designCompany/unVerifyStatus', // PUT 取消审核 (停用)

  // 订单管理
  adminPayOrderLists: '/admin/payOrder/lists', // GET 订单列表
  adminPayOrderTruePay: '/admin/payOrder/truePay', // POST 确认订单支付款（对公转账）

  // 提现管理
  adminWithDrawLists: '/admin/withdrawOrder/lists', // GET 订单列表
  adminWithDrawTruePay: '/admin/withdrawOrder/trueWithdraw', // 确认提现单已提现

  // 案例管理
  adminDesignCaseLists: '/admin/designCase/lists', // GET 案例列表
  adminDesignCaseOpenInfo: '/admin/designCase/openInfo', // GUT 案例是否公开

  // 栏目管理
  adminColumnList: '/admin/column/lists', // GET 列表
  adminColumn: '/admin/column', // GET 详情 | POST 添加 | PUT 更新 | DELETE 删除
  adminColumnChangeStatus: '/admin/column/changeStatus', // PUT 启用 | 禁用

  // 分类管理
  adminCategoryList: '/admin/classification/list', // GET 列表
  adminCategory: '/admin/classification', // GET 详情 | POST 添加 | PUT 更新
  adminCategoryChangeStatus: '/admin/classification/changeStatus', // PUT 更新状态

  // 文章管理
  adminArticleList: '/admin/article/list', // GET 列表
  adminArticle: '/admin/article', // GET 详情 | POST 添加 | PUT 更新
  adminArticleChangeStatus: '/admin/article/verifyStatus', // PUT 更新状态

  // 大赛作品管理
  adminWorksList: '/admin/works/list', // GET 列表
  adminWorks: '/admin/works', // GET 详情 | POST 添加 | PUT 更新
  adminWorksStatus: '/admin/works/verifyStatus', // PUT 更新状态
  adminWorksPublished: '/admin/works/published', // PUT 发布
  adminWorksDelete: '/admin/works/delete', // DELETE 删除

  // 日历管理
  adminAwardsList: '/admin/dateOfAward/list', // GET 列表
  adminAwards: '/admin/dateOfAward', // GET 详情 | POST 添加 | PUT 更新 | DELETE 删除
  adminAwardsChangeStatus: '/admin/dateOfAward/changeStatus', // PUT 更新状态
  adminDateOfAwardWeek: '/admin/dateOfAward/week', // GET 周历
  adminDateOfAwardMonth: '/admin/dateOfAward/month', // GET 月历
  dateOfAwardMonth: '/dateOfAward/month', // GET 月历
  dateOfAwardWeek: '/dateOfAward/week', // GET 周历

  // 趋势/报告管理
  adminTrendReportList: '/admin/trendReports/lists',  // GET 列表
  adminTrendReport: '/admin/trendReports', // GET 详情 | POST 添加 | PUT 更新 | DELETE 删除
  adminTrendReprotSetStatus: '/admin/trendReports/verifyStatus', // PUT 更改状态
  TrendReportList: '/trendReports/lists',  // GET 列表
  trendReports: '/trendReports', // GET 详情

  // 常用网站管理
  adminCommonlySite: '/admin/commonlyUsedUrls', // GET 详情 | POST 添加 | PUT 更新 | DELETE 删除
  adminCommonlySiteList: '/admin/commonlyUsedUrls/list',  // GET 列表
  adminCommonlySiteSetStatus: '/admin/commonlyUsedUrls/verifyStatus', // PUT 更改状态
  CommonlySite: '/commonlyUsedUrls', // GET 详情
  CommonlySiteList: '/commonlyUsedUrls/list',  // GET 列表

  // 奖项案例管理
  adminAwardCaseList: '/admin/awardCase/list', // GET 列表
  adminAwardCase: '/admin/awardCase', // GET 详情 | POST 添加 | PUT 更新 | DELETE 删除
  adminAwardCaseChangeStatus: '/admin/awardCase/changeStatus', // PUT 启用 | 禁用
  adminAwardCaseChangeRecommended: '/admin/awardCase/changeRecommended', // PUT 启用 | 禁用
  awardCaseList: '/awardCase/list', // GET 列表
  awardCase: '/awardCase', // GET 详情

  // 系统通知管理
  adminNoticeList: '/admin/notice/list', // GET 列表
  adminNotice: '/admin/notice', // GET 详情 | POST 添加 | PUT 更新 | DELETE 删除
  adminNoticeSetStatus: '/admin/notice/changeStatus', // PUT 启用 | 禁用

  // 栏目位管理
  adminBlockList: '/admin/block/list', // GET 列表
  adminBlock: '/admin/block', // GET 详情 | POST 添加 | PUT 更新 | DELETE 删除
  adminBlockSetStatus: '/admin/block/changeStatus', // PUT 启用 | 禁用

  // 附件管理
  adminAssetUrlUpload: '/admin/urlUpload',  // GET 图片地址上传

  // veer图片列表
  veerImage: '/veerImage/list',
  block: '/block',

  // test
  test: '/'  // End
}
