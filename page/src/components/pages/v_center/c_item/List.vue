<template>
  <div class="container">
    <el-row :gutter="24">
      <v-menu></v-menu>

      <el-col :span="20">
        <div class="right-content">
          <v-menu-sub></v-menu-sub>
          <div class="content-item-box">

            <div class="loading" v-loading.body="isLoading"></div>
            <div class="item" v-for="(d, index) in designItems">
              <div class="banner">
                  <p>
                    <span>{{ d.item.created_at }}</span>
                    <span>{{ d.item.company_name }}</span>
                    <span>和我联系</span>
                  </p>
              </div>
              <div class="content">
                <div class="l-item">
                  <p class="c-title">{{ d.item.name }}</p>
                  <p>项目预算: {{ d.item.design_cost_value }}</p>
                  <p>设计类别: {{ d.item.type_label }}</p>
                  <p>项目周期: {{ d.item.cycle_value }}</p>
                </div>
                <div class="r-item">
                  <p>{{ d.status_value }}</p>
                </div>
              </div>
              <div class="opt">
                <div class="l-item">
                  <p>
                    <!--<span>项目金额:</span>&nbsp;&nbsp;<span class="money-str">¥ <b>5000.00</b></span>-->
                  </p>
                </div>
                <div class="r-item">
                  <p class="btn" v-show="d.design_company_status === 0">
                    <a href="javascript:void(0);" @click="companyRefuseBtn" :index="index" :item_id="d.item.id">拒绝此单</a>&nbsp;&nbsp;
                    <a href="javascript:void(0);" @click="takingBtn" :item_id="d.item.id" :index="index" class="b-blue">有意向接单</a>&nbsp;&nbsp;
                    <!--<a href="javascript:void(0);" :item_id="d.item.id" class="b-red">一键抢单</a>-->
                  </p>
                </div>
              </div>
            </div>


          </div>
        </div>

      </el-col>
    </el-row>

    <el-dialog title="提交项目报价" v-model="takingPriceDialog">
      <el-form label-position="top" :model="takingPriceForm" :rules="takingPriceRuleForm" ref="takingPriceRuleForm">
        <input type="hidden" v-model="takingPriceForm.itemId" value="" />
        <el-form-item label="项目报价" label-width="200px">
          <el-input v-model.number="takingPriceForm.price" auto-complete="off" ></el-input>
        </el-form-item>
        <el-form-item label="报价说明" label-width="80px">
          <el-input v-model="takingPriceForm.summary" placeholder="" auto-complete="off"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="takingPriceDialog = false">取 消</el-button>
        <el-button type="primary" :loading="isTakingLoadingBtn" class="is-custom" @click="takingPriceSubmit('takingPriceRuleForm')">确 定</el-button>
      </div>
    </el-dialog>

    <el-dialog
      title="提示"
      v-model="sureRefuseItemDialog"
      size="tiny">
      <span>确认执行此操作?</span>
      <span slot="footer" class="dialog-footer">
        <input type="hidden" ref="refuseItemId" />
        <el-button @click="sureRefuseItemDialog = false">取 消</el-button>
        <el-button type="primary" :loading="refuseItemLoadingBtn" @click="sureRefuseItemSubmit">确 定</el-button>
      </span>
    </el-dialog>

  </div>
</template>

