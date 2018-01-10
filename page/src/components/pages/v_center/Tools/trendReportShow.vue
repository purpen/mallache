<template>
  <div class="container">
    <div class="trend-report">
      <div class="blank20"></div>
      <el-row :gutter="24" class="anli-elrow">
        <v-menu currentName="trendReport" v-if="menuStatus !== 'tools' || !isMob"></v-menu>
        <ToolsMenu v-if="menuStatus === 'tools' && isMob"
                   currentName="trendReport"></ToolsMenu>
        <el-col :span="isMob ? 24 : 20">
          <p class="title">{{pdf.title}}</p>
          <p class="info clearfix">
            <span class="date fl">{{pdf.created_at}}</span>
            <span class="view fl">{{pdf.hits}}</span>
            <span class="tigs fl">
              <b class="fl">标签:</b>
              <i v-for="(e, i) in pdf.tag" :key="i">{{e}}</i>
            </span>
            </p>
          <div v-if="already" :class="['pdf', 'swiper-container', {'fullscreen-pdf' : isFullscreen}]" ref="pdf">
            <pdf :src="pdf.image.file"
              v-loading.body="isLoading"
              @progress="loadedRatio = $event"
              @loaded ="load = $event"
              @num-pages="numPages = $event"
              @error="error"
              :rotate="rotate"
              :page="page">
            </pdf>
            <p class="flip" v-if="numPages">
              <span class="swiper-button-prev fl" @click="prev">
                <i class="el-icon-arrow-left"></i>
              </span>
              <span class="swiper-button-next fr" @click="next">
                <i class="el-icon-arrow-right"></i>
              </span>
            </p>
            <div class="fullscreen-tools" v-if="isFullscreen">
              <span class="exit-fullscreen" @click="exitFullscreen"></span>
            </div>
            <menu class="clearfix" v-if="!isFullscreen">
              <menuitem class="add" @click="prev">add</menuitem>
              <menuitem class="subtract" @click="next">subtract</menuitem>
              <menuitem class="rotate" @click="rotate += 90">rotate</menuitem>
              <menuitem class="fullscreen" @click="fullscreen">fullscreen</menuitem>
              <p class="total-page fr">
                <span>共{{numPages}}页</span>前往
                <input type="text" class="page-input" v-model.number="page" @blur="gotoPage(page)">
                页
              </p>
            </menu>
          </div>
        </el-col>
      </el-row>
    </div>
  </div>
</template>
<script>
  import api from '@/api/api'
  import vMenu from '@/components/pages/v_center/Menu'
  import ToolsMenu from '@/components/pages/v_center/ToolsMenu'
  export default {
    name: 'trendReport',
    components: {
      vMenu,
      ToolsMenu,
      pdf: (resolve) => {
        require(['vue-pdf'], resolve)
      }
    },
    data () {
      return {
        isLoading: true,
        id: '',
        already: false,
        pdf: {},
        pdfUrl: '',
        rotate: 0,
        page: 1,
        title: '',
        numPages: 0,
        loadedRatio: 0,
        load: 0,
        isFullscreen: false
      }
    },
    created() {
      this.id = this.$route.params.id
      this.getPDF()
    },
    mounted() {
      var resizeTimer = null
      const that = this
      window.addEventListener('keydown', (e) => {
        if (resizeTimer) {
          clearTimeout(resizeTimer)
        }
        if (e.keyCode === 13) {
          resizeTimer = setTimeout(function() {
            that.gotoPage(that.page)
          }, 100)
        } else if (e.keyCode === 37) {
          resizeTimer = setTimeout(function() {
            that.prev()
          }, 100)
        } else if (e.keyCode === 39) {
          resizeTimer = setTimeout(function() {
            that.next()
          }, 100)
        }
      })
    },
    watch: {
      numPages() {
        if (this.numPages) {
          this.isLoading = false
        }
      }
    },
    methods: {
      getPDF() {
        this.$http.get(api.trendReports, {params: {id: this.id}}).then((res) => {
          if (res.data.meta.status_code === 200) {
            this.already = true
            res.data.data.created_at = res.data.data.created_at
            .date_format()
            .format('yyyy年MM月dd日 hh:mm')
            this.pdf = res.data.data
          } else {
            this.$message.error(res.data.meta.message)
          }
        }).catch((err) => {
          console.error(err)
        })
      },
      next() {
        this.page ++
        if (this.page > this.numPages) {
          this.page = this.numPages
        }
      },
      prev() {
        this.page --
        if (this.page < 1) {
          this.page = 1
        }
      },
      gotoPage(e) {
        this.page = Number(e)
        if (this.page < 1) {
          this.page = 1
        } else if (this.page > this.numPages) {
          this.page = this.numPages
        }
      },
      error(err) {
        console.error(err)
      },
      fullscreen () {
        this.isFullscreen = true
        document.body.setAttribute('class', 'disableScroll')
        document.childNodes[1].setAttribute('class', 'disableScroll')
      },
      exitFullscreen () {
        this.isFullscreen = false
        document.body.removeAttribute('class', 'disableScroll')
        document.childNodes[1].removeAttribute('class', 'disableScroll')
      }
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      },
      menuStatus () {
        return this.$store.state.event.menuStatus
      }
    }
  }
