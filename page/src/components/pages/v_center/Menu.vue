<template>

  <el-col :span="4" class="left-menu">

    <div class="menu-list">
      <router-link :to="{name: 'vcenterItemList'}" class="item">
        项目动态
      </router-link>
      <router-link :to="{name: 'vcenterOrderList'}" class="item">
        项目订单
      </router-link>
      <router-link :to="{name: 'vcenterDesignCaseList'}" class="item">
        作品案例
      </router-link>
      <router-link :to="{name: 'vcenterWalletList'}" class="item">
        我的钱包
      </router-link>
      <router-link :to="{name: 'vcenterComputerAccreditation'}" class="item">
        公司信息
      </router-link>
      <router-link :to="{name: 'vcenterProfile'}" class="item">
        账号设置
      </router-link>

    </div>

    <div class="computer-btn">
      <el-button><router-link :to="{path: '/user/1'}">查看公司主页</router-link></el-button>
    </div>
  </el-col>
</template>

<script>
export default {
  name: 'vcenter_menu',
  data () {
    return {
      msg: 'This is menu'
    }
  }
}

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .left-menu {
  }

  .menu-list {
    padding: 0 0 0 0;
  }

  .menu-list .item {
    display: block;
    cursor: pointer;
    border: none;
    height: auto;
    text-align: center;
    line-height: 1em;
    color: rgba(112,123,135,.92);
    text-transform: none;
    font-weight: 400;
    padding: .85rem 1.5rem .85rem 1.5rem!important;
  }

  .menu-list .item:hover {
    color: #222;
    background-color: #fff;
  }

  .item.is-active {
    background-color: #fff;
    color: #222;
    font-weight: bold;

  }

  .computer-btn {
    margin-top: 30px;
    text-align: center;
  }
  .computer-btn a {
    font-size: 1.2rem;
  }

</style>
