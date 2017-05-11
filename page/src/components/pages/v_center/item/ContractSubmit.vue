<template>
  <div class="container">
    <el-row :gutter="24">
      <v-menu></v-menu>

      <el-col :span="20">
        <div class="right-content">
          <v-menu-sub></v-menu-sub>
          <div class="content-box">

          </div>

        </div>

      </el-col>
    </el-row>
  </div>
</template>

<script>
  import vMenu from '@/components/pages/v_center/Menu'
  import vMenuSub from '@/components/pages/v_center/design_case/MenuSub'
  import api from '@/api/api'
  import '@/assets/js/format'
  import '@/assets/js/date_format'
  // import typeData from '@/config'

  export default {
    name: 'vcenter_contract_submit',
    components: {
      vMenu,
      vMenuSub
    },
    data () {
      return {
        userId: this.$store.state.event.user.id,
        itemId: ''
      }
    },
    methods: {
      submit(formName) {
        const that = this
        that.$refs[formName].validate((valid) => {
          // 验证通过，提交
          if (valid) {
            var row = {
              type: that.form.type,
              design_type: that.form.design_type,
              field: that.form.field,
              industry: that.form.industry,
              title: that.form.title,
              customer: that.form.customer,
              prize_time: that.form.prize_time,
              prize: that.form.prize,
              mass_production: that.form.mass_production,
              sales_volume: that.form.sales_volume,
              profile: that.form.profile
            }
            row.prize_time = row.prize_time.format('yyyy-MM-dd')
            var apiUrl = null
            var method = null

            if (that.itemId) {
              method = 'put'
              apiUrl = api.designCaseId.format(that.itemId)
            } else {
              method = 'post'
              apiUrl = api.designCase
              if (that.uploadParam['x:random']) {
                row['random'] = that.uploadParam['x:random']
              }
            }
            that.$http({method: method, url: apiUrl, data: row})
            .then (function(response) {
              if (response.data.meta.status_code === 200) {
                that.$message.success('提交成功！')
                that.$router.push({name: 'vcenterDesignCaseList'})
                return false
              } else {
                that.$message.error(response.data.meta.message)
              }
            })
            .catch (function(error) {
              that.$message.error(error.message)
              console.log(error.message)
              return false
            })

            return false
          } else {
            console.log('error submit!!')
            return false
          }
        })
      }
    },
    computed: {
    },
    watch: {
    },
    created: function() {
      const that = this
      var id = this.$route.params.id
      if (id) {
        that.itemId = id
        that.uploadParam['x:target_id'] = id
        that.$http.get(api.designCaseId.format(id), {})
        .then (function(response) {
          if (response.data.meta.status_code === 200) {
            that.form = response.data.data
            if (response.data.data.sales_volume === 0) {
              that.form.mass_production = 0
            } else {
              that.form.mass_production = 1
            }
            if (response.data.data.case_image) {
              var files = []
              for (var i = 0; i < response.data.data.case_image.length; i++) {
                var obj = response.data.data.case_image[i]
                var item = {}
                item['response'] = {}
                item['name'] = obj['name']
                item['url'] = obj['small']
                item['response']['asset_id'] = obj['id']
                files.push(item)
              }
              that.fileList = files
            }

            console.log(response.data.data)
          }
        })
        .catch (function(error) {
          that.$message({
            showClose: true,
            message: error.message,
            type: 'error'
          })
          console.log(error.message)
          return false
        })
      } else {
        that.itemId = null
      }
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .form-btn {
    float: right;
  }
  .form-btn button {
    width: 120px;
  }

  .avatar-uploader .el-upload {
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }
  .avatar-uploader .el-upload:hover {
    border-color: #20a0ff;
  }
  .avatar-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 178px;
    height: 178px;
    line-height: 178px;
    text-align: center;
  }
  .avatar {
    width: 178px;
    height: 178px;
    display: block;
  }


</style>
