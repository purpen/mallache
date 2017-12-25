<template>
  <div class="container">
    <div class="trend-report">
      <div class="blank20"></div>
      <el-row :gutter="24" class="anli-elrow">
        <v-menu currentName="trendReport" v-if="menuStatus !== 'tools' || !isMob"></v-menu>
        <ToolsMenu v-if="menuStatus === 'tools' && isMob"
                   currentName="trendReport"></ToolsMenu>
        <el-col :span="isMob ? 24 : 20">
          <div v-if="already" class="pdf" v-loading.body="isLoading">
            <pdf v-if="already" :src="pdf.image.file" class="show"></pdf>
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
            this.pdf = res.data.data
            this.already = true
            this.isLoading = false
          } else {
            this.$message.error(res.data.meta.message)
          }
          console.log(res)
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
  }
</style>
