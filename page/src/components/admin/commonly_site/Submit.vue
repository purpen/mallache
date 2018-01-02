<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <v-menu selectedName="commonlySiteList"></v-menu>

      <el-col :span="20">
        <div class="content">

        <div class="admin-menu-sub">
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminCommonlySiteList'}" active-class="false" :class="{'item': true, 'is-active': menuType == 0}">全部</router-link>
          </div>
          <div class="fr">
            <router-link :to="{name: 'adminCommonlySiteAdd'}" class="item add"><i class="el-icon-plus"></i> 添加</router-link>
          </div>
        </div>

          <div class="content-box">
            <div class="form-title">
              <span>{{ itemMode }}</span>
            </div>
            <el-form label-position="top" :model="form" :rules="ruleForm" ref="ruleForm" label-width="80px">


              <el-form-item label="网站类型" prop="type">
                <el-radio-group v-model.number="form.type">
                  <el-radio-button
                    v-for="item in typeOptions"
                    :key="item.index"
                    :label="item.value">{{ item.label }}</el-radio-button>
                </el-radio-group>
              </el-form-item>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="标题" prop="title">
                    <el-input v-model="form.title" placeholder=""></el-input>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="链接" prop="url">
                    <el-input v-model="form.url" placeholder=""></el-input>
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

              <el-form-item label="描述" prop="summary">
                <el-input
                  type="textarea"
                  :rows="10"
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
  name: 'admin_commonly_site_submit',
  components: {
    vMenu
  },
  data () {
    return {
      menuType: 0,
      itemMode: '添加常用网站',
      isLoading: false,
      isLoadingBtn: false,
      uploadUrl: '',
      uploadParam: {
        'token': '',
        'x:random': '',
        'x:user_id': this.$store.state.event.user.id,
        'x:target_id': '',
        'x:type': 18
      },
      uploadMsg: '只能上传jpg/png文件，且不超过5M',
      pickerOptions: {
      },
      imageUrl: '',
      coverId: '',
      fileList: [],
      form: {
        type: '',
        title: '',
        summary: '',
        url: ''
      },
      ruleForm: {
        type: [
          { type: 'number', message: '请选择类型', trigger: 'change' }
        ],
        title: [
          { required: true, message: '请添写标题', trigger: 'blur' }
        ],
        url: [
          { required: true, message: '链接不能为空', trigger: 'blur' }
        ],
        summary: [
          { required: true, message: '请添写内容', trigger: 'blur' }
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
      that.$refs[formName].validate((valid) => {
        // 验证通过，提交
        if (valid) {
          var row = {
            type: that.form.type,
            title: that.form.title,
            summary: that.form.summary,
            url: that.form.url
          }
          row.cover_id = that.coverId
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
          that.$http({method: method, url: api.adminCommonlySite, data: row})
          .then (function(response) {
            if (response.data.meta.status_code === 200) {
              that.$message.success('提交成功！')
              // 跳转到上一页
              if (that.beforeRoute.name) {
                that.$router.push({name: that.beforeRoute.name, query: that.beforeRoute.query})
              } else {
                that.$router.push({name: 'adminCommonlySiteList'})
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
      this.$router.push({name: 'adminCommonlySiteList'})
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
    uploadProgress(event, file, fileList) {
      this.uploadMsg = '上传中...'
      console.log(event)
    },
    uploadSuccess(response, file, fileList) {
      this.uploadMsg = '只能上传jpg/png文件，且不超过5M'
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
    beforeUpload(file) {
      const arr = ['image/jpeg', 'image/gif', 'image/png']
      const isLt5M = file.size / 1024 / 1024 < 5

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
    typeOptions() {
      var items = []
      for (var i = 0; i < typeData.COMMONLY_SITE_TYPE.length; i++) {
        var item = {
          value: typeData.COMMONLY_SITE_TYPE[i]['id'],
          label: typeData.COMMONLY_SITE_TYPE[i]['name']
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
      that.itemMode = '编辑常用网站'
      that.itemId = id
      that.uploadParam['x:target_id'] = id
      that.$http.get(api.adminCommonlySite, {params: {id: id}})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          that.form = response.data.data
          if (that.form.cover_id) {
            that.coverId = that.form.cover_id
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
        }
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
