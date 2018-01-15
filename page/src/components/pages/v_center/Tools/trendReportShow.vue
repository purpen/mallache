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
            <span class="date fl"><i class="fx-2 fx-icon-time"></i>{{pdf.created_at}}</span>
            <span class="view fl"><i class="fx-3 fx-icon-see"></i>{{pdf.hits}}</span>
            <span class="tigs fl">
              <b class="fl">标签:</b>
              <i class="fx" v-for="(e, i) in pdf.tag" :key="i">
                <i class="fx-icon-label"></i>
                {{e}}
              </i>
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
            <p :class="['flip', {'flip-fullscreen' : isFullscreen}]" v-if="numPages">
              <span class="swiper-button-prev fl" @click="prev">
                <i class="el-icon-arrow-left"></i>
              </span>
              <span class="swiper-button-next fr" @click="next">
                <i class="el-icon-arrow-right"></i>
              </span>
            </p>
            <div class="fullscreen-tools" v-if="isFullscreen">
              <span class="exit-fullscreen" @click="exitFullscreen">
                <span class="fx-4 fx-icon-exitFull-screen"></span>
              </span>
              <span class="fx-icon-nothing-left" @click="prev"></span>
              <span class="fx-icon-nothing-right" @click="next"></span>
              <p class="total-page">
                <i>共{{numPages}}页</i>前往
                <input type="text" class="page-input" v-model.number="page" @blur="gotoPage(page)">
                页
              </p>
            </div>
            <menu class="clearfix" v-if="!isFullscreen">
              <menuitem class="fullscreen" @click="fullscreen">
                <span class="fx-4 fx-icon-full-screen"></span>
              </menuitem>
              <menuitem class="add" @click="prev">
                <span class="fx-icon-nothing-left"></span>
              </menuitem>
              <menuitem class="subtract" @click="next">
                <span class="fx-icon-nothing-right"></span>
              </menuitem>
              <menuitem class="rotate" @click="rotate += 90" v-if="isMob">
                <span class="fx-icon-rotate"></span>
              </menuitem>
              <p class="total-page">
                <i>共{{numPages}}页</i>前往
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
  import pdf from 'vue-pdf'
  export default {
    name: 'trendReport',
    components: {
      vMenu,
      ToolsMenu,
      pdf
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
        } else if (e.keyCode === 27) {
          resizeTimer = setTimeout(function() {
            that.exitFullscreen()
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
            .format('yyyy年MM月dd日')
            // .format('yyyy年MM月dd日 hh:mm')
            this.pdf = res.data.data
          } else {
            this.$message.error(res.data.meta.message)
          }
        }).catch((err) => {
          console.error(err)
        })
      },
      next() {
        if (this.numPages) {
          this.page ++
          if (this.page > this.numPages) {
            this.page = this.numPages
          }
        }
      },
      prev() {
        if (this.numPages) {
          this.page --
          if (this.page < 1) {
            this.page = 1
          }
        }
      },
      gotoPage(e) {
        if (this.numPages) {
          this.page = Number(e)
          if (this.page < 1) {
            this.page = 1
          } else if (this.page > this.numPages) {
            this.page = this.numPages
          }
        }
      },
      error(err) {
        console.error(err)
      },
      fullscreen () {
        if (this.numPages) {
          this.isFullscreen = true
          document.body.setAttribute('class', 'disableScroll')
          document.childNodes[1].setAttribute('class', 'disableScroll')
          console.log(this.$refs.pdf.offsetHeight)
        }
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
    background: #fff;
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
    font-size: 12px;
    line-height: 16px;
    padding-right: 15px;
    margin: 5px 0;
    display: flex;
    align-items: center;
  }

  .tigs i.fx {
    display: flex;
    align-items: center;
    margin-left: 6px;
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
    background: #FFFFFF;
    display: flex;
    align-items: center;
  }

  menu menuitem {
    margin-right: 8px;
    width: 30px;
    height: 30px;
    font-size: 14px;
    cursor: pointer;
    transition: 0.2s all ease;
    display: flex;
    align-items: center;
  }

  .fc-prev-button,
  .fc-next-button {
    border-radius: 50%;
    color: #999999;
    border: none;
  }

  .fx-icon-full-screen, .fx-icon-nothing-left, .fx-icon-nothing-right {
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 24px;
    height: 24px;
    font-size: 14px;
    line-height: 50px;
    border: 1px solid #979797;
  }

 .fx-icon-full-screen {
    border: none;
    font-size: 20px;
  }

  .fx-icon-nothing-left{
    padding-right: 2px
  }

  .fx-icon-nothing-right{
    padding-left: 2px
  }

  .fx-icon-rotate {
    font-size: 24px;
  }

  .fx-icon-rotate:hover,
  .fx-icon-full-screen:hover,
  .fx-icon-nothing-left:hover,
  .fx-icon-nothing-right:hover {
    color: #FF5D62;
    border-color: #FF5D62;
  }

  .fx-icon-nothing-left:active,
  .fx-icon-nothing-right:active {
    color: #FFFFFF;
    background-color: #FFACAF;
    border-color: #FF5D62;
  }

  menu .total-page {
    position: absolute;
    right: 15px;
    top: 0;
  }

  .total-page {
    color: #666;
  }

  .total-page i {
    position: relative;
    margin-right: 20px;
  }

  .total-page i::after {
    content: "";
    position: absolute;
    right: -10px;
    top: 2px;
    height: 16px;
    width: 2px;
    background: #999
  }

  .page-input {
    color: #666;
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
    width: 330px;
    height: 50px;
    margin: auto;
    opacity: 0.8;
    background-image: linear-gradient(-180deg, rgba(0,0,0,0.40) 0%, rgba(0,0,0,0.90) 100%);
    border: 0.5px solid #FFFFFF;
    border-radius: 25px;
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0.3;
    transition: 0.3s all ease;
    color: #fff;
  }

  .fullscreen-tools:hover {
    opacity: 1;
  }
  .fullscreen-tools span {
    cursor: pointer;
    width: 24px;
    height: 24px;
    line-height: 24px;
    margin-right: 20px;
  }

  .fullscreen-tools .total-page {
    color: #fff;
    line-height: 50px;
  }

  .fullscreen-tools .total-page i::after {
    background: #fff
  }

  .fullscreen-tools .page-input {
    background: #333;
    color: #fff;
  }

  .fullscreen-tools .fx-icon-nothing-right,
  .fullscreen-tools .fx-icon-nothing-left {
    border-width: 2px;
  }

  .fullscreen-tools .fx-icon-nothing-right:hover,
  .fullscreen-tools .fx-icon-nothing-left:hover {
    color:#fff;
    background-color: transparent;
    border-color: #979797;
  }

  .fullscreen-tools .fx-icon-nothing-right:active,
  .fullscreen-tools .fx-icon-nothing-left:active {
    color:#fff;
    background-color: transparent;
    border-color: #979797;
  }

  .fullscreen-tools span {
    opacity: 0.7;
  }

  .fullscreen-tools span:hover,
  .fullscreen-tools span:active {
    opacity: 1;
  }

  p.flip-fullscreen {
    position: fixed;
    height: 50px;
    top: 0;
    left: 0;
    right: 18px;
    bottom: 0;
    margin:auto;
  }
  @media screen and (max-width:330px) {
    menuitem.rotate {
      margin-right: 0;
    }
  }
</style>
