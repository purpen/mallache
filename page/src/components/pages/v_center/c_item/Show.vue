<template>
  <div class="container">

    <el-row :gutter="24">
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
                    <p><a href="#"><i class="fa fa-download" aria-hidden="true"></i> 下载</a></p>
                    <p><router-link :to="{name: 'vcenterContractView', params: {unique_id: contract.unique_id}}" target="_blank"><i class="fa fa-eye" aria-hidden="true"></i> 预览</router-link></p>
                    <p v-if="item.status < 7"><router-link :to="{name: 'vcenterContractSubmit', params: {item_id: item.id}}" target="_blank"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> 修改</router-link></p>
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
                  <p class="add-stage-btn" v-if="item.status === 11"><router-link :to="{name: 'vcenterDesignStageAdd', params: {item_id: item.id}}"><i class="el-icon-plus"></i> 添加阶段</router-link></p>
                  <div v-for="(d, index) in stages">
                    <div class="contract-left">
                      <img src="../../../../assets/images/icon/pdf2x.png" width="30" />
                      <div class="contract-content">
                        <p>{{ d.title }}</p>
                        <p class="contract-des">{{ d.created_at }}</p>
                      </div>
                    </div>
                    <div class="contract-right">
                      <p v-if="d.status === 0"><a href="javascript:void(0)" @click="stageSend(d.id, index)"><i class="fa fa-share" aria-hidden="true"></i> 发送</a></p>
                      <p><router-link :to="{name: 'vcenterDesignStageEdit', params: {id: d.id}}" target="_blank"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> 修改</router-link></p>
                      <p><router-link :to="{name: 'vcenterDesignStageShow', params: {id: d.id}}" target="_blank"><i class="fa fa-eye" aria-hidden="true"></i> 预览</router-link></p>
                    </div>
                    <div class="clear"></div>
                  </div>
                  <p class="finish-item-btn" v-if="item.status === 11"><el-button type="primary" class="is-custom" :loading="sendStageLoadingBtn" @click="sureItemBtn">项目确认完成</el-button></p>
                  <p class="finish-item-stat" v-if="item.status === 15">等待客户验收项目</p>
                  <p class="finish-item-stat" v-if="item.status === 18">项目已验收</p>
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
      sendStageLoadingBtn: false,
      comfirmMessage: '确认执行此操作?',
      stickCompanyIds: [],
      stages: [],
      secondPayLoadingBtn: false,
      item: {},
      info: {},
      contract: null,
      isLoadingBtn: false,
      selectCompanyCollapse: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '11', '15'],
      beginItemLoadingBtn: false,
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
        this.sureItemSubmit()
      } else {
        this.comfirmLoadingBtn = false
      }
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
    // 项目阶段发送
    stageSend(id, index) {
      var self = this
      self.$http.put(api.itemStageSend, {id: id})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          self.$message.success('发送成功!')
          self.stages[index].status = 1
        } else {
          self.$message.error(response.data.meta.message)
        }
      })
      .catch (function(error) {
        self.$message.error(error.message)
      })
    },
    // 确认项目完成弹出层
    sureItemBtn() {
      this.$refs.comfirmType.value = 1
      this.comfirmMessage = '确认项目已完成？'
      this.comfirmDialog = true
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
                } // endfor
              }
            })
            .catch (function(error) {
              self.$message.error(error.message)
            })
            break
          case 4: // 查看已提交报价的设计公司
            self.progressButt = 2
            self.progressContract = -1
            self.progressItem = -1
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
            self.statusLabel.contract = true
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
              for (var i = 0; i < items.length; i++) {
                var item = items[i]
                items[i].created_at = item.created_at.date.date_format().format('yyyy-MM-dd')
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
  }
  .contract-right p {
    float: right;
    margin: 10px;
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
    margin-top: 50px;
    font-size: 2rem;
    text-align: center;
  }

</style>

