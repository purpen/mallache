<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <v-menu selectedName="awardsList"></v-menu>

      <el-col :span="20">
        <div class="content">

        <div class="admin-menu-sub">
          <div class="admin-menu-sub-list">
            <router-link :to="{name: 'adminAwardsList'}" active-class="false" :class="{'item': true, 'is-active': menuType == 0}">全部</router-link>
          </div>
          <div class="fr">
            <router-link :to="{name: 'adminAwardsSubmit'}" class="item add"><i class="el-icon-plus"></i> 添加</router-link>
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
                <el-col :span="12">
                  <el-form-item label="名称" prop="name">
                    <el-input v-model="form.name" placeholder=""></el-input>
                  </el-form-item>           
                </el-col>
              </el-row>

              <el-row :gutter="10">
                <el-col :span="5">
                  <el-form-item label="开始时间" prop="start_time">
                    <el-date-picker
                      v-model="form.start_time"
                      type="date"
                      placeholder="选择日期">
                    </el-date-picker>
                  </el-form-item>           
                </el-col>
                <el-col :span="5">
                  <el-form-item label="结束时间" prop="end_time">
                    <el-date-picker
                      v-model="form.end_time"
                      type="date"
                      placeholder="选择日期">
                    </el-date-picker>
                  </el-form-item>           
                </el-col>
              </el-row>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="地址" prop="url">
                    <el-input v-model="form.url" placeholder=""></el-input>
                  </el-form-item>           
                </el-col>
              </el-row>

              <el-form-item label="简述" prop="content">
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
  name: 'admin_award_submit',
  components: {
    vMenu
  },
  data () {
    return {
      menuType: 0,
      itemMode: '创建日历',
      isLoading: false,
      isLoadingBtn: false,
      itemId: '',
      form: {
        type: '',
        start_time: '',
        end_time: '',
        name: '',
        summary: '',
        url: ''
      },
      ruleForm: {
        type: [
          { type: 'number', message: '请选择类型', trigger: 'change' }
        ],
        name: [
          { required: true, message: '请添写名称', trigger: 'blur' }
        ],
        summary: [
          { required: true, message: '请添写简介', trigger: 'blur' }
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
            start_time: that.form.start_time,
            end_time: that.form.end_time,
            summary: that.form.summary,
            url: that.form.url
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
          that.$http({method: method, url: api.adminAwards, data: row})
          .then (function(response) {
            if (response.data.meta.status_code === 200) {
              that.$message.success('提交成功！')
              that.$router.push({name: 'adminAwardsList'})
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
      this.$router.push({name: 'adminAwardsList'})
    }
  },
  computed: {
    typeOptions() {
      var items = []
      for (var i = 0; i < typeData.AWARDS_TYPE.length; i++) {
        var item = {
          value: typeData.AWARDS_TYPE[i]['id'],
          label: typeData.AWARDS_TYPE[i]['name']
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
      that.itemMode = '编辑日历'
      that.itemId = id
      that.$http.get(api.adminAwards, {params: {id: id}})
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
