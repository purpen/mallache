<template>
  <div class="container">
    <el-row :gutter="24">
      <v-menu></v-menu>

      <el-col :span="20">
        <div class="right-content">
          <v-menu-sub></v-menu-sub>
          <div class="content-box">

            <div class="form-title">
              <span>公司认证</span>
            </div>

            <div class="rz-box" v-if="isReady">
              <div class="rz-title">
                <span>认证</span>
              </div>
              <div class="rz-stat">
                <router-link :to="{name: 'vcenterComputerProfile'}" class="item">
                  +申请公司认证
                </router-link>
              </div>
            </div>

            <div class="rz-box" v-if="isApply">
              <div class="rz-title">
                <span>{{ statusLabel }}</span>
              </div>
              <div class="rz-stat">
                <router-link :to="{name: 'vcenterComputerProfile'}" class="item">
                  重新提交认证
                </router-link>
              </div>
            </div>

            <div class="clear"></div>
          </div>
        </div>

      </el-col>
    </el-row>
  </div>
</template>

<script>
  import vMenu from '@/components/pages/v_center/Menu'
  import vMenuSub from '@/components/pages/v_center/computer/MenuSub'
  import api from '@/api/api'

  export default {
    name: 'vcenter_computer_accredition',
    components: {
      vMenu,
      vMenuSub
    },
    data () {
      return {
        isReady: false,
        isApply: false,
        userId: this.$store.state.event.user.id,
        companyId: '',
        statusLabel: ''
      }
    },
    methods: {
    },
    created: function() {
      const that = this
      that.$http.get(api.designCompany, {})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          if (response.data.data) {
            that.companyId = response.data.data.id
            if (response.data.data.verify_status === 1) {
              that.statusLabel = '公司认证已审核通过'
            } else if (response.data.data.verify_status === 0) {
              that.statusLabel = '公司认证审核中'
            }
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

  .rz-title {
    float: left;
  }
  .rz-stat {
    float: right;
  }

</style>
