<template>
  <div class="content-box">

    <div class="banner">
    </div>

    <div class=" container">

      <div class="domain">
        <el-row :gutter="20">
          <el-col :span="8">
            <a href="javascript:void(0);" @click="playBtn"><img src="../../../assets/images/subject/zj/video_cover.jpg" style="width: 100%;" /></a>
          </el-col>
          <el-col :span="16">
            <p class="title">什么是设计再造计划</p>
            <p class="des">浙江“传统产业设计再造”计划由浙江省工业设计协会联合相关高校、梦栖工业设计小镇和全省17个省级工业设计示范基地共同发起，由浙江省工业设计创新服务基地运营公司杭州合创共响工业设计管理有限公司负责执行。该计划旨在贯彻落实省政府《浙江省全面改造提升传统制造业行动计划（2017—2020年）》，积极发挥工业设计在改造传统产业中的作用，集聚创新要素，用好创新平台，把浙江近五年集聚的设计优势转化为产业优势、市场优势、竞争优势，整合省内外工业设计产业生态链优势资源，通过工业设计为浙江传统产业注入创新活力，帮助制造企业“增品种、提品质、创品牌”，实现传统产业的转型升级。</p>
          </el-col>
        </el-row>
      </div>

      <div class="block block_1">
        <div class="sub-title">
          <div class="tig"><div class="jiao"></div></div>
          <div class="cont">
            <h3>开幕致辞</h3>
          </div>
          <div class="foo"></div>
        </div>

        <el-row :gutter="15">
          <el-col :span="8" v-for="(d, index) in startList" :key="index">
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
          <div class="tig"><div class="jiao"></div></div>
          <div class="cont">
            <h3>启动仪式</h3>
          </div>
          <div class="foo"></div>
        </div>

        <el-row :gutter="20">
          <el-col :span="12" v-for="(d, index) in qdList" :key="index">
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
          <div class="tig"><div class="jiao"></div></div>
          <div class="cont">
            <h3>浙江“传统产业设计再造”高峰论坛</h3>
          </div>
          <div class="foo"></div>
        </div>

        <el-row :gutter="15">
          <el-col :span="8" v-for="(d, index) in caseList" :key="index">
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
          <div class="tig"><div class="jiao"></div></div>
          <div class="cont">
            <h3>浙江“传统产业设计再造”对接会</h3>
          </div>
          <div class="foo"></div>
        </div>

        <el-row :gutter="15">
          <el-col :span="12" v-for="(d, index) in djList" :key="index">
            <el-card :body-style="{ padding: '0px' }" class="item">
              <img :src="d.image" class="image">
            </el-card>
          </el-col>
        </el-row>
      </div>

      <div class="block">
        <div class="sub-title">
          <div class="tig"><div class="jiao"></div></div>
          <div class="cont">
            <h3>活动照片</h3>
          </div>
          <div class="foo"></div>
        </div>

        <el-row :gutter="15">
          <el-col :span="6" v-for="(d, index) in hdList" :key="index">
            <el-card :body-style="{ padding: '0px' }" class="item">
              <img :src="d.image" class="image">
            </el-card>
          </el-col>
        </el-row>
      </div>

    </div>

    <div class="bottom">
      <div class="blank20">&nbsp;</div>
      <div class="b-title">
        <p>再造伙伴招募</p>
      </div>
      <p class="content">消费升级的当下，传统产业通过设计升级转型是必然趋势，亦是创造下一个未来的必经之路。我们在寻找有创意，有魄力，更对中国设计产业有使命感的设计公司，一起加入再造计划。有意加入设计再造计划的有志之师，可发送参与意向至邮箱</p>
      <p class="content"><img src="../../../assets/images/subject/zj/email-icon.png" style="width: 30px; vertical-align:middle;" /> mazhe@taihuoniao.com</p>
      <p class="content">让我们一起，以设计为发力点，打造优质创意价值链，让好设计发声，塑造全新产业升级的明天。</p>
      <p class="content">设计再造计划，等待你参与</p>

      <div class="b-title">
        <p>铟果介绍</p>
      </div>
      <p class="content plug">铟果D³ingo是太火鸟旗下基于大数据和智能匹配的产品创新SaaS平台，链接庞大智能硬件创业群体，吸引100万注册用户，1000+家专业设计企业，覆盖2000万创新设计群体。利用大数据和智能匹配技术连接制造企业和设计服务商，以设计驱动创新，挖掘消费需求，加速消费升级，用创新改变世界。</p>
    </div>

    <el-dialog
      title="视频播放"
      :visible.sync="dialogVisible"
      top="5%"
      :close-on-click-modal="false"
      size="large">
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
export default {
  name: 'subject_zj',
  data () {
    return {
      dialogVisible: false,
      startList: [
        {
          'clickUrl': '',
          'position': '浙江省经信委生产服务业处调研员',
          'name': '黄哲明',
          'image': require('@/assets/images/subject/zj/hzm.jpg')
        },
        {
          'clickUrl': '',
          'position': '黄岩区副区长',
          'name': '陶慧敏',
          'image': require('@/assets/images/subject/zj/thm.jpg')
        },
        {
          'clickUrl': '',
          'position': '浙江省工业设计协会副会长',
          'name': '章群星',
          'image': require('@/assets/images/subject/zj/zqx.jpg')
        }
      ],
      qdList: [
        {
          'clickUrl': '',
          'des': '省经信委生产服务业处调研员黄哲明，台州市经信委副主任陈海崧，黄岩区副区长陶慧敏，黄岩区政府党组成员、区工业领导小组专职副组长王志和，黄岩区经信局局长郑建光，浙江省工业设计协会副会长章群星共同启动浙江“传统产业设计再造计划”',
          'image': require('@/assets/images/subject/zj/qd1.jpg')
        },
        {
          'clickUrl': '',
          'des': '浙江省工业设计创新服务基地与北京太火鸟科技有限公司正式签约《共促浙江好设计交易战略合作》',
          'image': require('@/assets/images/subject/zj/qd2.jpg')
        }
      ],
      caseList: [
        {
          'clickUrl': '',
          'title': '星威国际家居总经理 朱坤',
          'image': require('@/assets/images/subject/zj/case1.jpg')
        },
        {
          'clickUrl': '',
          'title': '博乐设计董事长 周立钢',
          'image': require('@/assets/images/subject/zj/case2.jpg')
        },
        {
          'clickUrl': '',
          'title': '品物设计•火人孵化创始人 杨斐',
          'image': require('@/assets/images/subject/zj/case3.jpg')
        },
        {
          'clickUrl': '',
          'title': '太火鸟科技创始人&CEO 雷海波',
          'image': require('@/assets/images/subject/zj/case4.jpg')
        },
        {
          'clickUrl': '',
          'title': '奥格设计董事长 章炜',
          'image': require('@/assets/images/subject/zj/case5.jpg')
        },
        {
          'clickUrl': '',
          'title': '京东众筹文化创意组负责人 李晓熙',
          'image': require('@/assets/images/subject/zj/case6.jpg')
        }
      ],
      djList: [
        {
          'image': require('@/assets/images/subject/zj/dj1.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/dj2.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/dj3.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/dj4.jpg')
        }
      ],
      hdList: [
        {
          'image': require('@/assets/images/subject/zj/hd1.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/hd2.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/hd3.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/hd4.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/hd5.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/hd6.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/hd7.jpg')
        },
        {
          'image': require('@/assets/images/subject/zj/hd8.jpg')
        }
      ],
      videoUrl: 'http://oni525j96.bkt.clouddn.com/video/zjgz.mp4',
      msg: 'This is subject'
    }
  },
  methods: {
    // 播放视频按钮
    playBtn() {
      // http://oni525j96.bkt.clouddn.com/video/zjgz.mp4
      this.dialogVisible = true
    }
  }
}

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .container{
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
    height: 650px;
    background: url('../../../assets/images/subject/zj/banner.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    text-align: center;
  }

  .domain {
    margin: 20px 0 40px 0;
  }
  .domain .title {
    font-size: 2.5rem;
    margin-bottom: 15px;
    color: #333;
  }
  .domain .des {
    font-size: 1.5rem;
    line-height: 1.9;
    color: #666;
    font-weight: 300;
  }

  .sub-title {
    display: flex;
    height: 38px;
    line-height: 38px;
    margin-bottom: 20px;
  }
  .sub-title h3 {
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
    width: 640px;
    background: url('../../../assets/images/subject/zj/sub_title_foot.png') no-repeat;
    background-size: cover;
  }

  .block {
    margin-top: 20px;
  }

  .block_1 {
  
  }
  .block_1 .item {
  
  }
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


</style>
