import { USER_SIGNIN, USER_SIGNOUT, USER_INFO } from './mutation-types.js'

// 判断是否登录
var isLoggedIn = function() {
  // TODO 此处可以写异步请求，到后台一直比较Token
  var token = localStorage.getItem('token')
  if (token) {
    return JSON.parse(token)
  } else {
    return false
  }
}

var userInfo = function() {
  // TODO 用户从Store获取
  var user = localStorage.getItem('user')
  if (user) {
    return JSON.parse(user)
  } else {
    return false
  }
}

const state = {
  token: isLoggedIn() || null,
  user: userInfo() || {},
  loading: false,  // 是否显示loading
  apiUrl: 'http://sa.taihuoniao.com', // 接口base url
  imgUrl: 'http://sa.taihuoniao.com', // 图片base url
  indexConf: {
    isFooter: true, // 是否显示底部
    isSearch: true, // 是否显示搜索
    isBack: false, // 是否显示返回
    isShare: false, // 是否显示分享
    title: '' // 标题
  }
}

const mutations = {
  [USER_SIGNIN](state, token) {
    localStorage.setItem('token', null)
    localStorage.setItem('token', JSON.stringify(token))
    state.token = token
  },
  [USER_SIGNOUT](state) {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    state.token = false
  },
  [USER_INFO](state, user) {
    localStorage.setItem('user', {})
    localStorage.setItem('user', JSON.stringify(user))
    state.user = user
  }
}

export default {
  state,
  mutations
}
