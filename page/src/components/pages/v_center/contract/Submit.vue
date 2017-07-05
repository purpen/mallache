<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="24">
      <v-menu></v-menu>

      <el-col :span="20">
        <div class="right-content">
          <div class="content-box">
            <h2>{{ itemName }}合同</h2>
            <el-form :model="form" :rules="ruleForm" ref="ruleForm">

            <p class="title">1.基本信息</p>
            <p class="sub-title">甲方（客户）</p>
            <el-row :gutter="10">
              <el-col :span="12">
                <el-form-item label="" prop="demand_company_name">
                  <el-input v-model="form.demand_company_name" placeholder="公司名称" size="small"></el-input>
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="" prop="demand_company_legal_person">
                  <el-input v-model="form.demand_company_legal_person" placeholder="联系人" size="small"></el-input>
                </el-form-item>
              </el-col>
            </el-row>

            <el-row :gutter="10">
              <el-col :span="12">
                <el-form-item label="" prop="demand_company_phone">
                  <el-input v-model="form.demand_company_phone" placeholder="电话" size="small"></el-input>
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="" prop="demand_company_address">
                  <el-input v-model="form.demand_company_address" placeholder="地址" size="small"></el-input>
                </el-form-item>
              </el-col>
            </el-row>

            <p class="sub-title">乙方（服务商）</p>
            <el-row :gutter="10">
              <el-col :span="12">
                <el-form-item label="" prop="design_company_name">
                  <el-input v-model="form.design_company_name" placeholder="公司名称" size="small"></el-input>
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="" prop="design_company_legal_person">
                  <el-input v-model="form.design_company_legal_person" placeholder="联系人" size="small"></el-input>
                </el-form-item>
              </el-col>
            </el-row>

            <el-row :gutter="10">
              <el-col :span="12">
                <el-form-item label="" prop="design_company_phone">
                  <el-input v-model="form.design_company_phone" placeholder="电话" size="small"></el-input>
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="" prop="design_company_address">
                  <el-input v-model="form.design_company_address" placeholder="地址" size="small"></el-input>
                </el-form-item>
              </el-col>
            </el-row>

            <p class="title">2.项目内容</p>
            <el-form-item label="" prop="item_content">
              <el-input v-model="form.item_content" placeholder="项目设计内容" size="small"></el-input>
            </el-form-item>

            <p class="title">3.项目费用及计划</p>
            <p>项目总金额(¥):  <input class="bottom-border" type="text" disabled v-model="form.total" style="width:50px;" /> ; 阶段金额(¥): <input class="bottom-border" type="text" disabled :value="form.stage_money" style="width:50px;" /> 尾款金额(¥): <input class="bottom-border" type="text" disabled v-model="form.warranty_money" style="width:50px;" />(尾款金额占比总项目额度的5%)</p>
            <div class="blank20"></div>
            <!--
            <el-row :gutter="10">
              <el-col :span="6">
                <el-form-item label="" prop="total">
                  <el-input v-model="form.total" placeholder="项目设计费用" disabled size="small">
                    <template slot="prepend">¥</template>
                  </el-input>
                </el-form-item>
              </el-col>
            </el-row>
            -->
            <el-row>
              <el-col :span="6">
              <el-form-item prop="sort">
                <el-select v-model.number="form.sort" placeholder="设置项目阶段" size="small">
                  <el-option
                    v-for="item in stageOptions"
                    :label="item.label"
                    :key="item.index"
                    :value="item.value">
                  </el-option>
                </el-select>
              </el-form-item>
              </el-col>
              <el-col :span="4" style="line-height: 40px;">
                <el-button class="is-custom" @click="genStageInput" size="small">{{ stateMsg }}</el-button>
              </el-col>
            </el-row>

            <div v-for="(d, index) in form.stages" :key="index">
            
              <p class="sub-title">第{{ index + 1 }}阶段</p>
              <input type="hidden" v-model.number="form.stages[index].sort" />

              <el-row :gutter="6">
                <el-col :span="8">
                  <el-form-item
                    :prop="'stages.' + index + '.title'"
                    :rules="{
                      required: true, message: '请添写阶段标题', trigger: 'blur'
                    }"
                  >
                    <el-input v-model="form.stages[index].title" placeholder="阶段标题" size="small"></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="4">
                  <el-form-item
                    :prop="'stages.' + index + '.time'"
                    :rules="{
                      type: 'number', required: true, message: '请添写工作日', trigger: 'blur'
                    }"
                  >
                    <el-input v-model.number="form.stages[index].time" placeholder="" size="small">
                      <template slot="append">工作日</template>
                    </el-input>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-row :gutter="10">

                <el-col :span="4">
                  <el-form-item
                    :prop="'stages.' + index + '.percentage'"
                    :rules="{
                      type: 'number', required: true, message: '请添写阶段支付百分比', trigger: 'blur',
                      min: 10, max: 100, message: '比例在10-100之间', trigger: 'blur'
                    }"
                  >
                    <el-input v-model.number="form.stages[index].percentage" placeholder="阶段百分比" @blur="fetchAmount(index)" size="small">
                      <template slot="append">%</template>
                    </el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="4">
                  <el-form-item
                    :prop="'stages.' + index + '.amount'"
                    :rules="{
                      type: 'number', required: true, message: '请添写阶段金额', trigger: 'blur'
                    }"
                  >
                    <el-input v-model.number="form.stages[index].amount" disabled placeholder="" size="small">
                      <template slot="append">元</template>
                    </el-input>
                  </el-form-item>
                </el-col>
              </el-row>
            
            </div>


            <p class="title">1.1 甲方责任与义务 </p>
            <p>1.1.1 以书面形式提出对本设计项目的要求及有关技术资料。在双方合作的全过程中，向乙方提供必要的咨询，并委派专人（对该项目的方案评审具有决定权）负责本项目的事务接洽和业务联系。</p>
            <p>1.1.2 配合乙方的设计工作，积极参与该项目设计每个阶段的结果评审，及时得出结论并确认给乙方。</p>
            <p>1.1.3 甲方的任何修改意见，应以书面形式通知给乙方（电子邮件）。</p>
            <p>1.1.4 甲方不得以任何形式使用或转让乙方提供的除正选方案之外的其它方案，除非双方达成进一步关于其他方案合作的书面认同。</p>   
            <p>1.1.5 甲方按照合同的规定，及时按量地支付每一期的费用给乙方。</p>
            <p>1.1.6 设计方案一旦经甲方确认后，如再发生改动，乙方将按实际工作量另行收费。</p>
            <p>1.1.7 在甲方实际生产之前，甲方的供应生产商应对结构设计文档进行仔细分析，如乙方结构设计存在不合理之处，应给乙方以书面确认，及时沟通处理。</p>
            <p>1.1.8  在乙方为甲方提供最终设计方案后，若因甲方产品结构或用途而变更设计方案，视其为新方案设计，甲方应向乙方支付完成现阶段设计费用后，乙方将按实际工作量另行对修改工作收取费用。</p>

            <p class="title">1.2 乙方责任与义务</p>
            <p>1.2.1 乙方设计工作内容包括:  <input class="bottom-border" type="text" v-model="form.design_work_content" style="width:500px;" /> 工期数量双方根据具体情况协商确定，附设计流程协议书，详细制定设计流程、各阶段工期、内容数量。</p>
            <p>1.2.2 在方案设计阶段，乙方根据甲方提出的产品定位、目标市场设想等指引文件，在预付款支付以及相关资料交付后，提供设计草方案。</p>
            <p>1.2.3 乙方须充分听取甲方的意见，并在接到甲方书面形式修改意见后，依据进行修改。根据选定方案及甲方反馈的修改意见，进行定案二维效果图制作，以及后期的全尺寸油泥模型制作，逆向建模，结构设计，样件试制试装。</p>
            <p>1.2.4 乙方在设计方案修改完成，通过甲方评审并书面确认后，将最终设计方案以三维数模、二维图纸的文件方式交付给甲方。</p>
            <p>1.2.5  乙方未经甲方同意，不得将任何甲方提供的与之产品相关的资料（照片、图纸、参数等）公开发布或泄露给第三方，并在设计开发的过程中，对此设计项目的设计草图、效果图、油泥模型、三维数据、二维图纸、样件保密。应甲方要求，亦可在设计项目完成后的一定时间内，对设计项目的上述相关内容进行保密，并签订相应的保密协议书。</p>

            <p class="title">知识产权</p>
            <p>1、对因本合同产生的甲方选定方案，其全部知识产权由甲方所有。乙方保留设计者署名权。除甲方选定的方案外，落选方案的全部知识产权仍归乙方所有。  2、乙方保证其设计方案不侵犯任何第三方的知识产权。  3、乙方对本合同的内容、设计成果及其涉及的文档、数据资料负有保密义务，未经甲方许可，不得向任何第三方泄密。保密期限为一年（从本合同签订之日起计算），保密期间，落选的备用方案的文档资料不能泄露给第三方。  4、任何一方如遇政府法令或法律程序要求向第三方提供上述资料，可按规定提供，但应尽快将此项事实通知对方。</p>

            <p class="title">违约责任</p>
            <p>1、如甲方对乙方在设计过程中工作内容不满意，有权中止本合同，不再继续支付剩余之款项，乙方亦不退还甲方已付款项。</p>
            <p>2、如设计过程中甲方不能积极配合乙方工作，严重影响乙方的工作安排，在收到乙方书面通知后仍不能积极配合，则乙方有权中止合同。</p>
            <p>3、如甲方不能按照合同规定支付给乙方各设计阶段的设计费用，乙方有权中止合同。</p>
            <p>4、如甲方未付清该合同全部设计款项，则该项目所有设计方案之知识产权仍归乙方所有。<p>
            <p>1、对因本合同产生的甲方选定方案，其全部知识产权由甲方所有。乙方保留设计者署名权。除甲方选定的方案外，落选方案的全部知识产权仍归乙

            <p class="title">不可抗力</p>
            <p>1、本合同所指不可抗力包括地震、水灾、火灾、战争、政府行动、意外事件或其他双方所不能预见、不能避免并不能克服的事件。</p>
            <p>2、由于不可抗力原因致使本合同无法履行时，无法履行合同义务的一方应在15日内将不能履行合同的事实通知另一方，本合同自动终止。</p>
            <p>3、由于不可抗力原因致使本合同项目开发中断，项目交付日期及付款日期相应顺延，双方不承担违约责任。如中断超过30日，本合同自动终止。</p>
            <p class="title">合同变更</p>

            <p class="title">争议解决</p>
            <p>本合同签订后，未经双方同意不得单方面中止，否则由责任方承担造成的责任。与合同有关的争议或执行中产生的争议将通过友好协商解决。如不能达成一致，可向合同签订地的仲裁机构申请仲裁，也可以直接向人民法院起诉。</p>

            <div class="sept"></div>

            <div class="form-btn">
                <el-button type="primary" :loading="isLoadingBtn" class="is-custom" @click="submit('ruleForm')">保存合同</el-button>
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
  import vMenuSub from '@/components/pages/v_center/c_item/MenuSub'
  import api from '@/api/api'
  import '@/assets/js/format'
  import '@/assets/js/date_format'
  import '@/assets/js/math_format'
  // import typeData from '@/config'

  export default {
    name: 'vcenter_contract_submit',
    components: {
      vMenu,
      vMenuSub
    },
    data () {
      return {
        itemId: '',
        item: '',
        itemName: '',
        companyId: '',
        isLoadingBtn: false,
        contractId: '',
        stateMsg: '生成阶段',
        form: {
          demand_company_name: '',
          demand_company_address: '',
          demand_company_phone: '',
          demand_company_legal_person: '',

          design_company_name: '',
          design_company_address: '',
          design_company_phone: '',
          design_company_legal_person: '',

          item_content: '',

          design_type: '',
          design_type_paragraph: '',
          design_type_contain: '',
          total: '',
          warranty_money: '',

          project_start_date: '',
          determine_design_date: '',
          structure_layout_date: '',
          design_sketch_date: '',
          end_date: '',

          one_third_total: '',
          exterior_design_percentage: '',
          exterior_design_money: '',
          exterior_design_phase: '',
          exterior_modeling_design_percentage: '',
          exterior_modeling_design_money: '',

          design_work_content: '',
          item_stage: [],
          stages: [],
          sort: ''
        },
        ruleForm: {
          demand_company_name: [
            { required: true, message: '请添写公司名称', trigger: 'blur' }
          ],
          demand_company_address: [
            { required: true, message: '请添写公司地址名称', trigger: 'blur' }
          ],
          demand_company_phone: [
            { required: true, message: '请添写联系人方式', trigger: 'blur' }
          ],
          demand_company_legal_person: [
            { required: true, message: '请添写联系人姓名', trigger: 'blur' }
          ],

          design_company_name: [
            { required: true, message: '请添写公司名称', trigger: 'blur' }
          ],
          design_company_address: [
            { required: true, message: '请添写公司地址名称', trigger: 'blur' }
          ],
          design_company_phone: [
            { required: true, message: '请添写联系人方式', trigger: 'blur' }
          ],
          design_company_legal_person: [
            { required: true, message: '请添写联系人姓名', trigger: 'blur' }
          ],

          item_content: [
            { required: true, message: '请添写项目内容', trigger: 'blur' }
          ],
          total: [
            { type: 'number', required: true, message: '请添写项目总金额', trigger: 'blur' }
          ]
        },
        userId: this.$store.state.event.user.id
      }
    },
    methods: {
      submit(formName) {
        const that = this
        that.$refs[formName].validate((valid) => {
          // 验证通过，提交
          if (valid) {
            var row = that.form
            row.item_demand_id = that.itemId
            row.title = that.itemName
            if (!row.design_work_content) {
              that.$message.error('请添写设计工作内容!')
              return false
            }
            if (!row.sort) {
              that.$message.error('请至少添加一项项目阶段!')
              return false
            }
            if (!row.stages || row.stages.length === 0) {
              that.$message.error('请至少添加一项项目阶段!')
              return false
            }
            var totalPer = 0
            var totalAmount = 0
            row.item_stage = []
            for (var i = 0; i < row.stages.length; i++) {
              var stageRow = row.stages[i]
              var newStageRow = {}
              totalPer = totalPer.add(stageRow.percentage)
              totalAmount = totalAmount.add(stageRow.amount)
              newStageRow.sort = stageRow.sort
              newStageRow.title = stageRow.title
              newStageRow.amount = stageRow.amount
              newStageRow.percentage = stageRow.percentage.mul(0.01)
              newStageRow.time = stageRow.time
              row.item_stage.push(newStageRow)
            }
            if (totalPer !== 100) {
              that.$message.error('阶段比例之和应为100!')
              return false
            }
            var stagePrice = parseFloat(row.total.sub(parseFloat(row.warranty_money)))
            alert(stagePrice)
            if (totalAmount !== stagePrice) {
              that.$message.error('阶段金额总和不正确！')
              return false
            }
            console.log(row)

            var apiUrl = null
            var method = null

            if (that.contractId) {
              method = 'put'
              apiUrl = api.contractId.format(that.contractId)
            } else {
              method = 'post'
              apiUrl = api.contract
            }
            that.isLoadingBtn = true
            that.$http({method: method, url: apiUrl, data: row})
            .then (function(response) {
              if (response.data.meta.status_code === 200) {
                that.$message.success('提交成功！')
                that.isLoadingBtn = false
                that.$router.push({name: 'vcenterCItemShow', params: {id: that.itemId}})
                return false
              } else {
                that.isLoadingBtn = false
                that.$message.error(response.data.meta.message)
              }
            })
            .catch (function(error) {
              that.$message.error(error.message)
              that.isLoadingBtn = false
              return false
            })
          } else {
          }
        })
      },
      // 生成阶段
      genStageInput() {
        var count = this.form.sort
        if (!count) {
          this.$message.error('请选择阶段')
          return false
        }
        this.form.stages = []
        for (var i = 0; i < count; i++) {
          var row = {
            sort: i + 1,
            percentage: '',
            amount: '',
            title: '',
            time: ''
          }
          this.form.stages.push(row)
        }
      },
      // 获取阶段金额
      fetchAmount(index) {
        var self = this
        this.$refs['ruleForm'].validateField('stages.' + index + '.percentage', function (error) {
          if (!error) {
            var total = parseFloat(self.form.total.sub(self.form.warranty_money))
            var per = self.form.stages[index].percentage.mul(0.01)
            self.form.stages[index].amount = total.mul(per)
            // self.$set(self.form.stages[index], 'amount', total.mul(per))
          }
        })
      }
    },
    computed: {
      stageOptions() {
        var items = []
        for (var i = 1; i < 4; i++) {
          var item = {
            value: i,
            label: '共' + i + '阶段'
          }
          items.push(item)
        }
        return items
      }
    },
    watch: {
      form: {
        deep: true,
        handler: function(val, oldVal) {
          if (val.stages && val.stages.length > 0) {
            this.stateMsg = '重置阶段'
          } else {
            this.stateMsg = '生成阶段'
          }
        }
      }
    },
    created: function() {
      var that = this
      var id = this.$route.params.item_id
      if (id) {
        that.itemId = id
        that.$http.get(api.designItemId.format(id), {})
        .then (function(response) {
          if (response.data.meta.status_code === 200) {
            var item = that.item = response.data.data
            that.itemName = that.item.item.name
            that.companyId = item.quotation.design_company_id
            if (item.contract) {
              that.contractId = item.contract.id
              that.$http.get(api.contractId.format(item.contract.unique_id), {})
              .then (function(response) {
                if (response.data.meta.status_code === 200) {
                  var contract = response.data.data
                  if (contract) {
                    contract.stages = []
                    contract.sort = contract.item_stage.length
                    contract.total = parseFloat(contract.total)
                    contract.warranty_money = parseFloat(contract.warranty_money)
                    contract.stage_money = (contract.total - contract.warranty_money).toFixed(2)
                    that.form = contract
                    if (that.form.item_stage && that.form.item_stage.length > 0) {
                      for (var i = 0; i < that.form.item_stage.length; i++) {
                        var stageRow = that.form.item_stage[i]
                        var newStageRow = {}
                        newStageRow.sort = parseInt(stageRow.sort)
                        newStageRow.title = stageRow.title
                        newStageRow.percentage = parseFloat(stageRow.percentage).mul(100)
                        newStageRow.amount = parseFloat(stageRow.amount)
                        newStageRow.time = parseInt(stageRow.time)
                        that.form.stages.push(newStageRow)
                      }
                    }
                  }
                  console.log(response.data.data)
                }
              })
            } else {  // 合同首次创建，从项目表调用基础信息
              // 获取当前公司基本信息
              that.$http.get(api.designCompany, {})
              .then (function(response) {
                if (response.data.meta.status_code === 200) {
                  var company = response.data.data
                  if (company) {
                    that.form.demand_company_name = item.item.company_name
                    that.form.demand_company_address = item.item.address
                    that.form.demand_company_legal_person = item.item.contact_name
                    that.form.demand_company_phone = item.item.phone
                    that.form.total = parseFloat(item.item.price)
                    that.form.warranty_money = parseFloat(item.item.warranty_money)
                    that.form.stage_money = (that.form.total - that.form.warranty_money).toFixed(2)

                    that.form.design_company_name = company.company_name
                    that.form.design_company_address = company.address
                    that.form.design_company_legal_person = company.contact_name
                    that.form.design_company_phone = company.phone
                  }
                }
              })
              .catch (function(error) {
                that.$message.error(error.message)
                return false
              })
            }
            console.log(response.data.data)
          }
        })
        .catch (function(error) {
          that.$message.error(error.message)
          return false
        })
      } else {
        that.$message.error('缺少请求参数!')
        that.$router.push({name: 'home'})
      }
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .content-box {
  }
  .right-content {
    margin-top: -20px;
  }
  .content-box form {
    padding: 10px 50px 10px 50px;
  }
  .content-box h2{
    text-align: center;
    margin: 20px 0 10px 0;
    font-size: 2.5rem;
  }
  .content-box p {
    line-height: 1.5;
    font-size: 1rem;
    color: #666;
  }
  .content-box p.title {
    margin-top: 10px;
    font-size: 1.3rem;
    font-weight: bold;
    color: #333;
  }
  input.no-border {
    border: 0px;
  }
  input.bottom-border {
    border-bottom: 1px solid #666;  
    border-top:0px;  
    border-left:0px;  
    border-right:0px; 
  }

  .sept {
    width: 100%;
    margin: 20px 0 20px 0;
    padding: 0;
    border-top: 1px solid #ccc;
  }
  .form-btn {
    float: right;
  }
  .sub-title {
    margin: 5px 0;
    font-size: 1.5rem;
  }

  .el-input {
    margin: 0 0;
  }

  .el-form-item {
    margin: 5px 0 12px 0;
  }


</style>
