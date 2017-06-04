<template>
  <div class="container">
    <el-row :gutter="24" type="flex" justify="center">
      <v-menu currentName="c_item"></v-menu>

      <el-col :span="20">
        <div class="right-content">
          <v-menu-sub :waitCountProp="waitCount" :ingCountProp="ingCount"></v-menu-sub>

          <div class="loading" v-loading.body="isLoading"></div>
          <div class="content-item-box" v-show="!isLoading">

              <el-row :gutter="0" class="item-title-box" v-show="designItems.length > 0">
                <el-col :span="10">
                  <p>项目名称</p>
                </el-col>
                <el-col :span="4">
                  <p>交易金额</p>
                </el-col>
                <el-col :span="5">
                  <p>状态</p>
                </el-col>
                <el-col :span="5">
                  <p>操作</p>
                </el-col>
              </el-row>

            <div class="item" v-for="(d, index) in designItems">


                <el-row class="banner">
                  <el-col :span="3">
                    <p>{{ d.item.created_at }}</p>
                  </el-col>
                  <el-col :span="8">
                    <el-popover class="contact-popover" trigger="hover" placement="top">
                      <p class="contact">联系人: {{ d.item.contact_name }}</p>
                      <p class="contact">职位: {{ d.item.position }}</p>
                      <p class="contact">电话: {{ d.item.phone }}</p>
                      <p class="contact">邮箱: {{ d.item.email }}</p>
                        <p slot="reference" class="name-wrapper contact-user"><i class="fa fa-phone" aria-hidden="true"></i> {{ d.item.company_name }}</p>
                    </el-popover>
                  </el-col>
                </el-row>

                <el-row class="item-content">
                  <el-col :span="10" class="item-title">
                    <p class="c-title">
                      <router-link :to="{name: 'vcenterItemShow', params: {id: d.item.id}}">{{ d.item.name }}</router-link>
                    </p>
                    <p>项目预算: {{ d.item.design_cost_value }}</p>
                    <p>设计类别: {{ d.item.type_label }}</p>
                    <p>项目周期: {{ d.item.cycle_value }}</p>
                  </el-col>
                  <el-col :span="4">
                    <p>
                      <span v-show="d.item.price !== 0" class="money-str">¥ <b>{{ d.item.price }}</b></span>
                    </p>
                  </el-col>
                  <el-col :span="5">
                    <p class="status-str">{{ d.item.design_status_value }}</p>
                  </el-col>
                  <el-col :span="5">
                    <div class="btn">
                      <div v-if="d.is_contract === 0">
                        <p><el-button class="is-custom" @click="contractBtn" :index="index" size="small" :item_id="d.item.id" type="primary">提交在线合同</el-button></p>
                      </div>
                      <div v-else>
                        <div v-show="d.item.status === 5">
                          <p><el-button class="is-custom" size="small" @click="contractSendBtn" :index="index" :item_id="d.item.id" type="primary">发送合同</el-button></p>  
                          <p><el-button class="is-custom" size="small" @click="contractBtn" :index="index" :item_id="d.item.id" type="primary">修改合同</el-button></p>
                        </div>
                        <div v-show="d.item.status === 6">
                          <p><el-button class="is-custom" size="small" @click="contractBtn" :index="index" :item_id="d.item.id" type="primary">修改合同</el-button></p>                       
                        </div>

                      </div>
                      <p class="btn" v-show="d.item.status === 9">
                        <el-button class="is-custom" size="small" @click="sureBeginBtn" :index="index" :item_id="d.item.id" type="primary">确认开始</el-button>
                      </p>


                    </div>
                  </el-col>
                </el-row>

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
        <input type="hidden" ref="currentItemId" />
        <input type="hidden" ref="currentIndex" />
        <input type="hidden" ref="currentType" />
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
        userId: this.$store.state.event.user.id
      }
    },
    methods: {
      // 新增／编辑合同
      contractBtn(event) {
        var itemId = parseInt(event.currentTarget.getAttribute('item_id'))
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
        var itemId = parseInt(this.$refs.currentItemId.value)
        var index = parseInt(this.$refs.currentIndex.value)
        var type = parseInt(this.$refs.currentType.value)

        var self = this
        this.sureDialogLoadingBtn = true
        if (type === 1) {
          self.$http({method: 'post', url: api.sendContract, data: {item_demand_id: itemId}})
          .then (function(response) {
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
          .catch (function(error) {
            self.$message.error(error.message)
          })
        }
      },
      // 确认开始项目
      sureBeginBtn(event) {
        var itemId = parseInt(event.currentTarget.getAttribute('item_id'))
        var index = parseInt(event.currentTarget.getAttribute('index'))

        var self = this
        self.$http({method: 'post', url: api.designItemStartId.format(itemId), data: {}})
        .then (function(response) {
          if (response.data.meta.status_code === 200) {
            self.designItems[index].item.status = 11
            self.designItems[index].item.design_status_value = '项目进行中'
            self.$message.success('提交成功！')
          } else {
            self.$message.error(response.data.meta.message)
          }
        })
        .catch (function(error) {
          self.$message.error(error.message)
        })
      }
    },
    computed: {
    },
    created: function() {
      var self = this
      self.$http.get(api.designCooperationLists, {})
      .then (function(response) {
        self.isLoading = false
        if (response.data.meta.status_code === 200) {
          if (!response.data.data) {
            return false
          }
          self.ingCount = response.data.meta.pagination.total
          var designItems = response.data.data
          for (var i = 0; i < designItems.length; i++) {
            var item = designItems[i]
            var typeLabel = ''
            if (item.item.type === 1) {
              typeLabel = item.item.type_value + '/' + item.item.design_type_value + '/' + item.item.field_value + '/' + item.item.industry_value
            } else if (item.item.type === 2) {
              typeLabel = item.item.type_value + '/' + item.item.design_type_value
            }
            var showPrice = false
            if (item.item.status >= 5) showPrice = true
            designItems[i].item.show_price = showPrice
            designItems[i].item.type_label = typeLabel
            designItems[i]['item']['created_at'] = item.item.created_at.date_format().format('yyyy-MM-dd')
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

      // 获取待合同项目数
      self.$http.get(api.designItemList, {})
      .then (function(response) {
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
      .catch (function(error) {
        self.$message.error(error.message)
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
    margin: 0 0px 20px 0;
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
    border: 1px solid #ccc;
  }
  .el-col {
    padding: 10px 20px 10px 20px;
  }
  .el-col p {
  }

  .status-str {
    color: #FF5A5F;
    font-size: 1.2rem;
  }
  .item-title p {
    font-size: 1.2rem;
    line-height: 1.8;
  }

  p.c-title {
    font-size: 1.6rem;
    color: #333;
    padding: 0px 5px 10px 0;
    line-height: 1;
  }
  .item-content {
    padding: 10px 0 10px 0;
  }


</style>
