<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="24" type="flex" justify="center">
      <v-item-progress :progressButt="progressButt" :progressContract="progressContract" :progressItem="progressItem"></v-item-progress>

      <el-col :span="18">
        <div class="content">
          <div class="banner">
            <img v-show="statusIconUrl" class="" :src="statusIconUrl" width="50" />
            <h1>{{ item.name }}</h1>
            <p>{{ item.design_status_value }}</p>
          </div>

          <div class="select-item-box">
            <el-collapse v-model="selectCompanyCollapse" @change="selectCompanyboxChange">
              <el-collapse-item title="项目详情" name="1">

                <div class="base_info">
                  <el-table
                    :data="tableData"
                    border
                    :show-header="false"
                    style="width: 100%">
                    <el-table-column
                      prop="name"
                      width="180">
                    </el-table-column>
                    <el-table-column>
                        <template scope="scope">
                          <div v-if="scope.row.name === '相关附件'">
                            <p v-for="(d, index) in scope.row.image"><a :href="d.file" target="_blank">{{ d.name }}</a></p>
                          </div>
                          <div v-else>
                            <p>{{ scope.row.title }}</p>
                          </div>
                        </template>
                    </el-table-column>
                  </el-table>
                </div>

              </el-collapse-item>
            </el-collapse>
          </div>

          <div class="select-item-box">
            <el-collapse v-model="selectCompanyCollapse" @change="selectCompanyboxChange">
              <el-collapse-item title="报价管理" name="4">
                <div class="quotation-item">

                  <div class="item-logo">
                    <div class="fl">
                      <p class="p-title fl">
                        {{ item.company_name }}
                      </p>
                      <el-popover class="contact-popover fl contact-us" trigger="hover" placement="top">
                        <p class="contact">联系人: {{ item.contact_name }}</p>
                        <p class="contact">职位: {{ item.position }}</p>
                        <p class="contact">电话: {{ item.phone }}</p>
                        <p class="contact">邮箱: {{ item.email }}</p>
                          <p slot="reference" class="name-wrapper contact-user"><i class="fa fa-phone" aria-hidden="true"></i> 联系我们</p>
                      </el-popover>
                    </div>
                    <div class="fr item-stick-des" v-if="quotation && quotation.status === 0">
                      <p>已提交报价，等待需求方确认</p>
                    </div>
                  </div>
                  <div class="clear"></div>
                  <div class="item-bj" v-if="quotation">
                    <p>项目报价:  <span class="p-price">{{ quotation.price }} 元</span></p>
                    <p>报价说明:  {{ quotation.summary }}</p>                   
                  </div>
 
                  <div class="btn-quo" v-if="waitTakePrice">
                    <el-button @click="companyRefuseBtn">暂无兴趣</el-button>
                    <el-button class="is-custom" @click="takingBtn" type="primary">提交报价单</el-button>                      
                  </div>

                </div>

              </el-collapse-item>
            </el-collapse>
          </div>

          <div class="select-item-box" v-if="statusLabel.contract">
            <el-collapse v-model="selectCompanyCollapse" @change="selectCompanyboxChange">
              <el-collapse-item title="合同管理" name="6">
                <div class="contract-item" v-if="contract">
                  <div class="contract-left">
                    <img src="../../../../assets/images/icon/pdf2x.png" width="30" />
                    <div class="contract-content">
                      <p>{{ contract.title }}</p>
                      <p class="contract-des">{{ contract.created_at }}</p>
                    </div>
                  </div>
                  <div class="contract-right">
                    <p v-if="item.status === 5"><el-button @click="contractSendBtn" class="is-custom" type="primary" size="small" ><i class="fa fa-share-square-o" aria-hidden="true"></i> 发送</el-button></p>
                    <p><router-link :to="{name: 'vcenterContractDown', params: {unique_id: contract.unique_id}}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> 下载</router-link></p>
                    <p><router-link :to="{name: 'vcenterContractView', params: {unique_id: contract.unique_id}}" target="_blank"><i class="fa fa-eye" aria-hidden="true"></i> 预览</router-link></p>
                    <p v-if="item.status < 7"><router-link :to="{name: 'vcenterContractSubmit', params: {item_id: item.id}}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> 修改</router-link></p>

                  </div>

                </div>
                <div class="contract-item new" v-else>
                  <el-button @click="contractBtn" class="contract-btn is-custom">添写在线合同</el-button>
                </div>
              </el-collapse-item>
            </el-collapse>
          </div>

          <div class="select-item-box" v-if="statusLabel.amount">
            <el-collapse v-model="selectCompanyCollapse" @change="selectCompanyboxChange">
              <el-collapse-item title="托管项目资金" name="9">
                <div class="capital-item" v-if="statusLabel.isPay">
                  <p>项目资金</p>
                  <p class="capital-money">¥ {{ item.price }}</p>
                  <p class="pay-btn">
                      <span>项目资金已拖管 </span>
                  </p>
                </div>
                <div class="capital-item" v-else>
                  <p>项目资金</p>
                  <p class="capital-money">¥ {{ item.price }}</p>
                  <p class="pay-btn">
                      <span>等待客户支付项目资金 </span>
                  </p>
                  <p class="capital-des">＊客户需要将项目资金预先托管至太火鸟SaaS，完成后项目将自动启动并进入项目管理阶段。</p>
                </div>
              </el-collapse-item>
            </el-collapse>
          </div>

          <div class="select-item-box" v-if="statusLabel.manage">
            <el-collapse v-model="selectCompanyCollapse" @change="selectCompanyboxChange">
              <el-collapse-item title="项目管理" name="11">
                <div class="manage-item" v-if="item.status === 9">
                  <p class="wait-begin">
                      <el-button class="capital-btn is-custom" :loading="beginItemLoadingBtn" @click="beginItem" type="primary"><i class="fa fa-handshake-o" aria-hidden="true"></i> 立即开始项目</el-button>
                  </p>
                </div>
                <div class="manage-item add-stage" v-else>

                  <div class="stage-item" v-for="(d, index) in stages">
                    <div class="stage-title">
                      <h3>第{{ d.no }}阶段: {{ d.title }}</h3>
                      <p v-if="d.confirm === 0">
                        <el-upload
                          class=""
                          :action="uploadUrl"
                          :on-change="handleChange"
                          :on-progress="stageUploadProgress"
                          :on-preview="handlePreview"
                          :file-list="[]"
                          :data="uploadParam"
                          :show-file-list="false"
                          :on-error="uploadStageError"
                          :on-success="uploadStageSuccess"
                          :before-upload="beforeStageUpload"
                          list-type="text">
                          <el-button size="small" class="is-custom" @click="uplaodStageBtn" :stage_id="d.id" :index="index" type="primary">{{ stageUploadBtnMsg }}</el-button>
                        </el-upload>
                      </p>
                      <p v-if="d.status === 0"><el-button type="primary" @click="stageSendBtn" size="small" :stage_id="d.id" :index="index" class="is-custom"></i> 发送</el-button></p>
                      <p v-else>
                        <span v-if="d.confirm === 1">完成</span>
                      </p>
                    </div>
                    <div class="stage-asset-box clear" v-for="(asset, asset_index) in d.item_stage_image">
                      <div class="contract-left">
                        <img src="../../../../assets/images/icon/pdf2x.png" width="30" />
                        <div class="contract-content">
                          <p>{{ asset.name }}</p>
                          <p class="contract-des">{{ asset.created_at.date_format().format('yyyy-MM-dd') }}</p>
                        </div>
                      </div>
                      <div class="contract-right">
                        <p><a href="javascript:void(0);" @click="removeStageAsset" :asset_id="asset.id" :stage_index="index" :asset_index="asset_index" v-if="d.confirm === 0"><i class="fa fa-times" aria-hidden="true"></i> 删除</a></p>
                        <p><a :href="asset.file" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> 下载</a></p>

                      </div>
                      <div class="clear"></div>
                    </div>
                  </div>

                  <p class="finish-item-btn" v-if="sureFinishBtn"><el-button type="primary" class="is-custom" :loading="sendStageLoadingBtn" @click="sureItemBtn">项目确认完成</el-button></p>
                  <p class="finish-item-stat" v-if="item.status === 15">等待客户验收项目</p>
                  <p class="finish-item-stat" v-if="item.status === 18">项目已验收</p>
                </div>

              </el-collapse-item>
            </el-collapse>
          </div>

        </div>

      </el-col>
    </el-row>

    <el-dialog title="提交项目报价" v-model="takingPriceDialog">
      <el-form label-position="top" :model="takingPriceForm" :rules="takingPriceRuleForm" ref="takingPriceRuleForm">
        <el-form-item label="项目报价" prop="price" label-width="200px">
          <el-input type="text" v-model.number="takingPriceForm.price" :placeholder="item.design_cost_value" auto-complete="off" ></el-input>
        </el-form-item>
        <el-form-item label="报价说明" prop="summary" label-width="80px">
          <el-input type="textarea" v-model="takingPriceForm.summary" :autosize="{ minRows: 2, maxRows: 8}" auto-complete="off"></el-input>
        </el-form-item>
        <div class="taking-price-btn">
          <el-button @click="takingPriceDialog = false">取 消</el-button>
          <el-button type="primary" :loading="isTakingLoadingBtn" class="is-custom" @click="takingPriceSubmit('takingPriceRuleForm')">确 定</el-button>
        </div>

      </el-form>
    </el-dialog>

    <el-dialog
      title="提示"
      v-model="comfirmDialog"
      size="tiny">
      <span>{{ comfirmMessage }}</span>
      <span slot="footer" class="dialog-footer">
        <input type="hidden" ref="comfirmType" value="1" />
        <input type="hidden" ref="confirmTargetId" />
        <input type="hidden" ref="confirmIndex" />
        <el-button @click="comfirmDialog = false">取 消</el-button>
        <el-button type="primary" :loading="comfirmLoadingBtn" @click="sureComfirmSubmit">确 定</el-button>
      </span>
    </el-dialog>

  </div>
