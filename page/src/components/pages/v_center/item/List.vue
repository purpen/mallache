<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20" class="anli-elrow">
      <v-menu currentName="item"></v-menu>

      <el-col :span="isMob ? 24 : 20">
        <div class="right-content">
          <v-menu-sub></v-menu-sub>
          <div class="content-item-box">
            <div class="pub">
              <router-link :to="{name: 'itemSubmitOne'}">
                <el-button class="pub-btn is-custom" type="primary" size="large"><i class="el-icon-plus"></i> 发布项目
                </el-button>
              </router-link>
            </div>

            <div class="loading" v-loading.body="isLoading" style="top: 50%;"></div>
            <div class="item ing" v-if="itemIngList.length" v-for="(d, index) in itemIngList">
              <div class="banner">
                <p>
                  <span>进行中</span>
                </p>
              </div>
              <div class="content">
                <div class="pre">
                  <p class="c-title-pro">{{ d.item.name }}</p>
                  <p class="progress-line">
                    <el-progress :text-inside="true" :show-text="false" :stroke-width="18" :percentage="d.item.progress"
                                 status="exception"></el-progress>
                  </p>
                  <p class="prefect">您的项目需求填写已经完成了{{ d.item.progress }}%。</p>
                  <p>
                    <el-button class="is-custom" :progress="d.item.stage_status" :item_id="d.item.id"
                               :item_type="d.item.type" @click="editItem" size="" type="primary">
                      <i class="el-icon-edit"> </i> 完善项目
                    </el-button>
                  </p>
                </div>
              </div>
            </div>


            <el-row class="item-title-box list-box" v-if="itemList.length && !isMob">
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

            <div class="item" v-for="(d, index) in itemList" v-if="itemList.length && !isMob">

              <el-row class="banner list-box">
                <el-col :span="24">
                  <p>{{ d.item.created_at }}</p>
                </el-col>
              </el-row>

              <el-row class="item-content list-box">
                <el-col :span="10" class="item-title">
                  <p class="c-title">
                    <router-link :to="{name: 'vcenterItemShow', params: {id: d.item.id}}">{{ d.item.name }}
                    </router-link>
                  </p>
                  <p>项目预算: {{ d.item.design_cost_value }}</p>
                  <p v-if="d.item.type === 1">
                    {{ d.item.type_value + '/' + d.item.design_type_value + '/' + d.item.field_value + '/' + d.item.industry_value
                    }}</p>
                  <p v-if="d.item.type === 2">{{ d.item.type_value + '/' + d.item.design_type_value }}</p>
                  <p>项目周期: {{ d.item.cycle_value }}</p>
                  <p>产品功能：{{d.item.product_features}}</p>
                </el-col>
                <el-col :span="3">
                  <p>
                    <span v-show="d.item.price !== 0" class="money-str">¥ <b>{{ d.item.price }}</b></span>
                  </p>
                </el-col>

                <el-col :span="7">
                  <p class="status-str" v-if="d.item.show_offer">有设计服务供应商报价</p>
                  <p class="status-str" v-else>{{ d.item.status_value }}</p>
                </el-col>

                <el-col :span="4">
                  <div class="btn" v-show="d.item.status === -2">
                    <p>
                      <el-button class="is-custom" @click="restartBtn" :item_id="d.item.id" size="small"
                                 type="primary">重新编辑
                      </el-button>
                    </p>
                    <p>
                      <el-button class="" @click="closeItemBtn" :item_id="d.item.id" :index="index" size="small"
                                 type="gray">关闭项目
                      </el-button>
                    </p>
                  </div>
                  <p class="btn" v-show="d.item.status === -1">
                    <el-button class="is-custom" @click="delItemBtn" :item_id="d.item.id" size="small" type="primary">
                      删除项目
                    </el-button>
                  </p>

                  <p class="btn" v-show="d.item.status === 3">
                    <el-button class="is-custom" @click="viewShow" :item_id="d.item.id" size="small" type="primary">
                      选择设计服务供应商
                    </el-button>
                  </p>
                  <p class="btn" v-show="d.item.status === 4">
                    <el-button class="is-custom" @click="viewShow" :item_id="d.item.id" size="small" type="primary"
                               v-if="d.item.show_offer">查看报价
                    </el-button>
                    <el-button class="is-custom" @click="viewShow" :item_id="d.item.id" size="small" type="primary"
                               v-else>查看设计公司
                    </el-button>
                  </p>

                  <p class="btn" v-show="d.item.status === 6">
                    <el-button class="is-custom" @click="viewShow" :item_id="d.item.id" size="small" type="primary">
                      查看合同
                    </el-button>
                  </p>
                  <p class="btn" v-show="d.item.status === 7">
                    <el-button class="is-custom" @click="secondPay" :item_id="d.item.id" size="small" type="primary">
                      支付项目款
                    </el-button>
                  </p>
                  <p class="btn" v-show="d.item.status === 8">
                    <el-button class="is-custom" @click="secondPay" :item_id="d.item.id" size="small" type="primary">
                      支付项目款
                    </el-button>
                  </p>
                  <p class="btn" v-show="d.item.status === 15">
                    <el-button class="is-custom" @click="viewShow" :item_id="d.item.id" size="small" type="primary">
                      验收项目
                    </el-button>
                  </p>
                  <p class="btn" v-show="d.item.status === 18">
                    <el-button class="is-custom" @click="viewShow" :item_id="d.item.id" size="small" type="primary">
                      评价
                    </el-button>
                  </p>
                  <p class="btn" v-show="d.item.is_view_show">
                    <el-button class="is-custom" @click="viewShow" :item_id="d.item.id" size="small" type="primary">
                      查看详情
                    </el-button>
                  </p>
                </el-col>
              </el-row>
            </div>

            <div class="item" v-for="(d, index) in itemList" v-if="itemList.length && isMob">
              <div class="banner list-box">
                <p>{{ d.item.created_at }}</p>
              </div>
              <div class="list-body">
                <p class="list-title Bborder">
                  <router-link :to="{name: 'vcenterItemShow', params: {id: d.item.id}}">{{ d.item.name }}
                  </router-link>
                </p>
                <div class="list-content">
                  <section class="c-body">
                    <p>项目预算： {{ d.item.design_cost_value }}</p>
                    <p>项目周期：{{ d.item.cycle_value }}</p>
                    <p v-if="d.item.type === 1">
                      {{ d.item.type_value + '/' + d.item.design_type_value + '/' + d.item.field_value + '/' + d.item.industry_value
                      }}</p>
                    <p v-if="d.item.type === 2">{{ d.item.type_value + '/' + d.item.design_type_value }}</p>
                    <p>产品功能：{{d.item.product_features}}</p>
                  </section>
                  <p class="money-str price-m Bborder">交易金额：
                    <span v-if="d.item.price !== 0">¥ <b>{{ d.item.price }}</b></span>
                    <span v-else>暂无</span>
                  </p>
                  <p class="price-m Bborder">状态
                    <span class="status-str" v-if="d.item.show_offer">有设计服务供应商报价</span>
                    <span class="status-str" v-else>{{ d.item.status_value }}</span>
                  </p>
                  <section>
                    <div class="btn" v-show="d.item.status === -2">
                      <p>
                        <el-button class="is-custom" @click="restartBtn" :item_id="d.item.id" size="small"
                                   type="primary">重新编辑
                        </el-button>
                      </p>
                      <p>
                        <el-button class="" @click="closeItemBtn" :item_id="d.item.id" :index="index" size="small"
                                   type="gray">关闭项目
                        </el-button>
                      </p>
                    </div>
                    <p class="btn" v-show="d.item.status === -1">
                      <el-button class="is-custom" @click="delItemBtn" :item_id="d.item.id" size="small" type="primary">
                        删除项目
                      </el-button>
                    </p>

                    <p class="btn" v-show="d.item.status === 3">
                      <el-button class="is-custom" @click="viewShow" :item_id="d.item.id" size="small" type="primary">
                        选择设计服务供应商
                      </el-button>
                    </p>
                    <p class="btn" v-show="d.item.status === 4">
                      <el-button class="is-custom" @click="viewShow" :item_id="d.item.id" size="small" type="primary"
                                 v-if="d.item.show_offer">查看报价
                      </el-button>
                      <el-button class="is-custom" @click="viewShow" :item_id="d.item.id" size="small" type="primary"
                                 v-else>查看设计公司
                      </el-button>
                    </p>

                    <p class="btn" v-show="d.item.status === 6">
                      <el-button class="is-custom" @click="viewShow" :item_id="d.item.id" size="small" type="primary">
                        查看合同
                      </el-button>
                    </p>
                    <p class="btn" v-show="d.item.status === 7">
                      <el-button class="is-custom" @click="secondPay" :item_id="d.item.id" size="small" type="primary">
                        支付项目款
                      </el-button>
                    </p>
                    <p class="btn" v-show="d.item.status === 8">
                      <el-button class="is-custom" @click="secondPay" :item_id="d.item.id" size="small" type="primary">
                        支付项目款
                      </el-button>
                    </p>
                    <p class="btn" v-show="d.item.status === 15">
                      <el-button class="is-custom" @click="viewShow" :item_id="d.item.id" size="small" type="primary">
                        验收项目
                      </el-button>
                    </p>
                    <p class="btn" v-show="d.item.status === 18">
                      <el-button class="is-custom" @click="viewShow" :item_id="d.item.id" size="small" type="primary">
                        评价
                      </el-button>
                    </p>
                    <p class="btn" v-show="d.item.is_view_show">
                      <el-button class="is-custom" @click="viewShow" :item_id="d.item.id" size="small" type="primary">
                        查看详情
                      </el-button>
                    </p>
                  </section>
                </div>
              </div>
            </div>
          </div>
        </div>

      </el-col>
    </el-row>

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
  import vMenuSub from '@/components/pages/v_center/item/MenuSub'
  import api from '@/api/api'
  import '@/assets/js/format'
  import '@/assets/js/date_format'

  export default {
    name: 'vcenter_item_list',
    components: {
      vMenu,
      vMenuSub
    },
    data () {
      return {
        sureDialog: false,
        sureDialogMessage: '确认执行此操作？',
        sureDialogLoadingBtn: false,
        isLoading: false,
        itemList: [],
        itemIngList: [],
        pagination: {},
        userId: this.$store.state.event.user.id
      }
    },
    methods: {
      viewShow(event) {
        let itemId = parseInt(event.currentTarget.getAttribute('item_id'))
        this.$router.push({name: 'vcenterItemShow', params: {id: itemId}})
      },
      loadList(type) {
        const that = this
        that.isLoading = true
        that.$http.get(api.itemList, {params: {type: type, per_page: 50}})
          .then(function (response) {
            that.isLoading = false
            if (response.data.meta.status_code === 200) {
              if (!response.data.data) {
                return false
              }
              let data = response.data.data
              for (let i = 0; i < data.length; i++) {
                let d = data[i]
                let status = d.item.status
                let progress = d.item.stage_status
                switch (progress) {
                  case 1:
                    data[i]['item']['progress'] = 20
                    break
                  case 2:
                    data[i]['item']['progress'] = 60
                    break
                  case 3:
                    data[i]['item']['progress'] = 90
                    break
                  default:
                    data[i]['item']['progress'] = 0
                }
                let showOffer = false
                if (d.item.status === 4 && d.purpose_count > 0) {
                  showOffer = true
                }
                let showView = false
                if (status === 2 || status === 5 || status === 9 || status === 11 || status === 20 || status === 22) {
                  showView = true
                }
                data[i]['item']['is_view_show'] = showView
                data[i]['item']['show_offer'] = showOffer
                data[i]['item']['created_at'] = d.item.created_at.date_format().format('yyyy-MM-dd')
              } // endfor
              if (type === 1) {
                that.itemIngList = data
              } else if (type === 2) {
                that.itemList = data
              }
//              console.log(data)
            }
          })
          .catch(function (error) {
            that.$message.error(error.message)
            return false
          })
      },
      // 确认执行对话框
      sureDialogSubmit() {
        let itemId = parseInt(this.$refs.currentItemId.value)
        let index = parseInt(this.$refs.currentIndex.value)
        let type = parseInt(this.$refs.currentType.value)

        let self = this
        this.sureDialogLoadingBtn = true

        if (type === 1) {
          self.$http.post(api.demandCloseItem, {item_id: itemId})
            .then(function (response) {
              self.sureDialogLoadingBtn = false
              if (response.data.meta.status_code === 200) {
                self.designItems[index].item.status = -1
                self.designItems[index].item.status_value = '项目关闭'
                self.$message.success('提交成功！')
              } else {
                self.$message.error(response.data.meta.message)
              }
            })
            .catch(function (error) {
              self.sureDialogLoadingBtn = false
              self.$message.error(error.message)
            })
        }
      },
      editItem(event) {
        let progress = parseInt(event.currentTarget.getAttribute('progress'))
        let itemId = event.currentTarget.getAttribute('item_id')
        let type = parseInt(event.currentTarget.getAttribute('item_type'))
        let name = null
        switch (progress) {
          case 0:
            name = 'itemSubmitTwo'
            break
          case 1:
            if (type === 1) {
              name = 'itemSubmitThree'
            } else if (type === 2) {
              name = 'itemSubmitUIThree'
            }
            break
          case 2:
            name = 'itemSubmitFour'
            break
          case 3:
            name = 'itemSubmitFive'
            break
        }
        this.$router.push({name: name, params: {id: itemId}})
      },
      // 匹配失败后重新编辑项目
      restartBtn(event) {
        let itemId = event.currentTarget.getAttribute('item_id')
        let self = this
        self.$http.post(api.demandItemRestart, {item_id: itemId})
          .then(function (response) {
            if (response.data.meta.status_code === 200) {
              self.$router.push({name: 'itemSubmitTwo', params: {id: itemId}})
            } else {
              self.$message.error(response.data.meta.message)
            }
          })
          .catch(function (error) {
            self.$message.error(error.message)
          })
      },
      // 匹配失败后关闭项目
      closeItemBtn(event) {
        this.$refs.currentItemId.value = parseInt(event.currentTarget.getAttribute('item_id'))
        this.$refs.currentIndex.value = parseInt(event.currentTarget.getAttribute('index'))
        this.$refs.currentType.value = 1
        this.sureDialog = true
      },
      // 关闭项目后删除项目
      delItemBtn(event) {
      },
      // 支付项目资金
      secondPay(event) {
        let itemId = event.currentTarget.getAttribute('item_id')
        this.$router.push({name: 'itemPayFund', params: {item_id: itemId}})
      }
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      }
    },
    created: function () {
      let uType = this.$store.state.event.user.type
      // 如果是设计公司，跳到设计公司列表
      if (uType === 2) {
        this.$router.replace({name: 'vcenterCItemList'})
        return
      }
      this.loadList(1)
      this.loadList(2)
    },
    watch: {
      '$route' (to, from) {
        // 对路由变化作出响应...
        let type = this.$route.query.type
        if (type) {
          type = parseInt(type)
        } else {
          type = 0
        }
        this.loadList(type)
      }
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .content-item-box {
    min-height: 500px;
    margin-top: 20px;
  }

  .pub {
    background: #FAFAFA;
    height: 150px;
    margin: 20px 0 10px 0;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .pub .pub-btn {
    padding: 10px 40px 10px 40px;
  }

  .content-item-box .item {
    border: 1px solid #D2D2D2;
    margin: 0 0 20px 0;
  }

  .banner {
    height: 40px;
    line-height: 20px;
    border-bottom: 1px solid #ccc;
    background: #FAFAFA;
  }

  .content {
    border-bottom: 1px solid #ccc;
  }

  .item.ing p {
    padding: 10px;
  }

  p.c-title-pro {
    font-size: 1.5rem;
    color: #333;
    padding: 15px 10px 5px 10px;
  }

  .opt {
    height: 30px;
  }

  .money-str {
    font-size: 1.5rem;
    color: #222;
    overflow: hidden;
  }

  section .btn {
    width: 40%;
    margin: 10px auto 0;
  }

  section .btn button {
    width: 100%;
    line-height: 16px;
  }

  .btn {
    font-size: 1rem;
  }

  .btn p {
    line-height: 35px;
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

  .prefect {
    font-size: 1.5rem;
    color: #666;
    margin-top: 0;
    margin-bottom: -10px;
  }

  .item-title-box {
    margin-top: 20px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
  }

  .list-box .el-col {
    padding: 10px 20px 10px 20px;
  }

  .list-body {
    padding: 11px 15px;
  }

  .status-str {
    color: #FF5A5F;
    font-size: 1.2rem;
    line-height: 1.3;
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

  h3.c-title {
    font-size: 1.6rem;
    color: #333;
    padding: 14px 0;
  }

  .c-body {
    padding-bottom: 16px;
  }

  .c-body p {
    color: #222;
    line-height: 1.4;
  }

  .item-content {
    padding: 10px 0 10px 0;
  }

  .price-m {
    padding: 14px 0;
    overflow: hidden;
  }

  .price-m span {
    float: right;
    width: 50%;
    text-align: right;
  }

  .Bborder {
    border-bottom: 1px solid #E6E6E6
  }

  .list-title {
    font-size: 1.5rem;
    padding-bottom: 8px;
    border-bottom: none;
  }

</style>
