<template>
  <div class="container">
    <el-row :gutter="24">
      <v-menu></v-menu>

      <el-col :span="20">
        <div class="right-content">
          <div class="content-box">
            <h2>{{ itemName }}</h2>
            <p>&nbsp;</p>
            <p>甲方: <input class="no-border" type="text" disabled v-model="form.demand_company_name" style="width:250px;" /></p>
            <p>地址: <input class="no-border" type="text" disabled v-model="form.demand_company_address" style="width:500px;" /></p>
            <p>电话: <input class="no-border" type="text" disabled v-model="form.demand_company_phone" style="width:150px;" /></p>
            <p>联系人: <input class="no-border" type="text" disabled v-model="form.demand_company_legal_person" style="width:150px;" /></p>
            <p>&nbsp;</p>
            <p>乙方: <input class="no-border" type="text" disabled v-model="form.design_company_name" style="width:250px;" /></p>
            <p>地址: <input class="no-border" type="text" disabled v-model="form.design_company_address" style="width:500px;" /></p>
            <p>电话: <input class="no-border" type="text" disabled v-model="form.design_company_phone" style="width:150px;" /></p>
            <p>联系人: <input class="no-border" type="text" disabled v-model="form.design_company_legal_person" style="width:150px;" /></p>

            <p class="title">根据需求方与供应商在本平台的初步沟通，现确定双方合作内容与形式如下：</p>
            <p>
              自本协议签订之日起，需求方需在 <input class="bottom-border" disabled type="text" value="5" style="width:20px;" />个工作日内一次性支付给供应商 <input class="bottom-border" disabled type="text" v-model="form.title" style="width:100px;" />（项目名称 ） 的设计费用人民币（<input class="bottom-border" disabled type="text" v-model="form.total" style="width:50px;" />）元，其中     将作为项目预付款直接转入供应商账户，剩余款项在项目进行期间由本平台托管，并分阶段在供应商完成相应阶段工作后转入供应商账户，直至需求方确认项目完成且无异议后将全部款项转入供应商账户；供应商需按协议规定，在收到预付款后的   个工作日内启动项目相关设计工作，并按照需求方要求在规定时间内完成全部设计工作。
            </p>

            <p class="title">项目具体流程与时间节点</p>
            <p>经双方协商，本项目共分 <input class="bottom-border" disabled type="text" v-model="form.sort" style="width:20px;" /> 个阶段进行，细节流程与时间节点如下：</p>
            <p v-for="(d, index) in form.stages">
            第 <input class="bottom-border" disabled type="text" v-model="d.sort" style="width:20px;" /> 阶段：供应商在 <input class="bottom-border" disabled type="text" v-model="d.time" style="width:20px;" /> 个工作日内提交 <input class="bottom-border" disabled type="text" v-model="d.title" style="width:200px;" />（项目初步方案） ，经需求方确认后，该阶段的设计款项，项目全款的 <input class="bottom-border" disabled type="text" v-model="d.percentage" style="width:20px;" /> %，即人民币 （<input class="bottom-border" disabled type="text" v-model="d.amount" style="width:50px;" />）元，将从平台托管池内转入供应商账户；
            </p>


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
            <p>1.2.1 乙方设计工作内容包括:  <input class="bottom-border" disabled type="text" v-model="form.design_work_content" style="width:500px;" /> 工期数量双方根据具体情况协商确定，附设计流程协议书，详细制定设计流程、各阶段工期、内容数量。</p>
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

            <div class="form-btn" v-if="userType !== 2">
                <el-button type="primary" :loading="isLoadingBtn" class="is-custom" @click="agreeBtn" v-show="form.status === 0">我同意</el-button>
                <router-link :to="{name: 'vcenterContractDown', params: {unique_id: uniqueId}}" target="_blank" v-show="form.status === 1"><i class="fa fa-download" aria-hidden="true"></i> 下载合同</router-link>
            </div>
            <div class="form-btn" v-else>
                <router-link :to="{name: 'vcenterContractDown', params: {unique_id: uniqueId}}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> 下载合同</router-link>
            </div>
            <div class="clear"></div>

          </div>

        </div>

      </el-col>
    </el-row>

    <el-dialog
      title="提示"
      v-model="sureDialog"
      size="tiny">
      <input type="hidden" ref="currentType" value="1" />
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
  import vMenuSub from '@/components/pages/v_center/c_item/MenuSub'
  import api from '@/api/api'
  import '@/assets/js/format'
  import '@/assets/js/date_format'
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
        uniqueId: '',
        item: {},
        itemName: '',
        form: {
        },
        userType: this.$store.state.event.user.type,
        sureDialog: false,
        sureDialogMessage: '确认执行此操作？',
        sureDialogLoadingBtn: false,
        isLoadingBtn: false,
        userId: this.$store.state.event.user.id
      }
    },
    methods: {
      // 同意合同
      agreeBtn() {
        this.sureDialogMessage = '确认对合同内容熟知？'
        this.sureDialog = true
      },
      // 提示框确认操作
      sureDialogSubmit() {
        var type = parseInt(this.$refs.currentType.value)
        var self = this
        this.sureDialogLoadingBtn = true
        if (type === 1) {
          self.$http({method: 'post', url: api.demandTrueContract, data: {item_id: self.form.item_demand_id}})
          .then (function(response) {
            self.sureDialogLoadingBtn = false
            self.sureDialog = false
            if (response.data.meta.status_code === 200) {
              self.form.status = 1
              self.$message.success('提交成功！')
              // 跳到支付页面
              self.$router.push({name: 'itemPayFund', params: {item_id: self.form.item_demand_id}})
              return false
            } else {
              self.$message.error(response.data.meta.message)
            }
          })
          .catch (function(error) {
            self.sureDialogLoadingBtn = false
            self.$message.error(error.message)
          })
        }
      }
    },
    computed: {
    },
    watch: {
    },
    created: function() {
      const that = this
      var uniqueId = this.$route.params.unique_id
      if (uniqueId) {
        that.uniqueId = uniqueId
        that.$http.get(api.contractId.format(uniqueId), {})
        .then (function(response) {
          if (response.data.meta.status_code === 200) {
            var item = response.data.data
            if (item) {
              that.itemId = item.id
              item.stages = []
              item.sort = item.item_stage.length
              if (item.item_stage && item.item_stage.length > 0) {
                for (var i = 0; i < item.item_stage.length; i++) {
                  var stageRow = item.item_stage[i]
                  var newStageRow = {}
                  newStageRow.sort = parseInt(stageRow.sort)
                  newStageRow.title = stageRow.title
                  newStageRow.percentage = parseFloat(stageRow.percentage).mul(100)
                  newStageRow.amount = parseFloat(stageRow.amount)
                  newStageRow.time = parseInt(stageRow.time).date_format().format('yyyy-MM-dd')
                  item.stages.push(newStageRow)
                }
              }
              that.form = item
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
  .right-content .content-box {
    margin-top: 0;
  }
  .content-box p {
    line-height: 1.5;
    font-size: 1rem;
    color: #666;
  }
  .content-box h2{
    text-align: center;
    margin: 20px 0 10px 0;
    font-size: 2.5rem;
  }
  .content-box p.title {
    margin-top: 20px;
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

  input[type="text"]:disabled {
    background-color: #fff;
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


</style>
