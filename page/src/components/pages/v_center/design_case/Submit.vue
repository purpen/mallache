<template>
  <div class="container">
    <div v-if="!isMob" class="blank20"></div>
    <el-row :gutter="24">
      <v-menu currentName="design_case"></v-menu>

      <el-col :span="isMob ? 24 : 20">
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
                    :label="item.value">{{ item.label }}
                  </el-radio-button>
                </el-radio-group>
              </el-form-item>

              <div v-if="typeSwitch1">
                <el-form-item label="设计类别" prop="design_type">
                  <el-radio-group v-model.number="form.design_type" size="small">
                    <el-radio-button
                      v-for="item in typeDesignOptions"
                      :key="item.index"
                      :label="item.value">{{ item.label }}
                    </el-radio-button>
                  </el-radio-group>
                </el-form-item>
                <el-form-item label="产品领域" prop="field">
                  <el-radio-group v-model.number="form.field" size="small">
                    <el-radio-button
                      v-for="item in fieldOptions"
                      :key="item.index"
                      :label="item.value">{{ item.label }}
                    </el-radio-button>
                  </el-radio-group>
                </el-form-item>
                <el-form-item label="所属行业" prop="industry">
                  <el-radio-group v-model.number="form.industry" size="small">
                    <el-radio-button
                      v-for="item in industryOptions"
                      :key="item.index"
                      :label="item.value">{{ item.label }}
                    </el-radio-button>
                  </el-radio-group>
                </el-form-item>
              </div>

              <div v-if="typeSwitch2">
                <el-form-item label="设计类别" prop="design_type">
                  <el-radio-group v-model.number="form.design_type" size="small">
                    <el-radio-button
                      v-for="item in typeDesignOptions"
                      :key="item.index"
                      :label="item.value">{{ item.label }}
                    </el-radio-button>
                  </el-radio-group>
                </el-form-item>

              </div>


              <el-row :gutter="24">
                <el-col :span="isMob ? 24 : 12">
                  <el-form-item label="标题" prop="title">
                    <el-input v-model="form.title" placeholder=""></el-input>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-row :gutter="24">
                <el-col :span="isMob ? 24 : 4">
                  <el-form-item label="服务客户" prop="customer">
                    <el-input v-model="form.customer" placeholder=""></el-input>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-form-item label="获得奖项" class="fullwidth">
                <el-row>
                  <el-col :xs="24" :sm="6" :md="6" :lg="6">
                    <el-form-item prop="">
                      <el-date-picker
                        key="prize_time"
                        class="fullwidth"
                        v-model="form.prize_time"
                        type="month"
                        placeholder="获奖日期">
                      </el-date-picker>
                    </el-form-item>
                  </el-col>
                  <el-col :xs="24" :sm="6" :md="6" :lg="6">
                    <el-form-item prop="prize">
                      <el-select v-model.number="form.prize" placeholder="所属奖项">
                        <el-option
                          v-for="item in prizeOptions"
                          :label="item.label"
                          :key="item.index"
                          :value="item.value">
                        </el-option>
                      </el-select>
                    </el-form-item>
                  </el-col>
                </el-row>
              </el-form-item>

              <el-form-item label="产品量产">
                <el-radio-group v-model.number="form.mass_production" @change="isProduction">
                  <el-radio class="radio" :label="0">否</el-radio>
                  <el-radio class="radio" :label="1">是</el-radio>
                </el-radio-group>
                <span>&nbsp;&nbsp;&nbsp;</span>
                <el-select v-model.number="form.sales_volume" v-if="!isDisabledProduct" placeholder="销售额">
                  <el-option
                    v-for="item in saleOptions"
                    :label="item.label"
                    :key="item.index"
                    :value="item.value">
                  </el-option>
                </el-select>
              </el-form-item>
              
              <el-form-item label="是否申请专利" class="fullwidth">
                <el-row>
                  <el-col :xs="24" :sm="3" :md="3" :lg="3">
                    <el-radio-group v-model="is_apply" @change="isApplication">
                      <el-radio :label="false">否</el-radio>
                      <el-radio :label="true">是</el-radio>
                    </el-radio-group>
                  </el-col>
                  <el-col :xs="24" :sm="6" :md="6" :lg="6" v-if="is_apply">
                    <el-form-item>
                      <el-date-picker
                        key="patent_time"
                        class="fullwidth"
                        v-model="form.patent_time"
                        type="month"
                        placeholder="选择日期">
                      </el-date-picker>
                    </el-form-item>
                  </el-col>
                  <el-col :xs="24" :sm="6" :md="6" :lg="6" v-if="is_apply">
                    <el-form-item>
                      <el-select v-model.number="form.patent_info" placeholder="选择申请专利类型" 
                        key="patent_info">
                        <el-option
                          v-for="item in patentOptions"
                          :label="item.label"
                          :key="item.index"
                          :value="item.value">
                        </el-option>
                      </el-select>
                    </el-form-item>
                  </el-col>
                </el-row>
              </el-form-item>

              <el-row :gutter="0">
                <el-col :span="24">
                  <el-form-item label="上传图片" prop="">
                    <el-upload
                      class="upload-demo"
                      :action="uploadUrl"
                      :on-preview="handlePreview"
                      :on-remove="handleRemove"
                      :file-list="fileList"
                      :data="uploadParam"
                      :on-progress="uploadProgress"
                      :on-error="uploadError"
                      :on-success="uploadSuccess"
                      :before-upload="beforeUpload"
                      :show-file-list="false"
                      list-type="picture">
                      <el-button size="small" type="primary">点击上传</el-button>
                      <div slot="tip" class="el-upload__tip">{{ uploadMsg }}</div>
                    </el-upload>

                    <div class="file-list">
                      <el-row :gutter="10">
                        <el-col :span="isMob ? 24 : 8" v-for="(d, index) in fileList" :key="index">
                          <el-card :body-style="{ padding: '0px' }" class="item">
                            <div class="image-box">
                              <img :src="d.url">
                            </div>
                            <div class="content">
                              <p>{{ d.name }}</p>
                              <div class="summary-edit" v-if="d.edit">
                                <textarea v-model="d.summary"></textarea>
                              </div>
                              <div class="summary" v-else>
                                <p v-if="d.summary">{{ d.summary }}</p>
                                <p class="image-no-summary" v-else>暂无描述信息</p>
                              </div>
                              <div class="opt" v-if="d.edit">
                                <a href="javascript:void(0);" :item_id="d.response.asset_id" :index="index"
                                   @click="saveAssetSummary">保存</a>
                              </div>
                              <div class="opt" v-else>
                                <el-tooltip class="item" effect="dark" content="删除图片" placement="top">
                                  <a href="javascript:void(0);" :item_id="d.response.asset_id" :index="index"
                                     @click="delAsset"><i class="fa fa-times" aria-hidden="true"></i></a>
                                </el-tooltip>
                                <el-tooltip class="item" effect="dark" content="编辑文字" placement="top">
                                  <a href="javascript:void(0);" :item_id="d.response.asset_id" :index="index"
                                     @click="editAssetBtn"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                </el-tooltip>
                                <el-tooltip class="item" effect="dark" content="设为封面" placement="top">
                                  <a href="javascript:void(0);" :item_id="d.response.asset_id" :index="index"
                                     @click="setCoverBtn"><i
                                    :class="{'fa': true, 'fa-flag': true, 'is-active': parseInt(coverId) === d.response.asset_id ? true : false }"
                                    aria-hidden="true"></i></a>
                                </el-tooltip>
                              </div>
                            </div>
                          </el-card>
                        </el-col>
                      </el-row>
                    </div>

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
                <el-button @click="returnList">取消</el-button>
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
    name: 'vcenter_design_case_submit',
    components: {
      vMenu,
      vMenuSub
    },
    data () {
      return {
        userId: this.$store.state.event.user.id,
        itemId: null,
        isLoadingBtn: false,
        labelPosition: 'top',
        fileList: [],
        uploadUrl: '',
        isDisabledProduct: true,
        is_apply: false,
        typeSwitch1: false,
        typeSwitch2: false,
        uploadParam: {
          'token': '',
          'x:random': '',
          'x:user_id': this.$store.state.event.user.id,
          'x:target_id': '',
          'x:type': 5
        },
        uploadMsg: '只能上传jpg/png文件，且不超过5M',
        imageUrl: '',
        coverId: '',
        form: {
          type: '',
          field: '',
          industry: '',
          title: '',
          design_type: '',
          prize_time: '',
          prize: '',
          patent_time: '',
          patent_info: '',
          customer: '',
          mass_production: 0,
          sales_volume: '',
          cover_id: '',
          profile: ''
        },
        ruleForm: {
          type: [
            {type: 'number', message: '请选择设计类型', trigger: 'change'}
          ],
          design_type: [
            {type: 'number', message: '请选择设计类别', trigger: 'change'}
          ],
          field: [
            {type: 'number', message: '请选择设计领域', trigger: 'change'}
          ],
          industry: [
            {type: 'number', message: '请选择所属行业', trigger: 'change'}
          ],
          title: [
            {required: true, message: '请添写标题', trigger: 'blur'}
          ],
          customer: [
            {required: true, message: '请添写服务客户', trigger: 'blur'}
          ],
          profile: [
            {required: true, message: '请添写案例描述', trigger: 'blur'},
            {min: 10, max: 500, message: '长度在 10 到 500 个字符', trigger: 'blur'}
          ],
          prize: [
            {required: true, type: 'number', message: '请选择获奖名称', trigger: 'blur'}
          ],
          prize_time: [
            {required: true, type: 'date', message: '请选择获奖时间', trigger: 'blur'}
          ],
          patent_info: [
            {required: true, type: 'number', message: '请选择专利类型', trigger: 'blur'}
          ],
          patent_time: [
            {required: true, type: 'date', message: '请选择申请时间', trigger: 'blur'}
          ]
        }
      }
    },
    methods: {
      submit(formName) {
        const that = this
        if (!that.coverId) {
          that.$message.error ('必须设置一张封面图!')
          return false
        }
        that.$refs[formName].validate ((valid) => {
          // 验证通过，提交
          if (valid) {
            let row = {
              type: that.form.type,
              design_type: that.form.design_type,
              field: that.form.field,
              industry: that.form.industry,
              title: that.form.title,
              customer: that.form.customer,
              mass_production: that.form.mass_production,
              sales_volume: that.form.sales_volume,
              profile: that.form.profile
            }
            row.cover_id = that.coverId
            if (that.form.prize_time) {
              that.form.prize_time = that.form.prize_time.format ('yyyy-MM-dd')
            }
            if (that.form.patent_time) {
              that.form.patent_time = that.form.patent_time.format ('yyyy-MM-dd')
            }
            row.prizes = JSON.stringify({time: that.form.prize_time, type: that.form.prize})
            row.patent = JSON.stringify({time: that.form.patent_time, type: that.form.patent_info})
            let apiUrl = null
            let method = null

            if (that.itemId) {
              method = 'put'
              apiUrl = api.designCaseId.format (that.itemId)
            } else {
              method = 'post'
              apiUrl = api.designCase
              if (that.uploadParam['x:random']) {
                row['random'] = that.uploadParam['x:random']
              }
            }
            that.isLoadingBtn = true
            that.$http ({method: method, url: apiUrl, data: row})
              .then (function (response) {
                if (response.data.meta.status_code === 200) {
                  that.$message.success ('提交成功！')
                  that.$router.push ({name: 'vcenterDesignCaseList'})
                  return false
                } else {
                  that.$message.error (response.data.meta.message)
                  that.isLoadingBtn = false
                }
              })
              .catch (function (error) {
                that.$message.error (error.message)
                that.isLoadingBtn = false
                console.log (error.message)
                return false
              })
            return false
          } else {
            console.log ('error submit!!')
            return false
          }
        })
      },
      returnList() {
        this.$router.push ({name: 'vcenterDesignCaseList'})
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
      // 是否量产
      isProduction(val) {
        if (val === 0) {
          this.isDisabledProduct = true
          this.form.sales_volume = null
        } else if (val === 1) {
          this.isDisabledProduct = false
        }
      },
      // 是否申请专利
      isApplication(val) {
        this.is_apply = val
        if (!val) {
          this.form.patent_time = null
          this.form.patent_info = ''
        }
      },
      // 删除附件
      delAsset(event) {
        let id = event.currentTarget.getAttribute ('item_id')
        let index = event.currentTarget.getAttribute ('index')

        const self = this
        self.$http.delete (api.asset.format (id), {})
          .then (function (response) {
            if (response.data.meta.status_code === 200) {
              self.fileList.splice (index, 1)
            } else {
              self.$message.error (response.data.meta.message)
            }
          })
          .catch (function (error) {
            self.$message.error (error.message)
          })
      },
      // 编辑附件
      editAssetBtn(event) {
        // let id = event.currentTarget.getAttribute('item_id')
        let index = event.currentTarget.getAttribute ('index')
        this.fileList[index].edit = true
      },
      // 保存附件描述
      saveAssetSummary(event) {
        let id = event.currentTarget.getAttribute ('item_id')
        let index = event.currentTarget.getAttribute ('index')
        let summary = this.fileList[index].summary
        if (summary === '' || summary === null) {
          this.$message.error ('描述信息不能为空!')
          return false
        }
        const self = this
        self.$http.put (api.updateImageSummary, {asset_id: id, summary: summary})
          .then (function (response) {
            if (response.data.meta.status_code === 200) {
              self.fileList[index].edit = false
            } else {
              self.$message.error (response.data.meta.message)
            }
          })
          .catch (function (error) {
            self.$message.error (error.message)
          })
      },
      // 设置封面图
      setCoverBtn (event) {
        let id = event.currentTarget.getAttribute ('item_id')
        // let index = event.currentTarget.getAttribute('index')
        this.coverId = id
      },
      handleRemove(file, fileList) {
        if (file === null) {
          return false
        }

        let assetId = file.response.asset_id
        const that = this
        that.$http.delete (api.asset.format (assetId), {})
          .then (function (response) {
            if (response.data.meta.status_code === 200) {
            } else {
              that.$message.error (response.data.meta.message)
            }
          })
          .catch (function (error) {
            that.$message.error (error.message)
          })
      },
      handlePreview(file) {
      },
      handleChange(value) {
      },
      uploadError(err, file, fileList) {
        this.uploadMsg = '上传失败'
        this.$message ({
          showClose: true,
          message: '文件上传失败!',
          type: 'error'
        })
        console.log (err)
      },
      uploadProgress(event, file, fileList) {
        this.uploadMsg = '上传中...'
        console.log (event)
      },
      uploadSuccess(response, file, fileList) {
        this.uploadMsg = '只能上传jpg/png文件，且不超过5M'
        let add = fileList[fileList.length - 1]
        let item = {
          name: add.name,
          url: add.url,
          edit: false,
          summary: '',
          response: {
            asset_id: add.response.asset_id
          }
        }
        this.fileList.push (item)
      //        console.log(this.fileList)
      },
      beforeUpload(file) {
        const arr = ['image/jpeg', 'image/gif', 'image/png']
        const isLt5M = file.size / 1024 / 1024 < 5

        if (arr.indexOf (file.type) === -1) {
          this.$message.error ('上传文件格式不正确!')
          return false
        }
        if (!isLt5M) {
          this.$message.error ('上传文件大小不能超过 5MB!')
          return false
        }
      }
    },
    computed: {
      typeOptions() {
        let items = []
        for (let i = 0; i < typeData.COMPANY_TYPE.length; i++) {
          let item = {
            value: typeData.COMPANY_TYPE[i]['id'],
            label: typeData.COMPANY_TYPE[i]['name']
          }
          items.push (item)
        }
        return items
      },
      typeDesignOptions() {
        let items = []
        let index
        if (this.form.type === 1) {
          index = 0
        } else if (this.form.type === 2) {
          index = 1
        }
        for (let i = 0; i < typeData.COMPANY_TYPE[index].designType.length; i++) {
          let item = {
            value: typeData.COMPANY_TYPE[index].designType[i]['id'],
            label: typeData.COMPANY_TYPE[index].designType[i]['name']
          }
          items.push (item)
        }
        return items
      },
      fieldOptions() {
        let items = []
        let index
        if (this.form.type === 1) {
          index = 0
        } else if (this.form.type === 2) {
          index = 1
        }
        for (let i = 0; i < typeData.COMPANY_TYPE[index].field.length; i++) {
          let item = {
            value: typeData.COMPANY_TYPE[index].field[i]['id'],
            label: typeData.COMPANY_TYPE[index].field[i]['name']
          }
          items.push (item)
        }
        return items
      },
      industryOptions() {
        let items = []
        let index
        if (this.form.type === 1) {
          index = 0
        } else if (this.form.type === 2) {
          index = 1
        }
        for (let i = 0; i < typeData.COMPANY_TYPE[index].industry.length; i++) {
          let item = {
            value: typeData.COMPANY_TYPE[index].industry[i]['id'],
            label: typeData.COMPANY_TYPE[index].industry[i]['name']
          }
          items.push (item)
        }
        return items
      },
      prizeOptions() {
        let items = []
        for (let i = 0; i < typeData.DESIGN_CASE_PRICE_OPTIONS.length; i++) {
          let item = {
            value: typeData.DESIGN_CASE_PRICE_OPTIONS[i]['id'],
            label: typeData.DESIGN_CASE_PRICE_OPTIONS[i]['name']
          }
          items.push (item)
        }
        return items
      },
      patentOptions() {
        let items = []
        for (let i = 0; i < typeData.PATENT_FOR_INVENTION.length; i++) {
          let item = {
            value: typeData.PATENT_FOR_INVENTION[i]['id'],
            label: typeData.PATENT_FOR_INVENTION[i]['name']
          }
          items.push (item)
        }
        return items
      },
      saleOptions() {
        let items = []
        for (let i = 0; i < typeData.DESIGN_CASE_SALE_OPTIONS.length; i++) {
          let item = {
            value: typeData.DESIGN_CASE_SALE_OPTIONS[i]['id'],
            label: typeData.DESIGN_CASE_SALE_OPTIONS[i]['name']
          }
          items.push (item)
        }
        return items
      },
      isMob() {
        return this.$store.state.event.isMob
      }
    },
    watch: {
      form: {
        handler: function () {
        },
        deep: true
      }
    },
    created: function () {
      const that = this
      let id = this.$route.params.id
      if (id) {
        that.itemId = id
        that.uploadParam['x:target_id'] = id
        that.$http.get (api.designCaseId.format (id), {})
          .then (function (response) {
            if (response.data.meta.status_code === 200) {
              that.form = response.data.data
              if (that.form.prizes && that.form.prizes.time) {
                that.$set(that.form, 'prize_time', that.form.prizes.time)
                that.$set(that.form, 'prize', that.form.prizes.type)
              }
              if (that.form.patent && that.form.patent.time) {
                that.$set(that, 'is_apply', true)
                that.$set(that.form, 'patent_time', that.form.patent.time)
                that.$set(that.form, 'patent_info', that.form.patent.type)
              } else {
                that.$set(that, 'is_apply', false)
              }
              if (that.form.cover_id) {
                that.coverId = that.form.cover_id
              }
              if (response.data.data.sales_volume === 0) {
                that.form.mass_production = 0
              } else {
                that.form.mass_production = 1
              }
              if (response.data.data.case_image) {
                let files = []
                for (let i = 0; i < response.data.data.case_image.length; i++) {
                  let obj = response.data.data.case_image[i]
                  let item = {}
                  item['response'] = {}
                  item['id'] = obj['id']
                  item['name'] = obj['name']
                  item['url'] = obj['middle']
                  item['summary'] = obj['summary']
                  item['response']['asset_id'] = obj['id']
                  item['edit'] = false
                  files.push (item)
                }
                that.fileList = files
              }
            }
          })
          .catch (function (error) {
            that.$message.error (error.message)
            return false
          })
      } else {
        that.itemId = null
      }

      // 获取图片token
      that.$http.get (api.upToken, {})
        .then (function (response) {
          if (response.data.meta.status_code === 200) {
            if (response.data.data) {
              that.uploadParam['token'] = response.data.data.upToken
              that.uploadParam['x:random'] = response.data.data.random
              that.uploadUrl = response.data.data.upload_url
            }
          }
        })
        .catch (function (error) {
          that.$message ({
            showClose: true,
            message: error.message,
            type: 'error'
          })
          console.log (error.message)
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
    border: 1px dashed #D9D9D9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    }

  .avatar-uploader .el-upload:hover {
    border-color: #20A0FF;
    }

  .avatar-uploader-icon {
    font-size: 28px;
    color: #8C939D;
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

  @media screen and (max-width: 767px) {
    .right-content .content-box {
      border: none;
      border-top: 1px solid #D9D9D9;

      }

    }
</style>
