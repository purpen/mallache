<template>
  <div class="container">
    <div class="trend-report">
      <div class="blank20"></div>
      <el-row :gutter="24" class="anli-elrow">
        <v-menu currentName="trendReport" v-if="menuStatus !== 'tools' || !isMob"></v-menu>
        <ToolsMenu v-if="menuStatus === 'tools' && isMob"
                   currentName="trendReport"></ToolsMenu>
        <el-col :span="isMob ? 24 : 20">
          <p class="title">{{pdf.title}}</p>
          <p class="info clearfix">
            <span class="date fl">{{pdf.created_at}}</span>
            <span class="view fl">{{pdf.hits}}</span>
            <span class="tigs fl">
              <b class="fl">标签:</b>
              <i v-for="(e, i) in pdf.tag" :key="i">{{e}}</i>
            </span>
            </p>
          <div v-if="already" class="pdf" v-loading.body="isLoading">
            <pdf :src="pdf.image.file">
            </pdf>
            <menu>
              <menuitem class="plus">aaa</menuitem>
              <menuitem>aaa</menuitem>
              <menuitem>aaa</menuitem>
            </menu>
          </div>
        </el-col>
      </el-row>
    </div>
  </div>
</template>
<script>
  import api from '@/api/api'
  import vMenu from '@/components/pages/v_center/Menu'
  import ToolsMenu from '@/components/pages/v_center/ToolsMenu'
  import pdf from 'vue-pdf'
  export default {
    name: 'trendReport',
    components: {
      vMenu,
      ToolsMenu,
      pdf
    },
    data () {
      return {
        isLoading: true,
        id: '',
        already: false,
        pdf: {},
        pdfUrl: '',
        title: ''
      }
    },
    created () {
      this.id = this.$route.params.id
      this.getPDF()
    },
    methods: {
      getPDF () {
        this.$http.get(api.trendReports, {params: {id: this.id}}).then((res) => {
          if (res.data.meta.status_code === 200) {
            res.data.data.created_at = res.data.data.created_at
            .date_format()
            .format('yyyy年MM月dd日 hh:mm')
            this.pdf = res.data.data
            this.already = true
            this.isLoading = false
          } else {
            this.$message.error(res.data.meta.message)
          }
          console.log(res.data.data)
        }).catch((err) => {
          console.log(err)
        })
      }
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      },
      menuStatus () {
        return this.$store.state.event.menuStatus
      }
    }
  }
</script>
<style scoped>
  .pdf {
    border: 1px solid #D2D2D2;
    position: relative;
  }

  p.title {
    font-size: 24px;
    font-weight: 600;
    text-indent: 30px;
    background: url("../../../../assets/images/tools/report/PDF@2x.png") no-repeat;
    background-size: 24px;
    padding-bottom: 10px;
  }

  p.info {
    color: #999999;
    overflow: hidden;
    padding-bottom: 5px;
  }

  p.info span {
    text-indent: 26px;
    font-size: 12px;
    line-height: 16px;
    padding-right: 15px;
    margin: 5px 0;
  }

  p.info .date {
    text-indent: 22px;
    background: url('../../../../assets/images/tools/report/time-gray@2x.png') no-repeat 0 center;
    background-size: 16px;
  }

  p.info .view {
    text-indent: 30px;
    background: url('../../../../assets/images/tools/report/browse@2x.png') no-repeat 4px center;
    background-size: 20px;
  }

  p.info .tigs {
    text-indent: 0;
  }
  .tigs i {
    float: left;
    text-indent: 24px;
    background: url('../../../../assets/images/tools/report/label-gray@2x.png') no-repeat 6px 2px;
    background-size: 14px;
  }
</style>
