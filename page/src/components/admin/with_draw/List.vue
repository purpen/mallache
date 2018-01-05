<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <v-menu selectedName="withDrawList"></v-menu>

      <el-col :span="20">
        <div class="content">

        <div class="admin-menu-sub">
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminWithDrawList'}" active-class="false" :class="{'item': true, 'is-active': menuType === 0}">待审核</router-link>
            <router-link :to="{name: 'adminWithDrawList', query: {status: 1}}" active-class="false" :class="{'item': true, 'is-active': menuType === 1}">已通过</router-link>
          </div>
        </div>

          <el-table
            :data="tableData"
            border
            v-loading.body="isLoading"
            class="admin-table"
            @selection-change="handleSelectionChange"
            style="width: 100%">
            <el-table-column
              type="selection"
              width="55">
            </el-table-column>
            <el-table-column
              prop="id"
              label="ID"
              width="60">
            </el-table-column>
            <el-table-column
              label="银行卡信息"
              min-width="200">
                <template slot-scope="scope">
                  <p>
                    类型：{{ scope.row.account_bank_value }}
                  </p>
                  <p>
                    卡号：{{ scope.row.account_number }}
                  </p>
                  <p>
                    姓名：{{ scope.row.account_name }}
                  </p>
                  <p>
                    开户行：{{ scope.row.branch_name }}
                  </p>
                </template>
            </el-table-column>
            <el-table-column
              prop="amount"
              label="提现金额"
              width="80">
            </el-table-column>
            <el-table-column
              width="60"
              label="创建人">
                <template slot-scope="scope">
                  <p>
                    {{ scope.row.user_id }}
                  </p>
                </template>
            </el-table-column>
            <el-table-column
              prop="type_label"
              label="支付类型"
              width="100">
            </el-table-column>
            <el-table-column
              prop="summary"
              label="备注"
              min-width="150">
            </el-table-column>
            <el-table-column
              prop="status_label"
              width="80"
              label="状态">
            </el-table-column>
            <el-table-column
              prop="created_at"
              width="80"
              label="创建时间">
            </el-table-column>
            <el-table-column
              width="100"
              label="操作">
                <template slot-scope="scope">
                  <p>
                    <a href="javascript:void(0);" v-show="scope.row.status === 0" @click="sureTransfer(scope.$index, scope.row)">确认打款</a>
                  </p>
                  <p>
                  </p>
                </template>
            </el-table-column>
          </el-table>

          <el-pagination
            class="pagination"
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
            :current-page="query.page"
            :page-sizes="[50, 100, 500]"
            :page-size="query.pageSize"
            layout="total, sizes, prev, pager, next, jumper"
            :total="query.totalCount">
          </el-pagination>

        </div>
      </el-col>
    </el-row>

    <el-dialog title="确认线下已打款" v-model="sureTransferDialog">
      <el-form label-position="top">
        <el-form-item label="银行类型" label-width="200px">
          <el-input v-model="withDrawForm.bankName" disabled></el-input>
        </el-form-item>
        <el-form-item label="卡号" label-width="200px">
          <el-input v-model="withDrawForm.bankNumber" auto-complete="off" disabled></el-input>
        </el-form-item>
        <el-form-item label="开卡人姓名" label-width="200px">
          <el-input v-model="withDrawForm.bankUser" auto-complete="off" disabled></el-input>
        </el-form-item>

        <el-form-item label="备注" label-width="80px">
          <el-input type="textarea" rows="3" v-model="withDrawForm.summary" placeholder="请添写交易单号及相关说明" auto-complete="off"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="sureTransferDialog = false">取 消</el-button>
        <el-button type="primary" :load="sureTransferLoading" @click="sureTransferSubmit">确 定</el-button>
      </div>
    </el-dialog>

  </div>
</template>

