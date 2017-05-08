<template>
  <div class="content-box">

    <el-carousel :interval="5000" height="420px">
      <el-carousel-item v-for="item in slideList" :key="item">
        <div class="slide" :style="{ 'background-image': 'url(' + item.image + ')' }">
        </div>
      </el-carousel-item>
    </el-carousel>

    <div class="container">
      <div class="item_1">
        <h3>满足你的创意需求</h3>
        <el-row :gutter="24">
          <el-col :span="12">
            <div class="item_1_l">
              <img src="../../../assets/images/home/index_01.png" />
              <p class="item_1_title">产品设计</p>
              <p class="item_1_desc">产品创新设计／产品创新咨询</p>
            </div>
          </el-col>
          <el-col :span="12">
            <div class="item_1_r">
              <img src="../../../assets/images/home/index_02.png" />
              <p class="item_1_title">UI/UE设计</p>
              <p class="item_1_desc">App设计／网页设计</p>
            </div>
          </el-col>
        </el-row>
      </div>

      <div class="pub">
        <router-link :to="{name: 'itemSubmitOne'}">
          <el-button class="pub-btn is-custom" type="primary" size="large"><i class="el-icon-plus"></i> 发布项目</el-button>
        </router-link>
      </div>

      <div class="item_2">
        <h3>太火鸟SaaS</h3>
        <el-row :gutter="24">
          <el-col :span="8">
            <div class="">
              <img src="../../../assets/images/home/index_03.png" />
              <p class="item_1_title">智能</p>
              <p class="item_1_desc">So strongly and metaphysically did I conceive of my situation then, that while earnestly </p>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="">
              <img src="../../../assets/images/home/index_04.png" />
              <p class="item_1_title">安全</p>
              <p class="item_1_desc">received a mortal wound; and that another's mistake or misfortune might plunge innocent me into unmerited disaster and death. Therefore, </p>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="">
              <img src="../../../assets/images/home/index_05.png" />
              <p class="item_1_title">高效</p>
              <p class="item_1_desc">received a mortal wound; and that another's mistake or misfortune might plunge innocent me into unmerited disaster and death. Therefore, </p>
            </div>
          </el-col>
        </el-row>
      </div>

      <div class="item_3">
        <h3>优秀设计公司展示</h3>
        <el-row :gutter="24">
          <el-col :span="12">
            <div class="item_3_back">
              <img src="../../../assets/images/home/index_06.png" />
            </div>
          </el-col>
          <el-col :span="12">
            <div class="item_3_back" style="background-image: url(../../../assets/images/home/index_07.png)">
              <img src="../../../assets/images/home/index_07.png" />
            </div>
          </el-col>
        </el-row>
      </div>

    </div>

  </div>
</template>

<script>

export default {
  name: 'index',
  data () {
    return {
      slideList: [
        {
          'clickUrl': '#',
          'desc': 'nhwc',
          'image': 'https://p4.taihuoniao.com/asset/170508/590fe1783ffca2ec688b4902-1'
        },
        {
          'clickUrl': '#',
          'desc': 'hxrj',
          'image': 'https://p4.taihuoniao.com/asset/170508/590fe1783ffca2ec688b4902-2'
        }
      ],
      msg: ''
    }
  },
  mounted () {
  },

  watch: {
  },
  methods: {
  },
  created: function() {
  }
}

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .container{
  }

  .slide {
    color: #475669;
    font-size: 18px;
    line-height: 420px;
    height: 420px;
    margin: 0;
  }

  .item_1, .item_2, .item_3 {
    text-align: center;
    padding: 40px 0 40px 0;
  }
  .item_1 h3, .item_2 h3, .item_3 h3 {
    color: #666;
    font-size: 2rem;
    padding-bottom: 40px;
  }

  .item_1_l {
    margin-left: 50px;
  }
  .item_1_r {
    margin-right: 50px;
  }
  .item_1_title {
    color: #333;
    font-size: 1.5rem;
    margin-top: 30px;
    line-height: 2;
  }
  .item_1_desc {
    color: #666;
    font-size: 1rem;
    line-height: 1.5;
  }

  .pub {
    background: #FAFAFA;
    height: 200px;
    margin: 20px 0 10px 0;
    position: relative;
  }
  .pub .pub-btn {
    position: absolute;
    padding: 10px 30px 10px 30px;
    top: 40%;
    left: 45%;
  }

  .item_3 {
    background-color: #FAFAFA;
  }
  .item_3 h3 {
    color: #4B4B4B;
  }
  .item_3_back {

  }

</style>
