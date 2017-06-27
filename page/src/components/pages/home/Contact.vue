<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <v-menu></v-menu>

      <el-col :span="20">
        <div class="right-content">
          <div class="content-box" v-if="isCompany()">

            <div class="form-title">
              <span>提示信息</span>
            </div>
            <p class="alert-title"><span>*</span> 在铟果平台接单前，请先完善以下信息并完成公司认证，便于系统精准推送项目需求。</p>

            <div class="item">
              <h3>完善公司信息</h3>
              <p class="item-title">填写公司基本信息、公司简介、荣誉奖励</p>
              <p class="item-btn"><router-link :to="{name: 'vcenterComputerBase'}">编辑</router-link></p>
            </div>

            <div class="item">
              <h3>公司认证</h3>
              <p class="item-title">提交公司认证信息</p>
              <p class="item-btn"><router-link :to="{name: 'vcenterComputerAccreditation'}">未认证</router-link></p>
            </div>

            <div class="item">
              <h3>公司接单设置</h3>
              <p class="item-title">设计项目接单价格</p>
              <p class="item-btn"><router-link :to="{name: 'vcenterComputerTaking'}">设置接单价格</router-link></p>
            </div>

            <div class="item no-line">
              <h3>上传案例作品</h3>
              <p class="item-title">向客户更好的展示和推荐项目案例</p>
              <p class="item-btn"><router-link :to="{name: 'vcenterDesignCaseList'}">上传</router-link></p>
            </div>

          </div>

          <div class="content-box" v-else>

            <div class="form-title">
              <span>提示信息</span>
            </div>
            <p class="alert-title"><span>*</span> 在铟果平台发布需求前，请先完善以下信息并完成公司认证，便于系统精准匹配设计服务供应商。</p>

            <div class="item">
              <h3>完善公司信息</h3>
              <p class="item-title">填写公司基本信息、公司简介、荣誉奖励</p>
              <p class="item-btn"><router-link :to="{name: 'vcenterComputerBase'}">编辑</router-link></p>
            </div>

            <div class="item no-line">
              <h3>公司认证</h3>
              <p class="item-title">提交公司认证信息</p>
              <p class="item-btn"><router-link :to="{name: 'vcenterComputerAccreditation'}">未认证</router-link></p>
            </div>

          </div>

        </div>

        <div class="right-content message">
          <div class="content-box">
            <div class="form-title">
              <span>待处理事项</span>
            </div>
            <p class="alert-title">0 条消息</p>
            <div class="message-btn">
              <router-link :to="{name: 'home'}"><el-button class="is-custom">返回首页</el-button></router-link> &nbsp;&nbsp;
              <router-link :to="{name: 'home'}"><el-button type="primary" class="is-custom">查看消息</el-button></router-link>
            </div>
          </div>
        </div>

      </el-col>
    </el-row>
  </div>
</template>

<script>
  import vMenu from '@/components/pages/v_center/Menu'
  import api from '@/api/api'

  export default {
    name: 'vcenter_control',
    components: {
      vMenu
    },
    data () {
      return {
        isReady: false,
        isApply: false,
        userId: this.$store.state.event.user.id,
        item: '',
        companyId: '',
        statusLabel: ''
      }
    },
    methods: {
      isCompany() {
        var uType = this.$store.state.event.user.type
        if (uType === 2) {
          return true
        } else {
          return false
        }
      }
    },
    created: function() {
      const that = this
      that.$http.get(api.demandCompany, {})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          console.log(response.data.data)
          if (response.data.data) {
            that.item = response.data.data
            that.item.phone = that.item.phone === '0' ? '' : that.item.phone
            that.companyId = response.data.data.id
            that.isApply = true
          } else {
            that.isReady = true
          }
        } else {
          that.isReady = true
        }
      })
      .catch (function(error) {
        that.$message({
          showClose: true,
          message: error.message,
          type: 'error'
        })
        console.log(error.message)
        return false
      })
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .right-content .content-box {
    margin-top: 0;
  }

  p.alert-title {
    font-size: 1.6rem;
    color: #666;
    margin-bottom: 20px;
  }
  .alert-title span {
    color: red;
  }

  .item {
    height: 60px;
    border-bottom: 1px solid #ccc;
    margin-bottom: 20px;
  }
  
  .item h3 {
    color: #222;
    font-size: 1.6rem;
    line-height: 2;
  }
  .item .item-title {
    float: left;
    color: #666;
    font-size: 1.4rem;
    line-height: 1.7;
  }
  .item .item-btn {
    float: right;
    margin-right: 10px;
    font-size: 1.4rem;
    line-height: 1.7;
  }
  .item .item-btn a {
    color: #FE3824;
  }

  .no-line {
    border: 0;
    margin-bottom: 0;
  }

  .right-content.message {
    margin: 20px 0;
  }

  .message-btn {
    text-align: center;
    margin-bottom: 20px;
  }


</style>
