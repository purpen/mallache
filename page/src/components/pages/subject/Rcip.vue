<template>
  <div class="content-box">

    <div class="banner" :style="{height: calcHeight}">
    </div>

    <div class=" container">

      <div class="domain">
        <el-row :gutter="20" class="anli-elrow">
          <el-col :xs="24" :sm="12" :md="12" :lg="12">
            <img :src="require('assets/images/subject/rcip/img/RCIP@2x.jpg')" style="width: 100%;"/>
          </el-col>
          <el-col :xs="24" :sm="12" :md="12" :lg="12">
            <p class="title">RCIP战略联盟及衍生创新孵化平台</p>
            <p class="des">
              9月21日下午，作为浙江“传统产业设计再造计划”的重要组成部分，首届IP衍生创新峰会及新品发布会在杭州良渚梦栖小镇召开，今年主题为“聚合 • 衍生”。世界顶级IP与IP授权方、IP运营方、IP衍生品制造商及供应链伙伴、著名IP设计方、原创产品设计机构、IP类商品垂直与关联渠道，以及围绕IP生态成长的公共服务机构等各方资源参加本次大会。大会组织方还邀请了小满文化集团董事长唐冰，凸凹出品创始人李立成，小满文化集团联合创始人兼首席运营官王泽端，Dreamroom品牌创始人颜戎，上海芒果互娱科技有限公司内容营销中心高级总监刘娜，太火鸟科技创始人兼CEO雷海波，授权中国创始人吴卫文，等行业领军人物做专题演讲，从产业、平台、IP、科技、资本、渠道等多维度对IP产业做全方面解读与分享，并同与会者进行现场对话交流。</p>
          </el-col>
        </el-row>
      </div>
    </div>

    <div class=" container">
      <div class="block block_1">
        <div class="sub-title">
          <div class="tig">
          </div>
          <div class="cont">
            <h3>RCIP平台成立仪式演讲</h3>
          </div>
          <div class="foo"></div>
        </div>

        <el-row :gutter="15" class="anli-elrow">
          <el-col :xs="24" :sm="12" :md="12" :lg="12" v-for="(d, index) in startList" :key="index">
            <el-card :body-style="{ padding: '0px' }" class="item">
              <img v-lazy="d.image" class="image">
              <div style="padding: 14px;">
                <p class="des">{{ d.position }}</p>
                <p class="title">{{ d.name }}</p>
              </div>
            </el-card>
          </el-col>
        </el-row>
      </div>

      <div class="block block_2">
        <div class="sub-title">
          <div class="tig">
          </div>
          <div class="cont">
            <h3>RCIP平台成立仪式</h3>
          </div>
          <div class="foo"></div>
        </div>

        <el-row :gutter="20" class="anli-elrow">
          <el-col :xs="24" :sm="12" :md="12" :lg="12" v-for="(d, index) in qdList" :key="index">
            <el-card :body-style="{ padding: '0px' }" class="item">
              <img v-lazy="d.image" class="image">
            </el-card>
          </el-col>
        </el-row>
      </div>

      <div class="block">
        <div class="sub-title">
          <div class="tig">
          </div>
          <div class="cont">
            <h3>签约仪式</h3>
          </div>
          <div class="foo"></div>
        </div>

        <el-row :gutter="20" class="anli-elrow">
          <el-col :xs="24" :sm="12" :md="12" :lg="12" v-for="(d, index) in qdList2" :key="index">
            <el-card :body-style="{ padding: '0px' }" class="item">
              <img v-lazy="d.image" class="image">
            </el-card>
          </el-col>
        </el-row>
      </div>

      <div class="block block_3">
        <div class="sub-title">
          <div class="tig">
          </div>
          <div class="cont">
            <h3>RCIP衍生创新峰会</h3>
          </div>
          <div class="foo"></div>
        </div>

        <el-row :gutter="15" class="anli-elrow">
          <el-col :xs="24" :sm="12" :md="12" :lg="8" v-for="(d, index) in caseList" :key="index">
            <el-card :body-style="{ padding: '0px' }" class="item">
              <img v-lazy="d.image" class="image">
              <div style="padding: 14px;">
                <p class="title">{{ d.title }}</p>
              </div>
            </el-card>
          </el-col>
        </el-row>
      </div>

      <div class="block">
        <div class="sub-title">
          <div class="tig">
          </div>
          <div class="cont">
            <h3>新品发布会</h3>
          </div>
          <div class="foo"></div>
        </div>

        <el-row :gutter="15" class="anli-elrow">
          <el-col :xs="12" :sm="12" :md="8" :lg="6" v-for="(d, index) in djList" :key="index">
            <el-card :body-style="{ padding: '0px' }" class="item">
              <img v-lazy="d.image" class="image">
            </el-card>
          </el-col>
        </el-row>
      </div>

      <div class="block last-block">
        <div class="sub-title">
          <div class="tig">
          </div>
          <div class="cont">
            <h3>活动照片</h3>
          </div>
          <div class="foo"></div>
        </div>

        <el-row :gutter="15" class="anli-elrow">
          <el-col :xs="12" :sm="12" :md="8" :lg="6" v-for="(d, index) in hdList" :key="index">
            <el-card :body-style="{ padding: '0px' }" class="item">
              <img v-lazy="d.image" class="image">
            </el-card>
          </el-col>
        </el-row>
      </div>
    </div>

    <div class="bottom">
      <p class="content">
        太火鸟作为擅长孵化与运营的品牌，2017年已经推出全新铟果D³INGO平台，促进线上设计交易与创新产品的发现、孵化，提供针对消费升级趋势的品牌化、产品化咨询及解决方案。如有关设计交易及创新产品业务或相关想法与建议，欢迎邮件联系</p>
      <p class="content"><img :src="require('assets/images/subject/rcip/Email@2x.png')"
                              style="width: 30px; vertical-align:middle; color:#FE4548"/> mazhe@taihuoniao.com</p>
      <p class="content">让我们一起，以设计为发力点，打造优质创意价值链，让好设计发声，塑造全新产业升级的明天。</p>
      <p class="content">设计再造计划，等待您的参与。</p>

      <div class="b-title">
        <p>铟果介绍</p>
      </div>
      <p class="content plug">
        铟果D³INGO是太火鸟旗下基于大数据和智能匹配的产品创新SaaS平台，链接庞大智能硬件创业群体，吸引100万注册用户，1000+家专业设计企业，覆盖2000万创新设计群体。利用大数据和智能匹配技术连接制造企业和设计服务商，以设计驱动创新，挖掘消费需求，加速消费升级，用创新改变世界。</p>
    </div>
  </div>
