<template>
  <div class="container">
    <div class="commonly">
      <div class="blank20"></div>
      <el-row :gutter="24" class="anli-elrow">
        <v-menu currentName="commonlySites" v-if="menuStatus !== 'tools'"></v-menu>
        <ToolsMenu v-if="menuStatus === 'tools'" currentName="commonlySites"></ToolsMenu>
        <el-col :span="isMob ? 24 : 20"
                v-loading.body="loading">
          <div class="commonly-sites">

            <section class="lists design-info" v-if="designInfo.length">
              <h2>设计资讯</h2>
              <article>
                <el-row :gutter="isMob ? 10 : 20" class="padding10">
                  <el-col :span="isMob ? 24 : 8" v-for="(ele, index) in designInfo" :key="ele.id">
                    <div class="item clearfix" @click="aClick(ele.url)">
                      <div class="left fl"
                           :style="{background: 'url('+ele.cover.logo+')', backgroundSize :'contain'}">
                        {{ele.cover.name}}
                      </div>
                      <div class="right fl">
                        <p class="title">{{ele.title}}</p>
                        <p class="summary">{{ele.summary}}</p>
                      </div>
                    </div>
                  </el-col>
                </el-row>
              </article>
            </section>

            <section class="lists originality" v-if="originality.length">
              <h2>创意灵感</h2>
              <article>
                <article>
                  <el-row :gutter="isMob ? 10 : 20" class="padding10">
                    <el-col :span="isMob ? 24 : 8" v-for="(ele, index) in originality" :key="ele.id">
                      <div class="item clearfix" @click="aClick(ele.url)">
                        <div class="left fl"
                             :style="{background: 'url('+ele.cover.logo+')', backgroundSize :'contain'}">
                          {{ele.cover.name}}
                        </div>
                        <div class="right fl">
                          <p class="title">{{ele.title}}</p>
                          <p class="summary">{{ele.summary}}</p>
                        </div>
                      </div>
                    </el-col>
                  </el-row>
                </article>
              </article>
            </section>
            <section class="lists crowd-funding" v-if="crowdFunding.length">
              <h2>众筹</h2>
              <article>
                <article>
                  <el-row :gutter="isMob ? 10 : 20" class="padding10">
                    <el-col :span="isMob ? 24 : 8" v-for="(ele, index) in crowdFunding" :key="ele.id">
                      <div class="item clearfix" @click="aClick(ele.url)">
                        <div class="left fl"
                             :style="{background: 'url('+ele.cover.logo+')', backgroundSize :'contain'}">
                          {{ele.cover.name}}
                        </div>
                        <div class="right fl">
                          <p class="title">{{ele.title}}</p>
                          <p class="summary">{{ele.summary}}</p>
                        </div>
                      </div>
                    </el-col>
                  </el-row>
                </article>
              </article>
            </section>
            <section class="lists business-consult" v-if="businessConsult.length">
              <h2>商业咨询</h2>
              <article>
                <article>
                  <el-row :gutter="isMob ? 10 : 20" class="padding10">
                    <el-col :span="isMob ? 24 : 8" v-for="(ele, index) in businessConsult" :key="ele.id">
                      <div class="item clearfix" @click="aClick(ele.url)">
                        <div class="left fl"
                             :style="{background: 'url('+ele.cover.logo+')', backgroundSize :'contain'}">
                          {{ele.cover.name}}
                        </div>
                        <div class="right fl">
                          <p class="title">{{ele.title}}</p>
                          <p class="summary">{{ele.summary}}</p>
                        </div>
                      </div>
                    </el-col>
                  </el-row>
                </article>
              </article>
            </section>
            <section class="lists design-awards" v-if="designAwards.length">
              <h2>设计奖项</h2>
              <article>
                <article>
                  <el-row :gutter="isMob ? 10 : 20" class="padding10">
                    <el-col :span="isMob ? 24 : 8" v-for="(ele, index) in designAwards" :key="ele.id">
                      <div class="item clearfix" @click="aClick(ele.url)">
                        <div class="left fl"
                             :style="{background: 'url('+ele.cover.logo+')', backgroundSize :'contain'}">
                          {{ele.cover.name}}
                        </div>
                        <div class="right fl">
                          <p class="title">{{ele.title}}</p>
                          <p class="summary">{{ele.summary}}</p>
                        </div>
                      </div>
                    </el-col>
                  </el-row>
                </article>
              </article>
            </section>
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
    name: 'commonlySites',
    components: {
      vMenu,
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
                console.log(i)
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
        }
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
  .lists h2 {
    font-size: 24px;
    color: #222222;
    margin: 40px 0 20px;
    text-indent: 30px;
    background: url("../../../../assets/images/tools/commonlySite/DesignInformation.png") no-repeat left;
    background-size: 25px;
  }

  .originality h2 {
    background: url("../../../../assets/images/tools/commonlySite/inspiration.png") no-repeat left;
    background-size: 25px;
  }

  .crowd-funding h2 {
    background: url("../../../../assets/images/tools/commonlySite/Crowd-funding.png") no-repeat left;
    background-size: 25px;
  }

  .business-consult h2 {
    background: url("../../../../assets/images/tools/commonlySite/BusinessConsulting.png") no-repeat left;
    background-size: 25px;
  }

  .design-awards h2 {
    background: url("../../../../assets/images/tools/commonlySite/designAwards.png") no-repeat left;
    background-size: 25px;
  }

  .item {
    min-height: 100px;
    padding: 28px 0 15px 20px;
    border: 1px solid #DCDCDC;
    box-shadow: 0 0 4px 0 rgba(0, 0, 0, 0.05);
    border-radius: 10px;
    margin-bottom: 20px;
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
  }

  .item .right {
    padding-left: 10px;
    width: calc(100% - 42px);
  }

  .right .title {
    font-size: 16px;
    font-weight: 600;
    color: #222;
    padding-bottom: 10px;
  }

  .right .summary {
    font-size: 14px;
    color: #666;
    line-height: 1.2;
  }

  .padding10 {
    padding: 0 10px;
  }

  @media screen and (max-width: 600px) {
    .item {
      min-height: 70px;
      padding: 10px;
      margin-bottom: 10px;
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
    }

    .lists h2 {
      text-indent: 24px;
      margin: 20px 0;
      font-size: 17px;
      background: url("../../../../assets/images/tools/commonlySite/DesignInformation.png") no-repeat left;
      background-size: 17px;
    }

    .originality h2 {
      background: url("../../../../assets/images/tools/commonlySite/inspiration.png") no-repeat left;
      background-size: 17px;
    }

    .crowd-funding h2 {
      background: url("../../../../assets/images/tools/commonlySite/Crowd-funding.png") no-repeat left;
      background-size: 17px;
    }

    .business-consult h2 {
      background: url("../../../../assets/images/tools/commonlySite/BusinessConsulting.png") no-repeat left;
      background-size: 17px;
    }

    .design-awards h2 {
      background: url("../../../../assets/images/tools/commonlySite/designAwards.png") no-repeat left;
      background-size: 17px;
    }

  }
</style>
