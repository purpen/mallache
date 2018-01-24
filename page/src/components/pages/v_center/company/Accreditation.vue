<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="24">
      <v-menu currentName="profile" :class="[isMob ? 'v-menu' : '']"></v-menu>

      <el-col :span="isMob ? 24 : 20" v-loading.body="isLoading">
        <div class="right-content">
          <v-menu-sub></v-menu-sub>
          <section class="verify" v-if="item.verify_status === 0">
            <img :src="require('assets/images/item/authentication@2x.png')" alt="未认证">
            <h3>您还没有实名认证</h3>
            <router-link :to="{name: 'vcenterComputerIdentification'}" class="item">
              <el-button class="is-custom" type="primary">马上去认证</el-button>
            </router-link>
          </section>

          <section class="verify" v-if="item.verify_status === 3">
            <img :src="require('assets/images/item/to-examine@2x.png')" alt="认证中">
            <h3>您的实名认证正在审核中</h3>
            <p>请耐心等待...</p>
          </section>
          <section v-if="item.verify_status === 1">
            <div class="verify verify-success">
              <img :src="require('assets/images/item/authentication-success@2x.png')" alt="认证通过">
              <h3>恭喜！认证成功！</h3>
            </div>
            <div :class="['content-box', isMob ? 'content-box-m' : '']">
              <section class="certified">
                <div class="form-title">
                  <span>企业实名认证</span>
                </div>
                <div class="company-show">
                  <div class="item">
                    <p class="p-key">企业名称</p>
                    <p class="p-val">{{ item.company_name }}</p>
                  </div>

                  <div class="item">
                    <p class="p-key">企业证件类型</p>
                    <p class="p-val">{{ item.company_type_val }}</p>
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
                    <p class="p-val">{{ item.document_type_val }}</p>
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
              </section>
            </div>
          </section>
          <section class="verify" v-if="item.verify_status === 2">
            <img :src="require('assets/images/item/authentication-error@2x.png')" alt="认证失败">
            <h3>对不起，您的实名认证失败了...</h3>
            <router-link :to="{name: 'vcenterComputerIdentification'}" class="item">
              <el-button class="is-custom" type="primary">重新认证</el-button>
            </router-link>
          </section>
        </div>

      </el-col>
    </el-row>
  </div>
</template>

<script>
  import vMenu from '@/components/pages/v_center/Menu'
  import vMenuSub from '@/components/pages/v_center/company/MenuSub'
  import api from '@/api/api'

  export default {
    name: 'vcenter_company_accredition',
    components: {
      vMenu,
      vMenuSub
    },
    data () {
      return {
        isLoading: false,
        userId: this.$store.state.event.user.id,
        companyId: '',
        statusLabel: '',
        item: {}
      }
    },
    methods: {},
    created: function () {
      this.isLoading = true
      var uType = this.$store.state.event.user.type
      // 如果是需求公司，跳到设计公司
      if (uType !== 2) {
        this.$router.replace({name: 'vcenterDCompanyAccreditation'})
        return
      }
      const that = this
      that.$http.get(api.designCompany, {})
        .then(function (response) {
          that.isLoading = false
          if (response.data.meta.status_code === 200) {
            if (response.data.data) {
              that.item = response.data.data
              that.item.phone = that.item.phone === '0' ? '' : that.item.phone
              that.companyId = response.data.data.id
            }
          } else {
            that.message.error(response.data.meta.message)
          }
        })
        .catch(function (error) {
          that.isLoading = false
          that.$message({
            showClose: true,
            message: error.message,
            type: 'error'
          })
          console.error(error.message)
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
  .right-content .content-box {
    padding-bottom: 0;
  }

  .right-content .content-box-m {
    margin: 0;
    padding: 0 15px;
  }

  .success p {
    color: #008000;
    background: url(../../../../assets/images/item/badge@2x.png) no-repeat left;
    background-size: contain;
    text-indent: 30px;
  }

  .company-show .item {
    clear: both;
    height: 40px;
    border-bottom: 1px solid #d2d2d2;
  }

  .company-show .item:last-child {
    border: none
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

  .verify {
    height: 300px;
    padding-top: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    font-size: 15px;
    color: #999;
  }

  .verify-success {
    margin-top: 20px;
    height: auto;
    border: 1px solid #d2d2d2;
  }

  .verify img {
    width: 120px;
  }

  .verify h3 {
    color: #666;
    padding: 20px 0 10px;
  }

  .verify a {
    margin-top: 10px;
    width: 120px;
    height: 40px;
  }

  .is-custom {
    width: 100%;
  }
</style>
