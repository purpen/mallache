<template>
  <div class="min-height350">
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
              <p class="addr">北京市 朝阳区 酒仙桥路4号 751北京时尚设计广场 B7栋</p>
              <p class="phone">010-84799327</p>
              <p class="mail">support@taihuoniao.com</p>
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
  data() {
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
  line-height: 20px;
  padding-left: 30px;
  margin: 5px 0
}

.content-box p img {
  vertical-align: top;
  margin-right: 10px;
}

p.addr {
  background: url("../../../assets/images/icon/iconfont-shouhuodizhi.png") no-repeat -2px top;
  background-size: 20px;
}

p.phone {
  background: url("../../../assets/images/icon/iconfont-tel.png") no-repeat -1px top;
  background-size: 20px;
}

p.mail {
  background: url("../../../assets/images/icon/iconfont-email.png") no-repeat 0px top;
  background-size: 20px;
}

@media screen and (max-width: 767px) {
  .content-box {
    padding: 20px;
    padding-top: 0
  }
  .h-right-content .content-box {
    margin-top: 1px;
  }
}
</style>
