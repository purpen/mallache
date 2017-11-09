<template>
  <div class="container min-height350">
    <div class="blank20"></div>
    <el-row :gutter="24" class="anli-elrow">
      <v-menu currentName="wallet"></v-menu>

      <el-col :span="isMob ? 24 : 20">

        <div :class="['right-content', isMob ? 'right-content-m' : '']">

          <el-breadcrumb separator="/" class="bread">
            <el-breadcrumb-item :to="{ name: 'home' }">首页</el-breadcrumb-item>
            <el-breadcrumb-item :to="{ name: 'vcenterWalletList' }">我的钱包</el-breadcrumb-item>
            <el-breadcrumb-item>银行卡管理</el-breadcrumb-item>
          </el-breadcrumb>

          <div class="item-list" v-loading.body="isLoading">
            <el-row :gutter="15">

              <el-col :xs="24" :sm="8" :md="8" :lg="8" v-for="(d, index) in itemList" :key="index">
                <div class="item">
                  <div class="item-title">
                    <p>{{ d.bank_val }}</p>
                  </div>
                  <div class="item-content">
                    <div class="number">
                      <p>**** **** **** {{ d.m_number }}</p>
                    </div>
                    <div class="option">
                      <a href="javascript:void(0);" @click="edit" :item_id="d.id" :index="index">编辑</a>
                      <a href="javascript:void(0);" @click="del" :item_id="d.id" :index="index">删除</a>
                    </div>
                    <div class="clear default" v-if="d.default === 1">
                      <p><i class="fa fa-check-circle-o" aria-hidden="true"></i> 默认银行账户</p>
                    </div>
                  </div>
                </div>
              </el-col>

              <el-col :xs="24" :sm="8" :md="8" :lg="8">
                <div class="item add">
                  <a href="javascript:void(0);" @click="add">
                    <p class="add-icon"><i class="el-icon-plus avatar-uploader-icon"></i></p>
                    <p class="add-des">添加银行卡</p>
                  </a>
                </div>
              </el-col>

            </el-row>
          </div>
        </div>

      </el-col>
    </el-row>

    <!--弹框模板-->
    <el-dialog :title="itemModelTitle" v-model="itemModel" class="bank-dialog">

      <el-form :model="form" label-position="top" :rules="ruleForm" ref="ruleForm">
        <el-form-item label="开户人姓名" prop="account_name" label-width="200px">
          <el-input type="text" v-model="form.account_name" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="开户银行" prop="account_bank_id" label-width="150px">
          <el-select v-model.number="form.account_bank_id" placeholder="请选择银行">
            <el-option
              v-for="(item, index) in bankOptions"
              :label="item.label"
              :key="index"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>

        <region-picker :isEmpty="false" :isFirstProp="isFirst" :provinceProp="province" :titleProp="cityTitle"
                       :cityProp="city" :districtProp="district" :twoSelect="true" @onchange="change"></region-picker>

        <el-form-item label="支行名称" prop="branch_name" label-width="200px">
          <el-input type="text" v-model="form.branch_name" auto-complete="off"></el-input>
        </el-form-item>

        <el-form-item label="银行账号" prop="account_number" label-width="200px">
          <el-input type="text" v-model="form.account_number" auto-complete="off"></el-input>
        </el-form-item>

        <el-form-item label="设为默认账户" prop="account_number" label-width="200px">
          <el-switch
            v-model.number="form.default"
            on-text=""
            off-text="">
          </el-switch>
        </el-form-item>

      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="itemModel = false">取 消</el-button>
        <el-button type="primary" :loading="isLoadingBtn" @click="submit('ruleForm')">确 定</el-button>
      </div>
    </el-dialog>

    <el-dialog
      title="提示"
      v-model="sureDialog"
      size="tiny">
      <span>{{ sureDialogMessage }}</span>
      <span slot="footer" class="dialog-footer">
        <el-button @click="sureDialog = false">取 消</el-button>
        <el-button type="primary" :loading="sureDialogLoadingBtn" @click="sureDialogSubmit">确 定</el-button>
      </span>
    </el-dialog>

  </div>
</template>

