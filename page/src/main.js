// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import store from './store/index'
import axios from './http'
import ElementUI from 'element-ui'
import phenix from '@/assets/js/base'
import VueLazyload from 'vue-lazyload'
// import ECharts from 'vue-echarts/components/ECharts'

// 兼容 IE
import 'babel-polyfill'

// 样式表导入
import 'element-ui/lib/theme-default/index.css'
import 'swiper/dist/css/swiper.css'
import './assets/css/reset.css'
import './assets/css/font-awesome.min.css'
import './assets/css/base.css'
import './assets/css/fineix.css'
import './assets/css/admin.css'
import 'fullcalendar/dist/fullcalendar.css'

Vue.use(ElementUI)
// 图片懒加载
Vue.use(VueLazyload, {
  loading: require('assets/images/Bitmap.png')
})

Vue.config.productionTip = false

// 将axios挂载到prototype上，在组件中可以直接使用this.http访问
Vue.prototype.$http = axios

// js自定义方法集
Vue.prototype.$phenix = phenix

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: {
    App
  }
})
