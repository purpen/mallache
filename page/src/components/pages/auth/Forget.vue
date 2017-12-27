<template>
  <div class="container">
    <div class="register-box">
      <div class="regisiter-title">
        <h2>找回密码</h2>
      </div>
      <div class="register-content">

        <el-form label-position="top" :model="form" :rules="ruleForm" ref="ruleForm" label-width="80px" class="input">
          <el-form-item label="" prop="account">
            <el-input v-model="form.account" name="account" ref="account" auto-complete="on"
                      placeholder="手机号"></el-input>
          </el-form-item>
          <el-form-item label="" prop="smsCode">
            <el-input v-model="form.smsCode" auto-complete="off" name="smsCode" ref="smsCode" placeholder="验证码">
              <template slot="append">
                <el-button type="primary" class="code-btn" @click="fetchCode" :disabled="time > 0">{{ codeMsg }}
                </el-button>
              </template>
            </el-input>
          </el-form-item>
          <el-form-item label="" prop="password">
            <el-input v-model="form.password" type="password" name="password" ref="password" auto-complete="off"
                      placeholder="重置密码"></el-input>
          </el-form-item>
          <el-form-item label="" prop="checkPassword">
            <el-input v-model="form.checkPassword" type="password" name="checkPassword" ref="checkPassword"
                      auto-complete="off" placeholder="确认密码"></el-input>
          </el-form-item>
          <el-button type="primary" :loading="isLoadingBtn" @click="submit('ruleForm')" class="register-btn is-custom">
            重置
          </el-button>
        </el-form>

      </div>
    </div>

  </div>
</template>

<script>
  import api from '@/api/api'

  export default {
    name: 'forget',
    props: {
      second: {
        type: Number,
        default: 60
      }
    },
    data() {
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
        time: 0,
        form: {
          account: '',
          smsCode: '',
          password: '',
          checkPassword: ''

        },
        ruleForm: {
          account: [
            {required: true, message: '请输入手机号码', trigger: 'blur'},
            {min: 11, max: 11, message: '手机号码位数不正确！', trigger: 'blur'}
          ],
          smsCode: [
            {required: true, message: '请输入验证码', trigger: 'blur'},
            {min: 6, max: 6, message: '验证码格式不正确！', trigger: 'blur'}
          ],
          password: [
            {required: true, message: '请输入密码', trigger: 'change'},
            {min: 6, max: 18, message: '密码长度在6-18字符之间！', trigger: 'blur'}
          ],
          checkPassword: [
            {validator: checkPassword, trigger: 'blur'}
          ]
        }

      }
    },
    methods: {
      submit(formName) {
        const that = this
        that.$refs[formName].validate((valid) => {
          if (valid) {
            var account = this.$refs.account.value
            var password = this.$refs.password.value
            var smsCode = this.$refs.smsCode.value

            that.isLoadingBtn = true
            // 验证通过，重置
            that.$http.post(api.forget, {phone: account, password: password, sms_code: smsCode})
              .then(function (response) {
                if (response.data.meta.status_code === 200) {
                  that.$message.success('重置密码成功!')
                  that.$router.replace('/login')
                  return
                } else {
                  that.$message.error(response.data.meta.message)
                  that.isLoadingBtn = false
                }
              })
              .catch(function (error) {
                that.$message({
                  showClose: true,
                  message: error.message,
                  type: 'error'
                })
                that.isLoadingBtn = false
                console.log(error.message)
                return false
              })
            return false
          } else {
            console.log('error submit!!')
            return false
          }
        })
      },
      fetchCode() {
        var account = this.$refs.account.value
        if (account === '') {
          this.$message({
            message: '请输入手机号码!',
            type: 'error',
            duration: 1000
          })
          return
        }

        if (account.length !== 11 || !/^((13|14|15|17|18)[0-9]{1}\d{8})$/.test(account)) {
          this.$message({
            message: '手机号格式不正确!',
            type: 'error',
            duration: 1000
          })
          return
        }

        var url = api.check_account.format(account)
        // 检测手机号是否存在
        const that = this
        that.$http.get(url, {})
          .then(function (response) {
            if (response.data.meta.status_code !== 200) {
              // 获取验证码
              that.$http.post(api.fetch_msm_code, {phone: account})
                .then(function (response) {
                  if (response.data.meta.status_code === 200) {
                    that.time = that.second
                    that.timer()
                    that.$emit('send')
                  } else {
                    that.$message.error('获取验证码失败')
                  }
                })
                .catch(function (error) {
                  that.$message.error(error.message)
                })
            } else {
              that.$message.error(response.data.meta.message)
            }
          })
          .catch(function (error) {
            that.$message.error(error.message)
          })
      },

      timer() {
        if (this.time > 0) {
          this.time = this.time - 1
          setTimeout(this.timer, 1000)
        }
      }
    },
    computed: {
      codeMsg() {
        return this.time > 0 ? '重新发送' + this.time + 's' : '获取验证码'
      }
    },
    mounted: function () {
      const self = this
      window.addEventListener('keydown', function (e) {
        if (e.keyCode === 13) {
          self.submit('ruleForm')
        }
      })
    },
    created: function () {
      if (this.$store.state.event.token) {
        this.$message.error('已经登录!')
        this.$router.replace({name: 'home'})
      }
    }

  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .register-box {
    border: 1px solid #aaa;
    width: 800px;
    height: 450px;
    text-align: center;
    margin: 30px auto 30px auto;
  }

  .regisiter-title {
    width: 800px;
    height: 60px;
    font-size: 1.8rem;
    display: table-cell;
    vertical-align: middle;
    text-align: center;
    border-bottom: 1px solid #aaa;
  }

  p.des {
    font-size: 0.7em;
  }

  form {
    width: 50%;
    text-align: left;
    margin: 0 auto;
    margin-top: 30px;
  }

  .register-btn {
    width: 100%;
  }

  .code-btn {
    cursor: pointer;
  }

  @media screen and (max-width: 767px) {
    .register-box {
      width: auto;
      max-width: 450px;
      height: auto;
      border: none;
      margin: 0 auto;
    }

    .regisiter-title {
      line-height: 52px;
      border: none;
    }

    form {
      width: 100%;
      text-align: left;
      padding: 0 15px;
      margin-top: 0;
    }
  }

</style>
