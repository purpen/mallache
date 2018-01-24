<template>
  <div class="container">
    <div class="blank20"></div>

    <v-menu currentName="c_item" class="c_item"></v-menu>

    <el-col :span="isMob ? 24 : 20">
      <div class="right-content">
        <v-menu-sub :waitCountProp="waitCount" :ingCountProp="ingCount"></v-menu-sub>

        <div class="loading"></div>
        <div :class="['content-item-box', isMob ? 'content-item-box-m' : '' ]" v-loading="isLoading">

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
                </el-popover>
              </el-col>
            </el-row>
            <el-row :class="['item-content','list-box', isMob ? 'item-content-m' : '']">
              <el-col :span="isMob ? 24 : 10" class="item-title">
                <p class="c-title">
                  <router-link :to="{name: 'vcenterItemShow', params: {id: d.item.id}}">{{ d.item.name }}</router-link>
                </p>
                <p>项目预算: {{ d.item.design_cost_value }}</p>
                <p>设计类别: {{ d.item.type_label }}</p>
                <p>项目周期: {{ d.item.cycle_value }}</p>
              </el-col>
              <el-col :span="isMob ? 24 : 3">
                <p style="white-space: nowrap">
                  <span v-if="d.item.price !== 0" class="money-str"><i v-if="isMob">价格：</i>¥ <b>{{ d.item.price
                    }}</b></span>
                </p>
              </el-col>
              <el-col :span="isMob ? 24 : 7">
                <p :class="['status-str','clearfix', isMob ? 'status-str-m' : '']"><i
                  v-if="isMob">状态：</i><span>{{ d.item.design_status_value }}</span></p>
              </el-col>
              <el-col :span="isMob ? 24 : 4" :class="[isMob ? 'btnGroup' : '']">
                <div class="btn clearfix">
                  <div v-if="d.is_contract === 0" class="clearfix">
                    <p>
                      <el-button class="is-custom" @click="contractBtn" :index="index" size="small" :item_id="d.item.id"
                                 type="primary">编辑在线合同
                      </el-button>
                    </p>
                  </div>
                  <div v-else class="clearfix">
                    <div v-if="d.item.status === 5" class="clearfix">
                      <p>
                        <el-button class="is-custom" size="small" @click="contractSendBtn" :index="index"
                                   :item_id="d.item.id" type="primary">发送合同
                        </el-button>
                      </p>
                      <p>
                        <el-button class="is-custom" size="small" @click="contractBtn" :index="index"
                                   :item_id="d.item.id" type="primary">修改合同
                        </el-button>
                      </p>
                    </div>
                    <div v-if="d.item.status === 6" class="clearfix">
                      <p>
                        <el-button class="is-custom" size="small" @click="contractBtn" :index="index"
                                   :item_id="d.item.id" type="primary">修改合同
                        </el-button>
                      </p>
                    </div>

                  </div>
                  <p class="btn" v-if="d.item.status === 9">
                    <el-button class="is-custom" size="small" @click="sureBeginBtn" :index="index" :item_id="d.item.id"
                               type="primary">确认开始
                    </el-button>
                  </p>
                  <p v-if="d.item.is_show_view">
                    <el-button class="is-custom" size="small" @click="showView" :index="index" :item_id="d.item.id"
                               type="primary">查看详情
                    </el-button>
                  </p>
                </div>
              </el-col>
            </el-row>
          </div>
        </div>
      </div>

      <div class="empty" v-if="isEmpty === true"></div>
      <p v-if="isEmpty === true" class="noMsg">暂无已合作项目</p>
    </el-col>

    <el-dialog
      title="提示"
      v-model="sureDialog"
      size="tiny">
      <span>{{ sureDialogMessage }}</span>
      <span slot="footer" class="dialog-footer">
        <input type="hidden" ref="currentItemId"/>
        <input type="hidden" ref="currentIndex"/>
        <input type="hidden" ref="currentType"/>
        <el-button @click="sureDialog = false">取 消</el-button>
        <el-button type="primary" :loading="sureDialogLoadingBtn" @click="sureDialogSubmit">确 定</el-button>
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
    name: 'vcenter_true_c_item_list',
    components: {
      vMenu,
      vMenuSub
    },
    data () {
      return {
        sureDialog: false,
        sureDialogMessage: '确认执行此操作？',
        sureDialogLoadingBtn: false,
        isLoading: true,
        designItems: [],
        waitCount: 0,
        ingCount: 0,
        userId: this.$store.state.event.user.id,
        isEmpty: ''
      }
    },
    methods: {
      // 新增／编辑合同
      contractBtn(event) {
        let itemId = parseInt(event.currentTarget.getAttribute('item_id'))
        this.$router.push({name: 'vcenterContractSubmit', params: {item_id: itemId}})
      },
      // 发送合同
      contractSendBtn(event) {
        this.$refs.currentItemId.value = parseInt(event.currentTarget.getAttribute('item_id'))
        this.$refs.currentIndex.value = parseInt(event.currentTarget.getAttribute('index'))
        this.$refs.currentType.value = 1
        this.sureDialog = true
      },
      // 确认执行对话框
      sureDialogSubmit() {
        let itemId = parseInt(this.$refs.currentItemId.value)
        let index = parseInt(this.$refs.currentIndex.value)
        let type = parseInt(this.$refs.currentType.value)

        let self = this
        this.sureDialogLoadingBtn = true
        if (type === 1) {
          self.$http({method: 'post', url: api.sendContract, data: {item_demand_id: itemId}})
            .then(function (response) {
              self.sureDialogLoadingBtn = false
              self.sureDialog = false
              if (response.data.meta.status_code === 200) {
                self.designItems[index].item.status = 6
                self.designItems[index].item.design_status_value = '等待客户确认合同'
                self.$message.success('提交成功！')
              } else {
                self.$message.error(response.data.meta.message)
              }
            })
            .catch(function (error) {
              self.$message.error(error.message)
            })
        }
      },
      // 确认开始项目
      sureBeginBtn(event) {
        let itemId = parseInt(event.currentTarget.getAttribute('item_id'))
        let index = parseInt(event.currentTarget.getAttribute('index'))

        let self = this
        self.$http({method: 'post', url: api.designItemStartId.format(itemId), data: {}})
          .then(function (response) {
            if (response.data.meta.status_code === 200) {
              self.designItems[index].item.status = 11
              self.designItems[index].item.design_status_value = '项目进行中'
              self.$message.success('提交成功！')
            } else {
              self.$message.error(response.data.meta.message)
            }
          })
          .catch(function (error) {
            self.$message.error(error.message)
          })
      },
      // 进入详情
      showView() {
        let itemId = parseInt(event.currentTarget.getAttribute('item_id'))
        this.$router.push({name: 'vcenterCItemShow', params: {id: itemId}})
      }
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      }
    },
    created: function () {
      let self = this
      self.$http.get(api.designCooperationLists, {})
        .then(function (response) {
          self.isLoading = false
          if (response.data.meta.status_code === 200) {
            if (!response.data.data) {
              return false
            }
            self.ingCount = response.data.meta.pagination.total
            let designItems = response.data.data
            for (let i = 0; i < designItems.length; i++) {
              let item = designItems[i]
              let typeLabel = ''
              if (item.item.type === 1) {
                typeLabel = item.item.type_value + '/' + item.item.design_type_value + '/' + item.item.field_value + '/' + item.item.industry_value
              } else if (item.item.type === 2) {
                typeLabel = item.item.type_value + '/' + item.item.design_type_value
              }
              let showPrice = false
              let showView = false
              let status = item.item.status
              if (item.item.status >= 5) showPrice = true
              if (status === 7 || status === 8 || status === 11 || status === 15 || status === 18 || status === 20 || status === 22) {
                showView = true
              }
              designItems[i].item.show_price = showPrice
              designItems[i].item.is_show_view = showView
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
        })
        .catch(function (error) {
          self.$message.error(error.message)
          return false
        })

      // 获取待合同项目数
      self.$http.get(api.designItemList, {})
        .then(function (response) {
          self.isLoading = false
          if (response.data.meta.status_code === 200) {
            if (!response.data.data) {
              return false
            }
            self.waitCount = response.data.meta.pagination.total
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
    margin: 0 0 20px 0;
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

  .money-str i {
    font-size: 1.4rem;
  }

  .btnGroup {
    display: flex;
    margin: 10px 0;
    align-items: center;
    justify-content: space-around;
  }

  .btnGroup .btn {
    width: 40%;
  }

  .btnGroup .btn div {
    width: 100%;
    display: flex;
    justify-content: center;
    margin-right: 10px;
  }

  .btnGroup p {
    width: 100%;
  }

  .btnGroup p button {
    width: 100%;
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

  .btn p {
    margin-bottom: 10px;
  }

  p.contact {
    line-height: 1.5;
    font-size: 1.3rem;
    color: #666;
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
