// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import store from './store/index'
import axios from './http'
import VeeValidate, { Validator } from 'vee-validate'
import validateZhCn from 'vee-validate/dist/locale/zh_CN'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-default/index.css'
// 样式表导入
import './assets/css/reset.css'
// import './assets/css/bulma.css'
import './assets/css/font-awesome.min.css'
import 'vuex-toast/dist/vuex-toast.css'
import './assets/css/base.css'

// vee-validate 中文包
Validator.addLocale(validateZhCn)

const isMobile = {
  messages: {
    zh_CN: (field, args) => '手机号码格式不正确!'
  },
  validate: (value, args) => {
    return value.length === 11 && /^((13|14|15|17|18)[0-9]{1}\d{8})$/.test(value)
  }
}
Validator.extend('isMobile', isMobile)

const dictionary = {
  zh_CN: {
    messages: {
      required: () => '此处为必填项,不能为空'
    }
  }
}

Validator.updateDictionary(dictionary)

// validate 配置
const validateConfig = {
  errorBagName: 'errors', // change if property conflicts.
  delay: 0, // 代表输入多少ms之后进行校验
  locale: 'zh_CN',
  messages: null,
  strict: true  // 没有设置规则的表单不进行校验
}
Vue.use(VeeValidate, validateConfig)
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
