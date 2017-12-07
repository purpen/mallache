<template>
  <div id="enterprise">
    <div class="banner" :style="{height : calcHeight}" v-if="isMob"></div>
    <div class="banner2" :style="{height : calcHeight}" v-else></div>
    <div class="main">
      <h3 class="D3title"><i class="D3iLeft"></i><b>关于铟果</b>D³INGO<i class="D3iRight"></i></h3>
      <article>
        {{about}}
        <ul>
          <li v-for="(ele, index) in benefit" :key="index">{{ele}}</li>
        </ul>
      </article>
      <article>
        <h3 class="D3title"><i class="D3iLeft"></i><b>招募对象详细要</b>求<i class="D3iRight"></i></h3>
        <el-row :gutter="20">
          <el-col :offset="isMob ? 0 :6" :xs="24" :sm="12" :md="12" :lg="12">
            <el-card>
              <div class="el_header el_headerLeft">
                设计公司
              </div>
              <div class="el_body">
                <p>发挥创新造物进程中“设计驱动”的环节</p>
                <p class="blod">你需要具备：</p>
                <p>合法注册单位，完备的设计团队，专业案例或设计产品佐证的设计能力，并可从产品自身角度保证产品的快速量产能力。另外还需在铟果D³INGO平台完成企业注册与实名认证。</p>
                <p class="blod">注册与认证详情请见：</p>
                <p>服务-申请加入或帮助-常见问题</p>
              </div>
            </el-card>
          </el-col>
          <el-col v-if="false" :xs="24" :sm="12" :md="12" :lg="12">
            <el-card>
              <div class="el_header el_headerRight">
                供应链
              </div>
              <div class="el_body">
                <p>应用创新环节中的关键齿轮</p>
                <p class="blod">你需要具备：</p>
                <p>合法注册单位，完备的团队、设备及相当的生产能力，能够从产品生产、制造的角度保证产品的快速量产。另外还需在铟果D³INGO平台完成企业注册与实名认证。</p>
                <p class="blod">注册与认证详情请见：</p>
                <p>服务-申请加入或帮助-常见问题</p>
              </div>
            </el-card>
          </el-col>
        </el-row>
      </article>
      <p class="mail">咨询详情请联系 <a href="mailto:mazhe@taihuoniao.com">mazhe@taihuoniao.com</a></p>
      <div class="submit">
        <a class="no-select" @click="intoReg">立即入驻</a>
      </div>
      <h3 class="D3title"><i class="D3iLeft"></i><b>已入驻设计公司</b><i class="D3iRight"></i></h3>
      <section class="img-list clearfix">
        <div v-for="(ele, index) in imgList">
          <img v-lazy="require(`assets/images/company_logo/${index + 1}@3x.png`)" :alt="ele" :key="index"/>
          <span>{{ele}}</span>
        </div>
      </section>
    </div>
    <div class="recruitFoot" v-if="!isMob"></div>
    <div class="recruitFoot2" v-else></div>
  </div>
</template>
<script>
import { calcImgSize } from 'assets/js/common'
import ElCard from '../../../../node_modules/element-ui/packages/card/src/main'
export default {
  components: { ElCard },
  name: 'EnterpriseRecruit',
  data() {
    return {
      calcHeight: '',
      about:
        '铟果D³INGO是太火鸟旗下的创新产品交易与SaaS分发平台，是高效线上设计交易服务平台及中国领先的创新产品策源地，围绕创新产品与设计交易，为相关参与方提供包括创新产品孵化、资金保障、流量支持、运营维护等相关服务与帮助。',
      benefit: [
        '设计公司入驻铟果D³INGO能够：高效对接优质设计项目，解决创新产品的资金、营销难题',
        '项目需求方入驻铟果D³INGO能够：以最直观、有效的方式找到心仪的设计服务供应商，加快创新产品设计、升级进程',
        '我们同时也广泛招募优质供应链，打造真正的创新产品策源地'
      ],
      imgList: [
        '飞鱼设计',
        '飞鱼设计',
        '汉度工业设计',
        '华新意创工业设计',
        'Design Affair',
        '威曼设计',
        '威曼设计',
        '瑞德设计',
        '源骏设计',
        '广州大业设计',
        '杨明洁 Yang',
        '北京智加问道',
        '青岛图灵设计',
        '上品设计',
        '浪尖设计',
        '杭州领跑者设计',
        '北京品物设计',
        '北京品物设计',
        '北京木马设计',
        '上海木马设计',
        '凸凹设计'
      ]
    }
  },
  mounted() {
    let that = this
    window.addEventListener('resize', () => {
      if (that.isMob) {
        that.calcHeight = calcImgSize(670, 750, false)
      } else {
        that.calcHeight = calcImgSize(1260, 2880)
      }
    })
    if (this.isMob) {
      this.calcHeight = calcImgSize(670, 750, false)
    } else {
      this.calcHeight = calcImgSize(1260, 2880)
    }
  },
  computed: {
    isMob() {
      return this.$store.state.event.isMob
    }
  },
  methods: {
    intoReg() {
      this.$router.push({ name: 'register', params: { type: 2 } })
    }
  }
}
</script>
<style scoped>
.banner {
  width: 100%;
  background: url('../../../assets/images/subject/recruit/enterprise/enterprisetop.jpg')
    no-repeat center;
  background-size: cover;
}

