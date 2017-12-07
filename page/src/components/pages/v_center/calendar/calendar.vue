<template>
  <div ref="calendar" @event-selected="eventSelected" class="calendar"></div>
</template>

<script>
  import defaultsDeep from 'lodash.defaultsdeep'
  import 'fullcalendar'
  import $ from 'jquery'
  export default {
    props: {
      events: {
        default() {
          return [
            {
              title: 'event1',
              start: '2017-12-01',
              backgroundColor: '#FF9650',
              borderColor: '#FF9650'
            },
            {
              title: 'event2',
              start: '2017-12-05',
              end: '2017-12-12',
              backgroundColor: '#FF6E73',
              borderColor: '#FF6E73'
            },
            {
              title: 'event3',
              start: '2017-12-09T12:30:00',
              allDay: false,
              backgroundColor: '#FF6E73',
              borderColor: '#FF6E73'
            }
          ]
        }
      },
      eventSources: {
        default() {
          return []
        }
      },
      editable: {
        default() {
          return true
        }
      },
      selectable: {
        default() {
          return true
        }
      },
      selectHelper: {
        default() {
          return true
        }
      },
      header: {
        default() {
          return {
            left: 'prev,today,next,',
            center: 'title',
            right: 'month,basicWeek'
          }
        }
      },
      defaultView: {
        default() {
          return 'month'
        }
      },
      sync: {
        default() {
          return false
        }
      },
      config: {
        type: Object,
        default() {
          return {}
        }
      }
    },
    computed: {
      defaultConfig() {
        const self = this
        const cal = $(this.$el)
        return {
          header: this.header,
          defaultView: this.defaultView,
          editable: this.editable,
          selectable: this.selectable,
          selectHelper: this.selectHelper,
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
          events: this.events,
          eventSources: this.eventSources,
          eventRender(...args) {
            if (this.sync) {
              self.events = cal.fullCalendar('clientEvents')
            }
            self.$emit('event-render', ...args)
          },
          eventDestroy(event) {
            if (this.sync) {
              self.events = cal.fullCalendar('clientEvents')
            }
          },
          eventClick(...args) {
            console.log('ev')
            self.$emit('event-selected', ...args)
          },
          eventDrop(...args) {
            self.$emit('event-drop', ...args)
          },
          eventResize(...args) {
            self.$emit('event-resize', ...args)
          },
          dayClick(...args) {
            console.log('dayClick')
            self.$emit('day-click', ...args)
          },
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
    mounted() {
      const cal = $(this.$el)
      this.$on('remove-event', event => {
        if (event && event.hasOwnProperty('id')) {
          $(this.$el).fullCalendar('removeEvents', event.id)
        } else {
          $(this.$el).fullCalendar('removeEvents', event)
        }
      })
      this.$on('rerender-events', () => {
        $(this.$el).fullCalendar('rerenderEvents')
      })
      this.$on('refetch-events', () => {
        $(this.$el).fullCalendar('refetchEvents')
      })
      this.$on('render-event', event => {
        $(this.$el).fullCalendar('renderEvent', event)
      })
      this.$on('reload-events', () => {
        $(this.$el).fullCalendar('removeEvents')
        $(this.$el).fullCalendar('addEventSource', this.events)
      })
      this.$on('rebuild-sources', () => {
        $(this.$el).fullCalendar('removeEvents')
        this.eventSources.map(event => {
          $(this.$el).fullCalendar('addEventSource', event)
        })
      })
      cal.fullCalendar(defaultsDeep(this.config, this.defaultConfig))
    },
    methods: {
      fireMethod(...options) {
        return $(this.$el).fullCalendar(...options)
      },
      eventSelected() {
        console.log('eventClick')
      }
    },
    watch: {
      events: {
        deep: true,
        handler(val) {
          $(this.$el).fullCalendar('removeEvents')
          $(this.$el).fullCalendar('addEventSource', this.events)
        }
      },
      eventSources: {
        deep: true,
        handler(val) {
          this.$emit('rebuild-sources')
        }
      }
    },
    beforeDestroy() {
      this.$off('remove-event')
      this.$off('rerender-events')
      this.$off('refetch-events')
      this.$off('render-event')
      this.$off('reload-events')
      this.$off('rebuild-sources')
    }
  }
</script>
<
<style scoped>

</style>

