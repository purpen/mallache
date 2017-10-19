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
        <p><el-button class="is-custom" @click="pay" :loading="isLoadingBtn" type="primary">立即支付</el-button></p>
      </div>


    </div>
    <div id="payBlock" style="display:none;"></div>

    <el-dialog title="使用微信支付" v-model="wxPayDialog" :close-on-press-escape="false" :close-on-click-modal="false" @close="closeWxPay()">
      <div class="wx-pay-box">
        <img :src="wxBase64" width="180" />
        <p>等待支付结果...</p>
      </div>
    </el-dialog>

  </div>
</template>

<script>
import api from '@/api/api'
export default {
  name: 'item_payment',
  data () {
    return {
      payType: '',
      isLoadingBtn: false,
      toHtml: '',
      wxPayDialog: false,
      wxBase64: '',
      siObj: '',
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
          url = api.demandWxPay
          break
        case 'jdpay':
          url = api.demandJdPay
          break
      }
      if (!url) {
        this.$message.error('支付方式错误')
        return false
      }

      // 请求支付
      var that = this
      that.isLoadingBtn = true
      that.$http.get(url, {})
      .then (function(response) {
        that.isLoadingBtn = false
        if (response.data.meta.status_code === 200) {
          if (payType === 'alipay') {
            that.toHtml = response.data.data.html_text
            document.getElementById('payBlock').innerHTML = response.data.data.html_text
            document.forms['alipaysubmit'].submit()
          } else if (payType === 'jdpay') {
            that.toHtml = response.data.data.html_text
            document.getElementById('payBlock').innerHTML = response.data.data.html_text
            document.forms['batchForm'].submit()
          } else if (payType === 'wxpay') {
            var qrUrl = response.data.data.code_url
            var uid = response.data.data.uid
            var jrQrcode = require('jr-qrcode')
            var imgBase64 = jrQrcode.getQrBase64(qrUrl)
            that.wxPayDialog = true
            that.wxBase64 = imgBase64
            // 定时请求支付结果
            that.siObj = setInterval(function() {
              that.$http.get(api.orderId.format(uid), {})
              .then (function(response) {
                if (response.data.meta.status_code === 200) {
                  if (response.data.data.status === 1) {
                    clearInterval(that.siObj)
                    that.$router.push({name: 'wxpayCallback', query: {out_trade_no: uid}})
                    return
                  }
                } else {
                }
              })
              .catch (function(error) {
                that.$message.error(error.message)
              })
            }, 5000)
          }
        }
      })
      .catch (function(error) {
        that.isLoadingBtn = false
        that.$message.error(error.message)
      })
    },
    closeWxPay() {
      if (this.siObj) {
        clearInterval(this.siObj)
      }
    }
  },
  created: function() {
    // 如果是公司，跳到首页
    var uType = this.$store.state.event.user.type || 1
    if (uType === 2) {
      this.$message.error('当前账户类型不允许发布项目！')
      this.$router.replace({name: 'home'})
      return
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
    margin: 30px auto 150px auto;
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
    /* width: 150px;
    height: 35px; */
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

  .wx-pay-box {
    text-align: center;
  }

</style>
