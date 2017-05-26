<template>
  <div class="container">

    <el-row :gutter="24">
      <v-menu selectedName="orderList"></v-menu>

      <el-col :span="20">
        <div class="content">

        <div class="admin-menu-sub">
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminOrderList'}" active-class="false" :class="{'item': true, 'is-active': menuType === 0}">全部</router-link>
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
              prop="item_name"
              label="项目名称"
              width="140">
            </el-table-column>
            <el-table-column
              prop="amount"
              label="支付金额"
              width="80">
            </el-table-column>
            <el-table-column
              width="140"
              label="创建人">
                <template scope="scope">
                  <p>
                    {{ scope.row.user.account }}[{{ scope.row.user_id }}]
                  </p>
                </template>
            </el-table-column>
            <el-table-column
              prop="type_value"
              label="支付类型"
              width="100">
            </el-table-column>
            <el-table-column
              prop="pay_type_value"
              label="支付方式"
              width="100">
            </el-table-column>
            <el-table-column
              prop="status_value"
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
                <template scope="scope">
                  <p>
                    <a href="javascript:void(0);" v-show="scope.row.sure_outline_transfer" @click="sureTransfer(scope.$index, scope.row)">确认收款</a>
                  </p>
                  <p>
                  <!--
                    <a href="javascript:void(0);" @click="handleEdit(scope.$index, scope.row.id)">编辑</a>
                    <a href="javascript:void(0);" @click="handleDelete(scope.$index, scope.row.id)">删除</a>                 
                    <a href="javascript:void(0);" @click="handleDelete(scope.$index, scope.row.id)">查看</a> 

                    -->
                  </p>
                </template>
            </el-table-column>
          </el-table>

          <el-pagination
            class="pagination"
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
            :current-page="query.page"
            :page-sizes="[10, 50, 100, 500]"
            :page-size="query.pageSize"
            layout="total, sizes, prev, pager, next, jumper"
            :total="query.totalCount">
          </el-pagination>

        </div>
      </el-col>
    </el-row>

    <el-dialog title="确认线下打款" v-model="sureTransferDialog">
      <el-form label-position="top">
        <input type="hidden" v-model="orderForm.orderId" value="" />
        <input type="hidden" v-model.number="orderForm.index" value="" />
        <el-form-item label="项目名称" label-width="200px">
          <el-input v-model="orderForm.itemName" auto-complete="off" disabled></el-input>
        </el-form-item>
        <el-form-item label="订单金额" label-width="200px">
          <el-input v-model="orderForm.amount" auto-complete="off" disabled></el-input>
        </el-form-item>
        <el-form-item label="所属银行" prop="bandId">
          <el-select v-model.number="orderForm.bankId" placeholder="请选择银行">
            <el-option
              v-for="(item, index) in bankOptions"
              :label="item.label"
              :key="index"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>

        <el-form-item label="交易单号" label-width="80px">
          <el-input v-model="orderForm.payNo" placeholder="交易单号" auto-complete="off"></el-input>
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
import typeData from '@/config'
export default {
  name: 'admin_order_list',
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
      orderForm: {
        index: '',
        orderId: '',
        itemName: '',
        amount: '',
        bankId: '',
        payNo: ''
      },
      query: {
        page: 1,
        pageSize: 10,
        totalCount: 0,
        sort: 1,
        type: 0,
        payType: 0,

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
      this.orderForm.index = index
      this.orderForm.orderId = item.id
      this.orderForm.itemName = item.item_name
      this.orderForm.amount = item.amount
      this.sureTransferDialog = true
    },
    // 确认对公打款
    sureTransferSubmit() {
      if (!this.orderForm.orderId || !this.orderForm.itemName || !this.orderForm.bankId || !this.orderForm.payNo || !this.orderForm.index) {
        this.$message.error('缺少请求参数!')
        return
      }
      var self = this
      self.sureTransferLoading = true
      this.$http.post(api.adminPayOrderTruePay, {pay_order_id: this.orderForm.orderId, bank_id: this.orderForm.bankId, pay_no: this.orderForm.payNo})
      .then (function(response) {
        self.sureTransferLoading = false
        if (response.data.meta.status_code === 200) {
          self.$message.success('操作成功！')
          self.sureTransferDialog = false
          self.tableData[self.orderForm.index]['status'] = 1
          self.tableData[self.orderForm.index]['status_value'] = '支付完成'
          self.tableData[self.orderForm.index]['sure_outline_transfer'] = false
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
      self.query.type = this.$route.query.type || 0
      self.payType = this.$route.query.pay_type || 0
      self.isLoading = true
      self.$http.get(api.adminPayOrderLists, {params: {page: self.query.page, per_page: self.query.pageSize, sort: self.query.sort, type: self.query.type, pay_type: self.query.payType}})
      .then (function(response) {
        self.isLoading = false
        self.tableData = []
        if (response.data.meta.status_code === 200) {
          self.itemList = response.data.data
          self.query.totalCount = response.data.meta.pagination.total
          for (var i = 0; i < self.itemList.length; i++) {
            var item = self.itemList[i]
            var typeValue = ''
            if (item.type === 1) {
              typeValue = '预付押金'
            } else if (item.type === 2) {
              typeValue = '项目尾款'
            }
            item['type_value'] = typeValue
            item['created_at'] = item.created_at.date_format().format('yy-MM-dd')
            var sureOutlineTransfer = false
            if (item.pay_type === 5 && item.status === 0 && item.type === 2) {
              sureOutlineTransfer = true
            }
            item['sure_outline_transfer'] = sureOutlineTransfer

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
    bankOptions() {
      var items = []
      for (var i = 0; i < typeData.BANK_OPTIONS.length; i++) {
        var item = {
          value: typeData.BANK_OPTIONS[i]['id'],
          label: typeData.BANK_OPTIONS[i]['name']
        }
        items.push(item)
      }
      return items
    }
  },
  created: function() {
    this.loadList()
  },
  watch: {
    '$route' (to, from) {
      // 对路由变化作出响应...
      var type = this.$route.query.type
      this.menuType = 0
      if (type) {
        this.menuType = parseInt(type)
      }
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

</style>
