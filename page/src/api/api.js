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
  register: '/api/auth/register', // 注册
  check_account: '/auth/phoneState/',  // 验证手机号是否存在
  fetch_msm_code: '/auth/sms', // 获取手机验证码
  user: '/auth/user',  // 获取用户信息

  designCompany: '/designCompany', // 设计公司基本资料

  demandAlipay: '/pay/demandAliPay',  // 支付保证金

  saveDesignItem: '/designItem', // 接单信息保存
  designItems: '/designItem', // 查看接单列表
  designItem: '/designItem/{0}', // 查看/编辑/删除接单信息

  designCase: '/designCase',  // 公司案例
  designCaseId: '/designCase/{0}',  // 公司案例

  itemList: '/demand/itemList',  // 客户项目列表

  demand: '/demand',  // 添加项目领域
  demandId: '/api/demand/{0}',  // 更改项目领域

  ProductDesignId: '/ProductDesign/{0}', // 更改产品设计基本资料
  UDesignId: '/UDesign/{0}', // 更改UI设计基本资料

  release: '/demand/release', // 发布项目

  asset: '/upload/deleteFile/{0}',  // 删除／查看图片
  upToken: '/upload/upToken'  // 获取上传token

}