.banner2 {
  width: 100%;
  background: url('../../../assets/images/subject/recruit/enterprise/enterprise.jpg')
    no-repeat center;
  background-size: cover;
}

.main {
  max-width: 960px;
  padding: 15px;
  margin: 0 auto;
}

.D3title {
  font-weight: 600;
  text-align: center;
  font-size: 17px;
  padding: 30px 0;
  color: #aa3efd;
}

.D3title i {
  display: inline-block;
  width: 40px;
  height: 10px;
  margin-bottom: 1px;
}

.D3iLeft {
  margin-right: 12px;
  background: url('../../../assets/images/subject/recruit/enterprise/titleleft.png')
    no-repeat;
  background-size: cover;
}

.D3iRight {
  margin-left: 12px;
  background: url('../../../assets/images/subject/recruit/enterprise/titleright.png')
    no-repeat;
  background-size: cover;
}

.D3title b {
  letter-spacing: 8px;
}

.main article {
  font-size: 15px;
  line-height: 26px;
  color: #666;
}

.main ul {
  margin-top: 14px;
}

.main ul li {
  padding-left: 20px;
  background: url('../../../assets/images/subject/recruit/enterprise/spot.png')
    no-repeat 0 center;
  background-size: 10px;
}

.el_header {
  height: 50px;
  text-align: center;
  line-height: 50px;
  color: #fff;
}

.el_headerLeft {
  background: url('../../../assets/images/subject/recruit/enterprise/headerleft.png')
    no-repeat bottom right;
  background-size: cover;
}

.el_headerRight {
  background: url('../../../assets/images/subject/recruit/enterprise/headerright.png')
    no-repeat bottom left;
  background-size: cover;
}

.el_body {
  padding: 20px 30px;
}

.el-card {
  padding: 0;
  margin-bottom: 30px;
}

.blod {
  font-weight: 600;
  margin-top: 10px;
  margin-bottom: 2px;
}

.mail {
  color: #666;
  text-align: center;
  padding-bottom: 18px;
}

.mail a {
  margin-left: 10px;
  color: #aa3efd;
}

.recruitFoot {
  height: 130px;
  background: url('../../../assets/images/subject/recruit/enterprise/foot.png')
    no-repeat center;
  background-size: cover;
  margin-bottom: -52px;
}

.recruitFoot2 {
  height: 72px;
  background: url('../../../assets/images/subject/recruit/enterprise/foot.png')
    no-repeat center;
  background-size: cover;
  margin-bottom: -52px;
}

.submit {
  text-align: center;
}

.submit a {
  letter-spacing: 2px;
  display: block;
  width: 200px;
  height: 42px;
  margin: 12px auto 0;
  background: url('../../../assets/images/subject/recruit/product/Button.png')
    no-repeat;
  background-size: contain;
  font-size: 16px;
  color: #fff;
  line-height: 38px;
}

.submit a:hover {
  background: url('../../../assets/images/subject/recruit/product/Buttonhover.png')
    no-repeat;
  background-size: contain;
}

.submit a:active {
  color: #932bef;
  line-height: 42px;
  text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.5);
  background: url('../../../assets/images/subject/recruit/product/Buttonactive.png')
    no-repeat;
  background-size: contain;
}

.img-list {
  padding-bottom: 20px;
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-start;
}

.img-list div {
  font-family: 'PingFangSC-Light' !important;
  font-weight: 100;
  width: 14%;
  margin: 10px 1%;
  color: #999;
  font-size: 12px;
  text-align: center;
}

.img-list img {
  width: 100%;
  /*height: 50px;*/
  margin-bottom: 10px;
  box-shadow: 0 0 10px rgba(10, 10, 10, 0.1);
  border-radius: 50px;
}

@media screen and (max-width: 767px) {
  .img-list div {
    width: 29%;
    margin: 10px 2%;
  }
}

@media screen and (max-width: 500px) {
  .img-list div {
    margin: 10px 6px;
  }
}
</style>
