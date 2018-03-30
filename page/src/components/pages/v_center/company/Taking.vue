<template>
  <div class="container taking-dialog blank40">
    <el-row :gutter="24">
      <v-menu currentName="profile" :class="[isMob ? 'v-menu' : '']"></v-menu>

      <el-col :span="isMob ? 24 : 20">
        <div class="right-content">
          <v-menu-sub></v-menu-sub>
          <div :class="['content-box', isMob ? 'content-box-m' : '']" v-loading.body="isLoading">

            <div class="form-title">
              <span>接单设置</span>
            </div>

            <div class="taking-info">
              <p class="des">* 我们将根据设计公司的业务优势以及接单价格区间，来精准匹配推送项目。</p>
              <p>设置设计类别的接单价格区间</p>
            </div>

            <div class="taking-box" v-for="d in typeData">
              <div class="taking-item clear">
                <div class="rz-title">
                  <span>{{ d.name }}</span>
                </div>
                <div class="rz-stat">
                </div>
                <div class="clear"></div>

                <div class="item-list">
                  <div class="item-name" v-for="s in d.designType">
                    <div class="item-title">
                      <span class="sub-type">{{ s.name }}</span>
                    </div>
                    <v-design-item :pid="d.id" :sid="s.id" :item="items[s.key]" :isLoaded="isLoaded"
                                   @submitItem="submitItem" @delItem="delItem"></v-design-item>
                  </div>


                </div>
                <div class="line"></div>
              </div>

            </div>

          </div>
        </div>

      </el-col>
    </el-row>

    <!--弹框模板-->
    <el-dialog :title="itemModelTitle" v-model="itemModel">
      <div class="model-title">
        <span>{{ pName }}</span>-><span>{{ itemName }}</span>
      </div>
      <el-form :model="form" :rules="ruleForm" ref="ruleForm">
        <el-form-item label="项目平均周期" prop="project_cycle" :label-width="formLabelWidth">
          <el-select v-model.number="form.project_cycle" placeholder="请选择平均周期">
            <el-option
              v-for="item in projectCycleOptions"
              :label="item.label"
              :key="item.index"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="项目最低接单价格" prop="min_price" :label-width="formLabelWidth">
          <el-select v-model.number="form.min_price" placeholder="请选择最低接单价格">
            <el-option
              v-for="item in minPriceOptions"
              :label="item.label"
              :key="item.index"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="cancelFormVisible">取 消</el-button>
        <el-button type="primary" @click="submit('ruleForm')">确 定</el-button>
      </div>
    </el-dialog>

  </div>

</template>

