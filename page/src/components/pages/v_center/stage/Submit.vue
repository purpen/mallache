<template>
  <div class="container">
    <el-row :gutter="24">
      <v-menu></v-menu>

      <el-col :span="20">
        <div class="right-content">
          <v-menu-sub></v-menu-sub>
          <div class="content-box">
            <div class="form-title">
              <span>{{ itemStage }}</span>
            </div>
            <el-form :label-position="labelPosition" :model="form" :rules="ruleForm" ref="ruleForm" label-width="80px">

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="标题" prop="title">
                    <el-input v-model="form.title" placeholder=""></el-input>
                  </el-form-item>           
                </el-col>
              </el-row>

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

              <el-form-item label="描述" prop="content">
                <el-input
                  type="textarea"
                  :rows="10"
                  placeholder="请输入内容"
                  v-model="form.content">
                </el-input>
              </el-form-item>

              <div class="form-btn">
                  <!--<el-button>取消</el-button>-->
                  <el-button type="success" :loading="isLoadingBtn" @click="submit('ruleForm')">提交</el-button>
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
    name: 'vcenter_design_stage_submit',
    components: {
      vMenu,
      vMenuSub
    },
    data () {
      return {
        userId: this.$store.state.event.user.id,
        itemId: null,
        id: null,
        itemStage: '添加项目阶段',
        isLoadingBtn: false,
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
          'x:type': 8
        },
        pickerOptions: {
        },
        imageUrl: '',
        form: {
          title: '',
          design_type: '',
          item_id: '',
          content: ''
        },
        ruleForm: {
          title: [
            { required: true, message: '请添写标题', trigger: 'blur' }
          ],
          content: [
            { required: true, message: '请输入内容', trigger: 'blur' }
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
              title: that.form.title,
              item_id: that.form.item_id,
              content: that.form.content
            }
            var apiUrl = null
            var method = null

            if (that.id) {
              method = 'put'
              apiUrl = api.itemStageId.format(that.id)
            } else {
              method = 'post'
              apiUrl = api.itemStage
              if (that.uploadParam['x:random']) {
                row['random'] = that.uploadParam['x:random']
              }
            }
            that.isLoadingBtn = true
            that.$http({method: method, url: apiUrl, data: row})
            .then (function(response) {
              if (response.data.meta.status_code === 200) {
                that.$message.success('提交成功！')
                that.$router.push({name: 'vcenterCItemShow', params: {id: that.form.item_id}})
                return false
              } else {
                that.$message.error(response.data.meta.message)
                that.isLoadingBtn = false
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
            console.log('error submit!!')
            return false
          }
        })
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
      var itemId = this.$route.params.item_id
      if (itemId) {
        that.form.item_id = itemId
      }
      if (id) {
        that.itemStage = '编辑项目阶段'
        that.id = id
        that.uploadParam['x:target_id'] = id
        that.$http.get(api.itemStageId.format(id), {})
        .then (function(response) {
          if (response.data.meta.status_code === 200) {
            that.form = response.data.data
            if (response.data.data.item_stage_image) {
              var files = []
              for (var i = 0; i < response.data.data.item_stage_image.length; i++) {
                var obj = response.data.data.item_stage_image[i]
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
        that.id = null
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
