<template>
  <div id="app">
    <v-header></v-header>
    <div class="main">
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
  data() {
    return {
    }
  },
  watch: {
  },
  created() {
  }
}
</script>

<style>

</style>
