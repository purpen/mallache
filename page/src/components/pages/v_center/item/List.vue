<template>
  <div class="container">
    <el-row :gutter="24">
      <v-menu></v-menu>

      <el-col :span="20">
        <div class="right-content">
          <v-menu-sub></v-menu-sub>
          <div class="content-item-box">
            <div class="pub">
              <router-link :to="{name: 'itemSubmitOne'}">
                <el-button class="pub-btn is-custom" type="primary" size="large"><i class="el-icon-plus"></i> 发布项目</el-button>
              </router-link>
            </div>

            <div class="item" v-for="d in itemList">
              <div v-if="d.item.status === 1">
                <div class="banner">
                    <p>
                      <span>进行中</span>
                    </p>
                </div>
                <div class="content">
                  <div class="pre">
                    <p class="c-title-pro">{{ d.item.name }}</p>
                    <p class="progress-line"><el-progress :text-inside="true" :stroke-width="18" :percentage="d.item.progress" status="exception"></el-progress></p>
                    <p class="prefect">项目信息完善进度</p>
                    <p><el-button class="is-custom" :progress="d.item.stage_status" :item_id="d.item.id" :item_type="d.item.type" @click="editItem" size="small" type="primary"><i class="el-icon-edit"> </i> 完善项目</el-button></p>
                  </div>
                </div>
              </div>

              <div v-else>

                <div class="banner">
                    <p>
                      <span>2013-12-12</span>
                    </p>
                </div>
                <div class="content">
                  <div class="l-item">
                    <p class="c-title">{{ d.item.name }}</p>
                    <p>项目预算: {{ d.item.design_cost_value }}</p>
                    <p v-if="d.item.type === 1">{{ d.item.type_value + '/' + d.item.design_type_value + '/' + d.item.field_value + '/' + d.item.industry_value }}</p>
                    <p v-if="d.item.type === 2">{{ d.item.type_value + '/' + d.item.design_type_value }}</p>
                    <p>项目周期: {{ d.item.cycle_value }}</p>
                  </div>
                  <div class="r-item">
                    <p><a href="#">{{ d.item.status_label }}</a></p>
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
                      <a href="#" class="b-blue">查看报价，选择设计公司 ></a>&nbsp;&nbsp;
                    </p>
                  </div>
                </div>

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
  import vMenuSub from '@/components/pages/v_center/item/MenuSub'
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
        itemList: [],
        userId: this.$store.state.event.user.id
      }
    },
    methods: {
      loadList(type) {
        const that = this
        that.$http.get(api.itemList, {type: type})
        .then (function(response) {
          if (response.data.meta.status_code === 200) {
            var data = response.data.data
            for (var i = 0; i < data.length; i++) {
              var progress = data[i].item.stage_status
              var status = data[i].item.status
              switch (progress) {
                case 1:
                  data[i]['item']['progress'] = 20
                  break
                case 2:
                  data[i]['item']['progress'] = 60
                  break
                case 3:
                  data[i]['item']['progress'] = 90
                  break
                default:
                  data[i]['item']['progress'] = 0
              }
              switch (status) {
                case 1:
                  data[i]['item']['status_label'] = '完善信息'
                  break
                case 2:
                  data[i]['item']['status_label'] = '等待审核'
                  break
                case 3:
                  data[i]['item']['status_label'] = '等待设计公司接单…'
                  break
                default:
                  data[i]['item']['status_label'] = '待补充'
              }
            }
            that.itemList = data
            console.log(data)
          }
        })
        .catch (function(error) {
          that.$message.error(error.message)
          console.log(error.message)
          return false
        })
      },
      editItem(event) {
        var progress = parseInt(event.currentTarget.getAttribute('progress'))
        var itemId = event.currentTarget.getAttribute('item_id')
        var type = parseInt(event.currentTarget.getAttribute('item_type'))
        var name = null
        switch (progress) {
          case 0:
            name = 'itemSubmitTwo'
            break
          case 1:
            if (type === 1) {
              name = 'itemSubmitThree'
            } else if (type === 2) {
              name = 'itemSubmitUIThree'
            }
            break
          case 2:
            name = 'itemSubmitFour'
            break
          case 3:
            name = 'itemSubmitFive'
            break
        }
        this.$router.push({name: name, params: {id: itemId}})
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
        this.loadList(type)
      }
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .content-item-box {
  
  }
  .pub {
    background: #FAFAFA;
    height: 150px;
    margin: 20px 0 10px 0;
    position: relative;
  }
  .pub .pub-btn {
    position: absolute;
    padding: 10px 30px 10px 30px;
    top: 40%;
    left: 40%;
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
    height: 130px;
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
  p.c-title-pro {
    font-size: 1.5rem;
    color: #333;
    padding: 15px 10px 5px 10px;
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
  .content .pre {
    height: 500px;
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
  .prefect {
    font-size: 1rem;
    color: #666;
    margin-top: -12px;
    margin-bottom: -10px;
  }

</style>
