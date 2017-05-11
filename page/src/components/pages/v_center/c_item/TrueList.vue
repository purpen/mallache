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
                    <span>和我联系</span>
                  </p>
                  <p class="fr">{{ d.item.status_value }}</p>
              </div>
              <div class="content clear">
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
                  <p v-show="d.item.show_price">
                    <span>项目金额:</span>&nbsp;&nbsp;<span class="money-str">¥ <b>{{ d.item.price }}</b></span>
                  </p>
                </div>
                <div class="r-item">
                  <p class="btn" v-show="d.item.status === 5">
                    <a href="javascript:void(0);" @click="contractBtn" :index="index" :item_id="d.item.id">提交在线合同</a>
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
        isLoading: true,
        designItems: [],
        userId: this.$store.state.event.user.id
      }
    },
    methods: {
      contractBtn(event) {
        var itemId = parseInt(event.currentTarget.getAttribute('item_id'))
        this.$router.push({name: 'vcenterContractSubmit', params: {item_id: itemId}})
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

</style>
