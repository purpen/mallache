<template>
  <div class="content-box">
    <div>
      <el-row :gutter="0">
        <el-col :span="12" class="banner-item left">
          <router-link :to="{name: 'server'}">我是客户</router-link>
        </el-col>
        <el-col :span="12" class="banner-item right">
          <a href="javascript:void(0);" class="is-active">我是设计公司</a>
        </el-col>
      </el-row>
    </div>

    <div class="banner">
      <h3>成为铟果SaaS设计服务商</h3>
      <p>用专业服务，助力客户精准定位，从不同的角度观察、思考，感知风险，寻找机会，利用设计思维保持您竞争的优势。</p>
    </div>

    <div class="item item_2">
      <div class="item_2_box">
        <el-row :gutter="24">
          <el-col :span="8">
            <div class="">
              <img src="../../../assets/images/home/server_01.png" />
              <p class="item_1_title">需求明确</p>
              <p class="item_1_desc">3万+项目需求方带着他们明确的设计需求在这里等您</p>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="">
              <img src="../../../assets/images/home/server_02.png" />
              <p class="item_1_title">创意变现</p>
              <p class="item_1_desc">需求方主动下单，接单无忧，设计费有保障</p>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="">
              <img src="../../../assets/images/home/server_03.png" />
              <p class="item_1_title">版权保护</p>
              <p class="item_1_desc">SaaS平台担保版权，设计服务供应商放心将全部精力用来服务客户</p>
            </div>
          </el-col>
        </el-row>
      </div>
    </div>

    <div class=" container">

        <div class="pub-title">入驻设计服务供应商</div>
        <div class="design-company">
          <img src="../../../assets/images/home/server_logo_01.png" />
          <img src="../../../assets/images/home/server_logo_02.png" />
          <img src="../../../assets/images/home/server_logo_03.png" />
          <img src="../../../assets/images/home/server_logo_04.png" />
          <img src="../../../assets/images/home/server_logo_05.png" />
        </div>
    
        <div class="pub-title clear">公司入驻流程</div>
        <div class="process">
          <div class="item-process">
            <img src="../../../assets/images/item/d_item_join.png" />
            <p>1.加入铟果</p>
          </div>
          <div class="item-process line"></div>
          <div class="item-process">
            <img src="../../../assets/images/item/d_item_ws.png" />
            <p>2.完善公司信息</p>
          </div>
          <div class="item-process line"></div>
          <div class="item-process">
            <img src="../../../assets/images/item/d_item_rz.png" />
            <p>3.实名认证</p>
          </div>
          <div class="item-process line"></div>
          <div class="item-process">
            <img src="../../../assets/images/item/d_item_jd.png" />
            <p>4.接单设置</p>
          </div>
          <div class="item-process line"></div>
          <div class="item-process">
            <img src="../../../assets/images/item/d_item_sc.png" />
            <p>5.上传案列</p>
          </div>
          <div class="item-process line"></div>
          <div class="item-process">
            <img src="../../../assets/images/item/d_item_dd.png" width="68" />
            <p>6.等待审核</p>
          </div>
        </div>
    
    
    </div>

    <div class="container">
    </div>

  </div>


</template>

<script>
export default {
  name: 'test',
  data () {
    return {
      msg: 'This is Home'
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
  .container{
  }

  .banner-item {
    margin: 10px 0;
    text-align: center;
  }
  .banner-item a {
    font-size: 1.8rem;
    color: #666;
    line-height: 1.5;
  }

  .banner-item a.is-active {
    color: #FF5A5F;
  }

  .left {
    border-right: 1px solid #ccc;
  }
  .right {
    border-left: 1px solid #ccc;
  }

  .banner {
    width: 100%;
    height: 650px;
    background: url('../../../assets/images/home/service_banner_02.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    text-align: center;
  }

  .banner h3 {
    padding-top: 220px;
    padding-bottom: 10px;
    font-size: 5rem;
    color: #fff;
    line-height: 2;
    font-weight: 300;
  }
  .banner p {
    font-size: 3rem;
    color: #fff;
    font-weight: 300;
    padding: 0 150px;
  }

  .item_1_title {
    color: #222;
    font-size: 1.5rem;
    margin-top: 10px;
    margin-bottom: 10px;
    line-height: 2;
  }
  .item_1_desc {
    color: #666;
    font-size: 1rem;
    line-height: 1.5;
    padding: 0 20px;
  }

  .item_2 {
    background-color: #FAFAFA;
  }

  .item_2_box {
    margin: 0 8%;
  }
  .item_2_box img {
    width: 30%;
  }

  .item {
    text-align: center;
    padding: 60px 0 40px 0;
  }

  .item_3 {
  }

  .pub-title {
    font-size: 2rem;
    margin: 60px 0 40px 0;
    text-align: center;
  }

  .process {
    text-align: center;
    margin: 0 auto;
    height: 150px;
  }
  .process .item-process {
    width: 9%;
    margin: 10px 1%;
    text-align: center;
    float: left;
  }
  .process .item-process img, .process .item-process p {
    padding: 10px 1%;
    color: #555;
  }
  .process .item-process img {
    width: 60%;
  }
  .process .item-process.line {
    width: 4%;
    height: 30%;
    border-bottom: 1px dashed #979797;
  }

  .design-company {
    height: 150px;
  }
  .design-company img {
    width: 10%;
    float: left;
    margin: 10px 5%;
  }


</style>
