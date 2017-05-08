<template>
  <div class="container">

    <el-row :gutter="24">
      <v-menu></v-menu>

      <el-col :span="20">
        <div class="content">
          <h1>这是控制台</h1>
        </div>
      </el-col>
    </el-row>

  </div>
</template>

<script>
// import api from '@/api/api'
import vMenu from '@/components/admin/Menu'
export default {
  name: 'admin_dash_board',
  components: {
    vMenu
  },
  data () {
    return {
      msg: ''
    }
  },
  methods: {
  },
  created: function() {
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
