<template>
  <div class="test">
    <figure>
      <chart
        :options="scoreRadar"
        auto-resize
      />
    </figure>
  </div>
</template>
<script>
import ECharts from 'vue-echarts/components/ECharts.vue'
import 'echarts/lib/chart/bar'
import 'echarts/lib/component/tooltip'
import 'echarts/lib/chart/radar'
export default {
  name: 'test',
  data() {
    let scores = [
      {name: '进攻', max: 20, value: 19},
      {name: '防守', max: 20, value: 9},
      {name: '速度', max: 20, value: 18},
      {name: '力量', max: 20, value: 16},
      {name: '耐力', max: 20, value: 16},
      {name: '敏捷', max: 20, value: 20}
    ]
    return {
      scoreRadar: {
        title: {
          text: '能力雷达图'
        },
        tooltip: {},
        radar: {
          indicator: scores.map(({name, max}) => {
            return {name, max}
          })
        },
        series: [{
          name: '能力值',
          type: 'radar',
          data: [{value: scores.map(({value}) => value)}]
        }]
      }
    }
  },
  components: {
    'chart': ECharts
  }
}
</script>
<style>

</style>
