<template>
  <div class="container">
    <div class="nav-list">
      <div class="category-list" ref="categoryList">
        <router-link :to="{name: 'articleList'}" v-if="cateList.length">最新</router-link>
        <router-link :to="{name: 'articleList', query: {category_id: d.id}}" v-for="(d, index) in cateList" :key="index">{{ d.name }}</router-link>
        <router-link :to="{name: 'subjectList'}" class="active" v-if="cateList.length">专题</router-link>
      </div>
    </div>

    <div class="case-list" v-loading.body="isLoading">
      <el-row :gutter="20" class="anli-elrow">

        <el-col :xs="24" :sm="12" :md="12" :lg="12" v-for="(d, index) in itemList" :key="index">
          <el-card :body-style="{ padding: '0px' }" class="item">
            <div class="image-box">
              <a :href="d.url" target="_blank">
                <img :src="d.cover_url">
              </a>
            </div>
            <div class="content">
              <p class="title">
                <a :href="d.url">{{ d.title }}</a>
              </p>
              <div class="des">
                <p>{{ d.content }}</p>
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
  name: 'article_list',
  data() {
    return {
      cateList: [],
      itemList: [
        {
          id: 1,
          title: '传统产业设计再制造',
          cover_url: require('@/assets/images/subject/list_01.jpg'),
          url: '/subject/zj',
          content: '浙江“传统产业设计再造”计划由浙江省工业设计协会联合相关高校、梦栖工业设计小镇和全省17个省级工业梦栖工业设计小镇和全省17个省级工业全省17个省级工工业梦栖...'
        },
        {
          id: 4,
          title: 'RCIP衍生创新峰会',
          cover_url: require('@/assets/images/subject/list_02.jpg'),
          url: '/subject/rcip',
          content: '这是一场IP生态集群的权威路演；也是众多创新设计机构以IP内容结合的发布盛典想要了解更多？'
        }
      ],
      category_id: 0,
      isLoading: false,
      query: {
        page: 1,
        pageSize: 3,
        totalCount: 0,
        sort: 1,
        type: 0,
        category_id: 0,

        test: null
      },
      test: ''
    }
  },
  methods: {
  },
  created: function() {
    const self = this
    // 分类列表
    self.$http.get(api.categoryList, { params: { page: 1, per_page: 4, type: 1, sort: 1 } })
      .then(function(response) {
        if (response.data.meta.status_code === 200) {
          self.cateList = response.data.data
        }
      })
      .catch(function(error) {
        self.$message.error(error.message)
      })
  },
  mounted() {
  }
}

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.container h3 {
  font-size: 2rem;
  margin-bottom: 10px;
}

.category-list {
  padding-bottom: 10px;
  margin: 30px auto 10px auto;
  text-align: center;
  min-width: 100%;
  white-space: nowrap;
  overflow-x: auto;
}

.category-list a {
  font-size: 1.6rem;
  margin-right: 40px;
  color: #666;
}

.category-list a:hover,
.category-list a.active {
  color: #FF5A5F;
}

.case-list {
  min-height: 350px;
}

.item {
  /* height: 460px; */
  margin: 10px 0;
}

.item img {
  width: 100%;
}

.image-box {
  /* height: 350px; */
  overflow: hidden;
}

.content {
  padding: 20px;
}

.content p.title {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.content a {
  color: #222;
  font-size: 1.8rem;
}

.des {
  height: 40px;
  margin: 10px 0;
  overflow: hidden;
}

.des p {
  color: #666;
  font-size: 1.4rem;
  line-height: 1.5;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
}

@media screen and ( max-width:480px) {
  .nav-list {
    margin-top: 16px;
    height: 18px;
    overflow: hidden;
  }
  .category-list {
    margin: 0 0 16px 0;
    padding: 2px 0 18px 16px;
    white-space: nowrap;
    overflow-x: auto;
  }
  .category-list a {
    margin-right: 0;
    margin-right: 30px;
  }
  .content {
    padding: 15px;
  }
}
</style>
