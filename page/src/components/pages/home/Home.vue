<template>
  <div class="content-box">
    <swiper :options="swiperOption" class="banner">
      <swiper-slide>
        <div class="slide" :style="{ background: 'url(' + require ('assets/images/home/BG@2x.jpg') + ') no-repeat center', height: calcHeight}">
          <div class="container clearfix" style="height:100%;">
            <div class="left">
              <h3 :class="{'m-h3' : isMob}">铟果D³INGO产品创新SaaS平台</h3>
              <p :class="{'m-p' : isMob}">用设计重塑品质生活</p>
            </div>
            <div class="draw">
              <img :src="require('assets/images/home/BG02@2x.png')" width="90%" alt="">
            </div>
          </div>
          <div class="head-cover">
            <p :class="[{'need': uType !== 2}]"><span>{{tags[0]}}</span>专业设计服务商，<span>{{tags[1]}}</span>成交项目，<span>{{tags[2]}}</span>成交金额</p>
            <router-link v-if="uType !== 2" to="/item/submit_one">发布项目需求</router-link>
          </div>
        </div>
      </swiper-slide>
      <swiper-slide>
        <router-link class="banner-link slide" to="/innovation_index" :style="{ background: 'url(' + require ('assets/images/subject/innovation/innovationIndex.png') + ') no-repeat center',backgroundSize: 'cover', height: calcHeight}"></router-link>
      </swiper-slide>
      <div class="swiper-pagination" slot="pagination">
      </div>
      <div class="swiper-button-prev" slot="button-prev">
        <i class="el-icon-arrow-left"></i>
      </div>
      <div class="swiper-button-next" slot="button-next">
        <i class="el-icon-arrow-right"></i>
      </div>
    </swiper>

    <div class="categorie">
      <el-row class="container">
        <el-col class="list" :span="8">
          <i class="fx-icon-major-lg"></i>
          <article>
            <h3>专业服务</h3>
            <p>汇聚国内专业设计服务商</p>
          </article>
        </el-col>
        <el-col class="list" :span="8">
          <i class="fx-icon-accurate-lg"></i>
          <article>
            <h3>智能精准</h3>
            <p>找到适合你的设计服务商</p>
          </article>
        </el-col>
        <el-col class="list" :span="8">
          <i class="fx-icon-design-lg"></i>
          <article>
            <h3>优质设计</h3>
            <p>项目协同追踪保证质量</p>
          </article>
        </el-col>
      </el-row>
    </div>
    <div class="container article">
      <h3 class="title">铟果说</h3>
      <el-row :gutter="20" class="card-list">
        <el-col :xs="24" :sm="8" :md="8" :lg="8" v-for="(d, index) in articleList" :key="index">
          <el-card class="card" :body-style="{ padding: '0px' }">
            <router-link :to="{name: 'articleShow', params: {id: d.id}}"
                        :target="isMob ? '_self' : '_blank'">
              <div class="image-box" :style="{background: 'url('+ d.cover.middle + ') no-repeat center', backgroundSize: 'cover'}">
                <img v-lazy="d.cover.middle">
              </div>
              <div class="content">
                <p class="title">{{ d.title }}<p>
                <p class="des">{{ d.short_content }}</p>
              </div>
            </router-link>
          </el-card>
        </el-col>
      </el-row>
    </div>

    <div class="container article design">
      <h3 class="title">设计造物</h3>
      <el-row :gutter="20" class="card-list">
      <el-col :xs="24" :sm="12" :md="12" :lg="12" v-for="(d, index) in designList" :key="index">
        <el-card class="card" :body-style="{ padding: '0px' }">
          <a :href="d.url" :target="isMob ? '_self' : '_blank'">
            <div class="image-box not-limit">
                <img v-lazy="d.cover_url">
            </div>
            <div class="content">
              <p class="title">{{ d.title }}</p>
              <p class="des">{{ d.content }}</p>
            </div>
          </a>
        </el-card>
      </el-col>
    </el-row>
    </div>

    <div class="container article design-case">
      <h3 class="title">灵感</h3>
      <el-row :gutter="20" class="card-list">
      <el-col :xs="24" :sm="8" :md="8" :lg="8" v-for="(d, index) in designCaseList" :key="index">
        <el-card class="card" :body-style="{ padding: '0px' }">
          <router-link :to="{name: 'vcenterDesignCaseShow', params: {id: d.id}}" :target="isMob ? '_self' : '_blank'">
            <div class="image-box" :style="{background: 'url('+ d.cover.middle + ') no-repeat center', backgroundSize: 'cover'}">
                <img v-lazy="d.middle">
            </div>
            <div class="content">
              <p class="title">{{ d.title }}</p>
              <p class="des">{{ d.profile }}</p>
              <p class="company">
                <img class="avatar" v-if="d.design_company.logo_image" :src="d.design_company.logo_image.logo"
                    width="30"/>
                <img class="avatar" v-else :src="require('assets/images/avatar_100.png')" width="30"/>
                <span>{{d.design_company.company_abbreviation}}</span>
              </p>
            </div>
          </router-link>
        </el-card>
      </el-col>
    </el-row>
    </div>

    <div class="anli">
      <h3 class="title-center">铟果案例</h3>
      <swiper :options="swiperOption" class="clearfix">
        <swiper-slide v-for="(ele, index) in caseSlideList" :key="index" class="clearfix">
          <div class="slide-content container">
            <div class="slide-left">
              <p :class="['slide-company', 'slide' + index]"><img v-lazy="ele.companyLogo" :alt="ele.title" width="40px">{{ele.company}}</p>
              <h4 class="slide-title">{{ele.title}}</h4>
              <p class="slide-sales">{{ele.sales}}</p>
              <p class="slide-intro">{{ele.intro}}</p>
            </div>
            <a class="slide-right" :href="ele.clickUrl">
              <img v-lazy="ele.image" :alt="ele.title">
            </a>
          </div>
        </swiper-slide>
        <div class="swiper-pagination" slot="pagination"></div>
        <div class="swiper-button-prev" slot="button-prev">
          <i class="el-icon-arrow-left"></i>
        </div>
        <div class="swiper-button-next" slot="button-next">
          <i class="el-icon-arrow-right"></i>
        </div>
      </swiper>
    </div>

    <div class="item_4">
      <h3 class="title-center title-center2">合作伙伴</h3>

      <div class="logo-list clearfix">
        <span class="inline-flex">
          <img v-lazy="require('assets/images/home/logo_md.jpg')" />
          <img v-lazy="require('assets/images/home/jdjr_logo.jpg')" />
          <img v-lazy="require('assets/images/home/logo_cxgc.jpg')" />
          <img v-lazy="require('assets/images/home/logo_hqjj.jpg')" />
          <img v-lazy="require('assets/images/home/1logo.jpg')" />
          <img v-lazy="require('assets/images/home/2logo.jpg')" />
          <img v-lazy="require('assets/images/home/3logo.jpg')" />
          <img v-lazy="require('assets/images/home/4logo.jpg')" />
          <img v-lazy="require('assets/images/home/5logo.png')" />
        </span>
      </div>
    </div>
  </div>
