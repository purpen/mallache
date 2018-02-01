<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <v-menu selectedName="demandCompanyList"></v-menu>

      <el-col :span="20">
        <div class="content">

        <!-- <div class="admin-menu-sub">
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminDemandCompanyList'}" active-class="false" :class="{'item': true, 'is-active': menuType == 0}">全部</router-link>
          </div>
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminDemandCompanyList', query: {type: 1}}" :class="{'item': true, 'is-active': menuType === 1}" active-class="false">通过认证</router-link>
          </div>
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminDemandCompanyList', query: {type: 2}}" :class="{'item': true, 'is-active': menuType === 2}" active-class="false">拒绝认证</router-link>
          </div>
        </div> -->

        <div class="content-box" v-loading.body="isLoading">

          <div class="form-title">
            <span>基本信息</span>
          </div>

          <div class="company-show">

            <div class="item" style="height: 90px;">
              <p class="p-key">头像</p>
              <p class="p-val"><img v-if="item.logo_url" :src="item.logo_url" width="80" /></p>
            </div>

            <div class="item">
              <p class="p-key">简称</p>
              <p class="p-val">{{ item.company_abbreviation }}</p>
            </div>

            <div class="item">
              <p class="p-key">地址</p>
              <p class="p-val"><span v-for="(d, index) in item.city_arr" :key="index">{{ d }} &nbsp;</span> {{ item.address }}</p>
            </div>

            <div class="item">
              <p class="p-key">网址</p>
              <p class="p-val">{{ item.company_web }}</p>
            </div>

            <div class="item">
              <p class="p-key">规模</p>
              <p class="p-val">{{ item.company_size_value }}</p>
            </div>

            <div class="item">
              <p class="p-key">类型</p>
              <p class="p-val">{{ item.company_property_value }}</p>
            </div>

          </div>

          <div class="form-title">
            <span>认证信息</span>
          </div>

          <div class="company-show">
            <div class="item">
              <p class="p-key">企业名称</p>
              <p class="p-val">{{ item.company_name }}</p>
            </div>

            <div class="item">
              <p class="p-key">企业证件类型</p>
              <p class="p-val">{{ item.company_type_value }}</p>
            </div>

            <div class="item">
              <p class="p-key">企业营业执照</p>
              <p class="p-val"><a v-for="(d, index) in item.license_image" :key="index" :href="d.file" target="_blank">{{ d.name }} </a></p>
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
              <p class="p-key">法人证件附件</p>
              <p class="p-val"><a v-for="(d, index) in item.document_image" :key="index" :href="d.file" target="_blank">{{ d.name }} </a></p>
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

          <div class="form-title">
            <span>状态</span>
          </div>

          <div class="company-show">
            <div class="item">
              <p class="p-key">认证</p>
              <p class="p-val">
                  <span v-if="item.verify_status === 1" type="success">通过</span>
                  <span v-else-if="item.verify_status === 2" type="gray">拒绝</span>
                  <span v-else type="warning">待认证</span>
              </p>
              <p class="opt" v-if="item.verify_status === 0">
                <el-button class="is-custom" :loading="verifyLoadingBtn" size="small" @click="setRefuseRease(2)">拒绝</el-button>
                <el-button type="primary" class="is-custom" :loading="verifyLoadingBtn" size="small" @click="setVerify(1)">通过</el-button>
              </p>
              <p class="opt" v-else>
                <el-button class="is-custom" :loading="verifyLoadingBtn" size="small" v-if="item.verify_status === 1" @click="setRefuseRease(2)">拒绝</el-button>
                <el-button type="primary" class="is-custom" :loading="verifyLoadingBtn" size="small" v-else @click="setVerify(1)">通过</el-button>
              </p>
            </div>
          </div>

        </div>

        </div>
      </el-col>
    </el-row>
    <el-dialog title="请填写拒绝原因" :visible.sync="dialogVisible" size="tiny">
      <el-input v-model="refuseRease"></el-input>
      <span slot="footer" class="dialog-footer">
        <el-button size="small" @click="dialogVisible = false">取 消</el-button>
        <el-button size="small" type="primary" @click="setVerify(evt)">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import api from '@/api/api'
import vMenu from '@/components/admin/Menu'
export default {
  name: 'admin_company_list',
  components: {
    vMenu
  },
  data () {
    return {
      menuType: 0,
      item: '',
      tableData: [],
      isLoading: false,
      verifyLoadingBtn: false,
      msg: '',
      dialogVisible: false,
      itemId: '',
      refuseRease: '',
      evt: ''
    }
  },
  methods: {
    setRefuseRease (evt) {
      this.dialogVisible = !this.dialogVisible
      this.evt = evt
    },
    setVerify(evt) {
      this.dialogVisible = false
      var self = this
      self.verifyLoadingBtn = true
      self.$http.put(api.adminDemandCompanyVerifyOk, {id: self.itemId, status: evt, verify_summary: self.refuseRease})
      .then (function(response) {
        self.refuseRease = ''
        console.log(response)
        self.verifyLoadingBtn = false
        if (response.data.meta.status_code === 200) {
          self.item.verify_status = evt
          self.$message.success('操作成功')
        } else {
          self.$message.error(response.meta.message)
        }
      })
      .catch (function(error) {
        self.refuseRease = ''
        self.verifyLoadingBtn = false
        self.$message.error(error.message)
      })
    }
  },
  created: function() {
    var id = this.$route.params.id
    if (!id) {
      this.$message.error('缺少请求参数!')
      this.$router.replace({name: 'home'})
      return false
    }
    const self = this
    self.itemId = id
    self.isLoading = true
    self.$http.get(api.adminDemandCompanyShow, {params: {id: id}})
    .then (function(response) {
      self.isLoading = false
      if (response.data.meta.status_code === 200) {
        self.item = response.data.data
        if (self.item.logo_image) {
          self.item.logo_url = self.item.logo_image.logo
        } else {
          self.item.logo_url = false
        }
        console.log(self.item)
      } else {
        self.$message.error(response.data.meta.message)
      }
    })
    .catch (function(error) {
      self.$message.error(error.message)
      self.isLoading = false
    })
  },
  watch: {
    '$route' (to, from) {
      // 对路由变化作出响应...
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .content-box {
    margin-top: 20px;
    clear: both;
    border: 1px solid #ccc;
    padding: 0px 20px 20px 20px;
    min-height: 350px;
  }

  .company-show {
    clear: both;
    margin-bottom: 40px;
    margin-top: -5px;
  }
  .company-show .item {
    clear: both;
    min-height: 40px;
    border-bottom: 1px solid #ccc;
  }

  .company-show .item p {
    line-height: 40px;
  }

  .company-show .item p.p-key {
    float: left;
    width: 150px;
    color: #666;
  }

  .company-show .item p.p-val {
    width: 300px;
    float: left;
    font-size: 1.5rem;
  }

  .company-show .item p.opt {
    text-align: right;
    width: 150px;
    float: right;
    font-size: 1.2rem;
  }

</style>
