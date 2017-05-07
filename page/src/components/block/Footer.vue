<template>
  <div class="container footer">
    <div class="line"></div>
    <el-row :gutter="24">
      <el-col class="item" :span="6">
        <p class="title">太火鸟 SaaS</p>
        <p><router-link :to="{}" class="">关于我们</router-link></p>
        <p><router-link :to="{}" class="">联系我们</router-link></p>
        <p><router-link :to="{}" class="">公司介绍</router-link></p>
      </el-col>

      <el-col class="item" :span="6">
        <p class="title">服务</p>
        <p><router-link :to="{}" class="">工作流程</router-link></p>
        <p><router-link :to="{}" class="">媒体报道</router-link></p>
      </el-col>

      <el-col class="item" :span="6">
        <p class="title">帮助中心</p>
        <p><router-link :to="{}" class="">常见问题</router-link></p>
        <p><router-link :to="{}" class="">服务条款</router-link></p>
        <p><router-link :to="{}" class="">网站地图</router-link></p>
        <p><router-link :to="{}" class="">友情链接</router-link></p>
      </el-col>

      <el-col class="item" :span="6">
        <p class="title">关注我们</p>
        <div class="share-box">
          <p class="share"><router-link :to="{}" class=""><i class="fa fa-weixin fa-2x" aria-hidden="true"></i></router-link></p>
          <p class="share"><router-link :to="{}" class=""><i class="fa fa-weibo fa-2x" aria-hidden="true"></i></router-link></p>
          <p class="share"><router-link :to="{}" class=""><i class="fa fa-qq fa-2x" aria-hidden="true"></i></router-link></p>
        </div>
      </el-col>
    </el-row>

  </div>
</template>

<script>
export default {
  name: 'test',
  data () {
    return {
      msg: ''
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
