<template>
  <div class="container">

    <el-row :gutter="24">
      <v-menu selectedName="userList"></v-menu>

      <el-col :span="20">
        <div class="content">
          <h1>这是用户列表</h1>
        </div>
      </el-col>
    </el-row>


  </div>
</template>

<script>
import api from '@/api/api'
import vMenu from '@/components/admin/Menu'
export default {
  name: 'admin_item_list',
  components: {
    vMenu
  },
  data () {
    return {
      itemList: [],
      tableData: [],
      msg: ''
    }
  },
  methods: {
  },
  created: function() {
    const self = this
    var page = this.$route.query.page || 1
    var perPage = 5
    self.$http.get(api.adminItemList, {page: page, per_page: perPage})
    .then (function(response) {
      if (response.data.meta.status_code === 200) {
        self.itemList = response.data.data

        for (var i = 0; i < self.itemList.length; i++) {
          var item = self.itemList[i]

          var typeLabel = ''
          if (item.item.type === 1) {
            typeLabel = item.item.type_value + '/' + item.item.design_type_value + '/' + item.item.field_value + '/' + item.item.industry_value
          } else {
            typeLabel = item.item.type_value + '/' + item.item.design_type_value
          }

          item['item']['type_label'] = typeLabel
          item['item']['status_label'] = '[{0}]{1}'.format(item.item.status, item.item.status_value)

          if (item.info) {
            item['info']['locale'] = '{0}/{1}'.format(item.info.province_value, item.info.city_value)
          }

          self.tableData.push(item)
        } // endfor

        console.log(self.itemList)
      }
    })
    .catch (function(error) {
      self.$message.error(error.message)
      console.log(error.message)
    })
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
