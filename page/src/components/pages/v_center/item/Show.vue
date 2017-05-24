<template>
  <div class="container">

    <el-row :gutter="24" type="flex" justify="center">
      <v-item-progress :progressButt="progressButt" :progressContract="progressContract" :progressItem="progressItem"></v-item-progress>

      <el-col :span="19">
        <div class="content">
          <div class="banner">
            <img class="" src="../../../../assets/images/icon/item_status.png" width="80" />
            <h1>{{ item.name }}</h1>
            <p>{{ item.status_value }}</p>
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
                    <el-table-column
                      prop="title">
                    </el-table-column>
                  </el-table>
                </div>

              </el-collapse-item>
            </el-collapse>
          </div>



          <div class="select-item-box" v-if="statusLabel.selectCompany">
            <el-collapse v-model="selectCompanyCollapse" @change="selectCompanyboxChange">
              <el-collapse-item title="选择系统推荐的设计公司" name="3">

                <div class="select-company-item" v-for="d in stickCompany">
                  <el-checkbox class="check-box" v-model="stickCompanyIds" :label="d.id">&nbsp;</el-checkbox>
                  <div class="content">
                    <div class="img">
                      <img class="avatar" v-if="d.logo_url" :src="d.logo_url" width="50" />                     
                      <img class="avatar" v-else src="../../../../assets/images/avatar_100.png" width="50" />
                    </div>
                    <div class="company-title">
                      <h3><router-link :to="{name: 'companyShow', params: {id: d.id}}" target="_blank">{{ d.company_name }}</router-link></h3>
                      <p><i class="fa fa-map-marker" aria-hidden="true"></i> {{ d.city_arr.join(',') }}</p>
                      <p class="des"><span>类型: </span>{{ d.item_type_label }}</p>
                      <p class="des"><span>优势: </span>{{ d.professional_advantage }}</p>
                    </div>
                    <div class="case-box">
                      <a href=""><img width="150" src="https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1494248440981&di=f53c08dedb755a0d16b558b4feeaefdf&imgtype=0&src=http%3A%2F%2Fimg.mb.moko.cc%2F2016-03-28%2F7bc398f3-fa53-4ddb-b5a7-a250635acd32.jpg%3FimageView2%2F2%2Fw%2F915%2Fh%2F915" /></a>
                      <a href=""><img width="150" src="https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1494248440981&di=f53c08dedb755a0d16b558b4feeaefdf&imgtype=0&src=http%3A%2F%2Fimg.mb.moko.cc%2F2016-03-28%2F7bc398f3-fa53-4ddb-b5a7-a250635acd32.jpg%3FimageView2%2F2%2Fw%2F915%2Fh%2F915" /></a>

                    </div>
                  </div>
                </div>
                <div class="clear"></div>
                <div class="pub-btn" v-if="item.status === 3">
                  <el-button class="is-custom" @click="stickCompanySubmit" :loading="isLoadingBtn" :disabled="showStickCompanyBtn" type="primary">已选中<span>{{ selectedStickCompanyCount }}</span>家，推送给设计公司</el-button>
                </div>
              </el-collapse-item>
            </el-collapse>         
          </div>

          <div class="select-item-box" v-if="statusLabel.trueCompany">
            <el-collapse v-model="selectCompanyCollapse" @change="selectCompanyboxChange">
              <el-collapse-item title="选择参与报价的设计公司" name="4">
                <div v-if="hasOfferCompany">
                  <div class="offer-company-item" v-for="(d, index) in offerCompany" v-if="d.design_company_status === 2">
                    <el-row :gutter="24">
                      <el-col :span="3" class="item-logo">
                        <img class="avatar" v-if="d.design_company.logo_url" :src="d.design_company.logo_url" width="50" />
                        <img class="avatar" v-else src="../../../../assets/images/avatar_100.png" width="50" />
                        <p>项目报价: </p>
                        <p>报价说明: </p>
                      </el-col>

                      <el-col :span="21" class="item-title">
                        <p class="p-title">
                          <router-link :to="{name: 'companyShow', params: {id: d.design_company.id}}" target="_blank" :class="">{{ d.design_company.company_name }}</router-link>
                        </p>
                        <p class="p-price">{{ d.quotation.price }} 元</p>
                        <p>{{ d.quotation.summary }}</p>
                      </el-col>
                    </el-row>
                    <div class="line"></div>
                    <div class="btn" v-if="d.item_status === 0">
                      <el-button @click="refuseCompanyBtn" :index="index" :company_id="d.design_company.id">拒绝此单</el-button>
                      <el-button class="is-custom" @click="greeCompanyBtn" :index="index" :company_id="d.design_company.id" type="primary">确认合作</el-button> 
                    </div>
                    <div class="btn" v-if="d.item_status === -1">
                      <p>已拒绝该公司报价</p>
                    </div>
                    <div class="btn" v-if="d.item_status === 1">
                      <p>确认合作</p>
                    </div>

                  </div>
                </div>
                <div class="no-offer-company" v-else>
                  <p>还没有设计公司提交报价，请耐心等待</p>
                </div>
              </el-collapse-item>
            </el-collapse>
          </div>

          <div class="select-item-box" v-if="statusLabel.cooperateCompany">
            <el-collapse v-model="selectCompanyCollapse" @change="selectCompanyboxChange">
              <el-collapse-item title="合作的设计公司" name="5">
                <div class="offer-company-item" v-if="company">
                  <el-row :gutter="24">
                    <el-col :span="3" class="item-logo">
                      <img class="avatar" v-if="company.logo_url" :src="company.logo_url" width="50" />
                      <img class="avatar" v-else src="../../../../assets/images/avatar_100.png" width="50" />
                      <p>项目报价: </p>
                      <p>报价说明: </p>
                    </el-col>

                    <el-col :span="21" class="item-title">
                      <p class="p-title">
                        <router-link :to="{name: 'companyShow', params: {id: company.id}}" target="_blank" :class="">{{ company.company_name }}</router-link>
                      </p>
                      <p class="p-price">{{ quotation.price }} 元</p>
                      <p>{{ quotation.summary }}</p>
                    </el-col>
                  </el-row>

                  <!--
                  <div class="line"></div>
                  <div class="btn">
                    <p></p>
                  </div>
                  -->
                </div>

              </el-collapse-item>
            </el-collapse>
          </div>

          <div class="select-item-box" v-if="statusLabel.contract">
            <el-collapse v-model="selectCompanyCollapse" @change="selectCompanyboxChange">
              <el-collapse-item title="合同管理" name="6">
                <div class="contract-item">
                  <div class="contract-left">
                    <img src="../../../../assets/images/icon/pdf2x.png" width="30" />
                    <div class="contract-content">
                      <p>{{ contract.title }}</p>
                      <p class="contract-des">{{ contract.created_at }}</p>
                    </div>
                  </div>
                  <div class="contract-right">
                    <p v-show="contract.status === 1"><router-link :to="{name: 'vcenterContractDown', params: {unique_id: contract.unique_id}}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> 下载</router-link></p>
                    <p><router-link :to="{name: 'vcenterContractView', params: {unique_id: contract.unique_id}}" target="_blank"><i class="fa fa-eye" aria-hidden="true"></i> 预览</router-link></p>
                  </div>

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
                      <el-button class="capital-btn is-custom" :loading="secondPayLoadingBtn" @click="secondPay" type="primary"><i class="fa fa-money" aria-hidden="true"></i> 立即支付</el-button>
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
                  <p class="wait-begin">等待设计公司开始项目</p>
                </div>
                <div class="manage-item add-stage" v-else>
                  <div v-for="(d, index) in stages">
                    <div class="contract-left">
                      <img src="../../../../assets/images/icon/pdf2x.png" width="30" />
                      <div class="contract-content">
                        <p>{{ d.title }}</p>
                        <p class="contract-des">{{ d.created_at }}</p>
                      </div>
                    </div>
                    <div class="contract-right">
                      <p><router-link :to="{name: 'vcenterDesignStageShow', params: {id: d.id}}" target="_blank"><i class="fa fa-eye" aria-hidden="true"></i> 预览</router-link></p>
                    </div>
                    <div class="clear"></div>
                  </div>
                  <p class="finish-item-btn" v-if="item.status === 15"><el-button type="primary" class="is-custom" :loading="sendStageLoadingBtn" @click="sureItemBtn">项目确认完成</el-button></p>
                  <p class="finish-item-btn" v-if="item.status === 18"><el-button type="primary" class="is-custom">项目交易成功,给设计公司评价</el-button></p>
                </div>
              </el-collapse-item>
            </el-collapse>
          </div>

        
        </div>

      </el-col>
    </el-row>

    <el-dialog
      title="提示"
      v-model="comfirmDialog"
      size="tiny">
      <span>{{ comfirmMessage }}</span>
      <span slot="footer" class="dialog-footer">
        <input type="hidden" ref="companyId" />
        <input type="hidden" ref="comfirmType" value="1" />
        <input type="hidden" ref="currentIndex" />
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
      comfirmMessage: '确认执行此操作?',
      stickCompanyIds: [],
      stages: [],
      secondPayLoadingBtn: false,
      item: {},
      info: {},
      contract: {},
      isLoadingBtn: false,
      selectCompanyCollapse: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '11', '15'],
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

        end: false
      },
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
    refuseCompanyBtn(event) {
      var companyId = parseInt(event.currentTarget.getAttribute('company_id'))
      var index = parseInt(event.currentTarget.getAttribute('index'))
      this.$refs.companyId.value = companyId
      this.$refs.currentIndex.value = index
      this.$refs.comfirmType.value = 1
      this.comfirmMessage = '您确定要拒绝此公司报价？'
      this.comfirmDialog = true
    },
    greeCompanyBtn(event) {
      var companyId = parseInt(event.currentTarget.getAttribute('company_id'))
      var index = parseInt(event.currentTarget.getAttribute('index'))
      this.$refs.companyId.value = companyId
      this.$refs.currentIndex.value = index
      this.$refs.comfirmType.value = 2
      this.comfirmMessage = '与该公司合作后将不可修改，确认执行此操作？'
      this.comfirmDialog = true
    },
    sureComfirmSubmit() {
      var comfirmType = parseInt(this.$refs.comfirmType.value)
      this.comfirmLoadingBtn = true
      if (comfirmType === 1) {
        this.refuseCompanySubmit()
      } else if (comfirmType === 2) {
        this.agreeCompanySubmit()
      } else if (comfirmType === 3) {
        this.sureItemSubmit()
      } else {
        this.comfirmLoadingBtn = false
      }
    },
    // 拒绝设计公司报价提交
    refuseCompanySubmit() {
      var currentIndex = this.$refs.currentIndex.value
      var companyId = this.$refs.companyId.value
      var self = this
      self.$http.post(api.refuseDesignPrice, {item_id: self.item.id, design_company_id: companyId})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          self.comfirmLoadingBtn = false
          self.comfirmDialog = false
          self.$message.success('操作成功!')
          // self.offerCompany.splice(currentIndex, 1)
          self.offerCompany[currentIndex].item_status = -1
        } else {
          self.comfirmLoadingBtn = false
          self.$message.error(response.data.meta.message)
        }
      })
      .catch (function(error) {
        self.$message.error(error.message)
        self.comfirmLoadingBtn = false
      })
    },
    // 同意设计公司报价, 开始合作
    agreeCompanySubmit() {
      var currentIndex = this.$refs.currentIndex.value
      var companyId = this.$refs.companyId.value
      var self = this
      self.$http.post(api.agreeDesignCompany, {item_id: self.item.id, design_company_id: companyId})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          self.comfirmLoadingBtn = false
          self.comfirmDialog = false
          self.$message.success('操作成功!')
          // self.offerCompany.splice(currentIndex, 1)
          self.offerCompany[currentIndex].item_status = 1
        } else {
          self.comfirmLoadingBtn = false
          self.$message.error(response.data.meta.message)
        }
      })
      .catch (function(error) {
        self.$message.error(error.message)
        self.comfirmLoadingBtn = false
      })
    },
    // 支付项目资金
    secondPay() {
      this.$router.push({name: 'itemPayFund', params: {item_id: this.item.id}})
    },
    // 确认项目完成弹出层
    sureItemBtn() {
      this.$refs.comfirmType.value = 3
      this.comfirmMessage = '确认项目已完成？'
      this.comfirmDialog = true
    },
    // 确认项目完成
    sureItemSubmit() {
      var self = this
      self.$http.post(api.demandTrueItemDoneId.format(self.item.id), {})
      .then (function(response) {
        self.comfirmDialog = false
        if (response.data.meta.status_code === 200) {
          self.comfirmLoadingBtn = false
          self.item.status = 18
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
    // 下载合同
    downContractPdf() {
      this.$router.push({name: 'vcenterContractDown', params: {unique_id: this.contract.unique_id}})
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
    if (uType === 2) {
      this.$router.replace({name: 'vcenterCItemShow'})
      return
    }

    const self = this
    self.$http.get(api.demandId.format(id), {})
    .then (function(response) {
      if (response.data.meta.status_code === 200) {
        console.log(response.data.data)
        self.item = response.data.data.item
        // self.info = response.data.data.info
        self.contract = response.data.data.contract
        if (self.contract) {
          self.contract.created_at = self.contract.created_at.date_format().format('yy-MM-dd')
        }
        self.quotation = response.data.data.quotation
        switch (self.item.status) {
          case 1:
            self.progressButt = 0
            self.progressContract = -1
            self.progressItem = -1
            break
          case 2:
            self.progressButt = 0
            self.progressContract = -1
            self.progressItem = -1
            break
          case 3: // 获取系统推荐的设计公司
            self.progressButt = 1
            self.progressContract = -1
            self.progressItem = -1
            self.statusLabel.selectCompany = true
            break
          case 4: // 查看已提交报价的设计公司
            self.progressButt = 2
            self.progressContract = -1
            self.progressItem = -1
            self.statusLabel.selectCompany = true
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
          case 5:
            self.progressButt = 2
            self.progressContract = 0
            self.progressItem = -1
            self.statusLabel.cooperateCompany = true
            break
          case 6:
            self.progressButt = 2
            self.progressContract = 1
            self.progressItem = -1
            self.statusLabel.cooperateCompany = true
            self.statusLabel.contract = true
            break
          case 7:
            self.progressButt = 2
            self.progressContract = 2
            self.progressItem = -1
            self.statusLabel.cooperateCompany = true
            self.statusLabel.contract = true
            self.statusLabel.amount = true
            break
          case 8:
            self.progressButt = 2
            self.progressContract = 2
            self.progressItem = -1
            self.statusLabel.cooperateCompany = true
            self.statusLabel.contract = true
            self.statusLabel.amount = true
            break
          case 9:
            self.progressButt = 2
            self.progressContract = 3
            self.progressItem = -1
            self.statusLabel.cooperateCompany = true
            self.statusLabel.contract = true
            self.statusLabel.amount = true
            self.statusLabel.isPay = true
            self.statusLabel.manage = true
            break
          case 11:
            self.progressButt = 3
            self.progressContract = 3
            self.progressItem = 0
            self.statusLabel.cooperateCompany = true
            self.statusLabel.contract = true
            self.statusLabel.amount = true
            self.statusLabel.isPay = true
            self.statusLabel.manage = true
            self.statusLabel.stage = true
            break
          case 15:
            self.progressButt = 3
            self.progressContract = 3
            self.progressItem = 1
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
            self.progressItem = 3
            self.statusLabel.cooperateCompany = true
            self.statusLabel.contract = true
            self.statusLabel.amount = true
            self.statusLabel.isPay = true
            self.statusLabel.manage = true
            self.statusLabel.stage = true
            break
          default:
        }

        // 获取系统推荐的设计公司
        if (self.statusLabel.selectCompany) {
          self.$http.get(api.recommendListId.format(self.item.id), {})
          .then (function(stickCompanyResponse) {
            if (stickCompanyResponse.data.meta.status_code === 200) {
              self.stickCompany = stickCompanyResponse.data.data
              for (var i = 0; i < self.stickCompany.length; i++) {
                var item = self.stickCompany[i]
                if (item.logo_image && item.logo_image.length !== 0) {
                  self.stickCompany[i].logo_url = item.logo_image.logo
                } else {
                  self.stickCompany[i].logo_url = false
                }
                if (item.item_type) {
                  self.stickCompany[i].item_type_label = item.item_type.join('／')
                }
              } // endfor
              console.log('stickCompany')
              console.log(self.stickCompany)
            }
          })
          .catch (function(error) {
            self.$message.error(error.message)
          })
        }

        // 已设置报价的设计公司
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
          self.$http.get(api.itemStageDemandLists, {params: {item_id: self.item.id}})
          .then (function(response) {
            if (response.data.meta.status_code === 200) {
              var items = response.data.data
              for (var i = 0; i < items.length; i++) {
                var item = items[i]
                items[i].created_at = item.created_at.date_format().format('yyyy-MM-dd')
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
        }]

        self.tableData = tab.concat(itemTab)

        console.log(self.item)
      }
    })
    .catch (function(error) {
      self.$message.error(error.message)
      console.log(error.message)
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
    margin-top: 20px;
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

  .offer-company-item {
    padding-top: 10px;
    height: 200px;
    border: 1px solid #ccc;
    margin: 20px 0 20px 0;
  }
  .item-logo {
    text-align: center;
    margin: 0 0 0 0px;
  }
  .item-logo p {
    line-height: 2;
    color: #333;
  }
  .item-logo img {
    margin-bottom: 10px;
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

  .contract-item {
    height: 50px;
    margin: 15px 0 5px 0;
    padding: 10px 0 0 0;
    border-top: 1px solid #ccc;
    border-bottom: 1px solid #ccc;
  }
  .contract-item .contract-left {
    float: left;
  }
  .contract-left img {
    float: left;
  }
  .contract-content {
    float: left;
    margin: 0 0 0 10px;
  }
  .contract-right {
    float: right;
  }
  .contract-right p {
    float: right;
    margin: 10px;
  }

  .contract-content p {
    font-size: 1rem;
    color: #666;
    line-height: 1.5;
  }
  .contract-des {
    font-size: 1rem;
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
  .finish-item-btn {
    margin-top: 50px;
    margin-bottom: 20px;
    text-align: center;
  }
  .finish-item-btn button {
    padding: 10px 60px 10px 60px;
  }


</style>

