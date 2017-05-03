<template>
  <div class="content-box">

        <!-- swiper -->
        <swiper :options="swiperOption" class="swiper-container" :style="screenWidth">
          <swiper-slide class="swiper-slide" style="background-image: url('https://p4.taihuoniao.com/asset/170228/58b53fb420de8d36438bb0c6-1');"></swiper-slide>
          <swiper-slide class="swiper-slide" style="background-image: url('https://p4.taihuoniao.com/asset/170303/58b8e88b20de8d006f8be4cc-1');"></swiper-slide>
          <swiper-slide class="swiper-slide" style="background-image: url('https://p4.taihuoniao.com/asset/170113/58782624fc8b1236368b6c58-1');"></swiper-slide>

          <div class="swiper-pagination" slot="pagination"></div>
          <div class="swiper-button-prev" slot="button-prev"></div>
          <div class="swiper-button-next" slot="button-next"></div>
        </swiper>

    <div class="container">
    </div>

  </div>
</template>

<script>
// or require
// var Vue = require('vue')

// mount with component(can't work in Nuxt.js/SSR)
import { swiper, swiperSlide } from 'vue-awesome-swiper'

export default {
  name: 'test',
  data () {
    return {
      screenWidth: document.body.clientWidth,   // 这里是给到了一个默认值 （这个很重要）
      slideList: [
        {
          'clickUrl': '#',
          'desc': 'nhwc',
          'image': 'http://dummyimage.com/1745x492/f1d65b'
        },
        {
          'clickUrl': '#',
          'desc': 'hxrj',
          'image': 'http://dummyimage.com/1745x492/40b7ea'
        }

      ],
      currentIndex: 0,
      timer: '',

      swiperOption: {
        pagination: '.swiper-pagination',
        nextButton: '.swiper-button-next',
        prevButton: '.swiper-button-prev',
        paginationClickable: true,
        spaceBetween: 30,
        centeredSlides: true,
        autoplay: 2500,
        autoplayDisableOnInteraction: false
      }

    }
  },

  // you can find current swiper instance object like this, while the notNextTick property value must be true
  // 如果你需要得到当前的swiper对象来做一些事情，你可以像下面这样定义一个方法属性来获取当前的swiper对象，同时notNextTick必须为true
  computed: {
    swiper() {
      return this.$refs.mySwiper.swiper
    }
  },
  mounted () {
    const that = this
    window.onresize = () => {
      return (() => {
        window.screenWidth = document.body.clientWidth
        that.screenWidth = window.screenWidth
      })()
    }
    // you can use current swiper instance object to do something(swiper methods)
    // 然后你就可以使用当前上下文内的swiper对象去做你想做的事了
    // console.log('this is current swiper instance object', this.swiper)
    // this.swiper.slideTo(3, 1000, false)
  },

  watch: {

  },

  components: {
    swiper,
    swiperSlide
  },

  methods: {
    created () {
      this.$nextTick(() => {
        this.timer = setInterval(() => {
          this.autoPlay()
        }, 4000)
      })
    },
    go () {
      this.timer = setInterval(() => {
        this.autoPlay()
      }, 4000)
    },
    stop () {
      clearInterval(this.timer)
      this.timer = null
    },
    change (index) {
      this.currentIndex = index
    },
    autoPlay () {
      this.currentIndex++
      if (this.currentIndex > this.slideList.length - 1) {
        this.currentIndex = 0
      }
    }
  },
  created: function() {
  }

}

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .container{
  }
  h1{
    font-size: 60px;
    text-align: center;
  }

  .swiper-container {
      width: 100%;
      height: 420px;
  }
  .swiper-slide {
      text-align: center;
      font-size: 18px;
      background: #fff;

      /* Center slide text vertically */
      display: -webkit-box;
      display: -ms-flexbox;
      display: -webkit-flex;
      display: flex;
      -webkit-box-pack: center;
      -ms-flex-pack: center;
      -webkit-justify-content: center;
      justify-content: center;
      -webkit-box-align: center;
      -ms-flex-align: center;
      -webkit-align-items: center;
      align-items: center;

      background-size: 100% 100%;
      -webkit-background-size: cover;   
      -moz-background-size: cover;
      -o-background-size: cover;

  }

</style>
