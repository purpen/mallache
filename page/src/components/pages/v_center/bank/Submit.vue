<template>
  <div class="container">
    <el-row :gutter="24">
      <v-menu currentName="wallet"></v-menu>

      <el-col :span="20">
        <div class="right-content">
          <div class="item-list">
              <el-row :gutter="15">

                <el-col :span="8">
                  <div class="item">
                    <div class="item-title">
                      <p>中国建设银行</p>
                    </div>
                    <div class="item-content">
                      <div class="number">
                        <p>**** **** **** 0086</p>
                      </div>
                      <div class="option">
                        <router-link :to="{name: 'vcenterBankList'}">管理</router-link>
                      </div>
                      <div class="clear default">
                        <p><i class="fa fa-check-circle-o" aria-hidden="true"></i> 默认银行账户</p>
                      </div>
                    </div>
                  </div>
                </el-col>


                <el-col :span="8">
                  <div class="item add">
                    <router-link :to="{name: 'vcenterBankAdd'}">
                      <p class="add-icon"><i class="el-icon-plus avatar-uploader-icon"></i></p>
                      <p class="add-des">添加银行卡</p>
                    </router-link>
                  </div>
                </el-col>

              </el-row>
          </div>
        </div>

      </el-col>
    </el-row>
  </div>
</template>

<script>
  import vMenu from '@/components/pages/v_center/Menu'
  import api from '@/api/api'
  import '@/assets/js/format'
  import '@/assets/js/date_format'

  export default {
    name: 'vcenter_bank_list',
    components: {
      vMenu
    },
    data () {
      return {
        isLoading: false,
        itemList: [],
        userId: this.$store.state.event.user.id
      }
    },
    methods: {
      loadList() {
        const self = this
        self.isLoading = true
        self.$http.get(api.fundLogList, {})
        .then (function(response) {
          self.isLoading = false
          if (response.data.meta.status_code === 200) {
            self.itemList = response.data.data

            for (var i = 0; i < self.itemList.length; i++) {
              // var item = self.itemList[i]
            } // endfor

            console.log(response.data.data)
          }
        })
        .catch (function(error) {
          self.$message.error(error.message)
          self.isLoading = false
          return false
        })
      }
    },
    computed: {
    },
    created: function() {
      this.loadList()
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

  .item-list {

  }

  .item {
    border: 1px solid #ccc;
    margin-bottom: 10px;
    height: 150px;
  }

  .item-title {
    background-color: #F3F3F3;
    padding: 5px 15px;
  }
  .item-title p {
    line-height: 2;
    font-size: 1.5rem;
    color: #333;
  }

  .item-content {
    padding: 20px 15px 0 15px;
  }

  .item-content .number {
    float: left;
  }
  .item-content .number p {
    font-size: 1.5rem;
    color: #333;
  }

  .item-content .option {
    float: right;
  }
  .item-content .option a {
    color: #FF5A5F;
  }

  .default {
    margin: 70px 0 20px 0;
  }
  .default p {
    font-size: 1.2rem;
    color: #666;
  }

  .add {
    text-align: center;
  }
  .add .add-icon {
    margin-top: 40px;
    font-size: 3rem;
    color: #666;
  }
  .add .add-des {
    line-height: 2;
    color: #666;
  }

</style>
