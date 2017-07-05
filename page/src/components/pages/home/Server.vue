<template>
  <div class="content-box">
    <div>
      <el-row :gutter="0">
        <el-col :span="12" class="banner-item left">
          <a href="javascript:void(0);" class="is-active">我是客户</a>
        </el-col>
        <el-col :span="12" class="banner-item right">
          <router-link :to="{name: 'serverDesign'}">我是设计公司</router-link>
        </el-col>
      </el-row>
    </div>

    <div class="banner">
      <h3>成为铟果SaaS设计客户</h3>
      <p>铟果能为项目需求方提供上千家可供选择的专业设计服务供应商，运用云平台后台大数据计算，帮您高效、精准地找到心仪的设计服务供应商。</p>
    </div>

    <div class="item item_2">
      <div class="item_2_box">
        <el-row :gutter="24">
          <el-col :span="8">
            <div class="">
              <img src="../../../assets/images/home/server_10.png" />
              <p class="item_1_title">专业服务</p>
              <p class="item_1_desc">SaaS能为项目需求方提供上千家可供选择的专业设计服务供应商，为您解决问题</p>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="">
              <img src="../../../assets/images/home/server_11.png" />
              <p class="item_1_title">精准匹配</p>
              <p class="item_1_desc">云平台后台大数据计算，帮您高效、精准地找到心仪的设计服务供应商</p>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="">
              <img src="../../../assets/images/home/server_12.png" />
              <p class="item_1_title">便捷高效</p>
              <p class="item_1_desc">您只需要向SaaS说明您的设计需求，就能在最短的时间内看到符合您要求的供应商信息</p>
            </div>
          </el-col>
        </el-row>
      </div>
    </div>

    <div class=" container">
    
        <div class="pub-title">发布项目流程</div>
        <div class="process">
          <div class="item-process">
            <img src="../../../assets/images/item/item_dj.png" />
            <p>1.支付预付金</p>
          </div>
          <div class="item-process line"></div>
          <div class="item-process">
            <img src="../../../assets/images/item/item_lx.png" />
            <p>2.选择项目类型</p>
          </div>
          <div class="item-process line"></div>
          <div class="item-process">
            <img src="../../../assets/images/item/item_xq.png" />
            <p>3.完善项目需求</p>
          </div>
          <div class="item-process line"></div>
          <div class="item-process">
            <img src="../../../assets/images/item/item_lxx.png" />
            <p>4.填写公司信息</p>
          </div>
          <div class="item-process line"></div>
          <div class="item-process">
            <img src="../../../assets/images/item/item_fb.png" />
            <p>5.检查并发布</p>
          </div>
          <div class="item-process line"></div>
          <div class="item-process">
            <img src="../../../assets/images/item/item_tj.png" />
            <p>6.匹配设计服务</p>
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
    background: url('../../../assets/images/home/service_banner.jpg');
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
    padding: 10px 2px;
    color: #555;
  }
  .process .item-process img {
    width: 60%;
  }
  .process .item-process.line {
    width: 4.5%;
    height: 30%;
    border-bottom: 1px dashed #979797;
  }


</style>
