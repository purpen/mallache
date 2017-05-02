<template>
  <div class="progress-box">
  <ul>
    <li class="first">
      <p>1.选择设计类型</p>
    </li>
    <li class="base" id="p1">
      <p>2.填写项目需求</p>
    </li>
    <li class="base" id="p2">
      <p>3.填写联系信息</p>
    </li>
    <li class="base" id="p3">
      <p>4.检查并发布</p>
    </li>
    <li class="over" id="p4">
      <p>5.推荐设计公司</p>
    </li>
  </ul>
  </div>
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
        document.getElementById('p1').className += ' active'
      }
      if (this.companyStep) {
        document.getElementById('p1').className += ' active'
        document.getElementById('p2').className += ' active'
      }
      if (this.checkStep) {
        document.getElementById('p1').className += ' active'
        document.getElementById('p2').className += ' active'
        document.getElementById('p3').className += ' active'
      }
      if (this.stickStep) {
        document.getElementById('p1').className += ' active'
        document.getElementById('p2').className += ' active'
        document.getElementById('p3').className += ' active'
        document.getElementById('p4').className += ' active'
      }
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .progress-box {
    height: 65px;
    margin: 20px 0 20px 0;
  }
  ul li {
    float: left;
    width: 250px;
    height: 60px;
    margin: 0 -7px 0 0;
  }
  li p {
    line-height: 60px;
    text-align: center;
    color: #666;
  }
  li.first {
    background: url('../../../assets/images/item/step_type_active.png') no-repeat;
    background-size: cover;
  }
  li.first p{
    color: #00AC84;
  }
  li.base {
    background: url('../../../assets/images/item/step_base.png') no-repeat;
    background-size: cover; 
  }
  li.base.active {
    background: url('../../../assets/images/item/step_base_active.png') no-repeat;
    background-size: cover; 
  }
  li.base.active p{
    color: #00AC84; 
  }
  li.over {
    background: url('../../../assets/images/item/step_over.png') no-repeat;
    background-size: cover;  
  }
  li.over.active {
    background: url('../../../assets/images/item/step_over_active.png') no-repeat;
    background-size: cover;  
  }
  li.over p{
  
  }
  li.over.active p{
  
  }

</style>
