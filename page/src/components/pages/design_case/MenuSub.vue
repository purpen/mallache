<template>
<div class="nav-list">
  <div class="category-list">
    <router-link :to="{name: 'designGeneralList'}" :class="{'active': menuType === 'designGeneralList'}">公司案例</router-link>
    <router-link :to="{name: 'designAwardsList'}"  :class="{'active': menuType === 'designAwardsList'}">设计奖项</router-link>
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
<style scoped>
  .category-list {
    padding-bottom: 10px;
    margin: 30px auto 10px auto;
    text-align: center;
    min-width: 100%;
    white-space: nowrap;
    overflow-x: auto;
    }

  .category-list a {
    font-size: 1.6rem;
    margin-right: 40px;
    color: #666666;
    }

  .category-list a:hover,
  .category-list a.active {
    color: #FF5A5F;
    }

  @media screen and (max-width: 480px) {
    .nav-list {
      margin-top: 16px;
      height: 22px;
      overflow: hidden;
    }

    .category-list {
      margin: 0 0 16px 0;
      padding: 2px 0 48px 16px;
      white-space: nowrap;
      overflow-x: auto;
    }

    .category-list a {
      margin-right: 0;
      margin-right: 30px;
    }

    .content {
      padding: 15px;
    }
  }
</style>

