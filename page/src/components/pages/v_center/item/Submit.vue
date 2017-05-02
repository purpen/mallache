<template>
  <div class="container">
    <el-row :gutter="24">
      <v-menu></v-menu>

      <el-col :span="20">
        <div class="right-content">
          <v-menu-sub></v-menu-sub>
          <div class="content-box">
            <div class="form-title">
              <span>添加作品案例</span>
            </div>
            <el-form :label-position="labelPosition" :model="form" :rules="ruleForm" ref="ruleForm" label-width="80px">


              <el-form-item label="设计类型" prop="type">
                <el-radio-group v-model.number="form.type" @change="typeChange">
                  <el-radio-button
                    v-for="item in typeOptions"
                    :key="item.index"
                    :label="item.value">{{ item.label }}</el-radio-button>
                </el-radio-group>
              </el-form-item>

              <div v-if="typeSwitch1">
                <el-form-item label="设计类别" prop="design_type">
                  <el-radio-group v-model.number="form.design_type" size="small">
                    <el-radio-button
                      v-for="item in typeDesignOptions"
                      :key="item.index"
                      :label="item.value">{{ item.label }}</el-radio-button>
                  </el-radio-group>
                </el-form-item>
                <el-form-item label="产品领域" prop="field">
                  <el-radio-group v-model.number="form.field" size="small">
                    <el-radio-button
                      v-for="item in fieldOptions"
                      :key="item.index"
                      :label="item.value">{{ item.label }}</el-radio-button>
                  </el-radio-group>
                </el-form-item>
                <el-form-item label="所属行业" prop="industry">
                  <el-radio-group v-model.number="form.industry" size="small">
                    <el-radio-button
                      v-for="item in industryOptions"
                      :key="item.index"
                      :label="item.value">{{ item.label }}</el-radio-button>
                  </el-radio-group>
                </el-form-item>
              </div>

              <div v-if="typeSwitch2">
                <el-form-item label="设计类别" prop="design_type">
                  <el-radio-group v-model.number="form.design_type" size="small">
                    <el-radio-button
                      v-for="item in typeDesignOptions"
                      :key="item.index"
                      :label="item.value">{{ item.label }}</el-radio-button>
                  </el-radio-group>
                </el-form-item>

              </div>


              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="标题" prop="title">
                    <el-input v-model="form.title" placeholder=""></el-input>
                  </el-form-item>           
                </el-col>
              </el-row>

              <el-row :gutter="24">
                <el-col :span="4">
                  <el-form-item label="服务客户" prop="customer">
                    <el-input v-model="form.customer" placeholder=""></el-input>
                  </el-form-item>          
                </el-col>
              </el-row>

              <el-form-item label="获得奖项" prop="">
                <el-date-picker
                  v-model="form.prize_time"
                  type="month"
                  placeholder="获奖日期"
                  :picker-options="pickerOptions">
                </el-date-picker>

                <el-select v-model.number="form.prize" placeholder="所属奖项">
                  <el-option
                    v-for="item in prizeOptions"
                    :label="item.label"
                    :key="item.index"
                    :value="item.value">
                  </el-option>
                </el-select>
              </el-form-item>

              <el-form-item label="产品量产" prop="">
                <el-radio-group v-model.number="form.mass_production" @change="isProduction">
                  <el-radio class="radio" :label="0">否</el-radio>
                  <el-radio class="radio" :label="1">是</el-radio>
                </el-radio-group>
                <span>&nbsp;&nbsp;&nbsp;</span>
                <el-select v-model.number="form.sales_volume" :disabled="isDisabledProduct" placeholder="销售额">
                  <el-option
                    v-for="item in saleOptions"
                    :label="item.label"
                    :key="item.index"
                    :value="item.value">
                  </el-option>
                </el-select>
              </el-form-item>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="上传图片" prop="">
                    <el-upload
                      class="upload-demo"
                      :action="uploadUrl"
                      :on-preview="handlePreview"
                      :on-remove="handleRemove"
                      :file-list="fileList"
                      :data="uploadParam"
                      :on-error="uploadError"
                      :on-success="uploadSuccess"
                      :before-upload="beforeUpload"
                      list-type="picture">
                      <el-button size="small" type="primary">点击上传</el-button>
                      <div slot="tip" class="el-upload__tip">只能上传jpg/pdf文件，且不超过5M</div>
                    </el-upload>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-form-item label="描述" prop="profile">
                <el-input
                  type="textarea"
                  :rows="10"
                  placeholder="请输入内容"
                  v-model="form.profile">
                </el-input>
              </el-form-item>

              <div class="form-btn">
                  <el-button>取消</el-button>
                  <el-button type="success" @click="submit('ruleForm')">提交</el-button>
              </div>
              <div class="clear"></div>
            </el-form>

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
  import '@/assets/js/date_format'
  import typeData from '@/config'

  export default {
    name: 'vcenter_design_case_submit',
    components: {
      vMenu,
      vMenuSub
    },
    data () {
      return {
        userId: this.$store.state.event.user.id,
        itemId: null,
        labelPosition: 'top',
        fileList: [],
        uploadUrl: '',
        isDisabledProduct: true,
        typeSwitch1: false,
        typeSwitch2: false,
        uploadParam: {
          'token': '',
          'x:random': '',
          'x:user_id': this.$store.state.event.user.id,
          'x:target_id': '',
          'x:type': 5
        },
        pickerOptions: {
        },
        imageUrl: '',
        form: {
          type: '',
          field: '',
          industry: '',
          title: '',
          design_type: '',
          prize_time: '',
          prize: '',
          customer: '',
          mass_production: 0,
          sales_volume: '',
          profile: ''
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
          ],
          title: [
            { required: true, message: '请添写标题', trigger: 'blur' }
          ],
          customer: [
            { required: true, message: '请添写服务客户', trigger: 'blur' }
          ],
          profile: [
            { required: true, message: '请添写案例描述', trigger: 'blur' }
          ]
        }
      }
    },
    methods: {
      submit(formName) {
        const that = this
        that.$refs[formName].validate((valid) => {
          // 验证通过，提交
          if (valid) {
            var row = {
              type: that.form.type,
              design_type: that.form.design_type,
              field: that.form.field,
              industry: that.form.industry,
              title: that.form.title,
              customer: that.form.customer,
              prize_time: that.form.prize_time,
              prize: that.form.prize,
              mass_production: that.form.mass_production,
              sales_volume: that.form.sales_volume,
              profile: that.form.profile
            }
            row.prize_time = row.prize_time.format('yyyy-MM-dd')
            var apiUrl = null
            var method = null

            if (that.itemId) {
              method = 'put'
              apiUrl = api.designCaseId.format(that.itemId)
            } else {
              method = 'post'
              apiUrl = api.designCase
              if (that.uploadParam['x:random']) {
                row['random'] = that.uploadParam['x:random']
              }
            }
            that.$http({method: method, url: apiUrl, data: row})
            .then (function(response) {
              if (response.data.meta.status_code === 200) {
                that.$message.success('提交成功！')
                that.$router.push({name: 'vcenterDesignCaseList'})
                return false
              } else {
                that.$message.error(response.data.meta.message)
              }
            })
            .catch (function(error) {
              that.$message.error(error.message)
              console.log(error.message)
              return false
            })

            return false
          } else {
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
      },
      // 是否量产方法
      isProduction(val) {
        if (val === 0) {
          this.isDisabledProduct = true
          this.form.sales_volume = null
        } else if (val === 1) {
          this.isDisabledProduct = false
        }
      },
      handleRemove(file, fileList) {
        if (file === null) {
          return false
        }

        var assetId = file.response.asset_id
        const that = this
        that.$http.delete(api.asset.format(assetId), {})
        .then (function(response) {
          if (response.data.meta.status_code === 200) {
          } else {
            that.$message.error(response.data.meta.message)
            return false
          }
        })
        .catch (function(error) {
          that.$message.error(error.message)
          console.log(error.message)
          return false
        })
      },
      handlePreview(file) {
      },
      handleChange(value) {
      },
      uploadError(err, file, fileList) {
        this.$message({
          showClose: true,
          message: '文件上传失败!',
          type: 'error'
        })
        console.log(err)
      },
      uploadSuccess(response, file, fileList) {
      },
      beforeUpload(file) {
        const arr = ['image/jpeg', 'image/gif', 'image/png', 'image/pdf']
        const isLt5M = file.size / 1024 / 1024 < 5

        if (arr.indexOf(file.type) === -1) {
          this.$message.error('上传文件格式不正确!')
          return false
        }
        if (!isLt5M) {
          this.$message.error('上传文件大小不能超过 5MB!')
          return false
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
      },
      prizeOptions() {
        var items = []
        for (var i = 0; i < typeData.DESIGN_CASE_PRICE_OPTIONS.length; i++) {
          var item = {
            value: typeData.DESIGN_CASE_PRICE_OPTIONS[i]['id'],
            label: typeData.DESIGN_CASE_PRICE_OPTIONS[i]['name']
          }
          items.push(item)
        }
        return items
      },
      saleOptions() {
        var items = []
        for (var i = 0; i < typeData.DESIGN_CASE_SALE_OPTIONS.length; i++) {
          var item = {
            value: typeData.DESIGN_CASE_SALE_OPTIONS[i]['id'],
            label: typeData.DESIGN_CASE_SALE_OPTIONS[i]['name']
          }
          items.push(item)
        }
        return items
      }
    },
    watch: {
    },
    created: function() {
      const that = this
      var id = this.$route.params.id
      if (id) {
        that.itemId = id
        that.uploadParam['x:target_id'] = id
        that.$http.get(api.designCaseId.format(id), {})
        .then (function(response) {
          if (response.data.meta.status_code === 200) {
            that.form = response.data.data
            if (response.data.data.sales_volume === 0) {
              that.form.mass_production = 0
            } else {
              that.form.mass_production = 1
            }
            if (response.data.data.case_image) {
              var files = []
              for (var i = 0; i < response.data.data.case_image.length; i++) {
                var obj = response.data.data.case_image[i]
                var item = {}
                item['response'] = {}
                item['name'] = obj['name']
                item['url'] = obj['small']
                item['response']['asset_id'] = obj['id']
                files.push(item)
              }
              that.fileList = files
            }

            console.log(response.data.data)
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
      } else {
        that.itemId = null
      }

      // 获取图片token
      that.$http.get(api.upToken, {})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          if (response.data.data) {
            that.uploadParam['token'] = response.data.data.upToken
            that.uploadParam['x:random'] = response.data.data.random
            that.uploadUrl = response.data.data.upload_url
          }
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
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .form-btn {
    float: right;
  }
  .form-btn button {
    width: 120px;
  }

  .avatar-uploader .el-upload {
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }
  .avatar-uploader .el-upload:hover {
    border-color: #20a0ff;
  }
  .avatar-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 178px;
    height: 178px;
    line-height: 178px;
    text-align: center;
  }
  .avatar {
    width: 178px;
    height: 178px;
    display: block;
  }


</style>
