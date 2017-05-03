import { USER_SIGNIN, USER_SIGNOUT } from './mutation-types.js'

export default {
  actions: {
    [USER_SIGNIN]({commit}, token) {
      commit(USER_SIGNIN, token)
    },
    [USER_SIGNOUT]({commit}) {
      commit(USER_SIGNOUT)
    }
  }
}
