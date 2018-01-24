<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="24" class="anli-elrow">
      <v-menu currentName="wallet"></v-menu>

      <el-col :span="isMob ? 24 : 20">
        <div class="right-content">
          <div :class="['my-wallet', isMob ? 'my-wallet-m' : '' ]" v-loading.body="walletLoading">
            <div class="wallet-box">
              <div :class="['amount-show', isMob ? 'amount-show-m' : '']">
                <p :class="['price-title', isMob ? 'price-title-m' : '']">账户余额（元）</p>
                <p :class="['price-text', isMob ? 'price-text-m' : '']">¥{{ wallet.price_total }}</p>
                <p :class="['price-des', isMob ? 'price-des-m' : '']">*已冻结余额 {{ wallet.price_frozen }}元，不可提现</p>
              </div>
              <div :class="['amount-btn', isMob ? 'amount-show-m amount-btn-m' : '']">
                <p>
                  <el-button class="is-custom withdraw" @click="withdraw" size="small">提现</el-button>
                  <!--<el-button class="is-custom" type="primary" size="small">充值</el-button>-->
                </p>
              </div>
            </div>
            <div :class="['bank-box', isMob ? 'bank-box-m' : '']">
              <p>
                <router-link :to="{name: 'vcenterBankList'}"><i class="fa fa-credit-card" aria-hidden="true"></i> 银行账户管理
                </router-link>
              </p>
            </div>
          </div>

          <div :class="['item-box', isMob ? 'item-box-m' : '']" v-if="tableData.length">
            <h3 class="data-record">
              <span @click="showTransaction" :class="{'active' : record === 'transaction'}">交易记录</span>
              <span @click="showWithdraw" :class="{'active' : record === 'withdraw'}">提现记录</span>
            </h3>

            <article v-if="record === 'transaction'">
              <el-table v-if="!isMob"
                        :data="tableData" :border="false" v-loading.body="isLoading"
                        @selection-change="handleSelectionChange"
                        key="transaction"
                        style="width: 100%">
                <el-table-column prop="number" label="交易单号" width="200">
                </el-table-column>

                <el-table-column
                  prop="transaction_type_value" label="交易类型" width="120">
                </el-table-column>
                <el-table-column
                  label="收入/支出" width="120">
                  <template slot-scope="scope">
                    <p>
                      <a href="javascript:void(0);" v-show="scope.row.sure_outline_transfer"
                        @click="sureTransfer(scope.$index, scope.row)">确认收款</a>
                      <span v-if="scope.row.type === 1">+</span>
                      <span v-if="scope.row.type === -1">-</span>
                      <span> {{ scope.row.amount }}</span>
                    </p>
                  </template>
                </el-table-column>
                <el-table-column
                  prop="created_at" label="时间" width="140">
                </el-table-column>
                <el-table-column
                  prop="summary"
                  label="备注">
                </el-table-column>
              </el-table>

              <section v-loading.body="isLoading" v-if="isMob">
                <div class="transaction-record"
                  v-for="(ele, index) in tableData" :key="index"
                    @selection-change="handleSelectionChange">
                  <p>交易单号：<span v-if="ele.number">{{ele.number}}</span><span v-else>无</span></p>
                  <p>时间：<span>{{ele.created_at}}</span></p>
                  <p>交易类型：<span>{{ele.transaction_type_value}}</span></p>
                  <p>收入 / 支出：<span>{{ele.amount}}</span><span v-if="ele.type === 1">+</span><span
                    v-if="ele.type === -1">-</span></p>
                  <p class="no-border">备注：<span>{{ele.summary}}</span></p>
                </div>
              </section>
            </article>
            <article v-if="record === 'withdraw'">
              <el-table
                v-if="!isMob"
                key="withdraw"
                :data="WithdrawList"
                style="width: 100%">
                <el-table-column
                  min-width="200"
                  prop="uid"
                  label="交易单号">
                </el-table-column>
                <el-table-column
                  align="center"
                  prop="amount"
                  label="提现金额">
                </el-table-column>
                <el-table-column
                  prop="type_value"
                  label="交易类型">
                </el-table-column>
                <el-table-column
                  prop="account_bank_value"
                  label="银行">
                </el-table-column>
                <el-table-column
                  align="center"
                  prop="account_number"
                  label="卡号尾数">
                </el-table-column>
                <el-table-column
                  min-width="140"
                  prop="created_at"
                  label="时间">
                </el-table-column>
                <el-table-column
                  align="center"
                  prop="status_value"
                  label="交易状态">
                </el-table-column>
              </el-table>
              <section v-loading.body="isLoading" v-if="isMob">
                <div class="transaction-record"
                  v-for="(ele, index) in WithdrawList" :key="index"
                    @selection-change="handleSelectionChange">
                  <p>交易单号：<span v-if="ele.uid">{{ele.uid}}</span><span v-else>无</span></p>
                  <p>时间：<span>{{ele.created_at}}</span></p>
                  <p>提现金额: <span>{{ele.amount}}</span></p>
                  <p>交易类型：<span>{{ele.type_value}}</span></p>
                  <p>银行 <span>{{ele.account_bank_value}}</span></p>
                  <p>卡号尾数: <span>{{ele.account_number}}</span></p>
                  <p class="no-border">交易状态：<span>{{ele.status_value}}</span></p>
                </div>
              </section>
            </article>

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

    <!--弹框模板-->
    <el-dialog :title="itemModelTitle" v-model="itemModel" class="withdraw">

      <div class="withdraw-input">
        <p class="withdraw-title">选择银行卡</p>
        <el-select v-model.number="bankId" placeholder="选择银行卡">
          <el-option
            v-for="(item, index) in bankOptions"
            :label="item.label"
            :key="index"
            :value="item.value">
          </el-option>
        </el-select>
      </div>

      <div class="withdraw-input">
        <p class="withdraw-title">提现金额</p>
        <el-input placeholder="提现额度" v-model.number="withdrawPrice">
          <template slot="prepend">¥</template>
        </el-input>
        <p class="withdraw-des">可提现金额: ¥ {{ wallet.price }} <a href="javascript:void(0)" @click="allPrice">全部提现</a></p>
      </div>

      <div slot="footer" class="dialog-footer">
        <el-button @click="itemModel = false">取 消</el-button>
        <el-button type="primary" :loading="isLoadingBtn" @click="withdrawSubmit">确 定</el-button>
      </div>
    </el-dialog>


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
        isLoadingBtn: false,
        itemModel: false,
        itemModelTitle: '提现操作',
        sureDialog: false,
        sureDialogMessage: '确认执行此操作？',
        sureDialogLoadingBtn: false,
        wallet: {},
        tableData: [],
        itemList: [],
        WithdrawList: [],
        withdrawPrice: '',
        bankId: '',
        bankOptions: [],
        query: {
          page: 1,
          pageSize: 10,
          totalCount: 0,
          sort: 1,
          type: 0,
          payType: 0,
          test: null
        },
        userId: this.$store.state.event.user.id,
        record: 'transaction' // transaction || withdraw
      }
    },
    methods: {
      loadList() {
        const self = this
        self.query.page = parseInt (this.$route.query.page || 1)
        self.query.sort = this.$route.query.sort || 1

        self.isLoading = true
        self.$http.get (api.fundLogList, {
          params: {
            page: self.query.page,
            per_page: self.query.pageSize,
            sort: self.query.sort,
            type: self.query.type
          }
        })
          .then (function (response) {
            self.isLoading = false
            self.tableData = []
            if (response.data.meta.status_code === 200) {
              self.itemList = response.data.data
              self.query.totalCount = response.data.meta.pagination.total

              for (let i = 0; i < self.itemList.length; i++) {
                let item = self.itemList[i]
                item['created_at'] = item.created_at.date_format().format('yyyy-MM-dd hh:mm')

                self.tableData.push (item)
              } // endfor
            } else {
              self.$message.error (response.data.meta.message)
            }
          })
          .catch (function (error) {
            self.$message.error (error.message)
            self.isLoading = false
            return false
          })
      },
      getWithdrawList() {
        this.query.page = parseInt (this.$route.query.page || 1)
        this.query.sort = this.$route.query.sort || 1
        this.query.type = this.$route.query.type || 0

        this.isLoading = true
        this.$http.get(api.withdrawList, {params: {
          per_page: this.query.pageSize,
          page: this.query.page
        }}).then((res) => {
          this.isLoading = false
          if (res.data.meta.status_code === 200) {
            this.query.totalCount = res.data.meta.pagination.total
            this.WithdrawList = res.data.data
            for (let i of this.WithdrawList) {
              i.created_at = i.created_at.date_format().format('yyyy-MM-dd hh:mm')
              i.account_number = i.account_number.substring(i.account_number.length - 4)
              switch (i.type) {
                case 1:
                  i.type_value = '银行转账'
                  break
              }
              switch (i.status) {
                case 0:
                  i.status_value = '申请中'
                  break
                case 1:
                  i.status_value = '已同意'
                  break
              }
            }
          } else {
            this.$message.error (res.data.meta.message)
          }
        }).catch((err) => {
          this.isLoading = false
          this.$message.error (err.message)
        })
      },
      handleSelectionChange(val) {
        this.multipleSelection = val
      },
      handleCurrentChange(val) {
        this.query.page = val
        this.$router.push ({name: this.$route.name, query: {page: val}})
      },
      // 提现弹出框
      withdraw() {
        this.wallet.price = this.wallet.price_total - this.wallet.price_frozen
        if (this.wallet.price <= 0) {
          this.$message.error ('没有可提现余额!')
          return false
        }
        this.itemModel = true
        if (this.bankOptions.length === 0) {
          const self = this
          // 银行卡列表
          self.$http.get (api.bank, {})
            .then (function (response) {
              if (response.data.meta.status_code === 200) {
                for (let i = 0; i < response.data.data.length; i++) {
                  let item = response.data.data[i]
                  let newItem = {}
                  let number = item.account_number.substr (item.account_number.length - 4)
                  newItem.label = item.bank_val + '[' + number + ']'
                  newItem.value = item.id
                  if (item.default === 1) {
                    self.bankId = item.id
                  }
                  self.bankOptions.push (newItem)
                } // endfor
              }
            })
            .catch (function (error) {
              self.$message.error (error.message)
            })
        }
      },
      allPrice() {
        this.withdrawPrice = this.wallet.price
      },
      // 提现执行
      withdrawSubmit() {
        const self = this
        if (self.withdrawPrice <= 0) {
          self.$message.error ('请输入正确的金额!')
          return
        }
        if (self.withdrawPrice > self.wallet.price) {
          self.$message.error ('提现金额超出范围!')
          return
        }
        self.isLoadingBtn = true
        self.$http.post (api.withdrawCreate, {bank_id: self.bankId, amount: self.withdrawPrice})
          .then (function (response) {
            self.isLoadingBtn = false
            if (response.data.meta.status_code === 200) {
              self.itemModel = false
              self.$message.success ('操作成功,等待财务打款！')
            } else {
        //              console.log(response.data.meta.message)
            }
          })
          .catch (function (error) {
            self.isLoadingBtn = false
            self.$message.error (error.message)
          })
      },
      showTransaction() {
        this.record = 'transaction'
        this.loadList()
        this.query.page = 1
        this.$router.push ({name: this.$route.name, query: {page: 1}})
      },
      showWithdraw() {
        this.record = 'withdraw'
        this.getWithdrawList()
        this.query.page = 1
        this.$router.push ({name: this.$route.name, query: {page: 1}})
      }
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      }
    },
    created: function () {
      if (this.isMob) {
        this.query.pageSize = 3
      } else {
        this.query.pageSize = 10
      }
      const self = this
      // 获取我的钱包
      self.walletLoading = true
      self.$http.get (api.authFundInfo, {})
        .then (function (response) {
          self.walletLoading = false
          if (response.data.meta.status_code === 200) {
            let wallet = response.data.data
            if (wallet) {
              self.wallet = wallet
            }
            //            console.log(self.wallet)
          }
        })
        .catch (function (error) {
          self.$message.error (error.message)
          self.walletLoading = false
          return false
        })

      // 交易记录
      this.loadList ()
    },
    watch: {
      '$route' (to, from) {
        // 对路由变化作出响应...
        if (this.record === 'transaction') {
          this.loadList()
        } else {
          this.getWithdrawList()
        }
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

  .my-wallet-m {
    border: 1px solid #e6e6e6;
    height: auto;
    margin-top: 20px;
  }

  .wallet-box {

  }

  .amount-show {
    margin: 30px 0 15px 30px;
    float: left;
  }

  .amount-show-m {
    float: none !important;
    margin: 0
  }

  .amount-show-m p {
    text-align: center;
  }

  .amount-show p {
    line-height: 2;
  }

  .price-title {
    color: #333;
  }

  .price-title-m {
    padding-top: 11px;
    font-size: 15px !important;
    line-height: 22px;
  }

  .price-text {
    color: #FF5A5F;
    font-size: 2rem;
  }

  .price-text-m {
    font-size: 2.8rem;
    line-height: 1 !important;
    margin: 20px 0;
  }

  .price-des {
    font-size: 1.2rem;
    color: #666;
  }

  .price-des-m {
    font-size: 1.4rem;
  }

  .amount-btn {
    float: right;
    margin: 30px;
  }

  .amount-btn-m {
    margin: 0;

  }

  .amount-btn-m p {
    text-align: center;
  }

  .amount-btn button {
    padding: 8px 25px;
  }

  .amount-btn-m button {
    margin-top: 20px;
    width: 40%;
  }

  .bank-box {
    clear: both;
    margin: 15px 30px 10px 30px;
    border-top: 1px solid #ccc;
  }

  .bank-box-m {
    text-align: center;
    border-top: none;
  }

  .bank-box p {
    line-height: 3.5;
    color: #222;
    font-size: 1.3rem;
  }

  .bank-box-m a i {
    margin-right: 8px;
  }

  .item-box {
    margin: 20px 0 0 0;
  }

  .item-box-m {
    margin: 0;
  }

  .item-box h3 {
    font-size: 1.5rem;
    color: #666;
    line-height: 2;
  }

  .item-box-m h3 {
    padding: 5px 0 15px;
    text-align: center;
    color: #222;
    line-height: 1;
  }

  .withdraw-input {
    margin: 10px;
  }

  .withdraw-input p.withdraw-title {
    line-height: 2;
    color: #222;
  }

  .withdraw-input .el-input {
    width: 150px;
  }

  .withdraw-input p.withdraw-des {
    border-top: 1px solid #ccc;
    line-height: 2;
    margin-top: 20px;
    font-size: 1.3rem;
    color: #666;

  }

  .transaction-record {
    border: 1px solid #e6e6e6;
    border-radius: 5px;
    padding: 0 15px;
    margin-bottom: 20px;
  }

  .transaction-record p {
    line-height: 30px;
    font-size: 15px;
    overflow: hidden;
    padding: 6px 2px;
    border-bottom: 1px solid rgba(216, 216, 216, 0.5);
  }

  .transaction-record p span {
    color: #666;
    float: right;
  }

  .pagination {
    display: flex;
    justify-content: center;
  }

  .transaction-record .no-border {
    border: none;
  }
  .data-record span {
    cursor: pointer;
  }
  .active {
    color: #222;
    font-weight: 600;
  }
</style>
