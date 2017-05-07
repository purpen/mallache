<template>
  <div class="vcenter-menu-sub">

    <div class="vcenter-menu-sub-list">
      <router-link :to="{name: 'vcenterCItemList', query: {type: 1}}" active-class="false" class="item" :class="{'is-active': menuType == 1}">正在进行的项目</router-link>
    </div>
    <div class="vcenter-menu-sub-list">
      <router-link :to="{name: 'vcenterCItemList', query: {type: 8}}" active-class="false" class="item" :class="{'is-active': menuType == 8}">已完成</router-link>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'vcenter_c_item_menu',
    data () {
      return {
        menuType: 0,
        msg: ''
      }
    },
    created: function() {
      var type = this.$route.query.type
      this.menuType = 0
      if (type) {
        this.menuType = parseInt(type)
      }
    },
    watch: {
      '$route' (to, from) {
        // 对路由变化作出响应...
        var type = this.$route.query.type
        this.menuType = 0
        if (type) {
          this.menuType = parseInt(type)
        }
      }
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
