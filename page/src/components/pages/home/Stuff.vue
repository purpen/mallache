<template>
  <div class="container">
    <div class="blank20"></div>
    <div class="case-list" v-loading.body="isLoading">
      <el-row :gutter="20" class="anli-elrow">

        <el-col :xs="24" :sm="8" :md="8" :lg="8" v-for="(d, index) in itemList" :key="index">
          <el-card :body-style="{ padding: '0px' }" class="item">
            <div class="image-box">
              <router-link :to="{name: 'vcenterDesignCaseShow', params: {id: d.id}}"
                           :target="BMob ? '_self' : '_blank'">
                <img v-lazy="d.cover.middle">
              </router-link>
            </div>
            <div class="content">
              <router-link :to="{name: 'vcenterDesignCaseShow', params: {id: d.id}}" target="_blank">{{ d.title }}
              </router-link>
              <div class="des">
                <p>{{ d.profile }}</p>
              </div>

              <p class="company">
                <img class="avatar" v-if="d.design_company.logo_image" :src="d.design_company.logo_image.logo"
                     width="30"/>
                <img class="avatar" v-else :src="require('assets/images/avatar_100.png')" width="30"/>
                <span>{{d.design_company.company_abbreviation}}</span>
              </p>
            </div>
          </el-card>
        </el-col>
      </el-row>
    </div>
    <div class="blank20"></div>
    <div class="pager">
      <el-pagination class="pagination" :small="BMob" :current-page="query.page" :page-size="query.pageSize"
                     :total="query.totalCount" :page-count="query.totalPges" layout="total, prev, pager, next, jumper"
                     @current-change="handleCurrentChange">
      </el-pagination>
    </div>
  </div>
</template>

<script>
import api from '@/api/api'
export default {
  name: 'design_case_list',
  data() {
    return {
      itemList: [],
      isLoading: false,
      query: {
        page: 1,
        pageSize: 9,
        totalPges: 1,
        totalCount: 0
      },
      test: ''
    }
  },
  methods: {
    handleCurrentChange(page) {
      this.query.page = page
      this.loadList()
    },
    loadList() {
      const self = this
      self.$http
        .get(api.designCaseOpenLists, {
          params: { page: self.query.page, per_page: self.query.pageSize }
        })
        .then(function(response) {
          self.isLoading = false
          if (response.data.meta.status_code === 200) {
            // console.log(response)
            self.itemList = response.data.data
            self.query.totalCount = response.data.meta.pagination.total
            self.query.totalPges = response.data.meta.total_pages
          } else {
            self.$message.error(response.data.meta.message)
          }
        })
        .catch(function(error) {
          self.isLoading = false
          self.$message.error(error.message)
        })
    }
  },
  created: function() {
    const self = this
    self.isLoading = true
    this.loadList()
  },
  computed: {
    BMob() {
      return this.$store.state.event.isMob
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
a {
  color: #222;
}
.container h3 {
  font-size: 2rem;
  margin-bottom: 10px;
}

.case-list {
  min-height: 350px;
}

.item {
  /* height: 300px; */
  margin: 10px auto;
}

.image-box {
  height: 220px;
  overflow: hidden;
}

.image-box img {
  width: 100%;
  max-height: 360px;
}

.content {
  padding: 20px;
}

.content a {
  font-size: 1.6rem;
}

.des {
  height: 35px;
  margin: 10px 0;
  overflow: hidden;
}

.des p {
  color: #666;
  font-size: 1.4rem;
  line-height: 1.3;
  text-overflow: ellipsis;
}

.company {
  color: #666;
  display: block;
  line-height: 28px;
}

.company span {
  font-size: 14px;
}

.company img {
  margin-right: 6px;
}

.pager {
  margin: 0 auto;
}

.pager .pagination {
  text-align: center;
}

@media screen and (max-width: 1199px) and (min-width: 768px) {
  .image-box {
    height: 136px;
  }
}

@media screen and (max-width: 767px) {
  .image-box {
    height: auto;
  }
}
</style>
