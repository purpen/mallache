<template>
  <div id="app">
    <v-header></v-header>
    <div class="container-test">
      <router-view :inputValue="calcHeight"></router-view>
    </div>
    <v-footer></v-footer>
  </div>
</template>

<script>
import vHeader from '@/components/block/Header'
import vFooter from '@/components/block/Footer'
import { calcImgSize } from 'assets/js/common'

export default {
  name: 'app',
  components: {
    vHeader,
    vFooter
  },
  data() {
    return {
      calcHeight: ''
    }
  },
  mounted() {
    window.onresize = () => {
      this.$store.commit('INIT_PAGE')
      this.calcHeight = calcImgSize()
    }
    this.calcHeight = calcImgSize()
  },
  created() {
    this.$store.commit('INIT_PAGE')
  },
  watch: {
  }
}
</script>

<style>

</style>
