<template>
  <div>
    <div class="share-box">
      <span><i class="el-icon-share"></i> 分享：</span>
      <a href="javascript:void(0);" @click="weChat()" id="wechat-share"><img src="../../assets/images/icon/wechat.png" /></a>
      <a href="javascript:void(0);" @click="weibo()" id="sina-share"><img src="../../assets/images/icon/weibo.png" /></a>
      <a href="javascript:void(0);" @click="qZone()" id="qzone-share"><img src="../../assets/images/icon/q_zone.png" /></a>
    </div>

    <el-dialog
      title="打开微信“扫一扫”，打开网页后点击屏幕右上角分享按钮"
      :visible.sync="dialogVisible"
      :close-on-click-modal="false">
        <div style="text-align: center;" id="qrcode">
        </div>
      <span slot="footer" class="dialog-footer">

      </span>
    </el-dialog>
  </div>
</template>

<script>
import qrcanvas from 'qrcanvas'
export default {
  name: 'share_box',
  props: {
    link: {
      default: ''
    },
    title: {
      default: ''
    },
    picUrl: {
      default: ''
    },
    site: {
      default: '太火鸟-铟果SaaS'
    }
  },
  data () {
    return {
      windowName: 'tShare',
      dialogVisible: false,
      msg: ''
    }
  },
  computed: {
    oLink() {
      return encodeURIComponent(this.link)
    },
    oTitle() {
      return encodeURIComponent(this.title)
    },
    oPicUrl() {
      return encodeURIComponent(this.picUrl)
    },
    oSite() {
      return encodeURIComponent(this.site)
    }
  },
  methods: {
    getParamsOfShareWindow(width, height) {
      return ['toolbar=0,status=0,resizable=1,width=' + width + ',height=' + height + ',left=', (screen.width - width) / 2, ',top=', (screen.height - height) / 2].join('')
    },
    weChat() {
      this.dialogVisible = true
      let canvas = qrcanvas({
        data: this.link,
        size: 250,
        correctLevel: 'M'
      })
      var timer = setInterval(function() {
        if (document.getElementById('qrcode')) {
          console.log(canvas)
          document.getElementById('qrcode').innerHTML = ''
          document.getElementById('qrcode').appendChild(canvas)
          clearInterval(timer)
        }
      }, 100)
    },
    weibo() {
      var url = 'http://v.t.sina.com.cn/share/share.php?url=' + this.oLink + '&title=' + this.oTitle + '&pic=' + this.oPicUrl
      var params = this.getParamsOfShareWindow(607, 523)
      window.open(url, this.windowName, params)
      return false
    },
    qZone() {
      var url = 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=' + this.oLink + '&title=' + this.oTitle + '&pics=' + this.oPicUrl + '&site=' + this.oSite
      var params = this.getParamsOfShareWindow(600, 560)
      window.open(url, this.windowName, params)
      return false
    }
  },
  mounted () {
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style>
  .share-box {
    margin: 20px;
  }
  .share-box a {
    margin: 10px 10px;
  }
  .share-box a img {
    width: 5%;
    vertical-align: middle;
  }
</style>
