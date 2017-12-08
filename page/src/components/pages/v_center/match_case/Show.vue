<template>
  <div class="container" v-loading.fullscreen.lock="isFullLoading">
    <div class="blank20"></div>
    <el-row :gutter="20" class="anli-elrow">

      <el-col :xs="24" :sm="18" :md="18" :lg="18">
        <div class="design-case-content edit-content">
          <div class="title">
            <h1>{{ item.title }}</h1>
          </div>
          <div class="summary">
            <p>{{ item.profile }}</p>
          </div>
          <div class="des">
            <p v-for="(d, index) in item.case_image" :key="index">
              <img :src="d.big" :alt="d.name" :title="d.name" />
              <slot>
            <p class="img-des">{{ d.summary }}</p>
            </slot>
            </p>
          </div>
        </div>
      </el-col>

      <el-col :xs="24" :sm="6" :md="6" :lg="6">
        <div class="design-case-slide">
          <div class="info">
            <router-link :to="{name: 'companyShow', params: {id: item.company_id}}" target="_blank">
              <img class="avatar" v-if="item.company.logo_url" :src="item.company.logo_url" width="100" />
              <img class="avatar" v-else :src="require('assets/images/avatar_100.png')" width="100" />
            </router-link>
            <h3>{{ item.company.company_abbreviation }}</h3>
            <p class="com-addr">
              <span>{{ item.company.province_value }}</span>&nbsp;&nbsp;&nbsp;
              <span>{{ item.company.city_value }}</span>
            </p>
          </div>
          <div class="rate">
            <p>信用指数：
              <span>{{ item.company.score }}分</span>
            </p>
          </div>
          <div class="cate" v-if="item.design_type_val">
            <p class="c-title">类别</p>
            <p class="tag">
              <span>{{ item.design_type_val }}</span>
            </p>
          </div>

          <div class="cate" v-if="item.field_val">
            <p class="c-title">领域</p>
            <p class="tag">
              <span>{{ item.field_val }}</span>
            </p>
          </div>

          <div class="cate" v-if="item.industry_val">
            <p class="c-title">行业</p>
            <p class="tag">
              <span>{{ item.industry_val }}</span>
            </p>
          </div>

          <div class="cate" v-if="item.prize_val">
            <p class="c-title">获得奖项</p>
            <p class="tag">
              <span>{{ item.prize_val }}</span>
            </p>
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
    name: 'match_case_show',
    data() {
      return {
        isFullLoading: false,
        item: {
          company: ''
        },
        rateValue: 3.5,
        msg: 'This is About!!!'
      }
    },
    created: function () {
      let id = this.$route.params.id
      const that = this
      that.isFullLoading = true
      that.$http.get (api.workid.format (id), {})
        .then (function (response) {
          console.log (response)
          that.isFullLoading = false
          if (response.data.meta.status_code === 200) {
            that.item = response.data.data
            if (that.item.company.logo_image) {
              that.item.company.logo_url = that.item.company.logo_image.logo
            } else {
              that.item.company.logo_url = false
            }
            document.title = that.item.title + '-铟果'
          }
        })
        .catch (function (error) {
          that.$message.error (error.message)
          that.isFullLoading = false
        })
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="stylus" scoped>
  .design-case-content {
    padding: 20px;
    }

  .design-case-content .title {
    text-align: center;
    color: #222222;
    margin: 20px 0;
    font-size: 2rem;
    }

  .design-case-content .summary {
    margin: 0 0 20px 0;
    }

  .design-case-content .summary p {
    line-height: 1.6;
    color: #666666;
    }

  .design-case-content .des {
    }

  .design-case-content .des p {
    line-height: 1.6;
    text-align: center;
    }

  .design-case-slide {
    padding: 20px;
    color: #222222;
    }

  .design-case-slide .info {
    margin: 10px;
    margin-bottom: 0;
    text-align: center;
    }

  .design-case-slide .info img {
    border-radius: 50%;
    overflow: hidden;
    vertical-align: middle;
    border: 1px solid #C8C8C8;
    }

  .design-case-slide h3 {
    margin-top: 10px;
    font-size: 1.8rem;
    line-height: 2.8rem;
    }

  .design-case-slide .com-addr {
    line-height: 2.8rem;
    }

  .design-case-slide .rate {
    padding: 10px;
    text-align: center;
    }

  .design-case-slide .cate {
    margin-top: 20px;
    }

  .cate p {
    color: #666666;
    }

  .cate p.c-title {
    text-align: left;
    font-size: 1.6rem;
    font-weight: 500;
    color: #222222;
    margin-bottom: 5px;
    }

  .cate p.tag span {
    color: #666666;
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

  p.img-des {
    margin-bottom: 20px;
    }

  @media screen and (max-width: 767px) {
    .cate p {
      text-align: center;
      }

    .cate p.c-title {
      text-align: center;
      }
    }
</style>
