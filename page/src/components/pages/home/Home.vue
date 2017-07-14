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
          <el-col :span="8">
            <el-card class="box-card" :body-style="{ padding: '0px' }">
              <div class="image-box">
                <img src="../../../assets/images/home/stuff_01.jpg">
              </div>
              <div class="content">
                <p class="stuff-title">Amiro LUX明肌高清灯光化妆镜</p>
                <div class="des">
                  <p>专为室内“梳妆时刻”研发，采用高科技PureLux明肌光，精准显色，真实还原的镜面光源技术，照明系统包括高肤色显色LED灯珠，调光芯片，专利环形导光板，度假设计反光网点等，具有显色准、亮度高、光线均匀柔和等优点，专门针对亚洲人肤色，R15显色指数提升两倍，亮度高清至“珠宝陈列级别”，重现自然光感，还有三种光源模式，满足用户不同需求，让用户的化妆过程始终有专业、适度的极致光线照射。</p>
                </div>
              </div>

              <!--
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
              -->
            </el-card>
          </el-col>
          <el-col :span="8">
            <el-card class="box-card" :body-style="{ padding: '0px' }">
              <div class="image-box">
                <img src="../../../assets/images/home/stuff_02.jpg">
              </div>
              <div class="content">
                <p class="stuff-title">wowstick螺丝刀1f和1fs</p>
                <div class="des">
                  <p>wowstick的系列螺丝刀不只是工具，还是一套新而不同的乐趣和探索方案，堪称人类工具发展史上的革命性变革。这套工具重新定义了工具和生活的关系，在为我们的生活带来便捷、简单的同时，还能够融入到我们日益提高的生活美学和品位中。</p>
                  <p>螺丝刀身全铝一体成型，全套工具加配十多根常用附件批头，国际大品牌代工，S2硬质合金钢，整体喷砂深空灰，颜值高，实用性强，取用方便。</p>
                </div>
              </div>

            </el-card>
          </el-col>
          <el-col :span="8">
            <el-card class="box-card" :body-style="{ padding: '0px' }">
              <div class="image-box">
                <img src="../../../assets/images/home/stuff_03.jpg">
              </div>
              <div class="content">
                <p class="stuff-title">素士牙刷 白金升级版</p>
                <div class="des">
                  <p>让刷牙成为一种品位，每个人的品位和生活品质都蕴藏在日常的细节之中。</p>
                  <p>精巧的外形，更加精致的科技内核，弥补市面所有电动牙刷的缺陷，具备高效能磁悬浮声波马达，高密度植毛3D刷头，美国进口杜邦刷毛，全机身防水，四种高效清洁模式，可通过APP智能定制，电池超长续航25天，紫外线UV杀菌真空包装。全方位智能、彻底清洁口腔环境。</p>
                </div>
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
    margin-bottom: -50px;
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

  .box-card {
    height: 460px;
    margin: 10px 0;
  }

  .box-card img {
    width: 100%;
  }

  .image-box {
    height: 250px;
    overflow: hidden;
  }

  .box-card .content {
    padding: 10px;
  }
  .box-card .content p.stuff-title {
    font-size: 1.5rem;
    color: #222;
  }

  .box-card .des {
    margin: 10px 0;
    overflow: hidden;
  }

  .box-card .des p {
    color: #666;
    font-size: 1.2rem;
    line-height: 1.8;
    font-weight: 300;
    text-overflow: ellipsis;
  }

</style>
