<template>
  <div class="container">

  </div>
</template>

<script>
import api from '@/api/api'
import { mapActions } from 'vuex'
import { ADD_TOAST_MESSAGE } from 'vuex-toast'
import auth from '@/helper/auth'

export default {
  name: 'login',

  data() {
    return {
    }
  },
  logout: {
    validate() {
      const that = this
      // 退出登录
      that.$http.post(api.logout, {})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          auth.remove_token()
          auth.remove_user()
          that.$router.push('/home')
        } else {
          that.addToast({
            text: response.data.meta.message,
            type: 'danger',
            dismissAfter: 3000
          })
        }
      })
      .catch (function(error) {
        that.addToast({
          text: error.message,
          type: 'danger',
          dismissAfter: 3000
        })
        console.log(error.message)
        return false
      })
    },
    ...mapActions({
      addToast: ADD_TOAST_MESSAGE
    })

  },
  computed: {

  }

}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>

