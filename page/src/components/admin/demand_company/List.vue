<template>
  <div class="container company-verify">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <v-menu selectedName="demandCompanyList"></v-menu>

      <el-col :span="20">
        <div class="content">

          <div class="admin-menu-sub">
            <div class="admin-menu-sub-list">
              <router-link :to="{name: 'adminDemandCompanyList'}" active-class="false" :class="{'item': true, 'is-active': menuType == ''}">全部</router-link>
            </div>
            <div class="admin-menu-sub-list">
              <router-link :to="{name: 'adminDemandCompanyList', query: {type: 1}}" :class="{'item': true, 'is-active': menuType === 1}" active-class="false">通过认证</router-link>
            </div>
            <div class="admin-menu-sub-list">
              <router-link :to="{name: 'adminDemandCompanyList', query: {type: 2}}" :class="{'item': true, 'is-active': menuType === 2}" active-class="false">拒绝认证</router-link>
            </div>
          </div>

          <div class="admin-search-form">
            <el-form :inline="true" :model="query">
              <el-form-item>
                <el-input v-model="query.val" placeholder="Search..." size="small"></el-input>
              </el-form-item>
              <el-form-item>
                <el-select v-model="query.evt" placeholder="选择条件..." size="small">
                  <el-option label="公司ID" value="1"></el-option>
                  <el-option label="公司名称" value="2"></el-option>
                  <el-option label="公司简称" value="3"></el-option>
                  <el-option label="用户ID" value="4"></el-option>
                </el-select>
              </el-form-item>
              <el-form-item>
                <el-button type="primary" @click="onSearch" size="small">查询</el-button>
              </el-form-item>
            </el-form>
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
              align="center"
              prop="id"
              label="ID"
              width="60">
            </el-table-column>
            <el-table-column
              label="Logo"
              width="80">
                <template slot-scope="scope">
                  <p><img :src="scope.row.logo_url" width="50" /></p>
                </template>
            </el-table-column>
            <el-table-column
              label="内容"
              min-width="160">
                <template slot-scope="scope">
                  <p>全称: {{ scope.row.company_name }}</p>
                  <p>简称: {{ scope.row.company_abbreviation }}</p>
                  <p>网址: {{ scope.row.company_web }}</p>
                  <p>规模: {{ scope.row.company_property_value }}</p>
                  <p>地址: {{ scope.row.province_value }} {{ scope.row.city_value }}</p>
                </template>
            </el-table-column>
            <el-table-column
              width="120"
              label="创建人">
                <template slot-scope="scope">
                  <p v-if="scope.row.user">
                    {{ scope.row.user.account }}[{{ scope.row.user_id }}]
                  </p>
                </template>
            </el-table-column>
            <el-table-column
              align="center"
              width="80"
              label="认证状态">
                <template slot-scope="scope">
                  <p v-if="scope.row.verify_status === 0"><el-tag type="gray">未认证</el-tag></p>
                  <p v-if="scope.row.verify_status === 1"><el-tag type="success">通过</el-tag></p>
                  <p v-if="scope.row.verify_status === 2"><el-tag type="danger">失败</el-tag></p>
                  <p v-if="scope.row.verify_status === 3"><el-tag type="warning">待认证</el-tag></p>
                </template>
            </el-table-column>

            <el-table-column
              prop="created_at"
              width="80"
              label="创建时间">
            </el-table-column>
            <el-table-column
              align="center"
              width="100"
              label="操作">
                <template slot-scope="scope">
                <p class="operate">
                  <a @click="setRefuseRease(scope.$index, scope.row, 2)" v-if="scope.row.verify_status === 1 || scope.row.verify_status === 3" class="tag-refuse">拒绝</a>
                  <a v-if="scope.row.verify_status === 2 || scope.row.verify_status === 3" @click="setVerify(scope.$index, scope.row, 1)" class="tag-pass">通过</a>
                  <router-link :to="{name: 'adminDemandCompanyShow', params: {id: scope.row.id}}" target="_blank" class="tag-view">查看</router-link>
                  </p>
                  <!--
                  <p>
                    <a href="javascript:void(0);" @click="handleEdit(scope.$index, scope.row.id)">编辑</a>
                    <a href="javascript:void(0);" @click="handleDelete(scope.$index, scope.row.id)">删除</a>
                  </p>
                  -->
                </template>
            </el-table-column>
          </el-table>

          <el-dialog title="请填写拒绝原因" :visible.sync="dialogVisible" size="tiny">
            <el-input v-model="verify.refuseRease"></el-input>
            <span slot="footer" class="dialog-footer">
              <el-button size="small" @click="dialogVisible = false">取 消</el-button>
              <el-button size="small" type="primary" @click="setVerify(verify.index, verify.item, verify.evt, verify.refuseRease)">确 定</el-button>
            </span>
          </el-dialog>

          <el-pagination
            class="pagination"
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
            :current-page="query.page"
            :page-sizes="[50, 100, 500]"
            :page-size="query.pagesize"
            layout="total, sizes, prev, pager, next, jumper"
            :total="query.totalCount">
          </el-pagination>
        </div>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import api from '@/api/api'
