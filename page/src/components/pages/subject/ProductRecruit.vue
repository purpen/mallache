<template>
  <div id="enterprise">
    <div class="banner" :style="{height : calcHeight}" v-if="isMob"></div>
    <div class="banner2" :style="{height : calcHeight}" v-else></div>
    <div class="main">

      <article v-html="about"></article>

      <section>
        <h3 class="D3title"><i class="D3iLeft"></i><b>活动详情</b><i class="D3iRight"></i></h3>
        <article v-html="details"></article>
        <article v-html="reality" class="reality"></article>
      </section>

      <section>
        <h3 class="D3title"><i class="D3iLeft"></i><b>合作伙伴</b><i class="D3iRight"></i></h3>
      </section>
    </div>

    <div class="logo-list">
      <img v-lazy="require('assets/images/home/logo_md.jpg')"/>
      <img v-lazy="require('assets/images/home/jdjr_logo.jpg')"/>
      <img v-lazy="require('assets/images/home/logo_cxgc.jpg')"/>
      <img v-lazy="require('assets/images/home/logo_hqjj.jpg')"/>
    </div>

    <section>
      <h3 class="D3title"><i class="D3iLeft"></i><b>资料提交</b><i class="D3iRight"></i></h3>
      <article class="join">参与方需提交包括产品说明、图文介绍、产品模型、手板等在内、能够展示产品外观、结构、属性的产品相关资料。 </article>
      <div class="submit">
        <i>点击下方按钮提交相关资料</i>
        <a class="no-select" href="mailto:mazhe@taihuoniao.com">产品资料提交</a>
      </div>
    </section>

    <p class="mail">咨询详情请联系 <a href="mailto:mazhe@taihuoniao.com">mazhe@taihuoniao.com</a></p>
    <div class="recruitFoot" v-if="!isMob"></div>
    <div class="recruitFoot2" v-else></div>
  </div>
</template>
<script>
  import { calcImgSize } from 'assets/js/common'
  import ElCard from '../../../../node_modules/element-ui/packages/card/src/main'
  export default {
    components: {ElCard},
    name: 'EnterpriseRecruit',
    data() {
      return {
        calcHeight: '',
        about: '2017年初，太火鸟与投资方罗莱生活、海泉基金、京东金融、麦顿资本、泰德资本以及创新工场、真格基金等战略合作方共同发起了名为 “智见未来-太火鸟AesTech联合加速计划”，希望能够将太火鸟在产品孵化方面的前瞻性与各资本方及平台、渠道方在创新产品研发、孵化、营销环节的势能最大限度发挥出来，促进设计相关产业发展，改善设计生态，惠及大众。近年，大众消费升级趋势对全产业链的影响日益显著，围绕产品孵化积淀3年的太火鸟联合京东金融，在2017年末开启联合加速计划第一期，主题为“轻创新·设计造物”，旨在引领消费升级趋势下，“以设计驱动为核心的应用创新”。本次活动由平台方与资本方联合发起，以产品孵化、营销分发为出口，以创新产品落地为导向，反向推动产品再设计与生产升级。我们希望通过本次联合资本方、设计方、制造方和品牌方共同发起的活动，能够利用优质的成熟产能，深化“设计造物”理念，充分发挥新时代、新形势下中国设计集群的能动性，凝聚多方力量，共同发现或打造更多兼具实用性与美学，面向未来广泛应用场景的创新产品。<br />打破传统，结合具体的应用环境，运用富有实用性的创新手段，强调设计与更新，颠覆大众对于传统消费级产品的印象，让日常产品同时具备功能性、强应用情景、升级美学与创新性。',
        details: '活动共计划投资1000万元人民币，有针对性地选择10款产品进行“创新再设计与制造”，届时，每款产品将各获得100万元人民币启动资金，京东金融、太火鸟与设计公司及供应链多方合作，充分利用各方优势资源，打通产品从设计、生产制造、资本运作到渠道分发等全流程全环节，进行具体产品的升级改造，通过“轻创新”打造符合新消费升级趋势下、具有强互联网属性的“爆款”单品。',
        reality: '如果你有亟待升级改造、具有“未来潜力”的设计类产品<br />如果你的产品能够证明创新在实际应用场景中的潜力<br />如果你的产品能够为我们描绘一个“设计很精，创新很轻”的日常<br />加入“设计造物”计划，让创意成为现实。'
      }
    },
    mounted() {
      let that = this
      window.addEventListener('resize', () => {
        if (that.isMob) {
          that.calcHeight = calcImgSize(670, 750, false)
        } else {
          that.calcHeight = calcImgSize(1260, 2880)
        }
      })
      if (this.isMob) {
        this.calcHeight = calcImgSize(670, 750, false)
      } else {
        this.calcHeight = calcImgSize(1260, 2880)
      }
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      }
    }
  }
