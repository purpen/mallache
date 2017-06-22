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

            <div class="design-case-list">
              <el-row :gutter="24">
                <el-col
                  v-for="(d, index) in designCases"
                  :key="index"
                  :span="8">
                  <div class="item">
                    <div class="img">
                      <img v-if="hasImg(d.first_image)" :src="d.cover['small']" />
                      <img v-else src="https://p4.taihuoniao.com/topic/170302/58b81d1020de8dfc6e8bd658-2-p325x200.jpg" />
                      <div class="opt">
                        <router-link :to="{name: 'vcenterDesignCaseEdit', params: {id: d.id}}">
                          <i class="el-icon-edit"></i>
                        </router-link>
                        <a href="javascript:void(0);" :item_id="d.id" :index="index" @click="delItem"><i class="el-icon-delete"></i></a>
                      </div>
                    </div>
                    <div class="content">
                      <router-link :to="{name: 'vcenterDesignCaseShow', params: {id: d.id}}" target="_blank">
                        {{ d.title }}
                      </router-link>
                    </div>
                  </div>
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
        designCases: [],
        userId: this.$store.state.event.user.id
      }
    },
    methods: {
      hasImg(d) {
        if (d.length === 0) {
          return false
        } else {
          return true
        }
      },
      delItem(event) {
        var id = event.target.parentNode.getAttribute('item_id')
        var index = event.target.parentNode.getAttribute('index')
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
      }
    },
    computed: {
    },
    watch: {
    },
    created: function() {
      const that = this
      that.$http.get(api.designCase, {})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          that.designCases = response.data.data
        }
      })
      .catch (function(error) {
        that.$message.error(error.message)
        console.log(error.message)
        return false
      })
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .design-case-list {
  
  }
  .design-case-list .item {
    height: 250px;
  }
  .design-case-list .img {
    height: 210px;
    overflow: hidden;
  }
  .design-case-list .opt {
    display: none;
  }
  .design-case-list .img:hover {
    position:relative;
    background-color:#fff;filter:Alpha(Opacity=90);opacity:0.9;
  }
  .design-case-list .img:hover .opt {
    width: 100%;
    line-height: 2;
    text-align: right;
    background-color:#000;filter:Alpha(Opacity=80);opacity:0.8;
    display: block;
    position:absolute;
    top: 0;
  }
  .design-case-list .img:hover .opt a {
    color: #ccc;
    margin: 0 5px 0 0; 
  }
  .design-case-list .img:hover .opt a i {
  }
  .design-case-list .img:hover .opt a:hover {
    color: #fff;
  }
  .design-case-list .item img {
    width: 100%;
  }
  .design-case-list .content {
    padding: 5px 5px 5px 5px;
  }
  .design-case-list .content a {
    font-size: 1.5rem;
  }

</style>
