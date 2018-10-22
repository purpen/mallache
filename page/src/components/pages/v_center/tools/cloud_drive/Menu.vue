<template>
  <div class="menu">
    <h3><router-link class="clearfix" to="/vcenter/control"><i class="fx fx-icon-nothing-left"></i>设计云盘</router-link></h3>
    <el-collapse v-model="activeNames" class="cloud-menu">
      <el-collapse-item title="设计云盘" name="1">
        <ul class="cloud-classify">
          <li>
            <a :class="['files', {'active': isActive === '全部文件'}]" @click="changeTitle('全部文件')">
              <span>全部文件</span>
            </a>
          </li>
          <li>
            <a :class="['project', {'active': isActive === '项目'}]" @click="changeTitle('项目')">
              <span>项目</span>
              </a>
          </li>
          <li>
            <a :class="['recently', {'active': isActive === '最近使用'}]" @click="changeTitle('最近使用')">
              <span>最近使用</span>
            </a>
          </li>
          <li>
            <a :class="['recycle', {'active': isActive === '回收站'}]" @click="changeTitle('回收站')">
              <span>回收站</span>
            </a>
          </li>
        </ul>
      </el-collapse-item>
      <el-collapse-item title="项目" name="2">
      </el-collapse-item>
      <el-collapse-item title="文件类型" name="3">
        <ul class="cloud-classify">
          <li>
            <a class="image">
              <span>图片</span>
            </a>
          </li>
          <li>
            <a class="artboard">
              <span>PDF</span>
              </a>
          </li>
          <li>
            <a class="document">
              <span>文档</span>
            </a>
          </li>
          <li>
            <a class="spreadsheet">
              <span>电子表格</span>
            </a>
          </li>
          <li>
            <a class="powerpoint">
              <span>演示文稿</span>
            </a>
          </li>
          <li>
            <a class="folder">
              <span>文件夹</span>
            </a>
          </li>
          <li>
            <a class="video">
              <span>视频</span>
            </a>
          </li>
          <li>
            <a class="audio">
              <span>音频</span>
            </a>
          </li>
        </ul>
      </el-collapse-item>
    </el-collapse>
  </div>
</template>
<script>
  export default {
    name: 'cloud_drive_menu',
    data() {
      return {
        test: 'test',
        activeNames: ['1', '3'],
        isActive: '全部文件'
      }
    },
    methods: {
      changeTitle(e) {
        this.$emit('getTitle', e)
        this.isActive = e
      }
    }
  }
</script>
<style scoped>
  h3 {
    color: #666;
    font-size: 16px;
    line-height: 20px;
    height: 30px;
    border-bottom: 1px solid #D2D2D2;
  }
  h3 i {
    margin-right: 10px;
  }
  .cloud-classify {
    padding-left: 15px;
  }
  .cloud-classify li {
    font-size: 0;
    color: #222;
  }
  .cloud-classify li a {
    display: block;
    position: relative;
    padding-left: 44px;
    cursor: pointer;
  }
  .cloud-classify li a:before {
    content: '';
    position: absolute;
    width: 24px;
    height: 24px;
    left: 10px;
    top: 8px;
    background: url('../../../../../assets/images/tools/cloud_drive/file@2x.png') center no-repeat;
    background-size: contain
  }
  
  .cloud-classify li a.project:before {
    background: url('../../../../../assets/images/tools/cloud_drive/project@2x.png') center no-repeat;
    background-size: contain
  }
  
  .cloud-classify li a.recently:before {
    background: url('../../../../../assets/images/tools/cloud_drive/recently@2x.png') center no-repeat;
    background-size: contain
  }
  
  .cloud-classify li a.recycle:before {
    background: url('../../../../../assets/images/tools/cloud_drive/recycle@2x.png') center no-repeat;
    background-size: contain
  }

  .cloud-classify li a.image:before {
    background: url('../../../../../assets/images/tools/cloud_drive/type_menu/image@2x.png') 0 0 no-repeat;
    background-size: contain
  }

  .cloud-classify li a.artboard:before {
    background: url('../../../../../assets/images/tools/cloud_drive/type_menu/artboard@2x.png') 0 0 no-repeat;
    background-size: contain
  }

  .cloud-classify li a.document:before {
    background: url('../../../../../assets/images/tools/cloud_drive/type_menu/document@2x.png') 0 0 no-repeat;
    background-size: contain
  }

  .cloud-classify li a.spreadsheet:before {
    background: url('../../../../../assets/images/tools/cloud_drive/type_menu/spreadsheet@2x.png') 0 0 no-repeat;
    background-size: contain
  }

  .cloud-classify li a.powerpoint:before {
    background: url('../../../../../assets/images/tools/cloud_drive/type_menu/powerpoint@2x.png') 0 0 no-repeat;
    background-size: contain
  }

  .cloud-classify li a.folder:before {
    background: url('../../../../../assets/images/tools/cloud_drive/type_menu/folder@2x.png') 0 0 no-repeat;
    background-size: contain
  }

  .cloud-classify li a.video:before {
    background: url('../../../../../assets/images/tools/cloud_drive/type_menu/video@2x.png') 0 0 no-repeat;
    background-size: contain
  }

  .cloud-classify li a.audio:before {
    background: url('../../../../../assets/images/tools/cloud_drive/type_menu/audio@2x.png') 0 0 no-repeat;
    background-size: contain
  }
  

  .cloud-classify li span {
    font-size: 16px;
    line-height: 40px;
  }
  .cloud-classify li:hover a, .active {
    background: #f7f7f7;
  }
  .active {
    color: #ff5a5f
  }
</style>
