<template>
  <div class="container">
    <div v-html="toHtml">

    </div>
  </div>
</template>

<script>
export default {
  name: 'blank',
  data () {
    return {
      toHtml: '',
      msg: ''
    }
  },
  created: function() {
    this.toHtml = '<div></div><h1>1111111sdfsfasdfasfasfasdf</h1>'
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>


</style>
