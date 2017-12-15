<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <v-menu currentName="modify_pwd"></v-menu>

      <el-col :span="20">
        <div class="right-content">
          <v-menu-sub currentSubName="identification"></v-menu-sub>
          <div class="content-box" v-loading.body="isLoading">
            <div class="form-title">
              <span>企业实名认证</span>
            </div>
            <el-form :label-position="labelPosition" :model="form" :rules="ruleForm" ref="ruleForm" label-width="80px">

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="企业名称" prop="company_name">
                    <el-input v-model="form.company_name" name="company_name" ref="company_name" placeholder="请输入完整的公司名称"></el-input>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-form-item label="证件类型" prop="company_type">
                <el-select v-model.number="form.company_type" style="width: 350px;" placeholder="请选择证件类型">
                  <el-option
                    v-for="(d, index) in certificateTypeOptions"
                    :label="d.label"
                    :key="index"
                    :value="d.value">
                  </el-option>
                </el-select>
              </el-form-item>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="注册号" prop="registration_number">
                    <el-input v-model.number="form.registration_number" placeholder=""></el-input>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="公司法人营业执照" prop="">
                    <el-upload
                      class=""
                      :action="uploadParam.url"
                      :on-preview="handlePreview"
                      :on-remove="handleRemove"
                      :file-list="fileList"
                      :data="uploadParam"
                      :on-error="uploadError"
                      :on-success="uploadSuccess"
                      :before-upload="beforeUpload"
                      list-type="text">
                      <el-button size="small" type="primary">点击上传</el-button>
                      <div slot="tip" class="el-upload__tip">只能上传jpg/pdf文件，且不超过5M</div>
                    </el-upload>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="法人姓名" prop="legal_person">
                    <el-input v-model="form.legal_person" placeholder=""></el-input>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-form-item label="证件类型" prop="document_type">
                <el-select v-model.number="form.document_type" placeholder="请选择证件类型">
                  <el-option
                    v-for="(d, index) in documentTypeOptions"
                    :label="d.label"
                    :key="index"
                    :value="d.value">
                  </el-option>
                </el-select>
              </el-form-item>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="证件号码" prop="document_number">
                    <el-input v-model="form.document_number" placeholder=""></el-input>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="法人证件" prop="">
                    <el-upload
                      class=""
                      :action="uploadParam.url"
                      :on-preview="handlePreview"
                      :on-remove="handleRemove"
                      :file-list="filePersonList"
                      :data="uploadParam"
                      :on-error="uploadError"
                      :on-success="uploadSuccessPerson"
                      :before-upload="beforeUploadPerson"
                      list-type="picture">
                      <el-button size="small" type="primary">点击上传</el-button>
                      <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过5M</div>
                    </el-upload>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-row :gutter="10">
                <el-col :span="6">
                  <el-form-item label="联系人" prop="contact_name">
                    <el-input v-model="form.contact_name" placeholder=""></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="6">
                  <el-form-item label="职位" prop="position">
                    <el-input v-model="form.position" placeholder=""></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="6">
                  <el-form-item label="手机" prop="phone">
                    <el-input v-model="form.phone" placeholder=""></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="6">
                  <el-form-item label="邮箱" prop="email">
                    <el-input v-model="form.email" placeholder=""></el-input>
                  </el-form-item>
                </el-col>
              </el-row>

              <div class="form-btn">
                  <el-button @click="returnBase">返回</el-button>
                  <el-button :loading="isLoadingBtn" class="is-custom" type="primary" @click="submit('ruleForm')">提交审核</el-button>
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
import vMenuSub from '@/components/pages/v_center/account/MenuSub'
import api from '@/api/api'
import typeData from '@/config'

import '@/assets/js/format'

