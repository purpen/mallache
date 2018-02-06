<template>
  <div class="InnovationIndex">
    <div :class="[isMob ? 'banner2': 'banner']" :style="{height : calcHeight, maxHeight:' 500px'}">
    </div>
    <div class="giftbody">
      <a v-if="!isLogin" @click="upload" class="add-work">加入铟果</a>
      <h2><span class="icon">榜单说明</span></h2>
      <p class="padding26">进入21世纪第二个10年后，“科技创新”“技术创新”成为国家自上而下着力推动的国力发展重要方向之一，“创新设计”被提升到决胜未来的战略地位。与此同时，在全民消费升级和新中产崛起的时代背景下，北京太火鸟科技有限公司（简称“太火鸟”）全新推出了“铟果D³INGO”创新产品投资孵化平台（简称铟果），针对我国现有设计企业与机构进行了全面、客观的分析，结合自有大数据、云平台算法，以季度为单位，生成最能够体现我国当前设计、创新机构各方面能力与综合实力的榜单，也就是太火鸟·铟果D³INGO中国设计企业创新力指数。</p>

      <h2><span class="icon">评选目的</span></h2>
      <p>从第三方的角度，综合多平台资源与数据，通过精确、科学的分析、计算推倒出最终结果，更加直观地体现中国设计力量，让外界对我国设计机构的总体情况与各自特色有更加系统、直观、快速的认识，给需求方与品牌方在实际工作中提供一定的指导与参考素材，也让设计机构明确自身优劣，在进行下阶段规划时能够更加明确自身情况。</p>

      <h2><span class="icon">调查目标</span></h2>
      <p class="padding26 text-center">设计领域，主要是工业设计与交互设计领域的所有从业机构。</p>

      <h2><span class="icon">算法说明</span></h2>
      <h3 class="first-icon-dot"><span class="icon-dot">维度</span></h3>
      <el-row class="latitude">
        <el-col :xs="8" :sm="4" :lg="4">
          <img v-lazy="require('assets/images/subject/innovation/information@2x.png')" alt="基本信息">
          <p>基本信息</p>
        </el-col>
        <el-col :xs="8" :sm="4" :lg="4">
          <img v-lazy="require('assets/images/subject/innovation/design@2x.png')" alt="设计力">
          <p>设计力</p>
        </el-col>
        <el-col :xs="8" :sm="4" :lg="4">
          <img v-lazy="require('assets/images/subject/innovation/innovate@2x.png')" alt="创新力">
          <p>创新力</p>
        </el-col>
        <el-col :xs="8" :sm="4" :lg="4">
          <img v-lazy="require('assets/images/subject/innovation/business@2x.png')" alt="商业力">
          <p>商业力</p>
        </el-col>
        <el-col :xs="8" :sm="4" :lg="4">
          <img v-lazy="require('assets/images/subject/innovation/effect@2x.png')" alt="影响力">
          <p>影响力</p>
        </el-col>
        <el-col :xs="8" :sm="4" :lg="4">
          <img v-lazy="require('assets/images/subject/innovation/evaluate@2x.png')" alt="客户评价">
          <p>客户评价</p>
        </el-col>
      </el-row>
      <h3 class="first-icon-dot"><span class="icon-dot">数据来源</span></h3>
      <p class="data-origin"><i></i>设计公司在铟果平台填写的公司资料；</p>
      <p class="data-origin"><i></i>技术爬虫从其他第三方平台抓取；</p>
      <p class="data-origin"><i></i>设计公司主动提供。</p>

      <h3><span class="icon-dot">权重分析</span></h3>
      <p class="text-center">本榜单在设计企业基础信息之上，突出互联网影响力和社会化（用户点评）指数所占权重。</p>

      <h3><span class="icon-dot">发布密度</span></h3>
      <p class="text-center">每个季度发布一次，以及所有目标机构的排名总榜单。</p>

      <h3><span class="icon-dot">数据采集周期</span></h3>
      <p class="text-center">每个季度的第一天至第二个季度的前两天。</p>
      <p class="text-center">例如，2018年第一季度榜单的数据采集周期为2018年1月1日-2018年4月2日这个时间周期内，各设计机构的表现和相关数据。</p>

      <h2><span class="icon">支持单位</span></h2>
      <section class="img-list clearfix">
        <img v-lazy="require(`assets/images/subject/logo/CIDA@2x.png`)">
        <img v-lazy="require(`assets/images/subject/logo/haiquan@2x.png`)">
        <img v-lazy="require(`assets/images/subject/logo/InnovationWorks@2x.png`)">
        <img v-lazy="require(`assets/images/subject/logo/JD@2x.png`)">
        <img v-lazy="require(`assets/images/subject/logo/mi@2x.png`)">
        <img v-lazy="require(`assets/images/subject/logo/zhejiang@2x.png`)">
        <img v-lazy="require(`assets/images/subject/logo/zhenfund@2x.png`)">
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
        that.calcHeight = calcImgSize(840, 750, false)
      } else {
        that.calcHeight = calcImgSize(1000, 2880)
      }
    })
    if (this.isMob) {
      this.calcHeight = calcImgSize(840, 750, false)
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
  position: relative;
}

.banner2 {
  background: url('../../../assets/images/subject/innovation/innovationIndex.png') no-repeat
    -830px;
  background-size: cover;
  position: relative;
}

.add-work {
  text-indent: -999em;
  display: block;
  width: 214px;
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
  max-width: 666px;
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

.img-list img:nth-child(4) {
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
  width: 260px;
  margin: 0 auto;
}
.data-origin i {
  display: inline-block;
  width: 0;
  height: 0;
  margin-right: 6px;
  border: 5px solid transparent;
  border-left: 10px solid #F9DA58;
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

  .img-list img:nth-child(4) {
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
  .img-list img {
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
  .img-list img {
    width: 96px;
    height: 40px;
    margin-right: 18px;
  }
}
</style>
