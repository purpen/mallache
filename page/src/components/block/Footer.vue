<template>
  <div class="footer clear">
    <el-row>
      <el-col class="item" :xs="24" :sm="6" :md="6" :lg="6">
        <p class="title">铟果</p>
        <p>
          <router-link :to="{name: 'about'}">关于我们</router-link>
        </p>
        <p>
          <router-link :to="{name: 'contact'}">联系我们</router-link>
        </p>
      </el-col>

      <el-col class="item" :xs="24" :sm="6" :md="6" :lg="6">
        <p class="title">服务</p>
        <p>
          <router-link :to="{name: 'itemSubmitOne'}" class="">发布需求</router-link>
        </p>
        <p>
          <router-link :to="{name: 'serverDesign'}" class="">申请加入</router-link>
        </p>
      </el-col>

      <el-col class="item" :xs="24" :sm="6" :md="6" :lg="6">
        <p class="title">帮助</p>
        <p>
          <router-link :to="{name: 'trade'}" class="">交易保障</router-link>
        </p>
        <p>
          <router-link :to="{name: 'question'}" class="">常见问题</router-link>
        </p>
        <p>
          <router-link :to="{name: 'item'}" class="">服务条款</router-link>
        </p>
      </el-col>

      <el-col class="item" :xs="24" :sm="6" :md="6" :lg="6">
        <p class="title">关注我们</p>
        <div class="share-box">
          <p class="share">
            <router-link :to="{name: 'test'}" class="">
              <i class="fa fa-weixin fa-2x" aria-hidden="true"></i>
            </router-link>
          </p>
          <p class="share">
            <router-link :to="{name: 'test'}" class="">
              <i class="fa fa-weibo fa-2x" aria-hidden="true"></i>
            </router-link>
          </p>
          <p class="share">
            <router-link :to="{name: 'test'}" class="">
              <i class="fa fa-qq fa-2x" aria-hidden="true"></i>
            </router-link>
          </p>
        </div>
      </el-col>
    </el-row>

  </div>
</template>

<script>
export default {
  name: 'footer',
  props: {
    currentName: {
      default: ''
    }
  },
  data() {
    return {
      msg: ''
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
