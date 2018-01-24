<template>
  <div class="container clearfix">
    <div class="blank20"></div>
    <v-menu currentName="c_item" :class="[isMob ? 'v-menu' : '']"></v-menu>
    <el-col :span="isMob ? 24 :20" v-loading.body="isLoading">
      <div class="right-content">
        <v-menu-sub :waitCountProp="waitCount" :ingCountProp="ingCount"></v-menu-sub>

        <div :class="['content-item-box', isMob ? 'content-item-box-m' : '' ]">

          <el-row v-if="!isMob" class="item-title-box list-box" v-show="designItems.length">
            <el-col :span="10">
              <p>项目名称</p>
            </el-col>
            <el-col :span="3">
              <p>交易金额</p>
            </el-col>
            <el-col :span="7">
              <p>状态</p>
            </el-col>
            <el-col :span="4">
              <p>操作</p>
            </el-col>
          </el-row>

          <div class="item" v-for="(d, index) in designItems" :key="index">

            <el-row class="banner list-box">
              <el-col :span="12">
                <p>{{ d.item.created_at }}</p>
              </el-col>
              <el-col :span="12">
                <el-popover class="contact-popover" trigger="hover" placement="top-start">
                  <p class="contact">联系人: {{ d.item.contact_name }}</p>
                  <p class="contact">职位: {{ d.item.position }}</p>
                  <p class="contact">电话: {{ d.item.phone }}</p>
                  <p class="contact">邮箱: {{ d.item.email }}</p>
                  <p slot="reference" class="name-wrapper contact-user"><i class="fa fa-phone" aria-hidden="true"></i>
                    {{ d.item.company_name }}</p>
                  <p>产品功能：{{d.item.product_features}}</p>
                </el-popover>
              </el-col>
            </el-row>

            <el-row :class="['item-content','list-box', isMob ? 'item-content-m' : '']">
              <el-col :span="isMob ? 24 : 10" class="item-title">
                <p class="c-title">
                  <router-link :to="{name: 'vcenterCItemShow', params: {id: d.item.id}}">{{ d.item.name }}
                  </router-link>
                </p>
                <p>项目预算: {{ d.item.design_cost_value }}</p>
                <p v-if="d.item.type === 1">
                  {{ d.item.type_value + '/' + d.item.design_type_value + '/' + d.item.field_value + '/' + d.item.industry_value
                  }}</p>
                <p v-if="d.item.type === 2">{{ d.item.type_value + '/' + d.item.design_type_value }}</p>
                <p>项目周期: {{ d.item.cycle_value }}</p>
              </el-col>
              <el-col :span="isMob ? 24 : 3">
                <p>
                  <span v-if="d.item.price !== 0" class="money-str">¥ <b>{{ d.item.price }}</b></span>
                </p>
              </el-col>
              <el-col :span="isMob ? 24 : 7">
                <p :class="['status-str','clearfix', isMob ? 'status-str-m' : '']"><i
                  v-if="isMob">状态：</i><span>{{ d.status_value }}</span></p>
              </el-col>
              <el-col :span="isMob ? 24 : 4" :class="[isMob ? 'btnGroup' : '']">
                <div class="btn" v-if="d.design_company_status === 0">
                  <p>
                    <el-button class="is-custom" @click="takingBtn" size="small" :item_id="d.item.id" :index="index"
                               :cost="d.item.design_cost_value" type="primary">提交报价单
                    </el-button>
                  </p>
                  <p>
                    <el-button class="is-custom" @click="companyRefuseBtn" size="small" :index="index"
                               :item_id="d.item.id">暂无兴趣
                    </el-button>
                  </p>

                </div>
                <p>
                  <el-button class="is-custom" v-if="d.design_company_status === 2" @click="showView" size="small"
                             :index="index" :item_id="d.item.id">查看报价
                  </el-button>
                </p>
              </el-col>
            </el-row>
          </div>
        </div>
      </div>
      <div class="empty" v-if="isEmpty === true"></div>
      <p v-if="isEmpty === true" class="noMsg">暂无项目订单</p>
    </el-col>

    <el-dialog title="提交项目报价" v-model="takingPriceDialog">
      <el-form label-position="top" :model="takingPriceForm" :rules="takingPriceRuleForm" ref="takingPriceRuleForm">
        <input type="hidden" v-model="takingPriceForm.itemId" value=""/>
        <el-form-item label="项目报价" prop="price" label-width="200px">
          <el-input type="text" v-model.number="takingPriceForm.price" :placeholder="currentCost"
                    auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="报价说明" prop="summary" label-width="80px">
          <el-input type="textarea" v-model="takingPriceForm.summary" :autosize="{ minRows: 2, maxRows: 8}"
                    auto-complete="off"></el-input>
        </el-form-item>
        <div class="taking-price-btn">
          <el-button @click="takingPriceDialog = false">取 消</el-button>
          <el-button type="primary" :loading="isTakingLoadingBtn" class="is-custom"
                     @click="takingPriceSubmit('takingPriceRuleForm')">确 定
          </el-button>
        </div>

      </el-form>
    </el-dialog>

    <el-dialog
      title="提示"
      v-model="sureRefuseItemDialog"
      size="tiny">
      <span>确认执行此操作?</span>
      <span slot="footer" class="dialog-footer">
        <input type="hidden" ref="refuseItemId"/>
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
        currentIndex: '',
        currentCost: '',
        waitCount: 0,
        ingCount: 0,
        takingPriceForm: {
          itemId: '',
          price: '',
          summary: ''
        },
        takingPriceRuleForm: {
          price: [
            {required: true, type: 'number', message: '请添写报价金额,必须为整数', trigger: 'blur'}
          ],
          summary: [
            {required: true, message: '请添写报价说明', trigger: 'blur'}
          ]
        },
        userId: this.$store.state.event.user.id,
        isEmpty: ''
      }
    },
    methods: {
      // 进入详情
      showView() {
        let itemId = parseInt(event.currentTarget.getAttribute('item_id'))
        this.$router.push({name: 'vcenterCItemShow', params: {id: itemId}})
      },
      // 项目报价弹出层
      takingBtn(event) {
        let itemId = parseInt(event.currentTarget.getAttribute('item_id'))
        this.currentIndex = parseInt(event.currentTarget.getAttribute('index'))
        this.currentCost = event.currentTarget.getAttribute('cost')
        this.takingPriceForm.itemId = itemId
        this.takingPriceDialog = true
      },
      // 提交项目报价
      takingPriceSubmit(formName) {
        let self = this
        self.$refs[formName].validate((valid) => {
          // 验证通过，提交
          if (valid) {
            let row = {
              item_demand_id: self.takingPriceForm.itemId,
              price: self.takingPriceForm.price,
              summary: self.takingPriceForm.summary
            }

            if ((row.price + '').indexOf('.') !== -1) {
              self.$message.error('金额必须为整数!')
              return false
            }

            let apiUrl = api.addQuotation
            let method = 'post'

            self.isTakingLoadingBtn = true
            self.$http({method: method, url: apiUrl, data: row})
              .then(function (response) {
                if (response.data.meta.status_code === 200) {
                  self.$message.success('提交成功！')
                  self.designItems[self.currentIndex].design_company_status = 2
                  self.designItems[self.currentIndex].design_company_status_value = '已提交报价'
                  self.isTakingLoadingBtn = false
                  self.takingPriceDialog = false
                } else {
                  self.$message.error(response.data.meta.message)
                  self.isTakingLoadingBtn = false
                }
              })
              .catch(function (error) {
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
        let itemId = parseInt(event.currentTarget.getAttribute('item_id'))
        this.currentIndex = parseInt(event.currentTarget.getAttribute('index'))
        this.$refs.refuseItemId.value = itemId
        this.sureRefuseItemDialog = true
      },
      // 确认拒绝项目
      sureRefuseItemSubmit() {
        let itemId = this.$refs.refuseItemId.value
        let self = this
        this.refuseItemLoadingBtn = true
        self.$http({method: 'get', url: api.companyRefuseItemId.format(itemId), data: {}})
          .then(function (response) {
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
          .catch(function (error) {
            self.$message.error(error.message)
            self.refuseItemLoadingBtn = false
            self.sureRefuseItemDialog = false
            return false
          })
      }
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      }
    },
    created: function () {
      let self = this
      // 如果是用户，跳到设计用户列表
      let uType = this.$store.state.event.user.type
      if (uType !== 2) {
        this.isLoading = false
        this.$router.replace({name: 'vcenterItemList'})
        return
      }
      self.$http.get(api.designItemList, {})
        .then(function (response) {
          self.isLoading = false
          if (response.data.meta.status_code === 200) {
            if (!response.data.data) {
              return false
            }
            self.waitCount = response.data.meta.pagination.total
            let designItems = response.data.data
            for (let i = 0; i < designItems.length; i++) {
              let item = designItems[i]
              let typeLabel = ''
              if (item.item.type === 1) {
                typeLabel = item.item.type_value + '/' + item.item.design_type_value + '/' + item.item.field_value + '/' + item.item.industry_value
              } else if (item.item.type === 2) {
                typeLabel = item.item.type_value + '/' + item.item.design_type_value
              }
              designItems[i].item.type_label = typeLabel
              designItems[i]['item']['created_at'] = item.item.created_at.date_format().format('yyyy-MM-dd')
            } // endfor
            self.designItems = designItems
            if (self.designItems.length) {
              self.isEmpty = false
            } else {
              self.isEmpty = true
            }
          } else {
            self.$message.error(response.data.meta.message)
          }

//          console.log(response.data)
        })
        .catch(function (error) {
          self.$message.error(error.message)
          return false
        })

      // 获取已确认合作的项目数
      self.$http.get(api.designCooperationLists, {})
        .then(function (response) {
          self.isLoading = false
          if (response.data.meta.status_code === 200) {
            if (!response.data.data) {
              return false
            }
            self.ingCount = response.data.meta.pagination.total
          } else {
            self.$message.error(response.data.meta.message)
          }
        })
        .catch(function (error) {
          self.$message.error(error.message)
        })
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .container {
    overflow: hidden;
  }
  .content-item-box-m {
    margin: 15px 15px 0;
  }

  .content-item-box .item {
    border: 1px solid #D2D2D2;
    margin: -1px 0 20px 0;
  }

  .banner {
    line-height: 25px;
    border-bottom: 1px solid #ccc;
    background: #FAFAFA;
  }

  .banner .contact-user {
    color: #222;
  }

  .content {
    border-bottom: 1px solid #ccc;
    height: 120px;
  }

  p.c-title {
    width: 100%;
    font-size: 1.5rem;
    color: #333;
    padding: 15px 10px 15px 10px;
  }

  .opt {
    height: 30px;
  }

  .money-str {
    font-size: 1.5rem;
  }

  .btnGroup {
    display: flex;
    margin: 10px 0;
    align-items: center;
    justify-content: center;
  }

  .btnGroup p, .btnGroup .btn {
    width: 40%;
  }

  .btnGroup p button {
    width: 100%;
  }

  .btn {
    font-size: 1rem;
  }

  .btn p {
    margin-bottom: 10px;
    width: 100%;
  }

  .btn a.b-blue {
    color: #00AC84;
  }

  .btn a.b-red {
    color: #FF5A5F;
  }

  .btnGroup .btn {
    display: flex;
    justify-content: center;
  }

  p.contact {
    line-height: 1.5;
    font-size: 1.3rem;
    color: #666;
  }

  .taking-price-btn {
    float: right;
    margin-bottom: 20px;
  }

  .item-title-box {
    margin-top: 20px;
    border: 1px solid #d2d2d2;
    border-bottom: none;
  }

  .list-box .el-col {
    padding: 10px 20px 10px 20px;
  }

  .status-str {
    font-size: 1.4rem;
    line-height: 1.3;
  }

  .status-str-m {
    font-size: 1.5rem;
    margin-top: 10px;
    padding: 10px 0;
    border-top: 1px solid #e6e6e6;
    border-bottom: 1px solid #e6e6e6;
  }

  .status-str span {
    font-size: 1.2rem;
    color: #FF5A5F;
  }

  .status-str-m span {
    float: right;
    width: 40%;
    text-align: right;
  }

  .item-title p {
    font-size: 1.2rem;
    line-height: 1.8;
  }

  p.c-title {
    font-size: 1.6rem;
    color: #333;
    padding: 0 5px 10px 0;
    line-height: 1;
  }

  .item-content {
    padding: 10px 0 10px 0;
  }

  .item-content-m {
    padding: 0
  }

  .item-content-m .el-col {
    padding: 0 10px;
  }

  .item-content-m .item-title p.c-title {
    padding: 11px 0 8px;
    font-size: 1.5rem;
    line-height: 1
  }

  .item-content-m .item-title p {
    font-size: 1.4rem;
    line-height: 1.4;
  }

  .empty {
    width: 122px;
    height: 113px;
    margin: 100px auto 0;
    background: url("../../../../assets/images/\tools/report/NoContent.png") no-repeat;
    background-size: contain;
  }

  .noMsg {
    text-align: center;
    color: #969696;
    line-height: 3;
  }
</style>