</script>
<style scoped>
  .pdf {
    border: 1px solid #D2D2D2;
    position: relative;
    padding-top: 50px;
  }

  p.title {
    font-size: 24px;
    font-weight: 600;
    text-indent: 30px;
    background: url("../../../../assets/images/tools/report/PDF@2x.png") no-repeat;
    background-size: 24px;
    padding-bottom: 10px;
  }

  p.info {
    color: #999999;
    overflow: hidden;
    padding-bottom: 5px;
  }

  p.info span {
    text-indent: 26px;
    font-size: 12px;
    line-height: 16px;
    padding-right: 15px;
    margin: 5px 0;
  }

  p.info .date {
    text-indent: 22px;
    background: url('../../../../assets/images/tools/report/time-gray@2x.png') no-repeat 0 center;
    background-size: 16px;
  }

  p.info .view {
    text-indent: 30px;
    background: url('../../../../assets/images/tools/report/browse@2x.png') no-repeat 4px center;
    background-size: 20px;
  }

  p.info .tigs {
    text-indent: 0;
  }

  .tigs i {
    float: left;
    text-indent: 24px;
    background: url('../../../../assets/images/tools/report/label-gray@2x.png') no-repeat 6px 2px;
    background-size: 14px;
  }

  p.flip {
    position: absolute;
    height: 50px;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    margin:auto;
  }

  p.flip span{
    width: 50px;
    height: 50px;
    border-radius: 50%;
  }

  menu {
    position: absolute;
    top: 0;
    width: 100%;
    height: 50px;
    line-height: 50px;
    border-bottom: 1px solid #D2D2D2;
    padding: 0 20px 0 15px;
    background: #FFFFFF
  }

  menu menuitem {
    float: left;
    margin-top: 10px;
    margin-right: 8px;
    width: 26px;
    height: 30px;
    text-indent: -999rem;
    cursor: pointer;
    transition: 0.1s all ease;
  }

  menuitem.add {
    background: url('../../../../assets/images/tools/report/left@2x.png') no-repeat center;
    background-size: 24px;
  }
  menuitem.add:hover {
    transform: scale(1.2);
    background: url('../../../../assets/images/tools/report/left@2x.png') no-repeat center;
    background-size: 24px;
  }

  menuitem.subtract {
    margin-right: 18px;
    background: url('../../../../assets/images/tools/report/right@2x.png') no-repeat center;
    background-size: 24px;
  }
  menuitem.subtract:hover {
    transform: scale(1.2);
    background: url('../../../../assets/images/tools/report/right@2x.png') no-repeat center;
    background-size: 24px;
  }

  menuitem.rotate {
    background: url('../../../../assets/images/tools/report/Rotate@2x.png') no-repeat center;
    background-size: 24px;
  }
  menuitem.rotate:hover {
    background: url('../../../../assets/images/tools/report/RotateHover@2x.png') no-repeat center;
    background-size: 24px;
  }

  menuitem.fullscreen {
    background: url('../../../../assets/images/tools/report/FullScreen.svg') no-repeat center;
    background-size: 20px;
  }
  menuitem.fullscreen:hover {
    background: url('../../../../assets/images/tools/report/FullScreenHover.svg') no-repeat center;
    background-size: 20px;
  }

  .total-page span {
    position: relative;
    margin-right: 20px;
  }

  .total-page span::after {
    content: "";
    position: absolute;
    right: -10px;
    top: 2px;
    height: 16px;
    width: 2px;
    background: #666666
  }
  .page-input {
    margin-top: 15px;
    width: 30px;
    height: 20px;
    border: none;
    background: #FFFFFF;
    border: 1px solid #D2D2D2;
    border-radius: 4px;
    text-align: center
  }
  .fullscreen-pdf {
    position: fixed;
    z-index: 999;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    padding-top: 0;
    overflow-y: scroll
  }
  .fullscreen-tools {
    position: fixed;
    bottom:50px;
    left: 0;
    right: 0;
    width: 400px;
    height: 50px;
    margin: auto;
    opacity: 0.8;
    background-image: linear-gradient(-180deg, rgba(0,0,0,0.70) 0%, #000000 100%);
    border-radius: 25px;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .exit-fullscreen {
    cursor: pointer;
    width: 24px;
    height: 24px;
    background: url('../../../../assets/images/tools/report/ExitFullScreen.svg') no-repeat center;
    background-size: 20px;
  }
  @media screen and (max-width:330px) {
    menuitem.rotate {
      margin-right: 0;
    }
  }
</style>
