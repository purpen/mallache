// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import store from './store/index'
import axios from './http'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-default/index.css'

// 样式表导入
import './assets/css/reset.css'
// import './assets/css/bulma.css'
import './assets/css/font-awesome.min.css'
import 'vuex-toast/dist/vuex-toast.css'
import './assets/css/base.css'
import './assets/css/admin.css'

Vue.use(ElementUI)

Vue.config.productionTip = false

// 将axios挂载到prototype上，在组件中可以直接使用this.http访问
Vue.prototype.$http = axios

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  axios,
  store,
  template: '<App/>',
  components: { App }
})
