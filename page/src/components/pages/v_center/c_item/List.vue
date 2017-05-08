<template>
  <div class="container">
    <el-row :gutter="24">
      <v-menu></v-menu>

      <el-col :span="20">
        <div class="right-content">
          <v-menu-sub></v-menu-sub>
          <div class="content-item-box">

            <div class="item">
              <div class="banner">
                  <p>
                    <span>2013-12-12</span>
                    <span>太火鸟科技</span>
                    <span>和我联系</span>
                  </p>
              </div>
              <div class="content">
                <div class="l-item">
                  <p class="c-title">旅行钱包设计</p>
                  <p>项目预算: ¥ 5000元</p>
                  <p>设计类别: 产品设计/日常用品</p>
                  <p>项目周期: 3周</p>
                </div>
                <div class="r-item">
                  <p><a href="#">提交项目报价 ></a></p>
                </div>
              </div>
              <div class="opt">
                <div class="l-item">
                  <p>
                    <span>项目金额:</span>&nbsp;&nbsp;<span class="money-str">¥ <b>5000.00</b></span>
                  </p>
                </div>
                <div class="r-item">
                  <p class="btn">
                    <a href="#">拒绝此单</a>&nbsp;&nbsp;
                    <a href="#" class="b-blue">有意向接单</a>&nbsp;&nbsp;
                    <a href="#" class="b-red">一键抢单</a>
                  </p>
                </div>
              </div>
            </div>

            <div class="item">
              <div class="banner">
                  aaa
              </div>
              <div class="content">
                bbb
              </div>
              <div class="opt">
                ccc
              </div>
            </div>


          </div>
        </div>

      </el-col>
    </el-row>
  </div>
</template>

<script>
  import vMenu from '@/components/pages/v_center/Menu'
  import vMenuSub from '@/components/pages/v_center/c_item/MenuSub'
  import api from '@/api/api'
  import '@/assets/js/format'

  export default {
    name: 'vcenter_item_list',
    components: {
      vMenu,
      vMenuSub
    },
    data () {
      return {
        designItems: [],
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
    created: function() {
      const that = this
      that.$http.get(api.designItemList, {})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          that.designItems = response.data.data
        } else {
          that.$message.error(response.meta.message)
        }

        console.log(response.data)
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

  .content-item-box {
  
  }
  .content-item-box .item {
    border: 1px solid #D2D2D2;
    margin: 20px 0px 20px 0;
  }
  .banner {
    border-bottom: 1px solid #ccc;
    background: #FAFAFA;
  }
  .content {
    border-bottom: 1px solid #ccc;
    height: 120px;
  }
  .item p {
    padding: 10px;
  }
  .l-item p {
    font-size: 1rem;
    color: #666;
    padding: 5px 10px 5px 10px;
  }
  p.c-title {
    font-size: 1.5rem;
    color: #333;
    padding: 15px 10px 15px 10px;
  }
  .opt {
    height: 30px;
  }

  .content .l-item {
    float: left;
  }
  .content .r-item {
    float: right;
  }
  .opt .l-item {
    float: left;
    line-height: 1.2;
  }
  .opt .r-item {
    float: right;
  }
  .money-str {
    font-size: 1.5rem;
  }
  .btn {
    font-size: 1rem;
  }
  .btn a {
    color: #666;
  }
  .btn a.b-blue {
    color: #00AC84;
  }
  .btn a.b-red {
    color: #FF5A5F;
  }

</style>
