<template>
  <div class="container">

    <v-progress :companyStep="true"></v-progress>
    <el-row :gutter="24">

      <el-col :span="18">
        <div class="content">
            <el-form :label-position="labelPosition" :model="form" :rules="ruleForm" ref="ruleForm" label-width="80px">

              <el-row :gutter="24">
                <el-col :span="10">
                  <el-form-item label="公司名称" prop="company_name">
                    <el-input v-model="form.company_name" placeholder=""></el-input>
                  </el-form-item> 
                </el-col>
              </el-row>

              <el-row :gutter="24">
                <el-col :span="6">
                <el-form-item label="公司规模" prop="company_size">
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
                <el-col :span="8">
                  <el-form-item label="公司网站" prop="company_web">
                    <el-input v-model="form.company_web" placeholder="http://"></el-input>
                  </el-form-item>
                </el-col>
              </el-row>

              <region-picker :provinceProp="province" :cityProp="city" :districtProp="district" :isFirstProp="isFirst" titleProp="详细地址" @onchange="change"></region-picker>
              <el-form-item label="" prop="address">
                <el-input v-model="form.address" name="address" ref="address" placeholder="请输入公司的详细地址"></el-input>
              </el-form-item>

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

              <div class="sept"></div>
              <div class="return-btn">
                  <a href="javascript:void(0);" @click="returnBtn"><img src="../../../assets/images/icon/return.png" />&nbsp;&nbsp;返回</a>
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
  // 城市联动
  import RegionPicker from '@/components/block/RegionPicker'
  import '@/assets/js/format'
  export default {
    name: 'item_submit_four',
    components: {
      vProgress,
      RegionPicker
    },
    data () {
      return {
        itemId: '',
        isLoadingBtn: false,
        typeSwitch1: false,
        typeSwitch2: false,
        labelPosition: 'top',
        province: '',
        city: '',
        district: '',
        isFirst: false,
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
          stage_status: ''
        },
        ruleForm: {
          company_name: [
            { required: true, message: '请添写公司全称', trigger: 'blur' }
          ],
          company_size: [
            { type: 'number', required: true, message: '请选择公司规模', trigger: 'change' }
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
            if (!that.province) {
              that.$message.error('请选择所在省份')
              return false
            }
            if (!that.city) {
              that.$message.error('请选择所在城市')
              return false
            }
            that.isLoadingBtn = true
            var row = {
              company_name: that.form.company_name,
              company_size: that.form.company_size,
              company_web: that.form.company_web,
              address: that.form.address,
              contact_name: that.form.contact_name,
              phone: that.form.phone,
              email: that.form.email,
              company_province: that.province,
              company_city: that.city,
              company_area: that.district
            }
            if (that.form.stage_status < 3) {
              row.stage_status = 3
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
                that.$router.push({name: 'itemSubmitFive', params: {id: response.data.data.item.id}})
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
      change: function(obj) {
        this.province = obj.province
        this.city = obj.city
        this.district = obj.district
      },
      returnBtn() {
        this.$router.push({name: 'itemSubmitThree', params: {id: this.itemId}})
      }
    },
    computed: {
      companySizeOptions() {
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
    created: function() {
      const that = this
      var id = this.$route.params.id
      if (id) {
        that.itemId = id
        that.$http.get(api.demandId.format(id), {})
        .then (function(response) {
          that.isFirst = true
          if (response.data.meta.status_code === 200) {
            var row = response.data.data.item
            that.form.company_name = row.company_name
            that.form.company_size = row.company_size
            that.form.company_web = row.company_web
            that.form.address = row.address
            that.form.contact_name = row.contact_name
            that.form.phone = row.phone
            that.form.email = row.email
            that.form.stage_status = row.stage_status
            if (row.company_size === 0) {
              that.form.company_size = ''
            }
            that.province = row.company_province === 0 ? '' : row.company_province
            that.city = row.company_city === 0 ? '' : row.company_city
            that.district = row.company_area === 0 ? '' : row.company_area
          } else {
            that.$message.error(response.data.meta.message)
            console.log(response.data.meta.message)
            return false
          }
        })
        .catch (function(error) {
          that.$message.error(error.message)
          console.log(error.message)
          return false
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
  .form-btn {
    float: right;
  }
  .return-btn {
    float: left;
  }
  .return-btn a img {
    vertical-align: -8px;
  }
  .sept {
    width: 100%;
    margin: 20px 0 20px 0;
    padding: 0;
    border-top: 1px solid #ccc;
  }


</style>
