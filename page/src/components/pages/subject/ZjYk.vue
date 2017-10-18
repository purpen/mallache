<template>
  <div class="content-box">

    <div class="banner" :style="{height: calcHeight}">
    </div>

    <div class=" container">

      <div class="domain">
        <el-row :gutter="20" class="domain-row anli-elrow">
          <el-col :xs="24" :sm="8" :md="8" :lg="8">
            <a href="javascript:void(0);" @click="playBtn"><img src="../../../assets/images/subject/zj/video_cover.jpg" style="width: 100%;" /></a>
          </el-col>
          <el-col :xs="24" :sm="16" :md="16" :lg="16">
            <p class="title">什么是设计再造计划</p>
            <p class="des">浙江“传统产业设计再造”计划由浙江省工业设计协会联合相关高校、梦栖工业设计小镇和全省17个省级工业设计示范基地共同发起，由浙江省工业设计创新服务基地运营公司杭州合创共响工业设计管理有限公司负责执行。该计划旨在贯彻落实省政府《浙江省全面改造提升传统制造业行动计划（2017—2020年）》，积极发挥工业设计在改造传统产业中的作用，集聚创新要素，用好创新平台，把浙江近五年集聚的设计优势转化为产业优势、市场优势、竞争优势，整合省内外工业设计产业生态链优势资源，通过工业设计为浙江传统产业注入创新活力，帮助制造企业“增品种、提品质、创品牌”，实现传统产业的转型升级。</p>
          </el-col>
        </el-row>
      </div>
    </div>

    <div class="sub_menu">
      <p>
        <router-link :to="{name: 'subject_zj'}">台州黄岩</router-link>
        <router-link :to="{name: 'subject_zj_lz'}">杭州良渚</router-link>
        <a href="javascript:void(0);" class="active">金华永康</a>
      </p>
    </div>

    <div class=" container">

      <div class="sub-banner">
        <img src="../../../assets/images/subject/zj/yk/banner.jpg" />
      </div>
      <div class="blank60"></div>

      <div class="block block_1">
        <div class="sub-title">
          <div class="tig">
            <div class="jiao"></div>
          </div>
          <div class="cont">
            <h3>开幕致辞</h3>
          </div>
          <div class="foo"></div>
        </div>

        <el-row :gutter="15" class="anli-elrow">
          <el-col :xs="24" :sm="6" :md="6" :lg="6" v-for="(d, index) in startList" :key="index">
            <el-card :body-style="{ padding: '0px' }" class="item">
              <img :src="d.image" class="image">
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
            <div class="jiao"></div>
          </div>
          <div class="cont">
            <h3>签约仪式</h3>
          </div>
          <div class="foo"></div>
        </div>

        <el-row :gutter="20" class="anli-elrow">
          <el-col :xs="24" :sm="12" :md="12" :lg="12" v-for="(d, index) in qdList" :key="index">
            <el-card :body-style="{ padding: '0px' }" class="item">
              <img :src="d.image" class="image">
              <div style="padding: 14px;">
                <p class="des">{{ d.des }}</p>
              </div>
            </el-card>
          </el-col>
        </el-row>
      </div>

      <div class="block block_3">
        <div class="sub-title">
          <div class="tig">
            <div class="jiao"></div>
          </div>
          <div class="cont">
            <h3>浙江“传统产业设计再造”高峰论坛</h3>
          </div>
          <div class="foo"></div>
        </div>

        <el-row :gutter="15" class="anli-elrow">
          <el-col :xs="24" :sm="8" :md="8" :lg="8" v-for="(d, index) in caseList" :key="index">
            <el-card :body-style="{ padding: '0px' }" class="item">
              <img :src="d.image" class="image">
              <div style="padding: 14px;">
                <p class="des">{{ d.title }}</p>
              </div>
            </el-card>
          </el-col>
        </el-row>
      </div>

      <div class="block">
        <div class="sub-title">
          <div class="tig">
            <div class="jiao"></div>
          </div>
          <div class="cont">
            <h3>活动照片</h3>
          </div>
          <div class="foo"></div>
        </div>

        <el-row :gutter="15" class="anli-elrow">
          <el-col :xs="24" :sm="6" :md="6" :lg="6" v-for="(d, index) in hdList" :key="index">
            <el-card :body-style="{ padding: '0px' }" class="item">
              <img :src="d.image" class="image">
            </el-card>
          </el-col>
        </el-row>
      </div>

      <div class="block">
        <div class="sub-title">
          <div class="tig">
            <div class="jiao"></div>
          </div>
          <div class="cont">
            <h3>企业走访</h3>
          </div>
          <div class="foo"></div>
        </div>

        <el-row :gutter="15" class="anli-elrow">
          <el-col :xs="24" :sm="6" :md="6" :lg="6" v-for="(d, index) in vList" :key="index">
            <el-card :body-style="{ padding: '0px' }" class="item">
              <img :src="d.image" class="image">
            </el-card>
          </el-col>
        </el-row>
      </div>

    </div>

    <div class="blank40"></div>

    <div class="bottom">
      <div class="blank20">&nbsp;</div>
      <div class="b-title">
        <p>再造伙伴招募</p>
      </div>
      <p class="content">消费升级的当下，传统产业通过设计升级转型是必然趋势，亦是创造下一个未来的必经之路。我们寻找有创意，有魄力，更对中国设计产业有使命感的设计公司，一起加入再造计划。同时，如果你有亟待改造升级的潜力项目，也可以加入我们。有意加入设计再造计划的有志之师，可发送参与意向至邮箱</p>
      <p class="content"><img src="../../../assets/images/subject/zj/email-icon.png" style="width: 30px; vertical-align:middle;" /> mazhe@taihuoniao.com</p>
      <p class="content">让我们一起，以设计为发力点，打造优质创意价值链，让好设计发声，塑造全新产业升级的明天。</p>
      <p class="content">设计再造计划，等待您的参与。</p>

      <div class="b-title">
        <p>铟果介绍</p>
      </div>
      <p class="content plug">铟果D³INGO是太火鸟旗下基于大数据和智能匹配的产品创新SaaS平台，链接庞大智能硬件创业群体，吸引100万注册用户，1000+家专业设计企业，覆盖2000万创新设计群体。利用大数据和智能匹配技术连接制造企业和设计服务商，以设计驱动创新，挖掘消费需求，加速消费升级，用创新改变世界。</p>
    </div>

    <el-dialog title="视频播放" :visible.sync="dialogVisible" top="5%" :close-on-click-modal="false" size="large">
      <div style="text-align: center;">
        <video :src="videoUrl" controls="controls" width="960">
          您的浏览器不支持 video 标签。
        </video>
      </div>
      <span slot="footer" class="dialog-footer">

      </span>
    </el-dialog>

  </div>
