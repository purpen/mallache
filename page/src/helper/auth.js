import store from '@/store/index'
// import axios from '../http'
// import api from '@/api/api'
import { USER_SIGNIN, USER_SIGNOUT, USER_INFO, CLEAR_PREV_URL_NAME } from '@/store/mutation-types'

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
    avatar: user.logo_image,
    type: user.type,
    design_company_id: user.design_company_id,
    role_id: user.role_id,
    status: user.status
  }
  // 写入localStorage
  store.commit(USER_INFO, userInfo)
}

mallache.logout = function () {
  store.commit(USER_SIGNOUT)
}

mallache.clear_prev_url_name = function () {
  store.commit(CLEAR_PREV_URL_NAME)
}

export default mallache
