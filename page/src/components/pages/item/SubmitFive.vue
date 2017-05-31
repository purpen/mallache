<template>
  <div class="container">

    <v-progress :checkStep="true"></v-progress>
    <el-row :gutter="24" type="flex" justify="center">

      <el-col :span="19">
        <div class="content">

          <el-table
             v-loading.body="isLoading"
            :data="tableData"
            border
            style="width: 100%">
            <el-table-column
              prop="name"
              label="项目"
              width="180">
            </el-table-column>
            <el-table-column
              prop="key"
              label="需要添写信息"
              width="180">
            </el-table-column>
            <el-table-column
              label="客户添写信息">
                <template scope="scope">
                  <div v-if="scope.row.key === '相关附件'">
                    <p v-for="(d, index) in scope.row.image"><a :href="d.file" target="_blank">{{ d.name }}</a></p>
                  </div>
                  <div v-else>
                    <p>{{ scope.row.value }}</p>
                  </div>
                </template>
            </el-table-column>
          </el-table>

          <div class="sept"></div>
          <div class="return-btn">
              <a href="javascript:void(0);" @click="returnBtn"><img src="../../../assets/images/icon/return.png" />&nbsp;&nbsp;返回</a>
          </div>
          <div class="form-btn">
              <el-button type="primary" size="large" class="is-custom" @click="publish">发布</el-button>
          </div>
          <div class="clear"></div>
        
        </div>
      </el-col>
      <el-col :span="5">
        <div class="slider">
          <p class="slide-img"><img src="../../../assets/images/icon/zan.png" /></p>
          <p class="slide-str">{{ matchCount }} 家推荐</p>
          <p class="slide-des">根据你当前填写的项目需求，系统为你匹配出符合条件的设计公司</p>
        </div>

        <div class="slider info">
          <p>项目需求填写</p>
          <p class="slide-des">为了充分了解企业需求，达成合作，针对以下问题为了保证反馈的准确性，做出客观真实的简述，请务必由高层管理人员亲自填写。</p>
          <div class="blank20"></div>
          <p>项目预算设置</p>
          <p class="slide-des">产品研发费用通常是由产品设计、结构设计、硬件开发、样机、模具等费用构成，以普通消费电子产品为例设计费用占到产品研发费用10-20%，设置有竞争力的项目预算，能吸引到实力强的设计公司参与到项目中，建议预算设置到产品研发费用的20-30%。</p>
        </div>
      </el-col>
    </el-row>

  </div>
</template>

