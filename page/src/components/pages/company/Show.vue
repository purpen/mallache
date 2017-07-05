<template>
  <div class="container" v-loading.fullscreen.lock="isFullLoading">
    <div class="blank20"></div>
    <el-row :gutter="10">

      <el-col :span="6">
        <div class="design-case-slide">
          <div class="info">
            <img class="avatar" v-if="item.logo_url" :src="item.logo_url" width="100" />                     
            <img class="avatar" v-else src="../../../assets/images/avatar_100.png" width="100" />
            <h3>{{ item.company_name }}</h3>
            <p><span>{{ item.province_value }}</span>&nbsp;&nbsp;&nbsp;<span>{{ item.city_value }}</span></p>
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
            <p class="tag"><el-tag type="gray" v-for="(d, index) in item.design_type_val" :key="index">{{ d }}</el-tag></p>
          </div>
          <div class="cate">
            <p class="c-title">擅长领域</p>
            <p class="tag"><el-tag type="gray">家电维修</el-tag><el-tag type="gray">消费电子</el-tag><el-tag type="gray">设计</el-tag><el-tag type="gray">技术</el-tag></p>
          </div>
          <div class="cate">
            <p class="c-title">联系方式</p>
            <p>地址: {{ item.address }}</p>
            <p>联系人: {{ item.contact_name }}</p>
            <p>电话: {{ item.phone }}</p>
            <p>邮箱: {{ item.email }}</p>
            <p class="web">网址: <a :href="item.web" target="_blank">{{ item.web }}</a></p>
          </div>
        
        </div>
      </el-col>

      <el-col :span="18">
        <div class="design-case-content">
          <div class="">
            <h2>作品案例</h2>

            <div class="design-case-list" v-loading.body="isLoading">

              <el-row :gutter="10">

                <el-col :span="8" v-for="(d, index) in designCases" :key="index">
                  <el-card :body-style="{ padding: '0px' }" class="item">
                    <div class="image-box">
                      <router-link :to="{name: 'vcenterDesignCaseShow', params: {id: d.id}}" target="_blank">
                        <img :src="d.cover.middle">
                      </router-link>
                    </div>
                    <div class="content">
                      <router-link :to="{name: 'vcenterDesignCaseShow', params: {id: d.id}}" target="_blank">{{ d.title }}</router-link>
                    </div>
                  </el-card>
                </el-col>

              </el-row>

            </div>

          </div>
          <div class="summary">
            <h2>公司简介</h2>
            <p>{{ item.company_profile }}</p>
          </div>
          <div class="summary">
            <h2>专业优势</h2>
            <p>{{ item.professional_advantage }}</p>
          </div>
          <div class="summary">
            <h2>荣誉奖项</h2>
            <p>{{ item.awards }}</p>
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
    name: 'company_show',
    isLoading: false,
    data () {
      return {
        item: {},
        isLoading: false,
        isFullLoading: false,
        designCases: [],
        rateValue: 3.5,
        msg: ''
      }
    },
    methods: {
      hasImg(d) {
        if (!d) {
          return false
        } else {
          return true
        }
      }
    },
    created: function() {
      var id = this.$route.params.id
      const self = this
      self.isFullLoading = true
      self.$http.get(api.designCompanyId.format(id), {})
      .then (function(response) {
        self.isFullLoading = false
        if (response.data.meta.status_code === 200) {
          self.item = response.data.data
          if (self.item.logo_image) {
            self.item.logo_url = self.item.logo_image.logo
          } else {
            self.item.logo_url = false
          }

          self.isLoading = true
          self.$http.get(api.designCaseCompanyId.format(id), {})
          .then (function(response) {
            self.isLoading = false
            if (response.data.meta.status_code === 200) {
              self.designCases = response.data.data
            }
          })
          .catch (function(error) {
            self.isLoading = false
            self.$message.error(error.message)
          })
        }
      })
      .catch (function(error) {
        self.isFullLoading = false
        self.$message.error(error.message)
      })
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="stylus" scoped>
  .design-case-content {
    padding: 20px 40px 20px 40px;
    border: 1px solid #ccc;
  }
  .design-case-content .title {
    text-align: center;
    color: #5d6266;
    margin: 20px 0 20px 0;
    font-size: 2rem;
  }
  .design-case-content h2 {
    text-align: center;
    font-size: 1.8rem;
    color: #333;
    margin: 20px;
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
    text-align: left;
    font-size: 1.6rem;
    color: #333;
  }
  .cate p.tag span {
    margin: 5px;
  }
  .design-case-slide .prize {
    margin-top: 20px;
  }

  p.web {
    word-wrap: break-word;
  }


  .design-case-list {
    min-height: 350px;
  }
  .design-case-list .item {
    height: 190px;
  }

  .item {
    margin: 5px 0;
  }

  .item img {
    width: 100%;
  }

  .image-box {
    height: 150px;
    overflow: hidden;
  }

  .content {
    padding: 10px;
  }
  .content a {
    font-size: 1.5rem;
  }

</style>
