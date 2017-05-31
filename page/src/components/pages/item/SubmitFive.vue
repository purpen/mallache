<template>
  <div class="container">

    <v-progress :checkStep="true" :itemId="form.id" :step="form.stage_status"></v-progress>
    <el-row :gutter="24" type="flex" justify="center">

      <el-col :span="24">
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

    </el-row>

    <el-dialog
      title="提示"
      v-model="comfirmDialog"
      size="tiny">
      <span>{{ comfirmMessage }}</span>
      <span slot="footer" class="dialog-footer">
        <input type="hidden" ref="comfirmType" value="1" />
        <el-button @click="comfirmDialog = false">取 消</el-button>
        <el-button type="primary" :loading="comfirmLoadingBtn" @click="sureComfirmSubmit">确 定</el-button>
      </span>
    </el-dialog>

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
        comfirmDialog: false,
        comfirmMessage: '确认执行此操作？',
        comfirmLoadingBtn: false,
        form: {},
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
        if (this.matchCount === 0) {
          this.comfirmMessage = '您添写的信息没有匹配到合适的设计公司，确认发布？'
          this.comfirmDialog = true
        } else {
          this.publishSubmit()
        }
      },
      sureComfirmSubmit() {
        this.publishSubmit()
      },
      publishSubmit() {
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
  .content .input {
    padding: 0 150px;
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
  .return-btn a {
    font-size: 2rem;
  }
  .return-btn a img {
    vertical-align: -5px;
  }
  .sept {
    width: 100%;
    margin: 20px 0 20px 0;
    padding: 0;
    border-top: 1px solid #ccc;
  }


</style>
