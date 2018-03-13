<template>
  <div class="cloud-content">
    <section>
      <el-row :gutter="20">
        <el-col v-for="(ele, index) in list" :key="index" :span="24" v-if="curView === 'list'">
          <div @click="liClick(index)" :class="[{'active' : chooseList.indexOf(index) !== -1}, 'item']">
            <el-col :span="2" v-if="chooseStatus">
              <p :class="['file-radio', ele]">file-radio</p>
            </el-col>
            <el-col :span="2">
              <p :class="['file-icon', ele]">file-icon</p>
            </el-col>
            <el-col :span="8">
            <p class="file-name">{{ele}}</p>
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
  </div>
</template>
<script>
export default {
  name: 'cloud_content',
  data() {
    return {
      list: ['folder', 'artboard', 'audio', 'compress', 'document', 'image', 'other', 'powerpoint', 'spreadsheet', 'video'],
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
    },
    curView: {
      type: String
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
</style>
