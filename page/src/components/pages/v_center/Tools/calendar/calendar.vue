<template>
  <div :class="['calendar', {'m-calendar' : isMob}]">
    <div :class="['header', 'clearfix', {'mHeader' : isMob}]" ref="header">
      <div class="fl fc-button-group fc-button-group-fl">
        <a type="button" @click="prev"
                class="fc-prev-button fc-button fl fc-state-default fc-corner-left fc-state-hover">
          <span
            class="fx-icon-nothing-left"></span>
        </a>
        <a type="button" @click="todays"
                :class="['fc-today-button', 'fc-button', 'fl', 'fc-state-default']" :disabled="isToday">今天
        </a>
        <a type="button" @click="next"
                class="fc-next-button fc-button fl fc-state-default fc-corner-right">
          <span
            class="fx-icon-nothing-right"></span>
        </a>
      </div>
      <div class="fr fc-button-group fc-button-group-fr">
        <a type="button" @click="changeView('month')"
                :class="['fc-month-button','fc-button', 'fl', 'fc-state-default', 'fc-corner-left',
                view === 'month' ? 'fc-state-active' : '']">
          月
        </a>
        <a type="button" @click="changeView('basicWeek')"
                :class="['fc-basicWeek-button','fc-button', 'fl', 'fc-state-default', 'fc-corner-right',
                view === 'basicWeek' ? 'fc-state-active' : '']">
          周
        </a>
      </div>
      <div class="fc-button-group fc-button-group-center" v-if="isMob">{{eventMsg.headtitle}}</div>
      <div class="fc-button-group fc-button-group-center" v-else>{{eventMsg.month}}</div>
    </div>
    <full-calendar ref="calendar"
                   @event-selected="eventSelected"
                   :header="header"
                   :events="events"
                   :config="config"
    ></full-calendar>
    <div id="info" class="info" v-show="hideinfo" ref="info">
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
        isToday: true,
        view: 'month',
        bg: '',
        hideinfo: false,
        eventMsg: {
          month: '',
          date: '',
          headtitle: '',
          title: '',
          summary: '',
          tips: ''
        },
        eventSources: [],
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
            if (docWidth < 700) {
              $('html,body').animate({scrollTop: $('.calendar').height()}, 'slow')
            }
            self.eventMsg.title = event.title
            self.eventMsg.summary = event.summary
            self.eventMsg.tips = event.type_value
            if (event.end) {
              event.end._i = event.end._i.split(' ')[0] // 暂时隐藏具体时间
              self.eventMsg.date = event.start._i + ' - ' + event.end._i
            } else {
              self.eventMsg.date = event.start._i
            }
            switch (event.type) {
              case 1:
                self.$refs.title_ico.style.background = 'url(' +
                  require(`@/assets/images/tools/calendar/Contest@2x.png`) + ') no-repeat left'
                self.changeColor('rgba(101, 166, 255, 0.8)', 'blue')
                break
              case 2:
                self.$refs.title_ico.style.background = 'url(' +
                  require(`@/assets/images/tools/calendar/festival@2x.png`) + ') no-repeat left'
                self.changeColor('rgba(103, 212, 150, 0.8)', 'green')
                break
              case 3:
                self.$refs.title_ico.style.background = 'url(' +
                  require(`@/assets/images/tools/calendar/exhibition@2x.png`) + ') no-repeat left'
                self.changeColor('rgba(253, 158, 95, 0.8)', 'orange')
                break
              case 4:
                self.$refs.title_ico.style.background = 'url(' +
                  require(`@/assets/images/tools/calendar/Event@2x.png`) + ') no-repeat left'
                self.changeColor('rgba(255, 110, 115, 0.8)', 'red')
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
        }
      }
    },
    created () {
    },
    components: {
      FullCalendar
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      }
    },
    mounted() {
      this.getView()
      this.$emit('update-date', this.eventMsg.month)
      window.addEventListener('resize', this.winResize)
      this.$refs.header.style.width = this.$refs.calendar.$refs.calendar.offsetWidth + 'px'
    },
    methods: {
      winResize () {
        this.$refs.header.style.width = this.$refs.calendar.$refs.calendar.offsetWidth + 'px'
      },
      prev (e) {
        this.isToday = false
        this.hideinfo = false
        this.$refs.calendar.fireMethod('prev')
        this.getView()
        this.$emit('update-date', this.eventMsg.month)
      },
      next (e) {
        this.isToday = false
        this.hideinfo = false
        this.$refs.calendar.fireMethod('next')
        this.getView()
        this.$emit('update-date', this.eventMsg.month)
      },
      todays (e) {
        this.isToday = true
        this.hideinfo = false
        this.$refs.calendar.fireMethod('today')
        this.getView()
        this.$emit('update-date')
      },
      changeView (method) {
        this.view = method
        this.hideinfo = false
        this.$refs.calendar.fireMethod('changeView', method)
      },
      getView () {
        this.eventMsg.month = this.$refs.calendar.fireMethod('getView').title.replace(/(年)\s(\d)/g, '$1$2')
        let arr = this.$refs.calendar.fireMethod('getView').title.match(/\d+/g)
        switch (arr.length) {
          case 2:
            this.eventMsg.headtitle = `${arr[0]}.${arr[1]}`
            break
          case 3:
            this.eventMsg.headtitle = `${arr[0]}.${arr[1]}-${arr[2]}`
            break
          case 4:
            if (arr[0] === arr[2]) {
              this.eventMsg.headtitle = `${arr[0]}`
            } else {
              this.eventMsg.headtitle = `${arr[0]} - ${arr[2]}`
            }
            break
        }
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
          require(`@/assets/images/tools/calendar/time-${color}@2x.png`) + ') no-repeat left 3px'
        this.$refs.date.style.backgroundSize = '15px'
        this.$refs.summary.style.background = 'url(' +
          require(`@/assets/images/tools/calendar/details-${color}@2x.png`) + ') no-repeat left 3px'
        this.$refs.summary.style.backgroundSize = '15px'
        this.$refs.tips.style.background = 'url(' +
          require(`@/assets/images/tools/calendar/Label-${color}@2x.png`) + ') no-repeat left 3px'
        this.$refs.tips.style.backgroundSize = '15px'
      },
      eventCreated (...e) {
      }
    },
    beforeDestroy () {
      this.$off('remove-event')
      this.$off('rerender-events')
      this.$off('refetch-events')
      this.$off('render-event')
      this.$off('reload-events')
      this.$off('rebuild-sources')
    },
    destroyed () {
      window.removeEventListener('resize', this.winResize)
    }
  }
