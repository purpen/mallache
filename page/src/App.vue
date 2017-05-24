<template>
  <div id="app">
    <v-header></v-header>
    <div class="container-test">
      <router-view></router-view>   
    </div>

    <v-footer></v-footer>
  </div>
</template>

<script>
  import vHeader from '@/components/block/Header'
  import vFooter from '@/components/block/Footer'

  export default {
    name: 'app',
    components: {
      vHeader,
      vFooter
    },
    data () {
      return {
        screenWidth: document.body.clientWidth   // 这里是给到了一个默认值 （这个很重要）
      }
    },
    methods: {

    },
    computed: {

    },
    watch: {
      screenWidth (val) {
        if (!this.timer) {
          this.screenWidth = val
          this.timer = true
          let that = this
          setTimeout(function () {
            // that.screenWidth = that.$store.state.canvasWidth
            console.log(that.screenWidth)
            // that.init()
            that.timer = false
          }, 10)
        }
      }
    },
    mounted () {
      const that = this
      window.onresize = () => {
        return (() => {
          window.screenWidth = document.body.clientWidth
          that.screenWidth = window.screenWidth
          // window.document.getElementsByTagName('body')[0].style.width = that.screenWidth + 'px'
        })()
      }
    }
  }
</script>

<style>

</style>
