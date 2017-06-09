<template>
  <div class="container">

    <el-row :gutter="24">
      <v-menu currentName="order"></v-menu>

      <el-col :span="20">
        <div class="right-content">
          <v-menu-sub></v-menu-sub>
          <div class="content-box">
            <div class="main">
              <div class="status">
                <p class="main-status">订单状态: <span>{{ item.status_value }}</span></p>
                <p class="main-des" v-if="item.pay_type === 5 && item.status === 0">请于 {{ item.expire_at }} 前完成支付，逾期会关闭交易</p>
              </div>
              <div class="operation">
                <p v-if="item.status === 0"><el-button class="is-custom" @click="rePay">更改支付方式</el-button></p>
                <!--<p><el-button >取消订单</el-button></p>-->
              </div>
            </div>

            <div class="clear detail">
              <p class="detail-banner">订单详情</p>
              <p>项目名称: {{ item.item_name }}</p>
              <p>支付方式: {{ item.pay_type_value }}</p>
              <p>金&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;额: ¥ {{ item.amount }}</p>
              <p>订单编号: {{ item.uid }}</p>
              <p>备&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注: {{ item.summary }}</p>
              <p>创建时间: {{ item.created_at }}</p>

              <div class="outline-pay" v-show="item.pay_type === 5">
                <p class="detail-banner">对公转账</p>
                <p>收款公司: 北京太火红鸟科技有限公司</p>
                <p>收款账户: 1219 6215 6039 6010 3058 086</p>
                <p>开&nbsp;&nbsp;户&nbsp;行: 招商银行北京三里屯支行</p>
              </div>
            </div>

            <div class="server">
              <p>如果您有任何疑问，请立即联系客服。</p>
              <p>邮箱：support@taihuoniao.com</p>
              <p>电话：400-1234567</p>
            </div>

          </div>
        </div>

      </el-col>
    </el-row>

  </div>
</template>

<script>
import api from '@/api/api'
import vMenu from '@/components/pages/v_center/Menu'
import vMenuSub from '@/components/pages/v_center/order/MenuSub'

export default {
  name: 'vcenter_order_show',
  components: {
    vMenu,
    vMenuSub
  },
  data () {
    return {
      item: {},
      itemUid: '',
      msg: ''
    }
  },
  methods: {
    // 更改支付方式
    rePay() {
      this.$router.push({name: 'itemPayFund', params: {item_id: this.item.item_id}})
    }
  },
  computed: {
  },
  created: function() {
    const self = this
    var itemUid = this.$route.params.id
    if (itemUid) {
      self.itemUid = itemUid
      self.$http.get(api.orderId.format(itemUid), {})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          self.item = response.data.data
          var createdAt = self.item.created_at
          var createdFormat = createdAt.date_format()
          self.item.created_at = createdFormat.format('yyyy-MM-dd hh:mm')
          var expire = new Date((createdFormat / 1000 + 86400 * 3) * 1000)
          self.item.expire_at = expire.format('yyyy-MM-dd hh:mm')
          console.log(response.data.data)
        } else {
          self.$message.error(response.data.meta.message)
        }
      })
      .catch (function(error) {
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
  
  .content {
  }
  .content-box {
    height: 100%;
    border: 1px solid #ccc;
    padding: 20px;
  }
  .main {
    padding: 20px 0 20px 0;
  }
  .main p {
    line-height: 2;
  }
  .main .status {
    float: left;
  }
  .main .operation {
    float: right;
  }
  .main-status {
    font-size: 2rem;
    color: #222;
    font-weight: 400;
  }
  .main-status span {
    color: #FF5A5F;
  }
  .main-des {
    color: #666;
    font-size: 1rem;
  }
  .operation p {
    line-height: 50px;
  }
  .operation p button {
    width: 120px;
  }

  .detail {
  
  }
  .detail p {
    line-height: 2.5;
  }
  .detail-banner {
    font-size: 1.8rem;
    line-height: 2;
    border-bottom: 1px solid #ccc;
    margin-bottom: 20px;
  }
  .outline-pay {
    margin-top: 20px;
  }

  .server {
    margin-top: 50px;
  }
  .server p {
    color: #999;
    font-size: 1.8rem;
    line-height: 2;
  }

</style>

