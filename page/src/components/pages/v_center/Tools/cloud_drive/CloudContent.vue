<template>
  <div class="cloud-content">
    <section>
      <el-row :gutter="20" v-if="list.length">
        <el-col v-for="(ele, index) in list" :key="index" :span="24" v-if="curView === 'list'">
          <div @click="liClick(index)" :class="[{'active' : chooseList.indexOf(index) !== -1}, 'item']">
            <el-col :span="2" v-if="chooseStatus">
              <p class="file-radio">file-radio</p>
            </el-col>
            <el-col :span="2">
            <p :class="['file-icon', 'other', {
                'folder': /folder/.test(ele),
                'artboard': /pdf/.test(ele),
                'audio': /audio/.test(ele),
                'compress': /compress/.test(ele),
                'document': /(?:text|msword)/.test(ele),
                'image': /image/.test(ele),
                'powerpoint': /powerpoint/.test(ele),
                'spreadsheet': /excel/.test(ele),
                'video': /video/.test(ele)
              }]">file-icon</p>
            </el-col>
            <el-col :span="8">
              <p class="file-name" @click="showView">{{ele}}</p>
            </el-col>
            <el-col :span="2">
              <p :class="['file-size', {'hidden': ele === 'folder'}]">{{index}}px</p>
            </el-col>
            <el-col :span="5">
              <p :class="['file-uploader', {'hidden': ele === 'folder'}]">file-uploader</p>
            </el-col>
            <el-col :span="5">
              <p class="upload-date">{{date}}</p>
            </el-col>
          </div>
        </el-col>
        <el-col v-for="(ele, index) in list" :key="ele + index" :span="4" v-if="curView === 'chunk'">
          <div @click="liClick(index)" :class="[{'active' : chooseList.indexOf(index) !== -1}, 'item2']">
            <p v-if="chooseStatus" :class="['file-radio', ele]">file-radio</p>
            <p :class="['file-icon', ele]">file-icon</p>
            <p class="file-name">{{ele}}</p>
            <p class="upload-date">{{date}}</p>
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
          <span class="fl more">
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
      }
    }
  },
  methods: {
    liClick(i) {
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
        for (let i in this.list) {
          this.chooseList.push(Number(i))
        }
        this.chooseList = Array.from(new Set(this.chooseList))
        this.$emit('choose', this.chooseList, 'all')
      } else if (this.isChooseAll === 'empty') {
        this.chooseList = []
        this.$emit('choose', this.chooseList, 'empty')
      }
    }
  },
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
  section div.item {
    height: 70px;
    line-height: 70px;
    border-bottom: 1px solid #d2d2d2;
    background: #fff;
    cursor: pointer;
  }
  section div.item2 {
    height: 160px;
    border: 1px solid #f0f0f0;
    border-radius: 10px;
    margin: 10px 0;
    position: relative;
    cursor: pointer;
  }
  section div.item:hover, section div.item.active {
    background: #f7f7f7
  }

  section div.item2:hover, section div.item2.active {
    border: 1px solid #d2d2d2;
  }

  p.file-radio {
    text-indent: -999em;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 1px solid #d2d2d2;
    background: #fff;
    margin: 25px auto 0;
    position: relative;
  }
  
  .item2 p.file-radio {
    position: absolute;
    left: 10px;
    top: 10px;
    margin: 0;
  }
  p.file-radio:before {
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
  
  .active p.file-radio {    
    border: 1px solid #999;
    background: #999;
  }

  .item2 p.file-icon {
    width: 60px;
    height: 60px;
    margin: 16px auto 20px; 
  }

  p.file-uploader {
    text-align: center;
  }
  p.upload-date {
    text-align: right;
  }
  
  .item2 .file-name, .item2 .upload-date {
    text-align: center;
    line-height: 20px;
    font-size: 14px;
  }

  .item2 .upload-date{
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
  .view-cover-head p.fl i:hover {
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

  .operate .more:hover ul {
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
    line-height: 40px;
    width: 168px;
    box-shadow: 0 0 10px rgba(10, 10, 10, .3);
  }
  
  .more ul li {
    padding: 0 20px;
    color: #999;
  }
  
  .more ul li:first-child {
    border-radius: 4px 4px 0 0
  }
  .more ul li:last-child {
    border-radius: 0 0 4px 4px
  }
  .more ul li:hover {
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
    max-Height: calc(100% - 90px);
  }

  .image-preview {
    max-width: 980px;
    margin: 0 auto;
  }
</style>
