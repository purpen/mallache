<template>
  <div class="content-box">

    <el-carousel :interval="5000" height="650px">
      <el-carousel-item v-for="item in slideList" :key="item">
        <div class="slide" :style="{ 'background-image': 'url(' + item.image + ')'}">
          <h3>{{ item.title }}</h3>
          <p>{{ item.desc }}</p>
        </div>
      </el-carousel-item>
    </el-carousel>

    <div class="container">
      <div class="item_1 item">
        <h3>提供专业设计解决方案</h3>
        <el-row :gutter="0">
          <el-col :span="12">
            <div class="item_1_l">
              <img src="../../../assets/images/home/index_01.png" width="150" />
              <p class="item_1_title font-weight5">产品设计</p>
              <p class="item_1_desc">产品策略／产品外观设计／结构设计</p>
            </div>
          </el-col>
          <el-col :span="12">
            <div class="item_1_r">
              <img src="../../../assets/images/home/index_02.png" width="150" />
              <p class="item_1_title font-weight5">UI/UE设计</p>
              <p class="item_1_desc">App设计／网页设计／界面设计／服务设计／用户体验咨询</p>
            </div>
          </el-col>
        </el-row>

        <div class="pub" v-if="uType !== 2">
          <router-link :to="{name: 'itemSubmitOne'}">
            <el-button class="pub-btn is-custom" type="primary" size="large">发布项目</el-button>
          </router-link>
        </div>
      </div>
    </div>


    <div class="item item_2">
      <div class="item_2_box">
        <h3>铟果优势</h3>
        <el-row :gutter="24">
          <el-col :span="8">
            <div class="">
              <img src="../../../assets/images/home/index_03.png" />
              <p class="item_1_title">智能</p>
              <p class="item_1_desc">通过云平台+大数据计算，帮您智能匹配设计服务供应商</p>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="">
              <img src="../../../assets/images/home/index_04.png" />
              <p class="item_1_title">安全</p>
              <p class="item_1_desc">资金托管、时间戳、供应商信用评级为交易提供全方位的安全保障 </p>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="">
              <img src="../../../assets/images/home/index_05.png" />
              <p class="item_1_title">高效</p>
              <p class="item_1_desc">重构产品创新流程，提供高效、便捷的设计体验 </p>
            </div>
          </el-col>
        </el-row>
      </div>
    </div>

    <div class="container">
      <div class="item item_3">
        <h3>铟果案例</h3>
        <el-row :gutter="20">
          <el-col :span="12">
            <el-card class="box-card">
              <div class="avatar-header">
                <div class="avator-box">
                  <router-link :to="{name: 'companyShow', params: {id: 7}}" target="_blank"><img src="http://oni525j96.bkt.clouddn.com/saas/20170504/590af689c88cd-p180x180.jpg" class="avatar" width="60" /></router-link>
                </div>
                <div class="avator-content">
                  <p class="avatar-title">
                    <router-link :to="{name: 'companyShow', params: {id: 7}}" target="_blank">YANG DESIGN</router-link>
                  </p>
                  <p class="avatar-des">全球四大设计奖项的顾问</p>
                </div>
              </div>
              <div class="company-des">
                <p>YANG DESIGN（杨设计顾问）为中国最具前瞻思维的设计顾问公司，也是中国首家囊获包括德国红点、iF、日本 G-Mark、美国 IDEA 在内全球四大设计奖项的顾问公司，至今累计获得了近百项设计大奖。 </p>      
              </div>
            </el-card>
          </el-col>
          <el-col :span="12">
            <el-card class="box-card">
              <div class="avatar-header">
                <div class="avator-box">
                  <router-link :to="{name: 'companyShow', params: {id: 20}}" target="_blank"><img src="http://oni525j96.bkt.clouddn.com/saas/20170525/592635419f3e7-p180x180.jpg" class="avatar" width="60" /></router-link>
                </div>
                <div class="avator-content">
                  <p class="avatar-title">
                    <router-link :to="{name: 'companyShow', params: {id: 7}}" target="_blank">北京一流设计有限公司</router-link>
                    </p>
                  <p class="avatar-des">全球四大设计奖项的顾问</p>
                </div>
              </div>
              <div class="company-des">
                <p>YANG DESIGN（杨设计顾问）为中国最具前瞻思维的设计顾问公司，也是中国首家囊获包括德国红点、iF、日本 G-Mark、美国 IDEA 在内全球四大设计奖项的顾问公司，至今累计获得了近百项设计大奖。 </p>      
              </div>
            </el-card>
          </el-col>
        </el-row>
      </div>
    </div>

    <div class="item item_4">
      <h3>战略合作</h3>
      <div>
        <img src="../../../assets/images/home/jdjr_logo.png" width="150" />
      </div>
      <div class="blank40"></div>
      <h3>合作伙伴</h3>
      <div class="logo-list">
        <img src="../../../assets/images/home/1logo.png" />
        <img src="../../../assets/images/home/2logo.png" />
        <img src="../../../assets/images/home/3logo.png" />
        <img src="../../../assets/images/home/4logo.png" />
      </div>
    </div>

    </div>

  </div>