<script>
  import vMenu from '@/components/pages/v_center/Menu'
  import vMenuSub from '@/components/pages/v_center/company/MenuSub'
  import vDesignItem from '@/components/pages/v_center/company/DesignItem'
  import api from '@/api/api'
  import typeData from '@/config'
  import '@/assets/js/format'

  export default {
    name: 'vcenter_company_taking',
    components: {
      vMenu,
      vMenuSub,
      vDesignItem
    },
    data () {
      return {
        typeData: typeData.COMPANY_TYPE,
        itemModel: false,
        itemModelTitle: '项目接单设置',
        itemModelPid: '',
        itemId: '',
        isLoading: false,
        pid: '',
        sid: '',
        pName: '',
        itemName: '',
        isLoaded: false,
        items: {},
        projectCycleOptions: [{
          value: 1,
          label: '1个月内'
        }, {
          value: 2,
          label: '1-2个月'
        }, {
          value: 3,
          label: '2-3个月'
        }, {
          value: 4,
          label: '3-4个月'
        }, {
          value: 5,
          label: '5个月以上'
        }],
        userId: this.$store.state.event.user.id,
        form: {
          project_cycle: '',
          min_price: ''
        },
        current: {
          pid: '',
          itemId: '',
          sid: ''
        },
        ruleForm: {
          project_cycle: [
            {type: 'number', message: '请选择项目平均周期', trigger: 'change'}
          ],
          min_price: [
            {type: 'number', message: '请选择最低接单价格', trigger: 'change'}
          ]
        },
        formLabelWidth: '150px'
      }
    },
    computed: {
      minPriceOptions() {
        let arr = []
        for (let i = 1; i <= 30; i++) {
          let item = {}
          item.value = i
          item.label = i + '万'
          arr.push(item)
        }
        return arr
      },
      isMob() {
        return this.$store.state.event.isMob
      }
    },
    methods: {
      submitItem(event) {
        /**
         this.current.itemId = parseInt(event.target.getAttribute('item_id'))
         let sid = this.current.sid = parseInt(event.target.getAttribute('sid'))
         let pid = this.current.pid = parseInt(event.target.getAttribute('pid'))
         **/
        this.current.itemId = parseInt(event.itemId)
        let sid = this.current.sid = parseInt(event.sid)
        let pid = this.current.pid = parseInt(event.pid)

        if (pid === 1) {
          this.pName = '产品设计'
          if (sid === 1) {
            this.itemName = '产品策划'
          } else if (sid === 2) {
            this.itemName = '产品设计'
          } else if (sid === 3) {
            this.itemName = '结构设计'
          }
        } else if (pid === 2) {
          this.pName = 'UI/UX设计'
          if (sid === 1) {
            this.itemName = 'App设计'
          } else if (sid === 2) {
            this.itemName = '网页设计'
          }
        }

        this.itemModel = true
      },
      delItem(event) {
        let itemId = parseInt(event.itemId)
        let sid = this.current.sid = parseInt(event.sid)
        let pid = this.current.pid = parseInt(event.pid)
        const that = this

        let key = 'item_' + pid + '_' + sid
        delete that.items[key]

        let apiUrl = api.designItem.format(itemId)
        that.$http({method: 'get', url: apiUrl, data: {}})
          .then(function (response) {
            if (response.data.meta.status_code === 200) {
              that.$message({
                showClose: true,
                message: '删除成功!',
                type: 'success'
              })
              let key = 'item_' + pid + '_' + sid
              delete that.items[key]
              return false
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
      },
      cancelFormVisible() {
        this.itemModel = false
      },
      submit(formName) {
        const that = this
        that.$refs[formName].validate((valid) => {
          // 验证通过，提交
          if (valid) {
            let row = {
              design_type: that.current.sid,
              type: that.current.pid,
              project_cycle: that.form.project_cycle,
              min_price: that.form.min_price
            }

            let apiUrl = null
            let method = null

            if (that.current.itemId === 0) {
              apiUrl = api.SaveDesignItem
              method = 'post'
            } else {
              apiUrl = api.designItem.format(that.current.itemId)
              method = 'put'
            }
            that.$http({method: method, url: apiUrl, data: row})
              .then(function (response) {
                if (response.data.meta.status_code === 200) {
                  that.$message({
                    showClose: true,
                    message: '提交成功!',
                    type: 'success'
                  })
                  that.itemModel = false
                  return false
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
                console.error(error.message)
                return false
              })

            return false
          } else {
            console.error('error submit!!')
            return false
          }
        })
      }
    },
    created: function () {
      const that = this
      that.isLoading = true
      that.$http.get(api.designItems, {})
        .then(function (response) {
          that.isLoading = false
          if (response.data.meta.status_code === 200) {
            let data = response.data.data
            if (Array.isArray(data) && data.length === 0) {
              that.items['item'] = 'empty'
            } else {
              let items = {}
              for (let i = 0; i < data.length; i++) {
                let key = 'item_' + data[i].type + '_' + data[i].design_type
                items[key] = data[i]
              }

              // 重新渲染
              that.$nextTick(function () {
                that.items = items
              })
            }
          }
          that.isLoaded = true
        })
        .catch(function (error) {
          that.$message.error(error.message)
          that.isLoading = false
          console.log(error.message)
          return false
        })
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .content-box-m {
    margin: 0;
    padding: 0 15px;
  }

  .taking-info {
    margin: 0 0 20px 0;
  }

  .taking-info p {
    line-height: 2.5;
  }

  .rz-title {
    float: left;
    padding: 0 0 20px 0;
  }

  .rz-title span {
    font-size: 2.2rem;
    font-weight: 400;
  }

  .rz-stat {
    float: right;
  }

  .taking-item {
    padding: 0 0 30px 0;
  }

  .item-list {
    margin: 0 0 10px 0;
  }

  .item-list .item-name:last-child .item {
    border-bottom: none;
  }

  .item-name {
    margin: 0 0 10px 0;
  }

  .item-title {
    margin: 0 0 10px 0;
  }

  .item-title span {
    font-size: 1.8rem;
  }

  .taking-info .des {
    color: #666;
    font-size: 1.5rem;
  }

  .line {
    margin-top: 20px;
    border-bottom: 1px solid #ccc;
  }

</style>
