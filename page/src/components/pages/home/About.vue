<template>
  <div class="">
    <div class="blank20"></div>
    <div class="container">
      <div class="top-menu-list">
        <router-link :to="{name: 'contact'}" class="item">联系我们</router-link>
        <router-link :to="{name: 'about'}" class="item is-active">关于我们</router-link>
      </div>
    </div>

    <div class="banner">
      <h3>铟果的使用 重塑设计尊严</h3>
    </div>

    <div class="container">
      <el-row :gutter="20">

        <el-col :span="24">
          <div class="h-right-content">

            <div class="content-box">
              <h3>认识铟果</h3>
              <p>太火鸟旗下，基于大数据和智能匹配技术的专业设计产品创新交易平台。致力于充分服务有设计需求的项目方和设计服务供应商，植根于双方需求，广泛发掘市场设计需求，帮助先进设计理念匹配优质设计力量，打造互联网+时代线上设计新生态并提供可持续发展给养</p>
            </div>

          </div>

        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script>

  export default {
    name: 'about',
    components: {
    },
    data () {
      return {
        test: ''
      }
    },
    methods: {
    },
    created: function() {
      document.body.scrollTop = 0
      document.documentElement.scrollTop = 0
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .container {
    min-height: ;
  }

  .banner {
    width: 100%;
    height: 400px;
    background: url('../../../assets/images/home/about_banner.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    text-align: center;
  }

  .banner h3 {
    padding-top: 150px;
    padding-bottom: 10px;
    font-size: 5rem;
    color: #fff;
    line-height: 2;
    font-weight: 300;
  }
  .banner p {
    font-size: 3rem;
    color: #fff;
    font-weight: 300;
    padding: 0 150px;
  }

  .content-box {
    padding: 20px 120px;
  }
  .content-box h3 {
    font-size: 1.8rem;
    line-height: 2;
    margin: 10px 0;
  }


</style>