<script>
  import vMenu from '@/components/pages/v_center/Menu'
  import vMenuSub from '@/components/pages/v_center/c_item/MenuSub'
  import api from '@/api/api'
  import '@/assets/js/format'

  export default {
    name: 'vcenter_item_list',
    components: {
      vMenu,
      vMenuSub
    },
    data () {
      return {
        designItems: [],
        isLoading: true,
        takingPriceDialog: false,
        isTakingLoadingBtn: false,
        sureRefuseItemDialog: false,
        refuseItemLoadingBtn: false,
        takingPriceForm: {
          itemId: '',
          price: '',
          summary: ''
        },
        takingPriceRuleForm: {
          price: [
            { required: true, message: '请添写报价金额', trigger: 'blur' }
          ],
          summary: [
            { required: true, message: '请添写报价说明', trigger: 'blur' }
          ]
        },
        userId: this.$store.state.event.user.id
      }
    },
    methods: {
      // 项目报价弹出层
      takingBtn(event) {
        var itemId = parseInt(event.currentTarget.getAttribute('item_id'))
        this.currentIndex = parseInt(event.currentTarget.getAttribute('index'))
        this.takingPriceForm.itemId = itemId
        this.takingPriceDialog = true
      },
      // 提交项目报价
      takingPriceSubmit(formName) {
        var self = this
        self.$refs[formName].validate((valid) => {
          // 验证通过，提交
          if (valid) {
            self.isTakingLoadingBtn = true
            var row = {
              item_demand_id: self.takingPriceForm.itemId,
              price: self.takingPriceForm.price,
              summary: self.takingPriceForm.summary
            }

            console.log(row)
            var apiUrl = api.addQuotation
            var method = 'post'

            self.$http({method: method, url: apiUrl, data: row})
            .then (function(response) {
              if (response.data.meta.status_code === 200) {
                self.$message.success('提交成功！')
                self.designItems[self.currentIndex].design_company_status = 2
                self.designItems[self.currentIndex].status_value = '已提交报价'
                self.isTakingLoadingBtn = false
                self.takingPriceDialog = false
              } else {
                self.$message.error(response.data.meta.message)
                self.isTakingLoadingBtn = false
              }
            })
            .catch (function(error) {
              self.$message.error(error.message)
              self.isTakingLoadingBtn = false
              return false
            })
          } else {
          }
        })
      },
      // 拒绝项目确认框
      companyRefuseBtn(event) {
        var itemId = parseInt(event.currentTarget.getAttribute('item_id'))
        this.currentIndex = parseInt(event.currentTarget.getAttribute('index'))
        this.$refs.refuseItemId.value = itemId
        this.sureRefuseItemDialog = true
      },
      // 确认拒绝项目
      sureRefuseItemSubmit() {
        var itemId = this.$refs.refuseItemId.value
        var self = this
        this.refuseItemLoadingBtn = true
        self.$http({method: 'get', url: api.companyRefuseItemId.format(itemId), data: {}})
        .then (function(response) {
          if (response.data.meta.status_code === 200) {
            self.$message.success('提交成功！')
            self.refuseItemLoadingBtn = false
            self.sureRefuseItemDialog = false
            self.sureRefuseItemDialog = false
            self.designItems.splice(self.currentIndex, 1)
          } else {
            self.$message.error(response.data.meta.message)
            self.refuseItemLoadingBtn = false
            self.sureRefuseItemDialog = false
          }
        })
        .catch (function(error) {
          self.$message.error(error.message)
          self.refuseItemLoadingBtn = false
          self.sureRefuseItemDialog = false
          return false
        })
      }
    },
    computed: {
    },
    created: function() {
      var self = this
      // 如果是用户，跳到设计用户列表
      var uType = this.$store.state.event.user.type
      if (uType !== 2) {
        this.isLoading = false
        this.$router.replace({name: 'vcenterItemList'})
        return
      }
      self.$http.get(api.designItemList, {})
      .then (function(response) {
        self.isLoading = false
        if (response.data.meta.status_code === 200) {
          if (!response.data.data) {
            return false
          }
          var designItems = response.data.data
          for (var i = 0; i < designItems.length; i++) {
            var item = designItems[i]
            var typeLabel = ''
            if (item.item.type === 1) {
              typeLabel = item.item.type_value + '/' + item.item.design_type_value + '/' + item.item.field_value + '/' + item.item.industry_value
            } else if (item.item.type === 2) {
              typeLabel = item.item.type_value + '/' + item.item.design_type_value
            }
            designItems[i].item.type_label = typeLabel
          } // endfor
          self.designItems = designItems
        } else {
          self.$message.error(response.data.meta.message)
        }

        console.log(response.data)
      })
      .catch (function(error) {
        self.$message.error(error.message)
        return false
      })
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .content-item-box {
  
  }
  .content-item-box .item {
    border: 1px solid #D2D2D2;
    margin: 20px 0px 20px 0;
  }
  .banner {
    border-bottom: 1px solid #ccc;
    background: #FAFAFA;
  }
  .content {
    border-bottom: 1px solid #ccc;
    height: 120px;
  }
  .item p {
    padding: 10px;
  }
  .l-item p {
    font-size: 1rem;
    color: #666;
    padding: 5px 10px 5px 10px;
  }
  p.c-title {
    font-size: 1.5rem;
    color: #333;
    padding: 15px 10px 15px 10px;
  }
  .opt {
    height: 30px;
  }

  .content .l-item {
    float: left;
  }
  .content .r-item {
    float: right;
  }
  .opt .l-item {
    float: left;
    line-height: 1.2;
  }
  .opt .r-item {
    float: right;
  }
  .money-str {
    font-size: 1.5rem;
  }
  .btn {
    font-size: 1rem;
  }
  .btn a {
    color: #666;
  }
  .btn a.b-blue {
    color: #00AC84;
  }
  .btn a.b-red {
    color: #FF5A5F;
  }

</style>
