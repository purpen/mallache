<template>
  <div class="container blank40 min-height350">
    <el-row :gutter="20" class="anli-elrow">
      <v-menu currentName="message"></v-menu>

      <el-col :span="isMob ? 24 : 20" v-loading="isLoading">
        <div class="right-content">
          <v-menu-sub></v-menu-sub>
          <div class="content-box">
            <div class="item clearfix" v-for="(d, index) in itemList" :key="index" @click="removeRedDot(index)">
              <div class="left">
                <p class="logo"></p>
              </div>
              <div class="right">
                <div class="banner2">
                  <p class="notice">系统通知
                    <span class="time">{{ d.created_at }}</span>
                  </p>
                  <p class="title">
                    <el-badge :is-dot="d.not_read">{{ d.title }}</el-badge>
                  </p>
                </div>
                <p class="content">{{ d.content }}</p>
                <img class="content-img" v-if="d.cover" v-lazy="d.cover.big" :alt="d.cover.name">
                <p class="url"><a @click="aClick(d.url)">查看详情>></a></p>
              </div>
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
        <p v-if="isEmpty === true" class="noMsg">您还没收到任何通知～</p>
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
    name: 'SystemMessageList',
    components: {
      vMenu,
      vMenuSub
    },
    data () {
      return {
        isLoading: false,
        itemList: [],
        query: {
          page: 1,
          pageSize: 10,
          totalCount: 0
        },
        userId: this.$store.state.event.user.id,
        isEmpty: '',
        notice: 0
      }
    },
    methods: {
      loadList() {
        const self = this
        self.isLoading = true
        self.itemList = []
        self.$http.get(api.getNoticeList, { // 获取所有系统通知
          params: {
            page: self.query.page,
            per_page: self.query.pageSize
          }
        })
          .then(function (response) {
            self.isLoading = false
            if (response.data.meta.status_code === 200) {
              let data = response.data.data
              self.query.totalCount = response.data.meta.pagination.total
              for (let i = 0; i < data.length; i++) {
                let item = data[i]
                data[i]['created_at'] = item.created_at.date_format().format('yy-MM-dd hh:mm')
                data[i]['not_read'] = false
              }
              self.itemList = data
              if (self.itemList.length) {
                self.isEmpty = false
              } else {
                self.isEmpty = true
              }
              let noticeCount = sessionStorage.getItem('noticeCount')
              for (let j = 0; j < noticeCount; j++) {
                if (self.itemList[j]) {
                  self.itemList[j]['not_read'] = true // 给未读通知加上红点
                }
              }
            } else {
              self.$message.error(response.data.meta.message)
            }
          })
          .catch(function (error) {
            self.isLoading = false
            self.$message.error(error.message)
          })
      },
      removeRedDot(index) {
        this.itemList[index]['not_read'] = false
      },
      handleCurrentChange(val) {
        this.query.page = val
        this.$router.push({name: this.$route.name, query: {page: val}})
      },
      aClick (link) {
        let reg = /^(http)/
        if (!reg.test(link)) {
          window.open('http://' + link)
          return
        }
        window.open(link)
      },
      // 请求消息数量
      fetchMessageCount() {
        this.$http.get(api.messageGetMessageQuantity, {}).then((response) => {
          if (response.data.meta.status_code === 200) {
            this.notice = parseInt(response.data.data.notice)
          } else {
            this.$message.error(response.data.meta.message)
          }
        }).catch((error) => {
          console.error(error)
        })
      }
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      }
    },
    created: function () {
      let page = this.$route.query.page
      if (page) {
        this.query.page = parseInt(page)
      } else {
        this.query.page = 1
      }
      this.loadList()
    },
    watch: {
      '$route' (to, from) {
        // 对路由变化作出响应...
        let page = this.$route.query.page
        if (page) {
          this.query.page = parseInt(page)
        } else {
          this.query.page = 1
        }
        this.loadList()
      }
    }
  }
</script>

<style scoped>
  .container {
    overflow: hidden;
  }

  .right-content .content-box {
    padding: 0;
    border: none;
  }

  .content-box .item {
    border: 1px solid #ccc;
    margin-bottom: -1px;
    padding: 30px 50px 30px 20px;
    min-height: 30px;
    line-height: 30px;
    cursor: default;
    display: flex;
  }

  .content-box .item:hover {
    background-color: #F2F1F1;
  }

  .item .left {
    width: 70px;
    margin-right: 20px;
  }

  .item .left .logo{
    margin-top: 8px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 1px solid #EBEBEB;
    background: url("../../../../assets/images/logo.png") no-repeat center;
    background-size: 25px;
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
    line-height: 18px;
  }
  .item span.time {
    margin-left: 15px;
  }

  .item p.icon {
    float: right;
  }

  .item p.content {
    padding-top: 6px;
    font-size: 14px;
    clear: both;
    line-height: 1.5;
    color: #666;
  }

  .content-img {
    max-width: 100%;
    margin-top:12px;
  }

  .item p.url a{
    font-size: 14px;
    color: #FF5A5F;
    cursor: pointer;
  }

  i.alert {
    position: absolute;
    width: 7px;
    height: 7px;
    margin-top: 20px;
    margin-left: -72px;
    background: #FF5A5F;
    border-radius: 50%;
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
<style>
  .item p.title .el-badge__content.is-fixed {
    top: 10px;
    left: -88px;
    width: 7px;
    height: 7px;
  }
</style>
