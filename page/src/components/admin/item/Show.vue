<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <v-menu selectedName="itemList"></v-menu>

      <el-col :span="20">
        <div class="content">

        <div class="admin-menu-sub">
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminItemList'}" active-class="false" :class="{'item': true, 'is-active': menuType === 0}">全部</router-link>
          </div>
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminItemList', query: {type: 1}}" active-class="false" :class="{'item': true, 'is-active': menuType === 1}">完善资料</router-link>
          </div>
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminItemList', query: {type: 2}}" active-class="false" :class="{'item': true, 'is-active': menuType === 2}">等待推荐</router-link>
          </div>
        </div>


        <div class="content-box" v-loading.body="isLoading">

          <div class="form-title">
            <span>基本信息</span>
          </div>

          <div class="company-show">

            <div class="item">
              <p class="p-key">名称</p>
              <p class="p-val">{{ info.name }}</p>
            </div>

            <div class="item">
              <p class="p-key">类型</p>
              <p class="p-val">{{ item.type_value }}</p>
            </div>

            <div class="item">
              <p class="p-key">类别</p>
              <p class="p-val">{{ item.design_type_value }}</p>
            </div>

            <div class="item">
              <p class="p-key">领域</p>
              <p class="p-val">{{ info.field_value }}</p>
            </div>

            <div class="item">
              <p class="p-key">行业</p>
              <p class="p-val">{{ info.industry_value }}</p>
            </div>

            <div class="item">
              <p class="p-key">预算</p>
              <p class="p-val">{{ info.design_cost_value }}</p>
            </div>

            <div class="item">
              <p class="p-key">周期</p>
              <p class="p-val">{{ info.cycle_value }}</p>
            </div>

            <div class="item">
              <p class="p-key">工作地点</p>
              <p class="p-val">{{ info.province_value }} {{ info.city_value }}</p>
            </div>

            <div class="item">
              <p class="p-key">功能或卖点</p>
              <p class="p-val">{{ info.product_features }}</p>
            </div>

            <div class="item">
              <p class="p-key">相关附件</p>
              <p class="p-val" v-for="(d, index) in info.image"><a :href="d.file" target="_blank">{{ d.name }}</a></p>
            </div>

          </div>

          <div class="form-title">
            <span>公司信息</span>
          </div>

          <div class="company-show">
            <div class="item">
              <p class="p-key">名称</p>
              <p class="p-val">{{ item.company_name }}</p>
            </div>

            <div class="item">
              <p class="p-key">规模</p>
              <p class="p-val">{{ item.company_size_value }}</p>
            </div>

            <div class="item">
              <p class="p-key">网址</p>
              <p class="p-val">{{ item.company_web }}</p>
            </div>

            <div class="item">
              <p class="p-key">所在地区</p>
              <p class="p-val">{{ item.company_province_value + ', ' + item.company_city_value + ', ' + item.company_area_value }}</p>
            </div>

            <div class="item">
              <p class="p-key">详细地址</p>
              <p class="p-val">{{ item.address }}</p>
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
            <span>相关信息</span>
          </div>

          <div class="company-show">
            <div class="item">
              <p class="p-key">项目报价</p>
              <p class="p-val">{{ item.price }}</p>
            </div>
            <div class="item">
              <p class="p-key">项目剩余金额</p>
              <p class="p-val">{{ item.rest_fund }}</p>
            </div>
          </div>

          <div class="form-title">
            <span>状态</span>
          </div>

          <div class="company-show">
            <div class="item">
              <p class="p-key">状态</p>
              <p class="p-val">
                {{ item.status_value }}
              </p>

              <p class="opt" v-if="isSystem">
                <el-button class="is-custom" size="small" @click="forceCloseBtn">关闭项目并退款</el-button>
              </p>
            </div>
          </div>

        </div>

        </div>
      </el-col>
    </el-row>

    <el-dialog
      title="提示"
      v-model="comfirmDialog"
      size="tiny">
      <span>{{ comfirmMessage }}</span>
      <span slot="footer" class="dialog-footer">
        <input type="hidden" ref="comfirmType" value="1" />
        <el-button @click="comfirmDialog = false">取 消</el-button>
        <el-button type="primary" @click="sureDialogSubmit">确 定</el-button>
      </span>
    </el-dialog>


    <el-dialog title="匹配公司" v-model="matchCompanyDialog">
      <el-form label-position="top">
        <input type="hidden" v-model="matchCompanyForm.itemId" value="" />
        <input type="hidden" v-model="matchCompanyForm.itemStatus" value="" />
        <el-form-item label="项目名称" label-width="200px">
          <el-input v-model="matchCompanyForm.itemName" auto-complete="off" disabled></el-input>
        </el-form-item>
        <div class="match-company-box">
        <p>已匹配的公司：</p>
        <p><el-tag class="match-company-tag" type="success" v-for="(d, index) in currentMatchCompany" :key="index">{{ d.company_name }}</el-tag></p>
        </div>
        <el-form-item label="添加公司" label-width="80px">
          <el-input v-model="matchCompanyForm.companyIds" placeholder="多个公司ID用','分隔" auto-complete="off"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="matchCompanyDialog = false">取 消</el-button>
        <el-button type="primary" @click="addMatchCompany">确 定</el-button>
      </div>
    </el-dialog>

    <el-dialog title="关闭项目并返款" v-model="forceCloseDialog">
      <el-form label-position="left">
        <el-form-item label="需求公司返款金额">
          <el-input v-model="matchCompanyForm.demandAmount" placeholder="" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="设计公司返款金额">
          <el-input v-model="matchCompanyForm.designAmount" placeholder="" auto-complete="off"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="forceCloseDialog = false">取 消</el-button>
        <el-button type="primary" :loading="isForceCloseLoadingBtn" @click="forceCloseSubmit">确 定</el-button>
      </div>
    </el-dialog>

  </div>
