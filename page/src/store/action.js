import { USER_SIGNIN, USER_SIGNOUT, MSG_COUNT, PREV_URL_NAME, CLEAR_PREV_URL_NAME } from './mutation-types.js'

export default {
  actions: {
    [USER_SIGNIN]({commit}, token) {
      commit(USER_SIGNIN, token)
    },
    [USER_SIGNOUT]({commit}) {
      commit(USER_SIGNOUT)
    },
    [MSG_COUNT]({commit}, msgCount) {
      commit(MSG_COUNT, parseInt(msgCount))
    },
    [PREV_URL_NAME]({commit}, urlName) {
      commit(PREV_URL_NAME, urlName)
    },
    [CLEAR_PREV_URL_NAME]({commit}) {
      commit(CLEAR_PREV_URL_NAME)
    }
  }
}
