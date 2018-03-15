<template>
  <div :class="['vcenter-menu-sub', isMob ? 'vcenter-menu-sub-m' : '', 'clearfix']">
    <div :class="['vcenter-menu-sub-list', isMob ? 'vcenter-menu-sub-list-m' : '']">
      <router-link :to="{name: 'vcenterComputerBase'}" class="item">基本信息</router-link>
      <router-link :to="{name: 'vcenterComputerAccreditation'}"
                   :class="{'item': true, 'is-active': currentSubName === 'identification' ? true : false}">实名认证
      </router-link>
      <router-link :to="{name: 'vcenterComputerTaking'}" class="item">公司接单设置</router-link>

    </div>
  </div>
</template>

<script>
  export default {
    name: 'vcenter_company_menu',
    props: {
      currentSubName: {
        default: ''
      }
    },
    data () {
      return {
        msg: 'This is SubMenu'
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
