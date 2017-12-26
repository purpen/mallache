<template>
  <div class="container">
    <div class="exhibition">
      <div class="blank20"></div>
      <el-row :gutter="24" class="anli-elrow">
        <v-menu currentName="exhibition" v-if="menuStatus !== 'tools' || !isMob"></v-menu>
        <ToolsMenu v-if="menuStatus === 'tools' && isMob" currentName="exhibition"></ToolsMenu>
        <el-col :span="isMob ? 24 : 20"
                v-loading.body="loading" :style="{paddingTop: '50px'}">
          <vCalendar
            :events="events"
            @event-click="eventClick"
            @day-click="dayClick"
            @event-selected="eventSelected"
            @update-date="updateDate">
          </vCalendar>
        </el-col>
      </el-row>
    </div>
  </div>
</template>
<script>
  import api from '@/api/api'
  import vMenu from '@/components/pages/v_center/Menu'
  import ToolsMenu from '@/components/pages/v_center/ToolsMenu'
  export default {
    name: 'exhibition',
    components: {
      vCalendar: (resolve) => {
        require(['@/components/pages/v_center/Tools/calendar/calendar'], resolve)
      },
      vMenu,
      ToolsMenu
    },
    data () {
      return {
        loading: false,
        events: [],
        id: '',
        backgroundColor: ''
      }
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      },
      menuStatus () {
        return this.$store.state.event.menuStatus
      }
    },
    created () {
      this.getDate()
    },
    methods: {
      getDate (view = 'month', date) {
        this.loading = true
        let method = ''
        if (view === 'month') {
          method = api.dateOfAwardMonth
        } else {
          date = ''
          method = api.dateOfAwardWeek
        }
        this.$http.get(method, {params: {yearMonth: date}}).then((res) => {
          this.loading = false
          if (res.data.meta.status_code === 200) {
            if (res.data.data.length) {
              this.events = []
              for (let i of res.data.data) {
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
                obj.type_value = i.type_value
                obj.summary = i.summary
                this.events.push(obj)
              }
            }
          } else {
            this.$message.error(res.data.meta.message)
          }
        }).catch((err) => {
          this.loading = false
          console.error(err)
        })
      },
      dayClick (e) {
      },
      eventClick (e) {
      },
      eventSelected (e) {
      },
      updateDate (view, date) {
        this.getDate(view, date)
      }
    }
  }
</script>
<style scoped>

</style>