</template>

<script>
import { calcImgSize } from 'assets/js/common'
export default {
  name: 'subject_zj',
  data() {
    return {
      dialogVisible: false,
      startList: [
        {
          'clickUrl': '',
          'position': '永康市科技局局长',
          'name': '李兴周',
          'image': require('@/assets/images/subject/zj/yk/lxz.jpg')
        },
        {
          'clickUrl': '',
          'position': '永康市人民政府副市长',
          'name': '吕群勇',
          'image': require('@/assets/images/subject/zj/yk/lqy.jpg')
        },
        {
          'clickUrl': '',
          'position': '科技部原高新司司长、中国生产力促进中心协会常务副理事长',
          'name': '耿战修',
          'image': require('@/assets/images/subject/zj/yk/gzx.jpg')
        },
        {
          'clickUrl': '',
          'position': '浙江省经信委生产服务业处处长',
          'name': '胡震涛',
          'image': require('@/assets/images/subject/zj/yk/hzt.jpg')
        }
      ],
      qdList: [
        {
          'clickUrl': '',
          'des': '浙江永康五金生产力促进中心和沈阳创新设计服务有限公司签订了合作协议',
          'image': require('@/assets/images/subject/zj/yk/qd1.jpg')
        },
        {
          'clickUrl': '',
          'des': '浙江省工业设计创新中心和永康五金生产力促进中心签订了永康传统产业设计再造战略合作协议',
          'image': require('@/assets/images/subject/zj/yk/qd2.jpg')
        }
      ],
      caseList: [
        {
          'clickUrl': '',
          'title': '浙江工商大学艺术设计学院院长助理 许晓峰',
          'image': require('@/assets/images/subject/zj/yk/case1.jpg')
        },
        {
          'clickUrl': '',
          'title': '浙江大学工业设计系副主任、教授 罗仕鉴',
          'image': require('@/assets/images/subject/zj/yk/case2.jpg')
        },
        {
          'clickUrl': '',
          'title': '杭州源骏工业设计董事长 徐洪军',
          'image': require('@/assets/images/subject/zj/yk/case3.jpg')
        },
        {
          'clickUrl': '',
          'title': '浙江斐络设计设计总监 AndreaFilippi',
          'image': require('@/assets/images/subject/zj/yk/case4.jpg')
        },
        {
          'clickUrl': '',
          'title': '品物·杭州领跑者设计创始人 杨斐',
          'image': require('@/assets/images/subject/zj/yk/case5.jpg')
        },
        {
          'clickUrl': '',
          'title': '北京太火鸟科技合伙人 舒戈',
          'image': require('@/assets/images/subject/zj/yk/case6.jpg')
        }
      ],
      hdList: [
        {
          'image': require('@/assets/images/subject/zj/yk/hd1.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/yk/hd2.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/yk/hd3.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/yk/hd4.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/yk/hd5.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/yk/hd6.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/yk/hd7.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/yk/hd8.jpg')
        }
      ],
      vList: [
        {
          'image': require('@/assets/images/subject/zj/yk/v1.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/yk/v2.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/yk/v3.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/yk/v4.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/yk/v5.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/yk/v6.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/yk/v7.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/yk/v8.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/yk/v9.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/yk/v10.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/yk/v11.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/yk/v12.jpg')
        }
      ],
      videoUrl: 'http://oni525j96.bkt.clouddn.com/video/zjgz.mp4',
      msg: 'This is subject',
      calcHeight: ''
    }
  },
  methods: {
    // 播放视频按钮
    playBtn() {
      // http://oni525j96.bkt.clouddn.com/video/zjgz.mp4
      this.dialogVisible = true
    }
  },
  mounted() {
    var that = this
    window.addEventListener('resize', () => {
      that.calcHeight = calcImgSize(650, 1440)
    })
    this.calcHeight = calcImgSize(650, 1440)
  },
  computed: {
    BMob() {
      return this.$store.state.event.isMob
    }
  }
}

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.container {}

