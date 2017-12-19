<template>
  <div class="container">
    <div class="exhibition">
      <div class="blank20"></div>
      <el-row :gutter="24" class="anli-elrow">
        <v-menu currentName="exhibition"></v-menu>
        <el-col :span="isMob ? 24 : 20">
          <vCalendar
            :events="events" @day-click="dayClick"
            @event-selected="eventSelected"
            @env-mouse-over="envMouseOver"
            @env-mouse-out="envMouseOut">
          </vCalendar>
        </el-col>
      </el-row>
    </div>
  </div>
</template>
<script>
  import api from '@/api/api'
  import vMenu from '@/components/pages/v_center/Menu'
  import vCalendar from '@/components/pages/v_center/Tools/calendar/calendar'
  export default {
    name: 'exhibition',
    components: {
      vCalendar,
      vMenu
    },
    data () {
      return {
        events: [],
        eventMsg: {},
        id: '',
        backGround: ''
      }
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      }
    },
    created () {
      this.$http.get(api.dateOfAwardMonth).then((res) => {
        if (res.data.meta.status_code === 200) {
          let n = 0
          for (let i of res.data.data) {
            n++
            i.type = n
            let obj = {}
            switch (i.type) {
              case 1:  // 大赛
                obj.backgroundColor = '#65A6FFCC'
                obj.borderColor = '#65A6FFCC'
                break
              case 2:  // 节日
                obj.backgroundColor = '#67D496CC'
                obj.borderColor = '#67D496CC'
                break
              case 3:  // 展会
                obj.backgroundColor = '#FD9E5FCC'
                obj.borderColor = '#FD9E5FCC'
                break
              case 4:  // 事件
                obj.backgroundColor = '#FF6E73CC'
                obj.borderColor = '#FF6E73CC'
                break
            }
            obj.title = i.name
            obj.start = i.start_time
            obj.end = i.end_time
            obj.type = i.type
            obj.summary = i.summary
            obj.editable = false
            this.events.push(obj)
          }
        } else {
          this.$message.error(res.data.meta.message)
        }
      }).catch((err) => {
        console.error(err)
      })
    },
    methods: {
      dayClick (e) {
      },
      eventSelected (e) {
        console.log(e)
        this.eventMsg.title = e.title
        this.eventMsg.summary = e.summary
        this.eventMsg.type = e.type
        if (e.end) {
          this.eventMsg.date = e.start._i + ' - ' + e.end._i
        } else {
          this.eventMsg.date = e.start._i
        }
      },
      envMouseOver (id, bg) {
        this.id = id
        this.backGround = bg
        this.events[id].backgroundColor = '#0004'
        console.log('envMouseOver')
      },
      envMouseOut () {
        console.log('envMouseOut')
        this.events[this.id].backgroundColor = this.backGround
      }
    }
  }
</script>
<style scoped>

</style>
