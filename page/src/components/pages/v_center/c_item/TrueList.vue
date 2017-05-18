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
                  <p class="fl">
                    <span>{{ d.item.created_at }}</span>
                    <span>{{ d.item.company_name }}</span>
                    <span>&nbsp;&nbsp;</span>
                    <el-popover class="contact-popover" trigger="hover" placement="top">
                      <p class="contact">联系人: {{ d.item.contact_name }}</p>
                      <p class="contact">电话: {{ d.item.phone }}</p>
                      <p class="contact">邮箱: {{ d.item.email }}</p>
                        <el-tag slot="reference" class="name-wrapper">联系客户</el-tag>
                    </el-popover>

                  </p>
                  <p class="fr">{{ d.item.design_status_value }}</p>
              </div>
              <div class="content clear">
                <div class="l-item">
                  <p class="c-title"><router-link :to="{name: 'vcenterCItemShow', params: {id: d.item.id}}" target="_blank">{{ d.item.name }}</router-link></p>
                  <p>项目预算: {{ d.item.design_cost_value }}</p>
                  <p>设计类别: {{ d.item.type_label }}</p>
                  <p>项目周期: {{ d.item.cycle_value }}</p>
                </div>
                <div class="r-item">
                  <!--<p></p>-->
                </div>
              </div>
              <div class="opt">
                <div class="l-item">
                  <p v-show="d.item.show_price">
                    <span>项目金额:</span>&nbsp;&nbsp;<span class="money-str">¥ <b>{{ d.item.price }}</b></span>
                  </p>
                </div>
                <div class="r-item">
                  <p class="btn" v-show="d.item.status === 5">
                    <span v-if="d.is_contract === 0">
                      <a href="javascript:void(0);" @click="contractBtn" :index="index" :item_id="d.item.id">提交在线合同</a>
                    </span>
                    <span v-else>
                      <a href="javascript:void(0);" @click="contractBtn" :index="index" :item_id="d.item.id">修改合同</a>
                      <a href="javascript:void(0);" @click="contractSendBtn" :index="index" :item_id="d.item.id">发送合同</a>
                    </span>
                  </p>
                  <p class="btn" v-show="d.item.status === 6">
                    <a href="javascript:void(0);" @click="contractBtn" :index="index" :item_id="d.item.id">修改合同</a>
                  </p>

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
    height: 40px;
    line-height: 20px;
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

  p.contact {
    line-height: 1.5;
    font-size: 1.3rem;
    color: #666;
  }


</style>
