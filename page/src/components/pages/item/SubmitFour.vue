<template>
  <div class="container">

    <v-progress :companyStep="true" :itemId="form.id" :step="form.stage_status"></v-progress>
    <el-row :gutter="24" type="flex" justify="center">

      <el-col :span="24">
        <div class="content" v-loading="isLoading">
          <el-form :label-position="labelPosition" :model="form" :rules="ruleForm" ref="ruleForm" label-width="80px">
            <div class="input">
              <el-row :gutter="24">
                <el-col :xs="24" :sm="10" :md="10" :lg="10">
                  <el-form-item label="公司名称" prop="company_name">
                    <el-input v-model="form.company_name" placeholder=""></el-input>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-row :gutter="24">
                <el-col :xs="24" :sm="6" :md="6" :lg="6">
                  <el-form-item label="公司规模" prop="company_size"
                                class="fullwidth">
                    <el-select v-model.number="form.company_size" placeholder="请选择">
                      <el-option
                        v-for="item in companySizeOptions"
                        :label="item.label"
                        :key="item.index"
                        :value="item.value">
                      </el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-row :gutter="24">
                <el-col :xs="24" :sm="8" :md="8" :lg="8">
                  <el-form-item label="公司网站" prop="company_web">
                    <el-input v-model="form.company_web" placeholder="">
                      <template slot="prepend">http://</template>
                    </el-input>
                  </el-form-item>
                </el-col>
              </el-row>

              <region-picker :provinceProp="province" :cityProp="city" propStyle="margin:0;" :districtProp="district"
                             :isFirstProp="isFirst" titleProp="详细地址"
                             @onchange="change" class="fullwidth"></region-picker>
              <el-form-item label="" prop="address">
                <el-input v-model="form.address" placeholder="街道地址"></el-input>
              </el-form-item>

              <el-row :gutter="24">
                <el-col :xs="24" :sm="6" :md="6" :lg="6">
                  <el-form-item label="联系人" prop="contact_name">
                    <el-input v-model="form.contact_name" placeholder=""></el-input>
                  </el-form-item>
                </el-col>
                <el-col :xs="24" :sm="6" :md="6" :lg="6">
                  <el-form-item label="职位" prop="position">
                    <el-input v-model="form.position" placeholder=""></el-input>
                  </el-form-item>
                </el-col>
                <el-col :xs="24" :sm="6" :md="6" :lg="6">
                  <el-form-item label="手机" prop="phone">
                    <el-input v-model="form.phone" placeholder=""></el-input>
                  </el-form-item>
                </el-col>
                <el-col :xs="24" :sm="6" :md="6" :lg="6">
                  <el-form-item label="邮箱" prop="email">
                    <el-input v-model="form.email" placeholder=""></el-input>
                  </el-form-item>
                </el-col>
              </el-row>
            </div>
            <div class="sept"></div>
            <div class="return-btn">
              <a href="javascript:void(0);" @click="returnBtn"><img src="../../../assets/images/icon/return.png"/>&nbsp;&nbsp;返回</a>
            </div>
            <div class="form-btn">
              <el-button type="primary" size="large" class="is-custom" :loading="isLoadingBtn"
                         @click="submit('ruleForm')">保存并继续
              </el-button>
            </div>
            <div class="clear"></div>
          </el-form>
        </div>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import vProgress from '@/components/pages/item/Progress'