</script>
<style scoped>
  .banner {
    width: 100%;
    background: url('../../../assets/images/subject/recruit/product/MTproduct@2x.jpg') no-repeat;
    background-size: cover;
  }

  .banner2 {
    width: 100%;
    background: url('../../../assets/images/subject/recruit/product/product@2x.jpg') no-repeat;
    background-size: cover;
  }

  .main {
    max-width: 960px;
    padding: 30px 20px;
    margin: 0 auto;
  }

  .D3title {
    font-weight: 600;
    text-align: center;
    font-size: 17px;
    padding: 30px 0;
    color: #aa3efd;
  }

  .D3title i {
    display: inline-block;
    width: 40px;
    height: 10px;
    margin-bottom: 1px;
  }

  .D3iLeft {
    margin-right: 12px;
    background: url('../../../assets/images/subject/recruit/enterprise/titleleft.png') no-repeat;
    background-size: cover;
  }

  .D3iRight {
    margin-left: 12px;
    background: url('../../../assets/images/subject/recruit/enterprise/titleright.png') no-repeat;
    background-size: cover;
  }

  .D3title b {
    letter-spacing: 8px;
  }

  .main article {
    font-size: 15px;
    line-height: 26px;
    color: #666;
  }

  .main .reality {
    margin-top: 5px;
    text-align: center;
    line-height: 30px;
  }

  .mail {
    color: #666;
    text-align: center;
    padding-bottom: 40px;
  }

  .mail a {
    margin-left: 10px;
    color: #aa3efd
  }

  .recruitFoot {
    height: 130px;
    background: url('../../../assets/images/subject/recruit/enterprise/foot.png') no-repeat center;
    background-size: cover;
    margin-bottom: -52px;
  }

  .recruitFoot2 {
    height: 72px;
    background: url('../../../assets/images/subject/recruit/enterprise/foot.png') no-repeat center;
    background-size: cover;
    margin-bottom: -52px;
  }

  .submit {
    text-align: center;
  }

  .submit i {
    display: block;
    margin: 15px 0;
    color: #999;
    font-size: 14px;
  }

  .submit a {
    letter-spacing: 2px;
    display: block;
    width: 200px;
    height: 42px;
    margin: 0 auto 22px;
    background: url("../../../assets/images/subject/recruit/product/Button.png") no-repeat;
    background-size: contain;
    font-size: 16px;
    color: #fff;
    line-height: 38px;
  }

  .submit a:hover {
    background: url("../../../assets/images/subject/recruit/product/Buttonhover.png") no-repeat;
    background-size: contain;
  }

  .submit a:active {
    color: #932bef;
    line-height: 42px;
    text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.5);
    background: url("../../../assets/images/subject/recruit/product/Buttonactive.png") no-repeat;
    background-size: contain;
  }

  .join {
    text-align: center;
    padding: 0 15px;
  }

  .logo-list {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
  }

  .logo-list img {
    border-radius: 50px;
    box-shadow: 0 0 10px rgba(10, 10, 10, .1);
    margin: 10px 20px;
    height: 80px;
  }

  @media screen and ( max-width: 768px) {
    .logo-list img {
      margin: 10px 10px;
      height: 60px;
    }
  }

  @media screen and ( max-width: 380px) {
    .logo-list img {
      height: 50px;
    }
  }
</style>
