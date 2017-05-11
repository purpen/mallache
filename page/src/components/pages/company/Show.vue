<template>
  <div class="container">

    <el-row :gutter="24">

      <el-col :span="6">
        <div class="design-case-slide">
          <div class="info">
            <img class="avatar" v-if="item.logo_url" :src="item.logo_url" width="100" />                     
            <img class="avatar" v-else src="../../../assets/images/avatar_default.jpg" width="100" />
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
            <p class="tag"><el-tag type="gray">产品设计</el-tag><el-tag type="gray">消费电子</el-tag><el-tag type="gray">日用消费</el-tag><el-tag type="gray">这是一个长标签</el-tag></p>
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
            <p>网址: <a :href="item.web" target="_blank">{{ item.web }}</a></p>
          </div>
        
        </div>
      </el-col>

      <el-col :span="18">
        <div class="design-case-content">
          <div class="">
            <h2>作品案例</h2>

            <div class="design-case-list">
              <el-row :gutter="24">
                <el-col
                  v-for="(d, index) in designCases"
                  :key="index"
                  :span="8">
                  <div class="item">
                    <div class="img">
                      <img v-if="hasImg(d.first_image)" :src="d.first_image[0]['small']" />
                      <img v-else src="https://p4.taihuoniao.com/topic/170302/58b81d1020de8dfc6e8bd658-2-p325x200.jpg" />
                    </div>
                    <div class="content">
                      <router-link :to="{name: 'vcenterDesignCaseShow', params: {id: d.id}}" target="_blank">
                        {{ d.title }}
                      </router-link>
                    </div>
                  </div>
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
    data () {
      return {
        item: {},
        designCases: [],
        rateValue: 3.5,
        msg: ''
      }
    },
    methods: {
      hasImg(d) {
        if (d.length === 0) {
          return false
        } else {
          return true
        }
      }
    },
    created: function() {
      var id = this.$route.params.id
      const self = this
      self.$http.get(api.designCompanyId.format(id), {})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          self.item = response.data.data
          if (self.item.logo_image && self.item.logo_image.length !== 0) {
            self.item.logo_url = self.item.logo_image.logo
          } else {
            self.item.logo_url = false
          }
          console.log(self.item)

          self.$http.get(api.designCase, {})
          .then (function(response) {
            if (response.data.meta.status_code === 200) {
              self.designCases = response.data.data
            }
          })
          .catch (function(error) {
            self.$message.error(error.message)
            console.log(error.message)
          })
        }
      })
      .catch (function(error) {
        self.$message.error(error.message)
        console.log(error.message)
        return false
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

  .design-case-list {
  
  }
  .design-case-list .item {
    height: 250px;
  }
  .design-case-list .img {
    height: 210px;
    overflow: hidden;
  }

  .design-case-list .img:hover {
    position:relative;
    background-color:#fff;filter:Alpha(Opacity=90);opacity:0.9;
  }

  .design-case-list .item img {
    width: 100%;
  }
  .design-case-list .content {
    padding: 5px 5px 5px 5px;
  }
  .design-case-list .content a {
    font-size: 1.5rem;
  }

</style>
