<template>
  <div class="container">
    <div class="blank20"></div>
    <div class="payment">
      <div class="title-item">
        <h3>支付项目资金</h3>
        <p>客户需要将项目资金预先托管至太火鸟SaaS，完成后项目将自动启动并进入项目管理阶段。</p>
      </div>
      <div class="order-item">
        <p class="banner">订单详情</p>
        <p>订单内容:&nbsp;&nbsp; {{ item.item_name }}</p>
        <p>金&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;额:&nbsp;&nbsp; ¥ {{ item.amount }}</p>
        <p>订单编号:&nbsp;&nbsp; {{ item.uid }}</p>
      </div>
      <div class="pay-item">
        <p class="banner">选择支付方式</p>

        <div class="pay-type">
          <ul>
            <li>
              <label>
                <div :class="{'item': true, active: payType === 1 ? true : false}" @click="checkedPayBtn(1)">
                  <img src="../../../assets/images/icon/alipay.png"/>
                  <img class="pay-active" src="../../../assets/images/icon/pay_checked.png"/>
                </div>
              </label>
            </li>
            <!--
            <li>
              <label>
                <div :class="{'item': true, active: payType === 2 ? true : false}" @click="checkedPayBtn(2)">
                  <img src="../../../assets/images/icon/union1.png" />
                  <img class="pay-active" src="../../../assets/images/icon/pay_checked.png" />
                </div>
              </label>
            </li>
            -->
            <li>
              <label>
                <div :class="{'item': true, active: payType === 5 ? true : false}"
                     @click="checkedPayBtn(5)">
                  <p>对公转账</p>
                  <img class="pay-active" src="../../../assets/images/icon/pay_checked.png"/>
                </div>
              </label>
            </li>

          </ul>
          <div class="clear"></div>
        </div>

        <div class="pay-box">
          <p>
            <el-button class="is-custom" @click="pay" type="primary">立即支付</el-button>
          </p>
          <p class="total-price">¥ {{ item.amount }}</p>
          <p class="total-txt">总计: </p>
        </div>
        <div class="clear"></div>

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
        item: '',
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
          case 1:
            url = api.secondAlipayId.format(this.item.id)
            break
          case 2:
            url = 'wxpay'
            break
          case 5:
            url = api.payItemBankPayId.format(this.item.id)
            break
        }
        if (!url) {
          this.$message.error('支付方式错误')
          return false
        }

        // 请求支付
        var self = this
        self.$http.get(url, {})
          .then(function (response) {
            self.isFirst = true
            if (response.data.meta.status_code === 200) {
              if (payType === 1) {
                self.$nextTick(function () {
                  self.toHtml = response.data.data.html_text
                  document.getElementById('payBlock').innerHTML = response.data.data.html_text
                  document.forms['alipaysubmit'].submit()
                })
              }
              if (payType === 2) {
              }
              if (payType === 5) {
                self.$router.push({name: 'vcenterOrderShow', params: {id: self.item.uid}})
              }
            }
          })
          .catch(function (error) {
            self.$message.error(error.message)
          })
      },
      checkedPayBtn(payType) {
        this.payType = payType
      }
    },
    created: function () {
      const self = this
      var itemId = this.$route.params.item_id
      if (itemId) {
        self.itemId = itemId
        self.$http.get(api.endPayOrderItemId.format(itemId), {})
          .then(function (response) {
            if (response.data.meta.status_code === 200) {
              self.item = response.data.data
              // 如果已支付完成,跳到项目页
              if (self.item.status === 1) {
                self.$message.error('已支付款项!')
                self.$router.replace({name: 'vcenterItemShow', params: {id: self.itemId}})
                return
              }
              console.log(response.data.data)
            } else {
              self.$message.error(response.data.meta.message)
            }
          })
          .catch(function (error) {
            self.$message.error(error.message)
          })
      } else {
        self.$message.error('缺少请求参数!')
        self.$router.push({name: 'home'})
      }
    }

  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .payment {
    border: 1px solid #ccc;
    width: 900px;
    margin: 30px auto 30px auto;
    padding: 20px 20px 20px 20px;
  }

  .payment p {
    color: #333;
  }

  .title-item {

  }

  .title-item h3 {
    font-size: 2rem;
  }

  .title-item p {
    color: #666;
    font-size: 1rem;
    margin: 20px 0 20px 0;
  }

  .order-item p {
    line-height: 2;
  }

  .order-item p.banner, .pay-item p.banner {
    font-size: 1.6rem;
    border-bottom: 2px solid #ccc;
    margin-bottom: 15px;
  }

  .pay-item {
    margin: 15px 0 0 0;
  }

  .pay-item p {
    line-height: 2;
  }

  .pay-type {
    height: 100px;
  }

  .pay-type ul li {
    float: left;
    text-align: center;
  }

  .pay-type .item {
    position: relative;
    cursor: pointer;
    border: 1px solid #ccc;
    width: 160px;
    margin: 10px;
    padding: 15px 20px 15px 20px;
  }

  .pay-type .item.active {
    background: #F2FBFF;
    border: 1px solid #00A7F7;
  }

  .pay-type .item.active .pay-active {
    display: block;
  }

  .pay-type .item img {
  }

  .pay-type .item p {
    line-height: 35px;
    font-size: 2.5rem;
  }

  .pay-box {
    margin-top: 20px;
  }

  .pay-box p {
    float: right;
  }

  p.total-price {
    margin: 0 20px 0 10px;
    color: #FF5A5F;
    font-size: 2rem;
  }

  p.total-txt {
    line-height: 40px;
  }

  .pay-active {
    display: none;
    position: absolute;
    top: 0;
    left: 0;
  }


</style>