</template>

<script>
import api from '@/api/api'
import vItemProgress from '@/components/block/ItemProgress'
export default {
  name: 'vcenter_item_show',
  components: {
    vItemProgress
  },
  data () {
    return {
      showStickCompanyBtn: true,
      comfirmLoadingBtn: false,
      comfirmDialog: false,
      sendStageLoadingBtn: false,
      comfirmMessage: '确认执行此操作?',
      stickCompanyIds: [],
      stages: [],
      secondPayLoadingBtn: false,
      item: {},
      info: {},
      quotation: null,
      contract: null,
      isLoadingBtn: false,
      selectCompanyCollapse: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '11', '12', '15'],
      statusIconUrl: null,
      beginItemLoadingBtn: false,
      waitTakePrice: false,
      takingPriceDialog: false,
      isTakingLoadingBtn: false,
      sureFinishBtn: false,
      stageUploadBtnMsg: '上传附件',
      statusLabel: {
        detail: true,
        selectCompany: false,
        trueCompany: false,
        cooperateCompany: false,
        contract: false,
        amount: false,
        isPay: false,
        manage: false,
        stage: false,
        evaluate: false,

        end: false
      },
      takingPriceForm: {
        itemId: '',
        price: '',
        summary: ''
      },
      takingPriceRuleForm: {
        price: [
          { required: true, type: 'number', message: '请添写报价金额,必须为整数', trigger: 'blur' }
        ],
        summary: [
          { required: true, message: '请添写报价说明', trigger: 'blur' }
        ]
      },
      uploadParam: {
        'token': '',
        'x:random': '',
        'x:user_id': this.$store.state.event.user.id,
        'x:target_id': '',
        'x:type': ''
      },
      currentStageIndex: '',
      tableData: [],
      stickCompany: [],
      offerCompany: [],
      company: null,
      hasOfferCompany: false,
      progressButt: 0,
      progressContract: -1,
      progressItem: -1,
      msg: ''
    }
  },
  methods: {
    selectCompanyboxChange() {
    },
    // 提交项目报价
    takingPriceSubmit(formName) {
      var self = this
      self.$refs[formName].validate((valid) => {
        // 验证通过，提交
        if (valid) {
          self.isTakingLoadingBtn = true
          var row = {
            item_demand_id: self.item.id,
            price: self.takingPriceForm.price,
            summary: self.takingPriceForm.summary
          }

          var apiUrl = api.addQuotation
          var method = 'post'

          self.$http({method: method, url: apiUrl, data: row})
          .then (function(response) {
            if (response.data.meta.status_code === 200) {
              self.$message.success('提交成功！')
              self.isTakingLoadingBtn = false
              self.takingPriceDialog = false
              self.waitTakePrice = false
              self.quotation = row
              self.quotation.status = 0
            } else {
              self.$message.error(response.data.meta.message)
              self.isTakingLoadingBtn = false
            }
          })
          .catch (function(error) {
            self.$message.error(error.message)
            self.isTakingLoadingBtn = false
            return false
          })
        } else {
        }
      })
    },
    stickCompanySubmit() {
      var companyIds = this.stickCompanyIds
      var self = this
      self.isLoadingBtn = true
      self.$http.post(api.demandPush, {item_id: self.item.id, design_company_id: companyIds})
      .then (function(response) {
        self.isLoadingBtn = false
        if (response.data.meta.status_code === 200) {
          self.$message.success('操作成功，等待设计公司接单!')
          self.item.status = 4
          self.item.status_value = '等待设计公司接单'
          self.statusLabel.selectCompany = false
          self.statusLabel.trueCompany = true
        } else {
          self.$message.error(response.data.meta.message)
        }
      })
      .catch (function(error) {
        self.$message.error(error.message)
        self.isLoadingBtn = false
      })
    },
    // 项目报价弹出层
    takingBtn(event) {
      this.takingPriceDialog = true
    },
    // 对话框确认按钮
    sureComfirmSubmit() {
      var comfirmType = parseInt(this.$refs.comfirmType.value)
      var confirmTargetId = parseInt(this.$refs.confirmTargetId.value)
      var confirmIndex = parseInt(parseInt(this.$refs.confirmIndex.value))
      this.comfirmLoadingBtn = true
      if (comfirmType === 1) {
        this.sureItemSubmit()
      } else if (comfirmType === 2) {
        this.sureRefuseItemSubmit()
      } else if (comfirmType === 3) {
        this.contractSendDo()
      } else if (comfirmType === 4) {
        this.stageSend(confirmTargetId, confirmIndex)
      } else {
        this.comfirmLoadingBtn = false
      }
    },
    // 拒绝项目确认框
    companyRefuseBtn(event) {
      this.$refs.comfirmType.value = 2
      this.comfirmMessage = '确认拒绝该项目？'
      this.comfirmDialog = true
    },
    // 确认拒绝项目
    sureRefuseItemSubmit() {
      var self = this
      this.comfirmLoadingBtn = true
      self.$http({method: 'get', url: api.companyRefuseItemId.format(self.item.id), data: {}})
      .then (function(response) {
        self.comfirmLoadingBtn = false
        self.comfirmDialog = false
        if (response.data.meta.status_code === 200) {
          self.$message.success('提交成功！')
          self.$router.replace({name: 'vcenterCItemList'})
          return
        } else {
          self.$message.error(response.data.meta.message)
        }
      })
      .catch (function(error) {
        self.$message.error(error.message)
        self.comfirmLoadingBtn = false
        self.comfirmDialog = false
      })
    },
    // 发送合同确认框
    contractSendBtn(event) {
      this.$refs.comfirmType.value = 3
      this.comfirmMessage = '确认把合同发送给需求方？'
      this.comfirmDialog = true
    },
    // 发送合同执行
    contractSendDo() {
      var self = this
      self.$http({method: 'post', url: api.sendContract, data: {item_demand_id: self.item.id}})
      .then (function(response) {
        self.comfirmLoadingBtn = false
        self.comfirmDialog = false
        if (response.data.meta.status_code === 200) {
          self.$message.success('发送成功！')
          self.item.status = 6
          self.item.status_value = '等待需求方确认合同'
        } else {
          self.$message.error(response.data.meta.message)
        }
      })
      .catch (function(error) {
        self.$message.error(error.message)
      })
    },
    // 确认项目完成
    sureItemSubmit() {
      var self = this
      self.$http.post(api.designItemDoneId.format(self.item.id), {})
      .then (function(response) {
        self.comfirmDialog = false
        if (response.data.meta.status_code === 200) {
          self.comfirmLoadingBtn = false
          self.item.status = 15
          self.sureFinishBtn = false
          self.item.statue_value = '项目已完成'
          self.$message.success('操作成功!')
        } else {
          self.$message.error(response.data.meta.message)
        }
      })
      .catch (function(error) {
        self.$message.error(error.message)
        self.comfirmLoadingBtn = false
      })
    },
    // 新增／编辑合同
    contractBtn() {
      this.$router.push({name: 'vcenterContractSubmit', params: {item_id: this.item.id}})
    },
    // 开始项目
    beginItem() {
      var self = this
      this.beginItemLoadingBtn = true
      self.$http.post(api.designItemStartId.format(self.item.id), {})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          self.$message.success('操作成功!')
          self.item.status = 11
          self.item.status_value = '项目进行中'
        } else {
          self.$message.error(response.data.meta.message)
        }
      })
      .catch (function(error) {
        self.$message.error(error.message)
        self.beginItemLoadingBtn = false
      })
    },
    // 发送阶段确认框
    stageSendBtn(event) {
      var stageId = parseInt(event.currentTarget.getAttribute('stage_id'))
      var index = parseInt(event.currentTarget.getAttribute('index'))
      this.$refs.confirmTargetId.value = stageId
      this.$refs.confirmIndex.value = index
      this.$refs.comfirmType.value = 4
      this.comfirmMessage = '确认把阶段信息发送给需求方？'
      this.comfirmDialog = true
    },
    // 项目阶段发送
    stageSend(id, index) {
      var self = this
      self.$http.put(api.itemStageSend, {id: id})
      .then (function(response) {
        self.comfirmLoadingBtn = false
        self.comfirmDialog = false
        if (response.data.meta.status_code === 200) {
          self.$message.success('发送成功!')
          self.stages[index].status = 1
        } else {
          self.$message.error(response.data.meta.message)
        }
      })
      .catch (function(error) {
        self.comfirmLoadingBtn = false
        self.$message.error(error.message)
      })
    },
    // 确认项目完成弹出层
    sureItemBtn() {
      this.$refs.comfirmType.value = 1
      this.comfirmMessage = '确认项目已完成？'
      this.comfirmDialog = true
    },
    // 删除阶段附件
    removeStageAsset(event) {
      var assetId = parseInt(event.currentTarget.getAttribute('asset_id'))
      var stageIndex = parseInt(event.currentTarget.getAttribute('stage_index'))
      var assetIndex = parseInt(event.currentTarget.getAttribute('asset_index'))
      const that = this
      that.$http.delete(api.asset.format(assetId), {})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          that.stages[stageIndex].item_stage_image.splice(assetIndex, 1)
        } else {
          that.$message.error(response.data.meta.message)
        }
      })
      .catch (function(error) {
        that.$message.error(error.message)
        return false
      })
    },
    // 上传阶段附件
    uplaodStageBtn(event) {
      var stageId = parseInt(event.currentTarget.getAttribute('stage_id'))
      var index = parseInt(event.currentTarget.getAttribute('index'))
      this.currentStageIndex = index
      this.uploadParam['x:type'] = 8
      this.uploadParam['x:target_id'] = stageId
      this.stageUploadBtnMsg = '上传中...'
    },
    stageUploadProgress(event, file, fileList) {
    },
    // Before上传阶段附件
    beforeStageUpload(file) {
      const arr = ['image/jpeg', 'image/gif', 'image/png', 'application/pdf']
      const isLt10M = file.size / 1024 / 1024 < 10

      if (arr.indexOf(file.type) === -1) {
        this.$message.error('上传文件格式不正确!')
        return false
      }
      if (!isLt10M) {
        this.$message.error('上传文件大小不能超过 10MB!')
        return false
      }
    },
    uploadStageSuccess(response, file, fileList) {
      this.stageUploadBtnMsg = '上传附件'
      var index = this.currentStageIndex
      var row = {
        id: response.asset_id,
        name: response.name,
        file: response.file,
        created_at: response.created_at
      }
      this.stages[index].item_stage_image.push(row)
    },
    uploadStageError(err, file, fileList) {
      this.stageUploadBtnMsg = '上传附件'
      this.$message.error(err)
    },
    handlePreview(file) {
    },
    handleChange(value) {
    }
  },
  computed: {
    selectedStickCompanyCount() {
      var cnt = this.stickCompanyIds.length
      if (cnt > 0) {
        this.showStickCompanyBtn = false
      } else {
        this.showStickCompanyBtn = true
      }
      return cnt
    }
  },
  created: function() {
    var id = this.$route.params.id
    if (!id) {
      this.$message.error('缺少请求参数!')
      this.$router.push({name: 'home'})
      return
    }
    var uType = this.$store.state.event.user.type
    // 如果是设计公司，跳到设计公司项目详情
    if (uType !== 2) {
      this.$router.replace({name: 'vcenterItemShow'})
      return
    }

    const self = this
    self.$http.get(api.designItemId.format(id), {})
    .then (function(response) {
      if (response.data.meta.status_code === 200) {
        console.log(response.data.data)
        self.item = response.data.data.item
        // self.info = response.data.data.info
        self.contract = response.data.data.contract
        if (self.contract) {
          self.contract.created_at = self.contract.created_at.date_format().format('yyyy-MM-dd')
        }
        self.quotation = response.data.data.quotation
        // 是否显示提交报价单按钮
        if (self.quotation === null && (self.item.status === 3 || self.item.status === 4)) {
          self.waitTakePrice = true
        }
        switch (self.item.status) {
          case 4: // 查看已提交报价的设计公司
            self.progressButt = 2
            self.progressContract = -1
            self.progressItem = -1
            self.statusIconUrl = require('@/assets/images/item/wait_taking.png')
            self.statusLabel.trueCompany = true
            self.$http.get(api.demandItemDesignListItemId.format(self.item.id), {})
            .then (function(response) {
              if (response.data.meta.status_code === 200) {
                var offerCompany = response.data.data
                console.log(offerCompany)
                for (var i = 0; i < offerCompany.length; i++) {
                  var item = offerCompany[i]
                  // 是否存在已提交报价的公司
                  if (item.design_company_status === 2) {
                    self.hasOfferCompany = true
                  }
                  if (item.design_company.logo_image && item.design_company.logo_image.length !== 0) {
                    offerCompany[i].design_company.logo_url = item.design_company.logo_image.logo
                  } else {
                    offerCompany[i].design_company.logo_url = false
                  }
                } // endfor
                self.offerCompany = offerCompany
              }
            })
            .catch (function(error) {
              self.$message.error(error.message)
            })
            break
          case 5: // 等待提交合同
            self.progressButt = 2
            self.progressContract = 0
            self.progressItem = -1
            self.statusIconUrl = require('@/assets/images/item/wait_submit_ht.png')
            self.statusLabel.cooperateCompany = true
            self.statusLabel.contract = true
            break
          case 6: // 等待确认合同
            self.progressButt = 2
            self.progressContract = 1
            self.progressItem = -1
            self.statusLabel.cooperateCompany = true
            self.statusLabel.contract = true
            self.statusIconUrl = require('@/assets/images/item/wait_sure_ht.png')
            break
          case 7: // 已确认合同
            self.progressButt = 2
            self.progressContract = 2
            self.progressItem = -1
            self.statusIconUrl = require('@/assets/images/item/sure_ht.png')
            self.statusLabel.cooperateCompany = true
            self.statusLabel.contract = true
            self.statusLabel.amount = true
            break
          case 8: // 等待托管资金
            self.progressButt = 2
            self.progressContract = 2
            self.progressItem = -1
            self.statusIconUrl = require('@/assets/images/item/wait_pay.png')
            self.statusLabel.cooperateCompany = true
            self.statusLabel.contract = true
            self.statusLabel.amount = true
            break
          case 9: // 项目资金已拖管
            self.progressButt = 2
            self.progressContract = 3
            self.progressItem = -1
            self.statusIconUrl = require('@/assets/images/item/item_ing.png')
            self.statusLabel.cooperateCompany = true
            self.statusLabel.contract = true
            self.statusLabel.amount = true
            self.statusLabel.isPay = true
            self.statusLabel.manage = true
            break
          case 11:  // 项目进行中
            self.progressButt = 3
            self.progressContract = 3
            self.progressItem = 0
            self.statusIconUrl = require('@/assets/images/item/write_icon.png')
            self.statusLabel.cooperateCompany = true
            self.statusLabel.contract = true
            self.statusLabel.amount = true
            self.statusLabel.isPay = true
            self.statusLabel.manage = true
            self.statusLabel.stage = true
            break
          case 15:  // 项目完成
            self.progressButt = 3
            self.progressContract = 3
            self.progressItem = 1
            self.statusIconUrl = require('@/assets/images/item/item_finish.png')
            self.statusLabel.cooperateCompany = true
            self.statusLabel.contract = true
            self.statusLabel.amount = true
            self.statusLabel.isPay = true
            self.statusLabel.manage = true
            self.statusLabel.stage = true
            break
          case 18:
            self.progressButt = 3
            self.progressContract = 3
            self.progressItem = 2
            self.statusIconUrl = require('@/assets/images/item/item_yanshou.png')
            self.statusLabel.cooperateCompany = true
            self.statusLabel.contract = true
            self.statusLabel.amount = true
            self.statusLabel.isPay = true
            self.statusLabel.manage = true
            self.statusLabel.stage = true
            break
          case 20:
            self.progressButt = 3
            self.progressContract = 3
            self.progressItem = 4
            self.statusIconUrl = require('@/assets/images/item/item_success.png')
            self.statusLabel.cooperateCompany = true
            self.statusLabel.contract = true
            self.statusLabel.amount = true
            self.statusLabel.isPay = true
            self.statusLabel.manage = true
            self.statusLabel.stage = true
            self.statusLabel.evaluate = true
            break
          default:
        }

        // 合作的公司
        if (self.statusLabel.cooperateCompany) {
          self.$http.get(api.designCompanyId.format(self.item.design_company_id), {})
          .then (function(response) {
            if (response.data.meta.status_code === 200) {
              self.company = response.data.data
              var logoUrl = null
              if (self.company.logo_image) {
                logoUrl = self.company.logo_image.logo
              }
              self.company.logo_url = logoUrl
              console.log(self.company)
            }
          })
          .catch (function(error) {
            self.$message.error(error.message)
          })
        }

        // 项目阶段列表
        if (self.statusLabel.stage) {
          self.$http.get(api.itemStageDesignCompanyLists, {params: {item_id: self.item.id}})
          .then (function(response) {
            if (response.data.meta.status_code === 200) {
              var items = response.data.data
              var isAllPass = true
              for (var i = 0; i < items.length; i++) {
                var item = items[i]
                if (item.sort) {
                  switch (item.sort) {
                    case 1:
                      items[i].no = '一'
                      break
                    case 2:
                      items[i].no = '二'
                      break
                    case 3:
                      items[i].no = '三'
                      break
                    case 4:
                      items[i].no = '四'
                      break
                    case 5:
                      items[i].no = '五'
                      break
                    default:
                      items[i].no = ''
                  }
                } else {
                  items[i].no = ''
                }

                items[i].created_at = item.created_at.date_format().format('yyyy-MM-dd')
                if (item.confirm === 0) {
                  isAllPass = false
                }
              } // endfor
              if (self.item.status === 11 && isAllPass) {
                self.sureFinishBtn = true
              }
              self.stages = items
              console.log('aa')
              console.log(self.stages)
            }
          })
          .catch (function(error) {
            self.$message.error(error.message)
          })
        }

        var tab = []
        if (self.item.type === 1) {
          tab = [
            {
              name: '项目类型',
              title: self.item.type_value
            },
            {
              name: '设计类别',
              title: self.item.design_type_value
            },
            {
              name: '产品领域',
              title: self.item.field_value
            },
            {
              name: '所属行业',
              title: self.item.industry_value
            }
          ]
        } else if (self.item.type === 2) {
          tab = [
            {
              name: '项目类型',
              title: self.item.type_value
            },
            {
              name: '设计类别',
              title: self.item.design_type_value
            }
          ]
        }
        var itemTab = [{
          name: '项目预算',
          title: self.item.design_cost_value
        }, {
          name: '项目周期',
          title: self.item.cycle_value
        }, {
          name: '工作地点',
          title: self.item.province_value + ', ' + self.item.city_value
        }, {
          name: '相关附件',
          title: 'file',
          image: self.item.image
        }]

        self.tableData = tab.concat(itemTab)

        console.log(self.item)
      }
    })
    .catch (function(error) {
      self.$message.error(error.message)
      console.log(error.message)
    })

    // 获取图片token
    self.$http.get(api.upToken, {})
    .then (function(response) {
      if (response.data.meta.status_code === 200) {
        if (response.data.data) {
          self.uploadParam['token'] = response.data.data.upToken
          self.uploadParam['x:random'] = response.data.data.random
          self.uploadUrl = response.data.data.upload_url
        }
      }
    })
    .catch (function(error) {
      self.$message.error(error.message)
    })
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  
  .content {
  }
  .banner {
    height: 200px;
    text-align: center;
    margin-bottom: 20px;
    border: 1px solid #ccc;
  }
  .banner img {
    margin-top: 40px;
  }
  .banner h1 {
    padding-top: 10px;
    font-size: 1.5rem;
    color: #222;
  }
  .banner p {
    font-size: 1rem;
    color: #666;
    margin: 10px;
  }
  .base_info {
  }

  .el-step__title.is-finish {
    font-size: 3rem;
  }

  .select-item-box {
    margin: 20px 0 20px 0;
  }

  .select-company-item {
    height: 200px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
  }
  .select-company-item .check-box {
    margin: 10px;
    line-height: 160px;
    float: left;
  }

  .select-company-item .company-title {
    width: 350px;
    height: 160px;
    float: left;
    overflow: hidden;
  }
  .select-company-item .content .img {
    height: 180px;
    display: block;
    margin: 10px;
    float: left;
  }
  .select-company-item .content h3 {
    color: #222;
    font-weight: bold;
  }
  .select-company-item .content p {
    color: #666;
    font-size: 1rem;
  }
  .select-company-item .content p span {
    color: #222;
    font-size: 1.2rem;
    font-weight: bold;
  }
  .company-title {
    margin: 10px;
  }
  .select-company-item .case-box {
    margin: 10px;
    float: left;
    padding-top: 45px;
  }
  .select-company-item .case-box a {
  }
  .pub-btn {
    text-align: center;
  }

  .quotation-item {
    border: 1px solid #ccc;
    margin: 20px 0 20px 0;
  }
  .item-logo {
    margin: 0 10px 0 10px;
  }
  .item-logo .p-title {
    line-height: 50px;
    font-size: 1.5rem;
    font-weight: 500;
  }
  .item-logo p {
    line-height: 2;
    color: #333;
  }
  .item-logo img {
    margin-bottom: 10px;
  }

  .item-bj {
    padding: 15px 10px 15px 10px;
    border-top: 1px solid #ccc;
  }

  .item-title {
    margin-left: -30px;
    height: 150px;
  }
  .item-title p {
    line-height: 2;
    color: #666;
  }
  .item-title .p-title {
    color: #333;
    font-size: 1.5rem;
    font-weight: bold;
    line-height: 50px;
    margin-bottom: 8px;
  }
  .item-title .p-price {
    color: #FF5A5F;
    font-size: 1.8rem;
    font-weight: bold;
    margin-bottom: -3px;
  }
  .line {
    border-top: 1px solid #ccc;
  }
  .btn {
    padding-right: 10px;
    text-align: right;
    line-height: 50px;
  }

  .btn-quo {
    text-align: right;
    padding: 10px;
    border-top: 1px solid #ccc;
  }

  .contract-item {
    height: 50px;
    margin: 20px 0 10px 0;
    padding: 15px 0 5px 0;
    border-top: 1px solid #ccc;
    border-bottom: 1px solid #ccc;
  }
  .contract-item.new {
    text-align: center;
    border: none;
  }
  .contract-left {
    float: left;
  }
  .contract-left img {
    float: left;
  }
  .contract-content {
    float: left;
    margin: 0 0 0 10px;
  }
  .contract-content p {
    font-size: 1rem;
    color: #666;
    line-height: 1.5;
  }
  .contract-des {
    font-size: 1rem;
  }
  .contract-right {
    float: right;
    margin-right: 10px;
  }
  .contract-right p {
    float: right;
    margin: 5px 0 5px 10px;
    line-height: 30px;
  }

  .no-offer-company {
    text-align: center;
  }
  .no-offer-company p {
    font-size: 1.2rem;
    color: #666;
  }

  .capital-item {
    margin: 20px 0 10px 0;
  }

  .capital-item p {
    text-align: center;
    line-height: 1.3;
  }
  .capital-money {
    color: #FF5A5F;
    font-size: 2.5rem;
  }
  .capital-des {
    color: #666;
    font-size: 1rem;
  }
  .capital-item .pay-btn {
    font-size: 1.8rem;
    margin: 10px 0 20px 0;
  }
  .capital-item .capital-btn {
    padding: 10px 30px 10px 30px;
  }

  .manage-item {
    min-height: 80px;
    text-align: center;
  }
  .manage-item .wait-begin {
    margin: 30px 0 0 0;
    font-size: 1.8rem;
  }
  .manage-item.add-stage {
    min-height: 80px;
    text-align: left;
  }
  .add-stage p {
  
  }
  .add-stage-btn {
    margin-bottom: 20px;
  }
  .finish-item-btn {
    margin-top: 50px;
    margin-bottom: 20px;
    text-align: center;
  }
  .finish-item-btn button {
    padding: 10px 60px 10px 60px;
  }
  .finish-item-stat {
    margin-top: 20px;
    font-size: 2rem;
    text-align: center;
  }

  .contact-us p{
    margin-left: 20px;
    line-height: 50px;
  }

  p.contact {
    line-height: 1.5;
    font-size: 1.3rem;
    color: #666;
  }
  .item-stick-des p{
    line-height: 50px;
  }
  .stage-item {
    margin: 20px 0 20px 0;
  }
  .stage-title {
    height: 40px;
    border-bottom: 1px solid #ccc;
  }
  .stage-item .stage-title h3 {
    float: left;
    font-size: 1.8rem;
    color: #222;
  }

  .stage-title p {
    margin: 0 0 0 10px;
    float: right;
  }

  .stage-asset-box {
    padding: 10px 0 10px 0;
    border-bottom: 1px solid #ccc;
  }
  .taking-price-btn {
    float: right;
    margin-bottom: 20px;
  }

</style>

