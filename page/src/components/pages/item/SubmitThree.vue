<template>
  <div class="container">

    <v-progress :baseStep="true"></v-progress>
    <el-row :gutter="24">

      <el-col :span="18">
        <div class="content">
            <el-form :label-position="labelPosition" :model="form" :rules="ruleForm" ref="ruleForm" label-width="80px">

              <el-form-item label="项目名称" prop="name">
                <el-input v-model="form.name" placeholder="为你的项目取个简短的名称"></el-input>
              </el-form-item> 

              <el-form-item label="产品功能或卖点" prop="product_features">
                <el-input type="textarea" :rows="5" v-model="form.product_features" placeholder="详细描述下产品的主要功能，以便于设计服务商了解项目的产品需求。"></el-input>
              </el-form-item> 

              <el-form-item label="项目周期" prop="cycle">
                <el-select v-model.number="form.cycle" placeholder="请选择项目周期">
                  <el-option
                    v-for="item in cycleOptions"
                    :label="item.label"
                    :key="item.index"
                    :value="item.value">
                  </el-option>
                </el-select>
              </el-form-item>

              <el-form-item label="设计费用预算" prop="design_cost">
                <el-select v-model.number="form.design_cost" placeholder="请选择设计费用预算">
                  <el-option
                    v-for="item in costOptions"
                    :label="item.label"
                    :key="item.index"
                    :value="item.value">
                  </el-option>
                </el-select>
              </el-form-item>

              <region-picker :provinceProp="province" :titleProp="cityTitle" :cityProp="city" :districtProp="district" :twoSelect="true" :isFirstProp="isFirst" @onchange="change"></region-picker>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="上传附件及相关参考资料" prop="">
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
                      <div slot="tip" class="el-upload__tip">只能上传jpg/png/gif文件，且不超过5M</div>
                    </el-upload>
                  </el-form-item>
                </el-col>
              </el-row>

              <div class="sept"></div>
              <div class="return-btn">
                  <a href="javascript:void(0);" @click="returnBtn"><img src="../../../assets/images/icon/return.png" />&nbsp;&nbsp;返回</a>
              </div>
              <div class="form-btn">
                  <el-button type="success" :loading="isLoadingBtn" class="is-custom" @click="submit('ruleForm')">保存并继续</el-button>
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
  export default {
    name: 'item_submit_two',
    components: {
      vProgress,
      RegionPicker
    },
    data () {
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
        uploadParam: {
          'token': '',
          'x:random': '',
          'x:user_id': this.$store.state.event.user.id,
          'x:target_id': '',
          'x:type': 4
        },
        form: {
          name: '',
          product_features: '',
          cycle: '',
          design_cost: '',
          cProducts: []
        },
        ruleForm: {
          name: [
            { required: true, message: '请添写项目名称', trigger: 'blur' }
          ],
          product_features: [
            { required: true, message: '请添写产品功能或卖点', trigger: 'blur' }
          ],
          cycle: [
            { type: 'number', required: true, message: '请选择项目周期', trigger: 'change' }
          ],
          design_cost: [
            { type: 'number', required: true, message: '请选择设计费用预算', trigger: 'change' }
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
              that.$message.error('请选择所在省份!')
              return false
            }
            if (!that.city) {
              that.$message.error('请选择所在城市')
              return false
            }
            if (that.form.cProducts.length === 0) {
              // that.$message.error('至少添加一项竞品')
              // return false
            }
            that.isLoadingBtn = true
            var row = {
              name: that.form.name,
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
            that.$http({method: method, url: apiUrl, data: row})
            .then (function(response) {
              if (response.data.meta.status_code === 200) {
                that.$message.success('提交成功！')
                that.$router.push({name: 'itemSubmitFour', params: {id: response.data.data.item.id}})
                return false
              } else {
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
      handlePreview(file) {
        console.log(file)
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
        this.$router.push({name: 'itemSubmitTwo', params: {id: this.itemId}})
      }
    },
    computed: {
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
            if (row.type === 2) {
              that.$router.push({name: 'itemSubmitUIThree', params: {id: row.id}})
            }
            that.form.name = row.name
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
                var val = {value: row.competing_product[i]}
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
            console.log(response.data.data.item)
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

      // 请求图片上传参数
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
        that.$message.error(error.message)
        console.log(error.message)
        return false
      })
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

  .competing {
    margin:10px 0;
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
