<template>
  <div class="nofind">
    <h1 v-html="message">
    </h1>
    <h2>{{msg}}</h2>
  </div>
</template>

<script>
  export default {
    name: 'notfind',
    data() {
      return {
        message: 'HTTP 404……',
        msg: '可能这个页面已经飞走了'
      }
    }
  }
</script>

<style scoped>
  .nofind {
    width: 300px;
    margin: 0 auto;
    text-align: left;
  }

  h1 {
    color: #666;
    font-size: 48px;
    height: 100%;
    padding-top: 30%;
  }

  h2 {
    color: #999;
    font-size: 20px;
    text-align: center;
    line-height: 2;
  }
</style>

