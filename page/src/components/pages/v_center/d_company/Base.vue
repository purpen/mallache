<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="24">
      <v-menu currentName="profile"></v-menu>

      <el-col :span="20">
        <div class="right-content">
          <v-menu-sub></v-menu-sub>

          <div class="content-box" v-loading.body="isLoading">

            <el-row :gutter="gutter" class="item">
              <el-col :span="titleSpan" class="title">
                <p>头像</p>
              </el-col>
              <el-col :span="contentSpan" class="content">

                <el-upload
                  class="avatar-uploader"
                  :action="uploadParam.url"
                  :show-file-list="false"
                  :data="uploadParam"
                  :on-progress="avatarProgress"
                  :on-success="handleAvatarSuccess"
                  :before-upload="beforeAvatarUpload">
                  <img v-if="imageUrl" :src="imageUrl" class="avatar">
                  <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                      <div slot="tip" class="el-upload__tip">{{ avatarStr }}</div>
                </el-upload>

              </el-col>
              <el-col :span="editSpan" class="edit">
              </el-col>
            </el-row>

            <el-row :gutter="gutter" class="item">
              <el-col :span="titleSpan" class="title">
                <p>公司简称</p>
              </el-col>
              <el-col :span="contentSpan" class="content">
                <el-input v-if="element.company_abbreviation" v-model="form.company_abbreviation" style="width: 300px;" placeholder="如: 太火鸟"></el-input>
                <p v-else>{{ form.company_abbreviation }}</p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.company_abbreviation" title="保存" href="javascript:void(0)" @click="saveBtn('company_abbreviation', ['company_abbreviation'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('company_abbreviation')">编辑</a>
              </el-col>
            </el-row>

            <!--
            <el-row :gutter="gutter" class="item">
              <el-col :span="titleSpan" class="title">
                <p>联系人信息</p>
              </el-col>
              <el-col :span="contentSpan" class="content">

                <el-form label-position="left" label-width="50px" style="width: 300px;" v-if="element.contact">
                  <el-form-item label="姓名" style="margin: 0">
                    <el-input v-model="form.contact_name"></el-input>
                  </el-form-item>
                  <el-form-item label="职位" style="margin: 0">
                    <el-input v-model="form.position"></el-input>
                  </el-form-item>
                  <el-form-item label="手机" style="margin: 0">
                    <el-input v-model="form.phone"></el-input>
                  </el-form-item>
                  <el-form-item label="邮箱" style="margin: 0">
                    <el-input v-model="form.email"></el-input>
                  </el-form-item>
                </el-form>

                <div v-else>
                  <p v-show="form.contact_name">{{ form.contact_name }}</p>
                  <p v-show="form.position">{{ form.position }}</p>
                  <p v-show="form.phone">{{ form.phone }}</p>
                  <p v-show="form.email">{{ form.email }}</p>
                </div>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.contact" title="保存" href="javascript:void(0)" @click="saveBtn('contact', ['contact_name', 'phone', 'email', 'position'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('contact')">编辑</a>
              </el-col>
            </el-row>
            -->

            <el-row :gutter="gutter" class="item">
              <el-col :span="titleSpan" class="title">
                <p>地址</p>
              </el-col>
              <el-col :span="contentSpan" class="content">
                <el-form label-position="top" label-width="50px" style="width: 90%;" v-show="element.address">
                  <region-picker :provinceProp="province" :cityProp="city" :districtProp="district" :isFirstProp="isFirst" titleProp="详细地址" propStyle="margin: 0;" @onchange="change"></region-picker>
                  <el-form-item label="" prop="address" style="margin: 0">
                    <el-input v-model="form.address" name="address" ref="address" placeholder="街道地址"></el-input>
                  </el-form-item>
                </el-form>
                <div v-show="!element.address">
                  <p>{{ form.province_value }} {{ form.city_value }} {{ form.area_value }}</p>
                  <p>{{ form.address }}</p>
                </div>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.address" title="保存" href="javascript:void(0)" @click="saveBtn('address', ['province', 'city', 'area', 'address'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('address')">编辑</a>
              </el-col>
            </el-row>

            <el-row :gutter="gutter" class="item">
              <el-col :span="titleSpan" class="title">
                <p>公司类型</p>
              </el-col>
              <el-col :span="contentSpan" class="content">

                <el-select v-model.number="form.company_property" placeholder="请选择" v-if="element.company_property">
                  <el-option
                    v-for="item in propertyOptions"
                    :label="item.label"
                    :key="item.index"
                    :value="item.value">
                  </el-option>
                </el-select>

                <p v-else>{{ form.company_property_value }}</p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.company_property" title="保存" href="javascript:void(0)" @click="saveBtn('company_property', ['company_property'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('company_property')">编辑</a>
              </el-col>
            </el-row>

            <el-row :gutter="gutter" class="item">
              <el-col :span="titleSpan" class="title">
                <p>公司规模</p>
              </el-col>
              <el-col :span="contentSpan" class="content">

                <el-select v-model.number="form.company_size" placeholder="请选择" v-if="element.company_size">
                  <el-option
                    v-for="item in sizeOptions"
                    :label="item.label"
                    :key="item.index"
                    :value="item.value">
                  </el-option>
                </el-select>

                <p v-else>{{ form.company_size_value }}</p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.company_size" title="保存" href="javascript:void(0)" @click="saveBtn('company_size', ['company_size'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('company_size')">编辑</a>
              </el-col>
            </el-row>

            <!--
            <el-row :gutter="gutter" class="item">
              <el-col :span="titleSpan" class="title">
                <p>实名认证</p>
              </el-col>
              <el-col :span="contentSpan" class="content">
                <p>{{ form.verify_status_label }}</p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a href="javascript:void(0)" title="编辑" @click="goVerify">编辑</a>
              </el-col>
            </el-row>
            -->

            <el-row :gutter="gutter" class="item">
              <el-col :span="titleSpan" class="title">
                <p>网址</p>
              </el-col>
              <el-col :span="contentSpan" class="content">

                <el-input v-model="form.company_web" placeholder="" v-if="element.web">
                  <template slot="prepend">http://</template>
                </el-input>

                <p v-else><a :href="form.web" target="_blank">{{ form.web }}</a></p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.web" title="保存" href="javascript:void(0)" @click="saveBtn('web', ['company_web'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('web')">编辑</a>
              </el-col>
            </el-row>


          </div>

        </div>
      </el-col>
    </el-row>

  </div>

  </template>

<script>
  import vMenu from '@/components/pages/v_center/Menu'
  import vMenuSub from '@/components/pages/v_center/d_company/MenuSub'
  // 城市联动
  import RegionPicker from '@/components/block/RegionPicker'
  import api from '@/api/api'
  import '@/assets/js/format'
  import typeData from '@/config'
  import auth from '@/helper/auth'

  export default {
    name: 'vcenter_company_base',
    components: {
      vMenu,
      vMenuSub,
      RegionPicker
    },
    data () {
      return {
        gutter: 0,
        titleSpan: 3,
        contentSpan: 20,
        editSpan: 1,
        isLoaded: false,
        isLoading: false,
        avatarStr: '点击图像上传Logo，只能上传jpg/gif/png文件，且不超过2M',
        isFirst: false,
        companyId: '',
        province: '',
        city: '',
        district: '',
        items: {
        },
        form: {
          company_abbreviation: '',
          company_type: '',
          company_property: '',
          registration_number: '',
          company_web: '',
          company_size: '',
          contact_name: '',
          email: '',
          phone: '',
          position: ''

        },
        element: {
          company_abbreviation: false,
          contact: false,
          address: false,
          company_size: false,
          profile: false,
          web: false,
          company_property: false,

          test: false
        },
        uploadParam: {
          'url': '',
          'token': '',
          'x:random': '',
          'x:user_id': this.$store.state.event.user.id,
          'x:target_id': '',
          'x:type': 0
        },
        imageUrl: '',
        userId: this.$store.state.event.user.id
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
      },
      propertyOptions() {
        var items = []
        for (var i = 0; i < typeData.COMPANY_PROPERTY_TYPE.length; i++) {
          var item = {
            value: typeData.COMPANY_PROPERTY_TYPE[i]['id'],
            label: typeData.COMPANY_PROPERTY_TYPE[i]['name']
          }
          items.push(item)
        }
        return items
      }
    },
    methods: {
      editBtn(mark) {
        if (!mark) {
          return false
        }
        this.element[mark] = true
      },
      saveBtn(mark, nameArr) {
        var that = this
        var row = {}
        for (var i = 0; i < nameArr.length; i++) {
          var name = nameArr[i]
          row[name] = this.form[name]
          if (!row[name]) {
            this.$message.error('请完善您的公司信息！')
            return false
          }
        }
        // 处理网址前缀
        if (mark === 'web' && row['company_web']) {
          var urlRegex = /http:\/\/|https:\/\//
          if (!urlRegex.test(that.form.company_web)) {
            row.company_web = 'http://' + row['company_web']
          }
        }

        that.$http({method: 'POST', url: api.demandCompany, data: row})
        .then (function(response) {
          if (response.data.meta.status_code === 200) {
            that.element[mark] = false
            var item = response.data.data
            if (mark === 'address') {
              that.form.province_value = item.province_value
              that.form.city_value = item.city_value
              that.form.area_value = item.area_value
            } else if (mark === 'company_size') {
              that.form.company_size_value = item.company_size_value
            } else if (mark === 'company_property') {
              that.form.company_property_value = item.company_property_value
            } else if (mark === 'web') {
              that.form.web = row.company_web
              var urlRegex = /http:\/\/|https:\/\//
              if (urlRegex.test(row.company_web)) {
                that.form.company_web = row.company_web.replace(urlRegex, '')
              }
            }
          } else {
            that.$message.error(response.data.meta.message)
          }
        })
        .catch (function(error) {
          that.$message.error(error.message)
        })
      },
      change: function(obj) {
        this.province = this.form.province = obj.province
        this.city = this.form.city = obj.city
        this.district = this.form.area = obj.district
      },
      avatarProgress() {
        this.avatarStr = '上传中...'
      },
      handleAvatarSuccess(res, file) {
        this.imageUrl = URL.createObjectURL(file.raw)
        this.avatarStr = '点击图像上传Logo，只能上传jpg/gif/png文件，且不超过2M'
        // 查询用户表，更新头像到本地
        var that = this
        that.$http.get(api.user, {})
        .then (function(response) {
          if (response.data.meta.status_code === 200) {
            if (response.data.data) {
              auth.write_user(response.data.data)
            }
          }
        })
        .catch (function(error) {
          that.$message.error(error.message)
        })
      },
      beforeAvatarUpload(file) {
        const arr = ['image/jpeg', 'image/gif', 'image/png', 'image/png']
        const isLt2M = file.size / 1024 / 1024 < 2
        this.uploadParam['x:type'] = 7

        if (arr.indexOf(file.type) === -1) {
          this.$message.error('上传头像格式不正确!')
          return false
        }
        if (!isLt2M) {
          this.$message.error('上传头像图片大小不能超过 2MB!')
          return false
        }
      },
      // 去认证
      goVerify() {
        this.$router.push({name: 'vcenterDCompanyIdentification'})
      }
    },
    watch: {
    },
    created: function() {
      var uType = this.$store.state.event.user.type
      // 如果是设计公司，跳到设计公司
      if (uType === 2) {
        this.$router.replace({name: 'vcenterComputerBase'})
        return
      }
      const that = this
      that.isLoading = true
      that.$http.get(api.demandCompany, {})
      .then (function(response) {
        that.isLoading = false
        that.isFirst = true
        if (response.data.meta.status_code === 200) {
          if (response.data.data) {
            // 重新渲染
            that.$nextTick(function() {
              that.form = response.data.data
              that.form.company_size = that.form.company_size === 0 ? '' : that.form.company_size
              that.form.company_property = that.form.company_property === 0 ? '' : that.form.company_property
              that.companyId = response.data.data.id
              that.uploadParam['x:target_id'] = response.data.data.id
              that.province = response.data.data.province === 0 ? '' : response.data.data.province
              that.city = response.data.data.city === 0 ? '' : response.data.data.city
              that.district = response.data.data.area === 0 ? '' : response.data.data.area
              that.form.web = that.form.company_web
              // 处理网址前缀
              if (that.form.company_web) {
                var urlRegex = /http:\/\/|https:\/\//
                if (urlRegex.test(that.form.company_web)) {
                  that.form.company_web = that.form.company_web.replace(urlRegex, '')
                }
              }
              if (response.data.data.logo_image) {
                that.imageUrl = response.data.data.logo_image.logo
              }
              that.form.verify_status_label = ''
              if (that.form.verify_status === 0) {
                that.form.verify_status_label = '待认证'
              } else if (that.form.verify_status === 1) {
                that.form.verify_status_label = '认证通过'
              } else if (that.form.verify_status === 2) {
                that.form.verify_status_label = '认证失败'
              }

              console.log(that.form)
            })
          }
        }
      })
      .catch (function(error) {
        that.isLoading = false
        that.$message.error(error.message)
      })

      // 加载图片token
      that.$http.get(api.upToken, {})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          if (response.data.data) {
            that.uploadParam['token'] = response.data.data.upToken
            that.uploadParam['x:random'] = response.data.data.random
            that.uploadParam.url = response.data.data.upload_url
          }
        }
      })
      .catch (function(error) {
        that.$message.error(error.message)
      })
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .item {
    margin: 5px 0;
    padding: 10px 0;
    border-bottom: 1px solid #ccc;
  }
  .item .el-col {
    padding: 10px 0 10px 0;
  }
  .item .edit {
    padding-left: 10px;
  }

  .title p{
    color: #666;
    font-size: 1.5rem;
  }
  .edit a{
    font-size: 1.2rem;
  }
  .item p {
    line-height: 1.6;
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
    width: 100px;
    height: 100px;
    line-height: 100px;
    text-align: center;
    border: 1px dashed #ccc;
  }
  .avatar {
    width: 100px;
    height: 100px;
    display: block;
  }

</style>
