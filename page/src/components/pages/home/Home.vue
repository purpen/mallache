<template>
  <div class="content-box">
  <div class="slide" ref="slide"
    :style="{ 'background-image': 'url(' + require ('@/assets/images/home/BG@2x.jpg') + ')', height: calcHeight}">
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
        <p><span>100+</span>专业设计服务商，<span>20+</span>成交项目，<span>200万</span>成交金额</p>
        <router-link v-if="uType !== 2" :to="{}">发布项目需求</router-link>
      </div>
    </div>

    <div class="categorie">
      <el-row class="container" :gutter="15">
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
              <div class="image-box">
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
          <a :href="d.url" :target="isMob ? '_self' : '_blank'">
            <div class="image-box">
                <img v-lazy="d.cover_url">
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
          </a>
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
              <p class="slide-company"><img v-lazy="ele.companyLogo" :alt="ele.title" width="40px">{{ele.company}}</p>
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
        <img v-lazy="require('assets/images/home/logo_md.jpg')" />
        <img v-lazy="require('assets/images/home/jdjr_logo.jpg')" />
        <img v-lazy="require('assets/images/home/logo_cxgc.jpg')" />
        <img v-lazy="require('assets/images/home/logo_hqjj.jpg')" />
        <img v-lazy="require('assets/images/home/1logo.jpg')" />
        <img v-lazy="require('assets/images/home/2logo.jpg')" />
        <img v-lazy="require('assets/images/home/3logo.jpg')" />
        <img v-lazy="require('assets/images/home/4logo.jpg')" />
        <img v-lazy="require('assets/images/home/5logo.png')" />
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
            company: '素士',
            companyLogo: require('@/assets/images/home/logo_sushi.png'),
            title: '素士声波电动牙刷',
            sales: '自媒体销售额约2000万',
            intro: '种子期投资孵化项目，资本助力创业团队搭建和产品开发升级，头部大号攻城掠地、中腰和底部社群全网覆盖战略，已有100＋合作自媒体，助力全渠道分发',
            image: require ('@/assets/images/home/case_ss.jpg')
          }
        ],
        calcHeight: '',
        swiperOption: {
          pagination: '.swiper-pagination',
          paginationClickable: true,
          lazyLoading: true,
          autoplay: 5000,
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
        designCaseList: []
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
            console.log(res.data.data)
            // this.tags = res.data.data.code.split(';')
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
  .slide {
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
    position: relative;
    padding-bottom: 50px;
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
    flex: 1;
    height:100%;
    display: flex;
    flex-direction: column;
    justify-content: center
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

  .categorie .container .list{
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

  .categorie .container .list h3{
    font-size: 18px;
    line-height: 1.5
  }

  .categorie .container .list p{
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
    font-size: 2.4rem;
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
    text-align: center;
    margin: 0 auto;
    max-width: 815px;
  }

  .logo-list img {
    width: 144px;
    height: 60px;
    margin: 15px 15px 0 0;
    border: 1px solid #F6F6F6;
    border-radius: 4px;
  }

  .image-box {
    margin: 0 auto;
    overflow: hidden;
  }

  .image-box a {
    display: block;
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
  }

  .image-box img {
    width: 100%;
    height: 100%;
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

  @media screen and (max-width: 1199px) and (min-width: 991px) {
    .image-box:not(.not-limit) {
      height: 166px;
    }
  }

  @media screen and (max-width: 990px) and (min-width: 851px) {
    .image-box:not(.not-limit) {
      height: 150px;
    }
  }

  @media screen and (max-width: 850px) and (min-width: 768px) {
    .image-box:not(.not-limit) {
      height: 136px;
    }
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
      top: 80px;
      bottom: 0;
      left: 0;
      right: 0;
      margin: auto;
      width: 70%;
      height: auto;
    }

    .slide .head-cover {
      padding: 0 15px;
    }

    .slide .head-cover p {
      width: calc(100% - 106px);
      font-size: 1.2rem;
      padding-right: 0;
      margin-right: 12px;
    }

   .slide .head-cover p span {
      font-weight: normal;
      margin: 0 4px;
    }

    .slide .head-cover a {
      font-size: 1.2rem;
      width: 94px;
    }

    .title {
      font-size: 20px;
      padding: 30px 15px 10px;
    }

    .el-row {
      padding: 0 15px;
    }

    .categorie .container .list{
      flex-direction: column;
    }

    .categorie .container .list h3{
      text-align: center;
      margin-top: 10px;
    }
    .slide-content {
      display: flex;
      flex-direction: column-reverse;
    }
    .image-box {
      height: auto!important;
    }
    .slide p {
      font-size: 4rem
    }
    .slide h3 {
      font-size: 2rem
    }
    .categorie .container .list {
      text-align: center
    }
    .categorie .list i {
      font-size: 36px;
      margin-right: 0;
    }
    .categorie .container .list p {
      font-size: 12px;
    }
    .categorie .container .list h3 {
      font-size: 16px;
    }
  }

  @media screen and (max-width: 320px) {
    .logo-list img {
      width: 40%;
      height: auto;
    }

  }
</style>
