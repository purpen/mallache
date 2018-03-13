<template>
  <div class="container blank40 min-height350">
    <el-row :gutter="20">
      <el-col :xs="24" :sm="6" :md="6" :lg="6">
        <v-menu @getTitle="headTitle"></v-menu>
      </el-col>
      <el-col :xs="24" :sm="18" :md="18" :lg="18">
        <div class="content">
          <div class="content-head clearfix">
            <p class="title fl" v-if="!isChoose">{{title}}</p>
            <div class="fr operate" v-if="!isChoose">
              <p class="add">
                <span class="add-option">
                  <a class="new-folder">
                    <span>新建文件夹</span>
                  </a>
                  <a class="upload-files">
                    <el-upload
                      class="upload-button"
                      :action="uploadUrl"
                      :multiple="true"
                      list-type="picture"
                      :data="uploadParams"
                      :on-success="uploadSuccess"
                      :on-progress="uploadProgress"
                      :on-error="uploadError"
                      :before-upload="beforeUpload"
                      :on-change="uploadChange"
                      :show-file-list="false">
                      <span>上传文件</span>
                    </el-upload>
                  </a>
                </span>
              </p>
              <p class="search" title="搜索"></p>
              <p :class="[{'chunk': curView === 'list','list': curView === 'chunk'}]" 
                 :title="chunkTitle"
                 @click="changeFileView"></p>
              <p class="sequence"></p>
              <p class="edit" title="编辑模式" @click="changeChooseStatus"></p>
            </div>

            <p class="edit-menu" v-if="isChoose">
              <el-col :span="2">
                <i :class="['file-radio', {'active': isChooseAll === 'all'}, {'chunk-view': curView === 'chunk'}]" @click="changeChooseAll">file-icon</i>
              </el-col>
              <el-col :span="6" class="choose-info">
                <span class="already-choose">已选择{{alreadyChoose}}项</span>
                <span class="cancel-choose" @click="cancelChoose">取消选择</span>
              </el-col>
              <el-col :offset="4" :span="12">
                <span>共享</span>
                <span>下载</span>
                <span>复制</span>
                <span>移动</span>
                <span>重命名</span>
                <span>删除</span>
              </el-col>
            </p>
          </div>

          <!-- 文件列表 -->
          <vContent :chooseStatus="isChoose" @choose="chooseList" :isChooseAll="isChooseAll" :curView="curView"></vContent>
        </div>
      </el-col>
    </el-row>
    <footer class="drive-footer clearfix">
      <span class="fl">正在上传文件{{uploadingNumber}}/{{totalNumber}}</span>
      <span class="fr"><i class="fx-0 fx-icon-nothing-close-error"></i></span>
    </footer>

    <div class="web-uploader">
      <div class="web-uploader-header clearfix">
        上传进度<i class="fr fx-0 fx-icon-nothing-close-error"></i>
      </div>
      <div class="web-uploader-body">
        <el-row v-for="(ele, index) in fileList" :key="ele.name + index" class="upload-list">
          <el-col :span="3" class="upload-list-logo">
            <p :class="['file-icon', 'image']">file-icon</p>
          </el-col>
          <el-col :span="10" class="upload-list-title">
            <span :title="ele.name">{{ele.name}}</span>
            <span :title="ele.status">{{ele.percentage}}%</span>
          </el-col>
          <el-col :span="10">
            <span>{{ele.format_size}}</span>
          </el-col>
        </el-row>
      </div>
    </div>
  </div>
</template>
<script>
  import api from '@/api/api'
  import vMenu from '@/components/pages/v_center/Tools/cloud_drive/Menu'
  import vContent from '@/components/pages/v_center/Tools/cloud_drive/CloudContent'
  export default {
    name: 'cloud_drive',
    data() {
      return {
        test: 'test',
        title: '全部文件',
        isChoose: false, // 切换为选择状态
        alreadyChoose: 0, // 已选择数目
        isChooseAll: '', // 是否全选,
        curView: 'list', // 当前视图: 列表: list, 九宫格: chunk, 搜索: search
        uploadUrl: '', // 上传地址
        uploadParams: {
          'token': '',
          'x:pan_director_id': 0,
          'x:open_set': 1,
          'x:group_id': 0
        },
        fileList: [],
        totalNumber: 0
      }
    },
    components: {
      vMenu,
      vContent
    },
    methods: {
      headTitle(e) {
        this.title = e
      },
      changeChooseStatus() {
        this.isChoose = !this.isChoose
      },
      cancelChoose() {
        this.isChoose = false
      },
      chooseList(e, str) {
        this.alreadyChoose = e.length
        this.isChooseAll = str
      },
      changeChooseAll() {
        if (this.isChooseAll === '' || this.isChooseAll === 'empty') {
          this.isChooseAll = 'all'
        } else if (this.isChooseAll === 'all') {
          this.isChooseAll = 'empty'
        }
      },
      changeFileView() {
        if (this.curView === 'list') {
          this.curView = 'chunk'
        } else {
          this.curView = 'list'
        }
      },
      getUploadUrl(e) {
        this.$http.get(api.yunpanUpToken).then((res) => {
          if (res.data.meta.status_code === 200) {
            this.uploadUrl = res.data.data.upload_url
            this.uploadParams['token'] = res.data.data.upToken
          }
        })
      },
      uploadSuccess(res, file, fileList) {
      },
      uploadError(err, file, fileList) {
        console.error(err)
      },
      uploadProgress(event, file, fileList) {
        this.fileList = fileList
        this.totalNumber = this.fileList.length
      },
      beforeUpload(file) {
      },
      uploadChange(file, fileList) {
      }
    },
    created() {
      this.getUploadUrl()
    },
    computed: {
      chunkTitle() {
        if (this.curView === 'list') {
          return '缩略图显示'
        } else {
          return '列表显示'
        }
      },
      uploadingNumber() {
        let num = 0
        for (let i of this.fileList) {
          if (i.percentage === 100) {
            num++
          }
        }
        return num
      }
    },
    watch: {
      fileList: {
        handler(val) {
          for (let i of this.fileList) {
            i['format_size'] = Math.round(i['size'] / 1024) + 'KB'
          }
        },
        deep: true
      }
    }
  }
