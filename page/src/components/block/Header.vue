<template>
  <div id="header-layout">
    <div class="container">
      <div class="nav-header">
        <el-menu class="el-menu-header nav-left" :default-active="menuactive" mode="horizontal" router>
            <!--<img src="../../assets/images/d3ingo_logo_2x.png" width="120" alt="太火鸟">-->
            <li class="el-menu-item logo"><span class="logo">太火鸟&nbsp;SaaS</span></li>
          <el-menu-item index="home" v-bind:route="menu.home">首页</el-menu-item>
          <el-menu-item index="server" v-bind:route="menu.server">服务</el-menu-item>
          <el-menu-item index="stuff" v-bind:route="menu.stuff">灵感</el-menu-item>
        </el-menu>
        <div class="nav-right nav-menu" v-if="isLogin">
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
        <el-menu class="el-menu-header nav-right" :default-active="menuactive" mode="horizontal" router v-else>
          <el-menu-item index="login" v-bind:route="menu.login">登录</el-menu-item>
          <el-menu-item index="register" v-bind:route="menu.register">注册</el-menu-item>
        </el-menu>
      </div>
    </div>
    <div class="buttom-line"></div>
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
      }, 15000)
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
      if (menu === 'server_design') {
        return 'server'
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
  .nav-header{
    align-items: stretch;
    display: flex;
    background: #fff;
    padding-left: 5px;
  }
  .el-menu--horizontal>.el-menu-item.logo{
    width: 110px;
    height: 60px;
    transition:none;
    padding: 0 10px;
    margin-right: 30px;

    background-image: url(../../assets/images/d3ingo_logo_2x.png);
    background-repeat: no-repeat;
    background-position: 0 15px;
    background-size: 110px 30px;
    text-indent: -9999px;
  }
  .el-menu--horizontal>.el-menu-item.logo:hover,.el-menu--horizontal>.el-menu-item.logo.is-active{
    background: none;
    border: none;
    transition:none;
    background-image: url(../../assets/images/d3ingo_logo_2x.png);
    background-repeat: no-repeat;
    background-position: 0 15px;
    background-size: 110px 30px;
    text-indent: -9999px;
  }
  .el-menu-header{
    background: #fff;
  }
  .el-menu-header .el-menu-item,.el-menu-header .el-submenu{
    height: 60px;
    line-height: 60px;
    border-bottom: 3px solid transparent;
    color: #7a7a7a;
    font-size: 1.5rem;
    padding: 0 3px;
    margin: 0 22px;
  }
  .el-menu--horizontal.el-menu-header .el-submenu .el-submenu__title{
    height: 52px;
    line-height: 52px;
    border-bottom: 3px solid transparent;
  }
  .el-menu--horizontal>.el-menu-item:hover, .el-menu--horizontal>.el-submenu.is-active .el-submenu__title, .el-menu--horizontal>.el-submenu:hover .el-submenu__title,.el-menu--horizontal>.el-menu-item.is-active{
    border-bottom: 3px solid #FF5A5F;
    color: #FF5A5F;
    background: none;
  }
  .el-menu-item, .el-submenu__title{
    padding: 0 14px;
  }
  a.nav-item.is-tab.is-active {
    color: #222;
    border-bottom-color: #222;
  }
  a.nav-item.is-tab:hover {
    border-bottom-color: #222; 
  }
  .nav-left {
    display: flex;
    justify-content: flex-start;
    overflow: hidden;
    overflow-x: auto;
    white-space: nowrap;
  }
  .nav-left, .nav-right {
    align-items: stretch;
    flex-basis: 0;
    flex-grow: 1;
    flex-shrink: 0;
  }
  .nav-right{
    display: flex;
    -webkit-box-pack: end;
    -ms-flex-pack: end;
    justify-content: flex-end;
  }
  .nav-right .el-menu-item {
    margin: 0 10px;
  }
  .nav-item {
    align-items: center;
    display: flex;
    flex-grow: 0;
    flex-shrink: 0;
    font-size: 1rem;
    justify-content: center;
    line-height: 1.5;
    padding: 0.5rem 0.75rem;
  }
  .nav-item .icon {
    align-items: center;
    display: inline-flex;
    justify-content: center;
    height: 1.5rem;
    vertical-align: top;
    width: 1.5rem;
    position:relative;
  }
  .icon .fa {
    font-size: 21px;
  }
  .icon.active span {
  }
  .icon.active span {
    width: 18px;
    min-height: 18px;
    border: 1px solid red;/*设置红色*/
    border-radius:50%;/*设置圆角*/
    overflow: hidden;
    background-color: red;
    position: absolute;
    z-index: 1000;
    right: 0%;
    margin-right: -10px;
    margin-top: -10px;
    color: #fff;
    font-size: 10px;
    line-height: 18px;
  }
  .fa {
    display: inline-block;
    font: normal normal normal 14px/1 FontAwesome;
    font-size: inherit;
    text-rendering: auto;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    font-size: 21px;
    text-align: center;
    vertical-align: top;
  }
  .nav-right a {
    cursor: pointer;
    text-decoration: none;
    -webkit-transition: none 86ms ease-out;
    transition: none 86ms ease-out;
    margin: 0;
    border: 0;
    font-size: 100%;
    font: inherit;
    vertical-align: baseline;
  }
  .nav-item a:hover, a.nav-item:hover {
    color: #363636;
  }

  .buttom-line {
    box-shadow: 0 2px 3px rgba(10, 10, 10, 0.1);
    border-top: 3px solid transparent;
    margin-top: -3px;
  }
</style>

