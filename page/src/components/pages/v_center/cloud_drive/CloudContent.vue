<template>
  <div class="cloud-content">
    <ul>
      <li v-for="(ele, index) in list" :key="index" @click="liClick(index)" :class="[{'active' : chooseList.indexOf(index) !== -1}]">
        <el-col :span="2" v-if="chooseStatus">
          <p :class="['file-radio', ele]">file-radio</p>
        </el-col>
        <el-col :span="2">
          <p :class="['file-icon', ele]">file-icon</p>
        </el-col>
        <el-col :span="8">
        <span class="file-name">{{ele}}</span>
        </el-col>
        <el-col :span="2">
          <p class="file-size">{{index}}px</p>
        </el-col>
        <el-col :span="5">
          <p class="file-uploader">file-uploader</p>
        </el-col>
        <el-col :span="5">
          <p class="upload-date">{{date}}</p>
        </el-col>
      </li>
    </ul>
  </div>
</template>
<script>
export default {
  name: 'cloud_content',
  data() {
    return {
      list: ['artboard', 'audio', 'compress', 'document', 'folder', 'image', 'other', 'powerpoint', 'spreadsheet', 'video'],
      date: '2017-03-09',
      chooseList: []
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
      type: Boolean
    },
    isChooseAll: {
      type: String
    }
  }
}
</script>
<style scoped>
  ul {
    border-bottom: 1px solid #d2d2d2;    
  }
  ul li {
    height: 70px;
    line-height: 70px;
    border-top: 1px solid #d2d2d2;
    background: #fff;
    cursor: pointer;
  }
  
  ul li:first-child {
    border-top: none;
  }

  ul li:hover, ul li.active {
    background: #f7f7f7
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
  p.file-icon {
    display: block;
    width: 30px;
    height: 30px;
    margin-top: 20px;
    text-indent: -999em;
  }
  
  p.file-icon.artboard {
    background: url('../../../../assets/images/tools/cloud_drive/type/artboard@2x.png') 0 0 no-repeat;
    background-size: contain;
  }
  p.file-icon.audio {
    background: url('../../../../assets/images/tools/cloud_drive/type/audio@2x.png') 0 0 no-repeat;
    background-size: contain;
  }
  p.file-icon.compress {
    background: url('../../../../assets/images/tools/cloud_drive/type/compress@2x.png') 0 0 no-repeat;
    background-size: contain;
  }
  p.file-icon.document {
    background: url('../../../../assets/images/tools/cloud_drive/type/document@2x.png') 0 0 no-repeat;
    background-size: contain;
  }
  p.file-icon.folder {
    background: url('../../../../assets/images/tools/cloud_drive/type/folder@2x.png') 0 0 no-repeat;
    background-size: contain;
  }
  p.file-icon.image {
    background: url('../../../../assets/images/tools/cloud_drive/type/image@2x.png') 0 0 no-repeat;
    background-size: contain;
  }
  p.file-icon.other {
    background: url('../../../../assets/images/tools/cloud_drive/type/other@2x.png') 0 0 no-repeat;
    background-size: contain;
  }
  p.file-icon.powerpoint {
    background: url('../../../../assets/images/tools/cloud_drive/type/powerpoint@2x.png') 0 0 no-repeat;
    background-size: contain;
  }
  p.file-icon.spreadsheet {
    background: url('../../../../assets/images/tools/cloud_drive/type/spreadsheet@2x.png') 0 0 no-repeat;
    background-size: contain;
  }
  p.file-icon.video {
    background: url('../../../../assets/images/tools/cloud_drive/type/video@2x.png') 0 0 no-repeat;
    background-size: contain;
  }

  p.file-uploader {
    text-align: center;
  }
  p.upload-date {
    text-align: right;
  }
</style>
