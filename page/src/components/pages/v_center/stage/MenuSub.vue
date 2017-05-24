<template>
  <div class="vcenter-menu-sub">
    <div class="vcenter-menu-sub-list">
      <router-link :to="{name: 'vcenterTrueCItemList'}" class="item is-active">我的项目</router-link>
    </div>
    <div class="vcenter-menu-sub-list-right" style="float:right;">

    </div>
  </div>
</template>

<script>
  export default {
    name: 'vcenter_design_stage_menu',
    data () {
      return {
        msg: ''
      }
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
