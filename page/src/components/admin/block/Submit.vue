<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <v-menu selectedName="blockList"></v-menu>

      <el-col :span="20">
        <div class="content">

        <div class="admin-menu-sub">
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminBlockList'}" active-class="false" :class="{'item': true, 'is-active': menuType == 0}">全部</router-link>
          </div>
          <div class="fr">
            <router-link :to="{name: 'adminBlockAdd'}" class="item add"><i class="el-icon-plus"></i> 添加</router-link>
          </div>
        </div>

          <div class="content-box">
            <div class="form-title">
              <span>{{ itemMode }}</span>
            </div>
            <el-form label-position="top" :model="form" :rules="ruleForm" ref="ruleForm" label-width="80px">

              <el-row :gutter="24">
                <el-col :span="6">
                  <el-form-item label="标识" prop="mark">
                    <el-input v-model="form.mark" placeholder=""></el-input>
                  </el-form-item>           
                </el-col>
                <el-col :span="6">
                  <el-form-item label="名称" prop="name">
                    <el-input v-model="form.name" placeholder=""></el-input>
                  </el-form-item>           
                </el-col>
              </el-row>

              <el-form-item label="代码域" prop="code">
                <el-input
                  type="textarea"
                  :rows="5"
                  placeholder="请输入内容"
                  v-model="form.code">
                </el-input>
              </el-form-item>

              <el-form-item label="内容" prop="content">
                <el-input
                  type="textarea"
                  :rows="5"
                  placeholder="请输入内容"
                  v-model="form.content">
                </el-input>
              </el-form-item>

              <el-form-item label="备注" prop="summary">
                <el-input
                  type="textarea"
                  :rows="5"
                  placeholder="请输入内容"
                  v-model="form.summary">
                </el-input>
              </el-form-item>

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
  name: 'admin_block_submit',
  components: {
    vMenu
  },
  data () {
    return {
      menuType: 0,
      itemMode: '创建区块',
      isLoading: false,
      isLoadingBtn: false,
      itemId: '',
      form: {
        name: '',
        mark: '',
        code: '',
        content: '',
        summary: ''
      },
      ruleForm: {
        mark: [
          { required: true, message: '请添写标识', trigger: 'blur' }
        ],
        name: [
          { required: true, message: '请添写分类名称', trigger: 'blur' }
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
            name: that.form.name,
            mark: that.form.mark,
            code: that.form.code,
            summary: that.form.summary,
            content: that.form.content
          }
          var method = null

          if (that.itemId) {
            method = 'put'
            row.id = that.itemId
          } else {
            method = 'post'
          }
          console.log(row)
          that.isLoadingBtn = true
          that.$http({method: method, url: api.adminBlock, data: row})
          .then (function(response) {
            if (response.data.meta.status_code === 200) {
              that.$message.success('提交成功！')
              // 跳转到上一页
              if (that.beforeRoute.name) {
                that.$router.push({name: that.beforeRoute.name, query: that.beforeRoute.query})
              } else {
                that.$router.push({name: 'adminBlockList'})
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
      this.$router.push({name: 'adminBlockList'})
    }
  },
  computed: {
    typeOptions() {
      var items = []
      for (var i = 0; i < typeData.BLOCK_TYPE.length; i++) {
        var item = {
          value: typeData.BLOCK_TYPE[i]['id'],
          label: typeData.BLOCK_TYPE[i]['name']
        }
        items.push(item)
      }
      return items
    }
  },
  created: function() {
    const that = this
    var id = this.$route.params.id
    if (id) {
      that.itemMode = '编辑区块'
      that.itemId = id
      that.$http.get(api.adminBlock, {params: {id: id}})
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
