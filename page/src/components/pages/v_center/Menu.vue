<template>

  <el-col :span="4" class="left-menu">

    <div class="menu-list" v-if="isCompany()">
      <!--
      <router-link :to="{name: 'vcenterItemList'}" class="item">
        项目动态
      </router-link>
      <router-link :to="{name: 'vcenterOrderList'}" class="item">
        项目订单
      </router-link>
      -->
      <router-link :to="{name: 'vcenterCItemList'}" :class="{'item': true, 'is-active': currentName === 'c_item' ? true : false}">
        我的项目
      </router-link>
      <router-link :to="{name: 'vcenterDesignCaseList'}" class="item">
        作品案例
      </router-link>
      <!--
      <router-link :to="{name: 'vcenterComputerAccreditation'}" class="item">
        公司信息
      </router-link>
      -->
      <router-link :to="{name: 'vcenterComputerBase'}" :class="{'item': true, 'is-active': currentName === 'profile' ? true : false}">
        账号设置
      </router-link>
    </div>

    <div class="menu-list" v-else>
      <router-link :to="{name: 'vcenterItemList'}" :class="{'item': true, 'is-active': currentName === 'item' ? true : false}">
        我的项目
      </router-link>
      <router-link :to="{name: 'vcenterOrderList'}" :class="{'item': true, 'is-active': currentName === 'order' ? true : false}">
        项目订单
      </router-link>
      <router-link :to="{name: 'vcenterDComputerBase'}" :class="{'item': true, 'is-active': currentName === 'profile' ? true : false}">
        账号设置
      </router-link>
    </div>

    <div class="computer-btn" v-if="isCompany()">
      <el-button @click="redirectCompany">查看公司主页</el-button>
    </div>
  </el-col>
</template>

<script>
export default {
  name: 'vcenter_menu',
  props: {
    currentName: {
      default: ''
    }
  },
  data () {
    return {
      msg: 'This is menu'
    }
  },
  // 判断是客户还是设计公司
  methods: {
    isCompany() {
      var uType = this.$store.state.event.user.type
      if (uType === 2) {
        return true
      } else {
        return false
      }
    },
    redirectCompany() {
      var companyId = this.$store.state.event.user.design_company_id
      if (!companyId || companyId === 0) {
        this.$message.error('请先申请公司认证!')
      } else {
        this.$router.push({name: 'companyShow', params: {id: companyId}})
      }
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

  .menu-list .item {
    font-size: 1.3rem;
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
