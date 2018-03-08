<template>
  <div class="container blank40 min-height350">
    <v-menu></v-menu>
    <el-col></el-col>
  </div>
</template>
<script>
  import vMenu from '@/components/pages/v_center/cloud_drive/Menu'
  export default {
    name: 'cloud_drive',
    data() {
      return {
        test: 'test'
      }
    },
    components: {
      vMenu
    }
  }
</script>
<style scoped>

</style>
