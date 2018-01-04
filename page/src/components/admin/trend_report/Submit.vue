<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <v-menu selectedName="trendReportList"></v-menu>

      <el-col :span="20">
        <div class="content">

        <div class="admin-menu-sub">
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminTrendReportList'}" active-class="false" :class="{'item': true, 'is-active': menuType == 0}">全部</router-link>
          </div>
          <div class="fr">
            <router-link :to="{name: 'adminTrendReportAdd'}" class="item add"><i class="el-icon-plus"></i> 添加</router-link>
          </div>
        </div>

          <div class="content-box">
            <div class="form-title">
              <span>{{ itemMode }}</span>
            </div>
            <el-form label-position="top" :model="form" :rules="ruleForm" ref="ruleForm" label-width="80px">

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="标题" prop="title">
                    <el-input v-model="form.title" placeholder=""></el-input>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="标签" prop="tag">
                    <el-input v-model="form.tag" placeholder="多个标签用','分隔"></el-input>
                    <div class="description">*多个标签用','分隔,每个标签不超过7个字符，尽量避免使用特殊字符。</div>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-row >
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
                        <el-col :span="8" v-for="(d, index) in fileList" :key="index">
                          <el-card :body-style="{ padding: '0px' }" class="item">
                            <div class="image-box">
                                <img :src="d.url">
                            </div>
                            <div class="content">
                              <p>{{ d.name }}</p>
                              <div class="opt">
                                <el-tooltip class="item" effect="dark" content="删除图片" placement="top">
                                  <a href="javascript:void(0);" :item_id="d.response.asset_id" :index="index" @click="delAsset"><i class="fa fa-times" aria-hidden="true"></i></a>
                                </el-tooltip>
                                <el-tooltip class="item" effect="dark" content="设为封面" placement="top">
                                <a href="javascript:void(0);" :item_id="d.response.asset_id" :index="index" @click="setCoverBtn"><i :class="{'fa': true, 'fa-flag': true, 'is-active': parseInt(coverId) === d.response.asset_id ? true : false }" aria-hidden="true"></i></a>
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

              <el-row >
                <el-col :span="24">
                  <el-form-item label="上传PDF" prop="">
                    <el-upload
                      class=""
                      :action="uploadUrl"
                      :on-preview="handlePreview"
                      :on-remove="handleRemove"
                      :file-list="filePdfList"
                      :data="uploadParam"
                      :on-progress="uploadPdfProgress"
                      :on-error="uploadPdfError"
                      :on-success="uploadPdfSuccess"
                      :before-upload="beforePdfUpload"
                      :show-file-list="true"
                      list-type="text">
                      <el-button size="small" type="primary">点击上传</el-button>
                      <div slot="tip" class="el-upload__tip">{{ uploadPdfMsg }}</div>
                    </el-upload>

