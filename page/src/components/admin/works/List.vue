<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <v-menu selectedName="worksList"></v-menu>

      <el-col :span="20">
        <div class="content">

        <div class="admin-menu-sub">
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminWorksList'}" active-class="false" :class="{'item': true, 'is-active': menuType == 0}">全部</router-link>
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
              label="封面"
              width="70">
                <template scope="scope">
                  <p><img :src="scope.row.cover_url" width="50" /></p>
                </template>
            </el-table-column>
            <el-table-column
              label="内容"
              min-width="200">
                <template scope="scope">
                  <p>标题: <router-link :to="{name: 'vcenterMatchCaseShow', params: {id: scope.row.id}}" target="_blank">{{ scope.row.title }}</router-link></p>
                </template>
            </el-table-column>
            <el-table-column
              prop="match_id"
              label="大赛ID"
              width="60">
            </el-table-column>
            <el-table-column
              label="创建人">
                <template scope="scope">
                  <p>
                    {{ scope.row.user_id }}
                  </p>
                </template>
            </el-table-column>
            <el-table-column
              label="所属公司">
                <template scope="scope">
                  <p>
                    <router-link :to="{name: 'companyShow', params: {id: scope.row.company.id}}" target="_blank">{{ scope.row.company.company_name }}</router-link>
                  </p>
                </template>
            </el-table-column>

            <el-table-column
              prop="published"
              label="是否发布">
                <template scope="scope">
                  <p v-if="scope.row.published === 1"><el-tag type="success">是</el-tag></p>
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
                    <a href="javascript:void(0);" v-if="scope.row.published === 1" @click="setPublish(scope.$index, scope.row, 0)">取消发布</a>
                    <a href="javascript:void(0);" v-else @click="setPublish(scope.$index, scope.row, 1)">发布</a>
                    <a href="javascript:void(0);" v-if="scope.row.status === 1" @click="setStatus(scope.$index, scope.row, 0)">禁用</a>
                    <a href="javascript:void(0);" v-else @click="setStatus(scope.$index, scope.row, 1)">启用</a>
                  </p>
                  <p>
                    <!--<a href="javascript:void(0);" @click="handleEdit(scope.$index, scope.row.id)">编辑</a>-->
                    <a href="javascript:void(0);" @click="removeBtn(scope.$index, scope.row)">删除</a>
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

    <el-dialog
      title="提示"
      v-model="sureDialog"
      size="tiny">
      <span>确认执行此操作?</span>
      <span slot="footer" class="dialog-footer">
        <el-button @click="sureDialog = false">取 消</el-button>
        <el-button type="primary" :loading="dialogLoadingBtn" @click="sureDialogSubmit">确 定</el-button>
      </span>
    </el-dialog>


  </div>
</template>

<script>
import api from '@/api/api'
import vMenu from '@/components/admin/Menu'
export default {
  name: 'admin_works_list',
  components: {
    vMenu
  },
  data () {
    return {
      menuType: 0,
      itemList: [],
      tableData: [],
      isLoading: false,
      sureDialog: false,
      dialogLoadingBtn: false,
      currentDialogIndex: '',
      currentDialogId: '',
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
    setPublish(index, item, evt) {
      var id = item.id
      var self = this
      self.$http.put(api.adminWorksPublished, {id: id, published: evt})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          self.itemList[index].published = evt
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
      var self = this
      self.$http.put(api.adminWorksStatus, {id: id, status: evt})
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
    // 删除弹层
    removeBtn (index, obj) {
      this.sureDialog = true
      this.currentDialogIndex = index
      this.currentDialogId = obj.id
    },
    // 确认删除操作
    sureDialogSubmit () {
      var self = this
      self.dialogLoadingBtn = true
      self.$http.delete(api.adminWorksDelete, {params: {id: self.currentDialogId}})
      .then (function(response) {
        self.dialogLoadingBtn = false
        if (response.data.meta.status_code === 200) {
          self.tableData.splice(self.currentDialogIndex, 1)
          self.sureDialog = false
        } else {
          self.$message.error(response.data.meta.message)
        }
      })
      .catch (function(error) {
        self.$message.error(error.message)
        self.dialogLoadingBtn = false
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
      self.$http.get(api.adminWorksList, {params: {page: self.query.page, per_page: self.query.pageSize, sort: self.query.sort, type: self.query.type}})
      .then (function(response) {
        self.isLoading = false
        self.tableData = []
        if (response.data.meta.status_code === 200) {
          self.itemList = response.data.data
          console.log(self.itemList)
          self.query.totalCount = parseInt(response.data.meta.pagination.total)

          for (var i = 0; i < self.itemList.length; i++) {
            var item = self.itemList[i]
            item.cover_url = ''
            if (item.cover) {
              item.cover_url = item.cover.small
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


</style>
