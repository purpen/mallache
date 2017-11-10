<template>
  <div class="container">
    <div :class="['payment', {'payment-m' : isMob}]">
      <div class="pay-money">
        <p class="pay-p"><i><span>支付金额</span> &nbsp;&nbsp; <span class="money-txt">99.00 元</span></i></p>
      </div>
      <div class="pay-title">
        <p>支付方式</p>
      </div>
      <div :class="['pay-type',{'pay-type-m' : isMob}]">
        <ul class="clearfix">
          <li>
            <label>
              <div class="item clearfix">
                <el-radio v-model="payType" label="jdpay">&nbsp;</el-radio>
                <img src="../../../assets/images/icon/jd_icon.png"/>
                <span>京东支付</span>
              </div>
            </label>
          </li>
          <li>
            <label>
              <div class="item clearfix">
                <el-radio v-model="payType" label="alipay">&nbsp;</el-radio>
                <img src="../../../assets/images/icon/zfb_icon.png"/>
                <span class="tt">支付宝</span>
              </div>
            </label>
          </li>
          <li>
            <label>
              <div class="item clearfix">
                <el-radio v-model="payType" label="wxpay">&nbsp;</el-radio>
                <img src="../../../assets/images/icon/wx_icon.png"/>
                <span>微信</span>
              </div>
            </label>
          </li>
        </ul>
        <div class="clear"></div>
      </div>
      <div :class="['pay-btn', {'pay-btn-m' : isMob }]">
        <p>
          <el-button class="is-custom" @click="pay" :loading="isLoadingBtn" type="primary">立即支付</el-button>
        </p>
      </div>


    </div>
    <div id="payBlock" style="display:none;"></div>

    <el-dialog title="使用微信支付" v-model="wxPayDialog" :close-on-press-escape="false" :close-on-click-modal="false"
               @close="closeWxPay()">
      <div class="wx-pay-box">
        <img :src="wxBase64" width="180"/>
        <p>等待支付结果...</p>
      </div>
    </el-dialog>

  </div>
</template>

<script>
  import api from '@/api/api'
  export default {
    name: 'item_payment',
    data() {
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
        let payType = this.payType
        if (!payType) {
          this.$message.error('请选择支付方式')
          return false
        }
        let url = null
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
        let that = this
        that.isLoadingBtn = true
        that.$http.get(url, {})
          .then(function (response) {
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
                let qrUrl = response.data.data.code_url
                let uid = response.data.data.uid
                let jrQrcode = require('jr-qrcode')
                let imgBase64 = jrQrcode.getQrBase64(qrUrl)
                that.wxPayDialog = true
                that.wxBase64 = imgBase64
                // 定时请求支付结果
                that.siObj = setInterval(function () {
                  that.$http.get(api.orderId.format(uid), {})
                    .then(function (response) {
                      if (response.data.meta.status_code === 200) {
                        if (response.data.data.status === 1) {
                          clearInterval(that.siObj)
                          that.$router.push({
                            name: 'wxpayCallback',
                            query: {
                              out_trade_no: uid
                            }
                          })
                          return true
                        }
                      }
                    })
                    .catch(function (error) {
                      that.$message.error(error.message)
                    })
                }, 5000)
              }
            }
          })
          .catch(function (error) {
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
    created: function () {
      // 如果是公司，跳到首页
      let uType = this.$store.state.event.user.type || 1
      if (uType === 2) {
        this.$message.error('当前账户类型不允许发布项目！')
        this.$router.replace({
          name: 'home'
        })
        return false
      }
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      }
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .payment {
    width: 800px;
    border: 1px solid #e6e6e6;
    margin: 30px auto 150px auto;
  }

  .payment-m {
    width: auto;
    margin-top: 0;
    border: none;
  }

  .payment p {
    padding: 10px;
  }

  .pay-p {
    line-height: 30px;
  }

  .payment-m .pay-p {
    font-size: 1.7rem;
    padding: 10px 0;
    background: #FAFAFA;
  }

  .payment-m .pay-p i {
    display: block;
    padding: 6px 0;
    background: #fff;
    padding-left: 10px;
    border-top: 1px solid #e6e6e6;
    border-bottom: 1px solid #e6e6e6
  }

  .money-txt {
    font-size: 2rem;
    color: #FF5A5F;
  }

  .pay-title {
    border-top: 1px solid #e6e6e6;
    border-bottom: 1px solid #e6e6e6;
    background: #FAFAFA;
    line-height: 30px;
  }

  .payment-m .pay-title {
    background: none;
    border-bottom: none;
  }

  .pay-title p {
    font-size: 1.7rem;
  }

  .pay-btn p button {
    padding: 10px 40px;
  }

  .pay-btn-m p {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
    padding: 0
  }

  .pay-btn-m p button {
    width: 100%;
    margin: 0 10px;
  }

  .pay-type {
    border-bottom: 1px solid #e6e6e6;
    margin-top: 20px;
  }

  .payment-m .pay-type {
    margin-top: 0;
  }

  .pay-type ul {
    margin-bottom: 10px;
  }

  .payment li {
    width: 200px;
  }

  .pay-type ul li {
    float: left;
  }

  .pay-type-m {
    border: none;
  }

  .pay-type-m ul {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
  }

  .pay-type-m ul li {
    width: 100%;
    padding-left: 10px;
  }

  .pay-type .item {
    border: 1px solid #e6e6e6;
    margin: 10px;
    padding: 8px 15px 8px 15px;
  }

  .pay-type-m .item {
    margin: 0;
    border: none;
    border-top: 1px solid #e6e6e6;
  }

  .pay-type-m .item label {
    float: right;
    line-height: 31px;
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

  @media screen and (max-width: 767px) {
    .payment {
      width: auto
    }
  }
</style>
