<template>
  <div class="InnovationIndex">
    <div :class="[isMob ? 'banner-mob': 'banner']" :style="{height : calcHeight, maxHeight:' 500px'}">
    </div>
    <div class="giftbody">
      <a v-if="!isLogin" @click="upload" class="add-work">加入铟果</a>
      <h2><span class="icon">铟果D³IN Index指数说明</span></h2>
      <p class="padding26">太火鸟·铟果D³IN Index“中国设计企业创新力指数”是全球首个基于人工智能的设计创新企业综合实力指数排行榜，由太火鸟科技旗下的产品创新引擎——铟果D³INGO（简称铟果）于2018年初正式推出。</p>

      <p class="padding26">太火鸟·铟果D³IN 太火鸟科技作为中国领先的产品创新引擎以及智能分发平台，以中国创新产品策源地为愿景，致力于打通设计创新、优质制造、消费零售为一体的全新创新价值链，在消费升级的大时代趋势下助力我国传统产业高效转型。</p>

      <p class="padding26">如今，设计创新与科技创新与成为驱动全球经济发展的新主流，“设计创新”更是决胜全人类的未来的战略关键。基于此时代背景，太火鸟旗下“D³IN铟立方未来实验室”历时一年，运用人工智能技术、大数据、云计算及深度学习算法，自主研发推出了核心指数算法，形成了太火鸟·铟果D³IN Index“中国设计企业创新力指数”排行产品。</p>

      <p>太火鸟·铟果D³IN Index“中国设计企业创新力指数”整合了设计创意产业各环节资源，对全国数千家设计机构的企业规模、市场份额、获奖数量、品牌影响等数据进行深度筛选、分析，划分为基础能力、创新能力、设计能力、商业能力、社会信誉、行业影响力六大评级维度，以季度为单位，生成最能够体现中国设计创新机构综合实力的综合性指数榜单。</p>

      <h2><span class="icon">铟果D³IN Index指数排行目标</span></h2>
      <p class="text-center">设计创新领域，主要是工业设计与交互设计领域的所有从业机构。</p>

      <h2><span class="icon">铟果D³IN Index指数算法说明</span></h2>
      <h3 class="first-icon-dot"><span class="icon-dot">数据来源</span></h3>
      <p class="data-origin"><i></i><span>设计机构在铟果D³INGO产品创新SaaS平台提交的真实有效数据；</span>
      <p class="data-origin"><i></i><span>设计企业官方网站、微信公众号，以及百度指数、微信指数等互联网开放数据；</span></p>
      <p class="data-origin"><i></i><span>来自京东商城、淘宝、天猫、小米、亚马逊等创新产品销售平台的开放数据；</span></p>
      <p class="data-origin"><i></i><span>包括所有全球设计类奖项、展览、媒体、设计类行业协会、设计院校、设计机构的授权以及公开数据；</span></p>
      <p class="data-origin"><i></i><span>国家统计局、工信部、工商局、专利局等第三方公开数据。</span>

      <h3><span class="icon-dot">权重分析</span></h3>
      <p class="text-center">本指数在设计企业的基础条件上，相对看重企业的商业变现实力、创新力、设计力与客户服务反馈情况，针对不同信息分配不同的权重，以求最科学合理地体现企业在不同层面上的实力与潜能。</p>

      <h3><span class="icon-dot">发布密度</span></h3>
      <p class="text-center">指数根据企业具体操作实时更新，按季度公布季度指数情况 。</p>

      <h3><span class="icon-dot">数据采集周期</span></h3>
      <p class="text-center">每个季度的第一天至第二个季度的前两天。</p>
      <p class="text-center">例如，2018年第一季度榜单的数据采集周期为2018年1月1日-2018年4月2日这个时间周期内，各设计机构的表现和相关数据。</p>

      <h2><span class="icon">支持单位</span></h2>
      <section class="img-list clearfix">
        <img v-for="(ele, index) in 14" :key="index"
          v-lazy="require(`assets/images/subject/logo/${ele}.jpg`)">
      </section>
    </div>
    <div class="innovation-foot">
      <img v-if="isMob" :src="require(`assets/images/subject/innovation/foot02@2x.jpg`)">
      <img v-else :src="require(`assets/images/subject/innovation/foot01@2x.jpg`)">
    </div>
  </div>
</template>
<script>
import { calcImgSize } from 'assets/js/common'
import store from '@/store/index'
import * as types from '@/store/mutation-types'
export default {
  name: 'InnovationIndex',
  data() {
    return {
      calcHeight: '',
      imgList: 4
    }
  },
  mounted() {
    let that = this
    window.addEventListener('resize', () => {
      if (that.isMob) {
        that.calcHeight = calcImgSize(180, 320, false)
      } else {
        that.calcHeight = calcImgSize(1000, 2880)
      }
    })
    if (this.isMob) {
      this.calcHeight = calcImgSize(180, 320, false)
    } else {
      this.calcHeight = calcImgSize(1000, 2880)
    }
  },
  methods: {
    upload() {
      store.commit(types.PREV_URL_NAME, 'InnovationIndex')
      this.$router.push({name: 'register', params: { type: 2 }})
    }
  },
  computed: {
    isMob() {
      return this.$store.state.event.isMob
    },
    isLogin: {
      get() {
        return this.$store.state.event.token
      },
      set() {}
    }
  }
}
</script>
<style scoped>
.banner {
  background: url('../../../assets/images/subject/innovation/innovationIndex.png')
    no-repeat center;
  background-size: cover;
}

.banner-mob {
  background: url('../../../assets/images/subject/innovation/home_banner.jpg')
    no-repeat center;
  background-size: contain;
}

