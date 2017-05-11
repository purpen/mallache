/**
 * Created by superman on 17/2/16.
 * http配置
 */

import axios from 'axios'
import store from './store/index'
import * as types from './store/mutation-types'
import router from './router'

// npm install axios的时候默认会安装qs
// qs相关的问题请搜索"nodejs qs"或者看这里https://www.npmjs.com/package/qs
import Qs from 'qs'
const axiosInstance = axios.create({
  baseURL: process.env.API_ROOT,
  timeout: 20000,
  withCredentials: true,
  headers: {
    'Content-Type': 'application/x-www-form-urlencoded',
    // 'Access-Control-Allow-Origin': 'http://mc.taihuoniao.com',
    // 'Access-Control-Allow-Credentials': 'true',
    // 'Access-Control-Allow-Headers': 'Origin, X-Requested-With, Content-Type, Accept',
    'X-Requested-With': 'XMLHttpRequest'
  },
  transformRequest: [function (data) {
    data = Qs.stringify(data)
    return data
  }]

})

// http request 拦截器
axiosInstance.interceptors.request.use(
  config => {
    if (store.state.event.token) {
      config.headers.Authorization = `Bearer ${store.state.event.token}`
    }
    return config
  },
  err => {
    return Promise.reject(err)
  })

// http response 拦截器
axiosInstance.interceptors.response.use(
  response => {
    return response
  },
  error => {
    if (error.response) {
      switch (error.response.status) {
        case 401:
          // 401 清除token信息并跳转到登录页面
          store.commit(types.USER_SIGNOUT)
          router.replace({
            path: '/login',
            query: {redirect: router.currentRoute.fullPath}
          })
      }
    }
    // console.log(JSON.stringify(error));//console : Error: Request failed with status code 402
    return Promise.reject(error.response.data)
  })

export default axiosInstance