<!--
                    <div class="file-list">
                      <el-row :gutter="10">
                        <el-col :span="8" v-for="(d, index) in filePdfList" :key="index">
                          <el-card :body-style="{ padding: '0px' }" class="item">
                            <div class="image-box">
                                <img :src="d.url">
                            </div>
                            <div class="content">
                              <p>{{ d.name }}</p>
                              <div class="opt">
                                <el-tooltip class="item" effect="dark" content="删除PDF" placement="top">
                                  <a href="javascript:void(0);" :item_id="d.response.asset_id" :index="index" @click="delAsset"><i class="fa fa-times" aria-hidden="true"></i></a>
                                </el-tooltip>
                                <el-tooltip class="item" effect="dark" content="设为主要" placement="top">
                                <a href="javascript:void(0);" :item_id="d.response.asset_id" :index="index" @click="setPdfBtn"><i :class="{'fa': true, 'fa-flag': true, 'is-active': parseInt(coverId) === d.response.asset_id ? true : false }" aria-hidden="true"></i></a>
                                </el-tooltip>
                              </div>
                            </div>
                          </el-card>
                        </el-col>
                      </el-row>
                    </div>
                    -->

                  </el-form-item>

                </el-col>
              </el-row>

              <el-form-item label="描述" prop="content">
                <el-input
                  type="textarea"
                  :rows="5"
                  placeholder="请输入内容"
                  v-model="form.summary">
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
import api from '@/api/api'
import vMenu from '@/components/admin/Menu'
import typeData from '@/config'
export default {
  name: 'admin_trend_report_submit',
  components: {
    vMenu
  },
  data () {
    return {
      menuType: 0,
      itemMode: '添加趋势报告',
      isLoading: false,
      isLoadingBtn: false,
      uploadUrl: '',
      uploadParam: {
        'token': '',
        'x:random': '',
        'x:user_id': this.$store.state.event.user.id,
        'x:target_id': '',
        'x:type': 0
      },
      uploadMsg: '只能上传jpg/png文件，且不超过2M',
      uploadPdfMsg: '只能上传pdf文件，且不超过10M',
      pickerOptions: {
      },
      imageUrl: '',
      coverId: '',
      pdfId: '',
      fileList: [],
      filePdfList: [],
      form: {
        title: '',
        summary: '',
        tag: ''
      },
      ruleForm: {
        title: [
          { required: true, message: '请添写标题', trigger: 'blur' }
        ]
      },
      // 上一页信息
      beforeRoute: {
        name: null,
        query: {}
      },
      msg: ''
    }
  },
  methods: {
    submit(formName) {
      const that = this
      if (!that.coverId) {
        that.$message.error('请设置一张封面图')
        return false
      }
      if (!that.pdfId) {
        that.$message.error('请选中一张pdf')
        return false
      }
      that.$refs[formName].validate((valid) => {
        // 验证通过，提交
        if (valid) {
          var row = {
            title: that.form.title,
            summary: that.form.summary
          }

          if (that.form.tag) {
            row.tag = that.form.tag.split(',')
          }
          row.cover_id = that.coverId
          row.pdf_id = that.pdfId

          var method = null

          if (that.itemId) {
            method = 'put'
            row.id = that.itemId
          } else {
            method = 'post'
            if (that.uploadParam['x:random']) {
              row['random'] = that.uploadParam['x:random']
            }
          }
          that.isLoadingBtn = true
          that.$http({method: method, url: api.adminTrendReport, data: row})
          .then (function(response) {
            if (response.data.meta.status_code === 200) {
              that.$message.success('提交成功！')
              // 跳转到上一页
              if (that.beforeRoute.name) {
                that.$router.push({name: that.beforeRoute.name, query: that.beforeRoute.query})
              } else {
                that.$router.push({name: 'adminTrendReportList'})
              }
              return false
            } else {
              that.$message.error(response.data.meta.message)
              that.isLoadingBtn = false
            }
          })
          .catch (function(error) {
            that.$message.error(error.message)
            that.isLoadingBtn = false
            return false
          })
          return false
        } else {
          console.log('error submit!!')
          return false
        }
      })
    },
    returnList() {
      this.$router.push({name: 'adminTrendReportList'})
    },
    // 删除附件
    delAsset(event) {
      var id = event.currentTarget.getAttribute('item_id')
      var index = event.currentTarget.getAttribute('index')

      const self = this
      self.$http.delete(api.asset.format(id), {})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          self.fileList.splice(index, 1)
        } else {
          self.$message.error(response.data.meta.message)
        }
      })
      .catch (function(error) {
        self.$message.error(error.message)
      })
    },
    // 设置封面图
    setCoverBtn (event) {
      var id = event.currentTarget.getAttribute('item_id')
      // var index = event.currentTarget.getAttribute('index')
      this.coverId = id
    },
    // 设置pDf主要
    setPdfBtn (event) {
      var id = event.currentTarget.getAttribute('item_id')
      // var index = event.currentTarget.getAttribute('index')
      this.pdfId = id
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
        }
      })
      .catch (function(error) {
        that.$message.error(error.message)
      })
    },
    handlePreview(file) {
    },
    handleChange(value) {
    },
    uploadError(err, file, fileList) {
      this.uploadMsg = '上传失败'
      this.$message.error('文件上传失败!')
      console.log(err)
    },
    uploadPdfError(err, file, fileList) {
      this.uploadPdfMsg = '上传失败'
      this.$message.error('文件上传失败!')
      console.log(err)
    },
    uploadProgress(event, file, fileList) {
      this.uploadMsg = '上传中...'
      console.log(event)
    },
    uploadPdfProgress(event, file, fileList) {
      this.uploadPdfMsg = '上传中...'
      console.log(event)
    },
    uploadSuccess(response, file, fileList) {
      this.uploadMsg = '只能上传jpg/png文件，且不超过2M'
      var add = fileList[fileList.length - 1]
      var item = {
        name: add.name,
        url: add.url,
        edit: false,
        summary: '',
        response: {
          asset_id: add.response.asset_id
        }
      }
      this.fileList.push(item)
    },
    uploadPdfSuccess(response, file, fileList) {
      this.uploadPdfMsg = '只能上传pdf文件，且不超过10M'
      var add = fileList[fileList.length - 1]
      var item = {
        name: add.name,
        url: add.url,
        response: {
          asset_id: add.response.asset_id
        }
      }
      this.filePdfList.push(item)
      this.pdfId = add.response.asset_id
    },
    beforeUpload(file) {
      const arr = ['image/jpeg', 'image/gif', 'image/png']
      const isLt2M = file.size / 1024 / 1024 < 2
      this.uploadParam['x:type'] = 16

      if (arr.indexOf(file.type) === -1) {
        this.$message.error('上传文件格式不正确!')
        return false
      }
      if (!isLt2M) {
        this.$message.error('上传文件大小不能超过 2MB!')
        return false
      }
    },
    beforePdfUpload(file) {
      const arr = ['application/pdf']
      const isLt10M = file.size / 1024 / 1024 < 10
      this.uploadParam['x:type'] = 17

      if (arr.indexOf(file.type) === -1) {
        this.$message.error('上传文件格式不正确!')
        return false
      }
      if (!isLt10M) {
        this.$message.error('上传文件大小不能超过 10MB!')
        return false
      }
    }
  },
  computed: {
    typeOptions() {
      var items = []
      for (var i = 0; i < typeData.COLUMN_TYPE.length; i++) {
        var item = {
          value: typeData.COLUMN_TYPE[i]['id'],
          label: typeData.COLUMN_TYPE[i]['name']
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
      that.itemMode = '编辑趋势报告'
      that.itemId = id
      that.uploadParam['x:target_id'] = id
      that.$http.get(api.adminTrendReport, {params: {id: id}})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          that.form = response.data.data
          if (that.form.cover_id) {
            that.coverId = that.form.cover_id
          }
          if (that.form.pdf_id) {
            that.pdfId = that.form.pdf_id
          }
          if (that.form.tag) {
            that.form.tag = that.form.tag.join(',')
          }

          if (response.data.data.cover) {
            var files = []
            var obj = response.data.data.cover
            var item = {}
            item['response'] = {}
            item['id'] = obj['id']
            item['name'] = obj['name']
            item['url'] = obj['middle']
            item['response']['asset_id'] = obj['id']
            files.push(item)
            that.fileList = files
          }
          if (response.data.data.image) {
            var pdfFiles = []
            var objPdf = response.data.data.image
            var itemPdf = {}
            itemPdf['response'] = {}
            itemPdf['id'] = objPdf['id']
            itemPdf['name'] = objPdf['name']
            itemPdf['url'] = objPdf['middle']
            itemPdf['response']['asset_id'] = objPdf['id']
            pdfFiles.push(itemPdf)
            that.filePdfList = pdfFiles
          }
        }
        console.log(response.data.data)
      })
      .catch (function(error) {
        that.$message.error(error.message)
        return false
      })
    } else {
      that.itemId = null
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
      return false
    })
  },
  watch: {
    '$route' (to, from) {
      // 对路由变化作出响应...
    }
  },
  // 页面进入前获取路由信息
  beforeRouteEnter (to, from, next) {
    // 在导航完成前获取数据
    next (vm => {
      vm.beforeRoute.name = from.name
      vm.beforeRoute.query = from.query
    })
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .set-role-name {
    margin-bottom: 20px;
  }

</style>