import typeData from '@/config'
import api from '@/api/api'
// 城市联动
import RegionPicker from '@/components/block/RegionPicker'
import '@/assets/js/format'
export default {
  name: 'item_submit_four',
  components: {
    vProgress,
    RegionPicker
  },
  data() {
    return {
      itemId: '',
      isLoading: false,
      isLoadingBtn: false,
      typeSwitch1: false,
      typeSwitch2: false,
      labelPosition: 'top',
      province: '',
      city: '',
      district: '',
      isFirst: false,
      baseCompany: {},
      matchCount: '',
      form: {
        company_name: '',
        // company_abbreviation: '',
        company_size: '',
        company_web: '',
        company_province: '',
        company_city: '',
        company_area: '',
        address: '',
        contact_name: '',
        phone: '',
        email: '',
        position: '',
        stage_status: ''
      },
      ruleForm: {
        company_name: [{ required: true, message: '请添写公司全称', trigger: 'blur' }],
        company_size: [
          {
            type: 'number',
            required: true,
            message: '请选择公司规模',
            trigger: 'change'
          }
        ],
        address: [{ required: true, message: '请添写公司详细地址', trigger: 'blur' }],
        contact_name: [{ required: true, message: '请添写联系人', trigger: 'blur' }],
        phone: [{ required: true, message: '请添写联系电话', trigger: 'blur' }],
        email: [
          { required: true, message: '请添写联系人邮箱', trigger: 'blur' },
          { type: 'email', message: '请输入正确的邮箱格式', trigger: 'blur' }
        ],
        position: [{ required: true, message: '请添写联系人职位', trigger: 'blur' }]
      },
      msg: ''
    }
  },
  methods: {
    submit(formName) {
      const that = this
      that.$refs[formName].validate(valid => {
        // 验证通过，提交
        if (valid) {
          if (!that.province) {
            that.$message.error('请选择所在省份')
            return false
          }
          if (!that.city) {
            that.$message.error('请选择所在城市')
            return false
          }
          let web = that.form.company_web
          if (that.form.company_web) {
            let urlRegex = /http:\/\/|https:\/\//
            if (!urlRegex.test(that.form.company_web)) {
              web = 'http://' + that.form.company_web
            }
          }
          let row = {
            company_name: that.form.company_name,
            company_size: that.form.company_size,
            company_web: web,
            address: that.form.address,
            contact_name: that.form.contact_name,
            phone: that.form.phone,
            email: that.form.email,
            position: that.form.position,
            company_province: that.province,
            company_city: that.city,
            company_area: that.district
          }
          if (that.form.stage_status < 3) {
            row.stage_status = 3
          }
          let apiUrl = api.demandId.format(that.itemId)
          let method = 'put'

          that.isLoadingBtn = true
          that
            .$http({ method: method, url: apiUrl, data: row })
            .then(function(response) {
              that.isLoadingBtn = false
              if (response.data.meta.status_code === 200) {
                that.$message.success('提交成功！')
                sessionStorage.setItem('position', 516)
                that.$router.push({
                  name: 'itemSubmitFive',
                  params: { id: response.data.data.item.id }
                })
                return false
              } else {
                that.$message.error(response.data.meta.message)
              }
            })
            .catch(function(error) {
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
    // 获取已匹配公司数量
    matchRequest() {},
    change: function(obj) {
      this.province = obj.province
      this.city = obj.city
      this.district = obj.district
    },
    returnBtn() {
      sessionStorage.setItem('position', 172)
      this.$router.push({
        name: 'itemSubmitThree',
        params: { id: this.itemId }
      })
    }
  },
  computed: {
    companySizeOptions() {
      let items = []
      for (let i = 0; i < typeData.COMPANY_SIZE.length; i++) {
        let item = {
          value: typeData.COMPANY_SIZE[i]['id'],
          label: typeData.COMPANY_SIZE[i]['name']
        }
        items.push(item)
      }
      return items
    }
  },
  created: function() {
    this.isLoading = true
    const that = this
    let id = this.$route.params.id
    if (id) {
      that.itemId = id
      that.$http
        .get(api.demandId.format(id), {})
        .then(function(response) {
          that.isFirst = true
          if (response.data.meta.status_code === 200) {
            that.isLoading = false
            let row = response.data.data.item
            let web = row.company_web
            if (web) {
              let urlRegex = /http:\/\/|https:\/\//
              if (urlRegex.test(web)) {
                web = web.replace(urlRegex, '')
              }
            }
            that.form.id = row.id
            that.form.type = row.type
            that.form.design_type = row.design_type
            that.form.company_name = row.company_name
            that.form.company_size = row.company_size
            that.form.company_web = web
            that.form.address = row.address
            that.form.contact_name = row.contact_name
            that.form.phone = row.phone
            that.form.email = row.email
            that.form.position = row.position
            that.form.stage_status = row.stage_status
            if (row.company_size === 0) {
              that.form.company_size = ''
            }
            that.province =
              row.company_province === 0 ? '' : row.company_province
            that.city = row.company_city === 0 ? '' : row.company_city
            that.district = row.company_area === 0 ? '' : row.company_area

            // 如果是第一次添写，获取公司基本信息
            if (that.form.stage_status < 3) {
              that.$http.get(api.demandCompany, {}).then(function(response) {
                if (response.data.meta.status_code === 200) {
                  if (response.data.data) {
                    let bRow = response.data.data
                    let bWeb = bRow.company_web
                    if (bWeb) {
                      let urlRegex = /http:\/\/|https:\/\//
                      if (urlRegex.test(bWeb)) {
                        bWeb = bWeb.replace(urlRegex, '')
                      }
                    }
                    that.form.company_name = bRow.company_name
                    that.form.company_size =
                      bRow.company_size === 0 ? '' : bRow.company_size
                    that.form.company_web = bWeb
                    that.form.address = bRow.address
                    that.form.contact_name = bRow.contact_name
                    that.form.phone = bRow.phone
                    that.form.email = bRow.email
                    that.form.position = bRow.position

                    that.province = bRow.province === 0 ? '' : bRow.province
                    that.city = bRow.city === 0 ? '' : bRow.city
                    that.district = bRow.area === 0 ? '' : bRow.area
                  }
                } else {
                  that.$message.error(response.data.meta.message)
                }
              })
            }
          } else {
            that.$message.error(response.data.meta.message)
            that.$router.push({ name: 'home' })
          }
        })
        .catch(function(error) {
          that.$message.error(error.message)
          that.$router.push({ name: 'home' })
          return false
        })
    }
  },
  watch: {}
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
  text-align: center;
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

.slide-str {
  font-size: 2rem;
}

.slide-des {
  color: #666;
  line-height: 1.5;
  font-size: 1rem;
}

.form-btn {
  float: right;
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

@media screen and (max-width: 767px) {
  .content {
    border: none;
  }

  .content .input {
    padding: 0;
  }
}
</style>
