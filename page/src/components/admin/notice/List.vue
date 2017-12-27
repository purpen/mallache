<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <v-menu selectedName="noticeList"></v-menu>

      <el-col :span="20">
        <div class="content">

        <div class="admin-menu-sub">
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminNoticeList'}" active-class="false" :class="{'item': true, 'is-active': menuType == 0}">全部</router-link>
          </div>
          <div class="fr">
            <router-link :to="{name: 'adminNoticeAdd'}" class="item add"><i class="el-icon-plus"></i> 添加</router-link>
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
              width="90">
                <template scope="scope">
                  <p><img :src="scope.row.cover_url" width="60" style="margin: 5px;" /></p>
                </template>
            </el-table-column>
            <el-table-column
              label="信息"
              min-width="180">
                <template scope="scope">
                  <p>标题: {{ scope.row.title }}</p>
                  <p>链接: {{ scope.row.url }}</p>
                </template>
            </el-table-column>
            <el-table-column
              width="80"
              label="目标人群">
                <template scope="scope">
                  <p>{{ scope.row.evt_value }}</p>
                </template>
            </el-table-column>
            <el-table-column
              prop="user_id"
              label="用户ID"
              width="60">
            </el-table-column>
            <el-table-column
              width="70"
              label="状态">
                <template scope="scope">
                  <p v-if="scope.row.status === 0"><el-tag type="gray">禁用</el-tag></p>
                  <p v-else-if="scope.row.status === -1"><el-tag type="warning">发送中...</el-tag></p>
                  <p v-else><el-tag type="success">已发送</el-tag></p>
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
                    <a href="javascript:void(0);" v-if="scope.row.status === 1" @click="setStatus(scope.$index, scope.row, 0)">禁用</a>
                    <a href="javascript:void(0);" v-else-if="scope.row.status === -1">发送中...</a>
                    <a href="javascript:void(0);" v-else @click="setStatus(scope.$index, scope.row, -1)">发送</a>
                  </p>
                  <p>
                    <router-link :to="{name: 'adminNoticeEdit', params: {id: scope.row.id}}">编辑</router-link>
                    <a href="javascript:void(0)" @click="removeBtn(scope.$index, scope.row)">删除</a>
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
            :page-size="query.pagesize"
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
  name: 'admin_notice_list',
  components: {
    vMenu
  },
  data () {
    return {
      menuType: 0,
      itemList: [],
      tableData: [],
      isLoading: false,
      setRoleDialog: false,
      currentAccount: '',
      roleId: 0,
      sureDialog: false,
      dialogLoadingBtn: false,
      currentDialogIndex: '',
      currentDialogId: '',
      query: {
        page: 1,
        pageSize: 10,
        totalCount: 0,
        sort: 1,
        type: 0,
        evt: 0,
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
    handleSizeChange(val) {
      this.query.pageSize = val
      this.loadList()
    },
    handleCurrentChange(val) {
      this.query.page = val
      this.$router.push({name: this.$route.name, query: {page: val}})
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
      self.$http.delete(api.adminNotice, {params: {id: self.currentDialogId}})
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
    // 状态设置
    setStatus(index, item, evt) {
      var id = item.id
      var self = this
      self.$http.put(api.adminNoticeSetStatus, {id: id, evt: evt})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          self.tableData[index].status = evt
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
      self.query.sort = this.$route.query.sort || 0
      self.query.type = this.$route.query.type || 0
      self.query.status = this.$route.query.status || 0
      this.menuType = 0
      if (self.query.type) {
        this.menuType = parseInt(self.query.type)
      }
      self.isLoading = true
      self.$http.get(api.adminNoticeList, {params: {page: self.query.page, per_page: self.query.pageSize, sort: self.query.sort, type: self.query.type, status: self.query.status, evt: self.evt}})
      .then (function(response) {
        self.isLoading = false
        self.tableData = []
        if (response.data.meta.status_code === 200) {
          self.itemList = response.data.data
          self.query.totalCount = response.data.meta.pagination.total

          for (var i = 0; i < self.itemList.length; i++) {
            var item = self.itemList[i]
            item.cover_url = ''
            if (item.cover) {
              item.cover_url = item.cover.logo
            }
            item['created_at'] = item.created_at.date_format().format('yy-MM-dd')
            self.tableData.push(item)
          } // endfor
          console.log(self.tableData)
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

  .set-role-name {
    margin-bottom: 20px;
  }

</style>
