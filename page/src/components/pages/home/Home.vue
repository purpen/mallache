<template>
  <div class="content-box">
    <el-carousel :interval="5000" :height="calcHeight">
      <el-carousel-item v-for="(item,index) in slideList" :key="index">
        <a :href="item.clickUrl">
          <div class="slide" ref="slide" :style="{ 'background-image': 'url(' + item.image + ')', height: calcHeight}">
            <h3 :class="{'m-h3' : isMob}">{{ item.title }}</h3>
            <p :class="{'m-p' : isMob}">{{ item.desc }}</p>
          </div>
        </a>
      </el-carousel-item>
    </el-carousel>

    <div class="container">
      <div class="item_1 item">
        <h3>提供专业设计解决方案</h3>
        <el-row>
          <el-col :xs="24" :sm="12" :md="12" :lg="12">
            <div class="item_1_l">
              <img src="../../../assets/images/home/index_01.png" width="150" />
              <p class="item_1_title font-weight5">产品设计</p>
              <p class="item_1_desc">产品策略／产品外观设计／结构设计</p>
            </div>
          </el-col>
          <el-col :xs="24" :sm="12" :md="12" :lg="12">
            <div class="item_1_r">
              <img src="../../../assets/images/home/index_02.png" width="150" />
              <p class="item_1_title font-weight5">UI/UE设计</p>
              <p class="item_1_desc">App设计／网页设计／界面设计／服务设计／用户体验咨询</p>
            </div>
          </el-col>
        </el-row>

        <div class="pub" v-if="uType !== 2 && !isMob">
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
          <el-col :xs="24" :sm="8" :md="8" :lg="8">
            <div class="">
              <img src="../../../assets/images/home/index_03.png" />
              <p class="item_1_title">智能</p>
              <p class="item_1_desc">通过云平台+大数据计算，帮您智能匹配设计服务供应商</p>
            </div>
          </el-col>
          <el-col :xs="24" :sm="8" :md="8" :lg="8">
            <div class="item2banner">
              <img src="../../../assets/images/home/index_04.png" />
              <p class="item_1_title">安全</p>
              <p class="item_1_desc">资金托管、时间戳、供应商信用评级为交易提供全方位的安全保障 </p>
            </div>
          </el-col>
          <el-col :xs="24" :sm="8" :md="8" :lg="8">
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

        <el-carousel :interval="5000" height="480px" v-if="!isMob">
          <el-carousel-item v-for="(d, index) in caseSlideList" :key="index">
            <el-row :gutter="30" class="anli-elrow">
              <el-col :xs="24" :sm="8" :md="8" :lg="8" v-for="(k, i) in d" :key="i">
                <el-card class="box-card" :body-style="{ padding: '0px' }">
                  <div class="image-box">
                    <a :href="k.clickUrl">
                      <img :src="k.image">
                    </a>
                  </div>
                  <div class="content">
                    <p class="stuff-title">{{ k.title }}</p>
                    <div class="des">
                      <p>{{ k.desc }}</p>
                    </div>
                  </div>
                </el-card>
              </el-col>
            </el-row>
          </el-carousel-item>
        </el-carousel>

        <el-carousel :interval="5000" height="480px" v-if="isMob">
          <div v-for="(d, index) in caseSlideList" :key="index">
            <el-carousel-item v-for="(k, i) in d" :key="i">
              <el-row :gutter="30" class="anli-elrow">
                <el-col :xs="24" :sm="8" :md="8" :lg="8">
                  <el-card class="box-card" :body-style="{ padding: '0px' }">
                    <div class="image-box">
                      <a :href="k.clickUrl">
                        <img :src="k.image">
                      </a>
                    </div>
                    <div class="content">
                      <p class="stuff-title">{{ k.title }}</p>
                      <div class="des">
                        <p>{{ k.desc }}</p>
                      </div>
                    </div>
                  </el-card>
                </el-col>
              </el-row>
            </el-carousel-item>
          </div>
        </el-carousel>

      </div>
    </div>

    <div class="item item_4">
      <h3>战略合作</h3>

      <div class="logo-list">
        <img src="../../../assets/images/home/logo_md.jpg" />
        <img src="../../../assets/images/home/jdjr_logo.jpg" />
        <img src="../../../assets/images/home/logo_cxgc.jpg" />
        <img src="../../../assets/images/home/logo_hqjj.jpg" />
      </div>
      <div class="blank40"></div>
      <h3 class="m-partner">合作伙伴</h3>

      <div class="logo-list">
        <img src="../../../assets/images/home/1logo.jpg" />
        <img src="../../../assets/images/home/2logo.jpg" />
        <img src="../../../assets/images/home/3logo.jpg" />
        <img src="../../../assets/images/home/4logo.jpg" />
      </div>
    </div>

  </div>
