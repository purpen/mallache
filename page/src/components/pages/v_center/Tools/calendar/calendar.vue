<template>
  <div class="calendar">
    <div class="header clearfix">
      <span class="fl"></span>
      {{eventMsg.month}}
      <span class="fr"></span>
    </div>
    <full-calendar ref="calendar"
                   @event-selected="eventSelected"
                   @event-created="eventCreated"
                   :header="header"
                   :events="events"
                   :config="config"
    ></full-calendar>
    <div class="info" v-show="hideinfo" ref="info">
      <div class="title" ref="title">
        <i class="close" @click="hideInfo"></i>
        <p ref="title_ico">{{eventMsg.title}}</p>
      </div>
      <div class="info-body">
        <p class="date" ref="date"><span>时间:</span>{{eventMsg.date}}</p>
        <p class="summary" ref="summary">{{eventMsg.summary}}</p>
        <p class="tips" ref="tips">{{eventMsg.tips}}</p>
      </div>
    </div>
  </div>
</template>

<script>
  import { FullCalendar } from 'vue-full-calendar'
  import $ from 'jquery'

  export default {
    props: {
      events: {
        type: Array,
        required: true
      }
    },
    data () {
      const self = this
      return {
        bg: '',
        hideinfo: false,
        eventMsg: {
          month: '',
          date: '',
          title: '',
          summary: '',
          tips: ''
        },
        eventSources: [],
        //        header: {
        //          left: 'prev,today,next,',
        //          center: 'title',
        //          right: 'month,basicWeek'
        //        },
        header: {
          left: '',
          center: '',
          right: ''
        },
        config: {
          editable: false,
          selectable: true,
          selectHelper: true,
          defaultView: 'month',
          sync: false,
          aspectRatio: 1,
          timeFormat: 'HH:mm',
          titleFormat: 'YYYY年 MMM',
          buttonText: {
            today: '今天',
            month: '月',
            week: '周'
          },
          monthNames: [
            '1月',
            '2月',
            '3月',
            '4月',
            '5月',
            '6月',
            '7月',
            '8月',
            '9月',
            '10月',
            '11月',
            '12月'
          ],
          monthNamesShort: [
            '1月',
            '2月',
            '3月',
            '4月',
            '5月',
            '6月',
            '7月',
            '8月',
            '9月',
            '10月',
            '11月',
            '12月'
          ],
          dayNames: ['周一', '周二', '周三', '周四', '周五', '周六', '周日'],
          dayNamesShort: ['周一', '周二', '周三', '周四', '周五', '周六', '周日'],
          eventClick (event, jq, mouth) {
            let docWidth = $(window).width()
            // let mindocHeight = $(document).scrollTop()
            let maxdocHeight = $(window).height() + $(document).scrollTop()
            let infoWidth = $(self.$refs.info).width()
            let infoHeight = $(self.$refs.info).height()
            let top = jq.pageY - 50
            let left = jq.pageX - 100
            if (jq.pageX - 100 < 0) {
              left = 20
            }
            if (jq.pageX + infoWidth > docWidth) {
              left = docWidth - infoWidth - 100
            }
            if (jq.pageY + infoHeight > maxdocHeight) {
              top = jq.pageY - infoHeight - 90
            }
            self.hideinfo = true
            self.$refs.info.style.top = top + 'px'
            self.$refs.info.style.left = left + 'px'
            self.eventMsg.title = event.title
            self.eventMsg.summary = event.summary
            self.eventMsg.tips = event.type_value
            if (event.end) {
              self.eventMsg.date = event.start._i + ' - ' + event.end._i
            } else {
              self.eventMsg.date = event.start._i
            }
            switch (event.type) {
              case 1:
                self.$refs.title_ico.style.background = 'url(' +
                  require(`@/assets/images/tools/calendar/Contest@2x.png`) + ') no-repeat left'
                self.changeColor('#65A6FFCC', 'blue')
                break
              case 2:
                self.$refs.title_ico.style.background = 'url(' +
                  require(`@/assets/images/tools/calendar/festival@2x.png`) + ') no-repeat left'
                self.changeColor('#67D496CC', 'green')
                break
              case 3:
                self.$refs.title_ico.style.background = 'url(' +
                  require(`@/assets/images/tools/calendar/exhibition@2x.png`) + ') no-repeat left'
                self.changeColor('#FD9E5FCC', 'orange')
                break
              case 4:
                self.$refs.title_ico.style.background = 'url(' +
                  require(`@/assets/images/tools/calendar/Event@2x.png`) + ') no-repeat left'
                self.changeColor('#FF6E73CC', 'red')
                break
            }
          },
          eventResize (...args) {
            self.$emit('event-resize', ...args)
          },
          dayClick (e) {
            self.hideinfo = false
            self.$emit('day-click', e)
          }
          //          eventMouseover(event) {
          //            self.bg = event.backgroundColor
          //            event.backgroundColor = 'black'
          //            self.$refs.calendar.fireMethod('updateEvents', event)
          //          },
          //          eventMouseout (event) {
          //            event.backgroundColor = self.bg
          //            self.$refs.calendar.fireMethod('updateEvent', event)
          //          }
        }
      }
    },
    created () {
    },
    components: {
      FullCalendar
    },
    computed: {
      defaultConfig() {
        const self = this
        return {
          select(start, end, jsEvent, view, resource) {
            self.$emit('event-created', {
              start,
              end,
              allDay: !start.hasTime() && !end.hasTime(),
              view,
              resource
            })
          }
        }
      }
    },
    mounted() {},
    methods: {
      prevs (e) {
        console.log(this.$refs.calendar)
        this.$refs.calendar.fireMethod('prev')
      },
      fireMethod(...options) {
        return $(this.$el).fullCalendar(...options)
      },
      eventSelected() {
        console.log('eventSelected')
      },
      hideInfo () {
        this.hideinfo = !this.hideinfo
      },
      changeColor (bg, color) {
        this.$refs.title.style.background = bg
        this.$refs.title_ico.style.backgroundSize = '24px'
        this.$refs.date.style.background = 'url(' +
          require(`@/assets/images/tools/calendar/time-${color}@2x.png`) + ') no-repeat left'
        this.$refs.date.style.backgroundSize = '15px'
        this.$refs.summary.style.background = 'url(' +
          require(`@/assets/images/tools/calendar/details-${color}@2x.png`) + ') no-repeat left'
        this.$refs.summary.style.backgroundSize = '15px'
        this.$refs.tips.style.background = 'url(' +
          require(`@/assets/images/tools/calendar/Label-${color}@2x.png`) + ') no-repeat left'
        this.$refs.tips.style.backgroundSize = '15px'
      },
      eventCreated (...e) {
        console.log(e)
      }
    },
    beforeDestroy () {
      this.$off('remove-event')
      this.$off('rerender-events')
      this.$off('refetch-events')
      this.$off('render-event')
      this.$off('reload-events')
      this.$off('rebuild-sources')
    }
  }
