<template>
  <div class="tools-menu">
    <div :class="['menu-list', 'clearfix', isMob ? 'Mmenulist' : '']" ref="Mmenulist">
      <a @click="alick" :to="'/vcenter/commonly_sites'"
         :class="{'item': true, 'is-active': currentName === 'commonlySites'}">
        常用网站
      </a>
      <a @click="alick" class="item" :to="'/vcenter/veer_image'"
         :class="{'item': true, 'is-active': currentName === 'veerImage'}">
        图片素材
      </a>
      <a @click="alick" :to="'/vcenter/trend_report'"
         :class="{'item': true, 'is-active': currentName === 'trendReport'}">
        趋势/报告
      </a>
      <a @click="alick" :to="'/vcenter/exhibition'"
         :class="{'item': true, 'is-active': currentName === 'exhibition'}">
        设计日历
      </a>
    </div>
  </div>
</template>
<script>
  export default {
    name: 'ToolsMenu',
    data () {
      return {}
    },
    props: {
      currentName: {}
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      }
    },
    methods: {
      alick(e) {
        sessionStorage.setItem('TOOLS_MENU_BAR', e.target.offsetLeft)
        this.$router.push(e.target.getAttribute('to'))
      }
    },
    mounted() {
      let menu = sessionStorage.getItem('TOOLS_MENU_BAR')
      this.$refs.Mmenulist.scrollLeft = menu - document.documentElement.clientWidth / 2 + 38
    }
  }
</script>
<style scoped>
  .tools-menu {
    width: 100%;
    height: 20px;
    overflow: hidden;
    margin-bottom: 20px;
  }

  .menu-list span {
    display: block;
    padding: .85rem 1.5rem;
    font-size: 1.5rem;
    color: #666666;
    position: relative;
    text-indent: 12px;
  }

  .menu-list span::before {
    content: "";
    display: block;
    position: absolute;
    width: 4px;
    height: 15px;
    background: #D2D2D2;
  }
</style>