<script>
  import vProgress from '@/components/pages/item/Progress'
  // import typeData from '@/config'
  import api from '@/api/api'
  export default {
    name: 'item_submit_five',
    components: {
      vProgress
    },
    data () {
      return {
        isLoadingBtn: false,
        isLoading: true,
        matchCount: '',
        tableData: [{
          name: '',
          key: '',
          value: ''
        }],
        msg: ''
      }
    },
    methods: {
      publish() {
        const that = this
        that.isLoadingBtn = true
        that.$http({method: 'POST', url: api.release, data: {id: that.itemId}})
        .then (function(response) {
          if (response.data.meta.status_code === 200) {
            that.$router.push({name: 'itemPublish', query: {verify_status: response.data.data.verify_status}})
            return false
          } else {
            that.isLoadingBtn = false
            that.$message.error(response.data.meta.message)
          }
        })
        .catch (function(error) {
          that.$message.error(error.message)
          that.isLoadingBtn = false
          console.log(error.message)
          return false
        })
      },
      returnBtn() {
        this.$router.push({name: 'itemSubmitFour', params: {id: this.itemId}})
      }
    },
    computed: {
    },
    created: function() {
      const that = this
      var id = this.$route.params.id
      if (id) {
        that.itemId = id
        that.$http.get(api.demandId.format(id), {})
        .then (function(response) {
          that.isFirst = true
          that.isLoading = false
          if (response.data.meta.status_code === 200) {
            var row = response.data.data.item
            that.form = row
            var tab = []
            if (row.type === 1) {
              tab = [
                {
                  name: '',
                  key: '项目类型',
                  value: row.type_value
                },
                {
                  name: '',
                  key: '设计类别',
                  value: row.design_type_value
                },
                {
                  name: '',
                  key: '产品领域',
                  value: row.field_value
                },
                {
                  name: '',
                  key: '所属行业',
                  value: row.industry_value
                },
                {
                  name: '',
                  key: '项目功能或卖点',
                  value: row.product_features
                }
              ]
            } else if (row.type === 2) {
              if (row.other_content) {
                row.complete_content.push(row.other_content)
              }

              tab = [
                {
                  name: '',
                  key: '项目类型',
                  value: row.type_value
                },
                {
                  name: '',
                  key: '设计类别',
                  value: row.design_type_value
                },
                {
                  name: '',
                  key: '项目进展阶段',
                  value: row.stage_value
                },
                {
                  name: '',
                  key: '已有项目设计内容',
                  value: row.complete_content.join(',')
                }
              ]
            }

            var itemTab = [
              {
                name: '产品信息',
                key: '项目名称',
                value: row.name
              },
              {
                name: '',
                key: '项目预算',
                value: row.design_cost_value
              },
              {
                name: '',
                key: '项目周期',
                value: row.cycle_value
              },
              {
                name: '',
                key: '项目工作地点',
                value: row.province_value + ', ' + row.city_value
              }
            ]

            var assetFile = [
              {
                name: '',
                key: '相关附件',
                value: '',
                image: row.image
              }
            ]

            var baseTab = [
              {
                name: '公司基本信息',
                key: '公司名称',
                value: row.company_name
              },
              {
                name: '',
                key: '公司规模',
                value: row.company_size_value
              },
              {
                name: '',
                key: '公司网址',
                value: row.company_web
              },
              {
                name: '',
                key: '所在地区',
                value: row.company_province_value + ', ' + row.company_city_value + ', ' + row.company_area_value
              },
              {
                name: '',
                key: '详细地址',
                value: row.address
              },
              {
                name: '',
                key: '联系人',
                value: row.contact_name
              },
              {
                name: '',
                key: '职位',
                value: row.position
              },
              {
                name: '',
                key: '电话',
                value: row.phone
              },
              {
                name: '',
                key: '邮箱',
                value: row.email
              }
            ]

            that.tableData = itemTab.concat(tab.concat(assetFile.concat(baseTab)))

            // 匹配公司数量
            var mRow = {
              type: row.type,
              design_type: row.design_type,
              cycle: row.cycle,
              design_cost: row.design_cost,
              province: row.province,
              city: row.city
            }
            that.$http({url: api.demandMatchingCount.format(that.itemId), method: 'POST', data: mRow})
            .then (function(response) {
              if (response.data.meta.status_code === 200) {
                that.matchCount = response.data.data.count
              }
            })
            console.log(response.data.data.item)
          } else {
            that.$message.error(response.data.meta.message)
            that.$router.push({name: 'home'})
          }
        })
        .catch (function(error) {
          that.$message.error(error.message)
          that.$router.push({name: 'home'})
        })
      }
    },
    watch: {
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .content {
    padding: 20px;
    border: 1px solid #ccc;
  }

  .slider {
    border: 1px solid #ccc;
    height: 250px;
    text-align:center;
  }
  .slider.info {
    height: 350px;
    text-align: left;
  }
  .slider p {
    margin: 25px;
  }
  .slider.info p {
    margin: 10px 20px;
  }
  .form-btn {
    float: right;
  }

  .slide-img {
    padding-top: 20px;
  }
  .slide-img img {
    
  }
  .slide-str {
    font-size: 2rem;
  }
  .slide-des {
    color: #666;
    line-height: 1.5;
    font-size: 1rem;
  }

  .competing {
    margin:10px 0;
  }
  .return-btn {
    float: left;
  }
  .return-btn a img {
    vertical-align: -8px;
  }
  .sept {
    width: 100%;
    margin: 20px 0 20px 0;
    padding: 0;
    border-top: 1px solid #ccc;
  }


</style>
