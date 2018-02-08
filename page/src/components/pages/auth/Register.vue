<template>
  <div class="container">
    <div class="register-box">
      <div class="regisiter-title">
        <h2>注册铟果{{identity}}</h2>
      </div>

      <div class="register-tab" v-if="!isMob">
        <div :class="{'register-tab-user': true, active: uActive}" @click="selectUser">
          <h3>我是客户</h3>
          <p class="des">发布项目需求</p>
        </div>
        <div :class="{'register-tab-computer': true, active: cActive}" @click="selectComputer">
          <h3>我是设计公司</h3>
          <p class="des">入驻平台，大量设计需求等您解决。</p>
        </div>
      </div>

      <div class="register-content">
        <el-form :label-position="labelPosition" :model="form" :rules="ruleForm" ref="ruleForm" label-width="80px"
                 class="input">
          <el-form-item label="" prop="account">
            <el-input v-model="form.account" name="account" ref="account" placeholder="手机号"></el-input>
          </el-form-item>
          <el-form-item label="" prop="imgCode">
            <el-input class="imgCodeInput" v-model="form.imgCode" name="imgCode" ref="imgCode" placeholder="图形验证码">
              <template slot="append" class="aaaaaaa">
                <div @click="fetchImgCaptcha" class="imgCode" :style="{'background': `url(${imgCaptchaUrl}) no-repeat`}"></div>
              </template>
            </el-input>
          </el-form-item>
          <el-form-item label="" prop="smsCode">
            <el-input v-model="form.smsCode" name="smsCode" ref="smsCode" placeholder="验证码">
              <template slot="append">
                <el-button type="primary" class="code-btn" @click="fetchCode" :disabled="time > 0">{{ codeMsg }}
                </el-button>
              </template>
            </el-input>
          </el-form-item>
          <el-form-item label="" prop="password">
            <el-input v-model="form.password" type="password" name="password" ref="password"
                      placeholder="密码"></el-input>
          </el-form-item>
          <el-form-item label="" prop="checkPassword">
            <el-input v-model="form.checkPassword" type="password" name="checkPassword" ref="checkPassword"
                      placeholder="确认密码"></el-input>
          </el-form-item>
          <el-button type="primary" :loading="isLoadingBtn" @click="submit('ruleForm')" class="register-btn is-custom">
            注册
          </el-button>
        </el-form>

        <div class="reg">
          <p>已有铟果账户，您可以
            <router-link :to="{name: 'login'}">立即登录</router-link>
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
    name: 'register',
    props: {
      second: {
        type: Number,
        default: 60
      }
    },
    data() {
      let checkPassword = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请再次输入密码'))
        } else if (value !== this.form.password) {
          callback(new Error('两次输入密码不一致!'))
        } else {
          callback()
        }
      }
      let checkNumber = (rule, value, callback) => {
        if (!value) {
          return callback(new Error('请添写手机号'))
        } else {
          if (!Number.isInteger(Number(value))) {
            callback(new Error('手机号只能为数字！'))
          } else {
            let len = value.toString().length
            if (len === 11) {
              if (/^((13|14|15|17|18)[0-9]{1}\d{8})$/.test(value)) {
                callback()
              } else {
                callback(new Error('手机号格式不正确'))
              }
            } else {
              callback(new Error('手机号长度应为11位'))
            }
          }
        }
      }
      return {
        isLoadingBtn: false,
        uActive: false,
        cActive: false,
        time: 0,
        labelPosition: 'top',
        form: {
          type: 1,
          account: '',
          smsCode: '',
          imgCode: '',
          password: '',
          checkPassword: ''
        },
        ruleForm: {
          account: [
            {validator: checkNumber, trigger: 'blur'}
          ],
          imgCode: [
            {required: true, message: '请输入图形验证码', trigger: 'blur'}
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
        },
        identity: '',
        imgCaptchaUrl: '',
        imgCaptchaStr: '',
        timeInterval: null // 定时器
      }
    },
    methods: {
      selectUser() {
        this.form.type = 1
        this.cActive = false
        this.uActive = true
      },
      selectComputer() {
        this.form.type = 2
        this.cActive = true
        this.uActive = ''
      },
      submit(formName) {
        const that = this
        that.$refs[formName].validate((valid) => {
          if (valid) {
            let account = this.form.account
            let password = this.form.password
            let smsCode = this.form.smsCode
            let type = this.form.type
            if (!type) {
              that.$message.error('请选择客户或设计公司')
              return false
            }

            that.isLoadingBtn = true
            // 验证通过，注册
            that.$http.post(api.register, {account: account, password: password, type: type, sms_code: smsCode})
              .then(function (response) {
                if (response.data.meta.status_code === 200) {
                  let token = response.data.data.token
                  // 写入localStorage
                  auth.write_token(token)
                  // ajax拉取用户信息
                  that.$http.get(api.user, {})
                    .then(function (response) {
                      if (response.data.meta.status_code === 200) {
                        auth.write_user(response.data.data)

                        that.$message({
                          showClose: true,
                          message: '注册成功!',
                          type: 'success'
                        })

                        that.$router.replace({name: 'vcenterControl'})
                      } else {
                        auth.logout()
                        that.$message({
                          showClose: true,
                          message: response.data.meta.message,
                          type: 'error'
                        })
                        that.isLoadingBtn = false
                      }
                    })
                    .catch(function (error) {
                      auth.logout()
                      that.$message({
                        showClose: true,
                        message: error.message,
                        type: 'error'
                      })
                      that.isLoadingBtn = false
                    })
                } else {
                  that.$message({
                    showClose: true,
                    message: response.data.meta.message,
                    type: 'error'
                  })
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
        let full = 0
        this.$refs.ruleForm.validateField('account', (err) => {
          if (err) {
            full++
          } else {
            return false
          }
        })
        this.$refs.ruleForm.validateField('imgCode', (err) => {
          if (err) {
            full++
          } else {
            return false
          }
        })
        if (!full) {
          let account = this.form.account
          let url = api.check_account.format(account)
          // 检测手机号是否存在
          const that = this
          that.$http.get(url, {})
            .then(function (response) {
              if (response.data.meta.status_code === 200) {
                // 获取验证码
                that.$http.post(api.fetch_msm_code, {
                  phone: account,
                  str: that.imgCaptchaStr,
                  captcha: that.form.imgCode
                })
                  .then(function (response) {
                    if (response.data.meta.status_code === 200) {
                      that.time = that.second
                      that.timer()
                      that.$emit('send')
                    } else {
                      that.$message({
                        showClose: true,
                        message: response.data.meta.message,
                        type: 'error'
                      })
                    }
                  })
                  .catch(function (error) {
                    that.$message({
                      showClose: true,
                      message: error.message,
                      type: 'error'
                    })
                    console.log(error.message)
                    return false
                  })
              } else {
                that.$message({
                  showClose: true,
                  message: response.data.meta.message,
                  type: 'error'
                })
                return false
              }
            })
            .catch(function (error) {
              that.$message({
                showClose: true,
                message: error.message,
                type: 'error'
              })
              return false
            })
        } else {
          return
        }
      },
      fetchImgCaptcha() {
        // let ti = 0
        // clearInterval(this.timeInterval)
        // this.timeInterval = setInterval(() => {
        //   ti++
        //   console.log(ti)
        //   if (ti === 600) {
        //     clearInterval(this.timeInterval)
        //     this.fetchImgCaptcha()
        //   }
        // }, 1000)
        this.$http.get(api.fetch_img_captcha)
        .then((res) => {
          if (res.data.meta.status_code === 200) {
            this.imgCaptchaUrl = res.data.data.url
            this.imgCaptchaStr = res.data.data.str
          } else {
            console.log(res.data.meta.message)
          }
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
      },
      isMob() {
        return this.$store.state.event.isMob
      }
    },
    mounted() {
      this.fetchImgCaptcha()
      const self = this
      window.addEventListener('keydown', function (e) {
        if (e.keyCode === 13) {
          self.submit('ruleForm')
        }
      })
    },
    created() {
      this.form.type = this.$route.params.type
      if (this.form.type === 2) {
        this.cActive = true
      }
      if (this.$store.state.event.token) {
        this.$message.error('已经登录!')
        this.$router.replace({name: 'home'})
      }
      if (this.isMob) {
        if (this.form.type) {
          if (this.form.type === 1) {
            this.identity = '客户'
          } else if (this.form.type === 2) {
            this.identity = '服务商'
          }
        } else {
          this.$message.error('没有选择用户类型!')
          this.$router.replace({name: 'identity'})
        }
      }
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .register-box {
    width: 800px;
    height: 600px;
    text-align: center;
    margin: 30px auto 30px auto;
  }

  .regisiter-title {
    width: 800px;
    height: 60px;
    font-size: 2rem;
    display: table-cell;
    vertical-align: middle;
    text-align: center;
    color: #222;
  }

  .register-tab {
    font-size: 1.8rem;
    width: 100%;
    height: 80px;
    border: 1px solid #d2d2d2;
    border-bottom: none;
    position: relative;
    background-color: #f7f7f7;
  }

  .register-tab-user {
    padding-top: 20px;
    width: 50%;
    height: 80px;
    position: absolute;
    cursor: pointer;
    border-right: 1px solid #d2d2d2;
    border-bottom: 1px solid #d2d2d2;
  }

  .register-tab-user.active {
    border-bottom: 2px solid #FF5A5F;
    background-color: #fff;
  }

  .register-tab-user.active h3 {
    color: #FF5A5F;
  }

  .register-tab-computer {
    padding-top: 20px;
    width: 50%;
    height: 80px;
    position: absolute;
    left: 50%;
    cursor: pointer;
    border-bottom: 1px solid #d2d2d2;
  }

  .register-tab-computer.active {
    border-bottom: 2px solid #FF5A5F;
    background-color: #fff;
  }

  .register-tab-computer.active h3 {
    color: #FF5A5F;
  }

  .register-content {
    border: 1px solid #d2d2d2;
    border-top: none;
    padding-top: 30px;
  }

  .register-tab h3 {
    padding: 3px 0 6px 0;
    color: #666;
  }

  p.des {
    padding: 3px;
    font-size: 0.7em;
    color: #999;
  }

  form {
    width: 50%;
    text-align: left;
    margin: 0 auto;
  }

  .register-btn {
    width: 100%;
  }

  .code-btn {
    cursor: pointer;
  }

  .reg {
    margin: 32px 0;
  }

  .reg p {
    color: #666;
  }

  .reg p a {
    color: #FF5A5F;
  }

  .imgCode {
    width: 102px;
    height: 34px;
    background-size: cover;
    border-radius: 0 4px 4px 0;
    cursor: pointer;
  }

  @media screen and (max-width: 767px) {
    .register-box {
      border: none;
      width: auto;
      max-width: 450px;
      height: auto;
      margin-top: 0;
    }

    form {
      width: 100%;
      text-align: left;
      padding: 0 15px;
      margin-top: 0;
    }

    .register-tab {
      margin-bottom: 30px;
    }
    .register-content {
      border: none;
      padding-top: 0;
    }
    .reg {
      margin: 20px 0
    }
  }
</style>