img {
  vertical-align: top
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
  background: url('../../../assets/images/subject/zj/header.jpg');
  background-size: cover;
  background-repeat: no-repeat;
  text-align: center;
}

.domain {
  margin: 20px 0 30px 0;
}

.domain .title {
  font-size: 2.5rem;
  text-align: center;
  margin-bottom: 15px;
  color: #333;
}

.domain .des {
  font-size: 1.5rem;
  padding: 0 1em;
  line-height: 1.9;
  color: #666;
  font-weight: 300;
}

.sub_menu {
  width: 100%;
  height: 50px;
  background-color: #222;
}

.sub_menu p {
  text-align: center;
}

.sub_menu a {
  line-height: 50px;
  color: #fff;
  font-size: 1.8rem;
  margin: 0 15px;
  font-weight: 300;
}

.sub_menu a.active {
  color: #FBEF76;
}

.sub-banner img {
  width: 100%;
}

.sub-title {
  display: flex;
  position: relative;
  height: 38px;
  line-height: 38px;
  margin-bottom: 20px;
}

.sub-title h3 {
  position: absolute;
  left: 68px;
  color: #0F7553;
  font-size: 1.5rem;
}

.sub-title .tig {
  position: relative;
  height: 38px;
  width: 76px;
  background: url('../../../assets/images/subject/zj/sub_title_head.png') no-repeat;
  background-size: cover;
}

.sub-title .tig .jiao {
  position: absolute;
  left: 0;
  top: -12px;
  height: 12px;
  width: 76px;
  background: url('../../../assets/images/subject/zj/sub_title_jiao.png') no-repeat;
  background-size: cover;
}

.sub-title .cont {
  height: 38px;
  width: 100%;
  flex: 1;
  background-color: #FFF037;
}

.sub-title .foo {
  height: 38px;
  width: 50%;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
  background: url('../../../assets/images/subject/zj/sub_title_foot.png') no-repeat right;
  background-size: cover;
}

.block {
  margin-top: 20px;
}

.block_1 {}

.block_1 .item {}

.item img {
  width: 100%;
}

.item {
  margin-bottom: 15px;
}

.block_2 .item {
  height: 400px;
}

.item p {
  color: #666;
  font-size: 1.4rem;
}

.item p.des {
  font-weight: 300;
}

.item p.title {
  line-height: 2;
}

.bottom {
  overflow: hidden;
  height: 570px;
  background: url('../../../assets/images/subject/zj/bottom.png');
  background-size: cover;
  background-repeat: no-repeat;
  text-align: center;
  margin-bottom: -40px;
}

.bottom .b-title {
  width: 190px;
  height: 24px;
  margin: 30px auto 30px auto;
  background: url('../../../assets/images/subject/zj/title-bg.png');
  background-size: cover;
  background-repeat: no-repeat;
  text-align: center;
}

.bottom .b-title p {
  color: #fff;
}

.bottom p.content {
  line-height: 2;
  color: #057577;
  padding: 0 23%;
  margin: 10px 0;
}

.bottom p.content.plug {
  padding: 0 18%;
}

@media screen and (max-width: 767px) {
  .block_2 .item {
    height: auto
  }
  .domain .des {
    padding: 0
  }
}
</style>
