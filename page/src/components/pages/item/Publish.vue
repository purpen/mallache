<template>
  <div class="container">

    <div class="publish-box">
      <div v-if="verifyStatus === 1">
        <p class="success-img"><img src="../../../assets/images/icon/success_2x.png" width="80" /></p>
        <p class="success-str">发布成功</p>    
      </div>
      <div v-else>
        <p class="success-img"><img src="../../../assets/images/icon/warning_2x.png" width="80" /></p>
        <p class="success-str">未认证</p>    
      </div>

      <p class="success-des" v-if="verifyStatus === 0">发布项目，需认证公司信息，请你尽快认证</p>
      <p class="success-des" v-else>系统已经成功推送项目消息，等待设计公司接单，一旦有消息我们会及时通知您</p>
      <p v-if="verifyStatus === 0">
        <router-link :to="{name: 'vcenterDCompanyAccreditation'}" class="renzheng"><el-button class="is-custom" type="primary">去认证</el-button></router-link>
      </p>
      <p v-else>
        <router-link :to="{name: 'home'}" class="item"><el-button class="is-custom">返回首页</el-button></router-link>
        <router-link :to="{name: 'vcenterItemList'}" class="item"><el-button class="is-custom" type="primary">查看项目</el-button></router-link>
      </p>
    </div>

  </div>
</template>

<script>
  export default {
    name: 'item_publish',
    components: {
    },
    data () {
      return {
        verifyStatus: 0,
        msg: ''
      }
    },
    methods: {
    },
    computed: {
    },
    created: function() {
      var verifyStatus = this.$route.query.verify_status
      if (!verifyStatus) {
        verifyStatus = 0
      }
      this.verifyStatus = parseInt(verifyStatus)
    },
    watch: {
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .publish-box{
    width: 80%;
    height: 400px;
    text-align:center;
    margin: 30px auto 30px auto;
    padding: 100px 0 0 0;
    border: 1px solid #ccc;
  }

  .success-img {
    margin-bottom: 15px;
  }
  .success-img img {
    
  }
  .success-str {
    font-size: 2rem;
    margin-bottom: 20px;
  }
  .success-des {
    color: #666;
    font-size: 1rem;
    margin-bottom: 25px;
  }
  .red {
    color: red;
  }
  a.item button {
    padding: 10px 20px 10px 20px;
    margin: 5px;
  }
  a.renzheng button {
    padding: 10px 20px;
  }

</style>
