<template>
  <div class="container">

    <v-progress></v-progress>
    <el-row :gutter="24">

      <el-col :span="18">
        <div class="content">
            <el-form :label-position="labelPosition" :model="form" :rules="ruleForm" ref="ruleForm" label-width="80px">
              <el-form-item label="设计类型" prop="type">
                <el-radio-group v-model.number="form.type" @change="typeChange">
                  <el-radio-button
                    v-for="(item, index) in typeOptions"
                    :key="index"
                    :label="item.value">{{ item.label }}</el-radio-button>
                </el-radio-group>
              </el-form-item>

              <div v-if="typeSwitch1">
                <el-form-item label="设计类别" prop="design_type">
                  <el-radio-group v-model.number="form.design_type" size="small">
                    <el-radio-button
                      v-for="(item, index) in typeDesignOptions"
                      :key="index"
                      :label="item.value">{{ item.label }}</el-radio-button>
                  </el-radio-group>
                </el-form-item>
                <el-form-item label="产品领域" prop="field">
                  <el-radio-group v-model.number="form.field" size="small">
                    <el-radio-button
                      v-for="(item, index) in fieldOptions"
                      :key="index"
                      :label="item.value">{{ item.label }}</el-radio-button>
                  </el-radio-group>
                </el-form-item>
                <el-form-item label="所属行业" prop="industry">
                  <el-radio-group v-model.number="form.industry" size="small">
                    <el-radio-button
                      v-for="(item, index) in industryOptions"
                      :key="index"
                      :label="item.value">{{ item.label }}</el-radio-button>
                  </el-radio-group>
                </el-form-item>
              </div>

              <div v-if="typeSwitch2">
                <el-form-item label="设计类别" prop="design_type">
                  <el-radio-group v-model.number="form.design_type" size="small">
                    <el-radio-button
                      v-for="(item, index) in typeDesignOptions"
                      :key="index"
                      :label="item.value">{{ item.label }}</el-radio-button>
                  </el-radio-group>
                </el-form-item>

              </div>

              <div class="form-btn">
                  <el-button type="success" class="is-custom" :loading="isLoadingBtn" @click="submit('ruleForm')">保存并继续</el-button>
              </div>
              <div class="clear"></div>
              
            </el-form>

        
        </div>
      </el-col>
      <el-col :span="6">
        <div class="slider">
          <p class="slide-img"><img src="../../../assets/images/icon/zan.png" /></p>
          <p class="slide-str">100家推荐</p>
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
  import typeData from '@/config'
  import api from '@/api/api'
  export default {
    name: 'item_submit_two',
    components: {
      vProgress
    },
    data () {
      return {
        itemId: '',
        isLoadingBtn: false,
        typeSwitch1: false,
        typeSwitch2: false,
        labelPosition: 'top',
        form: {
          type: '',
          design_type: '',
          field: '',
          industry: ''
        },
        ruleForm: {
          type: [
            { type: 'number', message: '请选择设计类型', trigger: 'change' }
          ],
          design_type: [
            { type: 'number', message: '请选择设计类别', trigger: 'change' }
          ],
          field: [
            { type: 'number', message: '请选择设计领域', trigger: 'change' }
          ],
          industry: [
            { type: 'number', message: '请选择所属行业', trigger: 'change' }
          ]
        },
        msg: ''
      }
    },
    methods: {
      submit(formName) {
        const that = this
        that.$refs[formName].validate((valid) => {
          // 验证通过，提交
          if (valid) {
            that.isLoadingBtn = true
            var row = {
              type: that.form.type,
              design_type: that.form.design_type,
              field: that.form.field,
              industry: that.form.industry
            }
            if (that.form.stage_status < 1) {
              row.stage_status = 1
            }
            var apiUrl = null
            var method = null

            if (that.itemId) {
              method = 'put'
              apiUrl = api.demandId.format(that.itemId)
            } else {
              method = 'post'
              apiUrl = api.demand
            }
            that.$http({method: method, url: apiUrl, data: row})
            .then (function(response) {
              if (response.data.meta.status_code === 200) {
                that.$message.success('提交成功！')
                if (response.data.data.item.type === 1) {
                  that.$router.push({name: 'itemSubmitThree', params: {id: response.data.data.item.id}})
                } else if (response.data.data.item.type === 2) {
                  that.$router.push({name: 'itemSubmitUIThree', params: {id: response.data.data.item.id}})
                }
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

            return false
          } else {
            that.isLoadingBtn = false
            console.log('error submit!!')
            return false
          }
        })
      },
      typeChange(d) {
        if (d === 1) {
          this.typeSwitch1 = true
          this.typeSwitch2 = false
        } else if (d === 2) {
          this.typeSwitch2 = true
          this.typeSwitch1 = false
        }
      }
    },
    computed: {
      typeOptions() {
        var items = []
        for (var i = 0; i < typeData.COMPANY_TYPE.length; i++) {
          var item = {
            value: typeData.COMPANY_TYPE[i]['id'],
            label: typeData.COMPANY_TYPE[i]['name']
          }
          items.push(item)
        }
        return items
      },
      typeDesignOptions() {
        var items = []
        var index
        if (this.form.type === 1) {
          index = 0
        } else if (this.form.type === 2) {
          index = 1
        }
        for (var i = 0; i < typeData.COMPANY_TYPE[index].designType.length; i++) {
          var item = {
            value: typeData.COMPANY_TYPE[index].designType[i]['id'],
            label: typeData.COMPANY_TYPE[index].designType[i]['name']
          }
          items.push(item)
        }
        return items
      },
      fieldOptions() {
        var items = []
        var index
        if (this.form.type === 1) {
          index = 0
        } else if (this.form.type === 2) {
          index = 1
        }
        for (var i = 0; i < typeData.COMPANY_TYPE[index].field.length; i++) {
          var item = {
            value: typeData.COMPANY_TYPE[index].field[i]['id'],
            label: typeData.COMPANY_TYPE[index].field[i]['name']
          }
          items.push(item)
        }
        return items
      },
      industryOptions() {
        var items = []
        var index
        if (this.form.type === 1) {
          index = 0
        } else if (this.form.type === 2) {
          index = 1
        }
        for (var i = 0; i < typeData.COMPANY_TYPE[index].industry.length; i++) {
          var item = {
            value: typeData.COMPANY_TYPE[index].industry[i]['id'],
            label: typeData.COMPANY_TYPE[index].industry[i]['name']
          }
          items.push(item)
        }
        return items
      }
    },
    created: function() {
      const that = this
      var id = this.$route.params.id
      if (id) {
        that.itemId = id
        that.$http.get(api.demandId.format(id), {})
        .then (function(response) {
          if (response.data.meta.status_code === 200) {
            var row = response.data.data.item
            that.form.type = row.type
            that.form.design_type = row.design_type
            that.form.field = row.field
            that.form.industry = row.industry
            that.form.industry = row.stage_status
            console.log(response.data.data)
          } else {
            that.$message.error(response.data.meta.message)
            console.log(response.data.meta.message)
            that.$router.push({name: 'home'})
            return false
          }
        })
        .catch (function(error) {
          that.$message.error(error.message)
          console.log(error.message)
          that.$router.push({name: 'home'})
          return false
        })
      } else {
        that.$message.error('缺少请求参数！')
        that.$router.push({name: 'home'})
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


</style>
