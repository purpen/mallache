<template>
  <div :class="['vcenter-menu-sub', isMob ? 'vcenter-menu-sub-m' : '', 'clearfix']">
    <div :class="['vcenter-menu-sub-list', isMob ? 'vcenter-menu-sub-list-m' : '']">
      <router-link :to="{name: 'vcenterCItemList'}" :class="{'item': true}">待确认({{ waitCountProp }})</router-link>
      <router-link :to="{name: 'vcenterTrueCItemList'}" :class="{'item': true}">已合作({{ ingCountProp }})</router-link>
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
    created: function () {
    },
    watch: {},
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
