<template>
  <div>
    <article><!-- Your contents --></article>
    <toast position="ne"></toast>
  </div>
</template>

<script>
import { Toast } from 'vuex-toast'

export default {
  // ...
  components: {
    Toast
  }
  // ...
}
</script>
