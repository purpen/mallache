<template>

    <!--
    <div class="region-picker">
      <span class="province select">
        <slot name="province"></slot>
        <select class="province-select" name="province" :value="provinceSelected" @change="change('provinceSelected', $event.target.value)" :disabled="disabled">
          <option value="" v-text="placeholder.province"></option>
          <option v-for="item in provinces" :value="item" v-text="item[1]"></option>
        </select>
      </span>
      <span class="select city" v-show="!auto || cities.length">
        <slot name="city"></slot>
        <select class="city-select" name="city" :value="citySelected" @change="change('citySelected', $event.target.value)" :disabled="disabled">
          <option value="" v-text="placeholder.city"></option>
          <option v-for="item in cities" :value="item" v-text="item[1]"></option>
        </select>
      </span>
      <span class="select district" v-if="!twoSelect" v-show="!auto || districts.length">
        <slot name="district"></slot>
        <select class="district-select" name="district" :value="districtSelected" @change="change('districtSelected', $event.target.value)" :disabled="disabled">
          <option value="" v-text="placeholder.district"></option>
          <option v-for="item in districts" :value="item" v-text="item[1]"></option>
        </select>
      </span>
    </div>
    -->

    <el-form-item label="选择城市" prop="chinaCity">
        <el-select v-model="province" name="province" :value="provinceSelected" @change="change('provinceSelected', $event.target.value)" placeholder="请选择">
          <el-option
            v-for="item in provinces"
            :label="item[1]"
            :key="item.index"
            :value="item">
          </el-option>
        </el-select>
        <el-select v-model="city" name="city" :value="citySelected" @change="change('citySelected', $event.target.value)" placeholder="请选择">
          <el-option
            v-for="item in cities"
            :label="item[1]"
            :key="item.index"
            :value="item">
          </el-option>
        </el-select>
        <el-select v-model="district" name="district" :value="districtSelected" @change="change('districtSelected', $event.target.value)" placeholder="请选择">
          <el-option
            v-for="item in districts"
            :label="item[1]"
            :key="item.index"
            :value="item">
          </el-option>
        </el-select>
    </el-form-item>



</template>

<script>
  // 城市数据库
  import REGION_DATA from 'china-area-data'

  export default {
    name: 'RegionPicker',
    vueVersion: 2,
    region: REGION_DATA,
    props: {
      province: {},
      city: {},
      district: {},
      twoSelect: Boolean,
      auto: Boolean,
      completed: Boolean,
      required: Boolean,
      disabled: Boolean,
      rootCode: {
        default: '86'
      },
      placeholder: {
        type: Object,
        default () {
          return {
            province: '请选择',
            city: '请选择',
            district: '请选择'
          }
        }
      }
    },

    methods: {
      change (field, value) {
        this[field] = value.split(',')
        if (this.completed) {
          this.$emit('onchange', {
            province: this.provinceSelected,
            city: this.citySelected,
            district: this.districtSelected
          })
        } else {
          this.$emit('onchange', {
            province: this.provinceSelected[1],
            city: this.citySelected[1],
            district: this.districtSelected[1]
          })
        }
      },
      _filter (pid) {
        const result = []
        const items = this.$options.region[pid]
        for (let code in items) {
          result.push([parseInt(code, 10), items[code]])
        }
        return result
      },
      // data model: [code, name]
      _searchIndex (items, model, by) {
        if (!model) return -1
        // by name
        if (by === 1) {
          for (let key in items) {
            if (items[key][by].indexOf(model) > -1) {
              return key
            }
          }
          // by code
        } else {
          for (let key in items) {
            if (items[key][by] === model) {
              return key
            }
          }
        }
      },
      _selected (pid, model) {
        const items = this._filter(pid)
        let index = -1
        if (typeof model === 'string') {
          index = this._searchIndex(items, model, 1)
        } else if (typeof model === 'number') {
          index = this._searchIndex(items, Number(model), 0)
        } else if (Array.isArray(model)) {
          index = this._searchIndex(items, Number(model[0]), 0)
        }
        return items[index] || []
      }
    },

    computed: {
      provinces () {
        return this._filter(this.rootCode)
      },
      cities () {
        return this._filter(this.provinceSelected[0])
      },
      districts () {
        return this._filter(this.citySelected[0])
      },
      isVueNext () {
        return this.$options.vueVersion !== 1
      },
      provinceSelected: {
        get () {
          // alert('province:' + this.province)
          return this._selected(this.rootCode, this.current.province || this.province)
        },
        set (value) {
          if (!this.isVueNext) {
            this.province = this.completed ? value : value[1]
          } else {
            this.current.province = value
          }
        }
      },
      citySelected: {
        get () {
          alert('city:' + this.city)
          return this._selected(this.provinceSelected[0], this.current.city || this.city)
        },
        set (value) {
          if (!this.isVueNext) {
            this.city = this.completed ? value : value[1]
          } else {
            this.current.city = value
          }
        }
      },
      districtSelected: {
        get () {
          return this._selected(this.citySelected[0], this.current.district || this.district)
        },
        set (value) {
          if (!this.isVueNext) {
            this.district = this.completed ? value : value[1]
          } else {
            this.current.district = value
          }
        }
      }
    },

    data () {
      return {
        current: {
          province: '',
          city: '',
          district: ''
        }
      }
    },

    watch: {
      province () {
        this.current.province = ''
      },
      city () {
        this.current.city = ''
      },
      district () {
        this.current.district = ''
      }
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
