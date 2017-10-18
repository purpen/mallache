<template>
  <div class="container article-content" v-loading.fullscreen.lock="isFullLoading">
    <div class="blank20"></div>

    <!--
        <el-breadcrumb separator="/" class="bread">
          <el-breadcrumb-item :to="{ name: 'home' }">首页</el-breadcrumb-item>
          <el-breadcrumb-item :to="{ name: 'articleList' }">铟果说</el-breadcrumb-item>
          <el-breadcrumb-item>{{ item.title }}</el-breadcrumb-item>
        </el-breadcrumb>
        -->

    <div class="content">
      <div class="title">
        <p class="title-menu">
          <router-link :to="{name: 'articleList'}">铟果说</router-link> /
          <router-link :to="{name: 'articleList', query: {category_id: item.classification_id}}">{{ item.classification_value }}</router-link>
        </p>
        <h3>{{ item.title }}</h3>
        <p class="from" v-if="item.source_from">by {{ item.source_from }} &nbsp;&nbsp;&nbsp;{{ item.created_at }} </p>
        <p class="from" v-else>{{ item.created_at }} </p>

        <v-share :link="share.link" :title="share.title" :picUrl="share.picUrl"></v-share>
      </div>
      <div class="body markdown-body" v-html="item.content"></div>
    </div>

    <div class="tag-box">
      <a v-for="(d, index) in item.label" :key="index"><img src="../../../assets/images/icon/label.png" /> {{ d }}</a>
    </div>

  </div>
</template>

<script>
import api from '@/api/api'
import '@/assets/js/format'
import vShare from '@/components/block/Share'
// import 'mavon-editor'
import 'mavon-editor/dist/css/index.css'
export default {
  name: 'article_show',
  components: {
    vShare
  },
  data() {
    return {
      isFullLoading: false,
      item: '',
      share: {
        link: document.location,
        title: document.title.substring(0, 100),
        picUrl: '',
        test: ''
      },
      msg: ''
    }
  },
  mounted() {
  },
  created: function() {
    var id = this.$route.params.id
    if (!id) {
      that.$message.error('文章不存在！')
      that.$route.replace({ name: 'home' })
      return false
    }
    const that = this

    that.isFullLoading = true
    that.$http.get(api.article, { params: { id: id } })
      .then(function(response) {
        that.isFullLoading = false
        if (response.data.meta.status_code === 200) {
          that.item = response.data.data
          var markdownConfig = {
            html: true,        // Enable HTML tags in source
            xhtmlOut: true,        // Use '/' to close single tags (<br />).
            breaks: true,        // Convert '\n' in paragraphs into <br>
            langPrefix: 'language-markdown',  // CSS language prefix for fenced blocks. Can be
            linkify: false,        // 自动识别url
            typographer: true,
            quotes: '“”‘’',
            highlight: function(str, lang) {
              return '<pre class="hljs"><code class="' + lang + '">' + markdown.utils.escapeHtml(str) + '</code></pre>'
            }
          }

          var markdown = require('markdown-it')(markdownConfig)
          var container = require('markdown-it-container')
          markdown.use(container)
            .use(container, 'hljs-left') /* align left */
            .use(container, 'hljs-center') /* align center */
            .use(container, 'hljs-right') /* align right */

          that.item['content'] = markdown.render(that.item.content)
          that.item['created_at'] = that.item.created_at.date_format().format('yyyy年MM月dd日')
          console.log(that.item)

          that.share.title = that.item.title
          if (that.item.cover) {
            that.share.picUrl = that.item.cover.middle
          }
        } else {
          that.$message.error(response.data.meta.message)
          that.$route.replace({ name: 'home' })
        }
      })
      .catch(function(error) {
        that.$message.error(error.message)
        that.isFullLoading = false
      })
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="stylus" scoped>
.content {
}

.article-content {
  max-width: 800px;
  padding: 0 10px;
  margin: 0 auto;
}

.content .title {
  margin: 0 0 10px 0;
  text-align: center;
}

.content .title h3 {
  font-size: 2.4rem;
  line-height: 2;
  color: #222;
}

.content .title p.from {
  line-height: 40px;
  font-size: 1.6rem;
  color: #a8a8a8;
  height: 40px;
}

p.title-menu, p.title-menu a {
  color: #FF5A5F;
  font-size: 1.6rem;
  line-height: 2;
}

.tag-box {
  margin: 30px 0;
}

.tag-box a {
  font-size: 1.4rem;
  color: #C8C8C8;
  font-weight: 400;
  margin-right: 20px;
}

.tag-box a img {
  width: 2%;
  vertical-align: middle;
}
</style>
