<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="24">
      <v-menu></v-menu>

      <el-col :span="20">
        <div class="right-content">
          <v-menu-sub></v-menu-sub>
          <div class="content-box">
            <div class="form-title">
              <span>全部作品案例</span>
            </div>

            <div class="design-case-list" v-loading.body="isLoading">

              <el-row :gutter="10">

                <el-col :span="8" v-for="(d, index) in designCases" :key="index">
                  <el-card :body-style="{ padding: '0px' }" class="item">
                    <div class="image-box">
                      <router-link :to="{name: 'vcenterDesignCaseShow', params: {id: d.id}}" target="_blank">
                        <img :src="d.cover.middle">
                      </router-link>
                    </div>
                    <div class="content">
                      <router-link :to="{name: 'vcenterDesignCaseShow', params: {id: d.id}}" target="_blank">{{ d.title }}</router-link>
                      <div class="opt">
                        <a href="javascript:void(0);" :item_id="d.id" :index="index" @click="delItem">删除</a>
                        <router-link :to="{name: 'vcenterDesignCaseEdit', params: {id: d.id}}">
                          编辑
                        </router-link>
                      </div>
                    </div>
                  </el-card>
                </el-col>

              </el-row>

            </div>
            <div class="add blank20">
              <el-button class="is-custom" @click="add" type="primary"><i class="el-icon-plus"></i>  添加作品案例</el-button>
            </div>
          </div>
        </div>

      </el-col>
    </el-row>
  </div>
</template>

<script>
  import vMenu from '@/components/pages/v_center/Menu'
  import vMenuSub from '@/components/pages/v_center/design_case/MenuSub'
  import api from '@/api/api'
  import '@/assets/js/format'

  export default {
    name: 'vcenter_design_case_list',
    components: {
      vMenu,
      vMenuSub
    },
    data () {
      return {
        isLoading: false,
        designCases: [],
        userId: this.$store.state.event.user.id
      }
    },
    methods: {
      hasImg(d) {
        if (!d) {
          return false
        } else {
          return true
        }
      },
      delItem(event) {
        var id = event.currentTarget.getAttribute('item_id')
        var index = event.currentTarget.getAttribute('index')
        this.$confirm('是否执行此操作?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          const that = this
          that.$http.delete(api.designCaseId.format(id), {})
          .then (function(response) {
            if (response.data.meta.status_code === 200) {
              that.designCases.splice(index, 1)
              that.$message.success('删除成功!')
            }
          })
          .catch (function(error) {
            that.$message.error(error.message)
            console.log(error.message)
            return false
          })
        }).catch(() => {
        })
      },
      // 添加作品案例
      add() {
        this.$router.push({name: 'vcenterDesignCaseAdd'})
      }
    },
    computed: {
    },
    watch: {
    },
    created: function() {
      const that = this
      that.isLoading = true
      that.$http.get(api.designCase, {})
      .then (function(response) {
        that.isLoading = false
        if (response.data.meta.status_code === 200) {
          that.designCases = response.data.data
        }
      })
      .catch (function(error) {
        that.$message.error(error.message)
        that.isLoading = false
      })
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .design-case-list {
  
  }
  .design-case-list .item {
    height: 240px;
  }

  .item {
    margin: 5px 0;
  }

  .item img {
    width: 100%;
  }

  .image-box {
    height: 180px;
    overflow: hidden;
  }

  .content {
    padding: 10px;
  }
  .content a {
    font-size: 1.5rem;
  }

  .opt {
    margin: 10px 0 0 0;
    text-align: right;
  }

  .opt a {
    font-size: 1.2rem;
  }

  .add {
    text-align: center;
  }

</style>
