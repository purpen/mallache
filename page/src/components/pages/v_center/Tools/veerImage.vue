<template>
  <div class="container">
    <div class="veer-image">
      <div class="blank20"></div>
      <el-row :gutter="24" class="anli-elrow">
        <v-menu currentName="veerImage"
                v-if="menuStatus !== 'tools' || !isMob"></v-menu>
        <ToolsMenu v-if="menuStatus === 'tools' && isMob" currentName="veerImage"></ToolsMenu>
        <el-col :span="isMob ? 24 : 20" v-loading="isLoading">
          <h2 v-if="!isMob">{{msg}}</h2>
          <el-input class="search-input" placeholder="请输入内容" v-model="keyword">
            <el-button slot="append" class="search-btn" @click="getImgList(keyword)">搜索</el-button>
          </el-input>
          <waterfall :line-gap="picWidth" align="center" :watch="imgList"
                     class="waterfall" v-if="isEmpty !== true">
            <waterfall-slot
              v-for="(item, index) in imgList"
              :width="item.width"
              :height="item.height"
              :order="index"
              :key="item.id">
              <a :href="item.veer_url" target="_blank">
                <img v-lazy="item.small_url" alt="">
              </a>
            </waterfall-slot>
          </waterfall>
          <el-pagination
            class="pagination"
            v-if="imgList.length && !isMob"
            :current-page.sync="pagination.page"
            :page-size="pagination.pageSize"
            layout="prev, pager, next"
            :total="pagination.total"
            @current-change="handleCurChange">
          </el-pagination>

          <div class="is-empty" v-if="isEmpty === true">
            <div class="empty-bg">
              暂无相关信息～
            </div>
          </div>
        </el-col>
      </el-row>
    </div>
  </div>
</template>
<script>
  import api from '@/api/api'
  import vMenu from '@/components/pages/v_center/Menu'
  import Waterfall from 'vue-waterfall/lib/waterfall'
  import WaterfallSlot from 'vue-waterfall/lib/waterfall-slot'
  import ToolsMenu from '@/components/pages/v_center/ToolsMenu'
  export default {
    name: 'veerImage',
    components: {
      vMenu,
      Waterfall,
      WaterfallSlot,
      ToolsMenu
    },
    data () {
      return {
        msg: '图片素材',
        keyword: '',
        imgList: [],
        isEmpty: false,
        isLoading: false,
        isOk: true,
        picWidth: 240,
        docHeight: 0,
        pagination: {
          curPage: 1,
          pageSize: 20,
          total: 0,
          pageCount: 0
        }
      }
    },
    created () {
      let docWidth = document.body.offsetWidth
      if (docWidth < 750) {
        this.picWidth = 200
      }
      if (docWidth < 630) {
        this.picWidth = 160
      }
      if (docWidth < 350) {
        this.picWidth = 140
      }
    },
    watch: {
      keyword () {
        this.pagination.curPage = 1
      }
    },
    mounted () {
      window.addEventListener('keydown', (e) => {
        if (this.keyword) {
          if (e.keyCode === 13) {
            this.getImgList(this.keyword)
          } else if (e.keyCode === 37) {
            this.pagination.curPage--
            if (this.pagination.curPage < 0) {
              this.pagination.curPage = 0
            }
            this.getImgList(this.keyword)
          } else if (e.keyCode === 39) {
            this.pagination.curPage++
            if (this.pagination.curPage > this.pagination.pageCount) {
              this.pagination.curPage = this.pagination.pageCount
            }
            this.getImgList(this.keyword)
          }
        } else {
          return false
        }
      })
      let getImg = () => {
        if (this.isOk) {
          let docHeight = document.body.scrollHeight
          if (docHeight + document.body.getBoundingClientRect().top < document.body.offsetHeight + 310) {
            this.pagination.curPage++
            this.touchGetImgList(this.keyword)
          }
        }
      }
      if (this.imgList.length) {
        window.addEventListener('touchmove', getImg, false)
      }
    },
    methods: {
      getImgList (val) {
        this.isLoading = true
        this.$http.get(api.veerImage, {
          params: {
            page: this.pagination.curPage,
            keyword: val,
            nums: this.pagination.pageSize
          }
        }).then((res) => {
          this.isLoading = false
          if (res.data.meta.status_code === 200) {
            let data = res.data.data
            if (data.length) {
              this.isEmpty = false
              this.imgList = data
              this.pagination.pageCount = res.data.meta.pagination.total_pages
              if (this.pagination.pageCount > 1000) {
                this.pagination.pageCount = 1000
              }
              this.pagination.total = this.pagination.pageSize * this.pagination.pageCount
            } else {
              this.isEmpty = true
              this.imgList = data
            }
          } else {
            this.$message.error(res.data.meta.message)
          }
        }).catch((err) => {
          this.isLoading = false
          console.error(err)
        })
      },
      touchGetImgList (val) {
        this.isOk = false
        this.isLoading = true
        this.$http.get(api.veerImage, {
          params: {
            page: this.pagination.curPage,
            keyword: val,
            nums: this.pagination.pageSize
          }
        }).then((res) => {
          this.isOk = true
          this.isLoading = false
          if (res.data.meta.status_code === 200) {
            let data = res.data.data
            if (data) {
              if (data.length < 1) {
                this.isEmpty = true
              }
              this.imgList = this.imgList.concat(data)
              if (res.data.meta.pagination.total > this.pagination.pageSize * this.pagination.pageCount) {
                this.pagination.total = 20000
              } else {
                this.pagination.total = res.data.meta.pagination.total
              }
            }
          } else {
            this.$message.error(res.data.meta.message)
          }
        }).catch((err) => {
          this.isOk = true
          this.isLoading = false
          console.error(err)
        })
      },
      handleCurChange(val) {
        this.pagination.curPage = val
        this.getImgList(this.keyword)
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
  h2 {
    text-align: center;
    font-size: 17px;
    color: #222222;
    font-weight: 600;
    padding: 6px 0 20px;
  }

  img {
    width: 100%;
    height: auto;
    padding: 6px;
    transition: all ease .3s;
  }

  img:hover {
    transform: scale(1.05);
    /* transform: translate3d(0, -2px, 0); */
  }

  .pagination {
    display: flex;
    justify-content: center;
  }

  .waterfall {
    margin: 20px 0;
  }

  .search-btn {
    height: 36px;
    background: #FF5A5F!important;
    color: #ffffff!important;
    border-radius: 0 4px 4px 0!important
  }

  .is-empty {
    position: relative;
    height: calc(100% - 100px);
    min-height: 200px;
  }

  .empty-bg {
    width: 120px;
    height: 100px;
    line-height: 220px;
    text-align: center;
    position: absolute;
    top:0;
    left:0;
    bottom:0;
    right:0;
    margin:auto;
    background: url("../../../../assets/images/tools/report/NoMaterial.png") no-repeat center;
    background-size: 100px;
  }

  @media screen and (max-width: 767px) {
    h2 {
      padding: 20px 0
    }
  }
</style>