</template>

<script>
  import { calcImgSize } from 'assets/js/common'
  export default {
    name: 'subject_zj',
    data() {
      return {
        startList: [
          {
            'clickUrl': '',
            'position': 'IP造物论',
            'name': '小满文化集团联合创始人兼首席运营官 王泽瑞',
            'image': require('@/assets/images/subject/rcip/img/wzr@2x.jpg')
          },
          {
            'clickUrl': '',
            'position': 'RCIP赋能与变现',
            'name': '凸凹设计创始人及首席设计师 李立成',
            'image': require('@/assets/images/subject/rcip/img/llc@2x.jpg')
          }
        ],
        qdList: [
          {
            'clickUrl': '',
            'des': '',
            'image': require('@/assets/images/subject/rcip/img/Ceremony01@2x.jpg')
          },
          {
            'clickUrl': '',
            'des': '',
            'image': require('@/assets/images/subject/rcip/img/Ceremony02@2x.jpg')
          }
        ],
        qdList2: [
          {
            'clickUrl': '',
            'des': '',
            'image': require('@/assets/images/subject/rcip/img/Sign01@2x.jpg')
          },
          {
            'clickUrl': '',
            'des': '',
            'image': require('@/assets/images/subject/rcip/img/Sign02@2x.jpg')
          }
        ],
        caseList: [
          {
            'clickUrl': '',
            'title': 'IP衍生品牌Dreamroom品牌创始人  颜戎',
            'image': require('@/assets/images/subject/rcip/img/ry@2x.jpg')
          },
          {
            'clickUrl': '',
            'title': '上海芒果互娱科技有限公司内容营销中心高级总监  刘娜',
            'image': require('@/assets/images/subject/rcip/img/ln@2x.jpg')
          },
          {
            'clickUrl': '',
            'title': '太火鸟科技创始人&CEO  雷海波',
            'image': require('@/assets/images/subject/rcip/img/leizong@2x.jpg')
          },
          {
            'clickUrl': '',
            'title': '小满文化集团董事长  唐冰',
            'image': require('@/assets/images/subject/rcip/img/tb@2x.jpg')
          },
          {
            'clickUrl': '',
            'title': '授权中国创始人  吴卫文',
            'image': require('@/assets/images/subject/rcip/img/wuww@2x.jpg')
          },
          {
            'clickUrl': '',
            'title': 'Q&A环节',
            'image': require('@/assets/images/subject/rcip/img/Q&A@2x.jpg')
          }
        ],
        djList: [
          {
            'image': require('@/assets/images/subject/rcip/img/NewProduct01@2x.jpg')
          },
          {
            'image': require('@/assets/images/subject/rcip/img/NewProduct02@2x.jpg')
          },
          {
            'image': require('@/assets/images/subject/rcip/img/NewProduct03@2x.jpg')
          },
          {
            'image': require('@/assets/images/subject/rcip/img/NewProduct04@2x.jpg')
          },
          {
            'image': require('@/assets/images/subject/rcip/img/NewProduct05@2x.jpg')
          },
          {
            'image': require('@/assets/images/subject/rcip/img/NewProduct06@2x.jpg')
          },
          {
            'image': require('@/assets/images/subject/rcip/img/NewProduct07@2x.jpg')
          },
          {
            'image': require('@/assets/images/subject/rcip/img/NewProduct08@2x.jpg')
          },
          {
            'image': require('@/assets/images/subject/rcip/img/NewProduct09@2x.jpg')
          },
          {
            'image': require('@/assets/images/subject/rcip/img/NewProduct10@2x.jpg')
          },
          {
            'image': require('@/assets/images/subject/rcip/img/NewProduct11@2x.jpg')
          },
          {
            'image': require('@/assets/images/subject/rcip/img/NewProduct12@2x.jpg')
          }
        ],
        hdList: [
          {
            'image': require('@/assets/images/subject/rcip/img/Photo01@2x.jpg')
          },
          {
            'image': require('@/assets/images/subject/rcip/img/Photo02@2x.jpg')
          },
          {
            'image': require('@/assets/images/subject/rcip/img/Photo03@2x.jpg')
          },
          {
            'image': require('@/assets/images/subject/rcip/img/Photo04@2x.jpg')
          },
          {
            'image': require('@/assets/images/subject/rcip/img/Photo05@2x.jpg')
          },
          {
            'image': require('@/assets/images/subject/rcip/img/Photo06@2x.jpg')
          },
          {
            'image': require('@/assets/images/subject/rcip/img/Photo07@2x.jpg')
          },
          {
            'image': require('@/assets/images/subject/rcip/img/Photo08@2x.jpg')
          }
        ],
        videoUrl: 'http://oni525j96.bkt.clouddn.com/video/zjgz.mp4',
        msg: 'This is subject',
        calcHeight: ''
      }
    },
    mounted() {
      var that = this
      window.addEventListener('resize', () => {
        that.calcHeight = calcImgSize(1520, 2880)
      })
      this.calcHeight = calcImgSize(1520, 2880)
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .content-box {
    overflow-x: hidden;
    margin-bottom: -52px
  }

  .banner-item {
    margin: 10px 0;
    text-align: center;
  }

  .banner-item a {
    font-size: 1.8rem;
    color: #666;
    line-height: 1.5;
  }

  .banner-item a.is-active {
    color: #FF5A5F;
  }

  .banner {
    width: 100%;
    background: url('../../../assets/images/subject/rcip/head@2x.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    text-align: center;
  }

  .domain {
    margin: 20px 0 30px 0;
  }

  .domain .title {
    text-align: center;
    font-size: 2.4rem;
    margin-bottom: 15px;
    color: #222222;
  }

  .domain .des {
    text-indent: 1em;
    padding: 0 1em;
    font-size: 1.4rem;
    color: #666;
    font-weight: 300;
  }

  .sub-title {
    display: flex;
    height: 60px;
    line-height: 60px;
    margin-bottom: 20px;
  }

  .sub-title h3 {
    width: 160px;
    text-indent: -10px;
    color: #fff;
    font-size: 1.6rem;
  }

  .sub-title .tig {
    height: 60px;
    width: 50px;
    background: url('../../../assets/images/subject/rcip/TitleBarHead@2x.png') no-repeat;
    background-size: cover;
  }

  .sub-title .cont {
    position: relative;
    height: 60px;
    width: 100%;
    flex: 1;
    background: #383838;
  }

  .sub-title .foo {
    height: 60px;
    width: 380px;
    background: url('../../../assets/images/subject/rcip/TitleBarTail@2x.png') no-repeat;
    background-size: cover;
  }

  .block {
    margin-top: 20px;
  }

  .last-block {
    margin-bottom: 40px;
  }

  .item img {
    width: 100%;
  }

  .item {
    margin-bottom: 15px;
  }

  .item p {
    color: #666;
    font-size: 1.4rem;
    line-height: 2
  }

  .item p.des {
    font-weight: 300;
  }

  .item p.title {
    line-height: 2;
  }

  .bottom {
    overflow: hidden;
    background: url('../../../assets/images/subject/rcip/foot@2x.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    text-align: center;
  }

  .bottom .b-title {
    margin: 20px auto;
    text-align: center;
  }

  .bottom .b-title p {
    font-size: 24px;
    line-height: 1.5;
    color: #fff;
  }

  .bottom p.content {
    line-height: 2;
    color: #ffffff;
    padding: 0 23%;
    margin: 10px 0;
  }

  .bottom p.content.plug {
    padding: 0 18%;
  }

  @media screen and (max-width: 767px) {
    .domain .title {
      margin-top: 22px
    }
  }

  @media screen and (max-width: 600px) {
    .sub-title h3 {
      text-indent: 0;
    }

    .bottom p.content {
      padding: 0 15px
    }

    .bottom p.content.plug {
      padding: 0 15px;
      margin-bottom: 20px;
    }

  }

  @media screen and (max-width: 450px) {
    .sub-title .tig {
      display: none
    }

    .sub-title {
      border-radius: 50%;
    }

    .sub-title h3 {
      text-indent: 1.5em;
    }
  }
</style>
