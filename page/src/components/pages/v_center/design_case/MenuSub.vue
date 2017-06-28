<template>
  <div class="vcenter-menu-sub">
    <div class="vcenter-menu-sub-list">
      <router-link :to="{name: 'vcenterDesignCaseList'}" exact class="item">全部</router-link>
    </div>
    <div class="vcenter-menu-sub-list-right fr">
      <router-link :to="{name: 'vcenterDesignCaseAdd'}" exact class="item add"><i class="el-icon-plus"></i> 添加作品案例</router-link>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'vcenter_design_case_menu',
    data () {
      return {
        msg: 'This is Home'
      }
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .item.add {
    font-size: 1.3rem;
  }

</style>
