<template>
  <div class="container blank40 min-height350">
    <el-row :gutter="20" class="anli-elrow">
      <v-menu currentName="message"></v-menu>

      <el-col :span="isMob ? 24 : 20" v-loading="isLoading">
        <div class="right-content">
          <v-menu-sub></v-menu-sub>
          <div class="content-box">

            <div class="item" v-for="(d, index) in itemList" @click="showDes(d, index)" :key="index">
              <div class="banner2">
                <p class="read" v-if="d.status === 0"><i class="alert"></i></p>
                <p class="notice">项目提醒
                  <span class="time">{{ d.created_at }}</span>
                </p>
                <p class="title">{{ d.title }}</p>
                <p class="icon">
                  <i v-if="d.is_show" class="el-icon-arrow-up"></i>
                  <i v-else class="el-icon-arrow-down"></i>
                </p>
              </div>
              <p v-show="d.is_show" class="content">{{ d.content }}</p>
              <a v-show="d.is_show" class="detail" href="javascript:void(0);" v-if="d.is_url === 1" @click.stop="redirect(d)">查看详情>></a>
            </div>
          </div>

          <el-pagination
            v-if="itemList.length"
            class="pagination"
            @current-change="handleCurrentChange"
            :current-page="query.page"
            :page-size="query.pageSize"
            layout="prev, pager, next"
            :total="query.totalCount">
          </el-pagination>

        </div>
        <div class="empty" v-if="isEmpty === true"></div>
        <p v-if="isEmpty === true" class="noMsg">您还没收到任何消息～</p>
      </el-col>
    </el-row>
  </div>
</template>

<script>
  import vMenu from '@/components/pages/v_center/Menu'
  import vMenuSub from '@/components/pages/v_center/message/MenuSub'
  import api from '@/api/api'
  import '@/assets/js/format'
  import '@/assets/js/date_format'

  export default {
    name: 'VcenterMessageList',
    components: {
      vMenu,
      vMenuSub
    },
    data () {
      return {
        isLoading: true,
        itemList: [],
        query: {
          page: 1,
          pageSize: 50,
          totalCount: 0,
          sort: 1,
          type: 0,
          test: null
        },
        userId: this.$store.state.event.user.id,
        isEmpty: ''
      }
    },
    methods: {
      loadList(type) {
        const self = this
        self.query.page = parseInt(this.$route.query.page || 1)
        self.query.sort = this.$route.query.sort || 1
        self.query.type = this.$route.query.type || 0

        self.isLoading = true
        self.itemList = []
        self.$http.get(api.messageGetMessageList, {
          params: {
            page: self.query.page,
            per_page: self.query.pageSize,
            sort: self.query.sort,
            type: self.query.type
          }
        })
          .then(function (response) {
            self.isLoading = false
            if (response.data.meta.status_code === 200) {
              let data = response.data.data
              console.log(data)
              self.query.totalCount = response.data.meta.pagination.total
              for (let i = 0; i < data.length; i++) {
                let item = data[i]
                data[i]['created_at'] = item.created_at.date_format().format('yy-MM-dd hh:mm')
                data[i]['is_show'] = false
              }
              self.itemList = data
              if (self.itemList.length) {
                self.isEmpty = false
              } else {
                self.isEmpty = true
              }
            }
          })
          .catch(function (error) {
            self.isLoading = false
            self.$message.error(error.message)
          })
      },
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
      // 下拉展开
      showDes(d, index) {
        const self = this
        if (d.is_show) {
          this.itemList[index].is_show = false
        } else {
          this.itemList[index].is_show = true
          this.itemList[index].status = 1
          // 确认已读状态
          if (d.status === 0) {
            self.$http.put(api.messageTrueRead, {id: d.id})
              .then(function (response) {
                if (response.data.meta.status_code === 200) {
                  self.itemList[index].status = 1
                }
              })
              .catch(function (error) {
                self.$message.error(error.message)
              })
          }
        }
      },
      // 根据类型跳转
      redirect(d) {
        if (d.type === 2) {
          this.$router.push({name: 'vcenterItemShow', params: {id: d.target_id}})
        } else if (d.type === 3) {
          this.$router.push({name: 'vcenterWalletList'})
        }
      }
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      }
    },
    created: function () {
      let type = this.$route.query.page
      if (type) {
        type = parseInt(type)
      } else {
        type = 0
      }
      this.loadList(type)
    },
    watch: {
      '$route' (to, from) {
        // 对路由变化作出响应...
        let type = this.$route.query.page
        if (type) {
          type = parseInt(type)
        } else {
          type = 0
        }
        this.loadList()
      }
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .container {
    overflow: hidden;
  }

  .right-content .content-box {
    padding: 0;
    border: none;
  }

  .content-box .item {
    position: relative;
    border: 1px solid #ccc;
    margin-bottom: -1px;
    padding: 20px;
    cursor: pointer;
    min-height: 30px;
    line-height: 30px;
    overflow: hidden;
  }

  .content-box .item:hover {
    background-color: #F2F1F1;
  }

  .item p {
    line-height: 24px
  }

  .item .banner2 {
    height: 30px;
    line-height: 30px;
  }

  .item p.read {
    position: relative;
    float: left;
  }

  .item p.title {
    font-size: 15px;
    font-weight: 600;
    float: left;
    color: #222;
  }

  .item p.notice {
    color: #999;
    font-size: 12px;
  }

  .item span.time {
    margin-left: 15px;
  }


  .item p.icon {
    position: absolute;
    right: 20px;
    color: #999;
    top: 33px;
  }

  .item p.content {
    padding-top: 10px;
    font-size: 14px;
    clear: both;
    line-height: 18px;
  }

  .item .detail {
    font-size: 14px;
    line-height: 24px;
    color: #FF5A5F;
    cursor: pointer;
  }

  i.alert {
    display: block;
    background: #FF5A5F;
    border-radius: 50%;
    width: 7px;
    height: 7px;
    margin-top: 9px;
    margin-left: -13px;
    position: absolute;;
  }


  .pagination {
    display: flex;
    justify-content: center;
  }

  .empty {
    width: 122px;
    height: 113px;
    margin: 100px auto 0;
    background: url("../../../../assets/images/tools/report/NoMessage.png") no-repeat;
    background-size: contain;
  }

  .noMsg {
    text-align: center;
    color: #969696;
    line-height: 3;
  }

  .content a {
    color: #FF5A5F
  }

  @media screen and (max-width: 350px) {
    .content-box .item {
      padding: 10px 15px;
    }

    .item p.icon {
      float: none;
      position: absolute;
      right: 15px;
    }
  }
</style>
