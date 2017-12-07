<template>
  <section class="submit_one">
    <div class="pub-content" :style="{ height: calcHeight }">
      <div class="shade-cover" v-if="isMob"></div>
      <div class="shade">
        <p class="b-title">发布项目需求</p>
        <p class="info">
          铟果SaaS平台，这里有专业、智能、高效的设计服务，针对您的设计项目需求，我们将为您匹配专业设计服务供应商，实现您的项目Idea，为您提供全方位的设计解决方案。</p>
      </div>
    </div>
    <div class="container">
      <div class="blank20"></div>
      <el-row :gutter="24" type="flex" justify="center">
        <el-col :span="24">

          <div class="pub-title pub-btn">
            <p class="p-money">¥<span class="money">99</span> / 次</p>
            <p>
              <router-link :to="{name: 'itemPayment'}">
                <el-button class="is-custom" type="primary" size="large">立即支付</el-button>
              </router-link>
            </p>
          </div>

          <div class="process" v-if="!isMob">
            <div class="pub-title process-title">发布项目流程</div>
            <div class="item">
              <img src="../../../assets/images/item/item_dj.png"/>
              <p>1.支付预付金</p>
            </div>
            <div class="item line"></div>
            <div class="item">
              <img src="../../../assets/images/item/item_lx.png"/>
              <p>2.选择项目类型</p>
            </div>
            <div class="item line"></div>
            <div class="item">
              <img src="../../../assets/images/item/item_xq.png"/>
              <p>3.完善项目需求</p>
            </div>
            <div class="item line"></div>
            <div class="item">
              <img src="../../../assets/images/item/item_lxx.png"/>
              <p>4.填写公司信息</p>
            </div>
            <div class="item line"></div>
            <div class="item">
              <img src="../../../assets/images/item/item_fb.png"/>
              <p>5.检查并发布</p>
            </div>
            <div class="item line"></div>
            <div class="item item-last">
              <img src="../../../assets/images/item/item_tj.png"/>
              <p>6.匹配设计服务商</p>
            </div>
          </div>

          <el-row class="m-process" v-if="isMob">
            <div class="pub-title process-title">发布项目流程</div>
            <div class="item-process">
              <img src="../../../assets/images/item/item_dj.png"/>
              <p>1.支付预付金</p>
            </div>
            <div class="item-process">
              <img src="../../../assets/images/item/item_lx.png"/>
              <p>2.选择项目类型</p>
            </div>
            <div class="item-process">
              <img src="../../../assets/images/item/item_xq.png"/>
              <p>3.完善项目需求</p>
            </div>
            <div class="item-process">
              <img src="../../../assets/images/item/item_lxx.png"/>
              <p>4.填写公司信息</p>
            </div>
            <div class="item-process">
              <img src="../../../assets/images/item/item_fb.png"/>
              <p>5.检查并发布</p>
            </div>
            <div class="item-process item-last">
              <img src="../../../assets/images/item/item_tj.png"/>
              <p>6.匹配设计服务商</p>
            </div>
          </el-row>

          <div class="clear"></div>
          <div class="pub-title pub-btn about">
            <p class="advance">关于预付金：<span>￥99元是您每次发布项目需要支付的预付金</span></p>
            <p class="des-title">我为什么要付预付金？</p>
            <p class="des">预付金是为了保证您发布项目的严肃性和可执行性，让我们知道您是认真的。</p>

            <p class="des-title">预付金去哪了？</p>
            <p class="des">项目成功找到设计服务供应商后，预付金将进入您为该项目向设计方预付的订金，成为项目订金的一部分；如果项目对接失败，预付金将被退回您的账户。</p>
          </div>
        </el-col>
      </el-row>
    </div>
  </section>
</template>

<script>
  import {
    calcImgSize
  } from 'assets/js/common'
  export default {
    name: 'item_submit_one',
    data() {
      return {
        msg: 'This is About!!!',
        calcHeight: ''
      }
    },
    created() {
      document.body.scrollTop = 0
      document.documentElement.scrollTop = 0
    },
    mounted() {
      let that = this
      window.addEventListener('resize', () => {
        if (that.isMob) {
          that.calcHeight = calcImgSize(180, 320)
        } else {
          that.calcHeight = calcImgSize(400, 1180)
        }
      })
      if (that.isMob) {
        that.calcHeight = calcImgSize(200, 320)
      } else {
        that.calcHeight = calcImgSize(400, 1180)
      }
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
  .m-process {
    background: #fafafa;
  }

  .pub-title {
    text-align: center;
    font-size: 2rem;
    margin: 20px;
  }

  .pub-title.process-title {
    font-weight: 400;
    margin: 80px 0 30px 0;
  }

  .pub-content {
    background: url('../../../assets/images/item/item_submit_01.jpg') no-repeat right;
    background-size: cover;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .shade p {
    color: #fff;
    text-align: center;
    font-size: 1.8rem;
    padding: 30px 20% 20px 20%;
    line-height: 2;
    font-weight: 200;
  }

  .shade p.b-title {
    font-weight: 400;
    font-size: 3.5rem;
    padding-top: 0;
  }

  .process {
    height: 150px;
  }

  .process .item {
    width: 9.3%;
    margin: 10px 1%;
    text-align: center;
    float: left;
  }

  .process .item img,
  .process .item p {
    padding: 10px 2px;
    color: #555;
  }

  .process .item img {
    width: 65%;
  }

  .process .item-last p {
    width: 100%;
  }

  .process .item.line {
    width: 4%;
    height: 30%;
    border-bottom: 1px dashed #979797;
  }

  .pub-btn {
    margin: 50px 0 30px 0;
  }

  .pub-btn p {
    margin: 20px;
    color: #666;
  }

  .pub-btn .advance {
    color: #FF6C70;
    font-size: 1.5rem
  }

  .pub-btn .money {
    font-size: 2.5rem;
    color: #FF5A5F;
  }

  .pub-btn .p-money {
    font-size: 2rem;
  }

  .pub-btn p button {
    padding: 10px 40px 10px 40px;
  }

  .pub-btn .des {
    font-size: 1rem;
    color: #999;
  }

  p.des-title {
    font-size: 1rem;
    color: #222;
    margin-bottom: -10px;
    font-weight: 400;
  }

  .shade-cover {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background: rgba(0, 0, 0, 0.1);
  }

  @media screen and (max-width: 767px) {
    .pub-content {
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .pub-content .shade {
      width: 80%;
    }

    .shade p {
      padding: 0;
      font-size: 1.2rem;
      line-height: 1.5;
    }

    .shade p.b-title {
      padding-top: 0;
      padding-bottom: 5%;
      font-size: 1.7rem;
    }

    .pub-btn {
      margin: 0;
    }

    .m-process {
      padding-top: 30px;
    }

    .pub-title.process-title {
      margin-top: 0;
    }

    .pub-title.about {
      padding-top: 10px;
      text-align: left;
    }

    p.des-title,
    .pub-btn .des {
      font-size: 1.4rem;
      font-family: "PingFangSC-Medium", "Microsoft Yahei" !important;
    }

    .pub-btn .des {
      color: #666;
      line-height: 1.5;
    }

    .pub-btn .advance span {
      display: block;
    }

    .pub-title.about span {
      line-height: 2;
    }
  }
</style>
