<template>
  <div id="header-layout">
    <div class="container">
      <div class="nav-header">
        <el-menu class="el-menu-header nav-left" :default-active="menuactive" mode="horizontal" router>
            <!--<img src="../../assets/images/logo.png" width="120" alt="太火鸟">-->
            <li class="el-menu-item logo"><span class="logo">太火鸟&nbsp;SaaS</span></li>
          <el-menu-item index="home" v-bind:route="menu.home">首页</el-menu-item>
          <el-menu-item index="server" v-bind:route="menu.server">服务</el-menu-item>
          <el-menu-item index="topic" v-bind:route="menu.topic">铟果说</el-menu-item>
          <el-menu-item index="stuff" v-bind:route="menu.stuff">灵感</el-menu-item>
        </el-menu>
        <div class="">
          <div class="nav-right nav-menu" v-if="isLogin">
            <div class="server-in-btn">
              <el-button size="small" class="is-custom" @click="toServer">设计服务商入驻</el-button>
            </div>
            <router-link :to="{name: 'vcenterMessageList'}" class="nav-item is-hidden-mobile">
              <span class="icon active">
                <i class="fa fa-bell-o" aria-hidden="true"><span v-if="messageCount > 0">{{ messageCount }}</span></i>
              </span>
            </router-link>
            <el-menu class="el-menu-info" mode="horizontal" router>
              <el-submenu index="2">
                <template slot="title">
                  <img class="avatar" v-if="eventUser.logo_url" :src="eventUser.logo_url" />
                  <img class="avatar" v-else src="../../assets/images/avatar_100.png" />
                  <span class="b-nickname">{{ eventUser.account }}</span>
                </template>
                <el-menu-item index="/vcenter/control">个人中心</el-menu-item>
                <el-menu-item index="/admin" v-show="isAdmin > 0 ? true : false">后台管理</el-menu-item>
                <el-menu-item index=" " @click="logout">安全退出</el-menu-item>
              </el-submenu>
            </el-menu>         
          </div>

          <div class="nav-right" v-else>
            <div class="server-in-btn">
              <el-button size="small" class="is-custom" @click="toServer">设计服务商入驻</el-button>
            </div>
            <el-menu class="el-menu-header" :default-active="menuactive" mode="horizontal" router>
              <el-menu-item index="login" v-bind:route="menu.login">登录</el-menu-item>
              <el-menu-item index="register" v-bind:route="menu.register">注册</el-menu-item>
            </el-menu>
          </div>
        </div>

      </div>
    </div>
    <div class="header-buttom-line"></div>
  </div>
</template>

<script>
import auth from '@/helper/auth'
import api from '@/api/api'
import { MSG_COUNT } from '@/store/mutation-types'
export default {
  name: 'header',
  data () {
    return {
      // menuactive: this.$route.path.split('/')[1],
      requestMessageTask: null,
      menu: {
        home: { path: '/home' },
        server: { path: '/server' },
        topic: { path: '/article/list' },
        stuff: { path: '/stuff' },
        apply: { path: '/apply' },
        login: { path: '/login' },
        register: { path: '/register' }
      }
    }
  },
  watch: {
    '$route' (to, from) {
      // 对路由变化作出响应...
      // this.navdefact()
    }
  },
  methods: {
    navdefact() {
      // 设置router函数跳转
      this.menuactive = this.$route.path.split('/')[1]
    },
    logout() {
      auth.logout()
      this.isLogin = false
      this.$message({
        showClose: true,
        message: '登出成功!',
        type: 'success'
      })
      clearInterval(this.requestMessageTask)
      this.$router.replace('/home')
    },
    toServer() {
      this.$router.push({name: 'serverDesign'})
    },
    // 请求消息数量
    fetchMessageCount() {
      const self = this
      this.$http.get(api.messageGetMessageQuantity, {})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          var messageCount = parseInt(response.data.data.quantity)
          // 写入localStorage
          self.$store.commit(MSG_COUNT, messageCount)
        }
      })
    },
    // 定时加载消息数量
    timeLoadMessage() {
      const self = this
      // 定时请求消息数量
      self.requestMessageTask = setInterval(function() {
        self.fetchMessageCount()
      }, 30000)
    }
  },
  computed: {
    isLogin() {
      return this.$store.state.event.token
    },
    eventUser() {
      var user = this.$store.state.event.user
      if (user.avatar) {
        user.logo_url = user.avatar.logo
      } else {
        user.logo_url = null
      }
      return user
    },
    isAdmin() {
      return this.$store.state.event.user.role_id
    },
    menuactive() {
      let menu = this.$route.path.split('/')[1]
      if (menu === 'article') {
        return 'topic'
      }
      return menu
    },
    messageCount() {
      return this.$store.state.event.msgCount
    }
  },
  created: function() {
    const self = this
    if (self.isLogin) {
      self.fetchMessageCount()
      self.timeLoadMessage()
    }
  },
  destroyed() {
    clearInterval(this.requestMessageTask)
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .server-in-btn {
    height: 60px;
    line-height: 60px;
    margin-right: 20px;
  }

</style>

