<template>
  <div id="header-layout">
    <div class="nav-header">
      <el-menu class="el-menu-header nav-left" :default-active="menuactive" mode="horizontal" router>
          <!--<img src="../../assets/images/logo.png" width="120" alt="太火鸟">-->
          <li class="el-menu-item logo"><span class="logo">太火鸟&nbsp;SaaS</span></li>
        <el-menu-item index="home" v-bind:route="menu.home">首页</el-menu-item>
        <el-menu-item index="server" v-bind:route="menu.server">服务</el-menu-item>
        <el-menu-item index="stuff" v-bind:route="menu.stuff">灵感</el-menu-item>
        <el-menu-item index="apply" v-bind:route="menu.apply">申请入驻</el-menu-item>
      </el-menu>
      <div class="nav-right nav-menu" v-if="isLogin">
        <router-link :to="{name: 'remind'}" class="nav-item is-hidden-mobile">
          <span class="icon">
            <i class="fa fa-bell-o" aria-hidden="true"></i>
          </span>
        </router-link>
        <el-menu class="el-menu-info" mode="horizontal" router>
          <el-submenu index="2">
            <template slot="title">
              <img class="avatar" src="../../assets/images/avatar_default.jpg" />
              <span class="b-nickname">{{ eventUser.account }}</span>
            </template>
            <el-menu-item index="/vcenter/item/list?type=1">个人中心</el-menu-item>
            <el-menu-item index="/admin">后台管理</el-menu-item>
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
</template>

<script>
import auth from '@/helper/auth'
export default {
  name: 'header',
  data () {
    return {
      menuactive: this.$route.path.split('/')[1],
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
    '$route': 'navdefact'
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
      this.$router.push('/home')
    }
  },
  computed: {
    isLogin() {
      return this.$store.state.event.token
    },
    eventUser() {
      return this.$store.state.event.user
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .nav-header{
    align-items: stretch;
    display: flex;
    padding: 0 100px;
    background: #fff;
    box-shadow: 0 2px 3px rgba(10, 10, 10, 0.1);
    margin-bottom: 30px;
  }
  .el-menu--horizontal>.el-menu-item.logo{
    width: 54px;
    height: 50px;
    transition:none;
    padding: 0 10px;
    margin-right: 30px;

    background-image: url(../../assets/images/logo.png);
    background-repeat: no-repeat;
    background-position: 0 4px;
    background-size: 54px 42px;
    text-indent: -9999px;
  }
  .el-menu--horizontal>.el-menu-item.logo:hover,.el-menu--horizontal>.el-menu-item.logo.is-active{
    background: none;
    border: none;
    transition:none;
    background-image: url(../../assets/images/logo.png);
    background-repeat: no-repeat;
    background-position: 0 4px;
    background-size: 54px 42px;
    text-indent: -9999px;
  }
  .el-menu-header{
    background: #fff;
  }
  .el-menu-header .el-menu-item,.el-menu-header .el-submenu{
    height: 52px;
    line-height: 52px;
    border-bottom: 3px solid transparent;
    color: #7a7a7a;
    font-size: 1.5rem;
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
  }
  .icon .fa {
    font-size: 21px;
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
</style>

