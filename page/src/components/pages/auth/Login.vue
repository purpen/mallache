<template>
  <div class="container">
    <div class="login-box">
      <div class="login-title">
        <h2>登录铟果</h2>
      </div>

      <div class="login-content">

        <el-form :label-position="labelPosition" :model="form" :rules="ruleForm" ref="ruleForm" label-width="80px">
          <el-form-item label="" prop="account" class="input">
            <el-input v-model="form.account" name="account" ref="account" auto-complete="on"
                      placeholder="手机号"></el-input>
          </el-form-item>
          <el-form-item label="" prop="password" class="input">
            <el-input v-model="form.password" type="password" name="password" ref="password"
                      placeholder="密码"></el-input>
          </el-form-item>
          <div class="opt">
            <p class="rember"><input type="checkbox" id="passwd" /><label for="passwd">记住密码</label></p>
            <p class="forget">
              <router-link :to="{name: 'forget'}">忘记密码?</router-link>
            </p>
          </div>
          <el-button type="primary" :loading="isLoadingBtn" @keyup="submit('ruleForm')" @click="submit('ruleForm')"
                     class="login-btn is-custom">登录
          </el-button>
        </el-form>

        <div class="reg">
          <p v-if="!isMob">还没有铟果账户？
            <router-link v-if="type" :to="{name: 'register',params:{type: type}}">立即注册</router-link>
            <router-link v-else :to="{name: 'register'}">立即注册</router-link>
          </p>
          <p v-else>还没有铟果账户？
            <router-link :to="{name: 'identity'}">立即注册</router-link>
          </p>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import api from '@/api/api'
import auth from '@/helper/auth'

export default {
  name: 'login',

  data() {
    return {
      isLoadingBtn: false,
      labelPosition: 'top',
      form: {
        account: '',
        password: ''
      },
      ruleForm: {
        account: [
          { required: true, message: '请输入手机号码', trigger: 'blur' },
          { min: 11, max: 11, message: '手机号码位数不正确！', trigger: 'blur' }
        ],
        password: [
          { required: true, message: '请输入密码', trigger: 'change' },
          { min: 6, max: 18, message: '密码长度在6-18字符之间！', trigger: 'blur' }
        ]
      },
      type: 0
    }
  },
  methods: {
    submit(formName) {
      const that = this

      that.$refs[formName].validate(valid => {
        if (valid) {
          let account = this.$refs.account.value
          let password = this.$refs.password.value
          that.isLoadingBtn = true
          // 验证通过，登录
          that.$http
            .post(api.login, { account: account, password: password })
            .then(function(response) {
              that.isLoadingBtn = false
              if (response.data.meta.status_code === 200) {
                let token = response.data.data.token
                // 写入localStorage
                auth.write_token(token)
                // ajax拉取用户信息
                that.$http
                  .get(api.user, {})
                  .then(function(response) {
                    if (response.data.meta.status_code === 200) {
                      that.$message.success('登录成功')
                      auth.write_user(response.data.data)
                      let prevUrlName = that.$store.state.event.prevUrlName
                      if (prevUrlName) {
                        // 清空上一url
                        auth.clear_prev_url_name()
                        that.$router.replace({ name: prevUrlName })
                      } else {
                        if (that.isMob) {
                          that.$router.replace({ name: 'home' })
                        } else {
                          that.$router.replace({ name: 'vcenterControl' })
                        }
                      }
                    } else {
                      auth.logout()
                      that.$message.error(response.data.meta.message)
                    }
                  })
                  .catch(function(error) {
                    auth.logout()
                    that.$message.error(error.message)
                  })
              } else {
                that.$message.error(response.data.meta.message)
              }
            })
            .catch(function(error) {
              that.isLoadingBtn = false
              that.$message.error(error.message)
            })
        } else {
          console.log('error submit!!')
          return false
        }
      })
    }
  },
  mounted: function() {
    const self = this
    window.addEventListener('keydown', function(e) {
      if (e.keyCode === 13) {
        self.submit('ruleForm')
      }
    })
  },
  created: function() {
    let prevUrlName = this.$store.state.event.prevUrlName
    this.type = this.$route.params.type
    if (this.$route.params.url === 'yq') {
      this.$message({
        message: '请使用设计服务商的账号登录',
        type: 'info'
      })
    } else {
      if (prevUrlName) {
        this.$message.error('请先登录！')
      }
    }
    if (this.$store.state.event.token) {
      this.$message.error('已经登录!')
      this.$router.replace({ name: 'home' })
    }
  },
  computed: {
    isMob() {
      return this.$store.state.event.isMob
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.login-box {
  border: 1px solid #cccccc;
  width: 800px;
  height: 400px;
  text-align: center;
  margin: 30px auto 30px auto;
}

.login-title {
  width: 800px;
  height: 60px;
  font-size: 2rem;
  display: table-cell;
  vertical-align: middle;
  text-align: center;
  border-bottom: 1px solid #aaaaaa;
}

p.des {
  font-size: 0.8em;
}

form {
  width: 50%;
  text-align: left;
  margin: 0 auto;
  margin-top: 30px;
}

.login-btn {
  width: 100%;
}

.reg {
  margin-top: 40px;
}

.reg p {
  color: #666666;
}

.reg p a {
  color: #ff5a5f;
}

.opt {
  margin-top: -25px;
  line-height: 45px;
  overflow: hidden;
}

.forget {
  float: right;
}

.rember {
  float: left;
  font-size: 1.3rem;
}

.forget a {
  font-size: 1.3rem;
  color: #666666;
}

#passwd {
  width: 16px;
  height: 16px;
  vertical-align: sub;
}

@media screen and (max-width: 767px) {
  .login-box {
    width: auto;
    max-width: 450px;
    border: none;
    margin: 0 auto;
  }

  .login-title {
    line-height: 52px;
    border: none;
  }

  form {
    width: 100%;
    text-align: left;
    padding: 0 15px;
    margin-top: 0;
  }

  .opt {
    margin-top: -10px;
    overflow: hidden;
  }

  .reg {
    margin-top: 20px;
  }
}
</style>

