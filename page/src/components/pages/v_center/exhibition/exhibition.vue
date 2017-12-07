<template>
  <div class="container">
    <div class="exhibition">
      <div class="blank20"></div>
      <el-row :gutter="24" class="anli-elrow">
        <v-menu currentName="exhibition"></v-menu>
        <el-col :span="isMob ? 24 : 20">
          <vCalendar></vCalendar>
        </el-col>
      </el-row>
    </div>
  </div>
</template>
<script>

  import vMenu from '@/components/pages/v_center/Menu'
  import vCalendar from '@/components/pages/v_center/calendar/calendar'
  //  import api from '@/api/api'
  export default {
    name: 'exhibition',
    components: {
      vCalendar,
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
