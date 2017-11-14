<template>
  <div class="container min-height350">
    <div class="blank20"></div>
    <el-row :gutter="20" class="anli-elrow">
      <v-menu currentName="message"></v-menu>

      <el-col :span="isMob ? 24 : 20">
        <div class="right-content" v-if="itemList.length">
          <v-menu-sub></v-menu-sub>
          <div class="content-box">

            <div class="item" v-for="(d, index) in itemList" @click="showDes(d, index)">
              <div class="banner2">
                <p class="read" v-if="d.status === 0"><i class="alert"></i></p>
                <p class="title">{{ d.title }}</p>
                <p class="icon">
                  <i v-if="d.is_show" class="el-icon-arrow-up"></i>
                  <i v-else class="el-icon-arrow-down"></i>
                </p>
                <p class="time">{{ d.created_at }}</p>
              </div>
              <p v-show="d.is_show" class="content">{{ d.content }} <a href="javascript:void(0);" v-if="d.is_url === 1"
                                                                       @click.stop="redirect(d)">查看</a></p>
            </div>
          </div>

          <el-pagination
            class="pagination"
            @current-change="handleCurrentChange"
            :current-page="query.page"
            :page-size="query.pageSize"
            layout="prev, pager, next"
            :total="query.totalCount">
          </el-pagination>

        </div>
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
    name: 'vcenter_message_list',
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
          pageSize: 10,
          totalCount: 0,
          sort: 1,
          type: 0,

          test: null
        },
        userId: this.$store.state.event.user.id
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
            if (response.data.meta.status_code === 200) {
              let data = response.data.data
              self.query.totalCount = response.data.meta.pagination.total
              for (let i = 0; i < data.length; i++) {
                let item = data[i]
                data[i]['created_at'] = item.created_at.date_format().format('yy-MM-dd hh:mm')
                data[i]['is_show'] = false
              }
              self.itemList = data
//              console.log(data)
            }
          })
          .catch(function (error) {
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
      let type = this.$route.query.type
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
        let type = this.$route.query.type
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

  .right-content .content-box {
    padding: 0 0 0 0;
  }

  .content-box .item {
    border-bottom: 1px solid #ccc;
    padding: 10px 20px 10px;
    cursor: pointer;
    min-height: 30px;
    line-height: 30px
  }

  .content-box .item:hover {
    background-color: #F2F1F1;
  }

  .item p {
    line-height: 30px
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
    float: left;
    color: #222;
  }

  .item p.time {
    color: #666;
    font-size: 1.2rem;
    float: right;
    margin: 0 30px;
  }

  .item p.icon {
    float: right;
  }

  .item p.content {
    clear: both;
    line-height: 3;
    color: #666;
  }

  .item p.content a {
    font-size: 1.2rem;
  }

  i.alert {
    display: block;
    background: #f00;
    border-radius: 50%;
    width: 7px;
    height: 7px;
    margin-top: 7px;
    margin-left: -13px;
    position: absolute;;
  }

  i.alert.gray {
    background: #ddd;
  }

  .pagination {
    display: flex;
    justify-content: center;
  }
</style>
