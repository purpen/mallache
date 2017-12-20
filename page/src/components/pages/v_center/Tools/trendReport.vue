<template>
  <div class="container">
    <div class="trend-report">
      <div class="blank20"></div>
      <el-row :gutter="24" class="anli-elrow">
        <v-menu currentName="trendReport"></v-menu>
        <el-col :span="isMob ? 24 : 20">
        </el-col>
      </el-row>
    </div>
  </div>
</template>
<script>
  //  import api from '@/api/api'
  import vMenu from '@/components/pages/v_center/Menu'
  export default {
    name: 'trendReport',
    components: {
      vMenu
    },
    data () {
      return {}
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      }
    }
  }
</script>
<style scoped>

</style>
