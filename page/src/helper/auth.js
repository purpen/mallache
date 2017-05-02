import store from '@/store/index'
// import axios from '../http'
// import api from '@/api/api'
import { USER_SIGNIN, USER_SIGNOUT, USER_INFO } from '@/store/mutation-types'

var mallache = {}
mallache.write_token = function (token) {
  // 写入localStorage
  store.commit(USER_SIGNIN, token)
}

mallache.write_user = function (user) {
  var userInfo = {
    id: user.id,
    account: user.account,
    email: user.email,
    phone: user.phone,
    avatar_url: user.img,
    type: user.type,
    status: user.status
  }
  // 写入localStorage
  store.commit(USER_INFO, userInfo)
}

mallache.logout = function () {
  store.commit(USER_SIGNOUT)
}

export default mallache