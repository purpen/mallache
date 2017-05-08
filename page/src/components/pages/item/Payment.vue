<template>
  <div class="container">
    <div class="payment">
      <div class="pay-money">
        <p class="pay-p"><span>支付金额</span> &nbsp;&nbsp; <span class="money-txt">99.00 元</span></p>
      </div>
      <div class="pay-title">
        <p>支付方式</p>
      </div>
      <div class="pay-type">
        <ul>
          <li>
            <label>
              <div class="item">
                <el-radio v-model="payType" label="jdpay">&nbsp;</el-radio>
                <img src="../../../assets/images/icon/jd_icon.png" />
                <span>京东支付</span>
              </div>
            </label>
          </li>
          <li>
            <label>
              <div class="item">
                <el-radio v-model="payType" label="alipay">&nbsp;</el-radio>
                <img src="../../../assets/images/icon/zfb_icon.png" />
                <span class="tt">支付宝</span>
              </div>
            </label>
          </li>
          <li>
            <label>
              <div class="item">
                <el-radio v-model="payType" label="wxpay">&nbsp;</el-radio>
                <img src="../../../assets/images/icon/wx_icon.png" />
                <span>微信</span>
              </div>
            </label>
          </li>

        </ul>
        <div class="clear"></div>
      </div>
      <div class="pay-btn">
        <p><el-button class="is-custom" @click="pay" type="primary">立即支付</el-button></p>     
      </div>
    
    </div>
    <div id="payBlock"></div>
  </div>
</template>

<script>
import api from '@/api/api'
export default {
  name: 'item_payment',
  data () {
    return {
      payType: '',
      toHtml: '',
      msg: 'This is About!!!'
    }
  },
  methods: {
    pay() {
      var payType = this.payType
      if (!payType) {
        this.$message.error('请选择支付方式')
        return false
      }
      var url = null
      switch (payType) {
        case 'alipay':
          url = api.demandAlipay
          break
        case 'wxpay':
          url = 'wxpay'
          break
        case 'jdpay':
          url = 'jdpay'
          break
      }
      if (!url) {
        this.$message.error('支付方式错误')
        return false
      }

      // 请求支付
      var that = this
      that.$http.get(url, {})
      .then (function(response) {
        that.isFirst = true
        if (response.data.meta.status_code === 200) {
          that.$nextTick(function() {
            that.toHtml = response.data.data.html_text
            document.getElementById('payBlock').innerHTML = response.data.data.html_text
            document.forms['alipaysubmit'].submit()
          })
        }
      })
      .catch (function(error) {
        that.$message.error(error.message)
        console.log(error.message)
        return false
      })
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .payment {
    border: 1px solid #ccc;
    width: 800px;
    height: 280px;
    margin: 30px auto 30px auto;
  }
  .payment p {
    padding: 10px;
  }
  .pay-p {
    line-height: 30px;
  }
  .money-txt {
    font-size: 2rem;
    color: #FF5A5F;
  }
  .pay-title {
    border-top: 1px solid #ccc;
    border-bottom: 1px solid #ccc;
    background: #FAFAFA;
    line-height: 30px;
  }
  .pay-title p {
    font-size: 1.7rem;
  }

  .pay-btn p button {
    padding: 10px 40px 10px 40px;
  }
  .pay-type {
    height: 100px;
    border-bottom: 1px solid #ccc;
    margin-top: 20px;
  }
  .pay-type ul li {
    float: left;
  }
  .pay-type .item {
    border: 1px solid #ccc;
    width: 150px;
    height: 35px;
    margin: 10px;
    padding: 8px 15px 8px 15px;
  }
  .pay-type .item img {
    vertical-align: -8px;
  }
  .pay-type .item span {
    line-height: 30px;
    font-size: 1.5rem;
    padding-left: 10px;
  }

</style>
