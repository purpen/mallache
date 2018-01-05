<template>
  <div id="header-layout">
    <div class="container">
      <div class="nav-header" v-if="!this.isMob">
        <hgroup>
          <el-menu class="el-menu-header nav-left" :default-active="menuactive" mode="horizontal" router>
            <!--<img src="../../assets/images/logo.png" width="120" alt="太火鸟">-->
            <div class="el-menu-item logo">
              <span class="logo">太火鸟 SaaS</span>
            </div>
            <el-menu-item index="home" :route="menu.home">首页</el-menu-item>
            <el-menu-item index="server" :route="menu.server">服务</el-menu-item>
            <el-menu-item index="topic" :route="menu.topic">铟果说</el-menu-item>
            <el-menu-item index="design_case" :route="menu.design_case">灵感</el-menu-item>
          </el-menu>
        </hgroup>
        <div class="nav-right nav-menu" v-if="isLogin">
          <div class="server-in-btn">
            <el-button size="small" class="is-custom" @click="toServer">设计服务商入驻</el-button>
          </div>
          <a class="nav-item is-hidden-mobile" @click="viewMsg" ref="msgList">
              <span class="icon active">
                <i class="fa fa-bell-o" aria-hidden="true">
                  <span v-if="messageCount > 0">{{ messageCount }}</span>
                </i>
              </span>
              <div :class="['view-msg',{'view-msg-min': !msg.message && !msg.notice}]">
                <router-link :to="{name: 'vcenterMessageList'}" class="news">
                  <span v-if="msg.message"><i>{{msg.message}}</i>条[项目提醒]未查看</span>
                  <span v-else>[项目提醒]</span>
                </router-link>
                <router-link :to="{name: 'systemMessageList'}" class="notice">
                  <span v-if="msg.notice"><i>{{msg.notice}}</i>条[系统通知]未查看</span>
                  <span v-else>[系统通知]</span>
                </router-link>
              </div>
          </a>
          <el-menu class="el-menu-info" mode="horizontal" router>
            <el-submenu index="2">
              <template slot="title">
                <img class="avatar" v-if="eventUser.logo_url" :src="eventUser.logo_url"/>
                <img class="avatar" v-else :src="require('assets/images/avatar_100.png')"/>
                <span class="b-nickname">{{ eventUser.account }}</span>
              </template>
              <el-menu-item class="menu-control" index="/vcenter/control">个人中心</el-menu-item>
              <el-menu-item class="menu-admin" index="/admin" v-if="isAdmin > 0 ? true : false">后台管理</el-menu-item>
              <el-menu-item index="" class="menu-sign-out" @click="logout">安全退出</el-menu-item>
            </el-submenu>
          </el-menu>
        </div>

        <div class="nav-right" v-else>
          <div class="server-in-btn">
            <el-button size="small" class="is-custom" @click="toServer">设计服务商入驻</el-button>
          </div>
          <el-menu class="el-menu-header" :default-active="menuactive" mode="horizontal" router>
            <el-menu-item index="login" :route="menu.login">登录</el-menu-item>
            <el-menu-item index="register" :route="menu.register">注册</el-menu-item>
          </el-menu>
        </div>

      </div>
    </div>
    <div class="m-nav-header" v-if="isMob">

      <div class="el-menu-item logo">
        <span class="logo">太火鸟&nbsp;SaaS</span>
      </div>
      <div class="bars" @click="mMenuHide"></div>
      <section class="m-Menu" ref="mMenu" @click="mNavClick">
      </section>
      <div class="m-Nav" ref="mNav">
        <ul>
          <li @click="closeMenu">
            <router-link :to="menu.home">首页</router-link>
          </li>
          <li @click="closeMenu">
            <router-link :to="menu.server">服务</router-link>
          </li>
          <li @click="closeMenu">
            <router-link :to="menu.topic">铟果说</router-link>
          </li>
          <li @click="closeMenu">
            <router-link :to="menu.design_case">灵感</router-link>
          </li>
          <li @click="closeMenu">
            <router-link :to="menu.design">设计服务商入驻</router-link>
          </li>
          <li @click="closeMenu" class="m-Sign">
          <span @click="closeMenu" v-if="!isLogin">
            <router-link :to="menu.login" class="Flogin">登录</router-link>
          </span>
            <span @click="closeMenu" v-if="!isLogin">
            <router-link :to="menu.identity" @click="closeMenu">注册</router-link>
          </span>
            <span @click="closeMenu" v-if="isLogin">
            <i @click="logout">退出</i>
          </span>
          </li>
        </ul>
        <!--<div class="m-Sign" ref="mSign">-->
        <!--<span @click="closeMenu" v-if="!isLogin">-->
        <!--<router-link :to="menu.login">登录</router-link>-->
        <!--</span>-->
        <!--<span @click="closeMenu" v-if="!isLogin">-->
        <!--<router-link :to="menu.identity" @click="closeMenu">注册</router-link>-->
        <!--</span>-->
        <!--<span @click="closeMenu" v-if="isLogin">-->
        <!--<i @click="logout">退出</i>-->
        <!--</span>-->
        <!--</div>-->

      </div>
      <div class="m-Nav-right" v-if="isLogin">
        <a @click="moptionHide">
          <img class="avatar" v-if="eventUser.logo_url" :src="eventUser.logo_url"/>
          <img class="avatar" v-else :src="require('assets/images/avatar_100.png')"/>
        </a>
      </div>
      <div class="option-cover" v-if="!optionHide" @click="moptionHide"></div>
      <div class="option" v-if="!optionHide">
        <a @click="toCenter" :class="{'aActive': menuStatus === 'center'}">个人中心</a>
        <a v-if="isCompany" @click="toTools" :class="{'aActive': menuStatus === 'tools'}">工具</a>
      </div>
    </div>
    <div class="header-buttom-line"></div>
  </div>
