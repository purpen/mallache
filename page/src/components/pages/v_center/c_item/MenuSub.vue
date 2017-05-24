<template>
  <div class="vcenter-menu-sub">

    <div class="vcenter-menu-sub-list">
      <router-link :to="{name: 'vcenterCItemList'}" :class="{'item': true}">待合作项目({{ waitCountProp }})</router-link>
    </div>
    <div class="vcenter-menu-sub-list">
      <router-link :to="{name: 'vcenterTrueCItemList'}" :class="{'item': true}">已合作项目({{ ingCountProp }})</router-link>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'vcenter_c_item_menu',
    props: {
      waitCountProp: {
        default: 0
      },
      ingCountProp: {
        default: 0
      }
    },
    data () {
      return {
        msg: ''
      }
    },
    created: function() {
    },
    watch: {
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
