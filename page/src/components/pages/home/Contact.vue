<template>
  <div class="min-height350">
    <div class="blank20"></div>
    <div class="container">
      <div class="top-menu-list">
        <router-link :to="{name: 'contact'}" class="item is-active">联系我们</router-link>
        <router-link :to="{name: 'about'}" class="item">关于我们</router-link>
      </div>
    </div>
    <div class="line"></div>
    <div class="container">
      <el-row :gutter="20">

        <el-col :span="24">
          <div class="h-right-content">

            <div class="content-box">
              <h3>联系我们</h3>
              <p><img src="../../../assets/images/icon/iconfont-shouhuodizhi.png" width="20" /> 北京市朝阳区 酒仙桥路4号 751北京时尚设计广场B7栋</p>
              <p><img src="../../../assets/images/icon/iconfont-tel.png" width="20" /> 010-84799327</p>
              <p><img src="../../../assets/images/icon/iconfont-email.png" width="20" /> support@taihuoniao.com</p>
            </div>

          </div>

        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script>

  export default {
    name: 'contact',
    components: {
    },
    data () {
      return {
        test: ''
      }
    },
    methods: {
    },
    created: function() {
      document.body.scrollTop = 0
      document.documentElement.scrollTop = 0
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .container {
    min-height: ;
  }

  .line {
    border-bottom: 1px solid #ccc;
  }

  .content-box {
    padding: 20px 120px;
  }
  .content-box h3 {
    font-size: 1.8rem;
    line-height: 2;
    margin: 10px 0;
  }

  .content-box p {
    line-height: 2;
  }
  .content-box p img {
    vertical-align: middle;
  }


</style>
