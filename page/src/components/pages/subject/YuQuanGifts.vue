<template>
  <div class="yuquan">
    <div :class="[isMob ? 'banner2': 'banner']" :style="{height : calcHeight}">
      <a @click="upload" class="add-work">上传作品</a>
    </div>
    <div class="giftbody">
      <h2><span class="icon">羽泉的礼物创新产品征集</span></h2>
      <p class="padding26">
        2017年底，内地知名唱作组合羽泉与京东金融结成战略合作伙伴，双方在组合成立20周年巡回演唱会期间，将以羽泉组合这一明星IP为核心，衍生系列创意创新活动，内容涵盖广泛产品，活动名称为“羽泉的礼物”。</p>
      <p>太火鸟·铟果D3INGO作为“羽泉的礼物”合作方和资源入口之一，为羽泉与京东金融提供设计支持、创新产品征集、全网分销运营等环节的相关资源，现广泛征集创新产品，活动周期一年。</p>

      <h2><span class="icon">具体要求</span></h2>
      <p>有意参与的设计公司在铟果平台提交最新设计的创新产品；</p>
      <p>产品品牌、种类不限，但要具备相当的实用性，且需考虑后期配合羽泉IP的外观及应用场景；</p>
      <p>产品还需同时符合京东众筹平台对征选产品的要求。</p>

      <h2><span class="icon">入选产品将获得</span></h2>
      <p class="padding26">
        羽泉IP形象使用权一年；<br />
        入选产品直接进入京东众筹平台和相关销售渠道，享受平台营销资源。</p>
      <p>所有提交产品最终将由京东平台筛选并安排众筹时间，合作细节将由京东金融与产品所属方直接讨论决定。</p>

      <h2><span class="icon">主办单位</span></h2>
      <section class="img-list clearfix">
        <div v-for="(ele, index) in imgList">
          <img v-lazy="require(`assets/images/subject/gifts/${ele}@2x.png`)">
        </div>
      </section>
      <span class="explain"><i>*</i>该活动最终解释权归京东金融所有<i>*</i></span>
    </div>
    <div class="giftfoot">
    </div>
  </div>
</template>
<script>
import { calcImgSize } from 'assets/js/common'
import store from '@/store/index'
import * as types from '@/store/mutation-types'
export default {
  name: 'YuQuanGifts',
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
        that.calcHeight = calcImgSize(1040, 2880)
      }
    })
    if (this.isMob) {
      this.calcHeight = calcImgSize(840, 750, false)
    } else {
      this.calcHeight = calcImgSize(1040, 2880)
    }
  },
  methods: {
    upload() {
      if (this.isLogin) {
        if (!this.isCompany) {
          this.$message({
            message: '此活动只允许设计服务商参与',
            type: 'error',
            duration: 1000
          })
        } else {
          this.$router.push({ name: 'vcenterMatchCaseSubmit' })
        }
      } else {
        store.commit(types.PREV_URL_NAME, 'YuQuanGifts')
        this.$router.push({ name: 'login', params: { url: 'yq', type: 2 } })
      }
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
  background: url('../../../assets/images/subject/gifts/HeadBanner@2x.jpg')
    no-repeat center;
  background-size: cover;
  position: relative;
}

.banner2 {
  background: url('../../../assets/images/subject/gifts/MBanner.jpg') no-repeat
    center;
  background-size: cover;
  position: relative;
}

.add-work {
  text-indent: -9999rem;
  position: absolute;
  bottom: 20px;
  left: 0;
  right: 0;
  width: 180px;
  height: 45px;
  margin: auto;
  background: url('../../../assets/images/subject/gifts/upload@2x.png') left
    no-repeat;
  background-size: cover;
}

.add-work:hover {
  background: url('../../../assets/images/subject/gifts/upload@2x.png') center
    no-repeat;
  background-size: cover;
}

.add-work:active {
  background: url('../../../assets/images/subject/gifts/upload@2x.png') right
    no-repeat;
  background-size: cover;
}

.giftbody {
  max-width: 920px;
  margin: 0 auto;
  text-align: center;
  padding: 0 15px 60px;
}

.giftbody h2 {
  font-size: 18px;
  color: #a47339;
  padding: 60px 0 30px;
}

.giftbody p {
  color: #222222;
  font-size: 14px;
  line-height: 26px;
}

.padding26 {
  padding-bottom: 26px;
}

.img-list {
  padding-bottom: 30px;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
}

.img-list div {
  width: 14%;
  margin: 10px 1%;
}

.img-list img {
  width: 100%;
  height: 50px;
  margin-bottom: 10px;
  box-shadow: 0 0 10px rgba(10, 10, 10, 0.1);
  border-radius: 50px;
}

.explain {
  font-size: 14px;
  background: #a47339;
  opacity: 0.7;
  color: #ffffff;
  padding: 4px 8px;
  border-radius: 15px;
}

.explain i {
  padding: 0 4px;
}

.giftfoot {
  height: 158px;
  background: url('../../../assets/images/subject/gifts/bottom@2x.png')
    no-repeat center;
  -webkit-background-size: cover;
  background-size: cover;
  margin-bottom: -52px;
}

.icon {
  position: relative;
}

.icon:before {
  content: '';
  position: absolute;
  left: -30px;
  top: 0;
  width: 20px;
  height: 20px;
  background: url('../../../assets/images/subject/gifts/decorate@2x.png')
    no-repeat;
  -webkit-background-size: contain;
  background-size: contain;
}

.icon:after {
  content: '';
  position: absolute;
  right: -30px;
  top: 0;
  width: 20px;
  height: 20px;
  background: url('../../../assets/images/subject/gifts/decorate@2x.png')
    no-repeat;
  -webkit-background-size: contain;
  background-size: contain;
}

@media screen and (max-width: 767px) {
  .img-list div {
    width: 36%;
    margin: 10px 2%;
  }
}
</style>
