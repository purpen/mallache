<template>
  <div class="container">
    <div class="blank20"></div>
    <div class="case-list" v-loading.body="isLoading">
      <el-row :gutter="20">

        <el-col :span="8" v-for="(d, index) in itemList" :key="index">
          <el-card :body-style="{ padding: '0px' }" class="item">
            <div class="image-box">
              <router-link :to="{name: 'vcenterDesignCaseShow', params: {id: d.id}}" target="_blank">
                <img :src="d.cover.middle">
              </router-link>
            </div>
            <div class="content">
              <router-link :to="{name: 'vcenterDesignCaseShow', params: {id: d.id}}" target="_blank">{{ d.title }}</router-link>
              <div class="des">
                <p>{{ d.profile }}</p>
              </div>
            </div>
          </el-card>
        </el-col>

      </el-row>
    
    </div>
    <div class="blank20"></div>
  </div>
</template>

<script>
import api from '@/api/api'
export default {
  name: 'design_case_list',
  data () {
    return {
      itemList: [],
      isLoading: false,
      test: ''
    }
  },
  created: function() {
    const self = this
    self.isLoading = true
    self.$http.get(api.designCaseOpenLists, {params: {page: 1, per_page: 9, sort: 2}})
    .then (function(response) {
      self.isLoading = false
      if (response.data.meta.status_code === 200) {
        self.itemList = response.data.data
        console.log(self.itemList)
        for (var i = 0; i < self.itemList.length; i++) {
        } // endfor
      }
    })
    .catch (function(error) {
      self.isLoading = false
      self.$message.error(error.message)
    })
  }
}

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .container{
  }
  .container h3 {
    font-size: 2rem;
    margin-bottom: 10px;
  }
  .case-list {
    min-height: 350px;
  }

  .item {
    height: 300px;
    margin: 10px 0;
  }

  .item img {
    width: 100%;
  }

  .image-box {
    height: 220px;
    overflow: hidden;
  }

  .content {
    padding: 10px;
  }
  .content a {
    font-size: 1.5rem;
  }

  .des {
    height: 30px;
    margin: 10px 0;
    overflow: hidden;
  }

  .des p {
    color: #666;
    font-size: 1.2rem;
    line-height: 1.3;
    text-overflow: ellipsis;
  }

</style>