</template>

<script>
import { calcImgSize } from 'assets/js/common'
export default {
  name: 'index',
  data() {
    return {
      uType: this.$store.state.event.user.type || 1,
      slideList: [
        {
          'clickUrl': 'javascript:void(0);',
          'title': '铟果D³ingo产品创新SaaS平台',
          'desc': '用设计重塑品质生活',
          'image': require('@/assets/images/home/banner1.jpg')
        },
        {
          'clickUrl': '/subject/zj',
          'title': '',
          'desc': '',
          'image': require('@/assets/images/home/banner2.jpg')
        }
      ],
      caseSlideList: [
        [
          {
            'clickUrl': 'http://d3ingo.taihuoniao.com/article/show/26',
            'title': '洒哇地咔无线擦地机',
            'desc': '由太火鸟科技对接产品需求与设计服务，促成项目实现并通过太火鸟自媒体独家销售及独家专款产品首发，头部大号一条主推家电商品，曝光率100w＋，3个月出货量近5000台。',
            'image': require('@/assets/images/home/case_swdk.jpg')
          },
          {
            'clickUrl': 'http://d3ingo.taihuoniao.com/article/show/18',
            'title': '云马C1智能电单车',
            'desc': '2015年10月15日，太火鸟协助国内电单车品牌云造科技正式发布云马 C1智能电单车，同步上线淘宝众筹，并助其拓展线上线下及海外营销渠道，不到一个月时间即完成众筹金额破千万的成绩。截止众筹结束，云马C1智行车共取得5879粉丝支持，筹得资金¥11597621，1159%完成众筹目标，渠道直采4万台。',
            'image': require('@/assets/images/home/case_ym1.jpg')
          },
          {
            'clickUrl': 'http://d3ingo.taihuoniao.com/article/show/21',
            'title': '素士声波电动牙刷白金版',
            'desc': '素士品牌牙刷是由太火鸟孵化的舒可士（深圳）科技有限公司自主研发，2016年7月19日正式在小米众筹上线，共获得2,289,771元众筹金额，以999%的成绩完成了众筹目标。2016年12月，品牌系列产品登陆太火鸟自媒体电商销售，太火鸟担任了品牌首发总代理，截止2017年6月，该款产品在太火鸟自有电商平台上的销售额已高达5000万人民币。',
            'image': require('@/assets/images/home/case_ss.jpg')
          }
        ],
        [
          {
            'clickUrl': 'http://d3ingo.taihuoniao.com/article/show/23',
            'title': '婴萌智能配奶机',
            'desc': '北京婴萌科技有限公司设计研发的智能配奶机由太火鸟全程孵化运营，配合卖点提炼、市场定位和针对性营销规划。至今产品已在淘宝众筹平台共筹集320多万人民币，649%达成众筹目标，获得3794多人支持。',
            'image': require('@/assets/images/home/case_ym.jpg')
          },
          {
            'clickUrl': 'http://d3ingo.taihuoniao.com/article/show/24',
            'title': '小蚁智能行车记录仪',
            'desc': '2015年，太火鸟联合小蚁智能行车记录仪的出品公司小蚁科技，在淘宝众筹首发小蚁智能行车记录仪。太火鸟社区发起“募百人首批小蚁样机体验团”试用活动，申请使用人数突破2300人，3天众筹金额突破1000万，众筹总额高达16,191,730元。',
            'image': require('@/assets/images/home/case_xy.jpg')
          },
          {
            'clickUrl': 'http://d3ingo.taihuoniao.com/article/show/20',
            'title': 'WOWSTICK 1fs电动螺丝刀',
            'desc': 'WOWSTICK电动螺丝刀是小米智能家居2016年第42期众筹新品，太火鸟参与运营众筹并担任线上销售总代理，最终筹集金额199万元，2016年12月至2017年4月，该款产品在米家商城/米家有品的销售额高达600万，分销总额超过3,000万元。',
            'image': require('@/assets/images/home/case_lsd.jpg')
          }
        ],
        [
          {
            'clickUrl': 'http://d3ingo.taihuoniao.com/article/show/19',
            'title': 'AMIRO LUX日光镜',
            'desc': '深圳市宗匠科技有限公司（以下简称宗匠）以智能照明为核心研发对象，发布了高科技智能化妆镜由。2015年，太火鸟参与宗匠天使轮投资，并开放自有电商平台供宗匠产品入驻销售。如今，宗匠已通过研发日光灯、自拍补光灯等产品获得“中国智能化妆镜第一品牌”的称号。',
            'image': require('@/assets/images/home/case_rgj.jpg')
          },
          {
            'clickUrl': 'http://d3ingo.taihuoniao.com/article/show/25',
            'title': '四季沐歌不插电的WOW净水机',
            'desc': '产品由太火鸟运营众筹，3月27日众筹正式开始便展现了惊人的潜力，曾经创下38分钟众筹金额破百万、4天破千万的记录，经过一个月的众筹，最终的众筹总额高达23,134,254元，支持人数14,804人，2313%完成众筹目标。',
            'image': require('@/assets/images/home/case_sjmg.jpg')
          },
          {
            'clickUrl': 'http://d3ingo.taihuoniao.com/article/show/22',
            'title': '飞行鱼-亚特兰蒂斯号FiFish Atlantis',
            'desc': '由太火鸟旗下的“太火鸟深圳D3IN铟立方未来实验室”在2016年推出的全球首款进入消费级市场的ROV智能水下机器人，主要用途为水下摄像，一经推出即获得了2017年美国CES创新大奖和2017年DIA中国智造大奖。',
            'image': require('@/assets/images/home/case_fxy.jpg')
          }
        ]
      ],
      calcHeight: ''
    }
  },
  created() {
  },
  mounted() {
    var that = this
    window.addEventListener('resize', () => {
      that.calcHeight = calcImgSize(650, 1440)
    })
    this.calcHeight = calcImgSize(650, 1440)
  },
  computed: {
    isMob() {
      return this.$store.state.event.isMob
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.slide {
  color: #475669;
  font-size: 18px;
  width: 100%;
  margin: 0;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  text-align: center;
}

.slide h3 {
  font-size: 2.3rem;
  color: #fff;
  line-height: 1.8;
  font-weight: 300;
  padding: 10% 0 0 0;
}

.slide p {
  font-size: 5rem;
  color: #fff;
  font-weight: 300;
  padding: 0;
}

.slide .m-h3 {
  font-size: 1.4rem;
  padding: 0;
}

.slide .m-p {
  font-size: 2.4rem;
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

.item_3 h3 {
  padding-bottom: 20px;
  color: #4B4B4B;
}

.item_4 {
  background-color: #FAFAFA;
  margin: 50px 0 -50px 0;
}

.item_4 h3 {
  padding-bottom: 40px;
}

.item {
  text-align: center;
  padding: 60px 0 20px 0;
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
  margin-bottom: 50px;
}

.logo-list img {
  margin: 10px 20px;
  width: 15%;
}

.box-card {
  text-align: left;
  width: 100%;
  height: 460px;
  margin: 10px 0;
}

.box-card img {
  width: 100%;
}

.image-box {
  margin: 0 auto;
  max-width: 549px;
  max-height: 347px;
  overflow: hidden;
}

.image-box a {
  display: block
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

@media screen and (max-width: 767px) {
  .logo-list img {
    margin: 10px 10px;
    width: 30%;
  }
  .item {
    padding: 30px 0;
  }
  .item h3 {
    font-size: 2rem;
    padding-bottom: 30px;
  }
  .item_1_title {
    font-size: 1.8rem
  }
  .item_1_l {
    margin-left: 0;
    margin-bottom: 30px;
  }
  .item_1_r {
    margin-right: 0;
    padding: 0 18%;
  }
  .item_1_l img,
  .item_1_r img {
    width: 120px;
  }
  .item_2 {
    margin-top: 0;
  }
  .item_2_box .item2banner {
    margin: 40px 0;
  }
}

@media screen and (max-width: 500px) {
  .box-card .content {
    padding-top: 28px;
  }
  .item_2 h3 {
    padding-bottom: 30px;
  }
}
</style>
