<template>
  <div class="container">
    <div class="pub-title">发布项目需求</div>
    <div class="pub-content">
      <div class="shade">
        <p>太火鸟SaaS平台提供智能、专业、高效的设计服务，在这里你可以发布产品设计、UI／UX设计的项目需求，为你智能匹配专业设计服务，并提供全方位的解决方案。</p>
      </div>
    </div>
    <div class="pub-title process-title">发布流程</div>
    <div class="process">
      <div class="item">
        <img src="../../../assets/images/item/item_dj.png" width="68" />
        <p>1.预付99元定金</p>
      </div>
      <div class="item line"></div>
      <div class="item">
        <img src="../../../assets/images/item/item_fb.png" width="68" />
        <p>2.选择设计类型</p>
      </div>
      <div class="item line"></div>
      <div class="item">
        <img src="../../../assets/images/item/item_xq.png" width="68" />
        <p>3.填写项目需求</p>
      </div>
      <div class="item line"></div>
      <div class="item">
        <img src="../../../assets/images/item/item_lxx.png" width="68" />
        <p>4.填写联系信息</p>
      </div>
      <div class="item line"></div>
      <div class="item">
        <img src="../../../assets/images/item/item_fb.png" width="68" />
        <p>5.发布项目</p>
      </div>
      <div class="item line"></div>
      <div class="item">
        <img src="../../../assets/images/item/item_tj.png" width="68" />
        <p>6.推荐设计公司</p>
      </div>
    </div>
    <div class="clear"></div>
    <div class="pub-title pub-btn">
      <p>¥ <span class="money">99</span> / 次</p>
      <p><router-link :to="{name: 'itemPayment'}"><el-button class="is-custom" type="primary" size="large">立即支付</el-button></router-link></p>
      <p class="des"><span>*</span> 99元仅作为项目发布的预付金，项目对接失败将退换到账户，项目对接成功后将作为预付金，客户只需支付项目剩下金额即可</p>
    </div>

  </div>
</template>

<script>
export default {
  name: 'item_submit_one',
  data () {
    return {
      msg: 'This is About!!!'
    }
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
    height: 500px;
    background: url('../../../assets/images/item/item_submit_01.png') no-repeat;
    background-size: cover;
    position: relative;
  }
  .pub-content .shade {
    height: 130px;
    width: 100%;
    position: absolute;
    top: 370px;
    background-color: #000000;
    background-color: rgba(0,0,0,0.5);
  }
  .shade p {
    color: #fff;
    text-align: center;
    padding: 30px 20% 20px 20%;
    line-height: 2;
  }
  .process {
  
  }
  .process .item {
    margin: 10px 1%;
    text-align: center;
    float: left;
  }
  .process .item img, .process .item p {
    padding: 5px;
    color: #555;
  }
  .process .item.line {
    width: 5%;
    height: 50px;
    line-height: 30px;
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
  .pub-btn p button {
    padding: 10px 40px 10px 40px;
  }
  .pub-btn .des {
    font-size: 1rem;
    color: #999;
  }

</style>
