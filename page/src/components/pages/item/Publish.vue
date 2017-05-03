<template>
  <div class="container">

    <div class="publish-box">
      <p class="success-img"><img src="../../../assets/images/icon/success.png" /></p>
      <p class="success-str">发布成功</p>
      <p class="success-des">项目已发布成功，24小时内我们会为智能匹配合适的设计公司。</p>
      <p>
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
        msg: ''
      }
    },
    methods: {
    },
    computed: {
    },
    created: function() {
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

</style>