<script>
import api from '@/api/api'
import vMenu from '@/components/admin/Menu'
export default {
  name: 'admin_with_draw_list',
  components: {
    vMenu
  },
  data () {
    return {
      menuType: 0,
      sureTransferDialog: false,
      sureTransferLoading: false,
      itemList: [],
      tableData: [],
      isLoading: false,
      withDrawForm: {
        index: '',
        id: '',
        summary: ''
      },
      query: {
        page: 1,
        pageSize: 50,
        totalCount: 0,
        sort: 1,
        status: 0,

        test: null
      },
      msg: ''
    }
  },
  methods: {
    handleSelectionChange(val) {
      this.multipleSelection = val
    },
    handleEdit() {
    },
    handleDelete() {
    },
    // 对公打款弹层
    sureTransfer(index, item) {
      this.withDrawForm.index = index
      this.withDrawForm.id = item.id
      this.withDrawForm.bankName = item.account_bank_value
      this.withDrawForm.bankNumber = item.account_number
      this.withDrawForm.bankUser = item.account_name
      this.sureTransferDialog = true
    },
    // 确认对公打款
    sureTransferSubmit() {
      if (!this.withDrawForm.id || !this.withDrawForm.summary) {
        this.$message.error('缺少请求参数!')
        return
      }
      var self = this
      self.sureTransferLoading = true
      this.$http.post(api.adminWithDrawTruePay, {withdraw_order_id: this.withDrawForm.id, summary: this.withDrawForm.summary})
      .then (function(response) {
        self.sureTransferLoading = false
        if (response.data.meta.status_code === 200) {
          self.$message.success('操作成功！')
          self.sureTransferDialog = false
          self.tableData[self.withDrawForm.index]['status'] = 1
          self.tableData[self.withDrawForm.index]['status_label'] = '已完成'
        } else {
          self.$message.error(response.data.meta.message)
          return
        }
      })
      .catch (function(error) {
        self.$message.error(error.message)
        self.sureTransferLoading = false
        console.log(error.message)
      })
    },
    handleSizeChange(val) {
      this.query.pageSize = val
      this.loadList()
    },
    handleCurrentChange(val) {
      this.query.page = val
      this.$router.push({name: this.$route.name, query: {page: val}})
    },
    loadList() {
      const self = this
      self.query.page = this.$route.query.page || 1
      self.query.sort = this.$route.query.sort || 1
      self.query.status = this.$route.query.status || 0
      this.menuType = 0
      if (self.query.status) {
        this.menuType = parseInt(self.query.status)
      }
      self.isLoading = true
      self.$http.get(api.adminWithDrawLists, {params: {page: self.query.page, per_page: self.query.pageSize, sort: self.query.sort, status: self.query.status}})
      .then (function(response) {
        self.isLoading = false
        self.tableData = []
        if (response.data.meta.status_code === 200) {
          self.itemList = response.data.data
          self.query.totalCount = response.data.meta.pagination.total
          for (var i = 0; i < self.itemList.length; i++) {
            var item = self.itemList[i]
            var typeVal, statusVal
            if (item.type === 1) {
              typeVal = '银行转账'
            } else {
              typeVal = ''
            }

            if (item.status === 0) {
              statusVal = '审核中'
            } else if (item.status) {
              statusVal = '已完成'
            } else {
              statusVal = ''
            }
            item['type_label'] = typeVal
            item['status_label'] = statusVal
            item['created_at'] = item.created_at.date_format().format('yy-MM-dd')

            self.tableData.push(item)
          } // endfor

          console.log(self.itemList)
        } else {
          self.$message.error(response.data.meta.message)
        }
      })
      .catch (function(error) {
        self.isLoading = false
        self.$message.error(error.message)
      })
    }
  },
  computed: {
  },
  created: function() {
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

  .match-company-box {
    margin: 10px;
  }
  .match-company-box p {
    line-height: 2;
  }
  .match-company-tag {
    margin: 5px;
  }

  .el-form-item {
    margin: 5px 0;
  }

</style>