</script>
<style scoped>
  .header {
    position: relative;
    overflow: hidden;
    width: 100%;
    text-align: center;
    font-size: 16px;
    line-height: 26px;
    margin-top: 6px;
    padding: 8px 0;
  }

  .mHeader {
    position: relative;
    /* margin-top: -14px; */
  }
  .fc-button-group {
    font-size: 14px;
  }

  .fc-button-group a{
    cursor: pointer;
  }

  .fc-button-group-fl {
    padding-top: 4px;
  }

  .fc-button-group-fr {
    display: flex;
    justify-content: center;
  }

  .fc-button-group-center {
    display: block;
    padding: 0 80px 0 114px;
    line-height: 28px;
    font-size: 17px;
  }

  .fc-button-group .fc-state-default {
    padding: 0;
    background: #FFFFFF;
    border: 1px solid #979797;
    box-shadow: none;
    height: 22px;
  }

  .fc-button-group .fc-prev-button,
  .fc-button-group .fc-next-button {
    border-radius: 50%;
    color: #999999;
    border: none;
  }

  .fx-icon-nothing-left, .fx-icon-nothing-right {
    margin-right: 0;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 22px;
    height: 22px;
    font-size: 14px;
    line-height: 22px;
    border: 1px solid #979797;
  }

  .fx-icon-nothing-left{
    padding-right: 2px
  }
  .fx-icon-nothing-right{
    padding-left: 2px
  }

  .fc-button-group .fc-today-button:hover,
  .fx-icon-nothing-left:hover, .fx-icon-nothing-right:hover {
    color: #FF5D62;
    border-color: #FF5D62;
  }

  .fc-button-group .fc-prev-button:active .fx-icon-nothing-left,
  .fc-button-group .fc-next-button:active .fx-icon-nothing-right,
  .fc-button-group .fc-today-button:active {
    color: #FFFFFF;
    background-color: #FFACAF;
    border-color: #FF5D62;
  }

  .fc-button-group .fc-today-button {
    margin: 0 12px !important;
    border-radius: 10px;
    padding: 0 8px;
    color: #999999;
    height: 22px;
    line-height: 20px;
  }

  .fc-button-group .fc-month-button,
  .fc-button-group .fc-basicWeek-button {
    margin-left: -1px;
    height: 28px;
    line-height: 28px;
    padding: 0 12px;
    color: #FF5A5F;
    border-color: #FF5A5F;
  }

  .fc-button-group .fc-state-active {
    background: #FFACAF;
    color: #FFFFFF;
    border-color: #FF5A5F;
  }

  .fc-button-group .fc-basicWeek-button.fc-state-default {
    border-color: #FF5A5F;
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
    background: rgba(103, 212, 150, 0.8);
    border-radius: 4px 4px 0 0;
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
    background: url('../../../../../assets/images/tools/calendar/time-green@2x.png') no-repeat left 3px;
    background-size: 15px;
  }

  .date span {
    padding-right: 6px;
  }

  .summary {
    background: url('../../../../../assets/images/tools/calendar/details-green@2x.png') no-repeat left 3px;
    background-size: 15px;
  }

  .tips {
    background: url('../../../../../assets/images/tools/calendar/Label-green@2x.png') no-repeat left 3px;
    background-size: 15px;
  }

  @media screen and (max-width: 700px) {
    .info {
      position: static;
      margin-top: 20px;
      width: 100%;
      max-width: 100%;
    }
    .fc-button-group .fc-today-button {
      margin: 0 8px !important;
    }
  }
</style>