</template>

<script>
import api from '@/api/api'
import vMenu from '@/components/admin/Menu'
export default {
  name: 'admin_item_list',
  components: {
    vMenu
  },
  data () {
    return {
      menuType: 0,
      matchCompanyDialog: false,
      forceCloseDialog: false,
      isForceCloseLoadingBtn: false,
      item: '',
      info: '',
      itemId: '',
      currentMatchCompany: [],
      isLoading: false,
      matchCompanyForm: {
        itemId: '',
        itemName: '',
        companyIds: ''
      },
      forceCloseForm: {
        itemId: '',
        demandAmount: 0,
        designAmount: 0
      },
      comfirmDialog: false,
      comfirmMessage: '确认执行此操作?',
      msg: ''
    }
  },
  methods: {
    handleMatch(index, item) {
      if (item.item.status !== 2) {
        // this.$message.error('项目状态不允许推荐公司')
        // return
      }
      this.currentMatchCompany = item.designCompany
      this.matchCompanyForm.itemId = item.item.id
      this.matchCompanyForm.itemStatus = item.item.status
      this.matchCompanyForm.itemName = item.info.name
      this.matchCompanyDialog = true
    },
    addMatchCompany() {
      if (!this.matchCompanyForm.itemId || !this.matchCompanyForm.itemName || !this.matchCompanyForm.companyIds || !this.matchCompanyForm.itemStatus) {
        this.$message.error('缺少请求参数!')
        return
      }
      var companyIds = this.matchCompanyForm.companyIds.split(',')
      var self = this
      this.$http.post(api.addItemToCompany, {item_id: this.matchCompanyForm.itemId, recommend: companyIds})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          if (self.matchCompanyForm.itemStatus === 2) {
            self.$http.post(api.ConfirmItemToCompany, {item_id: self.matchCompanyForm.itemId})
            .then (function(response1) {
              if (response1.data.meta.status_code === 200) {
                self.$message.success('添加成功!')
                self.matchCompanyDialog = false
                return
              } else {
                self.$message.error(response1.data.meta.message)
              }
            })
            .catch (function(error) {
              self.$message.error(error.message)
            })
          } else {
            self.$message.success('添加成功!')
            self.matchCompanyDialog = false
          }
        } else {
          self.$message.error(response.data.meta.message)
          return
        }
      })
      .catch (function(error) {
        self.$message.error(error.message)
      })
    },
    // 强制关闭项目按钮
    forceCloseBtn() {
      this.$refs.comfirmType.value = 1
      this.comfirmMessage = '确认与双方达成一致，关闭项目并退款？'
      this.comfirmDialog = true
    },
    sureDialogSubmit() {
      let comfirmType = parseInt(this.$refs.comfirmType.value)
      if (comfirmType === 1) {
        this.forceCloseDialog = true
      }
      this.comfirmDialog = false
    },
    // 强制关闭项目提交
    forceCloseSubmit() {
      const self = this
      self.isForceCloseLoadingBtn = true
      var demandAmount = parseFloat(self.matchCompanyForm.demandAmount).toFixed(2)
      var designAmount = parseFloat(self.matchCompanyForm.designAmount).toFixed(2)
      self.$http.post(api.forceCloseSubmit, { item_id: self.itemId, demand_amount: demandAmount, design_amount: designAmount })
      .then (function(response) {
        self.isForceCloseLoadingBtn = false
        if (response.data.meta.status_code === 200) {
          self.forceCloseDialog = false
          var rs = response.data.data
          console.log(rs)
          self.$message.error('操作成功！')
        } else {
          self.$message.error(response.data.meta.message)
        }
      })
      .catch (function(error) {
        self.$message.error(error.message)
        self.isForceCloseLoadingBtn = false
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
    self.$http.get(api.adminItemShow, {params: {id: id}})
    .then (function(response) {
      self.isLoading = false
      if (response.data.meta.status_code === 200) {
        self.item = response.data.data.item
        self.info = response.data.data.info
        console.log(self.item)
        console.log(self.info)
      } else {
        self.$message.error(response.data.meta.message)
      }
    })
    .catch (function(error) {
      self.$message.error(error.message)
      self.isLoading = false
    })
  },
  computed: {
    isSystem() {
      var user = this.$store.state.event.user
      if (user.role_id === 15 || user.role_id === 20) {
        return true
      }
      return false
    }
  },
  watch: {
    '$route' (to, from) {
      // 对路由变化作出响应...
      this.loadList()
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
