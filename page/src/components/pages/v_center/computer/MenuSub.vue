<template>
  <div class="vcenter-menu-sub">
    <div class="vcenter-menu-sub-list">
      <router-link :to="{name: 'vcenterComputerAccreditation'}" exact class="item">公司认证</router-link>
      <router-link :to="{name: 'vcenterComputerProfile'}" class="item">基本信息</router-link>
      <router-link :to="{name: 'vcenterComputerTaking'}" class="item">公司接单设置</router-link>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'vcenter_computer_menu',
    data () {
      return {
        msg: 'This is Home'
      }
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
