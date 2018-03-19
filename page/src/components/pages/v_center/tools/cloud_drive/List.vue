<template>
  <div class="container blank30 min-height350">
    <el-row :gutter="20">
      <el-col :xs="24" :sm="6" :md="6" :lg="6">
        <v-menu @getTitle="headTitle"></v-menu>
      </el-col>
      <el-col :xs="24" :sm="18" :md="18" :lg="18">
        <div class="content">
          <div class="content-head">
            <div class="clearfix" v-show="showList">
              <p class="title fl" v-if="!isChoose">{{title}}</p>
              <div class="fr operate" v-if="!isChoose">
                <p class="add">
                  <span class="add-option">
                    <a class="new-folder">
                      <span>新建文件夹</span>
                    </a>
                    <a class="upload-files">
                      <el-upload
                        ref="upload"
                        class="upload-button"
                        :action="uploadUrl"
                        :multiple="true"
                        list-type="picture"
                        :data="uploadParams"
                        :on-success="uploadSuccess"
                        :on-progress="uploadProgress"
                        :on-error="uploadError"
                        :on-remove="uploadRemove"
                        :before-upload="beforeUpload"
                        :on-change="uploadChange"
                        :show-file-list="false">
                        <span>上传文件</span>
                      </el-upload>
                    </a>
                  </span>
                </p>
                <p class="search" title="搜索" @click="showList = !showList"></p>
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
                  <span @click="rename" :class="[{'disable': alreadyChoose > 1 || !alreadyChoose}]">重命名</span>
                  <span>删除</span>
                </el-col>
              </p>
            </div>
            <div class="search-head" v-show="!showList">
              <input v-model="searchWord" class="search-input" placeholder="搜索...">
              <i class="fr fx-0 fx-icon-nothing-close-error" @click="clearShowList"></i>
            </div>
          </div>
          <!-- 文件列表 -->
          <transition name="uploadList">
            <vContent v-show="showList" :list="list" :chooseStatus="isChoose" @choose="chooseList" :isChooseAll="isChooseAll" :curView="curView" :hasRename="hasRename" @renameCancel="renameCancel"
            @changeName="changeName"></vContent>
          </transition>
          <!-- 搜索列表 -->
            <vContent v-show="!showList"></vContent>
        </div>
      </el-col>
    </el-row>
    <footer class="drive-footer clearfix" v-if="webUploader">
      <span class="fl" @click="isShowProgress = true">正在上传文件{{uploadingNumber}}/{{totalNumber}}</span>
      <span class="fr"><i class="fx-0 fx-icon-nothing-close-error" @click="showConfirm = true"></i></span>
    </footer>

    <div class="web-uploader" v-if="webUploader && isShowProgress">
      <div class="web-uploader-header clearfix">
        上传进度<i class="fr fx-0 fx-icon-nothing-close-error" @click="isShowProgress = !isShowProgress"></i>
      </div>
      <div class="web-uploader-body">
        <el-row v-for="(ele, index) in fileList" :key="ele.name + index" class="upload-list">
          <el-col :span="3" class="upload-list-logo">
            <p :class="['file-icon', 'other', {
                'folder': /folder/.test(ele.raw.type),
                'artboard': /pdf/.test(ele.raw.type),
                'audio': /audio/.test(ele.raw.type),
                'compress': /compress/.test(ele.raw.type),
                'document': /(?:text|msword)/.test(ele.raw.type),
                'image': /image/.test(ele.raw.type),
                'powerpoint': /powerpoint/.test(ele.raw.type),
                'spreadsheet': /excel/.test(ele.raw.type),
                'video': /video/.test(ele.raw.type)
              }]">file-icon</p>
          </el-col>
          <el-col :span="20" class="upload-list-title">
            <p class="upload-list-title-p">
              <span :title="ele.name">{{ele.name}}</span>
              <span class="file-size">{{ele.format_size}}</span>
            </p>
            <el-progress v-if="ele.status === 'uploading'" class="upload-progress" :percentage="ele.format_percentage" :show-text="false"></el-progress>
            <p v-if="ele.status === 'success'" class="upload-success">完成</p>
            <p v-if="ele.status === 'fail'" class="upload-fail">传输失败</p>
            <p v-if="ele.status === 'ready'" class="upload-ready">正在等待</p>
            <p class="percentage" v-if="ele.status === 'uploading'">{{ele.format_percentage}}%</p>
            <!--ready success  uploading fail-->
          </el-col>
          <el-col :span="10">
          </el-col>
        </el-row>
      </div>
    </div>
    <section class="dialog-bg" v-if="showConfirm" @click="showConfirm = false"></section>
    <section class="dialog-body" v-if="showConfirm">
      <h3 class="dialog-header clearfix">
        放弃上传
        <i class="fr fx fx-icon-nothing-close-error" @click="showConfirm = !showConfirm"></i>
      </h3>
      <div class="dialog-conetent">
        <div class="dialog-article">
          <p>列表中有未上传完成的文件</p>
          <p>确定要放弃上传吗？</p>
        </div>
        <p class="buttons">
          <button class="cancel-btn" @click="showConfirm = false">取消</button>
          <button  class="confirm-btn" @click="clearUpload">确定</button>
        </p>
      </div>
    </section>
  </div>
