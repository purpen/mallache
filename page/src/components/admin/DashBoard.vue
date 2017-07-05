<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <v-menu></v-menu>

      <el-col :span="20">

        <div class="count-items">
          <el-row :gutter="10">
            <el-col :span="countNum">
              <div class="count-item red">
                <p class="count">32323</p>
                <p class="title">注册用户</p>
                <p class="des">供应商: 100 | 需求方: 320</p>
              </div>
            </el-col>

            <el-col :span="countNum">
              <div class="count-item blue">
                <p class="count">12343</p>
                <p class="title">项目</p>
                <p class="des">关闭: 100 | 进行中: 320 | 已完成: 5000</p>
              </div>
            </el-col>

            <el-col :span="countNum">
              <div class="count-item green">
                <p class="count">555</p>
                <p class="title">订单</p>
                <p class="des">待支付: 320</p>
              </div>
            </el-col>

            <el-col :span="countNum">
              <div class="count-item yellow">
                <p class="count">555</p>
                <p class="title">提现单</p>
                <p class="des">~</p>
              </div>
            </el-col>

          </el-row>
        </div>

        <div class="content-items">
          <el-row :gutter="8">
            <el-col :span="10">
              <div class="content-item">
                <div class="form-title">
                  <span>通知</span>
                </div>

                <div class="content-box">

                  <p class="alert-title"><span>*</span> 请您尽快解决如下问题，不要让用户等太久哦〜</p>

                  <div class="item">
                    <h3>设计公司</h3>
                    <p class="item-title">有 <span class="green">12</span> 家设计公司等待认证</p>
                    <p class="item-btn"><router-link :to="{name: 'adminCompanyList'}">查看</router-link></p>
                  </div>

                  <div class="item">
                    <h3>需求公司</h3>
                    <p class="item-title">有 <span class="green">12</span> 家需求公司等待认证</p>
                    <p class="item-btn"><router-link :to="{name: 'adminDemandCompanyList'}">查看</router-link></p>
                  </div>

                  <div class="item no-line">
                    <h3>订单</h3>
                    <p class="item-title">有 <span class="green">12</span> 个订单申请对公打款，请留意账户打款动向〜</p>
                    <p class="item-btn"><router-link :to="{name: 'adminOrderList'}">查看</router-link></p>
                  </div>

                  <div class="item no-line">
                    <h3>提现单</h3>
                    <p class="item-title">有 <span class="green">12</span> 个提现单等待提现，请处理~</p>
                    <p class="item-btn"><router-link :to="{name: 'adminWithDrawList'}">查看</router-link></p>
                  </div>

                </div>

              </div>
            </el-col>

            <el-col :span="14">
              <div class="content-item">
                <div class="form-title">
                  <span>最近的项目</span>
                </div>
                  <el-table
                    :data="tableItemData"
                    border
                    v-loading.body="isItemLoading"
                    class="admin-table"
                    @selection-change="handleSelectionChange"
                    style="width: 100%">
                    <el-table-column
                      prop="item.id"
                      label="ID"
                      width="40">
                    </el-table-column>
                    <el-table-column
                      label="项目名称"
                      min-width="140">
                        <template scope="scope">
                          <p>
                            <a href="#">{{ scope.row.info.name }}</a>
                          </p>
                        </template>
                    </el-table-column>
                    <el-table-column
                      prop="item.status_label"
                      min-width="100"
                      label="状态">
                    </el-table-column>
                    <el-table-column
                      prop="item.created_at"
                      width="80"
                      label="创建时间">
                    </el-table-column>
                  </el-table>

              </div>
            </el-col>

          </el-row>
        </div>

      </el-col>
    </el-row>

  </div>
</template>

<script>
import api from '@/api/api'
import vMenu from '@/components/admin/Menu'
export default {
  name: 'admin_dash_board',
  components: {
    vMenu
  },
  data () {
    return {
      countNum: 6,
      isItemLoading: false,
      tableItemData: [],
      msg: ''
    }
  },
  methods: {
    handleSelectionChange(val) {
      this.multipleSelection = val
    }
  },
  created: function() {
    const self = this
    // 加载最近的项目列表
    self.isItemLoading = true
    self.$http.get(api.adminItemList, {params: {page: 1, per_page: 10}})
    .then (function(response) {
      self.isItemLoading = false
      self.tableItemData = []
      if (response.data.meta.status_code === 200) {
        var itemList = response.data.data
        for (var i = 0; i < itemList.length; i++) {
          var item = itemList[i]
          item['item']['status_label'] = '[{0}]{1}'.format(item.item.status, item.item.status_value)
          item['item']['created_at'] = item.item.created_at.date_format().format('yy-MM-dd')
          self.tableItemData.push(item)
        } // endfor
      } else {
        self.$message.error(response.data.meta.message)
      }
    })
    .catch (function(error) {
      self.isItemLoading = false
      self.$message.error(error.message)
    })
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .count-item {
    border-top: 2px solid #ccc;
    background-color: #FAF8F8;
    padding: 10px;
    margin-bottom: 10px;
  }

  .count-item p {
    line-height: 2;
    font-size: 1.5rem;
    color: #666;
  }
  p.count {
    font-size: 1.8rem;
  }
  p.des {
    font-size: 1.2rem;
    color: #222;
  }
  p.title {
    
  }

  .count-item.red {
    border-top: 2px solid red; 
  }
  .count-item.red p.count {
    color: red; 
  }

  .count-item.blue {
    border-top: 2px solid blue; 
  }
  .count-item.blue p.count {
    color: blue; 
  }

  .count-item.green {
    border-top: 2px solid green; 
  }
  .count-item.green p.count {
    color: green; 
  }

  .count-item.yellow {
    border-top: 2px solid #B766AD; 
  }
  .count-item.yellow p.count {
    color: #B766AD; 
  }

  .content-items {
  }
  .content-item {
    min-height: 100px;
  }

  .content-item .form-title {
    font-size: 1.8rem;
    color: #666;
  }


  
  .content-box {
    border: 1px solid #ccc;
    margin-top: 0;
    min-height: 200px;
    padding: 20px 10px;
  }
  .content-box .item {
    height: 60px;
    border-bottom: 1px solid #ccc;
    margin-bottom: 20px;
  }
  
  .content-box .item h3 {
    color: #222;
    font-size: 1.6rem;
    line-height: 2;
  }
  .content-box .item .item-title {
    float: left;
    color: #666;
    font-size: 1.4rem;
    line-height: 1.7;
  }
  .content-box .item .item-btn {
    float: right;
    margin-right: 10px;
    font-size: 1.4rem;
    line-height: 1.7;
  }
  .content-box .item .item-btn a {
    color: #FE3824;
  }

  .no-line {
    border: 0;
    margin-bottom: 0;
  }

  p.alert-title {
    font-size: 1.5rem;
    color: #666;
    margin-bottom: 20px;
    color: red;
  }

  span.green {
    color: green;
    font-size: 1.8rem;
  }

</style>
