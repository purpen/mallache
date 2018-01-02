<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <v-menu selectedName="userList"></v-menu>

      <el-col :span="20">
        <div class="content">

        <div class="admin-menu-sub">
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminUserList'}" active-class="false" :class="{'item': true, 'is-active': menuType == 0}">全部</router-link>
          </div>
        </div>

          <div class="content-box">
            <div class="form-title">
              <span>{{ itemMode }}</span>
            </div>
            <el-form label-position="top" :model="form" :rules="ruleForm" ref="ruleForm" label-width="80px">


              <el-form-item label="用户属性" prop="kind">
                <el-radio-group v-model.number="form.kind">
                  <el-radio-button
                    v-for="item in kindOptions"
                    :key="item.index"
                    :label="item.value">{{ item.label }}</el-radio-button>
                </el-radio-group>
              </el-form-item>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="真实姓名" prop="realname">
                    <el-input v-model="form.realname" placeholder=""></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item label="职位" prop="position">
                    <el-input v-model="form.position" placeholder=""></el-input>
                  </el-form-item>
                </el-col>
              </el-row>


              <div class="form-btn">
                  <el-button @click="returnList">取消</el-button>
                  <el-button type="success" :loading="isLoadingBtn" @click="submit('ruleForm')">提交</el-button>
              </div>
              <div class="clear"></div>
            </el-form>

          </div>
        </div>
      </el-col>
    </el-row>


  </div>
</template>

<script>
import api from '@/api/api'
import vMenu from '@/components/admin/Menu'
import typeData from '@/config'
export default {
  name: 'admin_user_submit',
  components: {
    vMenu
  },
  data () {
    return {
      menuType: 0,
      itemMode: '编辑用户信息',
      isLoading: false,
      isLoadingBtn: false,
      form: {
        kind: '',
        realname: '',
        position: '',
        url: ''
      },
      ruleForm: {
        kind: [
          { type: 'number', message: '请选择属性', trigger: 'change' }
        ]
      },
      // 上一页信息
      beforeRoute: {
        name: null,
        query: {}
      },
      msg: ''
    }
  },
  methods: {
    submit(formName) {
      const that = this
      that.$refs[formName].validate((valid) => {
        // 验证通过，提交
        if (valid) {
          var row = {
            kind: that.form.kind,
            realname: that.form.realname,
            position: that.form.position
          }
          var method
          if (that.itemId) {
            method = 'post'
            row.id = that.itemId
          } else {
            that.$message.error('用户ID不存在')
          }

          that.isLoadingBtn = true
          that.$http({method: method, url: api.adminUserEdit, data: row})
          .then (function(response) {
            if (response.data.meta.status_code === 200) {
              that.$message.success('提交成功！')
              // 跳转到上一页
              if (that.beforeRoute.name) {
                that.$router.push({name: that.beforeRoute.name, query: that.beforeRoute.query})
              } else {
                that.$router.push({name: 'adminUserList'})
              }
              return false
            } else {
              that.$message.error(response.data.meta.message)
              that.isLoadingBtn = false
            }
          })
          .catch (function(error) {
            that.$message.error(error.message)
            that.isLoadingBtn = false
            return false
          })
          return false
        } else {
          console.log('error submit!!')
          return false
        }
      })
    },
    returnList() {
      this.$router.push({name: 'adminUserList'})
    }
  },
  computed: {
    kindOptions() {
      var items = []
      for (var i = 0; i < typeData.USER_KIND.length; i++) {
        var item = {
          value: typeData.USER_KIND[i]['id'],
          label: typeData.USER_KIND[i]['name']
        }
        items.push(item)
      }
      return items
    }
  },
  created: function() {
    const that = this
    var id = this.$route.query.id
    if (id) {
      that.itemMode = '编辑用户'
      that.itemId = id
      that.$http.get(api.adminUser, {params: {id: id}})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          that.form = response.data.data
        }
      })
      .catch (function(error) {
        that.$message.error(error.message)
        return false
      })
    } else {
      that.itemId = null
    }
  },
  watch: {
    '$route' (to, from) {
      // 对路由变化作出响应...
    }
  },
  // 页面进入前获取路由信息
  beforeRouteEnter (to, from, next) {
    // 在导航完成前获取数据
    next (vm => {
      vm.beforeRoute.name = from.name
      vm.beforeRoute.query = from.query
    })
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .set-role-name {
    margin-bottom: 20px;
  }

</style>
