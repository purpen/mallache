<template>
  <div class="container">

    <el-row :gutter="24">
      <v-menu selectedName="companyList"></v-menu>

      <el-col :span="20">
        <div class="content">

        <div class="admin-menu-sub">
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminCompanyList'}" active-class="false" :class="{'item': true, 'is-active': menuType == 0}">全部</router-link>
          </div>
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminCompanyList', query: {type: 1}}" :class="{'item': true, 'is-active': menuType === 1}" active-class="false">待审核</router-link>
          </div>
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminCompanyList', query: {type: 8}}" :class="{'item': true, 'is-active': menuType === 8}" active-class="false">通过审核</router-link>
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
              label="名称"
              width="180">
                <template scope="scope">
                  <p>全称: {{ scope.row.company_abbreviation }}</p>
                  <p>简称: {{ scope.row.company_name }}</p>
                  <p>网址: {{ scope.row.web }}</p>
                </template>
            </el-table-column>
            <el-table-column
              prop="user_id"
              label="创建人">
            </el-table-column>
            <el-table-column
              prop="company_type_val"
              label="类型">
            </el-table-column>
            <el-table-column
              prop="company_size_val"
              label="规模">
            </el-table-column>
            <el-table-column
              label="地点">
                <template scope="scope">
                  <p>省份: {{ scope.row.province }}</p>
                  <p>城市: {{ scope.row.city }}</p>
                  <p>地址: {{ scope.row.address }}</p>
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
                  <p>
                    <a href="javascript:void(0);" @click="handleEdit(scope.$index, scope.row.id)">编辑</a>
                    <a href="javascript:void(0);" @click="handleDelete(scope.$index, scope.row.id)">删除</a>
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


  </div>
</template>

<script>
import api from '@/api/api'
import vMenu from '@/components/admin/Menu'
export default {
  name: 'admin_item_list',
  components: {
    vMenu
  },
  data () {
    return {
      menuType: 0,
      page: 1,
      total: 1000,
      itemList: [],
      tableData: [],
      msg: ''
    }
  },
  methods: {
    handleSelectionChange(val) {
      this.multipleSelection = val
    },
    handleSizeChange(val) {
      console.log(`每页 ${val} 条`)
    },
    handleCurrentChange(val) {
      console.log(`当前页: ${val}`)
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
    }
  },
  created: function() {
    const self = this
    var page = this.$route.query.page || 1
    var perPage = 5
    self.$http.get(api.adminCompanyList, {page: page, per_page: perPage})
    .then (function(response) {
      if (response.data.meta.status_code === 200) {
        self.itemList = response.data.data

        for (var i = 0; i < self.itemList.length; i++) {
          var item = self.itemList[i]
          item.logo_url = ''
          if (item.logo_image && item.logo_image.length !== 0) {
            item.logo_url = item.logo_image[0]['logo']
          }
          self.tableData.push(item)
        } // endfor

        console.log(self.itemList)
      }
    })
    .catch (function(error) {
      self.$message.error(error.message)
      console.log(error.message)
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


</style>
