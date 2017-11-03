<template>
  <div class="footer clear">
    <el-row class="foot-main">
      <el-col class="item" :xs="12" :sm="6" :md="6" :lg="6">
        <p class="title">铟果</p>
        <p>
          <router-link :to="{name: 'about'}">关于我们</router-link>
        </p>
        <p>
          <router-link :to="{name: 'contact'}">联系我们</router-link>
        </p>
      </el-col>

      <el-col class="item server" :xs="12" :sm="6" :md="6" :lg="6">
        <p class="title">服务</p>
        <p v-if="!isMob">
          <router-link :to="{name: 'itemSubmitOne'}" class="">发布需求</router-link>
        </p>
        <p>
          <router-link :to="{name: 'serverDesign'}" class="">申请加入</router-link>
        </p>
      </el-col>

      <el-col class="item" :xs="12" :sm="6" :md="6" :lg="6">
        <p class="title">帮助</p>
        <p>
          <router-link :to="{name: 'trade'}" class="">交易保障</router-link>
        </p>
        <p>
          <router-link :to="{name: 'question'}" class="">常见问题</router-link>
        </p>
        <p>
          <router-link :to="{name: 'item'}" class="">服务条款</router-link>
        </p>
      </el-col>

      <el-col class="item fllow" :xs="12" :sm="6" :md="6" :lg="6">
        <p class="title">关注我们</p>
        <p class="call">电话：4008-798-751</p>
        <div class="Fshare-box">
          <p class="share" @click="clickwx">
            <i class="wechat"></i>
          </p>
          <p class="share">
            <i class="weibo"></i>
          </p>
          <!--<p class="share">-->
          <!--<router-link :to="{name: 'test'}" class="">-->
          <!--<i class="fa fa-qq fa-2x" aria-hidden="true"></i>-->
          <!--</router-link>-->
          <!--</p>-->
        </div>
      </el-col>
    </el-row>
    <transition name="fade">
      <section class="hidweixin" v-if="iswxhide && !isMob">
        <div class="hidebox" @click="clickwx"></div>
        <div class="wxbox">
          <i class="wxclose" @click="clickwx"></i>
          <h3>铟果官方微信二维码</h3>
          <p class="openxw">打开微信，点击右上角的“+”，选择“扫一扫”功能，对准下方二维码即可。</p>
          <div class="d3INeq">
            <p class="saoqr">扫码关注我们</p>
            <p class="qr"></p>
          </div>
        </div>
      </section>
    </transition>
  </div>
</template>

<script>
  export default {
    name: 'footer',
    props: {
      currentName: {
        default: ''
      }
    },
    data() {
      return {
        msg: '',
        iswxhide: false
      }
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      }
    },
    methods: {
      clickwx() {
        this.iswxhide = !this.iswxhide
      }
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  @keyframes typing {
    0% {
      width: 0
    }
    80% {
      width: 12em
    }
  }

  @keyframes caret {
    50% {
      background: url("../../assets/images/QR02.png") no-repeat;
      background-size: cover;
    }
  }

  @keyframes fadeEnter {
    0% {
      transform: scale(0.8);
    }
    100% {
      transform: scale(1);
    }

  }

  .foot-main {
    max-width: 1180px;
    margin: 0 auto;
  }

  .server {
    min-height: 114px;
  }

  /*
  pc端增加微信二维码
  */
  .hidweixin {
    position: fixed;
    z-index: 999999;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .hidebox {
    position: absolute;
    width: 100%;
    height: 100vh;
  }

  .wxbox {
    width: 600px;
    height: 400px;
    color: #222;
    background: #fff;
    border-radius: 10px;
    position: relative;
    animation: fadeEnter 0.5s;
  }

  .wxbox h3 {
    margin: 30px;
    font-size: 18px;
    font-weight: 600;
  }

  .wxclose {
    position: absolute;
    color: #BEBEBE;
    right: 40px;
    top: 30px;
    width: 20px;
    height: 20px;
    line-height: 20px;
    font-size: 20px;
    background: url("../../assets/images/icon/close@2x.png") no-repeat;
    background-size: contain;
  }

  .openxw {
    margin: 20px 0;
  }

  .saoqr {
    padding-left: 8px;
    transition: 0.3s ease all;
    margin: auto;
    width: 12em;
    letter-spacing: 1em;
    white-space: nowrap;
    overflow: hidden;
    font-size: 15px;
    animation: typing 1.8s steps(6) infinite;
    color: #38408A;
  }

  .qr {
    width: 300px;
    height: 250px;
    margin: -30px auto;
    background: url("../../assets/images/QR01.png") no-repeat;
    background-size: cover;
    animation: caret 2.8s infinite;
  }

  .footer .fllow {
    text-align: left;
    padding-left: 110px;
  }

  .footer .fllow p {
    margin-left: 0;
  }
  .footer .fllow .title {
    white-space: nowrap;
    margin: 40px 20px 10px 0px;
  }

  .call {
    color: #999;
    font-size: 14px;
    white-space: nowrap;
  }

  .fade-enter-active, .fade-leave-active {
    transition: all .3s ease;
  }

  .fade-enter, .fade-leave-to {
    opacity: 0;
  }

  @media screen and (min-width: 768px) and (max-width: 1055px) {
    .footer .fllow {
      padding-left: 60px;
    }
  }
</style>
