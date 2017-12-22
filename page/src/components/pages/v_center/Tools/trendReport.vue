<template>
  <div class="container">
    <div class="trend-report">
      <div class="blank20"></div>
      <el-row :gutter="24" class="anli-elrow">
        <v-menu currentName="trendReport" v-if="menuStatus !== 'tools'"></v-menu>
        <ToolsMenu v-if="menuStatus === 'tools'"
                   currentName="trendReport"></ToolsMenu>
        <el-col :span="isMob ? 24 : 20">
          <!--<iframe src="../../../../../static/pdf/demo.pdf" type="application/pdf" :scroll="no">-->
          <!--</iframe>-->
        </el-col>
      </el-row>
    </div>
  </div>
</template>
<script>
  //  import api from '@/api/api'
  import vMenu from '@/components/pages/v_center/Menu'
  import ToolsMenu from '@/components/pages/v_center/ToolsMenu'
  export default {
    name: 'trendReport',
    components: {
      vMenu,
      ToolsMenu
    },
    data () {
      return {}
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      },
      menuStatus () {
        return this.$store.state.event.menuStatus
      }
    }
  }
</script>
<style scoped>
  iframe {
    width: 100%;
    height: 100%;
  }
</style>
