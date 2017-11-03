<template>

    <el-row class="progress-box" :gutter="0" type="flex" justify="center">
      <el-col :span="5">
        <p id="p0" class="first" @click="redirect" step="1">1.选择设计类型</p>
      </el-col>
      <el-col :span="5">
        <p id="p1" class="base" @click="redirect" step="2">2.填写项目需求</p>
      </el-col>
      <el-col :span="5">
        <p id="p2" class="base" @click="redirect" step="3">3.填写公司信息</p>
      </el-col>
      <el-col :span="5">
        <p id="p3" class="base" @click="redirect" step="4">4.项目需求预览</p>
      </el-col>
      <el-col :span="5">
        <p id="p4" class="over" @click="redirect" step="5">5.推荐设计公司</p>
      </el-col>
    </el-row>

</template>

<script>
  export default {
    name: 'item_progress',
    props: {
      itemId: {
        default: 0
      },
      step: {
        default: 0
      },
      typeStep: Boolean,
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
    methods: {
      redirect(event) {
        if (event.currentTarget.classList.contains('active') || !event.currentTarget.classList.contains('pass')) {
          return false
        }
        var step = parseInt(event.currentTarget.getAttribute('step'))
        if (step === 1) {
          this.$router.push({name: 'itemSubmitTwo', params: {id: this.itemId}})
        } else if (step === 2) {
          this.$router.push({name: 'itemSubmitThree', params: {id: this.itemId}})
        } else if (step === 3) {
          this.$router.push({name: 'itemSubmitFour', params: {id: this.itemId}})
        } else if (step === 4) {
          this.$router.push({name: 'itemSubmitFive', params: {id: this.itemId}})
        }
      }
    },
    mounted: function() {
    },
    created: function() {
    },
    watch: {
      step(d) {
        if (d === 0) {
          document.getElementById('p0').className += ' pass'
        } else if (d === 1) {
          document.getElementById('p0').className += ' pass'
          document.getElementById('p1').className += ' pass'
        } else if (d === 2) {
          document.getElementById('p0').className += ' pass'
          document.getElementById('p1').className += ' pass'
          document.getElementById('p2').className += ' pass'
        } else if (d === 3) {
          document.getElementById('p0').className += ' pass'
          document.getElementById('p1').className += ' pass'
          document.getElementById('p2').className += ' pass'
          document.getElementById('p3').className += ' pass'
        } else if (d === 4) {
          document.getElementById('p0').className += ' pass'
          document.getElementById('p1').className += ' pass'
          document.getElementById('p2').className += ' pass'
          document.getElementById('p3').className += ' pass'
          document.getElementById('p4').className += ' pass'
        }
        if (this.typeStep) {
          document.getElementById('p0').className += ' active'
        }
        if (this.baseStep) {
          document.getElementById('p1').className += ' active'
        }
        if (this.companyStep) {
          document.getElementById('p2').className += ' active'
        }
        if (this.checkStep) {
          document.getElementById('p3').className += ' active'
        }
        if (this.stickStep) {
          document.getElementById('p4').className += ' active'
        }
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
    min-width: 860px;
  }
  .progress-box p {

  }
  .el-col {
    line-height: 60px;
    height:60px;
    text-align: center;
    color: #666;
  }
  .el-col p {
    height:60px;
    line-height: 60px;
  }
  p.first {
    background: url('../../../assets/images/item/step_type_pass.png') no-repeat;
    background-size: contain;
  }
  p.first.pass {
    background: url('../../../assets/images/item/step_type_pass.png') no-repeat;
    background-size: contain;
    color: #666;
    cursor: pointer;
  }
  p.first.active {
    background: url('../../../assets/images/item/step_type_active.png') no-repeat;
    background-size: contain;
    color: #fff;
    cursor: auto;
  }
  p.first p{
    color: #00AC84;
  }
  p.base {
    background: url('../../../assets/images/item/step_base.png') no-repeat;
    background-size: contain;
  }
  p.base.pass {
    background: url('../../../assets/images/item/step_base_pass.png') no-repeat;
    background-size: contain;
    cursor: pointer;
  }
  p.base.active {
    background: url('../../../assets/images/item/step_base_active.png') no-repeat;
    background-size: contain;
    color: #fff;
    cursor: auto;
  }
  p.base.active p{
    color: #00AC84;
  }
  .active p {
  }
  p.over {
    background: url('../../../assets/images/item/step_over.png') no-repeat;
    background-size: contain;
  }
  p.over.active {
    background: url('../../../assets/images/item/step_over_active.png') no-repeat;
    background-size: contain;
    color: #fff;
  }
  p.over {

  }
  p.over.active p{

  }

  @media screen and (max-width: 1000px) {
    .el-col p {
      line-height: 40px;
    }
  }
</style>
