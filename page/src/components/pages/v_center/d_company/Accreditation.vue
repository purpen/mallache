<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <v-menu currentName="profile" :class="[isMob ? 'v-menu' : '']"></v-menu>

      <el-col :span="isMob ? 24 : 20">
        <div class="right-content">
          <v-menu-sub></v-menu-sub>
          <div class="content-box">

            <div class="form-title">
              <span>企业实名认证</span>
            </div>

            <div class="company-show" v-if="isApply">
              <div class="item">
                <p class="p-key">企业名称</p>
                <p class="p-val">{{ item.company_name }}</p>
              </div>

              <div class="item">
                <p class="p-key">企业证件类型</p>
                <p class="p-val">{{ item.company_type_value }}</p>
              </div>

              <div class="item">
                <p class="p-key">统一社会信用代码</p>
                <p class="p-val">{{ item.registration_number }}</p>
              </div>

              <div class="item">
                <p class="p-key">法人姓名</p>
                <p class="p-val">{{ item.legal_person }}</p>
              </div>

              <div class="item">
                <p class="p-key">法人证件类型</p>
                <p class="p-val">{{ item.document_type_value }}</p>
              </div>

              <div class="item">
                <p class="p-key">证件号码</p>
                <p class="p-val">{{ item.document_number }}</p>
              </div>

              <div class="item">
                <p class="p-key">联系人</p>
                <p class="p-val">{{ item.contact_name }}</p>
              </div>

              <div class="item">
                <p class="p-key">职位</p>
                <p class="p-val">{{ item.position }}</p>
              </div>

              <div class="item">
                <p class="p-key">手机</p>
                <p class="p-val">{{ item.phone }}</p>
              </div>

              <div class="item">
                <p class="p-key">邮箱</p>
                <p class="p-val">{{ item.email }}</p>
              </div>


            </div>

            <div class="rz-box" v-if="isReady">
              <div class="rz-title">
                <span>认证</span>
              </div>
              <div class="rz-stat">
                <router-link :to="{name: 'vcenterDCompanyIdentification'}" class="item">
                  +申请企业认证
                </router-link>
              </div>
            </div>

            <div class="rz-box" v-if="isApply">
              <div class="rz-title success" v-if="item.verify_status === 1">
                <p>认证通过</p>
              </div>
              <div class="rz-title wait" v-else-if="item.verify_status === 0">
                <p>等待认证</p>
              </div>
              <div class="rz-title rejust" v-else-if="item.verify_status === 2">
                <p>认证未通过</p>
              </div>
              <div class="rz-stat" v-if="item.verify_status !== 1">
                <router-link :to="{name: 'vcenterDCompanyIdentification'}" class="item">
                  <el-button class="is-custom" type="primary">提交认证</el-button>
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
  import vMenuSub from '@/components/pages/v_center/d_company/MenuSub'
  import api from '@/api/api'

  export default {
    name: 'vcenter_d_company_accredition',
    components: {
      vMenu,
      vMenuSub
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
    methods: {},
    created: function () {
      let uType = this.$store.state.event.user.type
      // 如果是设计公司，跳到设计公司
      if (uType === 2) {
        this.$router.replace({name: 'vcenterComputerAccreditation'})
        return
      }
      const that = this
      that.$http.get(api.demandCompany, {})
        .then(function (response) {
          if (response.data.meta.status_code === 200) {
//            console.log(response.data.data)
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
        .catch(function (error) {
          that.$message({
            showClose: true,
            message: error.message,
            type: 'error'
          })
          console.log(error.message)
          return false
        })
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      }
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .rz-box {
    margin-top: 50px;
  }

  .rz-title {
    float: left;
  }

  .rz-title p {
    font-size: 1.8rem;
  }

  .success p {
    color: #008000;
  }

  .wait p {
    color: #FF4500;
  }

  .rejust p {
    color: #FF4500;
  }

  .rz-stat {
    float: right;
  }

  .company-show {

  }

  .company-show .item {
    clear: both;
    height: 40px;
    border-bottom: 1px solid #ccc;
  }

  .item p {
    line-height: 3;
  }

  .item p.p-key {
    float: left;
    width: 150px;
    color: #666;
  }

  .item p.p-val {
    width: 300px;
    float: left;
    font-size: 1.5rem;
  }

</style>