.add-work {
  cursor: pointer;
  text-indent: -999em;
  display: block;
  width: 216px;
  height: 60px;
  margin: 40px auto 0;
  background: url('../../../assets/images/subject/innovation/Button@2x.png') left
    no-repeat;
  background-size: cover;
}

.add-work:hover {
  background: url('../../../assets/images/subject/innovation/Button@2x.png') center
    no-repeat;
  background-size: cover;
}

.add-work:active {
  background: url('../../../assets/images/subject/innovation/Button@2x.png') right
    no-repeat;
  background-size: cover;
}

.giftbody {
  max-width: 920px;
  margin: 0 auto;
  padding: 0 15px 60px;
}

.giftbody h2 {
  font-size: 24px;
  color: #222;
  padding: 60px 0 30px;
  text-align: center
}

.giftbody h3 {
  font-size: 22px;
  color: #222;
  padding: 50px 0 20px;
  text-align: center
}
.giftbody .first-icon-dot {
  padding: 0 0 30px;
}

.giftbody p {
  color: #222222;
  font-size: 14px;
  line-height: 26px;
}

.img-list {
  max-width: 840px;
  margin: 0 auto;
  display: flex;
  flex-wrap: wrap;
  justify-content: center
}

.img-list img {
  width: 144px;
  height: 60px;
  border-radius: 4px;
  border: 1px solid #ededed;
  margin-right: 30px;
  margin-bottom: 18px;
}

.img-list img:nth-child(5n) {
  margin-right: 0;
}

.innovation-foot img {
  width: 100%;
  margin-bottom: -53px;
}

.icon, .icon-dot {
  position: relative;
}

.icon:before {
  content: '';
  position: absolute;
  left: -30px;
  top: 7px;
  width: 20px;
  height: 20px;
  background: url('../../../assets/images/subject/innovation/left@2x.png')
    no-repeat;
  -webkit-background-size: contain;
  background-size: contain;
}

.icon:after {
  content: '';
  position: absolute;
  right: -30px;
  top: 7px;
  width: 20px;
  height: 20px;
  background: url('../../../assets/images/subject/innovation/right@2x.png')
    no-repeat;
  -webkit-background-size: contain;
  background-size: contain;
}

.icon-dot:before {
  content: '';
  position: absolute;
  left: -20px;
  top: 10px;
  width: 10px;
  height: 10px;
  background: url('../../../assets/images/subject/innovation/dot@2x.png')
    no-repeat;
  -webkit-background-size: contain;
  background-size: contain;
}

.icon-dot:after {
  content: '';
  position: absolute;
  right: -20px;
  top: 10px;
  width: 10px;
  height: 10px;
  background: url('../../../assets/images/subject/innovation/dot@2x.png')
    no-repeat;
  -webkit-background-size: contain;
  background-size: contain;
}

.latitude {
  text-align: center
}

.latitude img {
  width: 70px;
}
.latitude p {
  padding: 10px 0 40px;
}
.data-origin {
  max-width: 512px;
  margin: 0 auto;
  padding-left: 20px;
}
.data-origin span {
  position: relative;
}
.data-origin span::before {
  content: "";
  position: absolute;
  left: -20px;
  top: 5px;
  width: 0;
  height: 0;
  margin-right: 6px;
  border: 5px solid transparent;
  border-left: 10px solid #F9DA58;
}

@media screen and (max-width: 767px) {
  .add-work {
    position: relative;
    z-index: 1;
    cursor: pointer;
    text-indent: -999em;
    display: block;
    width: 144px;
    height: 40px;
    margin: 20px auto -30px;
    background: url('../../../assets/images/subject/innovation/Button@2x.png') left
      no-repeat;
    background-size: cover;
  }

  .add-work:hover {
    background: url('../../../assets/images/subject/innovation/Button@2x.png') center
      no-repeat;
    background-size: cover;
  }

  .add-work:active {
    background: url('../../../assets/images/subject/innovation/Button@2x.png') right
      no-repeat;
    background-size: cover;
  }
  .giftbody h2 {
    font-size: 18px;
    padding: 40px 0 20px;
  }

  .icon:before {
    content: '';
    position: absolute;
    left: -28px;
    top: 4px;
    width: 18px;
    height: 18px;
  }

  .icon:after {
    content: '';
    position: absolute;
    right: -28px;
    top: 4px;
    width: 18px;
    height: 18px;
  }
  .giftbody h3 {
    font-size: 16px;
    padding:  20px 0
  }
  .icon-dot::before {
    content: '';
    position: absolute;
    left: -20px;
    top: 6px;
    width: 10px;
    height: 10px;
  }
  .icon-dot::after {
    content: '';
    position: absolute;
    right: -20px;
    top: 6px;
    width: 10px;
    height: 10px;
  }

  .giftbody .first-icon-dot {
    padding: 0 0 20px;
  }
  .img-list {
    padding-top: 6px
  }
}

/* 统一合作伙伴样式 */
@media screen and (max-width: 767px) and (min-width: 552px) {
  .img-list {
    max-width: 522px;
    margin: 0 auto;
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start
  }

  .img-list img:nth-child(3n) {
    margin-right: 0;
  }

  .img-list img:nth-child(5n) {
    margin-right: 30px;
  }
}

@media screen and (max-width: 551px) and (min-width: 354px) {
  .img-list {
    max-width: 318px;
    margin: 0 auto;
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start
  }

  .img-list img:nth-child(n) {
    margin-right: 18px;
  }

  .img-list img:nth-child(2n) {
    margin-right: 0;
  }
}


@media screen and (max-width: 353px) {

  .img-list {
    max-width: 228px;
    margin: 0 auto;
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start
  }

  .img-list img:nth-child(n) {
    width: 96px;
    height: 40px;
    margin-right: 18px;
  }
}
</style>
