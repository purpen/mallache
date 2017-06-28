<template>

  <el-col :span="4" class="left-menu">
    <div class="blank20"></div>
    <div class="menu-list">
      <router-link :to="{name: 'question', query: {name: 'auth'}}" :class="{'item': true, 'is-active': currentName === 'auth' ? true : false}">
        注册与登录
      </router-link>
      <router-link :to="{name: 'vcenterOrderList'}" :class="{'item': true, 'is-active': currentName === 'ident' ? true : false}">
        关于实名认证
      </router-link>
      <router-link :to="{name: 'vcenterCItemList'}" :class="{'item': true, 'is-active': currentName === 'cooper' ? true : false}">
        合作双方情形
      </router-link>
      <router-link :to="{name: 'vcenterDesignCaseList'}" class="item">
        您最关心的资金问题
      </router-link>
      <router-link :to="{name: 'vcenterWalletList'}" :class="{'item': true, 'is-active': currentName === 'wallet' ? true : false}">
        关于实行时效问题
      </router-link>
      <router-link :to="{name: 'vcenterComputerBase'}" :class="{'item': true, 'is-active': currentName === 'profile' ? true : false}">
        关于项目异议的处理
      </router-link>
      <router-link :to="{name: 'modifyPwd'}" :class="{'item': true, 'is-active': currentName === 'modify_pwd' ? true : false}">
        关于违规词语
      </router-link>
      <router-link :to="{name: 'vcenterWalletList'}" :class="{'item': true, 'is-active': currentName === 'wallet' ? true : false}">
        关于设计商品版权
      </router-link>
      <router-link :to="{name: 'vcenterComputerBase'}" :class="{'item': true, 'is-active': currentName === 'profile' ? true : false}">
        关于保密协议
      </router-link>
      <router-link :to="{name: 'modifyPwd'}" :class="{'item': true, 'is-active': currentName === 'modify_pwd' ? true : false}">
        意见与反馈
      </router-link>
    </div>

  </el-col>
</template>

<script>
export default {
  name: 'question_menu',
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
  // 方法集
  methods: {
  }
}

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>


</style>
