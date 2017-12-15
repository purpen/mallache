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
  import api from '@/api/api'
  import vMenu from '@/components/pages/v_center/Menu'
  import vCalendar from '@/components/pages/v_center/calendar/calendar'
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
    },
    created () {
      this.$http.get(api.dateOfAwardMonth).then((res) => {
        if (res.data.meta.status_code === 200) {
          console.log(res.data.data)
          for (let i of res.data.data) {
            let obj = {
              title: i.name,
              start: i.start_time,
              end: i.end_time,
              type: i.type,
              summary: i.summary
            }
            console.log(obj)
          }
        } else {
          this.$message.error(res.data.meta.message)
        }
      }).catch((err) => {
        console.error(err)
      })
    }
  }
</script>
<style scoped>

</style>
