<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <v-menu currentName="modify_pwd"></v-menu>

      <el-col :span="20">
        <div class="right-content">
          <v-menu-sub currentSubName="identification"></v-menu-sub>
          <div class="content-box" v-loading.body="isLoading">
            <div class="form-title">
              <span>修改密码</span>
            </div>
            <el-form label-position="top" :model="form" :rules="ruleForm" ref="ruleForm" label-width="80px">

              <el-row :gutter="24">
                <el-col :span="8">
                  <el-form-item label="旧密码" prop="old_password">
                    <el-input v-model="form.old_password" type="password" placeholder="请输入您的密码"></el-input>
                  </el-form-item>           
                </el-col>
              </el-row>

              <el-row :gutter="24">
                <el-col :span="8">
                  <el-form-item label="新密码" prop="password">
                    <el-input v-model="form.password" type="password" placeholder="请输入您的新密码"></el-input>
                  </el-form-item>           
                </el-col>
              </el-row>

              <el-row :gutter="24">
                <el-col :span="8">
                  <el-form-item label="确认密码" prop="checkPassword">
                    <el-input v-model="form.checkPassword" type="password" placeholder="请确认您的密码"></el-input>
                  </el-form-item>           
                </el-col>
              </el-row>

              <div class="form-btn">
                  <el-button :loading="isLoadingBtn" class="is-custom" type="primary" @click="submit('ruleForm')">提交</el-button>
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
  import vMenu from '@/components/pages/v_center/Menu'
  import vMenuSub from '@/components/pages/v_center/account/MenuSub'
  import api from '@/api/api'
  import auth from '@/helper/auth'

  import '@/assets/js/format'

  export default {
    name: 'vcenter_account_modify_pwd',
    components: {
      vMenu,
      vMenuSub
    },
    data () {
      var checkPassword = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请再次输入密码'))
        } else if (value !== this.form.password) {
          callback(new Error('两次输入密码不一致!'))
        } else {
          callback()
        }
      }
      return {
        isLoadingBtn: false,
        userId: this.$store.state.event.user.id,

        imageUrl: '',
        form: {
          old_password: '',
          password: '',
          checkPassword: '',

          test: ''
        },

        ruleForm: {
          old_password: [
            { required: true, message: '请输入旧密码', trigger: 'change' },
            { min: 6, max: 18, message: '密码长度在6-18字符之间！', trigger: 'blur' }
          ],
          password: [
            { required: true, message: '请输入新密码', trigger: 'change' },
            { min: 6, max: 18, message: '密码长度在6-18字符之间！', trigger: 'blur' }
          ],
          checkPassword: [
            { validator: checkPassword, trigger: 'blur' }
          ]
        }
      }
    },
    methods: {
      submit(formName) {
        const that = this
        that.$refs[formName].validate((valid) => {
          // 验证通过，提交
          if (valid) {
            if (that.form.old_password === that.form.password) {
              that.$message.error('新密码与旧密码一致！')
              return
            }
            var row = {
              old_password: that.form.old_password,
              password: that.form.password
            }

            that.isLoadingBtn = true
            that.$http({method: 'POST', url: api.modifyPwd, data: row})
            .then (function(response) {
              that.isLoadingBtn = false
              if (response.data.meta.status_code === 200) {
                that.$message.success('操作成功！')
                that.$refs[formName].resetFields()
                // 更新token
                var token = response.data.data.token
                // 写入localStorage
                auth.write_token(token)
                // that.$router.push({name: 'home'})
              } else {
                that.$message.error(response.data.meta.message)
              }
            })
            .catch (function(error) {
              that.isLoadingBtn = false
              that.$message.error(error.message)
            })
          } else {
            return false
          }
        })
      }
    },
    computed: {
    },
    watch: {
    },
    created: function() {
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .form-btn {
    float: right;
  }
  .form-btn button {
    width: 120px;
  }

</style>
