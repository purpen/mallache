import { USER_SIGNIN, USER_SIGNOUT, USER_INFO, MSG_COUNT, PREV_URL_NAME, CLEAR_PREV_URL_NAME } from './mutation-types.js'

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

var prevUrlName = function() {
  var urlName = localStorage.getItem('prev_url_name')
  if (urlName) {
    return urlName
  } else {
    return null
  }
}

// 消息数量
var msgCount = function() {
  var messageCount = localStorage.getItem('msgCount')
  if (messageCount) {
    return messageCount
  } else {
    return 0
  }
}

const state = {
  token: isLoggedIn() || null,
  user: userInfo() || {},
  loading: false,  // 是否显示loading
  apiUrl: 'http://sa.taihuoniao.com', // 接口base url
  imgUrl: 'http://sa.taihuoniao.com', // 图片base url
  prevUrlName: prevUrlName(),
  msgCount: msgCount(),
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
    localStorage.setItem('msgCount', 0)
    state.token = false
  },
  [USER_INFO](state, user) {
    localStorage.setItem('user', {})
    localStorage.setItem('user', JSON.stringify(user))
    state.user = user
  },
  [MSG_COUNT](state, msgCount) {
    if (msgCount < 0) {
      msgCount = 0
    }
    localStorage.setItem('msgCount', JSON.stringify(msgCount))
    state.msgCount = msgCount
  },
  [PREV_URL_NAME](state, urlName) {
    localStorage.setItem('prev_url_name', urlName)
    state.prevUrlName = urlName
  },
  [CLEAR_PREV_URL_NAME](state) {
    localStorage.removeItem('prev_url_name')
    state.prevUrlName = null
  }
}

export default {
  state,
  mutations
}
