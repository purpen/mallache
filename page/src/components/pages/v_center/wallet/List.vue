<template>
  <div class="container">
    <el-row :gutter="24">
      <v-menu currentName="wallet"></v-menu>

      <el-col :span="20">
        <div class="right-content">

          <div class="my-wallet" v-loading.body="walletLoading">
            <div class="wallet-box">
              <div class="amount-show">
                <p class="price-title">账户余额（元）</p>
                <p class="price-text">¥ {{ wallet.price_total }}</p>
                <p class="price-des">*已冻结余额 {{ wallet.price_frozen }}元，不可提现</p>
              </div>
              <div class="amount-btn">
                <p>
                  <el-button class="is-custom" size="small">提现</el-button>
                  <el-button class="is-custom" type="primary" size="small">充值</el-button>
                </p>
              </div>
            </div>
            <div class="bank-box">
              <p><a href=""><i class="fa fa-credit-card" aria-hidden="true"></i> 银行账户管理</a></p>
            </div>
          </div>

          <div class="item-box">
            <h3>交易记录</h3>

            <el-table
              :data="tableData"
              :border="false"
              v-loading.body="isLoading"
              class=""
              @selection-change="handleSelectionChange"
              style="width: 100%">
              <el-table-column
                prop="id"
                label="交易单号"
                width="80">
              </el-table-column>
              <el-table-column
                prop="created_at"
                label="时间"
                width="150">
              </el-table-column>
              <el-table-column
                prop="transaction_type_value"
                label="交易类型"
                width="140">
              </el-table-column>
              <el-table-column
                label="收入/支出"
                width="120">
                <template scope="scope">
                  <p>
                    <a href="javascript:void(0);" v-show="scope.row.sure_outline_transfer" @click="sureTransfer(scope.$index, scope.row)">确认收款</a>
                    <span v-if="scope.row.type === 1">+</span>
                    <span v-if="scope.row.type === -1">-</span>
                    <span> {{ scope.row.amount }}</span>
                  </p>
                </template>
              </el-table-column>
              <el-table-column
                prop="summary"
                label="备注"
                min-width="500">
              </el-table-column>
            </el-table>

            <el-pagination
              class="pagination"
              @current-change="handleCurrentChange"
              :current-page="query.page"
              :page-size="query.pageSize"
              layout="prev, pager, next"
              :total="query.totalCount">
            </el-pagination>

          </div>

        </div>

      </el-col>
    </el-row>
  </div>
</template>

<script>
  import vMenu from '@/components/pages/v_center/Menu'
  import api from '@/api/api'
  import '@/assets/js/format'
  import '@/assets/js/date_format'

  export default {
    name: 'vcenter_wallet_list',
    components: {
      vMenu
    },
    data () {
      return {
        walletLoading: false,
        isLoading: false,
        wallet: {},
        tableData: [],
        itemList: [],
        query: {
          page: 1,
          pageSize: 10,
          totalCount: 0,
          sort: 1,
          type: 0,
          payType: 0,

          test: null
        },
        userId: this.$store.state.event.user.id
      }
    },
    methods: {
      loadList() {
        const self = this
        self.query.page = parseInt(this.$route.query.page || 1)
        self.query.sort = this.$route.query.sort || 1
        self.query.type = this.$route.query.type || 0

        self.isLoading = true
        self.$http.get(api.fundLogList, {params: {page: self.query.page, per_page: self.query.pageSize, sort: self.query.sort, type: self.query.type}})
        .then (function(response) {
          self.isLoading = false
          self.tableData = []
          if (response.data.meta.status_code === 200) {
            self.itemList = response.data.data
            self.query.totalCount = response.data.meta.pagination.total

            for (var i = 0; i < self.itemList.length; i++) {
              var item = self.itemList[i]
              item['created_at'] = item.created_at.date_format().format('yy-MM-dd hh:mm')

              self.tableData.push(item)
            } // endfor

            console.log(response.data.data)
          }
        })
        .catch (function(error) {
          self.$message.error(error.message)
          self.isLoading = false
          return false
        })
      },
      handleSelectionChange(val) {
        this.multipleSelection = val
      },
      handleSizeChange(val) {
        this.query.pageSize = val
        this.loadList()
      },
      handleCurrentChange(val) {
        this.query.page = val
        this.$router.push({name: this.$route.name, query: {page: val}})
      }
    },
    computed: {
    },
    created: function() {
      const self = this
      // 获取我的钱包
      self.walletLoading = true
      self.$http.get(api.authFundInfo, {})
      .then (function(response) {
        self.walletLoading = false
        if (response.data.meta.status_code === 200) {
          var wallet = response.data.data
          if (wallet) {
            self.wallet = wallet
          }
        }
      })
      .catch (function(error) {
        self.$message.error(error.message)
        self.walletLoading = false
        return false
      })

      // 交易记录
      this.loadList()
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

  .content-item-box {
  
  }
  .my-wallet {
    background: #FAFAFA;
    height: 190px;
    margin: 0 0 10px 0;
    position: relative;
  }

  .wallet-box {

  }

  .amount-show {
    margin: 30px 0 15px 30px;
    float: left;
  }
  .amount-show p {
    line-height: 2;
  }

  .price-title {
    color: #333;
  }
  .price-text {
    color: #FF5A5F;
    font-size: 2rem;
  }
  .price-des {
    font-size: 1.2rem;
    color: #666;
  }

  .amount-btn {
    float: right;
    margin: 30px;
  }
  .amount-btn button {
    padding: 8px 25px;
  }

  .bank-box {
    clear: both;
    margin: 15px 30px 10px 30px;
    border-top: 1px solid #ccc;
  }
  .bank-box p {
    line-height: 3.5;
    color: #222;
    font-size: 1.3rem;
  }

  .item-box {
    margin: 20px 0 0 0;
  }
  .item-box h3 {
    font-size: 1.5rem;
    color: #666;
    line-height: 2;
  }



</style>
