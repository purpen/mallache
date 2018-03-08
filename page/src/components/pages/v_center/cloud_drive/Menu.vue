<template>
<el-col :xs="24" :sm="4" :md="4" :lg="4">
  <h3><router-link class="clearfix" to="/vcenter/control"><i class="fx fx-icon-nothing-left"></i>设计云盘</router-link></h3>
  <el-collapse v-model="activeNames" class="cloud-menu">
    <el-collapse-item title="设计云盘" name="1">
      <ul class="cloud-classify">
        <li>
          <router-link to="" class="files">
            <span>全部文件</span>
          </router-link>
        </li>
        <li>
          <router-link to="" class="project">
            <span>项目</span>
            </router-link>
        </li>
        <li>
          <router-link to="" class="recently">
            <span>最近使用</span>
          </router-link>
        </li>
        <li>
          <router-link to="" class="recycle">
            <span>回收站</span>
          </router-link>
        </li>
      </ul>
    </el-collapse-item>
    <el-collapse-item title="项目" name="2">
    </el-collapse-item>
    <el-collapse-item title="文件类型" name="3">
      <ul class="cloud-classify">
        <li>
          <router-link to="" class="image">
            <span>图片</span>
          </router-link>
        </li>
        <li>
          <router-link to="" class="artboard">
            <span>PDF</span>
            </router-link>
        </li>
        <li>
          <router-link to="" class="document">
            <span>文档</span>
          </router-link>
        </li>
        <li>
          <router-link to="" class="spreadsheet">
            <span>电子表格</span>
          </router-link>
        </li>
        <li>
          <router-link to="" class="powerpoint">
            <span>演示文稿</span>
          </router-link>
        </li>
        <li>
          <router-link to="" class="folder">
            <span>文件夹</span>
          </router-link>
        </li>
        <li>
          <router-link to="" class="video">
            <span>视频</span>
          </router-link>
        </li>
        <li>
          <router-link to="" class="audio">
            <span>音频</span>
          </router-link>
        </li>
      </ul>
    </el-collapse-item>
  </el-collapse>
</el-col>
</template>
<script>
  export default {
    name: 'cloud_drive_menu',
    data() {
      return {
        test: 'test',
        activeNames: ['1', '3']
      }
    }
  }
</script>
<style scoped>
  h3 {
    color: #666;
    padding-left: 15px;
    font-size: 16px;
    line-height: 2;
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
    padding-left: 38px;
  }
  .cloud-classify li a:before {
    content: '';
    position: absolute;
    width: 24px;
    height: 24px;
    left: 0;
    top: 8px;
    background: url('../../../../assets/images/tools/cloud_drive/file@2x.png') 2px 0 no-repeat;
    background-size: contain
  }
  
  .cloud-classify li a.project:before {
    background: url('../../../../assets/images/tools/cloud_drive/project@2x.png') 2px 0 no-repeat;
    background-size: contain
  }
  
  .cloud-classify li a.recently:before {
    background: url('../../../../assets/images/tools/cloud_drive/recently@2x.png') 2px 0 no-repeat;
    background-size: contain
  }
  
  .cloud-classify li a.recycle:before {
    background: url('../../../../assets/images/tools/cloud_drive/recycle@2x.png') 2px 0 no-repeat;
    background-size: contain
  }

  .cloud-classify li a.image:before {
    background: url('../../../../assets/images/tools/cloud_drive/type_menu/image@2x.png') 0 0 no-repeat;
    background-size: contain
  }

  .cloud-classify li a.artboard:before {
    background: url('../../../../assets/images/tools/cloud_drive/type_menu/artboard@2x.png') 0 0 no-repeat;
    background-size: contain
  }

  .cloud-classify li a.document:before {
    background: url('../../../../assets/images/tools/cloud_drive/type_menu/document@2x.png') 0 0 no-repeat;
    background-size: contain
  }

  .cloud-classify li a.spreadsheet:before {
    background: url('../../../../assets/images/tools/cloud_drive/type_menu/spreadsheet@2x.png') 0 0 no-repeat;
    background-size: contain
  }

  .cloud-classify li a.powerpoint:before {
    background: url('../../../../assets/images/tools/cloud_drive/type_menu/powerpoint@2x.png') 0 0 no-repeat;
    background-size: contain
  }

  .cloud-classify li a.folder:before {
    background: url('../../../../assets/images/tools/cloud_drive/type_menu/folder@2x.png') 0 0 no-repeat;
    background-size: contain
  }

  .cloud-classify li a.video:before {
    background: url('../../../../assets/images/tools/cloud_drive/type_menu/video@2x.png') 0 0 no-repeat;
    background-size: contain
  }

  .cloud-classify li a.audio:before {
    background: url('../../../../assets/images/tools/cloud_drive/type_menu/audio@2x.png') 0 0 no-repeat;
    background-size: contain
  }
  

  .cloud-classify li span {
    font-size: 16px;
    line-height: 40px;
  }
  .cloud-classify li:hover a {
    background: #f7f7f7;
    color: #ff5a5f
  }
</style>
