<template>
  <div class="vcenter-menu-sub">
    <div class="vcenter-menu-sub-list">
      <router-link :to="{name: 'vcenterItemList'}" exact class="item">全部</router-link>
    </div>
    <div class="vcenter-menu-sub-list">
      <router-link :to="{name: 'vcenterItemList'}" exact class="item">正在进行的项目</router-link>
    </div>
    <div class="vcenter-menu-sub-list">
      <router-link :to="{name: 'vcenterItemList'}" exact class="item">已完成</router-link>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'vcenter_item_menu',
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