</script>
<style scoped>
  @keyframes slowShow {
    0% {
      height: 0;
    }
    100% {
      height: 82px;
    }
  }

  .content-head {
    color: #999;
    font-size: 0;
    border-bottom: 1px solid #D2D2D2;
    height: 30px;
    line-height: 30px;
    position: relative;
    z-index: 10;
  }
  .operate {
    height: 30px;
  }
  .operate p {
    display: inline-block;
    width: 30px;
    height: 30px;
    margin-right: 20px;
    cursor: pointer;
    position: relative;
  }
  
  .operate p:hover {
    background-size: 26px!important
  }
  .operate p.add:hover .add-option {
    display: block;
  }

  .operate p.add {
    background: url('../../../../../assets/images/tools/cloud_drive/operate/add@2x.png') top no-repeat;
    background-size: 24px
  }
  .operate p.search {
    background: url('../../../../../assets/images/tools/cloud_drive/operate/search@2x.png') top no-repeat;
    background-size: 24px
  }
  .operate p.chunk {
    background: url('../../../../../assets/images/tools/cloud_drive/operate/chunk@2x.png') top no-repeat;
    background-size: 24px
  }
  .operate p.list {
    background: url('../../../../../assets/images/tools/cloud_drive/operate/list@2x.png') top no-repeat;
    background-size: 24px
  }
  .operate p.sequence {
    background: url('../../../../../assets/images/tools/cloud_drive/operate/sequence@2x.png') top no-repeat;
    background-size: 24px
  }
  .operate p.edit {
    background: url('../../../../../assets/images/tools/cloud_drive/operate/edit@2x.png') top no-repeat;
    background-size: 24px
  }
  .operate p:last-child {
    margin-right: 0;
  }
  
  span.add-option {
    display: block;
    position: absolute;
    left: -65px;
    top: 29px;
    width: 160px;
    height: 82px;
    border: 1px solid #d2d2d2;
    animation: slowShow 0.3s;
    /* display: none; */
    /* overflow: hidden; */
  }
  .already-choose {
    color: #222;
  }
  .add-option a {
    line-height: 40px;
    display: block;
    margin: 0;
    color: #999;
    padding-left: 40px;
    background: #fff;
  }
  
  .add-option a:hover {
    color: #222;
    background: #f7f7f7
  }
  
  .add-option a span {
    position: relative;
    display: block;
    padding-left: 18px;
  }
  .add-option span:before {
    content: '';
    position: absolute;
    left: -21px;
    top: 8px;
    width: 24px;
    height: 24px;
  }
  .add-option .new-folder span:before {
    background: url('../../../../../assets/images/tools/cloud_drive/operate/add_folder@2x.png') no-repeat;
    background-size: contain
  }
  .add-option .upload-files span:before {
    background: url('../../../../../assets/images/tools/cloud_drive/operate/upload@2x.png') no-repeat;
    background-size: contain
  }
  .edit-menu {
    font-size: 0;
    text-align: right;
    line-height: 30px;
    height: 30px;
    overflow: hidden;
  }

  .choose-info {
    text-align: left
  }
  .edit-menu span {
    font-size: 14px;
    margin-right: 20px;
    cursor: pointer;
  }

  .edit-menu span:last-child {
    margin-right: 0;
  }
  
  .edit-menu span:hover {
    color: #222;
  }

  .cancel-choose {
    cursor:pointer
  }

  i.file-radio {
    cursor: pointer;
    display: block;
    text-indent: -999em;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 1px solid #d2d2d2;
    background: #fff;
    position: relative;
    margin: 3px auto 0;
  }

  i.file-radio.chunk-view {
    margin: 3px 0 0;
  }
  
  i.file-radio:before {
    content: '';
    position: absolute;
    left: 3px;
    top: 4px;
    width: 12px;
    height: 7px;
    border: 2px solid #fff;
    border-top: none;
    border-right: none;
    transform: rotate(-45deg);
  }

  i.file-radio.active {    
    border: 1px solid #999;
    background: #999;
  }

  .drive-footer {
    width: 100%;
    height: 60px;
    background: #f7f7f7;
    position: fixed;
    bottom: 0;
    left: 0;
    z-index: 2;
    padding: 0 30px;
    line-height: 60px;
    font-size: 14px;
  }

  .web-uploader {
    position: fixed;
    z-index: 3;
    right: 0;
    bottom: 60px;
    width: 580px;
    border: 1px solid #d2d2d2
  }
  .web-uploader-header {
    height: 50px;
    line-height: 50px;
    text-align: center;
    background: #f7f7f7;
    padding-right: 30px;
    font-size: 16px;
    border-bottom: 1px solid #d2d2d2;
  }
  .web-uploader-body {
    overflow-y: scroll;
    background: #fff;
    padding: 15px 0;
    max-height: 300px;
    line-height: 1.5;
  }
  .upload-list {
    padding: 15px 10px;
    height: 69px;
    border-bottom: 1px solid #d2d2d2;
  }
  .upload-list-logo {
    display: flex;
    justify-content: center;
    align-items: center
  }
  .upload-list-logo p.file-icon {
    margin: 0;
  }
  .upload-list-title {
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-around;
  }

  .upload-list-title span {
    display: block;
    padding-right: 10px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .web-uploader-header i {
    margin-top: 23px;
  }
</style>
