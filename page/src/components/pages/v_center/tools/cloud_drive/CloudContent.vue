<template>
  <div class="cloud-content">
    <section>
      <el-row :gutter="20" v-if="list.length">
        <el-col v-for="(ele, index) in list" :key="index" :span="24" v-if="curView === 'list'">
          <div :class="[{'active' : chooseList.indexOf(ele.id) !== -1}, 'item']" @click="liClick(ele.id, index)">
            <el-col :span="2" v-if="chooseStatus">
              <p class="file-radio">file-radio</p>
            </el-col>
            <el-col :span="2">
            <p :class="['file-icon', 'other', {
                'folder': /folder/.test(ele.mime_type),
                'artboard': /pdf/.test(ele.mime_type),
                'audio': /audio/.test(ele.mime_type),
                'compress': /compress/.test(ele.mime_type),
                'document': /(?:text|msword)/.test(ele.mime_type),
                'image': /image/.test(ele.mime_type),
                'powerpoint': /powerpoint/.test(ele.mime_type),
                'spreadsheet': /excel/.test(ele.mime_type),
                'video': /video/.test(ele.mime_type)
              }]">file-icon</p>
            </el-col>
            <el-col :span="8">
              <p class="file-name">
                <span @click="showView" v-show="chooseList[0] !== ele.id || !hasRename">{{ele.name}}</span>
                <input v-show="chooseList[0] === ele.id && hasRename" class="rename" type="text" v-model="renameVal">
                <span @click="renameConfirm(index)" v-show="chooseList[0] === ele.id && hasRename" class="rename-confirm"></span>
                <span @click="renameCancel" v-show="chooseList[0] === ele.id && hasRename" class="rename-cancel"></span>
              </p>
            </el-col>
            <el-col :span="3">
              <p :class="['file-size', {'hidden': ele.name === 'folder'}]">{{ele.format_size}}</p>
            </el-col>
            <el-col :span="5">
              <p :class="['file-uploader']">file-uploader</p>
            </el-col>
            <el-col :span="4">
              <p class="upload-date">{{date}}</p>
            </el-col>
            <el-col :span="2" v-if="!chooseStatus">
              <div class="more-list" tabindex="100">
                <i></i>
                <ul>
                  <li>查看权限</li>
                  <li>分享</li>
                  <li>下载</li>
                  <li>复制</li>
                  <li>移动</li>
                  <li @click="rename(index)">重命名</li>
                  <li>删除</li>
                </ul>
              </div>
            </el-col>
          </div>
        </el-col>
        <el-col v-for="(ele, index) in list" :key="ele.name + index" :span="4" v-if="curView === 'chunk'">
          <div :class="[{'active' : chooseList.indexOf(ele.id) !== -1}, 'item2']">
            <p v-if="chooseStatus" @click="liClick(ele.id, index)" :class="['file-radio', ele.name]">file-radio</p>
            <p :class="['file-icon', 'other', {
                'folder': /folder/.test(ele.name),
                'artboard': /pdf/.test(ele.name),
                'audio': /audio/.test(ele.name),
                'compress': /compress/.test(ele.name),
                'document': /(?:text|msword)/.test(ele.name),
                'image': /image/.test(ele.name),
                'powerpoint': /powerpoint/.test(ele.name),
                'spreadsheet': /excel/.test(ele.name),
                'video': /video/.test(ele.name)
              }]">file-icon</p>
            <p class="file-name">
              <span @click="showView" v-show="chooseList[0] !== ele.id || !hasRename">{{ele.name}}</span>
              <input v-show="chooseList[0] === ele.id && hasRename" class="rename" type="text" v-model="renameVal">
                <span @click="renameConfirm(index)" v-show="chooseList[0] === ele.id && hasRename" class="rename-confirm"></span>
                <span @click="renameCancel" v-show="chooseList[0] === ele.id && hasRename" class="rename-cancel"></span>
            </p>
            <p class="upload-date">{{date}}</p>
            <div class="more-list" tabindex="200" v-if="!chooseStatus">
              <i></i>
              <ul>
                <li>查看权限</li>
                <li>分享</li>
                <li>下载</li>
                <li>复制</li>
                <li>移动</li>
                  <li @click="rename(index)">重命名</li>
                <li>删除</li>
              </ul>
            </div>
          </div>
        </el-col>
      </el-row>
    </section>
    <div class="view-cover" v-if="viewCover">
      <div class="view-cover-head clearfix">
        <p class="fl">
          <i class="fx fx-icon-nothing-left" @click="closeView"></i>
          <span class="title">图片01.JPG</span>
        </p>
        <p class="fr operate">
          <span class="fl">分享</span>
          <span class="fl">下载</span>
          <span class="fl">移动</span>
          <span class="fl more" tabindex="300">
            <i></i>
            <ul>
              <li>重命名</li>
              <li>删除</li>
              <li>查看权限</li>
              <li @click="showProfile = true">详细信息</li>
            </ul>
          </span>
        </p>
      </div>
      <div class="view-content">
        <div class="image-preview">
          <swiper ref="mySwiper" :options="swiperOption">
            <swiper-slide>
              <img v-lazy="require('assets/images/tools/cloud_drive/123.jpg')" alt="">
            </swiper-slide>
            <swiper-slide>
              <img v-lazy="require('assets/images/tools/cloud_drive/123.jpg')" alt="">
            </swiper-slide>
            <swiper-slide>
              <img v-lazy="require('assets/images/tools/cloud_drive/123.jpg')" alt="">
            </swiper-slide>
            <swiper-slide>
              <img v-lazy="require('assets/images/tools/cloud_drive/123.jpg')" alt="">
            </swiper-slide>
            <swiper-slide>
              <img v-lazy="require('assets/images/tools/cloud_drive/123.jpg')" alt="">
            </swiper-slide>
            <swiper-slide>
              <img v-lazy="require('assets/images/tools/cloud_drive/123.jpg')" alt="">
            </swiper-slide>
            <swiper-slide>
              <img v-lazy="require('assets/images/tools/cloud_drive/123.jpg')" alt="">
            </swiper-slide>
            <div class="swiper-button-prev" slot="button-prev">
              <i class="el-icon-arrow-left"></i>
            </div>
            <div class="swiper-button-next" slot="button-next">
              <i class="el-icon-arrow-right"></i>
            </div>
          </swiper>
        </div>
      </div>
      <div v-show="showProfile" class="file-profile-cover"></div>
      <div v-show="showProfile" class="file-profile">
        <p class="profile-head">详细信息<i class="fx-0 fx-black fx-icon-nothing-close-error"  @click="showProfile = false"></i></p>
        <article class="profile-body">
          <p><span>文件名:</span>20180113_071947740_ios.jpg</p>
          <p><span>类型:</span>图片</p>
          <p><span>创建时间:</span>2018年1月3日</p>
          <p><span>尺寸:</span>2048*2048 px</p>
          <p><span>大小:</span>3.3MB</p>
          <p><span>位置:</span>picture</p>
          <p><span>所有者:</span>张三</p>
          <p><span>查看权限:</span>管理员可见</p>
        </article>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'cloud_content',
  props: {
    chooseStatus: {
      type: Boolean,
      default: false
    },
    isChooseAll: {
      type: String,
      default: 'empty'
    },
    curView: {
      type: String,
      default: 'list'
    },
    list: {
      type: Array,
      default: function () {
        return []
      }
    },
    hasRename: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      date: '2017-03-09',
      chooseList: [],
      showProfile: false, // 显示详情
      viewCover: false, // 显示预览
      swiperOption: {
        lazyLoading: true,
        autoplay: 5000,
        prevButton: '.swiper-button-prev',
        nextButton: '.swiper-button-next',
        spaceBetween: 0
      },
      renameVal: '',
      hideFileName: -1
    }
  },
  methods: {
    liClick(i, index) {
      if (!this.hasRename) {
        this.renameVal = this.list[index]['name']
        let str = ''
        if (this.chooseStatus) {
          if (this.chooseList.indexOf(i) === -1) {
            this.chooseList.push(i)
          } else {
            this.chooseList.splice(this.chooseList.indexOf(i), 1)
          }
          if (this.chooseList.length) {
            if (this.chooseList.length === this.list.length) {
              str = 'all'
            }
          } else {
            str = 'empty'
          }
          this.$emit('choose', this.chooseList, str)
        } else {
          this.chooseList = []
        }
      } else {
        return
      }
    },
    showView() {
      this.viewCover = true
      document.body.setAttribute('class', 'disableScroll')
      document.childNodes[1].setAttribute('class', 'disableScroll')
    },
    closeView () {
      this.viewCover = false
      this.showProfile = false
      document.body.removeAttribute('class', 'disableScroll')
      document.childNodes[1].removeAttribute('class', 'disableScroll')
    },
    renameCancel() {
      this.$emit('renameCancel')
    },
    renameConfirm(index) {
      this.$emit('renameCancel')
      this.$emit('changeName', index, this.renameVal)
    },
    rename() {
    }
  },
  watch: {
    chooseStatus() {
      if (!this.chooseStatus) {
        this.chooseList = []
        this.$emit('choose', this.chooseList, 'empty')
      }
    },
    isChooseAll() {
      if (this.isChooseAll === 'all') {
        for (let i of this.list) {
          this.chooseList.push(i.id)
        }
        this.chooseList = Array.from(new Set(this.chooseList))
        this.$emit('choose', this.chooseList, 'all')
      } else if (this.isChooseAll === 'empty') {
        this.chooseList = []
        this.$emit('choose', this.chooseList, 'empty')
      }
    },
    chooseList() {
      this.hideFileName = this.chooseList[0]
    }
  },
  components: {
    swiper: (resolve) => {
      require(['vue-awesome-swiper/src/swiper'], resolve)
    },
    swiperSlide: (resolve) => {
      require(['vue-awesome-swiper/src/slide'], resolve)
    }
  }
}
</script>
<style scoped>
  section .item {
    height: 70px;
    line-height: 70px;
    border-bottom: 1px solid #d2d2d2;
    background: #fff;
    cursor: pointer;
  }
  
  section .item p, section .item2 p{
    overflow: hidden;
    text-overflow:ellipsis;
    white-space: nowrap;
  }

  .item2 p {
    padding: 0 5px
  }
  section .item2 {
    height: 160px;
    border: 1px solid #f0f0f0;
    border-radius: 10px;
    margin: 10px 0;
    position: relative;
    cursor: pointer;
  }
  section .item:hover, section .item.active {
    background: #f7f7f7
  }

  section .item2:hover, section .item2.active {
    border: 1px solid #d2d2d2;
  }

  .file-radio {
    text-indent: -999em;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 1px solid #d2d2d2;
    background: #fff;
    margin: 25px auto 25px;
    position: relative;
  }
  
  .item2 .file-radio {
    position: absolute;
    left: 10px;
    top: 10px;
    margin: 0;
  }
  .file-radio:before {
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
  
  .active .file-radio {
    border: 1px solid #999;
    background: #999;
  }

  .item2 .file-icon {
    width: 60px;
    height: 60px;
    margin: 16px auto 20px; 
  }
  section .item2 .file-name {
    overflow: initial;
    position: relative;
    height: 20px;
  }
  .item2 .rename {
    position: absolute;
    z-index: 1;
    width: 70%;
    left: -10px;
    top: -10px;
    margin: 0;
  }
  
  .item2 .rename-confirm, .item2 .rename-cancel {
    position: absolute;
    left: 70%;
    top: -10px;
    z-index: 1;
    margin: 0;
  }
  .item2 .rename-cancel {
    left: calc(70% + 35px);
  }
  .rename {
    width: 160px;
    height: 30px;
    margin: 20px 10px 0 3px;
    border-radius: 4px;
    padding: 4px 10px;
    border: none;
    box-shadow: 0 0 2px rgba(0, 0, 0, 0.5)
  }
  
  .rename:focus {
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.5)
  }

  .rename-confirm, .rename-cancel {
    display: inline-block;
    width: 30px;
    height: 30px;
    background: #ff5a5f;
    margin: 20px 10px 0 0;
    opacity: 0.7;
    border-radius: 4px;
    position: relative;
    user-select:none;
  }
  .rename-confirm::before {
    content: "";
    position: absolute;
    width: 2px;
    height: 12px;
    top: 9px;
    left: 17px;
    background: #fff;
    transform: rotate(45deg)
  }
  .rename-confirm::after {
    content: "";
    position: absolute;
    width: 2px;
    height: 6px;
    top: 14px;
    left: 11px;
    background: #fff;
    transform: rotate(-45deg)
  }
  .rename-cancel::before, .rename-cancel::after {
    content: '';
    position: absolute;
    width: 2px;
    height: 14px;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%) rotate(45deg);
    background: #999;
  }
  .rename-cancel::after {
    transform: translate(-50%, -50%) rotate(-45deg);
  }
  .rename-confirm:hover, .rename-cancel:hover {
    opacity: 0.9;
  }
  .rename-confirm:active, .rename-cancel:active {
    opacity: 1;
  }
  .rename-cancel {
    background: #fff;
    border: 1px solid #d2d2d2
  }
  
  .rename-cancel:active {
    background: #d2d2d2;
    border: 1px solid #999;
  }
  .rename-cancel:active::before {
    background: #fff;
  }
  .rename-cancel:active::after {
    background: #fff;
  }
  .file-uploader {
    text-align: center;
  }
  .upload-date {
    text-align: right;
  }

  .more-list i {
    display: block;
    height: 70px;
    background: url('../../../../../assets/images/tools/cloud_drive/jurisdiction/more@2x.png') center no-repeat;
    background-size: 25px
  }
  
  .item2 .more-list i {
    height: 30px;
    background: url('../../../../../assets/images/tools/cloud_drive/jurisdiction/more@2x.png') top no-repeat;
    background-size: 25px
  }
  .item2 .more-list ul {
    left: 50%;
    top: 30px;
  }
  .more-list {
    position: relative;
  }

  .item2 .file-name, .item2 .upload-date {
    text-align: center;
    line-height: 20px;
    font-size: 14px;
  }

  .item2 .upload-date {
    color: #999;
    font-size: 12px;
  }

  .hidden {
    opacity: 0;
  }
  .view-cover {
    position: fixed;
    z-index: 999;
    left: 0;
    top: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
  }
  .view-cover-head {
    line-height: 60px;
    height: 60px;
    padding-left: 30px;
    background: #222;
    color: #fff;
  }
  .view-cover-head p {
    font-size: 18px;
    line-height: 60px;
  }
  .view-cover-head .fl i:hover {
    color: #999;
  }
  .operate {
    color: #666;
  }
  .operate span {
    margin-left: 20px;
    cursor: pointer;
  }
  
  .operate span:hover, .operate .more:hover i {
    color: #fff;
    opacity: 1;
  }

  .operate .more {
    position: relative;
    height: 60px;
    margin-left: 0;
    padding-left: 20px;
  }

  .operate .more:focus ul {
    display: block
  }

  .operate .more i {
    width: 20px;
    height: 20px;
    margin-right: 30px;
    background: url('../../../../../assets/images/tools/cloud_drive/operate/more@2x.png') top no-repeat;
    background-size: contain;
    opacity: 0.5;
  }

  .more ul {
    border-radius: 4px;
    display: none;
    position: fixed;
    z-index: 1;
    background: #fff;
    font-size: 14px;
    right: 25px;
    top: 60px;
    width: 168px;
    box-shadow: 0 0 10px rgba(10, 10, 10, .3);
  }

  .more-list:focus ul {
    display: block;
  }

  .more-list ul {
    display: none;
    border-radius: 4px;
    position: absolute;
    z-index: 1;
    top: 50px;
    left: 0;
    width: 140px;
    background: #fff;
    color: #666;
    box-shadow: 0 0 10px rgba(10, 10, 10, .3);
   }
  .more ul li, .more-list ul li {
    padding: 0 20px;
    color: #999;
    line-height: 40px;
    height: 40px;
  }
  
  .more ul li:first-child {
    border-radius: 4px 4px 0 0
  }
  .more ul li:last-child {
    border-radius: 0 0 4px 4px
  }
  .more ul li:hover, .more-list ul li:hover {
    color: #222;
    background: #f7f7f7
  }

  .file-profile {
    width: 380px;
    height: 100vh;
    position: absolute;
    right: 0;
    top: 60px;
    background: #fff;
  }
  .file-profile-cover {
    position: fixed;
    left: 0;
    top: 60px;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
  }
  .file-profile .profile-head {
    background: #f7f7f7;
    height: 50px;
    border-bottom: 1px solid #d2d2d2;
    font-size: 16px;
    color: #222;
    text-align: center;
    line-height: 50px;
    position: relative;
    font-weight: 500
  }
  .profile-head i {
    position: absolute;
    right: 30px;
    top: 18px;
    color: #999
  }
  .profile-body {
    padding: 16px 20px;
  }
  .profile-body p {
    color: #666;
    font-size: 14px;
    line-height: 28px;
  }
  
  .profile-body p span {
    color: #999;
    margin-right: 10px;
  }
  .view-content {
    padding-top: 30px;
  }
  
  .view-content img {
    display: block;
    margin: 0 auto;
    max-width: 800px;
    max-height: calc(100% - 90px);
  }

  .image-preview {
    max-width: 980px;
    margin: 0 auto;
  }
  .swiper-slide {
    display: flex;
    justify-content: center;
    align-items: center;
  }
</style>
