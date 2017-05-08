<template>
  <div class="container">

    <el-row :gutter="24">
      <el-col :span="5">
        <div class="progress">

<el-steps :space="50" direction="vertical" finish-status="process" :active="1">
  <el-step title="项目创建" description="sdfdsfdsf"></el-step>
  <el-step title="等待设计公司接单"></el-step>
  <el-step title="确认项目报价"></el-step>
</el-steps>


        </div>

      </el-col>

      <el-col :span="19">
        <div class="content">
          <div class="banner">
            <h1>{{ item.item.name }}</h1>
            <p>{{ item.item.status_label }}</p>
          </div>

          <div class="base_info">
            <el-table
              :data="tableData"
              border
              :show-header="false"
              style="width: 100%">
              <el-table-column
                prop="name"
                width="180">
              </el-table-column>
              <el-table-column
                prop="title">
              </el-table-column>
            </el-table>
          </div>
        
        </div>

      </el-col>
    </el-row>

  </div>
</template>

<script>
import api from '@/api/api'
export default {
  name: 'item_show',
  data () {
    return {
      item: '',
      tableData: [],
      msg: ''
    }
  },
  methods: {
  },
  created: function() {
    var id = this.$route.params.id
    if (!id) {
      this.$message.error('缺少请求参数!')
      return
    }
    const self = this
    self.$http.get(api.demandId.format(id), {})
    .then (function(response) {
      if (response.data.meta.status_code === 200) {
        self.item = response.data.data
        switch (self.item.item.status) {
          case 1:
            self['item']['item']['status_label'] = '完善信息'
            break
          case 2:
            self['item']['item']['status_label'] = '等待系统自动匹配'
            break
          case 3:
            self['item']['item']['status_label'] = '等待设计公司接单…'
            break
          default:
            self['item']['item']['status_label'] = '待补充'
        }
        var typeLabel = ''
        if (self.item.item.type === 1) {
          typeLabel = self.item.item.type_value + '/' + self.item.item.design_type_value + '/' + self.item.item.field_value + '/' + self.item.item.industry_value
        } else {
          typeLabel = self.item.item.type_value + '/' + self.item.item.design_type_value
        }

        self.tableData = [{
          name: '项目预算',
          title: self.item.item.design_cost_value
        }, {
          name: '项目类别',
          title: typeLabel
        }, {
          name: '项目周期',
          title: self.item.item.cycle_value
        }, {
          name: '工作地点',
          title: self.item.item.province_value + ', ' + self.item.item.city_value
        }]

        console.log(self.item)
      }
    })
    .catch (function(error) {
      self.$message.error(error.message)
      console.log(error.message)
    })
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .progress {
    height: 500px;
    padding: 20px;
    border: 1px solid #ccc;
  }
  
  .content {
  }
  .banner {
    height: 120px;
    text-align: center;
    background-color: #F8F8F8;
    margin-bottom: 20px;
  }
  .banner h1 {
    padding-top: 30px;
    font-size: 2rem;
    color: #222;
  }
  .banner p {
    font-size: 1rem;
    color: #FF5A5F;
    margin: 10px;
  }
  .base_info {
  }

  .el-step__title.is-finish {
    font-size: 3rem;
  }



</style>