export default {
  name: 'vcenter_company_identification',
  components: {
    vMenu,
    vMenuSub
  },
  data() {
    var checkNumber = (rule, value, callback) => {
      if (!value) {
        return callback(new Error('请添写公司注册号!'))
      }
      setTimeout(() => {
        if (!Number.isInteger(value)) {
          callback(new Error('注册号只能为数字！'))
        } else {
          var len = value.toString().length
          if (len === 15 || len === 18) {
            callback()
          } else {
            callback(new Error('注册号长度应为15或18位'))
          }
        }
      }, 1000)
    }
    return {
      isLoading: false,
      isLoadingBtn: false,
      userId: this.$store.state.event.user.id,
      companyId: '',
      labelPosition: 'top',
      fileList: [],
      filePersonList: [],
      upToken: null,
      uploadParam: {
        url: '',
        token: '',
        'x:random': '',
        'x:user_id': this.$store.state.event.user.id,
        'x:target_id': '',
        'x:type': 0
      },
      imageUrl: '',
      form: {
        company_name: '',
        company_type: '',
        registration_number: '',
        legal_person: '',
        document_number: '',
        document_type: '',
        contact_name: '',
        position: '',
        phone: '',
        email: '',

        test: ''
      },

      ruleForm: {
        company_name: [{ required: true, message: '请添写公司全称', trigger: 'blur' }],
        company_type: [
          {
            type: 'number',
            required: true,
            message: '请添写公司全称',
            trigger: 'change'
          }
        ],
        registration_number: [{ validator: checkNumber, trigger: 'blur' }],
        legal_person: [
          { required: true, message: '请添写法人真实姓名', trigger: 'blur' }
        ],
        document_number: [
          { required: true, message: '请添写法人证件号码', trigger: 'blur' },
          { max: 20, min: 4, message: '证件号码格式不正确', trigger: 'blur' }
        ],
        document_type: [
          {
            type: 'number',
            required: true,
            message: '请选择法人证件类型',
            trigger: 'change'
          }
        ],
        contact_name: [{ required: true, message: '请添写联系人', trigger: 'blur' }],
        phone: [{ required: true, message: '请添写联系电话', trigger: 'blur' }],
        email: [
          { required: true, message: '请添写联系人邮箱', trigger: 'blur' },
          { type: 'email', message: '请输入正确的邮箱格式', trigger: 'blur' }
        ],
        position: [{ required: true, message: '请添写联系人职位', trigger: 'blur' }]
      }
    }
  },
  methods: {
    submit(formName) {
      const that = this
      that.$refs[formName].validate(valid => {
        // 验证通过，提交
        if (valid) {
          var row = {
            registration_number: that.form.registration_number,
            company_name: that.form.company_name,
            company_type: that.form.company_type,
            legal_person: that.form.legal_person,
            document_number: that.form.document_number,
            document_type: that.form.document_type,
            contact_name: that.form.contact_name,
            position: that.form.position,
            email: that.form.email,
            phone: that.form.phone
          }

          if (that.companyId) {
          } else {
            if (that.uploadParam['x:random']) {
              row.random = that.uploadParam['x:random']
            }
          }
          that.isLoadingBtn = true
          that
            .$http({ method: 'POST', url: api.designCompany, data: row })
            .then(function(response) {
              that.isLoadingBtn = false
              if (response.data.meta.status_code === 200) {
                that.$message.success('提交成功,等待审核')
                that.$router.push({ name: 'vcenterComputerAccreditation' })
                return false
              } else {
                that.$message.error(response.data.meta.message)
              }
            })
            .catch(function(error) {
              that.$message.error(error.message)
              return false
            })
        } else {
          return false
        }
      })
    },
    // 返回基本信息页
    returnBase() {
      this.$router.push({ name: 'vcenterComputerBase' })
    },
    handleRemove(file, fileList) {
      if (file === null) {
        return false
      }
      var assetId = file.response.asset_id
      const that = this
      that.$http
        .delete(api.asset.format(assetId), {})
        .then(function(response) {
          if (response.data.meta.status_code === 200) {
          } else {
            that.$message.error(response.data.meta.message)
            return false
          }
        })
        .catch(function(error) {
          that.$message.error(error.message)
          console.log(error.message)
          return false
        })
    },
    handlePreview(file) {
      console.log(file)
    },
    handleChange(value) {
      console.log(value)
    },
    uploadError(err, file, fileList) {
      this.$message.error(err + '附件上传失败!')
    },
    uploadSuccess(response, file, fileList) {},
    uploadSuccessPerson(response, file, fileList) {},
    beforeUpload(file) {
      const arr = ['image/jpeg', 'image/gif', 'image/png', 'application/pdf']
      const isLt5M = file.size / 1024 / 1024 < 5

      this.uploadParam['x:type'] = 3

      console.log(file)
      if (arr.indexOf(file.type) === -1) {
        this.$message.error('上传文件格式不正确!')
        return false
      }
      if (!isLt5M) {
        this.$message.error('上传文件大小不能超过 5MB!')
        return false
      }
    },
    beforeUploadPerson(file) {
      const arr = ['image/jpeg', 'image/gif', 'image/png']
      const isLt5M = file.size / 1024 / 1024 < 5

      this.uploadParam['x:type'] = 10

      console.log(file)
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
    documentTypeOptions() {
      var items = []
      for (var i = 0; i < typeData.DOCUMENT_TYPE.length; i++) {
        var item = {
          value: typeData.DOCUMENT_TYPE[i]['id'],
          label: typeData.DOCUMENT_TYPE[i]['name']
        }
        items.push(item)
      }
      return items
    },
    certificateTypeOptions() {
      var items = []
      for (var i = 0; i < typeData.COMPANY_CERTIFICATE_TYPE.length; i++) {
        var item = {
          value: typeData.COMPANY_CERTIFICATE_TYPE[i]['id'],
          label: typeData.COMPANY_CERTIFICATE_TYPE[i]['name']
        }
        items.push(item)
      }
      return items
    }
  },
  watch: {},
  created: function() {
    var uType = this.$store.state.event.user.type
    // 如果非设计公司，跳到相应页面
    if (uType !== 2) {
      this.$router.replace({ name: 'vcenterDComputerIdentification' })
      return
    }
    const that = this
    that.isLoading = true
    that.$http
      .get(api.designCompany, {})
      .then(function(response) {
        that.isLoading = false
        if (response.data.meta.status_code === 200) {
          if (response.data.data) {
            // 重新渲染
            that.$nextTick(function() {
              that.form = response.data.data
              that.form.registration_number = parseInt(
                that.form.registration_number
              )
              that.form.company_type =
                that.form.company_type === 0 ? '' : that.form.company_type
              that.form.document_type =
                that.form.document_type === 0 ? '' : that.form.document_type
              that.companyId = response.data.data.id
              that.uploadParam['x:target_id'] = response.data.data.id

              if (response.data.data.logo_image) {
                that.imageUrl = response.data.data.logo_image.small
              }
              // 法人营业执照
              if (response.data.data.license_image) {
                var files = []
                for (
                  var i = 0;
                  i < response.data.data.license_image.length;
                  i++
                ) {
                  if (i > 5) {
                    break
                  }
                  var obj = response.data.data.license_image[i]
                  var item = {}
                  item['response'] = {}
                  item['name'] = obj['name']
                  item['url'] = obj['small']
                  item['response']['asset_id'] = obj['id']
                  files.push(item)
                }
                that.fileList = files
              }
              // 法人证件
              if (response.data.data.document_image) {
                var personFiles = []
                for (
                  var j = 0;
                  j < response.data.data.document_image.length;
                  j++
                ) {
                  if (j > 5) {
                    break
                  }
                  var pObj = response.data.data.document_image[j]
                  var personItem = {}
                  personItem['response'] = {}
                  personItem['name'] = pObj['name']
                  personItem['url'] = pObj['small']
                  personItem['response']['asset_id'] = pObj['id']
                  personFiles.push(personItem)
                }
                that.filePersonList = personFiles
              }
            })
          }
        } else {
          that.$message.error(response.data.meta.message)
        }
      })
      .catch(function(error) {
        that.$message.error(error.message)
        that.isLoading = false
        return false
      })

    that.$http
      .get(api.upToken, {})
      .then(function(response) {
        if (response.data.meta.status_code === 200) {
          if (response.data.data) {
            that.uploadParam['token'] = response.data.data.upToken
            that.uploadParam['x:random'] = response.data.data.random
            that.uploadParam.url = response.data.data.upload_url
          }
        } else {
          that.$message.error(response.data.meta.message)
        }
      })
      .catch(function(error) {
        that.$message.error(error.message)
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
</style>
