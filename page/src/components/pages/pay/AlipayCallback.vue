<template>
  <div class="container">
    <div class="pay-result" v-if="payResult">
      <div class="publish-box" v-if="paySuccess">
        <p class="success-img"><img src="../../../assets/images/icon/success.png" /></p>
        <p class="success-str">支付成功</p>
        <p class="success-des">请稍后...</p>
      </div>

      <div class="publish-box" v-else>
        <p class="success-img"><img src="../../../assets/images/icon/fail.png" /></p>
        <p class="success-str fail">支付失败</p>
        <p class="success-des red">{{ errorMessage }}</p>
        <p>
          <router-link :to="{name: 'home'}" class="item">返回首页</router-link>
          <router-link :to="{name: 'vcenterItemList'}" class="item">项目管理</router-link>
        </p>
      </div>
    </div>
    <div class="wait" v-else><p class="normal">等待支付结果...</p></div>
  </div>
</template>

<script>
import api from '@/api/api'
export default {
  name: 'alipay_callback',
  data () {
    return {
      payResult: false,
      paySuccess: false,
      redClass: false,
      errorMessage: '支付出现异常，请稍后去个人中心->我的订单查看支付状态!',
      msg: ''
    }
  },
  created: function() {
    var self = this
    // 获取回调参数
    // console.log(self.$route.query)
    var outTradeNo = self.$route.query.out_trade_no
    // var subject = self.$route.query.subject

    if (!outTradeNo) {
      self.$message.error('缺少请求回调参数!')
      self.$router.push({name: 'home'})
      return false
    }

    self.$http.get(api.orderId.format(outTradeNo), {})
    .then (function(response) {
      if (response.data.meta.status_code === 200) {
        if (response.data.data.status === 1) {
          self.payResult = true
          self.paySuccess = true
          setTimeout(function () {
            var itemId = response.data.data.item_id
            var type = response.data.data.type
            if (type === 1) {
              self.$router.replace({name: 'itemSubmitTwo', params: {id: itemId}})
            } else {
              self.$router.replace({name: 'vcenterItemShow', params: {id: itemId}})
            }
          }, 3000)
          return
        }
      } else {
        console.log(response.data.meta.message)
        self.payResult = true
        self.errorMessage = response.data.meta.message
        return false
      }
    })
    .catch (function(error) {
      self.errorMessage = error.message
      console.log(error.message)
      return false
    })
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .wait {
    
  }
  .normal {
    color: #666;
    font-size: 1.5rem;
  }
  .red {
    color: red;
  }

  .publish-box{
    width: 100%;
    height: 500px;
    text-align:center;
    margin: 30px auto 30px auto;
    padding: 100px 0 0 0;
  }

  .success-img {
    margin-bottom: 15px;
  }
  .success-img img {
    
  }
  .success-str {
    font-size: 2rem;
    color: #00AC84;
    margin-bottom: 20px;
  }
  .fail {
    color: #FE3824;
  }
  .success-des {
    color: #666;
    font-size: 1rem;
    margin-bottom: 25px;
  }

</style>
