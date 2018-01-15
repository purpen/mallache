<template>
  <div class="container">
    <div class="exhibition">
      <div class="blank20"></div>
      <el-row :gutter="24" class="anli-elrow">
        <v-menu currentName="exhibition" v-if="menuStatus !== 'tools' || !isMob"></v-menu>
        <ToolsMenu v-if="menuStatus === 'tools' && isMob" currentName="exhibition"></ToolsMenu>
        <el-col :span="isMob ? 24 : 20">
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
    methods: {
      getDate (date) {
        if (date) {
          let arr = date.match(/\d+/g)
          var date1 = ''
          var date2 = ''
          switch (arr.length) {
            case 2:
              date = arr.join('-')
              break
            case 3:
              date1 = `${arr[0]}-${arr[1]}`
              date2 = `${arr[0]}-${arr[2]}`
              break
            case 4:
              date1 = `${arr[0]}-${arr[1]}`
              date2 = `${arr[2]}-${arr[3]}`
              break
          }
        }
        let method = api.dateOfAwardMonth
        if (date1 && date2) {
          Promise.all([ // 显示周历时可能出现 12-31 -- 1,6 获取两个月的信息
            this.$http.get(method, {params: {yearMonth: date1}}),
            this.$http.get(method, {params: {yearMonth: date2}})])
            .then(([res1, res2]) => {
              if (res1.data.meta.status_code === 200) {
                this.events = this.unique(this.events.concat(this.addType (res1.data.data)))
              } else {
                this.$message.error(res1.data.meta.message)
              }
              if (res2.data.meta.status_code === 200) {
                this.events = this.unique(this.events.concat(this.addType (res2.data.data)))
              } else {
                this.$message.error(res2.data.meta.message)
              }
            })
            .catch((err) => {
              console.error(err)
            })
        } else {
          this.$http.get(method, {params: {yearMonth: date}})
          .then((res) => {
            if (res.data.meta.status_code === 200) {
              this.events = this.addType (res.data.data)
            } else {
              this.$message.error(res.data.meta.message)
            }
          }).catch((err) => {
            console.error(err)
          })
        }
      },
      addType (arr) { // 修改键名,添加颜色
        let events = []
        if (arr.length) {
          let a = 0
          for (let i of arr) {
            a++
            let obj = {}
            switch (i.type) {
              case 1:  // 大赛
                obj.backgroundColor = `rgba(101, 166, 255, ${this.colorOpacity(a)})`
                obj.borderColor = `rgba(101, 166, 255, ${this.colorOpacity(a)})`
                break
              case 2:  // 节日
                obj.backgroundColor = `rgba(103, 212, 150, ${this.colorOpacity(a)})`
                obj.borderColor = `rgba(103, 212, 150, ${this.colorOpacity(a)})`
                break
              case 3:  // 展会
                obj.backgroundColor = `rgba(253, 158, 95, ${this.colorOpacity(a)})`
                obj.borderColor = `rgba(253, 158, 95, ${this.colorOpacity(a)})`
                break
              case 4:  // 事件
                obj.backgroundColor = `rgba(255, 110, 115, ${this.colorOpacity(a)})`
                obj.borderColor = `rgba(255, 110, 115, ${this.colorOpacity(a)})`
                break
            }
            obj.id = i.id
            obj.title = i.name
            obj.start = i.start_time
            if (i.end_time) {
              obj.end = i.end_time + ' 23:59' // 修复时间显示少一天
            }
            obj.type = i.type
            obj.type_value = i.type_value
            obj.summary = i.summary
            events.push(obj)
          }
        }
        return events
      },
      dayClick (e) {
      },
      eventClick (e) {
      },
      eventSelected (e) {
      },
      updateDate (date) {
        this.getDate(date)
      },
      colorOpacity (a) {
        switch (a % 3) {
          case 0:
            return '0.85' // 0.85 ==> D8
          case 1:
            return '0.7' // 0.7 ==> B2
          case 2:
            return '0.55' // 0.55 ==> 8C
        }
      },
      unique (arr) { // 根据id 去重
        var result = {}
        var finalResult = []
        for (let i of arr) {
          result[i.id] = i
        }
        for (let j in result) {
          finalResult.push(result[j])
        }
        return finalResult
      }
    }
  }
</script>
<style scoped>

</style>
