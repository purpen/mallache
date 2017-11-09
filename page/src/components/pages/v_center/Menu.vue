<template>

  <el-col :span="isMob ? 24 : 4" class="left-menu">
    <section :class="['menuHide', isMob ? 'MmenuHide' : '']">
      <div :class="['menu-list', isMob ? 'Mmenulist' : '']" v-if="isCompany()">
        <router-link :to="{name: 'vcenterControl'}" class="item">
          控制面板
        </router-link>
        <router-link :to="{name: 'vcenterMessageList'}" :class="{'item': true, 'is-active': currentName === 'message'}">
          消息
        </router-link>
        <router-link :to="{name: 'vcenterCItemList'}" :class="{'item': true, 'is-active': currentName === 'c_item'}">
          项目订单
        </router-link>
        <router-link :to="{name: 'vcenterDesignCaseList'}" class="item">
          作品案例
        </router-link>
        <router-link :to="{name: 'vcenterWalletList'}" :class="{'item': true, 'is-active': currentName === 'wallet'}">
          我的钱包
        </router-link>
        <router-link :to="{name: 'vcenterComputerBase'}"
                     :class="{'item': true, 'is-active': currentName === 'profile'}">
          账号设置
        </router-link>
        <router-link :to="{name: 'modifyPwd'}" :class="{'item': true, 'is-active': currentName === 'modify_pwd'}">
          安全设置
        </router-link>
      </div>

      <div :class="['menu-list', isMob ? 'Mmenulist' : '']" v-else>
        <router-link :to="{name: 'vcenterControl'}" class="item">
          控制面板
        </router-link>
        <router-link :to="{name: 'vcenterMessageList'}" :class="{'item': true, 'is-active': currentName === 'message'}">
          消息
        </router-link>
        <router-link :to="{name: 'vcenterItemList'}" :class="{'item': true, 'is-active': currentName === 'item'}">
          我的项目
        </router-link>
        <router-link :to="{name: 'vcenterWalletList'}" :class="{'item': true, 'is-active': currentName === 'wallet'}">
          我的钱包
        </router-link>
        <router-link :to="{name: 'vcenterDComputerBase'}"
                     :class="{'item': true, 'is-active': currentName === 'profile'}">
          账号设置
        </router-link>
        <router-link :to="{name: 'modifyPwd'}" :class="{'item': true, 'is-active': currentName === 'modify_pwd'}">
          安全设置
        </router-link>
      </div>

      <div class="computer-btn" v-if="isCompany()">
        <el-button @click="redirectCompany" class="companyBtn">查看公司主页</el-button>
      </div>
    </section>
  </el-col>
</template>

<script>
  export default {
    name: 'vcenter_menu',
    props: {
      currentName: {
        default: ''
      }
    },
    data () {
      return {
        msg: 'This is menu'
      }
    },
    // 判断是客户还是设计公司
    methods: {
      isCompany() {
        let uType = this.$store.state.event.user.type
        if (uType === 2) {
          return true
        } else {
          return false
        }
      },
      redirectCompany() {
        let companyId = this.$store.state.event.user.design_company_id
        if (!companyId || companyId === 0) {
          this.$message.error('请先申请公司认证!')
        } else {
          this.$router.push({name: 'companyShow', params: {id: companyId}})
        }
      }
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      }
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
