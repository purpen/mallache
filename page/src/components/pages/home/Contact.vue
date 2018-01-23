<template>
  <div class="min-height350">
    <div class="container">
      <div class="top-menu-list">
        <router-link :to="{name: 'contact'}" class="item is-active">联系我们</router-link>
        <router-link :to="{name: 'about'}" class="item">关于我们</router-link>
      </div>
    </div>
    <div class="line"></div>
    <el-row :gutter="20">
      <el-col :span="24">
        <div class="h-right-content">
          <div class="banner" :style="{height: calcHeight}"></div>
          <div class="container content-box">
            <h3>联系我们</h3>
            <a class="contact"><i class="fx-2 fx-icon-personal-center"></i>马哲</a>
            <a class="phone" href="tel:15711016577"><i class="fx-2 fx-icon-phone"></i>15711016577</a>
            <a class="tel" href="tel:010-84799327"><i class="fx-2 fx-icon-telephone"></i>010-84799327</a>
            <a class="mail" href="mailto:products@taihuoniao.com"><i class="fx-2 fx-icon-mail"></i>products@taihuoniao.com</a>
            <a class="addr"><i class="fx-2 fx-icon-address"></i>北京市 朝阳区 酒仙桥路4号 751北京时尚设计广场 B7栋</a>
          </div>
        </div>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import { calcImgSize } from 'assets/js/common'
export default {
  name: 'contact',
  components: {
  },
  data() {
    return {
      test: '',
      calcHeight: ''
    }
  },
  methods: {
  },
  created: function() {
    document.body.scrollTop = 0
    document.documentElement.scrollTop = 0
  },
  mounted() {
    window.addEventListener('resize', () => {
      this.calcHeight = calcImgSize(400, 1440)
    })
    this.calcHeight = calcImgSize(400, 1440)
  }
}

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

.h-right-content .content-box {
  margin-top: 20;
}

.banner {
  width: 100%;
  min-height: 180px;
  background: url('../../../assets/images/home/Contactus@2x.png') no-repeat center;
  background-size: cover;
  text-align: center;
  flex-wrap: wrap;
  align-content: center
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

.content-box a {
  font-size: 1.4rem;
  color: #666;
  display: block;
  line-height: 20px;
  margin: 10px 0
}

.content-box a:hover {
  color: #222
}

.content-box a i{
  margin-right: 10px;
}

@media screen and (max-width: 767px) {
  .content-box {
    padding: 20px;
  }
  .h-right-content .content-box {
    margin-top: 1px;
  }
}
</style>
