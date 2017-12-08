<template>
  <div class="vcenter-menu-sub" v-if="!isMob">
    <div class="vcenter-menu-sub-list">
      <router-link :to="{name: 'vcenterMatchCaseList'}" exact class="item">全部</router-link>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'vcenter_match_case_menu',
    data () {
      return {
        msg: 'This is Home'
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
  .item.add {
    font-size: 1.3rem;
  }
</style>
