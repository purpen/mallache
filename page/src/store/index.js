import Vue from 'vue'
import Vuex from 'vuex'
import event from './mutations'
import actions from './action'
import { createModule } from 'vuex-toast'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'
Vue.config.debug = debug
Vue.config.warnExpressionErrors = false

export default new Vuex.Store({
  modules: {
    actions,
    event,
    toast: createModule({
      dismissInterval: 3000
    })
  },
  strict: debug
})
