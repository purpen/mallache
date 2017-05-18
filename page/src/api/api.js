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
  check_account: '/auth/phoneState/',  // 验证手机号是否存在
  fetch_msm_code: '/auth/sms', // 获取手机验证码
  user: '/auth/user',  // 获取用户信息

  // 支付
  demandAlipay: '/pay/demandAliPay',  // 支付保证金-支付宝
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

  // 公司项目接口
  // 设计公司获取项目订单
  designItemList: '/design/itemList',  // 设计公司获取项目列表
  addQuotation: '/quotation', // 添写报价单
  companyRefuseItemId: '/design/refuseItem/{0}', // get 拒绝推送的项目报价
  designCooperationLists: '/design/cooperationLists', // 已确定合作的项目列表
  designItemId: '/design/item/{0}', // get 获取项目详细信息
  sendContract: '/contract/ok', // POST 发送合同

  // 合同
  contract: '/contract', // post 保存合同
  contractId: '/contract/{0}', // put/get 更新/查看合同
  demandTrueContract: '/demand/trueContract',  // POST 需求方确认项目合同

  // 订单
  orderId: '/pay/getPayStatus/{0}',  // GET 查看订单详情
  orderList: '',  // GET 查看订单列表

  // 公司接单设置
  saveDesignItem: '/designItem', // 接单信息保存
  designItems: '/designItem', // 查看接单列表
  designItem: '/designItem/{0}', // 查看/编辑/删除接单信息

  // 案例
  designCase: '/designCase',  // 公司案例
  designCaseId: '/designCase/{0}',  // 公司案例

  // 公司
  designCompanyId: 'designCompany/otherIndex/{0}', // 根据标识查看公司详情
  designCompany: '/designCompany', // 设计公司基本资料

  // 附件操作
  asset: '/upload/deleteFile/{0}',  // 删除／查看图片
  upToken: '/upload/upToken',  // 获取上传token

  // 后台管理
  adminItemList: '/admin/item/lists', // 项目列表
  adminCompanyList: '/admin/designCompany/lists', // 项目列表
  addItemToCompany: '/admin/item/addDesignToItem',  // 给项目推荐公司
  ConfirmItemToCompany: '/admin/item/trueItem',  // 确认项目推荐公司

  // 公司审核
  adminCompanyStatusOk: '/admin/designCompany/okStatus', // 启用
  adminCompanyStatusDisable: '/admin/designCompany/unStatus', // 禁用
  adminCompanyVerifyOk: '/admin/designCompany/verifyStatus', // 通过审核
  adminCompanyVerifyCancel: '/admin/designCompany/unVerifyStatus', // 取消审核

  test: '/'  // End
}
