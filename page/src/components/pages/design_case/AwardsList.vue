<template>
  <div class="container">
    <div class="blank20"></div>
    <menuSub></menuSub>
    <div class="case-list" v-loading.body="isLoading">
      <el-row :gutter="20" class="anli-elrow">
        <el-col :xs="24" :sm="8" :md="8" :lg="8" v-for="(d, index) in itemList" :key="index">
          <el-card :body-style="{ padding: '0px' }" class="card">
            <router-link :to="{name: 'designAwardsShow', params: {id: d.id}}"
                        :target="BMob ? '_self' : '_blank'">
              <div class="image-box" :style="{background: 'url('+ d.cover.middle + ') no-repeat center', backgroundSize: 'cover'}">
                  <img v-lazy="d.cover.middle">
              </div>
              <div class="content">
                <router-link :to="{name: 'designAwardsShow', params: {id: d.id}}" target="_blank">{{ d.title }}
                </router-link>
                <div class="des">
                  <p>{{ d.summary }}</p>
                </div>

                <p class="company">
                  <img class="avatar" :src="d.img"
                      width="30"/>
                  <span>{{d.category_value}}</span>
                </p>
              </div>
            </router-link>
          </el-card>
        </el-col>
      </el-row>
    </div>
    <div class="blank20"></div>
    <div class="pager" v-if="query.totalCount">
      <el-pagination v-if="itemList.length" class="pagination" :small="BMob" :current-page="query.page" :page-size="query.pageSize"
                     :total="query.totalCount" :page-count="query.totalPges" layout="total, prev, pager, next, jumper"
                     @current-change="handleCurrentChange">
      </el-pagination>
    </div>
  </div>
</template>

<script>
import api from '@/api/api'
import menuSub from './MenuSub'
import {AWARD_CASE_CATEGORY} from '@/config'
export default {
  name: 'designAwardsList',
  data() {
    return {
      itemList: [],
      isLoading: false,
      query: {
        page: 1,
        pageSize: 9,
        totalPges: 0,
        totalCount: 0
      },
      test: ''
    }
  },
  components: {
    menuSub
  },
  methods: {
    handleCurrentChange(page) {
      this.query.page = page
      this.$router.push({name: this.$route.name, query: {page: this.query.page}})
      this.loadList()
    },
    loadList() {
      const self = this
      self.isLoading = true
      self.$http
        .get(api.awardCaseList, {
          params: { page: self.query.page, per_page: self.query.pageSize }
        })
        .then(function(response) {
          self.isLoading = false
          if (response.data.meta.status_code === 200) {
            for (let i of response.data.data) {
              switch (i.category_id) {
                case 1:
                  i.img = AWARD_CASE_CATEGORY[0].img
                  break
                case 2:
                  i.img = AWARD_CASE_CATEGORY[1].img
                  break
                case 3:
                  i.img = AWARD_CASE_CATEGORY[2].img
                  break
                case 4:
                  i.img = AWARD_CASE_CATEGORY[3].img
                  break
                default:
                  i.img = AWARD_CASE_CATEGORY[4].img
                  break
              }
            }
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
    this.query.page = Number(this.$route.query.page) || 1
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

.image-box {
    height: 220px;
    overflow: hidden;
    border-bottom: 1px solid #D2D2D2;
    border-radius: 4px 4px 0 0;
}

.image-box img {
  display: none
}


.content {
  padding: 20px;
}

.content a {
  display: block;
  font-size: 1.6rem;
  overflow: hidden;
  text-overflow:ellipsis;
  white-space: nowrap;
}

.des {
  height: 36px;
  margin: 10px 0;
}

.des p {
  color: #666;
  font-size: 1.3rem;
  line-height: 18px;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
  overflow: hidden;
  max-height: 36px;
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

@media screen and (max-width: 767px) {
  .card {
    max-width: 500px;
    margin: 10px auto;
  }
  .image-box {
    height: auto;
    max-height: 300px;
    overflow: hidden;
    border-radius: 4px 4px 0 0;
  }
  .image-box img {
    display: block;
    width: 100%;
  }
}
</style>
