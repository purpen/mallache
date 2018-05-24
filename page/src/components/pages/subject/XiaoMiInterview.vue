<template>
  <div class="xiaomi-interview">
    <div v-if="!isMob" class="banner" :style="{height : calcHeight}"></div>
    <div v-else class="banner-m" :style="{height : calcHeight}"></div>
    <div class="container">
      <article>德国iF 设计奖，创立于1953年，是由德国历史最悠久的工业设计机构--汉诺威工业设计论坛(iF Industrie 
  Forum Design)每年定期举办的。德国IF国际设计论坛每年评选iF设计奖，它以“独立、严谨、可靠” 的评奖理
  念闻名于世，旨在提升大众对于设计的认知，其最具分量的金奖素有“产品设计界的奥斯卡奖”之称。</article>
      <article>2018年，iF设计大奖公布，小米生态链众多产品获奖，铟果作为太火鸟旗下专注设计驱动创新力的SaaS平
台，借此契机，采访了小米生态链负责人刘德和生态链内数位企业家，希望从他们的故事中，找到获奖产品背后不
为人知的细节。</article>
    </div>
    <div class="banner-liude" v-if="!isMob" :style="{height : calcHeight}">
      <p class="clearfix">
        <span class="title">小米生态链负责人刘德</span>
        <span class="info">工业设计师创业的底牌、痛点与危机</span>
        <a target="_blank" href="https://www.d3ingo.com/article/show/123"></a>
      </p>
    </div>
    <a target="_blank" href="https://www.d3ingo.com/article/show/123"><div v-if="isMob" class="banner-liude-m" :style="{height : calcHeight2}"></div></a> <!-- 移动端 -->
    <div class="people" v-if="!isMob">
      <div class="people-pic"></div>
      <div class="picture-cover">
        <p class="picture-cover-title">小米生态链企业家</p>
        <p class="picture-cover-more">更多专访敬请期待...</p>
      </div>
    </div>
    <div class="people-m" v-if="isMob" :style="{height : calcHeight}"></div>
    <div class="interview-footer">
      <div class="footer-pic">
        <div class="footer-pic-cover">
          <p>铟果作为太火鸟旗下的全新平台，以推广“设计造物”与“轻创新”概念为初衷。在“大众创业、万众创新”热潮下，铟果回首中国工业设计行业发展历程，与工业设计领域的创业设计师和领军人物们对话，希望能够从亲历中国工业设计发展史的“创业践行者”和“先驱设计师”们身上，学习经验，聆听感悟，指引未来，共同前行。</p></div>
      </div>
    </div>
  </div>
</template>
<script>
import { calcImgSize } from 'assets/js/common'
export default {
  name: 'xiaomiInterview',
  data() {
    return {
      calcHeight: '',
      calcHeight2: ''
    }
  },
  mounted() {
    let that = this
    window.addEventListener('resize', () => {
      if (that.isMob) {
        that.calcHeight = calcImgSize(460, 750, false)
      } else {
        that.calcHeight = calcImgSize(1040, 2880)
      }
    })
    if (this.isMob) {
      this.calcHeight = calcImgSize(460, 750, false)
    } else {
      this.calcHeight = calcImgSize(1040, 2880)
    }
    window.addEventListener('resize', () => {
      if (that.isMob) {
        that.calcHeight2 = calcImgSize(415, 690, false)
      }
    })
    if (this.isMob) {
      this.calcHeight2 = calcImgSize(415, 690, false)
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
    },
    isCompany() {
      return this.$store.state.event.user.type === 2
    }
  }
}
</script>
<style scoped>
  .banner {
    background: url('../../../assets/images/subject/xiaomi/mibanner.png') no-repeat bottom;
    background-size: cover;
  }
  .banner-m {
    background: url('../../../assets/images/subject/xiaomi/Mbanner.jpg') no-repeat bottom;
    background-size: cover;
  }
  .container {
    padding: 50px 0
  }
  article {
    max-width: 920px;
    margin: 0 auto;
    color: #222222;
    font-size: 16px;
    line-height: 1.5;
    text-indent: 2em
  }
  .banner-liude {
    position: relative;
    background: url('../../../assets/images/subject/xiaomi/liude.png') no-repeat top;
    background-size: contain;
    display: flex;
    align-items: center;
    margin-bottom: 80px;
  }
  .banner-liude-m {
    background: url('../../../assets/images/subject/xiaomi/Mliude.jpg') no-repeat top;
    background-size: contain;
  }
  .banner-liude p {
    width: 50%;
    padding-right: 68px;
    text-align: right
  }
  .title {
    white-space: nowrap;
    display: block;
    font-size: 30px;
    color: #fff;
    padding-bottom: 13px;
    letter-spacing: 8px;
  }
  .info {
    white-space: nowrap;
    display: block;
    font-size: 18px;
    color: #fff;
    letter-spacing: 6px;
    padding-bottom: 20px;
  }
  .banner-liude a {
    display: inline-block;
    width: 156px;
    height: 38px;
    background: url('../../../assets/images/subject/xiaomi/button.png') no-repeat top;
    background-size: cover;
    transform: scale(0.8)
  }
  
  .banner-liude a:active {
    transform: scale(0.76)
  }
  .people {
    height: 492px;
    background: #ededed;
    position: relative;
  }
  .people-m {
    background: url('../../../assets/images/subject/xiaomi/Mpeople.jpg') no-repeat top;
    background-size: contain;
    margin: 30px 0;
  }
  .people-pic {
    max-width: 1176px;
    height: 419px;
    margin: 0 auto;
    background: url('../../../assets/images/subject/xiaomi/people.png') no-repeat top;
    background-size: cover;
  }
  .picture-cover {
    max-width: 1306px;
    width: calc( 100% - 60px);
    margin: 0 30px;
    height: 430px;
    position: absolute;
    top: -50px;
    left: 0;
    border-radius: 10px;
    border: 4px solid #ca1317
  }
  
  .picture-cover-title {
    position: absolute;
    letter-spacing: 8px;
    left: 50%;
    transform: translateX(-50%);
    top: -18px;
    background: #fff;
    padding: 0 30px;
    font-size: 36px;
    color: #ca1317
  }
  .picture-cover-more {
    position: absolute;
    letter-spacing: 8px;
    left: 50%;
    transform: translateX(-50%);
    top: 60%;
    font-size: 30px;
    opacity: 0.6;
    color: #fff;
    padding: 10px 52px 10px 60px;
    border-radius: 10px;
    background: #ca1317;
  }
  .interview-footer {
    height: 478px;
    padding-bottom: 65px;
    background: #ca1317;
    margin-bottom: -52px;
  }
  .footer-pic {
    height: 413px;
    background:  url('../../../assets/images/subject/xiaomi/footer.png') no-repeat top;
      position: relative
  }
  .footer-pic-cover {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.65);
    display: flex;
    justify-content: center;
    align-items: center
  }
  .footer-pic-cover p {
    max-width: 920px;
    color: #fff;
    font-size: 16px;
    line-height: 1.5;
    text-indent: 2em;
  }
  @media screen and (max-width: 1176px) {
    .people-pic {
      margin: 0 64px;
      background-size: contain
    }
    
  }
  @media screen and (max-width: 768px) {
    .container {
      padding: 20px 15px;
    }
    .footer-pic-cover p {
      padding: 0 15px
    }
  }
</style>
