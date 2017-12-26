<template>
  <div class="vcenter-menu-sub" v-if="!isMob">
    <div class="vcenter-menu-sub-list">
      <router-link :to="{name: 'vcenterMessageList'}"
        active-class="false" :class="{'item': true, 'is-active': menuType === 'vcenterMessageList'}">
        项目提醒</router-link>
      <router-link :to="{name: 'systemMessageList'}"
        active-class="false" :class="{'item': true, 'is-active': menuType === 'systemMessageList'}">
        系统通知</router-link>
    </div>

  </div>
</template>

<script>
  export default {
    name: 'vcenter_message_menu',
    data () {
      return {
        menuType: '',
        msg: ''
      }
    },
    methods: {},
    created: function() {
      this.menuType = this.$route.name
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
