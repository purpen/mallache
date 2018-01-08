<template>
  <div class="container article-content" v-loading.fullscreen.lock="isFullLoading">
    <div class="blank20"></div>
    <div class="content" v-if="item">
      <div class="title">
        <h3>{{ item.title }}</h3>
        <p class="from">
          <span class="awards">{{item.category_value}}</span>
          <!-- <span class="category">国际大赛</span> -->
          <span class="awards-date" v-if="item.created_at">{{item.created_at}}</span>
        </p>
        <p v-if="item.tags.length" class="tigs">
          标签:
          <span v-for="(ele,index) in item.tags" :key="index">{{ele}}</span>
        </p>
      </div>
      <div class="body markdown-body" v-html="item.content"></div>
    </div>
  </div>
</template>

<script>
import api from '@/api/api'
import '@/assets/js/format'
// import 'mavon-editor'
import 'mavon-editor/dist/css/index.css'
export default {
  name: 'article_show',
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
  mounted() {},
  created: function() {
    let id = this.$route.params.id
    if (!id) {
      that.$message.error('案例不存在！')
      that.$router.replace({ name: 'designAwardsList' })
      return false
    }
    const that = this

    that.isFullLoading = true
    that.$http
      .get(api.awardCase, { params: { id: id } })
      .then(function(response) {
        that.isFullLoading = false
        if (response.data.meta.status_code === 200) {
          that.item = response.data.data
          let markdownConfig = {
            html: true, // Enable HTML tags in source
            xhtmlOut: true, // Use '/' to close single tags (<br />).
            breaks: true, // Convert '\n' in paragraphs into <br>
            langPrefix: 'language-markdown', // CSS language prefix for fenced blocks. Can be
            linkify: false, // 自动识别url
            typographer: true,
            quotes: '“”‘’',
            highlight: function(str, lang) {
              return (
                '<pre class="hljs"><code class="' +
                lang +
                '">' +
                markdown.utils.escapeHtml(str) +
                '</code></pre>'
              )
            }
          }

          let markdown = require('markdown-it')(markdownConfig)
          let container = require('markdown-it-container')
          markdown
            .use(container)
            .use(container, 'hljs-left') /* align left */
            .use(container, 'hljs-center') /* align center */
            .use(container, 'hljs-right')
          /* align right */

          that.item['content'] = markdown.render(that.item.content)
          if (that.item['created_at']) {
            that.item['created_at'] = that.item.created_at
              .date_format()
              .format('yyyy年MM月dd日')
          } else {
            that.item['created_at'] = ''
          }
          // 添加标题
          document.title = that.item.title + '-铟果'
          console.log(that.item['created_at'])
          that.share.title = that.item.title
          if (that.item.cover) {
            that.share.picUrl = that.item.cover.middle
          }
        } else {
          that.$message.error(response.data.meta.message)
          that.$router.replace({ name: 'designAwardsList' })
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
<style scoped>
.article-content {
  max-width: 880px;
  padding: 0 15px;
  margin: 0 auto;
}

.content .title {
  margin: 0 0 10px 0;
  text-align: center;
}

.content .title h3 {
  padding: 26px 0 16px;
  font-size: 2.4rem;
  line-height: 1;
  color: #222;
  font-weight: 600;
}

.content .title p.from {
  line-height: 20px;
  font-size: 1.6rem;
  color: #999999;
  height: 36px;
  text-align: center;
  padding-bottom: 16px;
}

p.from span {
  padding-left: 30px;
  margin-left: 8px;
}

.awards {
  background: url("../../../assets/images/tools/calendar/Contest@2x.png") no-repeat 0 1px;
  background-size: 18px;
}

.category {
  background: url("../../../assets/images/tools/calendar/Grade@2x.png") no-repeat 0;
  background-size: 20px;
}

.awards-date {
  background: url("../../../assets/images/tools/calendar/Winning@2x.png") no-repeat 0;
  background-size: 20px;
}

p.tigs {
  font-size: 14px;
  color: #999999
}
p.tigs span {
  padding-left: 30px;
  color: #C8C8C8;
  margin-left: 5px;
  background: url("../../../assets/images/tools/calendar/Fill@2x.png") no-repeat 3px;
  background-size: 15px;
}
p.title-menu,
p.title-menu a {
  color: #ff5a5f;
  font-size: 1.6rem;
  line-height: 2;
}

.tag-box {
  margin: 30px 0;
}

.tag-box a {
  font-size: 1.4rem;
  color: #c8c8c8;
  font-weight: 400;
  margin-right: 20px;
}

.tag-box a img {
  width: 2%;
  vertical-align: middle;
}

.markdown-body {
  color: #666;
  line-height: 28px;
}
</style>
