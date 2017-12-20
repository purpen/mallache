<template>
  <el-col :span="4">
    <div class="admin-menu">
      <el-menu :default-active="selectedName" class="" @select="handleSelect" @open="handleOpen" @close="handleClose" router>
        <el-submenu index="1">
          <template slot="title"><i class="fa fa-tachometer"></i> 控制台</template>
            <el-menu-item index="dashBoard" :route="{name: 'adminDashBoard'}">概览</el-menu-item>
        </el-submenu>
        <el-submenu index="2">
          <template slot="title"><i class="fa fa-file-text"></i> 项目管理</template>
            <el-menu-item index="itemList" :route="{name: 'adminItemList'}">列表</el-menu-item>
        </el-submenu>

        <el-submenu index="3">
          <template slot="title"><i class="fa fa-product-hunt"></i> 设计公司管理</template>
            <el-menu-item index="companyList" :route="{name: 'adminCompanyList'}">列表</el-menu-item>
        </el-submenu>
        <el-submenu index="4">
          <template slot="title"><i class="fa fa-product-hunt"></i> 需求公司管理</template>
            <el-menu-item index="demandCompanyList" :route="{name: 'adminDemandCompanyList'}">列表</el-menu-item>
        </el-submenu>
        <el-submenu index="5">
          <template slot="title"><i class="fa fa-file-text"></i> 订单管理</template>
            <el-menu-item index="orderList" :route="{name: 'adminOrderList'}">列表</el-menu-item>
        </el-submenu>
        <el-submenu index="6">
          <template slot="title"><i class="fa fa-money"></i> 提现管理</template>
            <el-menu-item index="withDrawList" :route="{name: 'adminWithDrawList'}">列表</el-menu-item>
        </el-submenu>
        <el-submenu index="7">
          <template slot="title"><i class="fa fa-clipboard"></i> 案例管理</template>
            <el-menu-item index="designCaseList" :route="{name: 'adminDesignCaseList'}">列表</el-menu-item>
        </el-submenu>
        <el-submenu index="15">
          <template slot="title"><i class="fa fa-window-maximize" aria-hidden="true"></i> 内容管理</template>
            <el-menu-item index="columnList" :route="{name: 'adminColumnList'}">栏目列表</el-menu-item>
            <el-menu-item index="articleList" :route="{name: 'adminArticleList'}">文章列表</el-menu-item>
            <el-menu-item index="worksList" :route="{name: 'adminWorksList'}">作品列表</el-menu-item>
            <el-menu-item index="awardsList" :route="{name: 'adminAwardsList'}">日历列表</el-menu-item>
            <el-menu-item index="trendReportList" :route="{name: 'adminTrendReportList'}">趋势/报告列表</el-menu-item>
            <el-menu-item index="commonlySiteList" :route="{name: 'adminCommonlySiteList'}">常用网站列表</el-menu-item>
        </el-submenu>
        <el-submenu index="18">
          <template slot="title"><i class="fa fa-cogs"></i> 系统管理</template>
            <el-menu-item index="categoryList" :route="{name: 'adminCategoryList'}">分类列表</el-menu-item>
        </el-submenu>
        <el-submenu index="20">
          <template slot="title"><i class="fa fa-user"></i> 用户管理</template>
            <el-menu-item index="userList" :route="{name: 'adminUserList'}">列表</el-menu-item>
        </el-submenu>
      </el-menu>    
    </div>

  </el-col>
</template>

<script>
export default {
  name: 'admin_menu',
  props: {
    selectedName: {
      default: 'dashBoard'
    }
  },
  data () {
    return {
      msg: ''
    }
  },
  methods: {
    handleOpen(key, keyPath) {
      console.log(key, keyPath)
    },
    handleClose(key, keyPath) {
      console.log(key, keyPath)
    },
    handleSelect(key, keyPath) {
      console.log(key, keyPath)
    }
  },
  created: function() {
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .admin-menu {
    font-size: 1rem;
  }

</style>
