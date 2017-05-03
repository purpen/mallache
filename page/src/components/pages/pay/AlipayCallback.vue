<template>
  <div class="container">
    <div class="pay-result" v-if="payResult">
      {{ message }}
    </div>
    <div class="wait" v-else><p class="normal">等待请求结果...</p></div>
  </div>
</template>

<script>
import api from '@/api/api'
export default {
  name: 'alipay_callback',
  data () {
    return {
      payResult: false,
      redClass: false,
      message: '',
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
      self.message = '*缺少请求回调参数!'
      self.payResult = true
      return false
    }

    // 定时提示,每次3秒,最多5次
    var limitTimes = 0
    var limitObj = setInterval(function() {
      if (limitTimes >= 3) {
        self.redClass = true
        self.message = '*支付出现异常，请稍后去个人中心->我的订单查看支付结果!'
        self.payResult = true
        return
      } else {
        self.$http.get(api.orderId.format(outTradeNo), {})
        .then (function(response) {
          if (response.data.meta.status_code === 200) {
            if (response.data.data.status === 1) {
              self.payResult = true
              self.message = '支付成功! 请稍候...'
              var itemId = response.data.data.item_id
              self.$message.success('支付成功!')
              self.$router.push({name: 'itemSubmitTwo', params: {id: itemId}})
              clearInterval(limitObj)
            }
          } else {
            self.$message.error(response.data.meta.message)
            console.log(response.data.meta.message)
            clearInterval(limitObj)
            return false
          }
        })
        .catch (function(error) {
          clearInterval(limitObj)
          self.$message.error(error.message)
          console.log(error.message)
          return false
        })
      }
      limitTimes += 1
    }, 1000 * 3)
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

</style>
