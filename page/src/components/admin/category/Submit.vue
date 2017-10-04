<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <v-menu selectedName="categoryList"></v-menu>

      <el-col :span="20">
        <div class="content">

        <div class="admin-menu-sub">
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminCategoryList'}" active-class="false" :class="{'item': true, 'is-active': menuType == 0}">全部</router-link>
          </div>
          <div class="fr">
            <router-link :to="{name: 'adminCategorySubmit'}" class="item add"><i class="el-icon-plus"></i> 添加</router-link>
          </div>
        </div>

          <div class="content-box">
            <div class="form-title">
              <span>{{ itemMode }}</span>
            </div>
            <el-form label-position="top" :model="form" :rules="ruleForm" ref="ruleForm" label-width="80px">


              <el-form-item label="类型" prop="type">
                <el-radio-group v-model.number="form.type">
                  <el-radio-button
                    v-for="item in typeOptions"
                    :key="item.index"
                    :label="item.value">{{ item.label }}</el-radio-button>
                </el-radio-group>
              </el-form-item>

              <el-row :gutter="24">
                <el-col :span="6">
                  <el-form-item label="名称" prop="name">
                    <el-input v-model="form.name" placeholder=""></el-input>
                  </el-form-item>           
                </el-col>
              </el-row>

              <el-form-item label="描述" prop="content">
                <el-input
                  type="textarea"
                  :rows="5"
                  placeholder="请输入内容"
                  v-model="form.content">
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
  name: 'admin_category_submit',
  components: {
    vMenu
  },
  data () {
    return {
      menuType: 0,
      itemMode: '创建分类',
      isLoading: false,
      isLoadingBtn: false,
      itemId: '',
      form: {
        type: '',
        name: '',
        content: '',
        url: ''
      },
      ruleForm: {
        type: [
          { type: 'number', message: '请选择类型', trigger: 'change' }
        ],
        name: [
          { required: true, message: '请添写分类名称', trigger: 'blur' }
        ],
        content: [
          { required: true, message: '请添写内容', trigger: 'blur' }
        ]
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
            type: that.form.type,
            name: that.form.name,
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
          that.$http({method: method, url: api.adminCategory, data: row})
          .then (function(response) {
            if (response.data.meta.status_code === 200) {
              that.$message.success('提交成功！')
              that.$router.push({name: 'adminCategoryList'})
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
      this.$router.push({name: 'adminCategoryList'})
    }
  },
  computed: {
    typeOptions() {
      var items = []
      for (var i = 0; i < typeData.CATEGORY_TYPE.length; i++) {
        var item = {
          value: typeData.CATEGORY_TYPE[i]['id'],
          label: typeData.CATEGORY_TYPE[i]['name']
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
      that.itemMode = '编辑分类'
      that.itemId = id
      that.$http.get(api.adminCategory, {params: {id: id}})
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
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .set-role-name {
    margin-bottom: 20px;
  }

</style>
