<template>
  <div>
    <div class="tools-menu" v-if="isMob">
      <div :class="['menu-list', 'clearfix', isMob ? 'mMenuList' : '']" ref="mMenuList">
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
    <div class="nav-list" v-else>
      <div class="category-list">
        <router-link :to="{name: 'vcentercommonlySites'}" :class="{'active': menuType === 'vcentercommonlySites'}">常用网站</router-link>
        <router-link :to="{name: 'vcenterVeerImage'}"  :class="{'active': menuType === 'vcenterVeerImage'}">图片素材</router-link>
        <router-link :to="{name: 'vcenterTrendReport'}" :class="{'active': menuType === 'vcenterTrendReport' || menuType === 'trendReportShow' }">趋势/报告</router-link>
        <router-link :to="{name: 'vcenterExhibition'}"  :class="{'active': menuType === 'vcenterExhibition'}">设计日历</router-link>
      </div>
    </div>
  </div>
</template>
<script>
  export default {
    name: 'ToolsMenu',
    data () {
      return {
        menuType: ''
      }
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
      if (this.isMob) {
        this.$refs.mMenuList.scrollLeft = menu - document.documentElement.clientWidth / 2 + 38
      }
    },
    created() {
      this.menuType = this.$route.name
    }
  }
</script>
<style scoped>
  .tools-menu {
    width: 100%;
    height: 40px;
    overflow: hidden;
    margin-bottom: 15px;
  }

  .mMenuList {
    margin-top: 20px;
  }

  .menu-list .item {
    display: inline;
  }

  .menu-list span {
    display: block;
    padding: .85rem 1.5rem;
    font-size: 1.5rem;
    color: #666666;
    position: relative;
    text-indent: 12px;
  }

  .menu-list span:first-child {
    padding-top: 0;
  }

  .menu-list span::before {
    content: "";
    display: block;
    position: absolute;
    width: 4px;
    height: 15px;
    background: #D2D2D2;
  }

  .nav-list {
    height: 66px;
    padding-bottom: 10px;
    overflow: hidden;
  }
  .category-list {
    padding-bottom: 10px;
    margin: 30px auto 10px auto;
    text-align: center;
    width: 100%;
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

  .nav-list {
    width: 100%;
  }
</style>
