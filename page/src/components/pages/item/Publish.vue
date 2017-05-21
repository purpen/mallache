<template>
  <div class="container">

    <div class="publish-box">
      <p class="success-img"><img src="../../../assets/images/icon/success.png" /></p>
      <p class="success-str">发布成功</p>

      <p class="success-des red" v-if="verifyStatus === 0">项目已发布, 请先完成公司认证，系统才会为您匹配合适的设计公司</p>
      <p class="success-des" v-else>项目已发布成功，24小时内我们会为智能匹配合适的设计公司。</p>
      <p v-if="verifyStatus === 0">
        <router-link :to="{name: 'vcenterItemList'}" class="item">公司认证</router-link>
      </p>
      <p v-else>
        <router-link :to="{name: 'home'}" class="item">返回首页</router-link>
        <router-link :to="{name: 'vcenterItemList'}" class="item">项目管理</router-link>
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
    width: 100%;
    height: 500px;
    text-align:center;
    margin: 30px auto 30px auto;
    padding: 100px 0 0 0;
  }

  .success-img {
    margin-bottom: 15px;
  }
  .success-img img {
    
  }
  .success-str {
    font-size: 2rem;
    color: #00AC84;
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

</style>
