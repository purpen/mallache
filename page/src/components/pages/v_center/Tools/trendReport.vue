<template>
  <div class="container">
    <div class="trend-report">
      <div class="blank20"></div>
      <el-row :gutter="24" class="anli-elrow">
        <v-menu currentName="trendReport" v-if="menuStatus !== 'tools' || !isMob"></v-menu>
        <ToolsMenu v-if="menuStatus === 'tools' && isMob"
                   currentName="trendReport"></ToolsMenu>
        <el-col :span="isMob ? 24 : 20">
          <div class="report">
            <el-row :gutter="20">
              <el-col :span="isMob ? 12 : 8" v-for="(ele, index) in reportList" :key="index">
                <router-link to="" class="item">
                  <div class="picture"
                       :style="{background: 'url('+ele.cover.middle+'no-repeat center)', backgroundSize: 'cover'}">
                    {{ele.cover.summary}}
                  </div>
                  <article class="item-boty clearfix">
                    <p class="title">{{ele.title}}</p>
                    <p class="view fl">{{ele.hits}}</p>
                    <p class="date fl">{{ele.created_at}}</p>
                  </article>
                </router-link>
              </el-col>
            </el-row>
            <div class="pager">
              <el-pagination class="pagination"
                             :current-page="pagination.current_page"
                             :page-size="pagination.per_page"
                             :total="pagination.total" :page-count="pagination.total_pages"
                             layout="prev, pager, next, total"
                             @current-change="handleCurrentChange">
                <!--@current-change="handleCurrentChange"-->
              </el-pagination>
            </div>
            <div class="no-report" v-if="noReport">
              <span class="icon"></span>
              <p>还没有相关报告~</p>
            </div>
          </div>
        </el-col>
      </el-row>
    </div>
  </div>
</template>
<script>
  import api from '@/api/api'
  import vMenu from '@/components/pages/v_center/Menu'
  import ToolsMenu from '@/components/pages/v_center/ToolsMenu'
  import pdf from 'vue-pdf'
  export default {
    name: 'trendReport',
    components: {
      vMenu,
      ToolsMenu,
      pdf
    },
    data () {
      return {
        noReport: false,
        reportList: [],
        pagination: {
          current_page: 1,
          per_page: 10,
          total: 0,
          total_pages: 0
        }
      }
    },
    created () {
      this.getTrendReportList()
    },
    methods: {
      getTrendReportList () {
        this.$http.get(api.TrendReportList, {
          params: {
            page: this.pagination.current_page,
            per_page: this.pagination.per_page
          }
        }).then((res) => {
          console.log(res.data.data)
          if (res.data.meta.status_code === 200) {
            let meta = res.data.meta
            if (res.data.data.length) {
              for (let i of res.data.data) {
                let date = new Date(i.created_at * 1000)
                i.created_at = date.toLocaleDateString() + ' ' + date.getHours() + ':' + date.getMinutes()
              }
              this.reportList = res.data.data
              this.pagination.total = meta.pagination.total
              this.pagination.total_pages = meta.pagination.total_pages
            } else {
              this.noReport = true
            }
          }
        }).catch((err) => {
          console.error(err)
        })
      },
      password (updatePassword, reason) {
        updatePassword(prompt('password is "test"'))
      },
      handleCurrentChange (val) {
        this.pagination.current_page = val
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

  .item {
    border: 1px solid #D2D2D2;
    border-radius: 4px 4px 0 0;
    display: block;
    height: 100%;
  }

  .report {
    min-height: 100%;
    position: relative;
    padding-bottom: 50px
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

  .picture {
    height: 180px;
  }

  .item-boty {
    overflow: hidden;
    padding: 10px 10px 20px;
  }

  .item-boty .title {
    font-size: 18px;
    line-height: 1.4;
    margin: 10px 0;
    color: #222222;
    font-weight: 600;
  }

  .item-boty .view {
    font-size: 12px;
    line-height: 16px;
    text-indent: 26px;
    color: #999999;
    font-weight: normal;
    background: url('../../../../assets/images/tools/report/browse@2x.png') no-repeat left;
    background-size: 20px;
    padding-right: 40px;
    margin-bottom: 10px;
  }

  .item-boty .date {
    font-size: 12px;
    line-height: 16px;
    text-indent: 26px;
    color: #999999;
    font-weight: normal;
    background: url('../../../../assets/images/tools/report/time-gray@2x.png') no-repeat left;
    background-size: 16px;
  }

  .pager {
    position: absolute;
    width: 100%;
    left: 0;
    bottom: 0;
  }

  .pagination {
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
  }
</style>