import vMenu from '@/components/admin/Menu'
export default {
  name: 'admin_company_list',
  components: {
    vMenu
  },
  data () {
    return {
      menuType: 0,
      itemList: [],
      tableData: [],
      isLoading: false,
      query: {
        page: 1,
        pageSize: 50,
        totalCount: 0,
        sort: 1,
        type: 0,
        evt: '',
        val: '',
        test: null
      },
      verify: {
        index: '',
        item: '',
        evt: '',
        refuseRease: ''
      },
      dialogVisible: false,
      msg: ''
    }
  },
  methods: {
    // 查询
    onSearch() {
      this.query.page = 1
      this.$router.push({name: this.$route.name, query: this.query})
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
      this.$router.push({name: this.$route.name, query: this.query})
    },
    setRefuseRease (index, item, evt) {
      this.dialogVisible = !this.dialogVisible
      this.verify.index = index
      this.verify.item = item
      this.verify.evt = evt
    },
    setVerify(index, item, evt, refuseRease = '') {
      this.dialogVisible = false
      var id = item.id
      var self = this
      self.$http.put(api.adminDemandCompanyVerifyOk, {id: id, status: evt, verify_summary: refuseRease})
      .then (function(response) {
        self.verify.refuseRease = ''
        if (response.data.meta.status_code === 200) {
          self.itemList[index].verify_status = evt
          self.$message.success('操作成功')
        } else {
          self.$message.error(response.meta.message)
        }
      })
      .catch (function(error) {
        self.$message.error(error.message)
        console.log(error.message)
      })
    },
    setStatus(index, item, evt) {
      var id = item.id
      var url = ''
      if (evt === 0) {
        url = api.adminCompanyStatusDisable
      } else {
        url = api.adminCompanyStatusOk
      }
      var self = this
      self.$http.put(url, {id: id})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          self.itemList[index].status = evt
          self.$message.success('操作成功')
        } else {
          self.$message.error(response.meta.message)
        }
      })
      .catch (function(error) {
        self.$message.error(error.message)
        console.log(error.message)
      })
    },
    loadList() {
      const self = this
      self.query.page = this.$route.query.page || 1
      self.query.sort = this.$route.query.sort || 0
      self.query.type = this.$route.query.type || ''
      self.query.evt = this.$route.query.evt || ''
      self.query.val = this.$route.query.val || ''
      this.menuType = 0
      if (this.$route.query.type) {
        this.menuType = parseInt(this.$route.query.type)
      }
      self.isLoading = true
      self.$http.get(api.adminDemandCompanyList, {params: {page: self.query.page, per_page: self.query.pageSize, sort: self.query.sort, type_verify_status: self.query.type, evt: self.query.evt, val: self.query.val}})
      .then (function(response) {
        self.isLoading = false
        self.tableData = []
        if (response.data.meta.status_code === 200) {
          self.itemList = response.data.data
          self.query.totalCount = response.data.meta.pagination.total
          for (var i = 0; i < self.itemList.length; i++) {
            var item = self.itemList[i]
            item.logo_url = require ('@/assets/images/df_100x100.png')
            if (item.logo_image) {
              item.logo_url = item.logo_image.logo
            }
            item['created_at'] = item.created_at.date_format().format('yy-MM-dd')
            self.tableData.push(item)
          } // endfor
        } else {
          self.$message.error(response.data.meta.message)
        }
      })
      .catch (function(error) {
        self.$message.error(error.message)
        self.isLoading = false
      })
    }
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
  .operate a {
    cursor: pointer;
    display: block;
    margin-bottom: 8px;
    border-radius: 4px;
  }
</style>
