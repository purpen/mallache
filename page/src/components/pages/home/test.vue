<!--
<template>
  <div class="test">
    <figure>
      <chart
        class="chart"
        :options="scoreRadar"
        :init-options="initOptions"
        auto-resize
      />
    </figure>
  </div>
</template>
<script> 
-->
// export default {
//   name: 'test',
//   data() {
//     let scores = [
//       {name: '进攻', max: 20, value: 19},
//       {name: '防守', max: 20, value: 9},
//       {name: '速度', max: 20, value: 18},
//       {name: '力量', max: 20, value: 16},
//       {name: '耐力', max: 20, value: 16},
//       {name: '敏捷', max: 20, value: 20}
//     ]
//     return {
//       scoreRadar: {
//         tooltip: {},
//         radar: {
//           indicator: scores.map(({name, max}) => {
//             return {name, max}
//           })
//         },
//         series: [{
//           name: '能力值',
//           type: 'radar',
//           data: [{value: scores.map(({value}) => value)}]
//         }]
//       },
//       initOptions: {
//         opts: {
//           width: 100,
//           height: 100,
//           renderer: 'canvas'
//         }
//       }
//     }
//   }
// }
// </script>
<style scoped>
/*  .test .chart {
    width: 200px;
    height: 100px;
  } */
</style>
