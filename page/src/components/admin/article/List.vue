<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <v-menu selectedName="articleList"></v-menu>

      <el-col :span="20">
        <div class="content">

          <div class="admin-menu-sub">
            <div class="admin-menu-sub-list">
              <router-link :to="{name: 'adminArticleList'}" active-class="false" :class="{'item': true, 'is-active': menuType == 0}">全部</router-link>
            </div>

            <div class="fr">
              <router-link :to="{name: 'adminArticleSubmit'}" class="item add">
                <i class="el-icon-plus"></i> 添加</router-link>
            </div>
          </div>

          <el-table :data="tableData" border v-loading.body="isLoading" class="admin-table" @selection-change="handleSelectionChange" style="width: 100%">
            <el-table-column type="selection" width="55">
            </el-table-column>
            <el-table-column prop="id" label="ID" width="60">
            </el-table-column>
            <el-table-column label="封面" width="80">
              <template scope="scope">
                <p><img :src="scope.row.cover_url" width="50" /></p>
              </template>
            </el-table-column>
            <el-table-column prop="title" label="标题" min-width="180">
            </el-table-column>
            <el-table-column prop="classification_value" label="分类" width="100">
            </el-table-column>
            <el-table-column prop="source_from" label="来源" width="120">
            </el-table-column>
            <el-table-column prop="user_id" label="创建人" width="60">
            </el-table-column>
            <el-table-column prop="read_amount" label="浏览量" width="60">
            </el-table-column>
            <el-table-column label="状态" width="60">
              <template scope="scope">
                <p v-if="scope.row.status === 1">
                  <el-tag type="success">发布</el-tag>
                </p>
                <p v-else>
                  <el-tag type="gray">草稿</el-tag>
                </p>
              </template>
            </el-table-column>
            <el-table-column prop="created_at" width="100" label="创建时间">
            </el-table-column>
            <el-table-column width="100" label="操作">
              <template scope="scope">
                <p>
                  <a href="javascript:void(0);" v-if="scope.row.status === 1" @click="setStatus(scope.$index, scope.row, 0)">取消发布</a>
                  <a href="javascript:void(0);" v-else @click="setStatus(scope.$index, scope.row, 1)">发布</a>
                </p>
                <p>
                  <router-link :to="{name: 'adminArticleSubmit', query: {id: scope.row.id}}">编辑</router-link>
                  <a href="javascript:void(0);">删除</a>
                </p>
              </template>
            </el-table-column>
          </el-table>

          <el-pagination class="pagination" @size-change="handleSizeChange" @current-change="handleCurrentChange" :current-page="query.page" :page-sizes="[50, 100, 500]" :page-size="query.pageSize" layout="total, sizes, prev, pager, next, jumper" :total="query.totalCount">
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
  data() {
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
      this.$router.push({ name: this.$route.name, query: { page: val } })
    },
    setStatus(index, item, evt) {
      var id = item.id
      var url = api.adminArticleChangeStatus
      var self = this
      self.$http.put(url, { id: id, status: evt })
        .then(function(response) {
          if (response.data.meta.status_code === 200) {
            self.itemList[index].status = evt
            self.$message.success('操作成功')
          } else {
            self.$message.error(response.meta.message)
          }
        })
        .catch(function(error) {
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
      self.$http.get(api.adminArticleList, { params: { page: self.query.page, per_page: self.query.pageSize, sort: self.query.sort, type: self.query.type } })
        .then(function(response) {
          self.isLoading = false
          self.tableData = []
          if (response.data.meta.status_code === 200) {
            self.itemList = response.data.data
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

            console.log(self.itemList)
          } else {
            self.$message.error(response.data.meta.message)
          }
        })
        .catch(function(error) {
          self.$message.error(error.message)
          self.isLoading = false
        })
    }
  },
  created: function() {
    this.loadList()
  },
  watch: {
    '$route'(to, from) {
      // 对路由变化作出响应...
      this.loadList()
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