</template>

<script>
  import auth from '@/helper/auth'
  import api from '@/api/api'
  import { MSG_COUNT, MENU_STATUS } from '@/store/mutation-types'
  export default {
    name: 'head_menu',
    data() {
      return {
        // menuactive: this.$route.path.split('/')[1],
        requestMessageTask: null,
        menu: {
          home: {path: '/home'},
          server: {path: '/server'},
          design: {path: '/server_design'},
          topic: {path: '/article/list'},
          design_case: {path: '/design_case/general_list'},
          apply: {path: '/apply'},
          login: {path: '/login'},
          register: {path: '/register'},
          identity: {path: '/identity'}
        },
        menuHide: true,
        msgHide: true,
        optionHide: true,
        msg: {
          message: 0,
          notice: 0,
          quantity: 0
        }
      }
    },
    watch: {
      $route(to, from) {
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
          message: '已退出',
          type: 'success',
          duration: 800
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
        this.$http.get(api.messageGetMessageQuantity, {}).then(function (response) {
          if (response.data.meta.status_code === 200) {
            self.msg.message = parseInt(response.data.data.message)
            self.msg.notice = parseInt(response.data.data.notice)
            sessionStorage.setItem('noticeCount', self.msg.notice)
            let messageCount = parseInt(response.data.data.quantity)
            // 写入localStorage
            self.$store.commit(MSG_COUNT, messageCount)
          } else {
            self.$message.error(response.data.meta.message)
          }
        }).catch((error) => {
          console.error(error)
        })
      },
      // 定时加载消息数量
      timeLoadMessage() {
        const self = this
        // 定时请求消息数量
        var limitTimes = 0
        self.requestMessageTask = setInterval(function () {
          if (limitTimes >= 12) {
            return
          } else {
            self.fetchMessageCount()
            limitTimes += 1
          }
        }, 30000)
      },
      // 查看消息
      viewMsg() {
      },
      // 移动端菜单显示/隐藏
      mMenuHide() {
        this.menuHide = !this.menuHide
        if (this.menuHide) {
          this.reScroll()
        } else {
          this.addScroll()
        }
      },
      // 点击超链接关闭菜单
      closeMenu(e) {
        e.stopPropagation()
        this.menuHide = !this.menuHide
        this.reScroll()
      },
      // 点击其他地方关闭菜单
      mNavClick(e) {
        this.closeMenu(e)
      },
      addScroll() {
        // this.$refs.mCover.style.width = '100%'
        this.$refs.mNav.style.marginLeft = 0
        this.$refs.mMenu.style.width = '100%'
        document.body.setAttribute('class', 'disableScroll')
        document.childNodes[1].setAttribute('class', 'disableScroll')
      }, // 移动端显示 ↑  隐藏 ↓ 侧边栏
      reScroll() {
        // this.$refs.mCover.style.width = 0
        this.$refs.mNav.style.marginLeft = '-54vw'
        this.$refs.mMenu.style.width = 0
        document.body.removeAttribute('class', 'disableScroll')
        document.childNodes[1].removeAttribute('class', 'disableScroll')
      },
      toCenter () {
        this.moptionHide()
        this.$store.commit(MENU_STATUS, 'center')
        sessionStorage.setItem('MENU_BAR', 11)
        this.$router.push({name: 'vcenterControl'})
      },
      toTools () {
        this.moptionHide()
        this.$store.commit(MENU_STATUS, 'tools')
        sessionStorage.setItem('MENU_BAR', 11)
        this.$router.push({name: 'vcentercommonlySites'})
      },
      moptionHide () {
        this.optionHide = !this.optionHide
      }
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      },
      isLogin: {
        get() {
          return this.$store.state.event.token
        },
        set() {}
      },
      eventUser() {
        let user = this.$store.state.event.user
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
        if (menu === 'article' || menu === 'subject') {
          return 'topic'
        }
        return menu
      },
      messageCount() {
        return this.$store.state.event.msgCount
      },
      menuStatus () {
        return this.$store.state.event.menuStatus
      },
      isCompany() {
        return this.$store.state.event.user.type === 2
      }
    },
    created: function () {
      const self = this
      if (self.isLogin) {
        self.fetchMessageCount()
        self.timeLoadMessage()
      }
      this.$store.commit('INIT_PAGE')
    },
    mounted() {
      let that = this
      window.addEventListener('resize', () => {
        that.$store.commit('INIT_PAGE')
      })
    },
    destroyed() {
      clearInterval(this.requestMessageTask)
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  @keyframes slow {
    0% {
      transform: translateY(-60px);
    }
    100% {
      transform: translateY(0px);
    }
  }

  @keyframes slowShow {
    0% {
      height: 0;
    }
    100% {
      height: 81.5px;
    }
  }

  #header-layout {
    position: relative;
    z-index: 999;
    animation: slow 0.4s;
  }

  .Flogin {
    background: #ff5a5f;
    border-color: #ff5a5f;
  }

  .server-in-btn {
    height: 60px;
    line-height: 60px;
    margin-right: 11px;
  }

  .more {
    width: 1200px;
    height: 60px;
    background: #ff4500;
    position: fixed;
  }

  .m-Nav-right {
    position: absolute;
    top: 15px;
    right: 12px;
  }

  .m-Nav-right .avatar {
    width: 30px;
    height: 30px;
  }

  .option {
    position: absolute;
    z-index: 10;
    width: 100px;
    background: #FFFFFF;
    right: 8px;
    top: 53px;
    border: 1px solid #DCDCDC;
    box-shadow: 0 0 4px 0 rgba(0, 0, 0, 0.05);
    border-radius: 10px;
    padding: 0 4px;
    font-size: 14px;
  }

  .option a {
    display: block;
    line-height: 30px;
    text-align: center;
  }

  .option a.aActive {
    color: #FE3824;
  }

  .option a:first-child {
    border-bottom: 1px solid #DCDCDC;
  }

  .option a:last-child {
    border-bottom: none;
  }

  .option::after {
    content: "";
    width: 10px;
    height: 10px;
    position: absolute;
    background: #FFFFFF;
    right: 12px;
    top: -5px;
    border: 1px solid #DCDCDC;
    transform: rotate(45deg);
  }

  .option::before {
    content: "";
    width: 10px;
    height: 20px;
    position: absolute;
    z-index: 2;
    right: 12px;
    top: -5px;
    background: #FFFFFF;
    transform: rotate(90deg);
  }

  .option-cover {
    position: absolute;
    z-index: 9;
    top: 0;
    left: 0;
    width: 100%;
    height: 100vh;
    background: #0006
  }

  .view-msg {
    animation: slowShow 0.3s;
  }

  .container {
    overflow:visible
  }
</style>
