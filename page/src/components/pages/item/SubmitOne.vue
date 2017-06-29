<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="24" type="flex" justify="center">
      <el-col :span="24">
        <div class="pub-content">
          <div class="shade">
            <p class="b-title">发布项目需求</p>
            <p>铟果SaaS平台，这里有专业、智能、高效的设计服务，针对您的设计项目需求，我们将为您匹配专业设计服务供应商，实现您的项目Idea，为您提供全方位的设计解决方案。</p>
          </div>
        </div>

        <div class="pub-title pub-btn">
          <p class="p-money">¥ <span class="money">99</span> / 次</p>
          <p><router-link :to="{name: 'itemPayment'}"><el-button class="is-custom" type="primary" size="large">立即支付</el-button></router-link></p>
        </div>

        <div class="pub-title process-title">发布项目流程</div>
        <div class="process">
          <div class="item">
            <img src="../../../assets/images/item/item_dj.png" />
            <p>1.支付预付金</p>
          </div>
          <div class="item line"></div>
          <div class="item">
            <img src="../../../assets/images/item/item_lx.png" />
            <p>2.选择项目类型</p>
          </div>
          <div class="item line"></div>
          <div class="item">
            <img src="../../../assets/images/item/item_xq.png" />
            <p>3.完善项目需求</p>
          </div>
          <div class="item line"></div>
          <div class="item">
            <img src="../../../assets/images/item/item_lxx.png" />
            <p>4.填写公司信息</p>
          </div>
          <div class="item line"></div>
          <div class="item">
            <img src="../../../assets/images/item/item_fb.png" />
            <p>5.检查并发布</p>
          </div>
          <div class="item line"></div>
          <div class="item">
            <img src="../../../assets/images/item/item_tj.png" />
            <p>6.匹配设计服务</p>
          </div>
        </div>
        <div class="clear"></div>
        <div class="pub-title pub-btn">
          <p>关于预付金：￥99元是您每次发布项目需要支付的预付金</p>
          <p class="des-title">我为什么要付预付金？</p>
          <p class="des">预付金是为了保证您发布项目的严肃性和可执行性，让我们知道您是认真的。</p>

          <p class="des-title">预付金去哪了？</p>
          <p class="des">项目成功找到设计服务供应商后，预付金将进入您为该项目向设计方预付的订金，成为项目订金的一部分；如果项目对接失败，预付金将被退回您的账户。</p>
        </div>
      </el-col>
    </el-row>
  </div>
</template>

<script>
export default {
  name: 'item_submit_one',
  data () {
    return {
      msg: 'This is About!!!'
    }
  },
  created: function() {
    document.body.scrollTop = 0
    document.documentElement.scrollTop = 0
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .pub-title {
    text-align: center;
    font-size: 2rem;
    margin: 20px;
  }
  .pub-title.process-title {
    margin: 80px 0 30px 0;
  }
  .pub-content {
    height: 400px;
    background: url('../../../assets/images/item/item_submit_01.jpg') no-repeat;
    background-size: cover;
    position: relative;
  }
  .pub-content .shade {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    background-color: #000000;
    background-color: rgba(0,0,0,0.1);
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
    padding-top: 80px;
    font-size: 3.5rem;

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
  .process .item img, .process .item p {
    padding: 10px 2px;
    color: #555;
  }
  .process .item img {
    width: 60%;
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
  }

</style>