<script>
  import vMenu from '@/components/pages/v_center/Menu'
  // 城市联动
  import RegionPicker from '@/components/block/RegionPicker'
  import api from '@/api/api'
  import '@/assets/js/format'
  import '@/assets/js/date_format'
  import typeData from '@/config'

  export default {
    name: 'vcenter_bank_list',
    components: {
      vMenu,
      RegionPicker
    },
    data () {
      return {
        isLoading: false,
        isLoadingBtn: false,
        itemList: [],
        itemModel: false,
        itemModelTitle: '添加银行卡',
        sureDialog: false,
        sureDialogMessage: '确认执行此操作？',
        sureDialogLoadingBtn: false,
        isFirst: false,
        cityTitle: '选择开户行所在地',
        province: '',
        city: '',
        district: '',
        form: {
          account_name: '',
          account_bank_id: '',
          branch_name: '',
          account_number: '',
          default: false
        },
        current: {
          id: '',
          index: ''
        },
        ruleForm: {
          account_name: [
            {required: true, message: '请添写开户人姓名', trigger: 'blur'}
          ],
          account_bank_id: [
            {type: 'number', required: true, message: '请选择银行', trigger: 'change'}
          ],
          branch_name: [
            {required: true, message: '请添写开户行信息', trigger: 'blur'}
          ],
          account_number: [
            {required: true, message: '请添写银行账号', trigger: 'blur'}
          ]
        },
        userId: this.$store.state.event.user.id
      }
    },
    methods: {
      loadList() {
        const self = this
        self.isLoading = true
        self.$http.get(api.bank, {})
          .then(function (response) {
            self.isLoading = false
            if (response.data.meta.status_code === 200) {
              self.itemList = response.data.data

              for (var i = 0; i < self.itemList.length; i++) {
                var item = self.itemList[i]
                self.itemList[i]['m_number'] = item.account_number.substr(item.account_number.length - 4)
              } // endfor

              console.log(response.data.data)
            }
          })
          .catch(function (error) {
            self.$message.error(error.message)
            self.isLoading = false
            return false
          })
      },
      add() {
        this.current = {}
        this.form = {
          account_name: '',
          account_bank_id: '',
          branch_name: '',
          account_number: '',
          default: false
        }
        this.itemModelTitle = '添加银行卡'
        this.itemModel = true
        const self = this
        setTimeout(function () {
          self.isFirst = true
          self.province = ''
          self.city = ''
        }, 100)
      },
      edit(event) {
        this.current.id = parseInt(event.currentTarget.getAttribute('item_id'))
        this.current.index = parseInt(event.currentTarget.getAttribute('index'))

        this.itemModelTitle = '编辑银行卡'
        this.itemModel = true
        this.form = this.itemList[this.current.index]
        if (this.form.default === 1) {
          this.form.default = true
        } else {
          this.form.default = false
        }

        const self = this
        setTimeout(function () {
          self.isFirst = true
          self.province = self.form.province
          self.city = self.form.city
        }, 100)
      },
      del(event) {
        this.current.id = parseInt(event.currentTarget.getAttribute('item_id'))
        this.current.index = parseInt(event.currentTarget.getAttribute('index'))
        this.current.type = 1
        this.sureDialog = true
      },
      sureDialogSubmit() {
        const self = this
        self.sureDialogLoadingBtn = true
        if (self.current.type === 1) {
          self.$http.put(api.bankUnStatus, {id: self.current.id})
            .then(function (response) {
              self.sureDialogLoadingBtn = false
              if (response.data.meta.status_code === 200) {
                self.$message.success('操作成功！')
                self.sureDialog = false
                self.itemList.splice(self.current.index, 1)
              }
            })
            .catch(function (error) {
              self.$message.error(error.message)
              self.sureDialogLoadingBtn = false
            })
        }
      },
      submit(formName) {
        const that = this
        that.$refs[formName].validate((valid) => {
          // 验证通过，提交
          if (valid) {
            if (that.province === '') {
              that.$message.error('请选择省份!')
              return false
            }
            if (that.city === '') {
              that.$message.error('请选择城市!')
              return false
            }
            var defaultd = that.form.default ? 1 : 0
            var row = {
              account_name: that.form.account_name,
              account_bank_id: that.form.account_bank_id,
              branch_name: that.form.branch_name,
              account_number: that.form.account_number,
              default: defaultd,
              province: this.province,
              city: this.city
            }

            var apiUrl = null
            var method = null

            if (!that.current.id) {
              apiUrl = api.bank
              method = 'post'
            } else {
              apiUrl = api.bankId.format(that.current.id)
              method = 'put'
            }
            that.isLoadingBtn = true
            that.$http({method: method, url: apiUrl, data: row})
              .then(function (response) {
                that.isLoadingBtn = false
                if (response.data.meta.status_code === 200) {
                  that.$message.success('提交成功!')
                  that.itemModel = false
                  var item = response.data.data
                  item['default'] = parseInt(item.default)
                  item['m_number'] = item.account_number.substr(item.account_number.length - 4)
                  if (!that.current.id) {
                    that.itemList.unshift(item)
                  } else {
                    that.itemList[that.current.index] = item
                  }
                } else {
                  that.$message.error(response.data.meta.message)
                }
              })
              .catch(function (error) {
                that.$message.error(error.message)
                that.isLoadingBtn = false
                console.log(error.message)
              })
          } else {
          }
        })
      },
      change: function (obj) {
        this.province = obj.province
        this.city = obj.city
        this.district = obj.district
      }
    },
    computed: {
      bankOptions() {
        var items = []
        for (var i = 0; i < typeData.BANK_OPTIONS.length; i++) {
          var item = {
            value: typeData.BANK_OPTIONS[i]['id'],
            label: typeData.BANK_OPTIONS[i]['name']
          }
          items.push(item)
        }
        return items
      },
      isMob() {
        return this.$store.state.event.isMob
      }
    },
    created: function () {
      this.loadList()
    },
    watch: {
      '$route' (to, from) {
        // 对路由变化作出响应...
        this.loadList()
      }
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .right-content-m {
    margin-top: 20px;
  }

  .bread {
    margin-bottom: 10px;
  }

  .item-list {

  }

  .el-form-item {
    margin-bottom: 15px;
  }

  .item {
    border: 1px solid #ccc;
    margin-bottom: 10px;
    height: 150px;
  }

  .item-title {
    background-color: #F3F3F3;
    padding: 5px 15px;
  }

  .item-title p {
    line-height: 2;
    font-size: 1.5rem;
    color: #333;
  }

  .item-content {
    padding: 20px 15px 0 15px;
  }

  .item-content .number {
    float: left;
  }

  .item-content .number p {
    font-size: 1.5rem;
    color: #333;
  }

  .item-content .option {
    float: right;
  }

  .item-content .option a {
    color: #FF5A5F;
  }

  .default {
    margin: 60px 0 20px 0;
  }

  .default p {
    font-size: 1.2rem;
    color: #666;
  }

  .add {
    text-align: center;
    background-color: #FAFAFA;
  }

  .add .add-icon {
    margin-top: 40px;
    font-size: 3rem;
    color: #666;
  }

  .add .add-des {
    line-height: 2;
    color: #666;
  }
</style>
