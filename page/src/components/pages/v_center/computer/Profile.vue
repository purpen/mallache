<template>
  <div class="container">
    <el-row :gutter="24">
      <v-menu></v-menu>

      <el-col :span="20">
        <div class="right-content">
          <v-menu-sub></v-menu-sub>
          <div class="content-box">
            <div class="form-title" v-loading.body="isLoading">
              <span>基本信息</span>
            </div>
            <el-form :label-position="labelPosition" :model="form" :rules="ruleForm" ref="ruleForm" label-width="80px">

              <el-form-item label="公司Logo" prop="">
                <el-upload
                  class="avatar-uploader"
                  :action="uploadUrl"
                  :show-file-list="false"
                  :data="uploadParam"
                  :on-progress="avatarProgress"
                  :on-success="handleAvatarSuccess"
                  :before-upload="beforeAvatarUpload">
                  <img v-if="imageUrl" :src="imageUrl" class="avatar">
                  <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                      <div slot="tip" class="el-upload__tip">{{ avatarStr }}</div>
                </el-upload>
              </el-form-item>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="企业名称" prop="company_name">
                    <el-input v-model="form.company_name" name="company_name" ref="company_name" placeholder="请输入完整的公司名称"></el-input>
                  </el-form-item>           
                </el-col>
              </el-row>

              <el-form-item label="证件类型" prop="company_type">
                <el-radio-group v-model.number="form.company_type">
                  <el-radio class="radio" :label="1">普通营业执照</el-radio>
                  <el-radio class="radio" :label="2">多证合一营业执照</el-radio>
                </el-radio-group>
              </el-form-item>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="注册号" prop="registration_number">
                    <el-input v-model="form.registration_number" placeholder=""></el-input>
                  </el-form-item>          
                </el-col>
              </el-row>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="公司简称" prop="company_abbreviation">
                    <el-input v-model="form.company_abbreviation" name="company_abbreviation" ref="company_abbreviation" placeholder="如: 太火鸟"></el-input>
                  </el-form-item>          
                </el-col>
              </el-row>

              <el-form-item label="公司规模" prop="company_size">
                <el-select v-model.number="form.company_size" placeholder="请选择">
                  <el-option
                    v-for="item in sizeOptions"
                    :label="item.label"
                    :key="item.index"
                    :value="item.value">
                  </el-option>
                </el-select>
              </el-form-item>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="网址" prop="web">
                    <el-input v-model="form.web" name="web" ref="web" placeholder="http://"></el-input>
                  </el-form-item>          
                </el-col>
              </el-row>

              <el-row :gutter="24">
                <el-form-item label="是否设有分公司" prop="">
                  <el-col :span="2">
                    <el-switch
                      style="line-height:50px;"
                      @change="isBranch"
                      v-model="is_branch"
                      on-text=""
                      off-text="">
                    </el-switch>
                  </el-col>
                  <el-col :span="2" v-show="is_branch">
                      <el-input v-model.number="form.branch_office" :disabled="!is_branch" placeholder=""><template slot="append">家</template></el-input>
                  </el-col>
                </el-form-item>
              </el-row>


              <region-picker :provinceProp="province" :cityProp="city" :districtProp="district" :isFirstProp="isFirst" @onchange="change"></region-picker>
              <el-form-item label="详细地址" prop="address">
                <el-input v-model="form.address" name="address" ref="address" placeholder="请输入公司的详细地址"></el-input>
              </el-form-item>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="公司法人营业执照" prop="">
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

              <el-row :gutter="24">
                <el-col :span="6">
                  <el-form-item label="联系人" prop="contact_name">
                    <el-input v-model="form.contact_name" placeholder=""></el-input>
                  </el-form-item>             
                </el-col>
                <el-col :span="6">
                  <el-form-item label="电话" prop="phone">
                    <el-input v-model="form.phone" placeholder=""></el-input>
                  </el-form-item>             
                </el-col>
                <el-col :span="6">
                  <el-form-item label="邮箱" prop="email">
                    <el-input v-model="form.email" placeholder=""></el-input>
                  </el-form-item>             
                </el-col>
              </el-row>

              <el-form-item label="专业优势" prop="professional_advantage">
                <el-input
                  type="textarea"
                  :rows="3"
                  placeholder="请输入内容"
                  v-model="form.professional_advantage">
                </el-input>
              </el-form-item>

              <el-form-item label="公司简介" prop="company_profile">
                <el-input
                  type="textarea"
                  :rows="5"
                  placeholder="请输入内容"
                  v-model="form.company_profile">
                </el-input>
              </el-form-item>

              <el-form-item label="荣誉奖项" prop="awards">
                <el-input
                  type="textarea"
                  :rows="5"
                  placeholder="请输入内容"
                  v-model="form.awards">
                </el-input>
              </el-form-item>

              <div class="form-btn">
                  <el-button>取消</el-button>
                  <el-button type="success" @click="submit('ruleForm')">提交审核</el-button>
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
  import vMenuSub from '@/components/pages/v_center/computer/MenuSub'
  import api from '@/api/api'
  // 城市联动
  import RegionPicker from '@/components/block/RegionPicker'
  import '@/assets/js/format'
  import typeData from '@/config'

  export default {
    name: 'vcenter_computer_profile',
    components: {
      vMenu,
      vMenuSub,
      RegionPicker
    },
    data () {
      return {
        isLoading: true,
        avatarStr: '只能上传jpg/gif/png文件，且不超过2M',
        userId: this.$store.state.event.user.id,
        is_branch: false,
        companyId: '',
        province: '',
        city: '',
        district: '',
        labelPosition: 'top',
        isFirst: false,
        fileList: [],
        upToken: null,
        uploadUrl: '',
        uploadParam: {
          'token': '',
          'x:random': '',
          'x:user_id': this.$store.state.event.user.id,
          'x:target_id': '',
          'x:type': 0
        },
        imageUrl: '',
        form: {
          company_name: '',
          company_abbreviation: '',
          company_type: '',
          registration_number: '',
          web: '',
          company_size: '',
          branch_office: '',
          contact_name: '',
          email: '',
          phone: ''

        },
        professional_advantage: '',
        company_profile: '',
        awards: '',
        ruleForm: {
          company_name: [
            { required: true, message: '请添写公司全称', trigger: 'blur' }
          ],
          company_abbreviation: [
            { required: true, message: '请添写公司简称', trigger: 'blur' }
          ],
          company_type: [
            { type: 'number', message: '请选择公司规模', trigger: 'change' }
          ],
          registration_number: [
            { required: true, message: '请添写公司注册号', trigger: 'blur' }
          ],
          company_size: [
            { type: 'number', message: '请选择公司规模', trigger: 'change' }
          ],
          branch_office: [
            { type: 'number', message: '必须为数字', trigger: 'blur' }
          ],
          address: [
            { required: true, message: '请添写公司详细地址', trigger: 'blur' }
          ],
          contact_name: [
            { required: true, message: '请添写联系人', trigger: 'blur' }
          ],
          phone: [
            { required: true, message: '请添写联系电话', trigger: 'blur' }
          ],
          email: [
            { required: true, message: '请添写联系人邮箱', trigger: 'blur' },
            { type: 'email', message: '请输入正确的邮箱格式', trigger: 'blur' }
          ],
          professional_advantage: [
            { required: true, message: '请添写专业优势', trigger: 'blur' }
          ],
          company_profile: [
            { required: true, message: '请添写公司简介', trigger: 'blur' }
          ],
          province: [
            { required: true, message: '请添写公司简介', trigger: 'change' }
          ]
        }
      }
    },
    methods: {
      avatarProgress() {
        this.avatarStr = '上传中...'
      },
      isBranch(val) {
        if (val === true) {
          this.is_branch = true
          this.form.branch_office = 1
        } else {
          this.is_branch = false
          this.form.branch_office = 0
        }
      },
      submit(formName) {
        const that = this
        that.$refs[formName].validate((valid) => {
          // 验证通过，提交
          if (valid) {
            if (!that.province) {
              that.$message({
                showClose: true,
                message: '请选择所在省份',
                type: 'error'
              })
              return false
            }
            if (!that.city) {
              that.$message({
                showClose: true,
                message: '请选择所在城市',
                type: 'error'
              })
              return false
            }
            var row = {
              position: '',
              company_type: that.form.company_type,
              registration_number: that.form.registration_number,
              company_name: that.form.company_name,
              company_abbreviation: that.form.company_abbreviation,
              company_size: that.form.company_size,
              branch_office: that.form.branch_office,
              web: that.form.web,
              address: that.form.address,
              contact_name: that.form.contact_name,
              phone: that.form.phone,
              email: that.form.email,
              professional_advantage: that.form.professional_advantage,
              company_profile: that.form.company_profile,
              awards: that.form.awards,
              province: that.province,
              city: that.city,
              area: that.district
            }

            var apiUrl = null
            var method = null

            apiUrl = api.designCompany
            if (that.companyId) {
              method = 'put'
            } else {
              method = 'post'
              if (that.uploadParam['x:random']) {
                row['random'] = that.uploadParam['x:random']
              }
            }
            that.$http({method: method, url: apiUrl, data: row})
            .then (function(response) {
              if (response.data.meta.status_code === 200) {
                that.$message({
                  showClose: true,
                  message: '提交成功,等待审核',
                  type: 'success'
                })
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
        console.log(file)
      },
      change: function(obj) {
        this.province = obj.province
        this.city = obj.city
        this.district = obj.district
      },
      handleChange(value) {
        console.log(value)
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
      handleAvatarSuccess(res, file) {
        this.imageUrl = URL.createObjectURL(file.raw)
        this.avatarStr = '只能上传jpg/gif/png文件，且不超过2M'
      },
      beforeAvatarUpload(file) {
        const arr = ['image/jpeg', 'image/gif', 'image/png', 'image/png']
        const isLt2M = file.size / 1024 / 1024 < 2
        this.uploadParam['x:type'] = 6

        if (arr.indexOf(file.type) === -1) {
          this.$message.error('上传头像格式不正确!')
          return false
        }
        if (!isLt2M) {
          this.$message.error('上传头像图片大小不能超过 2MB!')
          return false
        }
      }
    },
    computed: {
      sizeOptions() {
        var items = []
        for (var i = 0; i < typeData.COMPANY_SIZE.length; i++) {
          var item = {
            value: typeData.COMPANY_SIZE[i]['id'],
            label: typeData.COMPANY_SIZE[i]['name']
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
      that.$http.get(api.designCompany, {})
      .then (function(response) {
        that.isFirst = true
        that.isLoading = false
        if (response.data.meta.status_code === 200) {
          if (response.data.data) {
            // 重新渲染
            console.log(response.data.data)
            that.$nextTick(function() {
              that.form = response.data.data
              that.companyId = response.data.data.id
              that.uploadParam['x:target_id'] = response.data.data.id
              that.province = response.data.data.province
              that.city = response.data.data.city
              that.district = response.data.data.area
              if (response.data.data.branch_office !== 0) {
                that.is_branch = true
              }
              if (response.data.data.logo_image.length !== 0) {
                that.imageUrl = response.data.data.logo_image[0]['small']
              }
              if (response.data.data.license_image) {
                var files = []
                for (var i = 0; i < response.data.data.license_image.length; i++) {
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
            })
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
