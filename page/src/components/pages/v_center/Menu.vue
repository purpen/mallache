<template>
  <el-col :span="isMob ? 24 : 4" class="left-menu">
    <section :class="['menuHide', isMob ? 'MmenuHide' : '']">
      <div :class="['menu-list', 'clearfix', isMob ? 'Mmenulist' : '']" ref="Mmenulist" v-if="isCompany">
        <span v-if="!isMob">个人中心</span>
        <a @click="alick" :to="'/vcenter/control'"
           :class="{'item': true, 'is-active': currentName === 'control'}">
          控制面板
        </a>
        <a @click="alick" :to="'/vcenter/message/list'"
           :class="{'item': true, 'is-active': currentName === 'message'}">
          消息
        </a>
        <a @click="alick" :to="'/vcenter/citem/list'"
           :class="{'item': true, 'is-active': currentName === 'c_item'}">
          项目订单
        </a>
        <a @click="alick" :to="'/vcenter/design_case'"
           :class="{'item': true, 'is-active': currentName === 'design_case'}">
          作品案例
        </a>
        <a @click="alick" :to="'/vcenter/match_case'"
           :class="{'item': true, 'is-active': currentName === 'match_case'}">
          参赛作品
        </a>
        <a @click="alick" :to="'/vcenter/wallet/list'"
           :class="{'item': true, 'is-active': currentName === 'wallet'}">
          我的钱包
        </a>
        <a @click="alick" :to="'/vcenter/company/base'"
           :class="{'item': true, 'is-active': currentName === 'profile'}">
          账号设置
        </a>
        <a @click="alick" :to="'/vcenter/modify_pwd'"
           :class="{'item': true, 'is-active': currentName === 'modify_pwd'}">
          安全设置
        </a>
        <a :class="{'item': true, 'is-active': currentName === 'company'}" @click="redirectCompany" v-if="isMob">
          查看公司主页
        </a>
      </div>

      <div :class="['menu-list', 'clearfix', isMob ? 'Mmenulist' : '']" ref="Mmenulist" v-else>
        <a @click="alick" :to="'/vcenter/control'" :class="{'item': true, 'is-active': currentName === 'control'}">
          控制面板
        </a>
        <a @click="alick" :to="'/vcenter/message/list'"
           :class="{'item': true, 'is-active': currentName === 'message'}">
          消息
        </a>
        <a @click="alick" :to="'/vcenter/item/list'"
           :class="{'item': true, 'is-active': currentName === 'item'}">
          我的项目
        </a>
        <a @click="alick" :to="'/vcenter/wallet/list'"
           :class="{'item': true, 'is-active': currentName === 'wallet'}">
          我的钱包
        </a>
        <a @click="alick" :to="'/vcenter/d_company/base'"
           :class="{'item': true, 'is-active': currentName === 'profile'}">
          账号设置
        </a>
        <a @click="alick" :to="'/vcenter/modify_pwd'"
           :class="{'item': true, 'is-active': currentName === 'modify_pwd'}">
          安全设置
        </a>
      </div>

      <div class="computer-btn" v-if="isCompany">
        <el-button @click="redirectCompany" class="companyBtn">查看公司主页</el-button>
      </div>
      <div class="menu-list" v-if="true">
        <span v-if="!isMob">工具</span>
        <a @click="alick" :to="'/vcenter/commonly_sites'"
           :class="{'item': true, 'is-active': currentName === 'commonlySites'}">
          常用网站
        </a>
        <a @click="alick" class="item" :to="'/vcenter/veer_image'"
           :class="{'item': true, 'is-active': currentName === 'veerImage'}">
          图片素材
        </a>
        <a @click="alick" :to="'/vcenter/trend_report'"
           :class="{'item': true, 'is-active': currentName === 'trendReport'}">
          趋势/报告
        </a>
        <a @click="alick" :to="'/vcenter/exhibition'"
           :class="{'item': true, 'is-active': currentName === 'exhibition'}">
          设计日历
        </a>
      </div>
    </section>
  </el-col>
</template>

<script>
  export default {
    name: 'vcenter_menu',
    props: {
      currentName: {}
    },
    data () {
      return {
        msg: 'This is menu',
        leftVal: 0
      }
    },
    // 判断是客户还是设计公司
    methods: {
      redirectCompany(e) {
        let companyId = this.$store.state.event.user.design_company_id
        if (!companyId || companyId === 0) {
          this.$message.error('请先申请公司认证!')
        } else {
          this.$router.push({name: 'companyShow', params: {id: companyId}})
        }
      },
      alick(e) {
        sessionStorage.setItem('MENU_BAR', e.target.offsetLeft)
        this.$router.push(e.target.getAttribute('to'))
      }
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      },
      isCompany() {
        return this.$store.state.event.user.type === 2
      }
    },
    mounted() {
      let menu = sessionStorage.getItem('MENU_BAR')
      this.$refs.Mmenulist.scrollLeft = menu - document.documentElement.clientWidth / 2 + 38
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .menu-list span {
    display: block;
    padding: .85rem 1.5rem;
    font-size: 1.5rem;
    color: #666666;
    position: relative;
    text-indent: 12px;
  }

  .menu-list span::before {
    content: "";
    display: block;
    position: absolute;
    width: 4px;
    height: 15px;
    background: #D2D2D2;
  }
</style>
