<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="24" class="anli-elrow">
      <v-menu class="rightmenu"></v-menu>

      <el-col :span="isMob ? 24 : 20" class="stages-day">
        <div class="right-content">
          <div class="content-box">
            <h2>{{ itemName }}项目合同</h2>
            <el-form :model="form" :rules="ruleForm" ref="ruleForm">

              <p class="title">基本信息</p>
              <p class="sub-title">甲方（客户）</p>
              <el-row :gutter="10">
                <el-col :span="isMob ? 24 : 12">
                  <el-form-item label="" prop="demand_company_name">
                    <el-input v-model="form.demand_company_name" placeholder="公司名称" size="small"></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="isMob ? 24 : 12">
                  <el-form-item label="" prop="demand_company_legal_person">
                    <el-input v-model="form.demand_company_legal_person" placeholder="联系人" size="small"></el-input>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-row :gutter="10">
                <el-col :span="isMob ? 24 : 12">
                  <el-form-item label="" prop="demand_company_phone">
                    <el-input v-model="form.demand_company_phone" placeholder="电话" size="small"></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="isMob ? 24 : 12">
                  <el-form-item label="" prop="demand_company_address">
                    <el-input v-model="form.demand_company_address" placeholder="地址" size="small"></el-input>
                  </el-form-item>
                </el-col>
              </el-row>

              <p class="sub-title">乙方（服务商）</p>
              <el-row :gutter="10">
                <el-col :span="isMob ? 24 : 12">
                  <el-form-item label="" prop="design_company_name">
                    <el-input v-model="form.design_company_name" placeholder="公司名称" size="small"></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="isMob ? 24 : 12">
                  <el-form-item label="" prop="design_company_legal_person">
                    <el-input v-model="form.design_company_legal_person" placeholder="联系人" size="small"></el-input>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-row :gutter="10">
                <el-col :span="isMob ? 24 : 12">
                  <el-form-item label="" prop="design_company_phone">
                    <el-input v-model="form.design_company_phone" placeholder="电话" size="small"></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="isMob ? 24 : 12">
                  <el-form-item label="" prop="design_company_address">
                    <el-input v-model="form.design_company_address" placeholder="地址" size="small"></el-input>
                  </el-form-item>
                </el-col>
              </el-row>

              <!--
              <p class="title">项目内容</p>
              <el-form-item label="" prop="item_content">
                <el-input v-model="form.item_content" placeholder="项目设计内容" size="small"></el-input>
              </el-form-item>
              -->

              <p class="title">项目交付内容、工作周期及付款支付</p>
              <p>项目总金额(¥):
                <input class="bottom-border" type="text" disabled v-model="form.total" style="width:50px;"/>
                ; 首付款(¥):
                <input class="bottom-border" type="text" disabled :value="form.first_payment" style="width:50px;"/>
                (首付款占比总项目额度的40%); 阶段金额(¥):
                <input class="bottom-border" type="text" disabled :value="form.stage_money" style="width:50px;"/>
                (阶段金额占比总项目额度的50%); 尾款(¥):
                <input class="bottom-border" type="text" disabled v-model="form.warranty_money" style="width:50px;"/>
                (尾款占比总项目额度的10%)
              </p>
              <div class="blank20"></div>

              <el-row>
                <el-col :span="isMob ? 16 : 6">
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
                <el-col :span="isMob ? 6 : 4" :offset="2" style="line-height: 40px;">
                  <el-button class="is-custom" @click="genStageInput" size="small">{{ stateMsg }}</el-button>
                </el-col>
              </el-row>

              <div v-for="(d, index) in form.stages" :key="index">

                <p class="sub-title">第{{ index + 1 }}阶段</p>
                <input type="hidden" v-model.number="form.stages[index].sort"/>

                <el-row :gutter="6">
                  <el-col :span="isMob ? 24 : 8">
                    <el-form-item
                      :prop="'stages.' + index + '.title'"
                      :rules="{
                      required: true, message: '请添写阶段标题', trigger: 'blur'
                    }"
                    >
                      <el-input v-model="form.stages[index].title" placeholder="阶段标题" size="small"></el-input>
                    </el-form-item>
                  </el-col>
                  <el-col :span="isMob ? 24 : 4">
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

                  <el-col :span="isMob ? 12 : 4">
                    <el-form-item
                      :prop="'stages.' + index + '.percentage'"
                      :rules="{
                      type: 'number', required: true, message: '请添写阶段支付百分比', trigger: 'blur',
                      min: 10, max: 100, message: '比例在10-50之间', trigger: 'blur'
                    }"
                    >
                      <el-input v-model.number="form.stages[index].percentage" placeholder="阶段百分比"
                                @blur="fetchAmount(index)" size="small">
                        <template slot="append">%</template>
                      </el-input>
                    </el-form-item>
                  </el-col>
                  <el-col :span="isMob ? 12 : 4">
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

                <el-row :gutter="10" v-for="(s, i) in d.content" :key="i">
                  <el-col :span="isMob ? 24 : 12">
                    <el-form-item
                      :prop="'stages.' + index + '.content.' + i + ''"
                      :rules="{
                      required: true, message: '请添写内容', trigger: 'blur'
                    }"
                    >
                      <el-input v-model="form.stages[index].content[i]" placeholder="阶段内容" size="small">
                        <template slot="append">
                          <el-button @click="removeSubStage" :index="i" :stage_index="index">删除</el-button>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>
                </el-row>

                <div class="add-substage" @click="addSubStage" :index="index"><i class="fa fa-plus-circle"
                                                                                 aria-hidden="true"></i> 添加子内容
                </div>

              </div>


              <p class="title">甲方责任与义务 </p>
              <p>1、 以书面形式提出对本设计项目的要求及有关技术资料。在双方合作的全过程中，向乙方提供必要的咨询，并委派专人（对该项目的方案评审具有决定权）负责本项目的事务接洽和业务联系。</p>
              <p>2、 配合乙方的设计工作，积极参与该项目设计每个阶段的结果评审，及时得出结论并确认给乙方。</p>
              <p>3、 甲方的任何修改意见，应以书面形式通知给乙方（电子邮件）。</p>
              <p>4、 甲方不得以任何形式使用或转让乙方提供的除正选方案之外的其它方案，除非双方达成进一步关于其他方案合作的书面认同。</p>
              <p>5、 甲方按照合同的规定，及时按量地支付每一期的费用给乙方。</p>
              <p>6、 设计方案一旦经甲方确认后，如再发生改动，乙方将按实际工作量另行收费。</p>
              <p>7、 在甲方实际生产之前，甲方的供应生产商应对结构设计文档进行仔细分析，如乙方结构设计存在不合理之处，应给乙方以书面确认，及时沟通处理。</p>
              <p>8、  在乙方为甲方提供最终设计方案后，若因甲方产品结构或用途而变更设计方案，视其为新方案设计，甲方应向乙方支付完成现阶段设计费用后，乙方将按实际工作量另行对修改工作收取费用。</p>

              <p class="title">乙方责任与义务</p>
              <p>1、 严格执行本合同条款，按甲方所提供的文件、资料和具体要求进行设计制作，未经甲方书面许可乙方无权擅自变更设计方案或者以任何理由拖延交付时间；</p>
              <p>
                2、 由于审美标准的不确定性，甲方对乙方的外观设计方案若不满意，乙方有责任继续为甲方进行不超过3次（包含3次）的方案调整，而无须甲方支付任何额外费用。对于超过3次（不含3次）的方案调整，乙方每调整一次，甲方需额外增加外观设计费用10%的设计费用；</p>
              <p>3、 乙方在设计过程中应及时书面提请甲方进行设计、技术研究和阶段性把关；</p>
              <p>4、 乙方保证为甲方设计制作的方案与国家相关的法律、法规不相抵触并不侵犯任何第三方的权益；</p>
              <p>5、 协助甲方对产品生产加工过程中涉及外观设计，结构设计等方面的内容进行监督管理；</p>
              <p>
                6、 在合同执行过程中，若因乙方原因导致合同执行期的延误，则乙方应为执行周期延误而向甲方支付每日合同总额千分之五的延期违约金，但违约金总和最高不超过合同总额的百分之十；若因甲方不及时交付款项，则甲方应为拖欠款项向乙方支付每日合同总额千分之五的延期违约金，但违约金总和最高不超过合同总额的百分之十；</p>
              <p>7、 设计方案未最终确定之前，乙方可以拒绝甲方提出的任何形式的方案留存；</p>
              <p>8、 在合同签定后，对于项目涉及内容略有调整的情况，甲、乙双方应友好协商解决；</p>
              <p>9、 其它未尽事宜由甲、乙双方友好协商确定。</p>

              <p class="title">知识产权</p>
              <p>
                1、 对因本合同产生的甲方选定方案，其全部知识产权由甲方所有。乙方保留设计者署名权。除甲方选定的方案外，落选方案的全部知识产权仍归乙方所有。 若甲方需要享有其他设计方案的知识产权时，需与乙方协商买断知识产权相关事宜。</p>
              <p>2、 乙方保证其设计方案不侵犯任何第三方的知识产权。</p>
              <p>
                3、 乙方对本合同的内容、设计成果及其涉及的文档、数据资料负有保密义务，未经甲方许可，不得向任何第三方泄密。保密期限为一年（从本合同签订之日起计算），保密期间，落选的备用方案的文档资料不能泄露给第三方。</p>
              <p>4、 任何一方如遇政府法令或法律程序要求向第三方提供上述资料，可按规定提供，但应尽快将此项事实通知对方。</p>

              <p class="title">违约责任</p>
              <p>1、 如甲方对乙方在设计过程中工作内容不满意，有权中止本合同，不再继续支付剩余之款项，乙方亦不退还甲方已付款项。</p>
              <p>2、 如设计过程中甲方不能积极配合乙方工作，严重影响乙方的工作安排，在收到乙方书面通知后仍不能积极配合，则乙方有权中止合同。 </p>
              <p>3、 如甲方不能按照合同规定支付给乙方各设计阶段的设计费用，乙方有权中止合同。</p>
              <p>4、 如甲方未付清该合同全部设计款项，则该项目所有设计方案之知识产权仍归乙方所有。</p>

              <p class="title">不可抗力</p>
              <p>1、 本合同所指不可抗力包括地震、水灾、火灾、战争、政府行动、意外事件或其他双方所不能预见、不能避免并不能克服的事件。</p>
              <p>2、 由于不可抗力原因致使本合同无法履行时，无法履行合同义务的一方应在15日内将不能履行合同的事实通知另一方，本合同自动终止。</p>
              <p>3、 由于不可抗力原因致使本合同项目开发中断，项目交付日期及付款日期相应顺延，双方不承担违约责任。如中断超过30日，本合同自动终止。</p>

              <p class="title">争议解决</p>
              <p>
                1、 本合同签订后，未经双方同意不得单方面中止，否则由责任方承担造成的责任。与合同有关的争议或执行中产生的争议将通过友好协商解决。如不能达成一致，可向合同签订地的仲裁机构申请仲裁，也可以直接向人民法院起诉。</p>

              <div class="sept"></div>

              <div class="form-btn">
                <el-button type="primary" :loading="isLoadingBtn" class="is-custom" @click="submit('ruleForm')">保存合同
                </el-button>
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
            {required: true, message: '请添写公司名称', trigger: 'blur'}
          ],
          demand_company_address: [
            {required: true, message: '请添写公司地址名称', trigger: 'blur'}
          ],
          demand_company_phone: [
            {required: true, message: '请添写联系人方式', trigger: 'blur'}
          ],
          demand_company_legal_person: [
            {required: true, message: '请添写联系人姓名', trigger: 'blur'}
          ],

          design_company_name: [
            {required: true, message: '请添写公司名称', trigger: 'blur'}
          ],
          design_company_address: [
            {required: true, message: '请添写公司地址名称', trigger: 'blur'}
          ],
          design_company_phone: [
            {required: true, message: '请添写联系人方式', trigger: 'blur'}
          ],
          design_company_legal_person: [
            {required: true, message: '请添写联系人姓名', trigger: 'blur'}
          ],

          total: [
            {type: 'number', required: true, message: '请添写项目总金额', trigger: 'blur'}
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
            let row = that.form
            row.item_demand_id = that.itemId
            row.title = that.itemName
            if (!row.sort) {
              that.$message.error('请至少添加一项项目阶段!')
              return false
            }
            if (!row.stages || row.stages.length === 0) {
              that.$message.error('请至少添加一项项目阶段!')
              return false
            }
            for (let j = 0; j < row.stages.length; j++) {
              if (!row.stages[j].content || row.stages[j].content.length === 0) {
                that.$message.error('每个阶段至少添加一项子内容!')
                return false
              }
            }
            let totalPer = 0
            let totalAmount = 0
            row.item_stage = []
            for (let i = 0; i < row.stages.length; i++) {
              let stageRow = row.stages[i]
              let newStageRow = {}
              totalPer = totalPer.add(stageRow.percentage)
              totalAmount = totalAmount.add(stageRow.amount)
              newStageRow.sort = stageRow.sort
              newStageRow.title = stageRow.title
              newStageRow.amount = stageRow.amount
              newStageRow.percentage = stageRow.percentage.mul(0.01)
              newStageRow.time = stageRow.time
              newStageRow.content = stageRow.content
              row.item_stage.push(newStageRow)
            }
            if (totalPer !== 50) {
              that.$message.error('阶段比例之和应为50!')
              return false
            }
            let stagePrice = parseFloat(row.total.sub(parseFloat(row.warranty_money).add(parseFloat(row.first_payment))))
            if (totalAmount !== stagePrice) {
              that.$message.error('阶段金额总和不正确！')
              return false
            }
            let apiUrl = null
            let method = null

            if (that.contractId) {
              method = 'put'
              apiUrl = api.contractId.format(that.contractId)
            } else {
              method = 'post'
              apiUrl = api.contract
            }
            that.isLoadingBtn = true
            that.$http({method: method, url: apiUrl, data: row})
              .then(function (response) {
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
              .catch(function (error) {
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
        let count = this.form.sort
        if (!count) {
          this.$message.error('请选择阶段')
          return false
        }
        this.form.stages = []
        for (let i = 0; i < count; i++) {
          let row = {
            sort: i + 1,
            percentage: '',
            amount: '',
            title: '',
            time: '',
            content: []
          }
          this.form.stages.push(row)
        }
      },
      // 添加阶段子内容
      addSubStage (event) {
        let index = parseInt(event.currentTarget.getAttribute('index'))
        this.form.stages[index].content.push('')
      },
      // 删除阶段子内容
      removeSubStage (event) {
        let stageIndex = parseInt(event.currentTarget.getAttribute('stage_index'))
        let index = parseInt(event.currentTarget.getAttribute('index'))
        this.form.stages[stageIndex].content.splice(index, 1)
      },
      // 获取阶段金额
      fetchAmount(index) {
        let self = this
        this.$refs['ruleForm'].validateField('stages.' + index + '.percentage', function (error) {
          if (!error) {
            let total = parseFloat(self.form.total.sub(self.form.warranty_money.add(self.form.first_payment)))
            let per = self.form.stages[index].percentage.mul(0.01).mul(2)
            self.form.stages[index].amount = total.mul(per)
            // self.$set(self.form.stages[index], 'amount', total.mul(per))
          }
        })
      }
    },
    computed: {
      stageOptions() {
        let items = []
        for (let i = 2; i < 4; i++) {
          let item = {
            value: i,
            label: '共' + i + '阶段'
          }
          items.push(item)
        }
        return items
      },
      isMob() {
        return this.$store.state.event.isMob
      }
    },
    watch: {
      form: {
        deep: true,
        handler: function (val, oldVal) {
          if (val.stages && val.stages.length > 0) {
            this.stateMsg = '重置阶段'
          } else {
            this.stateMsg = '生成阶段'
          }
        }
      }
    },
    created () {
      let that = this
      let id = this.$route.params.item_id
      if (id) {
        that.itemId = id
        that.$http.get(api.designItemId.format(id), {})
          .then(function (response) {
            if (response.data.meta.status_code === 200) {
              let item = that.item = response.data.data
              that.itemName = that.item.item.name
              that.companyId = item.quotation.design_company_id

              if (item.contract) {
                that.contractId = item.contract.id
                that.$http.get(api.contractId.format(item.contract.unique_id), {})
                  .then(function (response) {
                    if (response.data.meta.status_code === 200) {
                      let contract = response.data.data
                      if (contract) {
                        contract.stages = []
                        contract.sort = contract.item_stage.length
                        contract.total = parseFloat(contract.total)
                        contract.warranty_money = parseFloat(contract.warranty_money)
                        contract.first_payment = parseFloat(contract.first_payment)
                        contract.stage_money =
                          (contract.total - (contract.warranty_money + contract.first_payment)).toFixed(2)
                        that.form = contract
                        if (that.form.item_stage && that.form.item_stage.length > 0) {
                          for (let i = 0; i < that.form.item_stage.length; i++) {
                            let stageRow = that.form.item_stage[i]
                            let newStageRow = {}
                            newStageRow.sort = parseInt(stageRow.sort)
                            newStageRow.title = stageRow.title
                            newStageRow.percentage = parseFloat(stageRow.percentage).mul(100)
                            newStageRow.amount = parseFloat(stageRow.amount)
                            newStageRow.time = parseInt(stageRow.time)
                            newStageRow.content = stageRow.content
                            that.form.stages.push(newStageRow)
                          }
                        }
                      }
//                      console.log(response.data.data)
                    }
                  })
              } else {  // 合同首次创建，从项目表调用基础信息
                // 获取当前公司基本信息
                that.$http.get(api.designCompany, {})
                  .then(function (response) {
                    if (response.data.meta.status_code === 200) {
                      let company = response.data.data
//                      console.log(response)
                      if (company) {
                        that.form.demand_company_name = item.item.company_name
                        that.form.demand_company_address = item.item.address
                        that.form.demand_company_legal_person = item.item.contact_name
                        that.form.demand_company_phone = item.item.phone
                        that.form.total = parseFloat(item.item.price)
                        that.form.warranty_money = parseFloat(item.item.warranty_money)
                        that.form.first_payment = parseFloat(item.item.first_payment)
                        that.form.stage_money =
                          (that.form.total - (that.form.warranty_money + that.form.first_payment)).toFixed(2)
                        that.form.design_company_name = company.company_name
                        that.form.design_company_address = company.address
                        that.form.design_company_legal_person = company.contact_name
                        that.form.design_company_phone = company.phone
                      }
                    }
                  })
                  .catch(function (error) {
                    that.$message.error(error.message)
                    return false
                  })
              }
//              console.log(response.data.data)
            }
          })
          .catch(function (error) {
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
  .rightmenu {
    margin-bottom: 18px;
  }

  .right-content {
    margin-top: -20px;
  }

  .content-box form {
    padding: 10px 50px
  }

  .content-box h2 {
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
    border-top: 0px;
    border-left: 0px;
    border-right: 0px;
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

  .add-substage {
    font-size: 1.3rem;
    margin: 0 0 10px 5px;
    clear: both;
    cursor: pointer;
  }

  @media screen and (max-width: 767px) {
    .right-content .content-box {
      border: none;
    }

    .content-box form {
      padding: 0
    }
  }
</style>
