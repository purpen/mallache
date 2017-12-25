<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="24">
      <v-menu :class="[isMob ? 'v-menu' : '']" currentName="match_case"></v-menu>

      <el-col :span="isMob ? 24 : 20">
        <div class="right-content">
          <v-menu-sub></v-menu-sub>
          <div :class="['content-box', isMob ? 'content-box-m' : '']" v-if="designCases.length">
            <div class="form-title">
              <span>参赛作品案例</span>
            </div>

            <div class="design-case-list" v-loading.body="isLoading">
              <el-row :gutter="10">
                <el-col :xs="24" :sm="8" :md="8" :lg="8" v-for="(d, index) in designCases" :key="index">
                  <el-card :body-style="{ padding: '0px' }" class="item">
                    <div class="image-box">
                      <router-link :to="{name: 'vcenterMatchCaseShow', params: {id: d.id}}"
                                   :target="isMob ? '_self' : '_blank'">
                        <img :src="d.cover.middle">
                      </router-link>
                    </div>
                    <div class="content">
                      <router-link :to="{name: 'vcenterMatchCaseShow', params: {id: d.id}}"
                                   :target="isMob ? '_self' : '_blank'">{{ d.title
                        }}
                      </router-link>
                      <div class="opt">
                        <a href="javascript:void(0);" :item_id="d.id" :index="index"
                           @click="delItem">删除</a>
                        <router-link :to="{name: 'vcenterMatchCaseSubmit', params: {id: d.id}}">
                          编辑
                        </router-link>
                      </div>
                    </div>
                  </el-card>
                </el-col>

              </el-row>

            </div>

          </div>
        </div>

      </el-col>
    </el-row>
  </div>
</template>

<script>
  import vMenu from '@/components/pages/v_center/Menu'
  import vMenuSub from '@/components/pages/v_center/match_case/MenuSub'
  import api from '@/api/api'
  import '@/assets/js/format'

  export default {
    name: 'vcenter_design_case_list',
    components: {
      vMenu,
      vMenuSub
    },
    data() {
      return {
        isLoading: false,
        designCases: [],
        userId: this.$store.state.event.user.id
      }
    },
    methods: {
      hasImg(d) {
        if (d) {
          return true
        } else {
          return false
        }
      },
      delItem(event) {
        let id = event.currentTarget.getAttribute ('item_id')
        this.$confirm ('是否执行此操作?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then (() => {
          const that = this
          that.$http.delete (api.workid.format (id), {})
            .then (function (response) {
              if (response.data.meta.status_code === 200) {
                that.$message.success ('删除成功!')
                that.getDesignCase ()
              }
            })
            .catch (function (error) {
              that.$message.error (error.message)
              console.log (error.message)
              return false
            })
        }).catch (() => {
        })
      },
      // 添加作品案例
      add() {
        this.$router.push ({name: 'vcenterMatchCaseSubmit'})
      },
      getDesignCase () {
        const that = this
        that.isLoading = true
        that.$http.get (api.work)
          .then ((res) => {
            that.isLoading = false
            if (res.data.meta.status_code === 200) {
              that.designCases = res.data.data
            } else {
              that.$message.error (res.data.meta.message)
            }
          })
          .catch ((err) => {
            that.isLoading = false
            console.error (err)
          })
      }
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      }
    },
    watch: {},
    created: function () {
      this.getDesignCase ()
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .right-content .content-box-m {
    border-top: 1px solid #E6E6E6;
    margin: 14px 0 0 0;
    padding: 0 15px;
    }

  .content-box-m .form-title {
    margin: 10px 0 6px;
    }

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

  @media screen and (max-width: 767px) {
    .opt a {
      font-size: 1.4rem;
      }
    }

  .add {
    text-align: center;
    }
</style>
