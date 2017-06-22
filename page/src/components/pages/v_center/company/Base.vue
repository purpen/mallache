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

                <p v-else>{{ form.company_size_val }}</p>
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
                <p>专业优势</p>
              </el-col>
              <el-col :span="contentSpan" class="content">

                <el-input
                  type="textarea"
                  :rows="3"
                  v-if="element.advantage"
                  placeholder="请输入内容"
                  v-model="form.professional_advantage">
                </el-input>

                <p v-else>{{ form.professional_advantage }}</p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.advantage" title="保存" href="javascript:void(0)" @click="saveBtn('advantage', ['professional_advantage'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('advantage')">编辑</a>
              </el-col>
            </el-row>

            <el-row :gutter="gutter" class="item">
              <el-col :span="titleSpan" class="title">
                <p>公司简介</p>
              </el-col>
              <el-col :span="contentSpan" class="content">

                <el-input
                  type="textarea"
                  :rows="3"
                  v-if="element.profile"
                  placeholder="请输入内容"
                  v-model="form.company_profile">
                </el-input>

                <p v-else>{{ form.company_profile }}</p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.profile" title="保存" href="javascript:void(0)" @click="saveBtn('profile', ['company_profile'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('profile')">编辑</a>
              </el-col>
            </el-row>

            <el-row :gutter="gutter" class="item">
              <el-col :span="titleSpan" class="title">
                <p>奖项荣誉</p>
              </el-col>
              <el-col :span="contentSpan" class="content">

                <el-input
                  type="textarea"
                  :rows="3"
                  v-if="element.awards"
                  placeholder="请输入内容"
                  v-model="form.awards">
                </el-input>

                <p v-else>{{ form.awards }}</p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.awards" title="保存" href="javascript:void(0)" @click="saveBtn('awards', ['awards'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('awards')">编辑</a>
              </el-col>
            </el-row>

            <el-row :gutter="gutter" class="item">
              <el-col :span="titleSpan" class="title">
                <p>网址</p>
              </el-col>
              <el-col :span="contentSpan" class="content">

                <el-input v-model="form.web" placeholder="" v-if="element.web">
                  <template slot="prepend">http://</template>
                </el-input>

                <p v-else><a :href="form.web_p" target="_blank">{{ form.web_p }}</a></p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.web" title="保存" href="javascript:void(0)" @click="saveBtn('web', ['web'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('web')">编辑</a>
              </el-col>
            </el-row>

            <el-row :gutter="gutter" class="item">
              <el-col :span="titleSpan" class="title">
                <p>分公司</p>
              </el-col>
              <el-col :span="contentSpan" class="content">

                <div v-if="element.branch">
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
                </div>

                <p v-else>{{ form.branch }}</p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.branch" title="保存" href="javascript:void(0)" @click="saveBtn('branch', ['branch_office'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('branch')">编辑</a>
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
  import vMenuSub from '@/components/pages/v_center/company/MenuSub'
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
        is_branch: false,
        companyId: '',
        province: '',
        city: '',
        district: '',
        items: {
        },
        form: {
          company_abbreviation: '',
          company_type: '',
          branch: '',
          registration_number: '',
          web: '',
          company_size: '',
          branch_office: '',
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
          advantage: false,
          profile: false,
          awards: false,
          web: false,
          branch: false,

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
      }
    },
    methods: {
      editBtn(mark) {
        if (!mark) {
          return false
        }
        this.element[mark] = true
        if (mark === 'address') {
        }
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
        if (mark === 'web' && row['web']) {
          var urlRegex = /http:\/\/|https:\/\//
          if (!urlRegex.test(that.form.web)) {
            row.web = 'http://' + row['web']
          }
        }
        that.$http({method: 'POST', url: api.designCompany, data: row})
        .then (function(response) {
          if (response.data.meta.status_code === 200) {
            that.element[mark] = false
            var item = response.data.data
            if (mark === 'address') {
              that.form.province_value = item.province_value
              that.form.city_value = item.city_value
              that.form.area_value = item.area_value
            } else if (mark === 'company_size') {
              that.form.company_size_val = item.company_size_val
            } else if (mark === 'web') {
              that.form.web_p = row.web
              var urlRegex = /http:\/\/|https:\/\//
              if (urlRegex.test(row.web)) {
                that.form.web = row.web.replace(urlRegex, '')
              }
            } else if (mark === 'branch') {
              that.form.branch = row.web
              if (that.form.branch_office > 0) {
                that.form.branch = '有, ' + that.form.branch_office + '家'
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
        this.uploadParam['x:type'] = 6

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
        this.$router.push({name: 'vcenterComputerIdentification'})
      }
    },
    watch: {
    },
    created: function() {
      var uType = this.$store.state.event.user.type
      // 如果是需求方，跳转到相应页面
      if (uType !== 2) {
        this.$router.replace({name: 'vcenterDComputerBase'})
        return
      }
      const that = this
      that.isLoading = true
      that.$http.get(api.designCompany, {})
      .then (function(response) {
        that.isLoading = false
        that.isFirst = true
        if (response.data.meta.status_code === 200) {
          if (response.data.data) {
            // 重新渲染
            that.$nextTick(function() {
              that.form = response.data.data
              that.form.company_size = that.form.company_size === 0 ? '' : that.form.company_size
              that.companyId = response.data.data.id
              that.uploadParam['x:target_id'] = response.data.data.id
              that.province = response.data.data.province === 0 ? '' : response.data.data.province
              that.city = response.data.data.city === 0 ? '' : response.data.data.city
              that.district = response.data.data.area === 0 ? '' : response.data.data.area
              that.form.web_p = that.form.web
              if (response.data.data.branch_office !== 0) {
                that.is_branch = true
              }
              // 处理网址前缀
              if (that.form.web) {
                var urlRegex = /http:\/\/|https:\/\//
                if (urlRegex.test(that.form.web)) {
                  that.form.web = that.form.web.replace(urlRegex, '')
                }
              }
              that.form.branch = '无'
              if (that.form.branch_office > 0) {
                that.form.branch = '有, ' + that.form.branch_office + '家'
              }
              if (response.data.data.logo_image) {
                that.imageUrl = response.data.data.logo_image.logo
              }
              that.form.verify_status_label = ''
              if (that.form.verify_status === 0) {
                that.form.verify_status_label = '等待认证'
              } else if (that.form.verify_status === 1) {
                that.form.verify_status_label = '认证通过'
              } else if (that.form.verify_status === 2) {
                that.form.verify_status_label = '认证失败'
              }
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
    font-size: 1.3rem;
    color: #FE3824;
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
