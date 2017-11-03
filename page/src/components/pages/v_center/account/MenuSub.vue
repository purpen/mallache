<template>
  <div class="vcenter-menu-sub" v-if="!isMob">
    <div class="vcenter-menu-sub-list">
      <router-link :to="{name: 'modifyPwd'}" :class="{'item': true, 'is-active': currentSubName === 'modify_pwd'}">
        修改密码
      </router-link>

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
