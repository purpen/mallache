<template>
  <el-col :span="isMob ? 24 : 6">
    <div class="progress">

      <div class="process-item">
        <p>项目对接</p>
        <el-steps :space="50" direction="vertical" finish-status="process" :active="progressButt">
          <el-step title="项目创建" description=""></el-step>
          <el-step title="推荐设计公司"></el-step>
          <el-step title="等待设计公司接单(报价)"></el-step>
        </el-steps>
      </div>

      <div class="process-item">
        <p>合同管理</p>
        <el-steps :space="50" direction="vertical" finish-status="process" :active="progressContract">
          <el-step title="等待设计公司提交合同" description=""></el-step>
          <el-step title="确认合同"></el-step>
          <el-step title="合同已确认"></el-step>
          <el-step title="托管项目资金"></el-step>
        </el-steps>
      </div>

      <div class="process-item">
        <p>项目管理</p>
        <el-steps :space="50" direction="vertical" finish-status="process" :active="progressItem">
          <el-step title="项目进行中" description=""></el-step>
          <el-step title="项目验收"></el-step>
          <el-step title="项目交易成功"></el-step>
          <el-step title="发表评价"></el-step>
        </el-steps>
      </div>

    </div>

  </el-col>
</template>

<script>
  export default {
    name: 'item_progress',
    props: {
      progressButt: {
        default: 0
      },
      progressContract: {
        default: -1
      },
      progressItem: {
        default: -1
      }
    },
    data () {
      return {
        msg: ''
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
<style>
  .process-item {
    margin: 0 0 30px 0;
  }

  .process-item p {
    line-height: 2;
    font-size: 1.7rem;
    font-weight: bold;
  }

  .progress {
    padding: 20px;
    border: 1px solid #ccc;
  }

</style>
