<template>

    <el-row class="progress-box" :gutter="0" type="flex" justify="center">
      <el-col :span="5">
        <p id="p0" class="first">1.选择设计类型</p>
      </el-col>
      <el-col :span="5">
        <p id="p1" class="base">2.填写项目需求</p>
      </el-col>
      <el-col :span="5">
        <p id="p2" class="base">3.填写联系信息</p>
      </el-col>
      <el-col :span="5">
        <p id="p3" class="base">4.检查并发布</p>
      </el-col>
      <el-col :span="5">
        <p id="p4" class="over">5.推荐设计公司</p>
      </el-col>
    </el-row>

</template>

<script>
  export default {
    name: 'item_progress',
    props: {
      baseStep: Boolean,
      companyStep: Boolean,
      checkStep: Boolean,
      stickStep: Boolean
    },
    data () {
      return {
        msg: ''
      }
    },
    mounted: function() {
      if (this.baseStep) {
        document.getElementById('p0').className += ' pass'
        document.getElementById('p1').className += ' active'
      }
      if (this.companyStep) {
        document.getElementById('p0').className += ' pass'
        document.getElementById('p1').className += ' pass'
        document.getElementById('p2').className += ' active'
      }
      if (this.checkStep) {
        document.getElementById('p0').className += ' pass'
        document.getElementById('p1').className += ' pass'
        document.getElementById('p2').className += ' pass'
        document.getElementById('p3').className += ' active'
      }
      if (this.stickStep) {
        document.getElementById('p0').className += ' pass'
        document.getElementById('p1').className += ' pass'
        document.getElementById('p2').className += ' pass'
        document.getElementById('p3').className += ' pass'
        document.getElementById('p4').className += ' active'
      }
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .progress-box {
    height: 60px;
    margin: 20px 0 20px 0;
    width: 100%;
  }
  .el-col {
    line-height: 60px;
    text-align: center;
    color: #666;
  }
  p.first {
    background: url('../../../assets/images/item/step_type_active.png') no-repeat;
    color: #fff;
    background-size: cover;
  }
  p.first.pass {
    background: url('../../../assets/images/item/step_type_pass.png') no-repeat;
    background-size: cover;
    color: #666;
  }
  p.first p{
    color: #00AC84;
  }
  p.base {
    background: url('../../../assets/images/item/step_base.png') no-repeat;
    background-size: cover;
  }
  p.base.pass {
    background: url('../../../assets/images/item/step_base_pass.png') no-repeat;
    background-size: cover;
  }
  p.base.active {
    background: url('../../../assets/images/item/step_base_active.png') no-repeat;
    background-size: cover;
    color: #fff;
  }
  p.base.active p{
    color: #00AC84; 
  }
  p.over {
    background: url('../../../assets/images/item/step_over.png') no-repeat;
    background-size: cover;
  }
  p.over.active {
    background: url('../../../assets/images/item/step_over_active.png') no-repeat;
    background-size: cover;
    color: #fff;
  }
  p.over {
  
  }
  p.over.active p{
  
  }

</style>
