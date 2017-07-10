<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <v-menu selectedName="companyList"></v-menu>

      <el-col :span="20">
        <div class="content">

        <div class="admin-menu-sub">
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminCompanyList'}" active-class="false" :class="{'item': true, 'is-active': menuType == 0}">全部</router-link>
          </div>
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminCompanyList', query: {type: -1}}" :class="{'item': true, 'is-active': menuType === -1}" active-class="false">待审核</router-link>
          </div>
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminCompanyList', query: {type: 1}}" :class="{'item': true, 'is-active': menuType === 1}" active-class="false">通过审核</router-link>
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
              label="Logo"
              width="80">
                <template scope="scope">
                  <p><img :src="scope.row.logo_url" width="50" /></p>
                </template>
            </el-table-column>
            <el-table-column
              label="内容"
              min-width="180">
                <template scope="scope">
                  <p>全称: <router-link :to="{name: 'companyShow', params: {id: scope.row.id}}" target="_blank">{{ scope.row.company_name }}</router-link></p>
                  <p>简称: {{ scope.row.company_abbreviation }}</p>
                  <p>网址: {{ scope.row.web }}</p>
                  <p>类型: {{ scope.row.company_type_val }}</p>
                  <p>规模: {{ scope.row.company_size_val }}</p>
                  <p>地址: {{ scope.row.province_value }} {{ scope.row.city_value }}</p>
                </template>
            </el-table-column>
            <el-table-column
              label="创建人">
                <template scope="scope">
                  <p>
                    {{ scope.row.users.account }}[{{ scope.row.user_id }}]
                  </p>
                </template>
            </el-table-column>
            <el-table-column
              prop="verify_status"
              label="是否审核">
                <template scope="scope">
                  <p v-if="scope.row.verify_status === 1"><el-tag type="success">是</el-tag></p>
                  <p v-else><el-tag type="gray">否</el-tag></p>
                </template>
            </el-table-column>
            <el-table-column
              label="状态">
                <template scope="scope">
                  <p v-if="scope.row.status === 1"><el-tag type="success">正常</el-tag></p>
                  <p v-else><el-tag type="gray">禁用</el-tag></p>
                </template>
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
                    <a href="javascript:void(0);" v-if="scope.row.verify_status === 1" @click="setVerify(scope.$index, scope.row, 0)">取消审核</a>
                    <a href="javascript:void(0);" v-else @click="setVerify(scope.$index, scope.row, 1)">通过审核</a>
                    <a href="javascript:void(0);" v-if="scope.row.status === 1" @click="setStatus(scope.$index, scope.row, 0)">禁用</a>
                    <a href="javascript:void(0);" v-else @click="setStatus(scope.$index, scope.row, 1)">启用</a>
                  </p>
                  <!--
                  <p>
                    <a href="javascript:void(0);" @click="handleEdit(scope.$index, scope.row.id)">编辑</a>
                    <a href="javascript:void(0);" @click="handleDelete(scope.$index, scope.row.id)">删除</a>
                  </p>
                  -->
                  <p>
                    <router-link :to="{name: 'adminCompanyShow', params: {id: scope.row.id}}" target="_blank">查看</router-link>
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

        test: null
      },
      msg: ''
    }
  },
  methods: {
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
    },
    setVerify(index, item, evt) {
      var id = item.id
      var url = ''
      if (evt === 0) {
        url = api.adminCompanyVerifyCancel
      } else {
        url = api.adminCompanyVerifyOk
      }
      var self = this
      self.$http.put(url, {id: id})
      .then (function(response) {
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
      self.query.page = parseInt(this.$route.query.page || 1)
      self.query.sort = this.$route.query.sort || 1
      self.query.type = this.$route.query.type || 0
      this.menuType = 0
      if (self.query.type) {
        this.menuType = parseInt(self.query.type)
      }
      self.isLoading = true
      self.$http.get(api.adminCompanyList, {params: {page: self.query.page, per_page: self.query.pageSize, sort: self.query.sort, type: self.query.type}})
      .then (function(response) {
        self.isLoading = false
        self.tableData = []
        if (response.data.meta.status_code === 200) {
          self.itemList = response.data.data
          self.query.totalCount = parseInt(response.data.meta.pagination.total)

          for (var i = 0; i < self.itemList.length; i++) {
            var item = self.itemList[i]
            item.logo_url = ''
            if (item.logo_image) {
              item.logo_url = item.logo_image.logo
            }
            item['created_at'] = item.created_at.date_format().format('yy-MM-dd')
            self.tableData.push(item)
          } // endfor

          console.log(self.itemList)
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


</style>