</template>

<script>

export default {
  name: 'index',
  data () {
    return {
      uType: this.$store.state.event.user.type || 1,
      slideList: [
        {
          'clickUrl': '#',
          'title': '铟果',
          'desc': '用设计重塑品质生活',
          'image': require('@/assets/images/home/banner1.jpg')
        },
        {
          'clickUrl': '#',
          'title': '专业设计资源',
          'desc': '汇聚100+国内专业设计服务商，满足你的创意需求',
          'image': require('@/assets/images/home/banner2.jpg')
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
  .content-box {
  }
  .container{
  }

  .slide {
    color: #475669;
    font-size: 18px;
    line-height: 450px;
    height: 650px;
    margin: 0;
    background-size: cover;
    background-repeat: no-repeat;
    text-align: center;
  }

  .slide h3 {
    font-size: 5rem;
    color: #fff;
    line-height: 1.5;
    font-weight: 300;
    padding: 250px 0 0 0;
  }
  .slide p {
    font-size: 4rem;
    color: #fff;
    font-weight: 300;
    padding: 0;
  }

  .item h3 {
    font-size: 2.8rem;
    padding-bottom: 80px;
  }

  .item_1_l {
    margin-left: 120px;
  }
  .item_1_r {
    margin-right: 120px;
  }
  .item_1_title {
    color: #222;
    font-size: 2rem;
    margin-top: 20px;
    margin-bottom: 10px;
    line-height: 2;
  }
  .item_1_desc {
    color: #666;
    font-size: 1.5rem;
    line-height: 1.5;
    padding: 0 20px;
  }

  .pub {
    margin: 80px 0 0 0;
  }
  .pub .pub-btn {
    padding: 20px 80px 20px 80px;
  }

  .item_2 {
    margin-top: 50px;
    background-color: #FAFAFA;
  }

  .item_2_box {
    margin: 0 8%;
  }
  .item_2_box img {
    width: 35%;
  }

  .item_2 h3 {
    padding-bottom: 80px;
  }

  .item_3 {
  }
  .item_3 h3 {
    padding-bottom: 80px;
    color: #4B4B4B;
  }
  .item_3_back {

  }

  .item_4 {
    background-color: #FAFAFA;
  }
  .item_4 h3{
    padding-bottom: 40px;
  }

  .item {
    text-align: center;
    padding: 60px 0 60px 0;
  }

  .box-card {
    text-align: left;
    height: 250px;
    width: 100%;
  }

  .avatar-header {
    height: 60px;
    padding-bottom: 30px;
  }

  .avator-box {
    float: left;
    width: 60px;
  }

  .avator-content {
    float: left;
    margin: 0 0 0 20px;
    border-bottom: 1px solid #ccc;
    width: 80%;
    padding: 10px 0 20px 0;
  }
  .avator-content p {
    line-height: 1.5;
  }

  .avatar-title {
    color: #222;
    font-size: 1.5rem;
  }
  .avatar-des {
    font-size: 1.2rem;
    color: #666;
  }

  .company-des {
    clear: both;
  }
  .company-des p {
    color: 1.2rem;
    color: #666;
    line-height: 1.5;
  }

  .item_5 h3 {
    padding-bottom: 0px;
  }

  .logo-list {
    text-align: center;
  }
  .logo-list img {
    margin: 10px 35px;
    width: 100px;
  }

</style>
