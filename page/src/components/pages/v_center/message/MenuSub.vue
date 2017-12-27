<template>
  <div :class="['vcenter-menu-sub', isMob ? 'vcenter-menu-sub-m' : '', 'clearfix']">
    <div :class="['vcenter-menu-sub-list', isMob ? 'vcenter-menu-sub-list-m' : '']">
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
  .vcenter-menu-sub-m {
    padding: 15px 0 0;
    height: auto;
  }

  .vcenter-menu-sub-list-m {
    /*padding: 0 10px;*/
    float: none;
    margin:0 -15px -1px;
    border-top: 1px solid #ccc;
    display: flex;
    justify-content: space-around;
  }

  .vcenter-menu-sub-list-m .item {
    text-align: center;
    line-height: 32px;
  }
</style>
