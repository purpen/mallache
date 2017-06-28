<template>
  <div class="">
    <div class="blank20"></div>
    <div class="container">
      <div class="top-menu-list">
        <router-link :to="{name: 'item'}" class="item">服务条款</router-link>
        <router-link :to="{name: 'question'}" class="item">常见问题</router-link>
        <router-link :to="{name: 'trade'}" class="item is-active">交易保障</router-link>
      </div>
    </div>
    <div class="line"></div>
    <div class="banner">
      <h3>选择太火鸟SaaS，交易有保障</h3>
    </div>

    <div class="">

      <div class="content-box">
        <div class="trade-text gray">
          <h3>保证交易完成</h3>
          <p>服务商未在约定工期内完成工作时，雇主有权发起维权并申请赔付，若判定成立，雇主将获得双倍赔付（双赔赔付由服务商的保证金赔付和服务商需退还的交易款项两部分共同构成）</p>
        </div>

        <div class="trade-text">
          <h3>保证原创</h3>
          <p>铟果平台担保版权。交易完成一年内，如设计需求方发现交易产品非原创，有权发起维权。经判定非原创事实，需求方将获得双倍赔付。</p>
        </div>

        <div class="trade-text gray">
          <h3>版权保护</h3>
          <p>铟果引入时间戳工具，保证设计服务供应商的产品版权，以切实有效的法律手段避免侵权事件发生。</p>
        </div>

      </div>

    </div>

  </div>
</template>

<script>

  export default {
    name: 'trade',
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

  .container {
    min-height: ;
  }

  .line {
    border-bottom: 1px solid #ccc;
  }

  .banner {
    width: 100%;
    height: 450px;
    background: url('../../../assets/images/home/trade_banner.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    text-align: center;
  }

  .banner h3 {
    padding-top: 150px;
    padding-bottom: 10px;
    font-size: 5rem;
    color: #fff;
    line-height: 2;
    font-weight: 300;
  }
  .banner p {
    font-size: 3rem;
    color: #fff;
    font-weight: 300;
    padding: 0 150px;
  }

  .content-box {
  }
  .content-box h3 {
    font-size: 1.8rem;
    line-height: 2;
    margin: 10px 0;
  }

  .content-box p {
    line-height: 2;
  }
  .content-box p img {
    vertical-align: middle;
  }

  .trade-text {
    text-align: center;
    padding: 50px;
  }
  .trade-text.gray {
    background: #FAFAFA;
  }

  .trade-text h3 {
    color: #222;
    font-size: 3rem;
    margin: 0 0;
    font-weight: 500;
    margin-bottom: 30px;
  }
  .trade-text p {
    text-align: left;
    color: #666;
    font-size: 1.8rem;
    line-height: 2;
    padding: 0 350px;
  }


</style>
