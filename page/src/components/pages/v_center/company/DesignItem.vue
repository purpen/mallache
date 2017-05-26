<template>
  <div class="item" v-show="isLoaded">
    <p v-if="isShow()">
      <span>{{ cItem.project_cycle_val }}, 最低{{ cItem.min_price }}起</span>
      <span style="margin-left: 20px;"><a href="javascript:void(0);" @click="submitItem"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></span> <span><a href="javascript:void(0);" @click="delItem"><i class="fa fa-trash" aria-hidden="true"></i></a></span>  
    </p>
    <p v-else><span><a href="javascript:void(0);" @click="submitItem"><i class="fa fa-plus" aria-hidden="true"></i> 添加设置</a></span></p>



    <!--弹框模板-->
    <el-dialog :title="itemModelTitle" v-model="itemModel">
      <div class="model-title">
        <span>{{ pName }}</span>-><span>{{ itemName }}</span>
      </div>
      <el-form :model="form" :rules="ruleForm" ref="ruleForm">
        <el-form-item label="项目平均周期" prop="project_cycle" :label-width="formLabelWidth">
          <el-select v-model.number="form.project_cycle" placeholder="请选择平均周期">
            <el-option
              v-for="item in projectCycleOptions"
              :label="item.label"
              :key="item.index"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="项目最低接单价格" prop="min_price" :label-width="formLabelWidth">
          <el-select v-model.number="form.min_price" placeholder="请选择最低接单价格">
            <el-option
              v-for="item in minPriceOptions"
              :label="item.label"
              :key="item.index"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="cancelFormVisible">取 消</el-button>
        <el-button type="primary" @click="submit('ruleForm')">确 定</el-button>
      </div>
    </el-dialog>

  </div>
</template>

<script>
  import api from '@/api/api'
  import TYPE_DATA from '@/config'
  import '@/assets/js/format'
  export default {
    name: 'vcenter_company_design_item',
    props: {
      pid: {},
      sid: {},
      item: {},
      isLoaded: {
        default: false
      }
    },
    data () {
      return {
        typeData: TYPE_DATA.companyType,
        itemModel: false,
        itemModelTitle: '项目接单设置',
        itemModelPid: '',
        itemId: 0,
        cItem: '',
        pName: '',
        itemName: '',
        projectCycleOptions: [{
          value: 1,
          label: '1个月内'
        }, {
          value: 2,
          label: '1-2个月'
        }, {
          value: 3,
          label: '2-3个月'
        }, {
          value: 4,
          label: '3-4个月'
        }, {
          value: 5,
          label: '5个月以上'
        }],
        form: {
          project_cycle: '',
          min_price: ''
        },
        ruleForm: {
          project_cycle: [
            { type: 'number', message: '请选择项目平均周期', trigger: 'change' }
          ],
          min_price: [
            { type: 'number', message: '请选择最低接单价格', trigger: 'change' }
          ]
        },
        formLabelWidth: '150px'

      }
    },
    methods: {
      isShow() {
        if (this.cItem) {
          this.itemId = this.cItem.id
          return true
        } else {
          this.itemId = 0
          return false
        }
      },
      submitItem() {
        var sid = this.sid
        var pid = this.pid
        if (this.cItem) {
          this.itemId = this.cItem.id
          this.form.project_cycle = this.cItem.project_cycle
          this.form.min_price = this.cItem.min_price
        } else {
          this.itemId = 0
        }

        if (pid === 1) {
          this.pName = '产品设计'
          if (sid === 1) {
            this.itemName = '产品策划'
          } else if (sid === 2) {
            this.itemName = '产品设计'
          } else if (sid === 3) {
            this.itemName = '结构设计'
          }
        } else if (pid === 2) {
          this.pName = 'UI/UX设计'
          if (sid === 1) {
            this.itemName = 'App设计'
          } else if (sid === 2) {
            this.itemName = '网页设计'
          }
        }

        this.itemModel = true
      },
      delItem() {
        if (this.item) {
          this.itemId = this.cItem.id
        } else {
          this.itemId = 0
        }

        const that = this

        var apiUrl = api.designItem.format(that.itemId)
        that.$http({method: 'delete', url: apiUrl, data: {}})
        .then (function(response) {
          if (response.data.meta.status_code === 200) {
            that.$message({
              showClose: true,
              message: '删除成功!',
              type: 'success'
            })
            that.cItem = null
            return false
          } else {
            that.$message({
              showClose: true,
              message: response.data.meta.message,
              type: 'error'
            })
          }
        })
        .catch (function(error) {
          that.$message({
            showClose: true,
            message: error.message,
            type: 'error'
          })
          console.log(error.message)
          return false
        })
      },
      cancelFormVisible() {
        this.itemModel = false
      },
      submit(formName) {
        const that = this
        that.$refs[formName].validate((valid) => {
          // 验证通过，提交
          if (valid) {
            var row = {
              design_type: that.sid,
              type: that.pid,
              project_cycle: that.form.project_cycle,
              min_price: that.form.min_price
            }

            var apiUrl = null
            var method = null

            if (that.itemId === 0) {
              apiUrl = api.saveDesignItem
              method = 'post'
            } else {
              apiUrl = api.designItem.format(that.itemId)
              method = 'put'
            }
            that.$http({method: method, url: apiUrl, data: row})
            .then (function(response) {
              if (response.data.meta.status_code === 200) {
                that.$message({
                  showClose: true,
                  message: '提交成功,等待审核',
                  type: 'success'
                })
                that.cItem = response.data.data
                that.itemId = that.cItem.id
                that.itemModel = false
                return false
              } else {
                that.$message({
                  showClose: true,
                  message: response.data.meta.message,
                  type: 'error'
                })
              }
            })
            .catch (function(error) {
              that.$message({
                showClose: true,
                message: error.message,
                type: 'error'
              })
              console.log(error.message)
              return false
            })

            return false
          } else {
            console.log('error submit!!')
            return false
          }
        })
      }
    },
    computed: {
      minPriceOptions() {
        var arr = []
        for (var i = 1; i <= 30; i++) {
          var item = {}
          item.value = i * 10000
          item.label = i + '万'
          arr.push(item)
        }
        return arr
      }
    },
    created() {
    },
    watch: {
      item(d) {
        console.log(d)
        this.cItem = d
      }
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .model-title {
    margin: 0 0 20px 0;
  }
  .item {
    border-bottom: 1px dotted #ccc;
  }
  .item span {
    line-height: 2;
  }
  .item p span {
    color: #666;
    font-size: 1.5rem;
  }


</style>
