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
            class="admin-table"
            @selection-change="handleSelectionChange"
            style="width: 100%">
            <el-table-column
              type="selection"
              width="55">
            </el-table-column>
            <el-table-column
              prop="item.id"
              label="ID"
              width="60">
            </el-table-column>
            <el-table-column
              prop="info.name"
              label="名称"
              width="140">
            </el-table-column>
            <el-table-column
              width="140"
              label="创建人">
                <template scope="scope">
                  <p>
                    {{ scope.row.item.user.account }}[{{ scope.row.item.user_id }}]
                  </p>
                </template>
            </el-table-column>
            <el-table-column
              prop="item.type_label"
              label="类型"
              width="150">
            </el-table-column>
            <el-table-column
              prop="info.design_cost_value"
              label="预算">
            </el-table-column>
            <el-table-column
              prop="info.cycle_value"
              label="周期">
            </el-table-column>
            <el-table-column
              prop="info.locale"
              label="工作地点">
            </el-table-column>
            <el-table-column
              prop="item.status_label"
              width="120"
              label="状态">
            </el-table-column>
            <el-table-column
              prop="item.created_at"
              width="80"
              label="创建时间">
            </el-table-column>
            <el-table-column
              width="100"
              label="操作">
                <template scope="scope">
                  <p>
                    <a href="javascript:void(0);" v-show="scope.row.item.status === 2 || scope.row.item.status === 3" @click="handleMatch(scope.$index, scope.row)">匹配公司</a>
                  </p>
                  <p>
                  <!--
                    <a href="javascript:void(0);" @click="handleEdit(scope.$index, scope.row.item.id)">编辑</a>
                    <a href="javascript:void(0);" @click="handleDelete(scope.$index, scope.row.item.id)">删除</a>                 
                    -->
                    <a href="javascript:void(0);" @click="handleDelete(scope.$index, scope.row.item.id)">查看</a> 
                  </p>
                </template>
            </el-table-column>
          </el-table>

          <el-pagination
            class="pagination"
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
            :current-page="page"
            :page-sizes="[100, 200, 300, 400, 500]"
            :page-size="100"
            layout="total, sizes, prev, pager, next, jumper"
            :total="total">
          </el-pagination>

        </div>
      </el-col>
    </el-row>

    <el-dialog title="匹配公司" v-model="matchCompanyDialog">
      <el-form label-position="top">
        <input type="hidden" v-model="matchCompanyForm.itemId" value="" />
        <el-form-item label="项目名称" label-width="200px">
          <el-input v-model="matchCompanyForm.itemName" auto-complete="off" disabled></el-input>
        </el-form-item>
        <div class="match-company-box">
        <p>已匹配的公司：</p>
        <p><el-tag class="match-company-tag" type="success" v-for="(d, index) in currentMatchCompany" :key="index">{{ d.company_name }}</el-tag></p>
        </div>
        <el-form-item label="添加公司" label-width="80px">
          <el-input v-model="matchCompanyForm.companyIds" placeholder="多个公司ID用','分隔" auto-complete="off"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="matchCompanyDialog = false">取 消</el-button>
        <el-button type="primary" @click="addMatchCompany">确 定</el-button>
      </div>
    </el-dialog>

  </div>
</template>

<script>
import api from '@/api/api'
import vMenu from '@/components/admin/Menu'
export default {
  name: 'admin_order_list',
  components: {
    vMenu
  },
  data () {
    return {
      menuType: 0,
      matchCompanyDialog: false,
      itemList: [],
      tableData: [],
      currentMatchCompany: [],
      page: 1,
      total: 1000,
      matchCompanyForm: {
        itemId: '',
        itemName: '',
        companyIds: ''
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
    handleMatch(index, item) {
      if (item.item.status !== 2) {
        // this.$message.error('项目状态不允许推荐公司')
        // return
      }
      this.currentMatchCompany = item.designCompany
      this.matchCompanyForm.itemId = item.item.id
      this.matchCompanyForm.itemName = item.info.name
      this.matchCompanyDialog = true
    },
    addMatchCompany() {
      if (!this.matchCompanyForm.itemId || !this.matchCompanyForm.itemName || !this.matchCompanyForm.companyIds) {
        this.$message.error('缺少请求参数!')
        return
      }
      var companyIds = this.matchCompanyForm.companyIds.split(',')
      var self = this
      this.$http.post(api.addItemToCompany, {item_id: this.matchCompanyForm.itemId, recommend: companyIds})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          self.$http.post(api.ConfirmItemToCompany, {item_id: self.matchCompanyForm.itemId})
          .then (function(response1) {
            if (response1.data.meta.status_code === 200) {
              self.$message.success('添加成功!')
              self.matchCompanyDialog = false
              return
            } else {
              self.$message.error(response1.data.meta.message)
              return
            }
          })
          .catch (function(error) {
            self.$message.error(error.message)
            console.log(error.message)
          })
        } else {
          self.$message.error(response.data.meta.message)
          return
        }
      })
      .catch (function(error) {
        self.$message.error(error.message)
        console.log(error.message)
      })
    },
    handleSizeChange(val) {
      console.log(`每页 ${val} 条`)
    },
    handleCurrentChange(val) {
      console.log(`当前页: ${val}`)
    }
  },
  created: function() {
    const self = this
    var page = this.$route.query.page || 1
    var sort = this.$route.query.sort || 1
    var type = this.$route.query.type || 0
    var payType = this.$route.query.pay_type || 0
    var perPage = 100
    self.$http.get(api.adminPayOrderLists, {params: {page: page, per_page: perPage, sort: sort, type: type, pay_type: payType}})
    .then (function(response) {
      if (response.data.meta.status_code === 200) {
        self.itemList = response.data.data

        for (var i = 0; i < self.itemList.length; i++) {
          var item = self.itemList[i]

          // item['item']['status_label'] = '[{0}]{1}'.format(item.status, item.item.status_value)

          item['item']['created_at'] = item.created_at.date_format().format('yy-MM-dd')

          self.tableData.push(item)
        } // endfor

        console.log(self.itemList)
      } else {
        self.$message.error(response.data.meta.message)
        // self.$router.push({name: 'home'})
      }
    })
    .catch (function(error) {
      self.$message.error(error.message)
      // self.$router.push({name: 'home'})
    })
  },
  watch: {
    '$route' (to, from) {
      // 对路由变化作出响应...
      var type = this.$route.query.type
      this.menuType = 0
      if (type) {
        this.menuType = parseInt(type)
      }
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
