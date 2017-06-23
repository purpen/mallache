<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20">

      <el-col :span="18">
        <div class="design-case-content edit-content">
          <div class="title">
            <h1>{{ item.title }}</h1>
          </div>
          <div class="summary">
            <p>{{ item.profile }}</p>
          </div>
          <div class="des">
            <p v-for="d in item.case_image"><img :src="d.big" :alt="d.name" :title="d.name" /></p>
          </div>
        </div>
      </el-col>

      <el-col :span="6">
        <div class="design-case-slide">
          <div class="info">
            <router-link :to="{name: 'companyShow', params: {id: item.design_company.id}}" target="_blank">
              <img class="avatar" v-if="item.design_company.logo_url" :src="item.design_company.logo_url" width="100" />                     
              <img class="avatar" v-else src="../../../assets/images/avatar_100.png" width="100" />
            </router-link>
            <h3>{{ item.design_company.company_name }}</h3>
            <p><span>{{ item.design_company.province_value }}</span>&nbsp;&nbsp;&nbsp;<span>{{ item.design_company.city_value }}</span></p>
          </div>
          <div class="rate">
            <el-rate
              v-model="rateValue"
              disabled
              show-text
              text-color="#ff9900"
              text-template="{value}">
            </el-rate>
          </div>
          <div class="cate">
            <p class="c-title">设计类别</p>
            <p class="tag"><el-tag type="gray" v-for="(d, index) in item.design_company.design_type_val" :key="index">{{ d }}</el-tag></p>
          </div>
          <div class="cate">
            <p class="c-title">擅长领域</p>
            <p class="tag"><el-tag type="gray">家电维修</el-tag><el-tag type="gray">消费电子</el-tag><el-tag type="gray">设计</el-tag><el-tag type="gray">技术</el-tag></p>
          </div>

          <div class="prize" v-if="item.prize_val">
            <p>获得奖项: {{ item.prize_val }}</p>
          </div>
        
        </div>
      </el-col>

    </el-row>
    
  </div>
</template>

<script>
  import api from '@/api/api'
  import '@/assets/js/format'
  export default {
    name: 'design_case_show',
    data () {
      return {
        item: {},
        rateValue: 3.5,
        msg: 'This is About!!!'
      }
    },
    created: function() {
      var id = this.$route.params.id
      const that = this
      that.$http.get(api.designCaseId.format(id), {})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          that.item = response.data.data
          if (that.item.design_company.logo_image) {
            that.item.design_company.logo_url = that.item.design_company.logo_image.logo
          } else {
            that.item.design_company.logo_url = false
          }
          console.log(that.item)
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
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="stylus" scoped>
  .design-case-content {
    padding: 20px 20px 20px 20px;
    border: 1px solid #ccc;
  }
  .design-case-content .title {
    text-align: center;
    color: #5d6266;
    margin: 20px 0 20px 0;
    font-size: 2rem;
  }
  .design-case-content .summary {
    margin: 0 0 20px 0;
  }
  .design-case-content .summary p {
    line-height: 1.6;
    color: #5d6266;
  }
  .design-case-content .des {
  }
  .design-case-content .des p {
    line-height: 1.6;
    text-align: center;
  }
  .design-case-slide {
    padding: 20px 20px 20px 20px;
    border: 1px solid #ccc; 
  }
  .design-case-slide .info {
    margin: 10px;
    text-align: center;
  }
  .design-case-slide .info img {
    border-radius: 50%;
    overflow: hidden;
    vertical-align: middle;
    border: 1px solid;
  }
  .design-case-slide h3 {
    margin: 10px;
    font-size: 2rem;   
  }
  .design-case-slide .rate {
    padding: 10px;
    text-align: center;
    border-top: 1px solid rgba(224,224,224,.46);
  }
  .design-case-slide .cate {
    line-height: 2;
    margin-top: 20px;
  }
  .cate p {
    color: #666;
  }
  .cate p.c-title {
    text-align: center;
    font-size: 1.6rem;
    color: #333;
  }
  .cate p.tag span {
    margin: 5px;
  }
  .design-case-slide .prize {
    margin-top: 20px;
  }
  .edit-content {
    padding: 20px;
    overflow: hidden;
  }
  .edit-content img {
  }

</style>
