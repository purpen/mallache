<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <el-col :span="8">
        <div class="progress">
          aaaa
        </div>

      </el-col>
        <div class="content">
        
        </div>

      <el-col :span="16">

      </el-col>
    <el-row>

  </div>
</template>

<script>
import api from '@/api/api'
export default {
  name: 'item_show',
  data () {
    return {
      item: '',
      msg: ''
    }
  },
  methods: {
  },
  created: function() {
    var id = this.$route.params.id
    if (!id) {
      this.$message.error('缺少请求参数!')
      return
    }
    const self = this
    self.$http.get(api.demandId.format(id), {})
    .then (function(response) {
      if (response.data.meta.status_code === 200) {
        self.item = response.data.data
        console.log(self.item)
      }
    })
    .catch (function(error) {
      self.$message.error(error.message)
      console.log(error.message)
    })
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .progress {
    height: 500px;
    border: 1px solid #ccc;
  }
  
  .content {
    height: 500px;
    border: 1px solid #ccc; 
  }


</style>
