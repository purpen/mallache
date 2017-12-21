<template>
  <div class="tools-menu">
    <div :class="['menu-list', 'clearfix', isMob ? 'Mmenulist' : '']" ref="Mmenulist" v-else>
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
        展览
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
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      }
    },
    methods: {
      alick(e) {
        sessionStorage.setItem('MENU_BAR', e.target.offsetLeft)
        this.$router.push(e.target.getAttribute('to'))
      }
    }
  }
</script>
<style scoped>
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
