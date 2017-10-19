<template>
  <div class="">
    <div class="container">
      <div class="top-menu-list">
        <router-link :to="{name: 'contact'}" class="item">联系我们</router-link>
        <router-link :to="{name: 'about'}" class="item is-active">关于我们</router-link>
      </div>
    </div>

    <div class="banner">
      <h3>铟果的使命</h3>
      <h3>重塑设计尊严</h3>
    </div>

    <div class="container">
      <el-row :gutter="20">

        <el-col :span="24">
          <div class="h-right-content">

            <div class="content-box">
              <h3>认识铟果</h3>
              <p>太火鸟是中国首家产品创新和智能分发SaaS平台，以 [ 技术创新、设计创新、模式创新 ] 三轮驱动，致力于在「智能科技」和「生活美学」新消费升级趋势下，重构产品创新流程、提升孵化投资效率、增强营销分发势能；发掘既有「创新价值」又不失「感性设计」的中国原创产品和品牌；太火鸟科技的愿景是成为中国最大的产品创新发源地，打造科技美学创新产品的新消费生态。 </p>
              <p>&nbsp;</p>
              <p>2017年，太火鸟锐意转型，从中国领先的智能硬件孵化平台升级为以科技生活美学为引导、助力消费升级大趋势的资源集成式闭环的企业。于是，铟果应运而生。</p>
              <p>&nbsp;</p>
              <p>铟果是太火鸟旗下基于大数据和智能匹配的产品创新交易平台，致力于充分服务有设计需求的项目方和设计服务供应商，植根于双方痛点，广泛发掘市场设计需求，帮助先进设计理念匹配优质设计力量，打造互联网+时代线上设计新生态，并提供可持续发展给养。</p>
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
  data() {
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
  flex-wrap: wrap;
  align-content: center
}

.banner h3 {
  float: left;
  font-size: 5rem;
  color: #fff;
  line-height: 2;
  font-weight: 300;
  margin: 0 7px;
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


@media screen and (max-width: 767px) {
  .content-box {
    padding: 20px
  }
}
</style>