</template>

<script>
  import { calcImgSize } from 'assets/js/common'
  import api from '@/api/api'
  export default {
    name: 'index',
    data() {
      return {
        uType: this.$store.state.event.user.type || 1,
        caseSlideList: [
          {
            clickUrl: 'http://d3ingo.taihuoniao.com/article/show/26',
            company: '洒哇地咔',
            companyLogo: require('@/assets/images/home/logo_swdk.png'),
            title: '洒哇地咔智能拖地机器人',
            sales: '单周销量1500台',
            intro: '由太火鸟科技对接产品需求与设计服务，促成项目实现并通过太火鸟自媒体独家销售及独家专款产品首发，头部大号一条主推家电商品，曝光率100w＋，3个月出货量近5000台。',
            image: require ('@/assets/images/home/case_swdk.jpg')
          },
          {
            clickUrl: 'http://d3ingo.taihuoniao.com/article/show/21',
            company: '',
            companyLogo: require('@/assets/images/home/micoe&WOW@2x.png'),
            title: '四季沐歌不插电的WOW净水机',
            sales: '众筹总额高达23,134,254元',
            intro: '众筹纪录创造者，产品由太火鸟运营众筹，3月27日众筹正式开始便展现了惊人的潜力，众筹表现38分钟众筹金额破百万、4天破千万。经过一个月的众筹，最终的众筹总额高达23,134,254元，支持人数14,804人，2313%完成众筹目标。',
            image: require ('@/assets/images/home/micoe&WOW.jpg')
          },
          {
            clickUrl: 'http://d3ingo.taihuoniao.com/article/show/21',
            company: '小蚁',
            companyLogo: require('@/assets/images/home/xiaoyi@2x.png'),
            title: '小蚁智能行车记录仪',
            sales: '众筹总额高达16,191,730元',
            intro: '2015年，太火鸟联合小蚁智能行车记录仪的出品公司小蚁科技，在淘宝众筹首发小蚁智能行车记录仪。太火鸟社区发起“募百人首批小蚁样机体验团”试用活动，申请使用人数突破2300人，3天众筹金额突破1000万。',
            image: require ('@/assets/images/home/yi.jpg')
          },
          {
            clickUrl: 'http://d3ingo.taihuoniao.com/article/show/21',
            company: '婴萌科技',
            companyLogo: require('@/assets/images/home/ingmeng.png'),
            title: '婴萌智能配奶机',
            sales: '众筹总额超过320万人民币',
            intro: '北京婴萌科技有限公司设计研发的智能配奶机由太火鸟全程孵化运营，配合卖点提炼、市场定位和针对性营销规划。至今产品已在淘宝众筹平台共筹集320多万人民币，649%达成众筹目标，获得3794多人支持。',
            image: require ('@/assets/images/home/ingmeng.jpg')
          },
          {
            clickUrl: 'http://d3ingo.taihuoniao.com/article/show/21',
            company: '',
            companyLogo: require('@/assets/images/home/fifish@2x.png'),
            title: '飞行鱼-亚特兰蒂斯号FiFish Atlantis',
            sales: '属于水下世界的未来消费级产品',
            intro: '由太火鸟旗下的“太火鸟深圳D3IN铟立方未来实验室”在2016年推出的全球首款进入消费级市场的ROV智能水下机器人，主要用途为水下摄像，一经推出即获得了2017年美国CES创新大奖和2017年DIA中国智造大奖。',
            image: require ('@/assets/images/home/fifish.jpg')
          },
          {
            clickUrl: 'http://d3ingo.taihuoniao.com/article/show/21',
            company: '素士',
            companyLogo: require('@/assets/images/home/logo_sushi.png'),
            title: '素士声波电动牙刷',
            sales: '自媒体销售额约2000万',
            intro: '种子期投资孵化项目，资本助力创业团队搭建和产品开发升级，头部大号攻城掠地、中腰和底部社群全网覆盖战略，已有100＋合作自媒体，助力全渠道分发',
            image: require ('@/assets/images/home/case_ss.jpg')
          },
          {
            clickUrl: 'http://d3ingo.taihuoniao.com/article/show/21',
            company: 'WOWSTICK',
            companyLogo: require('@/assets/images/home/WOWSTICK@2x.png'),
            title: 'WOWSTICK 1fs电动螺丝刀',
            sales: '分销总额超过3,000万元',
            intro: 'WOWSTICK电动螺丝刀是小米智能家居2016年第42期众筹新品，太火鸟参与运营众筹并担任线上销售总代理，最终筹集金额199万元，2016年12月至2017年4月，该款产品在米家商城/米家有品的销售额高达600万，分销总额超过3,000万元。',
            image: require ('@/assets/images/home/case_lsd.jpg')
          },
          {
            clickUrl: 'http://d3ingo.taihuoniao.com/article/show/21',
            company: '',
            companyLogo: require('@/assets/images/home/AMIRP_LUX@2x.png'),
            title: 'AMIRO LUX日光镜',
            sales: '俘获众网红芳心的科技生活爆品',
            intro: '深圳市宗匠科技有限公司（以下简称宗匠）以智能照明为核心研发对象，发布了高科技智能化妆镜由。2015年，太火鸟参与宗匠天使轮投资，并开放自有电商平台供宗匠产品入驻销售。如今，宗匠已通过研发日光灯、自拍补光灯等产品获得“中国智能化妆镜第一品牌”的称号。',
            image: require ('@/assets/images/home/AMIRO_LUX.jpg')
          },
          {
            clickUrl: 'http://d3ingo.taihuoniao.com/article/show/21',
            company: '云马',
            companyLogo: require('@/assets/images/home/uma.png'),
            title: '云马C1智能电单车',
            sales: '太火鸟运营发布的爆款“鼻祖”',
            intro: '2015年10月15日，太火鸟协助国内电单车品牌云造科技正式发布云马 C1智能电单车，同步上线淘宝众筹，并助其拓展线上线下及海外营销渠道，不到一个月时间即完成众筹金额破千万的成绩。截止众筹结束，云马C1智行车共取得5879粉丝支持，筹得资金¥11597621，1159%完成众筹目标，渠道直采4万台。',
            image: require ('@/assets/images/home/yunma.jpg')
          }
        ],
        calcHeight: '',
        swiperOption: {
          pagination: '.swiper-pagination',
          paginationClickable: true,
          lazyLoading: true,
          autoplay: 500000,
          prevButton: '.swiper-button-prev',
          nextButton: '.swiper-button-next',
          spaceBetween: 0
        },
        articleList: [],
        designList: [
          {
            id: 7,
            title: '羽泉二十周年巡回演唱会-IP衍生品招募',
            cover_url: require ('@/assets/images/subject/list_05.jpg'),
            url: '/subject/YuQuanGifts',
            content: '2017年底，内地知名唱作组合羽泉与京东金融结成战略合作伙伴，双方在组合成立20周年巡回演唱会期间，将以羽泉组合这一明星IP为核心，衍生系列创意创新活动，内容涵盖广泛产品，活动名称为“羽泉的礼物”。'
          },
          {
            id: 5,
            title: '轻创新⋅设计造物-再设计⋅消费升级创新产品征集',
            cover_url: require ('@/assets/images/subject/list_03.jpg'),
            url: '/subject/ProductRecruit',
            content: '2017年初，太火鸟与投资方罗莱生活、海泉基金、京东金融、麦顿资本、泰德资本以及创新工场、真格基金等战略合作方共同发起了名为 “智见未来-太火鸟AesTech联合加速计划”，希望能够将太火鸟在产品孵化方面的前瞻性与各资本方及平台、渠道方在创新产品研发、孵化、营销环节的势能最大限度发挥出来，促进设计相关产业发展，改善设计生态，惠及大众。'
          }],
        designCaseList: [],
        tags: []
      }
    },
    created() {
      this.getArticleList()
      this.getDesignCase()
      this.getBlock()
    },
    mounted() {
      let that = this
      window.addEventListener ('resize', () => {
        if (that.isMob) {
          that.calcHeight = calcImgSize (180, 320)
        } else {
          that.calcHeight = calcImgSize (500, 1440)
        }
      })
      if (that.isMob) {
        that.calcHeight = calcImgSize (180, 320)
      } else {
        this.calcHeight = calcImgSize (500, 1440)
      }
    },
    methods: {
      getArticleList() {
        this.$http.get(api.articleList,
        {params: {per_page: 3}})
        .then((res) => {
          this.articleList = res.data.data
          for (let i = 0; i < res.data.data.length; i++) {
            this.articleList[i].cover_url = res.data.data[i].cover.middle
          }
        }).catch((err) => {
          console.error(err)
        })
      },
      getDesignCase() {
        this.$http.get(api.designCaseOpenLists,
        {params: {per_page: 6}})
        .then((res) => {
          this.designCaseList = res.data.data
          for (let i = 0; i < res.data.data.length; i++) {
            this.designCaseList[i].cover_url = res.data.data[i].cover.middle
          }
        }).catch((err) => {
          console.error(err)
        })
      },
      getBlock () {
        this.isLoading = true
        this.$http.get(api.block, {params: {mark: 'data_number_view'}})
        .then((res) => {
          this.isLoading = false
          if (res.data.meta.status_code === 200) {
            this.tags = res.data.data.code.split(';')[0].split('|')
          } else {
            this.$Message.error(res.data.meta.message)
          }
        })
        .catch((err) => {
          this.isLoading = false
          console.error(err)
        })
      }
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      }
    },
    components: {
      swiper: (resolve) => {
        require(['vue-awesome-swiper/src/swiper'], resolve)
      },
      swiperSlide: (resolve) => {
        require(['vue-awesome-swiper/src/slide'], resolve)
      }
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .banner-link {
    display: block
  }

  .slide {
    position: relative;
    color: #475669;
    font-size: 18px;
    width: 100%;
    max-height: 500px;
    min-height: 180px;
    margin: 0;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    text-align: left;
    /* padding-bottom: 50px; */
    overflow: hidden;
  }

  .slide h3 {
    font-size: 2.4rem;
    color: #FFFFFF;
    line-height: 1.8;
    font-weight: 300;
  }

  .slide p {
    font-size: 4.8rem;
    color: #FFFFFF;
    font-weight: 300;
    padding: 0;
  }

  .slide .container {
    display: flex;
  }

  .slide .left {
    flex: 1;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center
  }

  .slide .draw {
    height: 100%;
    flex: 1;
    display: flex;
    align-items: center
  }

  .slide .head-cover {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 13%;
    min-height: 50px;
    line-height: 80px;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    background: rgba(0, 0, 0, 0.3)
  }

  .slide .head-cover p {
    font-size: 1.7rem;
    padding-right: 35px;
  }

  .slide .head-cover p span {
    font-weight: 600;
    margin: 0 10px;
  }

  .slide .head-cover p span:first-child {
    margin-left: 0;
  }

  .slide .head-cover a {
    border: 1px solid rgba(255, 255, 255, 0.4);
    color:rgba(255, 255, 255, 0.7);
    font-size: 1.4rem;
    border-radius: 4px;
    line-height: 30px;
    padding: 0 10px;
  }

  .categorie {
    background: #fafafa;
  }

  .categorie .container{
    margin:0 auto
  }

  .categorie .list{
    height: 150px;
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center
  }

  .categorie .list i {
    font-size: 60px;
    margin-right: 12px;
  }

  .categorie .list h3{
    font-size: 18px;
    line-height: 1.5
  }

  .categorie .list p{
    font-size: 16px;
  }

  .title {
    color: #222;
    font-size: 20px;
    padding: 30px 10px 10px;
  }

  .title-center {
    text-align: center;
    color: #222;
    font-size: 24px;
    padding: 50px 0 40px;
  }
  .title-center2 {
    padding: 55px 0 30px;
  }
  .slide .head-cover a:hover {
    border: 1px solid rgba(255, 255, 255, 1);
    color: #fff;
  }

  .slide .m-h3 {
    font-size: 1.2rem;
    padding: 0;
    padding-top: 20px;
  }

  .slide .m-p {
    font-size: 2.2rem;
  }

  .pub {
    margin: 80px 0 0 0;
  }

  .pub .pub-btn {
    padding: 20px 80px 20px 80px;
  }

  .company-des {
    clear: both;
  }

  .company-des p {
    font-size: 1.2rem;
    color: #666666;
    line-height: 1.5;
  }

  .logo-list {
    margin: 0 auto;
    max-width: 815px;
    text-align: center;
  }

  .logo-list img {
    width: 144px;
    height: 60px;
    margin: 15px 15px 0 0;
    border: 1px solid #F6F6F6;
    border-radius: 4px;
  }

  .el-card:hover {
    transform: translate3d(0, -3px, 0);
    box-shadow: 0 5px 18px rgba(0, 0, 0, 0.3);
  }

  .anli {
    overflow: hidden;
  }

  .el-row {
    padding: 0 10px;
  }

  /* swiper css*/

  .swiper-container {
    overflow: visible;
  }

  .banner > .swiper-pagination-bullets {
    width: 100%;
    bottom: 10px !important;
  }

  .swiper-pagination .swiper-pagination-bullet {
    margin-right: 8px;
  }

  .swiper-pagination-fraction,
  .swiper-pagination-custom,
  .swiper-container-horizontal > .swiper-pagination-bullets {
    width: 100%;
  }


/* articleList */
  .article .content {
    padding: 15px;
  }

  .article .content .title {
    padding: 0;
    color: #222;
    font-size: 1.8rem;
    overflow: hidden;
    text-overflow:ellipsis;
    white-space: nowrap;
  }

  .design .content .title {
    font-size: 1.8rem;
    color: #222;
  }

  .article .content .des {
    margin: 10px 0;
    font-size: 1.4rem;
    color: #666;
    line-height: 1.5;
    overflow: hidden;
    max-height: 42px;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
  }

  .card-list {
    display: flex;
    flex-wrap: wrap;
  }

  .image-box:not(.not-limit) {
      height: 220px;
      overflow: hidden;
      border-bottom: 1px solid #D2D2D2;
      border-radius: 4px 4px 0 0;
  }

  .image-box img {
    width: 100%;
  }

  .image-box:not(.not-limit) img {
    display: none
  }

  .company {
    color: #666;
    display: block;
    line-height: 28px;
  }

  .company span {
    font-size: 14px;
  }

  .company img {
    margin-right: 6px;
  }
/* articleList */

  .slide-content {
    display: flex;
  }

  .slide-left, .slide-right {
    flex: 1;
    padding: 10px 20px;
  }

  .slide-right img {
    width: 100%;
    border-radius: 6px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
  }

  .slide-company {
    font-size: 18px;
    color: #222;
    display: flex;
    align-items: center;
    padding-bottom: 20px;

  }
  .slide1 img, .slide4 img, .slide8 img {
    height: 30px;
    width: auto
  }

  .slide3 img {
    height: 40px;
    width: auto
  }

  .slide7 img {
    height: 14px;
    width: auto;
  }

  .slide-company img{
    padding-right: 10px;
  }

  .slide-title {
    font-size: 24px;
    color: #222;
    padding-bottom: 10px;
  }

  .slide-sales {
    font-size: 18px;
    color: #666;
    padding-bottom: 10px;
  }

  .slide-intro {
    font-size: 14px;
    color:#666;
  }

  @media screen and (max-width: 991px) {
    .container {
      width: auto;
      padding: 0 15px;
    }
    .slide p {
      font-size: 4rem
    }
    .slide h3 {
      font-size: 2rem
    }
  }

  @media screen and (max-width: 767px) {
    .container {
      padding: 0;
    }

    .slide .container {
      display: block;
      position: relative;
    }

    .slide .left {
      text-align: center;
      position: relative;
      z-index: 2;
      justify-content: flex-start
    }

    .slide .draw {
      position: absolute;
      top: 100px;
      bottom: 0;
      left: 0;
      right: 0;
      margin: auto;
      width: 70%;
      height: auto
    }

    .slide .head-cover {
      /* padding: 0 15px; */
    }

    .slide .head-cover p {
      font-size: 1.2rem;
      padding-right: 0;
      margin-right: 12px;
    }

    .slide .head-cover p.need {
      width: calc(100% - 136px);
    }

   .slide .head-cover p span {
      font-weight: normal;
      margin: 0 4px;
    }

    .slide .head-cover a {
      font-size: 1.2rem;
      /* width: 94px; */
    }

    .title {
      font-size: 20px;
      padding: 30px 15px 10px;
    }

    .el-row {
      padding: 0 15px;
    }

    .categorie .list{
      flex-direction: column;
    }

    .categorie .list h3{
      text-align: center;
      margin-top: 10px;
    }
    .slide-content {
      display: flex;
      flex-direction: column-reverse;
    }

    .slide p {
      font-size: 2.2rem
    }

    .slide h3 {
      font-size: 1.2rem
    }

    .categorie .list {
      text-align: center
    }

    .categorie .list i {
      font-size: 36px;
      margin-right: 0;
    }

    .categorie .list p {
      font-size: 12px;
    }

    .categorie .list h3 {
      font-size: 16px;
    }

    .inline-flex {
      display: block;
      width: 322px;
      margin: 0 auto;
      overflow: hidden;
    }
    .inline-flex img {
      float: left;
    }

    .image-box {
      height: auto!important;
      overflow: hidden;
      max-height: 300px;
      border-radius: 4px 4px 0 0;
    }

    .image-box img {
      display: block!important;
      width: 100%;
    }
  }

  @media screen and (min-width: 480px) and (max-width: 767px) {
    .inline-flex {
      display: block;
      width: 477px;
      margin: 0 auto;
      overflow: hidden;
    }
  }

  @media screen and (max-width: 320px) {
    .logo-list img {
      width: 40%;
    }

  }
</style>
