<template>
  <h1 v-html="message">
  </h1>
</template>

<script>
export default {
  name: 'notfind',
  data() {
    return {
      message: '404 not find'
    }
  }
}
</script>

<style scoped>
h1 {
  color: #e03030;
  font-size: 48px;
  text-align: center;
  height: 100%;
  padding-top: 10%;
}
</style>

