<template>
  <div class="container">

    <v-progress :baseStep="true" :itemId="form.id" :step="form.stage_status"></v-progress>

    <div ref="content_box">
      <el-row :gutter="18">

        <el-col :span="isMob ? 24 : 19">
          <div class="content">
            <el-form :label-position="labelPosition" :model="form" :rules="ruleForm" ref="ruleForm" label-width="80px">

              <el-form-item label="项目名称" prop="name">
                <el-input v-model="form.name" placeholder="为您的项目取个简短的名称"></el-input>
              </el-form-item>

              <el-form-item label="产品功能或卖点" prop="product_features">
                <el-input type="textarea" :rows="5" v-model="form.product_features"
                          placeholder="详细描述下产品的主要功能，以便于设计服务商了解项目的产品需求。"></el-input>
              </el-form-item>

              <el-form-item label="项目周期" prop="cycle" class="fullwidth">
                <el-select v-model.number="form.cycle" placeholder="请选择项目周期" @change="matchCompany"
                >
                  <el-option
                    v-for="item in cycleOptions"
                    :label="item.label"
                    :key="item.index"
                    :value="item.value">
                  </el-option>
                </el-select>
              </el-form-item>

              <el-form-item label="设计费用预算" prop="design_cost" class="fullwidth">
                <el-select v-model.number="form.design_cost" placeholder="请选择设计费用预算" @change="matchCompany">
                  <el-option
                    v-for="item in costOptions"
                    :label="item.label"
                    :key="item.index"
                    :value="item.value">
                  </el-option>
                </el-select>
              </el-form-item>

              <el-form-item label="所属行业" prop="industry" class="fullwidth">
                <el-select v-model.number="form.industry" placeholder="请选择行业">
                  <el-option
                    v-for="item in industryOptions"
                    :label="item.label"
                    :key="item.index"
                    :value="item.value">
                  </el-option>
                </el-select>
              </el-form-item>

              <region-picker :isEmpty="true" :provinceProp="province" :titleProp="cityTitle" :cityProp="city"
                             :districtProp="district" :twoSelect="true" :isFirstProp="isFirst"
                             @onchange="change" class="fullwidth"></region-picker>

              <el-row :gutter="24">
                <el-col :xs="24" :sm="12" :md="12" :lg="12">
                  <el-form-item label="上传附件及相关参考资料" prop="">
                    <el-upload
                      class="upload-demo clearfix"
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
                      <div slot="tip" class="el-upload__tip">只能上传jpg/png/gif文件，且不超过5M</div>
                    </el-upload>
                  </el-form-item>
                </el-col>
              </el-row>

              <div class="sept"></div>
              <div class="return-btn">
                <a href="javascript:void(0);" @click="returnBtn"><img src="../../../assets/images/icon/return.png"/>&nbsp;&nbsp;返回</a>
              </div>
              <div class="form-btn">
                <el-button type="primary" size="large" :loading="isLoadingBtn" class="is-custom"
                           @click="submit('ruleForm')">保存并继续
                </el-button>
              </div>
              <div class="clear"></div>

            </el-form>


          </div>
        </el-col>
        <el-col :span="isMob ? 24 : 5">
          <div id="right_box" :class="{'fixed': isFixed && !isMob}">
            <div class="slider" v-loading.body="matchLoading">
              <div v-if="matchCount === 0">
                <p class="slide-str error"><img src="../../../assets/images/icon/item_stick_fail.png" width="25"/> 匹配失败
                </p>
                <p class="slide-des error">可能出现的原因：</p>
                <p class="slide-des error">当前项目设计周期太短，无法匹配有效的设计服务供应商，请重新设置项目周期。</p>
                <p class="slide-des error">当前项目设计项目设计服务费预算过低，无法匹配有效的设计服务供应商，请重新设置项目设计服务费。</p>
                <p class="slide-des error">选择当前的城市没有对应的设计公司。</p>
              </div>
              <div v-else-if="matchCount > 0">
                <p class="slide-str success"><img src="../../../assets/images/icon/item_stick.png" width="25"/>
                  {{ matchCount }} 家推荐</p>
                <p class="slide-des">根据您在当前页面填写的项目需求详情，SaaS平台会为您精心筛选，呈现与您的项目需求匹配度最高的设计服务供应商。</p>
              </div>
              <div v-else>
                <p class="slide-str">系统推荐中..</p>
                <p class="slide-des">根据您在当前页面填写的项目需求详情，铟果D³ingo会为您精心筛选，呈现与您的项目需求匹配度最高的设计服务供应商。</p>
              </div>

            </div>

            <div class="slider info">
              <p v-if="!isMob">提示</p>
              <p v-if="isMob" class="warning">提示</p>
              <p>项目需求填写</p>
              <p class="slide-des">为了充分了解企业需求，保证反馈的准确性并最终达成合作，以下信息请务必由企业高层管理人员亲自填写，保证信息的真实准确与完整。</p>
              <div class="blank20"></div>
              <p>项目预算设置</p>
              <p class="slide-des">
                产品研发费用通常是由产品设计、结构设计、硬件开发、样机、模具等费用构成，以普通消费电子产品为例设计费用占到产品研发费用10-20%，设置有竞争力的项目预算，能吸引到实力强的设计公司参与到项目中，建议预算设置到产品研发费用的20-30%。</p>
            </div>
          </div>
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script>
import vProgress from '@/components/pages/item/Progress'
import typeData from '@/config'
import api from '@/api/api'
// 城市联动
import RegionPicker from '@/components/block/RegionPicker'
export default {
  name: 'item_submit_two',
  components: {
    vProgress,
    RegionPicker
  },
  data() {
    return {
      itemId: '',
      isLoadingBtn: false,
      isFirst: false,
      typeSwitch1: false,
      typeSwitch2: false,
      labelPosition: 'top',
      province: '',
      city: '',
      district: '',
      cityTitle: '项目工作地点',
      fileList: [],
      upToken: null,
      uploadUrl: '',
      matchCount: '',
      matchLoading: false,
      isFixed: false,
      uploadParam: {
        token: '',
        'x:random': '',
        'x:user_id': this.$store.state.event.user.id,
        'x:target_id': '',
        'x:type': 4
      },
      form: {
        name: '',
        industry: '',
        product_features: '',
        cycle: '',
        design_cost: '',
        cProducts: []
      },
      ruleForm: {
        name: [{ required: true, message: '请添写项目名称', trigger: 'blur' }],
        product_features: [
          { required: true, message: '请添写产品功能或卖点', trigger: 'blur' }
        ],
        industry: [
          {
            type: 'number',
            required: true,
            message: '请选择所属行业',
            trigger: 'change'
          }
        ],
        cycle: [
          {
            type: 'number',
            required: true,
            message: '请选择项目周期',
            trigger: 'change'
          }
        ],
        design_cost: [
          {
            type: 'number',
            required: true,
            message: '请选择设计费用预算',
            trigger: 'change'
          }
        ]
      },
      msg: ''
    }
  },
  methods: {
    montiorWindow() {
      var currentScroll =
        document.documentElement.scrollTop ||
        window.pageYOffset ||
        document.body.scrollTop
      if (currentScroll > this.scroll) {
        var rObj = document.querySelector('#right_box')
        var l1 = document.querySelector('.container').offsetLeft
        var l2 = document.querySelector('.content').offsetWidth
        var w = rObj.offsetWidth
        rObj.style.left = l1 + l2 + 18 + 'px'
        rObj.style.width = w + 'px'
        this.isFixed = true
      } else {
        this.isFixed = false
      }
    },
    submit(formName) {
      const that = this
      that.$refs[formName].validate(valid => {
        // 验证通过，提交
        if (valid) {
          if (that.province === '') {
            that.$message.error('请选择所在省份!')
            return false
          }
          if (that.city === '') {
            that.$message.error('请选择所在城市')
            return false
          }
          if (that.form.cProducts.length === 0) {
            // that.$message.error('至少添加一项竞品')
            // return false
          }
          if (that.matchCount === 0) {
            // that.$message.error('匹配失败，请重新匹配!')
            // return false
          }
          that.isLoadingBtn = true
          var row = {
            name: that.form.name,
            industry: that.form.industry,
            product_features: that.form.product_features,
            design_cost: that.form.design_cost,
            cycle: that.form.cycle,
            competing_product: [],
            province: that.province,
            city: that.city,
            area: that.district
          }
          if (that.form.stage_status < 2) {
            row.stage_status = 2
          }

          if (that.form.cProducts) {
            for (var i = 0; i < that.form.cProducts.length; i++) {
              // row.competing_product[i] = that.form.cProducts[i]['value']
            }
          }
          var apiUrl = null
          var method = null

          if (that.itemId) {
            method = 'put'
            apiUrl = api.ProductDesignId.format(that.itemId)
          } else {
            method = 'post'
            apiUrl = api.ProductDesignId
          }
          that
            .$http({ method: method, url: apiUrl, data: row })
            .then(function(response) {
              if (response.data.meta.status_code === 200) {
                that.$message.success('提交成功！')
                sessionStorage.setItem('position', 344)
                that.$router.push({
                  name: 'itemSubmitFour',
                  params: { id: response.data.data.item.id }
                })
                return false
              } else {
                that.$message.error(response.data.meta.message)
              }
            })
            .catch(function(error) {
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
    matchCompany() {
      this.matchRequest()
    },
    // 获取已匹配公司数量
    matchRequest() {
      var mRow = {
        item_id: this.itemId,
        type: this.form.type,
        design_type: this.form.design_type,
        cycle: this.form.cycle,
        design_cost: this.form.design_cost,
        province: this.province,
        city: this.city
      }
      const that = this
      that.matchCount = ''
      that.matchLoading = true
      that
        .$http({ url: api.demandMatchingCount, method: 'POST', data: mRow })
        .then(function(response) {
          that.matchLoading = false
          if (response.data.meta.status_code === 200) {
            that.matchCount = response.data.data.count
          } else {
            that.$message.error(response.data.meta.message)
          }
        })
    },
    change: function(obj) {
      this.province = obj.province
      this.city = obj.city
      this.district = obj.district

      this.matchRequest()
    },
    uploadError(err, file, fileList) {
      this.$message({
        message: '文件上传失败!',
        type: 'error',
        duration: 1000
      })
      console.log(err)
    },
    uploadSuccess(response, file, fileList) {},
    handlePreview(file) {
      console.log(file)
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
    beforeUpload(file) {
      const arr = ['image/jpeg', 'image/gif', 'image/png', 'image/pdf']
      const isLt5M = file.size / 1024 / 1024 < 5

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
    returnBtn() {
      sessionStorage.setItem('position', 0)
      this.$router.push({ name: 'itemSubmitTwo', params: { id: this.itemId } })
    }
  },
  computed: {
    // 所属行业下拉选项
    industryOptions() {
      var items = []
      for (var i = 0; i < typeData.INDUSTRY.length; i++) {
        var item = {
          value: typeData.INDUSTRY[i]['id'],
          label: typeData.INDUSTRY[i]['name']
        }
        items.push(item)
      }
      return items
    },
    cycleOptions() {
      var items = []
      for (var i = 0; i < typeData.CYCLE_OPTIONS.length; i++) {
        var item = {
          value: typeData.CYCLE_OPTIONS[i]['id'],
          label: typeData.CYCLE_OPTIONS[i]['name']
        }
        items.push(item)
      }
      return items
    },
    costOptions() {
      var items = []
      for (var i = 0; i < typeData.DESIGN_COST_OPTIONS.length; i++) {
        var item = {
          value: typeData.DESIGN_COST_OPTIONS[i]['id'],
          label: typeData.DESIGN_COST_OPTIONS[i]['name']
        }
        items.push(item)
      }
      return items
    },
    isMob() {
      return this.$store.state.event.isMob
    }
  },
  mounted: function() {
    window.addEventListener('scroll', this.montiorWindow)
    this.scroll = this.$refs.content_box.offsetTop

    window.onresize = () => {
      return (() => {
        this.montiorWindow()
      })()
    }
  },
  created: function() {
    const that = this
    var id = this.$route.params.id
    if (id) {
      that.itemId = id
      that.$http
        .get(api.demandId.format(id), {})
        .then(function(response) {
          that.isFirst = true
          if (response.data.meta.status_code === 200) {
            var row = response.data.data.item
            if (row.type === 2) {
              that.$router.replace({
                name: 'itemSubmitUIThree',
                params: { id: row.id }
              })
            }
            that.form.id = row.id
            that.form.type = row.type
            that.form.design_type = row.design_type
            that.form.name = row.name
            that.form.industry = row.industry === 0 ? '' : row.industry
            that.form.product_features = row.product_features
            that.form.cycle = row.cycle === 0 ? '' : row.cycle
            that.form.design_cost = row.design_cost === 0 ? '' : row.design_cost
            that.form.stage_status = row.stage_status
            that.province = row.province === 0 ? '' : row.province
            that.city = row.city === 0 ? '' : row.city

            that.uploadParam['x:target_id'] = row.id

            that.form.cProducts = []
            if (row.competing_product) {
              for (var i = 0; i < row.competing_product.length; i++) {
                var val = { value: row.competing_product[i] }
                that.form.cProducts[i] = val
              }
            }
            if (response.data.data.item.image) {
              var files = []
              for (i = 0; i < response.data.data.item.image.length; i++) {
                if (i > 5) {
                  break
                }
                var obj = response.data.data.item.image[i]
                var item = {}
                item['response'] = {}
                item['name'] = obj['name']
                item['url'] = obj['small']
                item['response']['asset_id'] = obj['id']
                files.push(item)
              }
              that.fileList = files
            }

            // 获取已匹配公司数量
            that.matchRequest()
            console.log(response.data.data.item)
          } else {
            that.$message.error(response.data.meta.message)
            that.$router.push({ name: 'home' })
          }
        })
        .catch(function(error) {
          that.$message.error(error.message)
          that.$router.push({ name: 'home' })
        })
    }

    // 请求图片上传参数
    that.$http
      .get(api.upToken, {})
      .then(function(response) {
        if (response.data.meta.status_code === 200) {
          if (response.data.data) {
            that.uploadParam['token'] = response.data.data.upToken
            that.uploadParam['x:random'] = response.data.data.random
            that.uploadUrl = response.data.data.upload_url
          }
        } else {
          that.$message.error(response.data.meta.message)
        }
      })
      .catch(function(error) {
        that.$message.error(error.message)
        console.log(error.message)
        return false
      })
  },
  watch: {},
  destroyed() {
    window.removeEventListener('scroll', this.montiorWindow)
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.content {
  padding: 20px;
  border: 1px solid #ccc;
}

#right_box {
  z-index: 9999;
}

.slider {
  background-color: #fff;
  border: 1px solid #ccc;
  text-align: center;
  margin-bottom: 20px;
}

.fixed {
  position: fixed;
  top: 0;
  width: 255px;
  left: 75%;
}

.slider.info {
  text-align: left;
}

.slider p {
  margin: 20px;
}

.slider img {
  vertical-align: bottom;
}

.slider.info p {
  margin: 10px 20px;
}

.slide-img {
  padding-top: 20px;
}

.slide-str {
  font-size: 2rem;
}

.slide-str.success {
  color: #00ac84;
}

.slide-str.error {
  color: #fe3824;
  font-weight: 600;
}

.slide-des {
  color: #666;
  line-height: 1.5;
  font-size: 1rem;
  text-align: left;
}

.slide-des.error {
  color: #fe3824;
  margin-top: 0px;
  margin-bottom: 5px;
}

.form-btn {
  float: right;
}

.competing {
  margin: 10px 0;
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

.slider.info p.warning {
  color: #ffb800;
  font-weight: 600;
  font-size: 2rem;
  width: 76px;
  margin: 20px auto 0;
  height: 25px;
  line-height: 25px;
  background: url('../../../assets/images/icon/warning_2x.png') no-repeat left;
  background-size: contain;
  text-align: right;
}

@media screen and (max-width: 767px) {
  .content {
    border: none;
  }

  .slider {
    padding-bottom: 20px;
  }
}
</style>
