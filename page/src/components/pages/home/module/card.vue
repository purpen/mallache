<template>
  <el-card :body-style="{ padding: '0px' }">
    <router-link :to="{name: link, params: {id: d.id}}"
                :target="isMob ? '_self' : '_blank'">
      <div class="image-box">
        <img v-lazy="d.cover.middle">
      </div>
      <div class="content">
        <p class="title">{{ d.title }}<p>
        <div class="des">
          <p>{{ d.short_content }}</p>
        </div>
      </div>
    </router-link>
  </el-card>
</template>
<script>
export default {
  name: 'card',
  data() {
    return {}
  },
  props: {
    content: {
      default: []
    },
    link: {
      default: ''
    },
    showPage: {
      default: true
    },
    pagination: {
      default: {}
    }
  }
}
</script>


<style>
  .content {
    padding: 15px 15px 25px;
  }

  .content .title {
    padding: 0;
    line-height: 2;
    color: #222;
    font-size: 1.6rem;
    overflow: hidden;
    text-overflow:ellipsis;
    white-space: nowrap;
  }

  .content .des {
    font-size: 1.4rem;
    color: #666;
    line-height: 1.5;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
  }

  .image-box img {
    width: 100%;
    height: 100%;
  }
</style>
