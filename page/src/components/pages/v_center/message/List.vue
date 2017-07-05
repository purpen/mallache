<template>
  <div class="container min-height350">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <v-menu currentName="message"></v-menu>

      <el-col :span="20">
        <div class="right-content">
          <v-menu-sub></v-menu-sub>
          <div class="content-box">

            <div class="item" v-for="(d, index) in itemList">
              <p class="title">系统通知 <span>{{ d.created_at }}</span></p>
              <p class="content">{{ d.content }}</p>
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
        self.$http.get(api.messageGetMessageList, {params: {page: self.query.page, per_page: self.query.pageSize, sort: self.query.sort, type: self.query.type}})
        .then (function(response) {
          if (response.data.meta.status_code === 200) {
            var data = response.data.data
            self.query.totalCount = response.data.meta.pagination.total
            for (var i = 0; i < data.length; i++) {
              var item = data[i]
              data[i]['created_at'] = item.created_at.date_format().format('yy-MM-dd hh:mm')
            }
            self.itemList = data
            console.log(data)
          }
        })
        .catch (function(error) {
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
      }
    },
    computed: {
    },
    created: function() {
      var type = this.$route.query.type
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
        var type = this.$route.query.type
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

  .content-box {
  
  }
  .content-box .item {
    border-bottom: 1px solid #ccc;
    padding: 10px 0;
  }
  .item p {
    font-size: 1.3rem;
  }
  .item p.title {
    color: #FF5A5F
  }
  .item p.title span {
    color: #666;
    font-size: 1.2rem;
  }
  .item p.content {
  
  }


</style>
