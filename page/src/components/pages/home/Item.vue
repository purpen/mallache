<template>
  <div class="">
    <div class="blank20"></div>
    <div class="container">
      <div class="top-menu-list">
        <router-link :to="{name: 'item'}" class="item is-active">服务条款</router-link>
        <router-link :to="{name: 'question'}" class="item">常见问题</router-link>
        <router-link :to="{name: 'trade'}" class="item">交易保障</router-link>
      </div>
    </div>
    <div class="line"></div>
    <div class="container">
      <el-row :gutter="20">

        <el-col :span="24">
          <div class="h-right-content">

            <div class="content-box">
              <h3>1.关于用户</h3>
              <p>在铟果平台注册、发布、承接设计项目的用户，必须是可以代表企业的完全民事行为能力人或法人。用户享有万千民事行为能力人或法人的权利，也要遵守相关的义务，遵守平台规则与相关的法律法规。用户需对平台上的所有操作负法律责任。平台所有用户应遵循的原则包括但不仅限于以下条款</p>
              <p>遵守中国有关的法律和法规；</p>
              <p>遵守所有与网络服务有关的网络协议、规定和程序；</p>
              <p>遵守铟果平台所有规定；</p>
              <p>不得为任何非法目的而使用网络服务系统；</p>
              <p>不得利用铟果网络服务系统进行任何可能对互联网正常运转造成不利影响的行为；</p>
              <p>不得侵犯其他任何第三方的专利权、著作权、商标权、名誉权或其他任何合法权益；</p>
              <p>不得通过价格控制等不正当手段进行不当竞争；</p>
              <p>不得采取不正当手段（包括但不仅限于虚假交易、互换好评等方式）提高自身或他人信用度，或采用不正当手段恶意评价其他用户，降低其他用户信用度；</p>
              <p>&nbsp;</p>
              <p>作为铟果平台的用户，我们需要您做到：</p>
              <p>&nbsp;</p>
              <p>严肃认真对待您在铟果平台上的账号，保证提供的所欲信息真实、准确，如有变更，及时在系统中更改；</p>
              <p>铟果平台账户的所有内容与信息都与您的权利与义务直接相关，请您妥善保管，勿转借他人；若因黑客行为、第三方恶意攻击、用户的保管疏忽导致用户损失的，平台不承担任何责任；</p>


              <h3>2.关于平台</h3>
              <p>铟果平台同意按照本协议规定发布的操作规则提供基于互联网以及移动网相关服务（以下称"网络服务"），为获得网络服务，平台所有用户应当同意本协议的全部条款并按照页面上的提示完成全部的注册程序。用户在使用过程中完成全部流程即表示完全接受本协议项下的全部条款。</p>
              <p></p>
              <p>平台有权根据具体需要修订服务条款或各类规则，届时会以系统通知的形式告知所有用户。</p>

              <h3>3.知识产权</h3>
              <p>铟果平台为项目需求方与设计服务供应商提供合作平台，对所有用户提供必需的服务，在服务过程中遵守国家关于版权和知识产权的相关法律法规。</p>

              <h3>4.隐私保护</h3>
              <p>铟果承诺不对外公开或向第三方提供单个用户的注册资料及用户在使用网络服务时存储在平台的非公开内容；</p>
              <p>为保证平台正常运转，平台会对用户提交的资料进行审核，如发现不妥之处，有权要求用户更改或完善；</p>
              <p>若用户在平台行为涉及违法违规，平台有权配合相关法律机构进行调查；</p>
              <p>有以下情况时，铟果不严格遵守对用户的隐私保护条款：</p>
              <p>事先获得用户的明确授权；</p>
              <p>根据有关的法律法规要求或相关政府主管部门的要求；</p>
              <p>为维护平台其他用户、平台自身或社会公众的利益。</p>


              <h3>5.免责声明</h3>
              <p>在用户遵守平台相关规则的前提下，平台保障用户的隐私和个人信息安全，有网络异常、恶意攻击等不可控情况发生时除外；</p>
              <p>用户违反法律法规或平台规定造成的损失，平台不承担任何责任；</p>
              <p>因不可抗力造成任何一方的损失，平台不承担任何责任；</p>
              <p>用户间的侵权行为，铟果平台可以帮助协调解决，但不承担任何法律责任；</p>

              <h3>6.举报机制</h3>
              <p>若您在平台使用过程中发现任何违法、违规或侵害您权益的现象，请及时与铟果联络，寻求法律手段处理不当现象。</p>
              <p>&nbsp;</p>
              <p>铟果平台对以上协议内容拥有最终解释权</p>

            </div>

          </div>

        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script>

  export default {
    name: 'item',
    components: {
    },
    data () {
      return {
        test: ''
      }
    },
    methods: {
    },
    created: function() {
      document.body.scrollTop = 0
      document.documentElement.scrollTop = 0
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .line {
    border-bottom: 1px solid #ccc;
  }

  .content-box {
    padding: 20px;
  }
  .content-box h3 {
    font-size: 1.8rem;
    line-height: 2;
    margin: 10px 0;
  }

  .content-box p {
    line-height: 1.7;
  }
  .content-box p img {
    vertical-align: middle;
  }

</style>