</script>
<style scoped>
  .header {
    position: absolute;
    top: 16px;
    width: 100%;
    height: 30px;
    background: #6663;
  }

  .info {
    min-width: 260px;
    max-width: 360px;
    min-height: 220px;
    position: absolute;
    z-index: 9999;
    top: 284px;
    left: 603px;
    background: #FFFFFF;
    box-shadow: 0 0 7px 3px rgba(0, 0, 0, 0.20);
    border-radius: 4px;
  }

  .info .title {
    font-size: 14px;
    color: #FFFFFF;
    height: 60px;
    background: #67D496CC;
    position: relative;
    overflow: hidden;
  }

  .title p {
    margin-top: 24px;
    margin-left: 10px;
    text-indent: 30px;
    height: 24px;
    line-height: 24px;
    background: url("../../../../../assets/images/tools/calendar/festival@2x.png") no-repeat left;
    background-size: 24px;
  }

  .info .close {
    position: absolute;
    right: 10px;
    top: 10px;
    width: 15px;
    height: 15px;
    background: url('../../../../../assets/images/tools/calendar/Close@2x.png') no-repeat center;
    background-size: contain;
  }

  .info-body {
    font-size: 12px;
    padding: 12px;
    color: #666666;
    overflow: hidden;
  }

  .info-body p {
    text-indent: 24px;
    line-height: 1.5;
    margin-bottom: 6px;
  }

  .date {
    background: url('../../../../../assets/images/tools/calendar/time-green@2x.png') no-repeat left;
    background-size: 15px;
  }

  .date span {
    padding-right: 6px;
  }

  .summary {
    background: url('../../../../../assets/images/tools/calendar/details-green@2x.png') no-repeat left;
    background-size: 15px;
  }

  .tips {
    background: url('../../../../../assets/images/tools/calendar/Label-green@2x.png') no-repeat left;
    background-size: 15px;
  }
</style>

