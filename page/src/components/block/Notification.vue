<template>

  <div class="notification is-danger" v-if="show" :style="setStyle">
    <button v-if="!options.autoClose" class="delete" @click="close()"></button>
      {{ options.content }}
  </div>

</template>

<script>
import { SET_NOTIFICATIONS } from '@/store/mutation-types'
export default {
  data () {
    return {

    }
  },
  props: {
    options: {
      type: Object,
      default: () => {
        return {}
      }
    },
    show: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    setStyle () {
      return {
        color: this.options.textColor || '#fff',
        background: this.options.backgroundColor || '#21e7b6'
      }
    },
    setTime () {
      return {
        transition: `all ${(this.options.showTime / 1000) || 3}s linear`,
        background: this.options.barColor || '#03D6D2'
      }
    }
  },
  methods: {
    countdown () {
      if (this.options.autoClose) {
        if (this.options.countdownBar) {
          setTimeout(() => {
            this.barControl = 'count-leave'
          }, 10)
        }
        const t = setTimeout(() => {
          const options = {}
          this.$store.commit(SET_NOTIFICATIONS, options)
        }, this.options.showTime || 3000)
        this.timers.push(t)
      }
    },
    close () {
      const options = {}
      this.$store.commit(SET_NOTIFICATIONS, options)
    }
  },
  watch: {
    options () {
      this.barControl = ''
      this.timers.forEach((timer) => {
        window.clearTimeout(timer)
      })
      this.timers = []
      this.countdown()
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
