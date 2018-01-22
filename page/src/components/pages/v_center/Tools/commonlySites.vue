<template>
  <div class="container">
    <div class="commonly">
      <ToolsMenu currentName="commonlySites"></ToolsMenu>
      <div class="commonly-sites" v-loading.body="loading">
        <section class="lists design-info" v-if="designInfo.length">
          <h2><span class="fx-3 fx-icon-information"></span>设计资讯</h2>
          <article>
            <el-row :gutter="isMob ? 10 : 20">
              <el-col :span="isMob ? 12 : 6" v-for="(ele, index) in designInfo" :key="index">
                <div class="item clearfix" @click="aClick(ele.url)">
                  <div class="left fl"
                        :style="{background: 'url('+ele.cover.logo+')', backgroundSize :'contain'}">
                    {{ele.cover.name}}
                  </div>
                  <div class="right hide2lines fl">
                    <p class="title">{{ele.title}}</p>
                    <p class="summary hide2lines">{{ele.summary}}</p>
                  </div>
                </div>
              </el-col>
            </el-row>
          </article>
        </section>

        <section class="lists originality" v-if="originality.length">
          <h2><span class="fx-icon-inspiration2"></span>创意灵感</h2>
          <article>
            <article>
              <el-row :gutter="isMob ? 10 : 20">
                <el-col :span="isMob ? 12 : 6" v-for="(ele, index) in originality" :key="index">
                  <div class="item clearfix" @click="aClick(ele.url)">
                    <div class="left fl"
                          :style="{background: 'url('+ele.cover.logo+')', backgroundSize :'contain'}">
                      {{ele.cover.name}}
                    </div>
                    <div class="right fl">
                      <p class="title">{{ele.title}}</p>
                      <p class="summary hide2lines">{{ele.summary}}</p>
                    </div>
                  </div>
                </el-col>
              </el-row>
            </article>
          </article>
        </section>
        <section class="lists crowd-funding" v-if="crowdFunding.length">
          <h2><span class="fx-icon-crowdfunding"></span>众筹</h2>
          <article>
            <article>
              <el-row :gutter="isMob ? 10 : 20">
                <el-col :span="isMob ? 12 : 6" v-for="(ele, index) in crowdFunding" :key="index">
                  <div class="item clearfix" @click="aClick(ele.url)">
                    <div class="left fl"
                          :style="{background: 'url('+ele.cover.logo+')', backgroundSize :'contain'}">
                      {{ele.cover.name}}
                    </div>
                    <div class="right fl">
                      <p class="title">{{ele.title}}</p>
                      <p class="summary hide2lines">{{ele.summary}}</p>
                    </div>
                  </div>
                </el-col>
              </el-row>
            </article>
          </article>
        </section>
        <section class="lists business-consult" v-if="businessConsult.length">
          <h2><span class="fx-icon-business-consulting"></span>商业咨询</h2>
          <article>
            <article>
              <el-row :gutter="isMob ? 10 : 20">
                <el-col :span="isMob ? 12 : 6" v-for="(ele, index) in businessConsult" :key="index">
                  <div class="item clearfix" @click="aClick(ele.url)">
                    <div class="left fl"
                          :style="{background: 'url('+ele.cover.logo+')', backgroundSize :'contain'}">
                      {{ele.cover.name}}
                    </div>
                    <div class="right fl">
                      <p class="title">{{ele.title}}</p>
                      <p class="summary hide2lines">{{ele.summary}}</p>
                    </div>
                  </div>
                </el-col>
              </el-row>
            </article>
          </article>
        </section>
        <section class="lists design-awards" v-if="designAwards.length">
          <h2><span class="fx-icon-prize"></span>设计奖项</h2>
          <article>
            <article>
              <el-row :gutter="isMob ? 10 : 20">
                <el-col :span="isMob ? 12 : 6" v-for="(ele, index) in designAwards" :key="index">
                  <div class="item clearfix" @click="aClick(ele.url)">
                    <div class="left fl"
                          :style="{background: 'url('+ele.cover.logo+')', backgroundSize :'contain'}">
                      {{ele.cover.name}}
                    </div>
                    <div class="right fl">
                      <p class="title">{{ele.title}}</p>
                      <p class="summary hide2lines">{{ele.summary}}</p>
                    </div>
                  </div>
                </el-col>
              </el-row>
            </article>
          </article>
        </section>
      </div>
    </div>
  </div>
</template>
<script>
  import api from '@/api/api'
  import ToolsMenu from '@/components/pages/v_center/ToolsMenu'
  export default {
    name: 'commonlySites',
    components: {
      ToolsMenu
    },
    data () {
      return {
        loading: false,
        designInfo: [],
        originality: [],
        crowdFunding: [],
        businessConsult: [],
        designAwards: []
      }
    },
    created () {
      this.loading = true
      this.$http.get(api.CommonlySiteList).then((res) => {
        this.loading = false
        if (res.data.meta.status_code === 200) {
          for (let i of res.data.data) {
            switch (i.type) {
              case 1:
                this.designInfo.push(i)
                break
              case 2:
                this.originality.push(i)
                break
              case 3:
                this.crowdFunding.push(i)
                break
              case 4:
                this.businessConsult.push(i)
                break
              case 5:
                this.designAwards.push(i)
                break
            }
          }
        } else {
          this.$message.error(res.data.meta.message)
        }
      }).catch((err) => {
        this.loading = false
        console.error(err)
      })
    },
    methods: {
      clearArr () {
        this.designInfo = []
        this.originality = []
        this.crowdFunding = []
        this.businessConsult = []
        this.designAwards = []
      },
      aClick (link) {
        let reg = /^(http)/
        if (!reg.test(link)) {
          window.open('http://' + link)
          return
        }
        window.open(link)
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
  .commonly-sites {
    padding: 0 15px;
  }

  .lists h2 {
    font-size: 20px;
    color: #222222;
    margin: 30px 0 20px;
    display: flex;
    align-items: center;
  }

  .design-info h2 {
    margin: 10px 0 20px;
  }

  .item {
    height: 100px;
    padding: 20px;
    border: 1px solid #DCDCDC;
    box-shadow: 0 0 4px 0 rgba(0, 0, 0, 0.05);
    border-radius: 10px;
    margin-bottom: 20px;
    cursor: pointer;
    transition: all ease .3s;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .item:hover {
    transform: translate3d(0, -2px, 0);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
  }

  .item a {
    display: block;
    width: 100%;
    height: 100%;
  }

  .item .left {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    text-indent: -999rem;
    border: 1px solid #DCDCDC;
    box-shadow: 0 0 4px 0 rgba(0, 0, 0, 0.05);
  }

  .item .right {
    padding-left: 10px;
    width: calc(100% - 42px);
  }

  .right .title {
    font-size: 16px;
    color: #222;
    padding-bottom: 6px;
  }

  .right .summary {
    font-size: 14px;
    color: #666;
    line-height: 18px;
  }

  @media screen and (max-width: 600px) {
    .item {
      height: 80px;
      padding: 10px;
      margin-bottom: 10px;
    }

    .item:hover {
      transform: translate3d(0, 0, 0);
      box-shadow: none;
    }

    .item .left {
      margin: 10px 0;
      width: 30px;
      height: 30px;
    }

    .item .right {
      width: calc(100% - 30px);
    }

    .right .title {
      font-size: 12px;
    }

    .right .summary {
      font-size: 12px;
      line-height: 14px;
    }

    .lists h2 {
      margin: 15px 0;
      font-size: 17px;
    }

    .design-info h2 {
      margin: 0 0 15px;
    }
  }
</style>
