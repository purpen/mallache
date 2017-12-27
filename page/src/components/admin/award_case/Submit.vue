<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <v-menu selectedName="awardCaseList"></v-menu>

      <el-col :span="20">
        <div class="content">

        <div class="admin-menu-sub">
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminAwardCaseList'}" active-class="false" :class="{'item': true, 'is-active': menuType == 0}">全部</router-link>
          </div>
          <div class="fr">
            <router-link :to="{name: 'adminAwardCaseAdd'}" class="item add"><i class="el-icon-plus"></i> 添加</router-link>
          </div>
        </div>

          <div class="content-box">
            <div class="form-title">
              <span>{{ itemMode }}</span>
            </div>
            <el-form label-position="top" :model="form" :rules="ruleForm" ref="ruleForm" label-width="80px">


              <el-form-item label="所属奖项" prop="category_id">
                <el-radio-group v-model.number="form.category_id">
                  <el-radio-button
                    v-for="item in categoryOptions"
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

              <el-row :gutter="12">
                <el-col :span="6">
                  <el-form-item label="奖项级别" prop="grade">
                    <el-input v-model="form.grade" placeholder=""></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="6">
                  <el-form-item label="获奖时间" prop="time_at">
                    <el-input v-model="form.time_at" placeholder=""></el-input>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="标签" prop="tags">
                    <el-input v-model="form.tags" placeholder=""></el-input>
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
                              <div class="summary-edit" v-if="d.edit">
                                <textarea v-model="d.summary">{{ d.summary }}</textarea>
                              </div>
                              <div class="summary" v-else>
                                <p v-if="d.summary">{{ d.summary }}</p>
                                <p class="image-no-summary" v-else>暂无描述信息</p>
                              </div>
                              <div class="opt" v-if="d.edit">
                                <a href="javascript:void(0);" :item_id="d.response.asset_id" :index="index" @click="saveAssetSummary">保存</a>
                              </div>
                              <div class="opt" v-else>
                                <el-tooltip class="item" effect="dark" content="删除图片" placement="top">
                                  <a href="javascript:void(0);" :item_id="d.response.asset_id" :index="index" @click="delAsset"><i class="fa fa-times" aria-hidden="true"></i></a>
                                </el-tooltip>
                                <el-tooltip class="item" effect="dark" content="编辑文字" placement="top">
                                  <a href="javascript:void(0);" :item_id="d.response.asset_id" :index="index" @click="editAssetBtn"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
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

              <el-row v-if="form.images_url">
                <el-col :span="24">
                  <el-form-item label="图片草稿" prop="images_url">
                    <div class="file-list">
                      <el-row :gutter="10">
                        <el-col :span="8" v-for="(d, index) in fileDraftList" :key="index">
                          <el-card :body-style="{ padding: '0px' }" class="item">
                            <div class="image-box">
                                <img :src="d">
                            </div>
                            <div class="content">
                              <div class="opt">
                                <el-tooltip class="item" effect="dark" content="设为封面" placement="top">
                                <a href="javascript:void(0);" :index="index" :url="d" @click="upCoverBtn"><i class="fa fa-flag" aria-hidden="true"></i></a>
                                </el-tooltip>
                                <el-tooltip class="item" effect="dark" content="删除图片" placement="top">
                                  <a href="javascript:void(0);" :url="d" :index="index" @click="delDrafImage"><i class="fa fa-times" aria-hidden="true"></i></a>
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

              <el-form-item label="简述" prop="summary">
                <el-input
                  type="textarea"
                  :rows="5"
                  placeholder="请输入简述"
                  v-model="form.summary">
                </el-input>
              </el-form-item>

              <el-form-item label="内容" prop="content">
                <mavon-editor ref="mavonEditor" :ishljs="false" v-model="form.content" id="editor" @imgAdd="$imgAdd" @imgDel="$imgDel" ></mavon-editor>
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
import { mavonEditor } from 'mavon-editor'
import 'mavon-editor/dist/css/index.css'
import axios from 'axios'
import api from '@/api/api'
import vMenu from '@/components/admin/Menu'
import typeData from '@/config'
export default {
  name: 'admin_award_case_submit',
  components: {
    vMenu,
    mavonEditor
  },
  data () {
    return {
      menuType: 0,
      itemMode: '添加奖项案例',
      isLoading: false,
      isLoadingBtn: false,
      content_file: [],
      uploadUrl: '',
      uploadParam: {
        'token': '',
        'x:random': '',
        'x:user_id': this.$store.state.event.user.id,
        'x:target_id': '',
        'x:type': 25
      },
      uploadMsg: '只能上传jpg/png文件，且不超过5M',
      pickerOptions: {
      },
      imageUrl: '',
      coverId: '',
      fileList: [],
      fileDraftList: [],
      form: {
        category_id: '',
        grade: '',
        time_at: '',
        'summary': '',
        title: '',
        content: '',
        cover_id: '',
        tags: '',
        url: ''
      },
      ruleForm: {
        category_id: [
          { type: 'number', message: '请选择所属奖项', trigger: 'change' }
        ],
        title: [
          { required: true, message: '请添写标题', trigger: 'blur' }
        ],
        time_at: [
          { required: true, message: '请添写获奖时间', trigger: 'blur' }
        ]
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
            category_id: that.form.category_id,
            title: that.form.title,
            summary: that.form.summary,
            grade: that.form.grade,
            time_at: that.form.time_at,
            content: that.form.content,
            url: that.form.url
          }

          if (that.form.tags) {
            row.tags = that.form.tags.split(',')
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
          that.$http({method: method, url: api.adminAwardCase, data: row})
          .then (function(response) {
            if (response.data.meta.status_code === 200) {
              that.$message.success('提交成功！')
              that.$router.push({name: 'adminAwardCaseList'})
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
      this.$router.push({name: 'adminAwardCaseList'})
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
    // 删除草稿图
    delDrafImage(event) {
      var index = event.currentTarget.getAttribute('index')
      this.fileDraftList.splice(index, 1)
    },
    // 编辑附件
    editAssetBtn(event) {
      // var id = event.currentTarget.getAttribute('item_id')
      var index = event.currentTarget.getAttribute('index')
      this.fileList[index].edit = true
    },
    // 保存附件描述
    saveAssetSummary(event) {
      var id = event.currentTarget.getAttribute('item_id')
      var index = event.currentTarget.getAttribute('index')
      var summary = this.fileList[index].summary
      if (summary === '' || summary === null) {
        this.$message.error('描述信息不能为空!')
        return false
      }
      const self = this
      self.$http.put(api.updateImageSummary, {asset_id: id, summary: summary})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          self.fileList[index].edit = false
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
    // 上传封面图
    upCoverBtn (event) {
      var self = this
      var url = event.currentTarget.getAttribute('url')
      // var index = event.currentTarget.getAttribute('index')
      // 上传图片
      self.$http.get(api.adminAssetUrlUpload, {params: {url: url, type: self.uploadParam['x:type'], target_id: self.uploadParam['x:target_id'], user_id: self.uploadParam['x:user_id']}})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          if (response.data.data) {
            var item = {
              name: response.data.data.name,
              url: response.data.data.middle,
              edit: false,
              summary: '',
              response: {
                asset_id: response.data.data.id
              }
            }
            self.fileList.push(item)
            self.coverId = response.data.data.id
          } else {
            console.log(response.data)
          }
        }
      })
      .catch (function(error) {
        self.$message({
          showClose: true,
          message: error.message,
          type: 'error'
        })
        return false
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
    },
    $imgAdd(pos, $file) {
      this.content_file[pos] = $file
      const that = this

      var formdata = new FormData()
      formdata.append('file', $file)
      that.uploadParam['x:type'] = 26
      for (var key in that.uploadParam) {
        formdata.append(key, that.uploadParam[key])
      }
      axios
        .post(that.uploadUrl, formdata, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        .then(function(response) {
          if (response.data.success === 1) {
            // that.$refs.mavonEditor.$imgUpdateByFilename(pos, './aaa')
            that.$refs.mavonEditor.$img2Url(pos, response.data.big)
          } else {
            that.$message.error('上传失败！')
          }
          console.log(response)
        })
        .catch(function(error) {
          that.$message.error(error)
        })
    },
    $imgDel(pos) {
      delete this.content_file[pos]
    }
  },
  computed: {
    categoryOptions() {
      var items = []
      for (var i = 0; i < typeData.AWARD_CASE_CATEGORY.length; i++) {
        var item = {
          value: typeData.AWARD_CASE_CATEGORY[i]['id'],
          label: typeData.AWARD_CASE_CATEGORY[i]['name']
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
      that.itemMode = '编辑奖项案例'
      that.itemId = id
      that.uploadParam['x:target_id'] = id
      that.$http.get(api.adminAwardCase, {params: {id: id}})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          that.form = response.data.data
          if (that.form.cover_id) {
            that.coverId = that.form.cover_id
          }

          if (that.form.tags) {
            that.form.tags = that.form.tags.join(',')
          }

          if (response.data.data.images) {
            var files = []
            for (var i = 0; i < response.data.data.images.length; i++) {
              var obj = response.data.data.images[i]
              var item = {}
              item['response'] = {}
              item['id'] = obj['id']
              item['name'] = obj['name']
              item['url'] = obj['middle']
              item['summary'] = obj['summary']
              item['response']['asset_id'] = obj['id']
              item['edit'] = false
              files.push(item)
            }
            that.fileList = files
          }

          // 图片草稿
          if (response.data.data.images_url) {
            that.fileDraftList = response.data.data.images_url.split('@@')
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
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .set-role-name {
    margin-bottom: 20px;
  }

</style>
