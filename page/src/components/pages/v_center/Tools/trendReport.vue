<template>
  <div class="container">
    <div class="trend-report">
      <ToolsMenu currentName="trendReport"></ToolsMenu>
      <div :class="['report', {isMob : 'm-report'}]" v-loading.body="isLoading">
        <el-row :gutter="15" class="report-list">
          <el-col :xs="24" :sm="8" :md="8" :lg="8" v-for="(ele, index) in reportList" :key="index" class="item-cover">
            <router-link :to="{name: 'trendReportShow', params: {id: ele.id}}" class="item">
              <div class="image-box" :style="{background: 'url('+ ele.cover.middle + ') no-repeat center', backgroundSize: 'cover'}">
                <img :src="ele.cover.middle" :alt="ele.cover.summary">
              </div>
              <article class="item-body clearfix">
                <p class="title">{{ele.title}}</p>
                <p class="view fl"><i class="fx-4 fx-icon-browse-sm"></i>{{ele.hits}}</p>
                <p class="date fr"><i class="fx-2 fx-icon-time-orange-sm"></i>{{ele.created_at}}</p>
              </article>
            </router-link>
          </el-col>
        </el-row>
        <div class="pager" v-if="pagination.total_pages > 1">
          <el-pagination
            class="pagination"
            :current-page="pagination.current_page"
            :page-size="pagination.per_page"
            :total="pagination.total" :page-count="pagination.total_pages"
            layout="prev, pager, next, total"
            @current-change="handleCurrentChange">
          </el-pagination>
        </div>
        <div class="no-report" v-if="noReport">
          <span class="icon"></span>
          <p>还没有相关报告~</p>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import api from '@/api/api'
  import ToolsMenu from '@/components/pages/v_center/ToolsMenu'
  export default {
    name: 'trendReport',
    components: {
      ToolsMenu
    },
    data () {
      return {
        noReport: false,
        isLoading: false,
        reportList: [],
        pagination: {
          current_page: 1,
          per_page: 12,
          total: 0,
          total_pages: 0
        }
      }
    },
    created () {
      this.pagination.current_page = Number(this.$route.query.page) || 1
      this.getTrendReportList()
    },
    methods: {
      getTrendReportList () {
        this.isLoading = true
        this.$http.get(api.TrendReportList, {
          params: {
            page: this.pagination.current_page,
            per_page: this.pagination.per_page
          }
        }).then((res) => {
          this.isLoading = false
          //          console.log(res.data.data)
          if (res.data.meta.status_code === 200) {
            let meta = res.data.meta
            if (res.data.data.length) {
              for (let i of res.data.data) {
                i.created_at = i.created_at.date_format().format('yyyy年MM月dd日')
              }
              this.reportList = res.data.data
              this.pagination.total = meta.pagination.total
              this.pagination.total_pages = meta.pagination.total_pages
            } else {
              this.noReport = true
            }
          } else {
            this.$message.error(res.data.meta.message)
          }
        }).catch((err) => {
          this.isLoading = false
          console.error(err)
        })
      },
      password (updatePassword, reason) {
        updatePassword(prompt('password is "test"'))
      },
      handleCurrentChange (val) {
        this.pagination.current_page = val
        this.$router.push({name: this.$route.name, query: {page: this.pagination.current_page}})
        this.getTrendReportList()
      }
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      },
      menuStatus () {
        return this.$store.state.event.menuStatus
      }
    }
  }
</script>
<style scoped>
  .trend-report {
    min-height: 100%;
    margin-bottom: -30px;
  }

  .report-list {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start;
  }

  .item-cover {
    margin: 10px 0;
  }

  .item {
    max-width: 500px;
    margin: auto;
    border: 1px solid #D2D2D2;
    /* border-radius: 4px; */
    display: block;
    height: 100%;
    transition: all ease .3s;
  }

  .item:hover {
    transform: translate3d(0, -2px, 0);
    box-shadow: 0 5px 24px rgba(0, 0, 0, 0.3);
  }

  .report {
    min-height: 100%;
    position: relative;
    padding: 0 15px;
  }

  .no-report {
    overflow: hidden;
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    margin: auto;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    color: #666;
    font-size: 16px;
  }

  .no-report .icon {
    display: block;
    width: 80px;
    height: 80px;
    background: url("../../../../assets/images/tools/report/NoContent.png") no-repeat;
    background-size: contain;
    margin-bottom: 20px;
  }

  .image-box {
    height: 220px;
    overflow: hidden;
    border-bottom: 1px solid #D2D2D2;
    /* border-radius: 4px 4px 0 0; */
  }

  .image-box img {
    display: none
  }

  .item-body {
    overflow: hidden;
    padding: 10px;
    /* border-radius: 0 0 4px 4px; */
  }

  .item-body .title {
    font-size: 15px;
    max-height: 42px;
    line-height: 1.4;
    margin-bottom: 10px;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
  }

  .item-body .view {
    font-size: 12px;
    color: #999999;
    font-weight: normal;
    padding-right: 24px;
  }

  .item-body .date {
    font-size: 12px;
    color: #999999;
    font-weight: normal;
  }

  .item-body .view,
  .item-body .date {
    display: flex;
    align-items: center;
  }

  .pagination {
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  @media screen and (max-width: 767px) {
  .image-box {
    height: auto;
    max-height: 300px;
    overflow: hidden;
    /* border-radius: 4px 4px 0 0; */
  }
  .image-box img {
    display: block;
    width: 100%;
  }
  }
</style>