</template>
<script>
  import api from '@/api/api'
  import vMenu from '@/components/pages/v_center/tools/cloud_drive/Menu'
  import vContent from '@/components/pages/v_center/tools/cloud_drive/CloudContent'
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
        list: [], // 获取文件列表
        fileList: [], // 上传列表
        totalNumber: 0,
        webUploader: false, // 上传状态
        isShowProgress: false, // 是否显示上传列表
        showConfirm: false, // 确认删除?
        showList: true, // 显示全部文件或搜索
        searchWord: '', // 搜索关键字
        hasRename: false // 重命名状态
      }
    },
    components: {
      vMenu,
      vContent
    },
    mounted: function () {
      window.addEventListener('keydown', e => {
        if (e.keyCode === 13) {
          this.getSearchList()
        }
      })
    },
    methods: {
      getList(id = 0) {
        this.$http.get(api.yunpanList, {params: {pan_director_id: id}}).then(
          (res) => {
            console.log(res.data.data)
            this.list = res.data.data
            for (let i of this.list) {
              let size = i['size'] / 1024
              if (size > 1024) {
                i['format_size'] = Number(Math.round(i['size'] / 1024 / 1024)) + 'MB'
              } else {
                i['format_size'] = Number(size.toFixed(2)) + 'KB'
              }
            }
          })
      },
      getSearchList() {
        if (!this.showList && this.searchWord) {
          console.log('getSearchList')
        } else {
          console.log('enter')
        }
      },
      headTitle(e) {
        this.title = e
      },
      changeChooseStatus() {
        this.isChoose = !this.isChoose
      },
      cancelChoose() {
        this.hasRename = false
        this.isChoose = false
      },
      chooseList(e, str) {
        this.alreadyChoose = e.length
        this.isChooseAll = str
      },
      changeChooseAll() {
        if (!this.hasRename) {
          if (this.isChooseAll === '' || this.isChooseAll === 'empty') {
            this.isChooseAll = 'all'
          } else if (this.isChooseAll === 'all') {
            this.isChooseAll = 'empty'
          }
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
      uploadRemove(file, fileList) {
      },
      uploadProgress(event, file, fileList) {
        this.webUploader = true
        this.fileList = fileList
        this.totalNumber = this.fileList.length
      },
      beforeUpload(file) {
        const size = file.size / 1024 / 1024 < 1000
        if (!size) {
          this.$message.error('文件大小不能超过 1000MB!')
        }
        return size
      },
      uploadChange(file, fileList) {
      },
      clearUpload() {
        this.$refs.upload.clearFiles()
        for (let i of this.fileList) {
          this.$refs.upload.handleRemove(i)
        }
        this.showConfirm = false
      },
      clearShowList() {
        this.showList = true
        this.searchWord = ''
      },
      rename() {
        if (this.alreadyChoose) {
          if (this.alreadyChoose > 1) {
            return
          } else {
            this.hasRename = true
            this.$message.success('rename')
          }
        } else {
          this.$message.error('请选择要重命名的文件')
        }
      },
      renameCancel() {
        this.hasRename = false
      },
      changeName(index, name) {
        this.list[index]['name'] = name
      }
    },
    created() {
      this.getUploadUrl()
      this.getList()
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
          let a = 0
          for (let i of this.fileList) {
            let size = Math.round(i['size'] / 1024)
            if (size > 1024) {
              i['format_size'] = Math.round(i['size'] / 1024 / 1024) + 'MB'
            } else {
              i['format_size'] = size + 'KB'
            }
            i['format_percentage'] = Number(i.percentage.toFixed(2))
            if (i.percentage === 100) {
              a++
            }
          }
          if (a === this.fileList.length) {
            this.webUploader = false
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
    line-height: 20px;
    position: relative;
    z-index: 10;
  }
  .content-head .title {
    font-size: 16px;
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
    opacity: 0.6;
  }
  
  .operate p:hover {
    opacity: 1;
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
    display: none;
    overflow: hidden;
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
    line-height: 20px;
    height: 30px;
    overflow: hidden;
  }

  .choose-info {
    text-align: left
  }
  .edit-menu span {
    user-select: none;
    font-size: 16px;
    margin-right: 20px;
    cursor: pointer;
  }

  .edit-menu .disable {
    color: #d2d2d2
  }

  .edit-menu .disable:hover {
    color: #d2d2d2
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

  .drive-footer span {
    cursor: pointer;
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
    max-height: 275px;
    line-height: 1.5;
  }
  .upload-list {
    padding: 15px 10px;
    min-height: 69px;
    border-bottom: 1px solid #d2d2d2;
  }
  .upload-list:last-child {
    border-bottom: 0;
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

  .upload-list-title-p {
    display: flex;
    padding-bottom: 10px;
  }

  .upload-list-title span {
    padding-right: 20px;
    max-width: 80%;
  }
  span.file-size {
    color: #999;
  }
  .upload-progress {
    padding-bottom: 10px;
  }

  p.upload-success,
  p.upload-fail,
  p.upload-ready {
    padding-left: 26px;
    line-height: 16px;
    font-size: 14px;
    color: #666;
    position: relative;
  }

  p.upload-success::before,
  p.upload-fail::before,
  p.upload-ready::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: url('../../../../../assets/images/tools/cloud_drive/status/success@2x.png') no-repeat;
    background-size: contain
  }
  p.upload-fail::before{
    background: url('../../../../../assets/images/tools/cloud_drive/status/fail@2x.png') no-repeat;
    background-size: contain
  }
  
  p.upload-ready::before {
    background: url('../../../../../assets/images/tools/cloud_drive/status/wait@2x.png') no-repeat;
    background-size: contain
  }
  p.percentage {
    color: #666666;
    font-size: 12px;
  }
  .web-uploader-header i {
    margin-top: 23px;
  }
  .dialog-bg {
    position: fixed;
    z-index: 999;
    left: 50%;
    top: 50%;
    transform:  translate(-50%, -50%);
    width: 100vw;
    height: 100vh;
    background: rgba(0,0,0,0.30)
  }
  .dialog-body {
    position: fixed;
    z-index: 999;
    left: 50%;
    top: 50%;
    transform:  translate(-50%, -50%);
    width: 380px;
    height: 208px;
    margin: auto;
    background: #FFFFFF;
    box-shadow: 0 0 4px 0 rgba(0,0,0,0.10);
    border-radius: 4px;
  }
  .dialog-header {
    font-size: 16px;
    color: #222;
    height: 50px;
    line-height: 50px;
    text-align: center;
    background: #F7F7F7;
    border-radius: 4px 4px 0 0;
  }
  .dialog-header i {
    margin-right: 20px;
    margin-top: 18px;
    color: #666;
  }
  .dialog-header i:hover {
    color: #222;
  }
  .dialog-conetent {
    text-align: center
  }
  .dialog-article {
    margin: 32px 0;
    color: #666;
    line-height: 20px;
    font-size: 14px;
  }

  .buttons {
    display: flex;
    justify-content: center;
    align-items: center
  }

  .buttons button {
    width: 118px;
    height: 32px;
    border: 1px solid #d2d2d2;
    margin-right: 25px;
    border-radius: 4px;
    background: #fff;
    color: #666;
    cursor: pointer;
  }
  
  .buttons button:last-child {
    margin-right: 0;
  }
  .buttons button.confirm-btn {
    color: #fff;
    border-color: #ff5a5f;
    background-color: #ff5a5f
  }

  .uploadList-enter-active {
    transition: all 0.3s ease
  } 
  .uploadList-leave-active {
    transition: all 0.3s cubic-bezier(1.0, 0.5, 0.8, 1.0)
  }
  .uploadList-enter, .uploadList-leave-to {
    transform: translateX(100%);
    opacity: 0;
  }

  .search-input {
    width: calc(100% - 30px);
    border: none;
    padding-left: 30px;
    height: 20px;
    font-size: 16px;
    color: #666;
    background: url('../../../../../assets/images/tools/cloud_drive/search@2x.png') no-repeat;
    background-size: contain
  }
  
  .search-input:focus {
    outline: none;
  }
  
  .search-head, .search-head i {
    line-height: 20px;
  }
  .search-head i:active {
    transform: scale(0.9)
  }
</style>
