<template>
  <div class="container">
    <div class="category-list">
      <router-link :to="{name: 'articleList'}" :class="{'active': menuType == 0}">最新</router-link>
      <router-link :to="{name: 'articleList', query: {category_id: d.id}}" v-for="(d, index) in cateList" :key="index" :class="{'active': menuType == d.id}">{{ d.name }}</router-link>
      <router-link :to="{name: 'subjectList'}">专题</router-link>
    </div>
    <div class="case-list" v-loading.body="isLoading">
      <el-row :gutter="20">

        <el-col :span="8" v-for="(d, index) in itemList" :key="index">
          <el-card :body-style="{ padding: '0px' }" class="item">
            <div class="image-box">
              <router-link :to="{name: 'articleShow', params: {id: d.id}}" target="_blank">
                <img :src="d.cover.middle">
              </router-link>
            </div>
            <div class="content">
              <router-link :to="{name: 'articleShow', params: {id: d.id}}" target="_blank">{{ d.title }}</router-link>
              <div class="des">
                <p>{{ d.short_content }}</p>
              </div>
            </div>
          </el-card>
        </el-col>

      </el-row>
    
    </div>

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

    <div class="blank20"></div>
  </div>
</template>

<script>
import api from '@/api/api'
export default {
  name: 'article_list',
  data () {
    return {
      cateList: [],
      itemList: [],
      category_id: 0,
      isLoading: false,
      query: {
        page: 1,
        pageSize: 3,
        totalCount: 0,
        sort: 1,
        type: 0,
        category_id: 0,

        test: null
      },
      test: ''
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
    loadList() {
      const self = this
      self.query.page = parseInt(this.$route.query.page || 1)
      self.query.sort = this.$route.query.sort || 1
      self.query.category_id = this.$route.query.category_id || 0
      this.menuType = 0
      if (self.query.category_id) {
        this.menuType = parseInt(self.query.category_id)
      }
      self.isLoading = true
      self.$http.get(api.articleList, {params: {page: self.query.page, per_page: self.query.pageSize, classification_id: self.query.category_id, sort: self.query.sort}})
      .then (function(response) {
        self.isLoading = false
        self.tableData = []
        if (response.data.meta.status_code === 200) {
          self.itemList = response.data.data
          self.query.totalCount = parseInt(response.data.meta.pagination.total)

          for (var i = 0; i < self.itemList.length; i++) {
            var item = self.itemList[i]
            item.cover_url = ''
            if (item.cover) {
              self.itemList[i].cover_url = item.cover.middle
            }
            // self.itemList[i]['created_at'] = item.created_at.date_format().format('yy-MM-dd')
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
    const self = this
    self.loadList()
    // 分类列表
    self.$http.get(api.categoryList, {params: {page: 1, per_page: 4, type: 1, sort: 1}})
    .then (function(response) {
      if (response.data.meta.status_code === 200) {
        self.cateList = response.data.data
      }
    })
    .catch (function(error) {
      self.$message.error(error.message)
    })
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
  .container{
  }
  .container h3 {
    font-size: 2rem;
    margin-bottom: 10px;
  }
  .category-list {
    margin: 30px 0 20px 0;
  }
  .category-list a {
    font-size: 1.6rem;
    margin-right: 40px;
    color: #666;
  }
  .category-list a:hover, .category-list a.active {
    color: #FF5A5F;
  }
  .case-list {
    min-height: 350px;
  }

  .item {
    height: 310px;
    margin: 10px 0;
  }

  .item img {
    width: 100%;
  }

  .image-box {
    height: 220px;
    overflow: hidden;
  }

  .content {
    padding: 10px;
  }
  .content a {
    color: #222;
    font-size: 1.8rem;
  }

  .des {
    height: 40px;
    margin: 10px 0;
    overflow: hidden;
  }

  .des p {
    color: #666;
    font-size: 1.4rem;
    line-height: 1.3;
    text-overflow: ellipsis;
  }

</style>
