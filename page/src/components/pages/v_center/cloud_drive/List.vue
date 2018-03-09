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
                    <span>上传文件</span>
                  </a>
                </span>
              </p>
              <p class="search"></p>
              <p class="chunk"></p>
              <p class="sequence"></p>
              <p class="edit" @click="changeChooseStatus"></p>
            </div>

            <p class="edit-menu" v-if="isChoose">
              <el-col :span="2">
                <i :class="['file-radio', {'active': isChooseAll === 'all'}]" @click="changeChooseAll">file-icon</i>
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
          <vContent :chooseStatus="isChoose" @choose="chooseList" :isChooseAll="isChooseAll"></vContent>
        </div>
      </el-col>
    </el-row>
  </div>
</template>
<script>
  import vMenu from '@/components/pages/v_center/cloud_drive/Menu'
  import vContent from '@/components/pages/v_center/cloud_drive/CloudContent'
  export default {
    name: 'cloud_drive',
    data() {
      return {
        test: 'test',
        title: '全部文件',
        isChoose: false, // 切换为选择状态
        alreadyChoose: 0, // 已选择数目
        isChooseAll: '' // 是否全选
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
    transform: scale(1.1)
  }
  .operate p.add:hover .add-option {
    display: block;
  }

  .operate p.add {
    background: url('../../../../assets/images/tools/cloud_drive/operate/add@2x.png') top no-repeat;
    background-size: 24px
  }
  .operate p.search {
    background: url('../../../../assets/images/tools/cloud_drive/operate/search@2x.png') top no-repeat;
    background-size: 24px
  }
  .operate p.chunk {
    background: url('../../../../assets/images/tools/cloud_drive/operate/chunk@2x.png') top no-repeat;
    background-size: 24px
  }
  .operate p.sequence {
    background: url('../../../../assets/images/tools/cloud_drive/operate/sequence@2x.png') top no-repeat;
    background-size: 24px
  }
  .operate p.edit {
    background: url('../../../../assets/images/tools/cloud_drive/operate/edit@2x.png') top no-repeat;
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
    background: url('../../../../assets/images/tools/cloud_drive/operate/add_folder@2x.png') no-repeat;
    background-size: contain
  }
  .add-option .upload-files span:before {
    background: url('../../../../assets/images/tools/cloud_drive/operate/upload@2x.png') no-repeat;
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
</style>
