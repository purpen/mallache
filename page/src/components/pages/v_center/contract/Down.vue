<template>
  <div class="container">
    <div class="show-box"><p>{{ downStatus }}</p></div>
  </div>
</template>

<script>
  import api from '@/api/api'
  import '@/assets/js/format'
  import '@/assets/js/date_format'
  // import '../../../../../static/js/pdfmake.min.js'
  // import '../../../../../static/js/vfs_fonts.js'

  export default {
    name: 'vcenter_contract_submit',
    components: {
    },
    data () {
      return {
        itemId: '',
        item: {},
        itemName: '',
        form: {},
        downStatus: '正在生成合同，请等待......',
        userId: this.$store.state.event.user.id
      }
    },
    methods: {
      // 下载
      downBtn() {
        var dd = {
          content: [
            { text: this.itemName, style: 'header' },
            { text: '甲方: ' + this.form.demand_company_name, style: 'p' },
            { text: '地址: ' + this.form.demand_company_address, style: 'p' },
            { text: '电话: ' + this.form.demand_company_phone, style: 'p' },
            { text: '联系人: ' + this.form.demand_company_legal_person, style: 'p' },

            { text: ' ', style: 'p' },
            { text: '已方: ' + this.form.design_company_name, style: 'p' },
            { text: '地址: ' + this.form.design_company_address, style: 'p' },
            { text: '电话: ' + this.form.design_company_address, style: 'p' },
            { text: '联系人: ' + this.form.design_company_legal_person, style: 'p' },

            { text: '设计项目内容和费用', style: 'title' },
            {
              text: [
                { text: '依照中华人民共和国法律及本行业相关法规条例之规定，甲乙双方本着平等自愿和互惠互利的原则，由乙方接受委托为甲方提供' },
                { text: '     ' + this.form.design_type + '     ', style: 'write' },
                { text: '设计' },
                { text: '     ' + this.form.design_type_paragraph + '     ', style: 'write' },
                { text: '款（包含：' },
                { text: '     ' + this.form.design_type_contain + '     ', style: 'write' },
                { text: '）。本合同设计费用总额为人民币' },
                { text: '     ' + this.form.total + '     ', style: 'write' },
                { text: '整（￥元）。  双方共同签署此项设计委托合同（以下简称合同），甲方和乙方合称为合同双方（以下简称双方）。' }
              ],
              style: 'p'
            },

            { text: '工作时间', style: 'title' },
            {
              text: [
                { text: '1 自项目启动之日起' },
                { text: '     ' + this.form.project_start_date + '     ', style: 'write' },
                { text: '日内，乙方应在甲方配合下完善确定设计方向、市场研究部署、执行内部草图概念设计、内部头脑风暴决议等。' }
              ],
              style: 'p'
            },
            {
              text: [
                { text: '2 设计方向确定之日起' },
                { text: '     ' + this.form.determine_design_date + '     ', style: 'write' },
                { text: '日内，乙方应进行Layout结构布局设计验证结构。' }
              ],
              style: 'p'
            },
            {
              text: [
                { text: '3 结构布局验证完成后' },
                { text: '     ' + this.form.structure_layout_date + '     ', style: 'write' },
                { text: '日内，乙方应向甲方提供该项目ID概念PPT效果图叁款。' }
              ],
              style: 'p'
            },
            {
              text: [
                { text: '4 PPT效果图经甲方商讨并提出书面意见后' },
                { text: '     ' + this.form.design_sketch_date + '     ', style: 'write' },
                { text: '日内，乙方应向甲方提供该项目最终由甲方选定的PPT 3D渲染效果图壹款。' }
              ],
              style: 'p'
            },
            {
              text: [
                { text: '5 最终3D效果图经甲方书面确认后' },
                { text: '     ' + this.form.end_date + '     ', style: 'write' },
                { text: '日内，乙方应完成3D外观防护建模设计内容。 6 上述之进度系乙方所需有效工作日，具体进度参照乙方提供的设计流程计划书。' }
              ],
              style: 'p'
            },

            { text: '支付时间', style: 'title' },
            { text: '甲方按下列付款时间和金额分阶段，以银行托付方式或者其他方式向乙方支付设计费：', style: 'p' },
            {
              text: [
                { text: '1、自合同签署起3日内，甲方向乙方支付设计费的30%，即人民币' },
                { text: '     ' + this.form.one_third_total + '     ', style: 'write' },
                { text: '整(￥)（作为本项目启动预付款）。乙方收到甲方款项或相应付款凭证后即开始本项目的设计运作。' }
              ],
              style: 'p'
            },
            {
              text: [
                { text: '2、乙方完成外观设计并为甲方确认后（以确认书为准），甲方即于3日内支付该项目设计费的' },
                { text: '     ' + this.form.exterior_design_percentage + '     ', style: 'write' },
                { text: '%，即人民币' },
                { text: '     ' + this.form.exterior_design_money + '     ', style: 'write' },
                { text: '整(￥)。乙方收到甲方款项或相应付款凭证后开始' },
                { text: '     ' + this.form.exterior_design_phase + '     ', style: 'write' },
                { text: '设计阶段的运作。' }
              ],
              style: 'p'
            },
            {
              text: [
                { text: '3、乙方完成外观建模设计并为甲方确认后（以确认书为准），甲方即于3日内支付该项目设计费的设计的' },
                { text: '     ' + this.form.exterior_modeling_design_percentage + '     ', style: 'write' },
                { text: '%，即人民币' },
                { text: '     ' + this.form.exterior_modeling_design_money + '     ', style: 'write' },
                { text: '整(￥)。乙方收到甲方款项或相应付款凭证后即提交该项目全部成果之电子文档，即此项目结案，乙方应协助甲方立即开始展开模型制作和量产接洽跟踪服务。' }
              ],
              style: 'p'
            },

            { text: '1.1 甲方责任与义务', style: 'title' },
            { text: '1.1.1 以书面形式提出对本设计项目的要求及有关技术资料。在双方合作的全过程中，向乙方提供必要的咨询，并委派专人（对该项目的方案评审具有决定权）负责本项目的事务接洽和业务联系。', style: 'p' },
            { text: '1.1.2 配合乙方的设计工作，积极参与该项目设计每个阶段的结果评审，及时得出结论并确认给乙方。', style: 'p' },
            { text: '1.1.3 甲方的任何修改意见，应以书面形式通知给乙方（电子邮件）。', style: 'p' },
            { text: '1.1.4 甲方不得以任何形式使用或转让乙方提供的除正选方案之外的其它方案，除非双方达成进一步关于其他方案合作的书面认同。', style: 'p' },
            { text: '1.1.5 甲方按照合同的规定，及时按量地支付每一期的费用给乙方。', style: 'p' },
            { text: '1.1.6 设计方案一旦经甲方确认后，如再发生改动，乙方将按实际工作量另行收费。', style: 'p' },
            { text: '1.1.7 在甲方实际生产之前，甲方的供应生产商应对结构设计文档进行仔细分析，如乙方结构设计存在不合理之处，应给乙方以书面确认，及时沟通处理。', style: 'p' },
            { text: '1.1.8 在乙方为甲方提供最终设计方案后，若因甲方产品结构或用途而变更设计方案，视其为新方案设计，甲方应向乙方支付完成现阶段设计费用后，乙方将按实际工作量另行对修改工作收取费用。', style: 'p' },

            { text: '1.2 乙方责任与义务', style: 'title' },
            {
              text: [
                { text: '1.2.1 乙方设计工作内容包括:' },
                { text: '     ' + this.form.design_work_content + '     ', style: 'write' },
                { text: '工期数量双方根据具体情况协商确定，附设计流程协议书，详细制定设计流程、各阶段工期、内容数量。' }
              ],
              style: 'p'
            },
            { text: '1.2.2 在方案设计阶段，乙方根据甲方提出的产品定位、目标市场设想等指引文件，在预付款支付以及相关资料交付后，提供设计草方案。', style: 'p' },
            { text: '1.2.3 乙方须充分听取甲方的意见，并在接到甲方书面形式修改意见后，依据进行修改。根据选定方案及甲方反馈的修改意见，进行定案二维效果图制作，以及后期的全尺寸油泥模型制作，逆向建模，结构设计，样件试制试装。', style: 'p' },
            { text: '1.2.4 乙方在设计方案修改完成，通过甲方评审并书面确认后，将最终设计方案以三维数模、二维图纸的文件方式交付给甲方。', style: 'p' },
            { text: '1.2.5 乙方未经甲方同意，不得将任何甲方提供的与之产品相关的资料（照片、图纸、参数等）公开发布或泄露给第三方，并在设计开发的过程中，对此设计项目的设计草图、效果图、油泥模型、三维数据、二维图纸、样件保密。应甲方要求，亦可在设计项目完成后的一定时间内，对设计项目的上述相关内容进行保密，并签订相应的保密协议书。', style: 'p' },

            { text: '知识产权', style: 'title' },
            { text: '1、对因本合同产生的甲方选定方案，其全部知识产权由甲方所有。乙方保留设计者署名权。除甲方选定的方案外，落选方案的全部知识产权仍归乙方所有。 ', style: 'p' },
            { text: '2、乙方保证其设计方案不侵犯任何第三方的知识产权。 ', style: 'p' },
            { text: '3、乙方对本合同的内容、设计成果及其涉及的文档、数据资料负有保密义务，未经甲方许可，不得向任何第三方泄密。保密期限为一年（从本合同签订之日起计算），保密期间，落选的备用方案的文档资料不能泄露给第三方。 ', style: 'p' },
            { text: '4、任何一方如遇政府法令或法律程序要求向第三方提供上述资料，可按规定提供，但应尽快将此项事实通知对方。', style: 'p' },

            { text: '违约责任', style: 'title' },
            { text: '1、如甲方对乙方在设计过程中工作内容不满意，有权中止本合同，不再继续支付剩余之款项，乙方亦不退还甲方已付款项。', style: 'p' },
            { text: '2、如设计过程中甲方不能积极配合乙方工作，严重影响乙方的工作安排，在收到乙方书面通知后仍不能积极配合，则乙方有权中止合同。', style: 'p' },
            { text: '3、如甲方不能按照合同规定支付给乙方各设计阶段的设计费用，乙方有权中止合同。', style: 'p' },
            { text: '4、如甲方未付清该合同全部设计款项，则该项目所有设计方案之知识产权仍归乙方所有。', style: 'p' },
            { text: '5、对因本合同产生的甲方选定方案，其全部知识产权由甲方所有。乙方保留设计者署名权。除甲方选定的方案外，落选方案的全部知识产权仍归乙', style: 'p' },

            { text: '不可抗力', style: 'title' },
            { text: '1、本合同所指不可抗力包括地震、水灾、火灾、战争、政府行动、意外事件或其他双方所不能预见、不能避免并不能克服的事件。', style: 'p' },
            { text: '2、由于不可抗力原因致使本合同无法履行时，无法履行合同义务的一方应在15日内将不能履行合同的事实通知另一方，本合同自动终止。 ', style: 'p' },
            { text: '3、由于不可抗力原因致使本合同项目开发中断，项目交付日期及付款日期相应顺延，双方不承担违约责任。如中断超过30日，本合同自动终止。', style: 'p' },

            { text: '争议解决', style: 'title' },
            { text: '本合同签订后，未经双方同意不得单方面中止，否则由责任方承担造成的责任。与合同有关的争议或执行中产生的争议将通过友好协商解决。如不能达成一致，可向合同签订地的仲裁机构申请仲裁，也可以直接向人民法院起诉。', style: 'p' },

            { text: '', style: 'p' }
          ],
          defaultStyle: {
            font: '微软雅黑'
          },

          styles: {
            header: {
              fontSize: 20,
              bold: true,
              alignment: 'center',
              margin: [ 0, 10, 0, 20 ]
            },
            title: {
              fontSize: 12,
              bold: true,
              margin: [0, 20, 0, 2]
            },
            p: {
              fontSize: 10,
              margin: [0, 2, 0, 2]
            },
            write: {
              decoration: 'underline'
            },
            anotherStyle: {
              italics: true,
              alignment: 'right'
            }
          }
        }

        window.pdfMake.fonts = {
          Roboto: {
            normal: 'Roboto-Regular.ttf',
            bold: 'Roboto-Medium.ttf',
            italics: 'Roboto-Italic.ttf',
            bolditalics: 'Roboto-Italic.ttf'
          },
          微软雅黑: {
            normal: '微软雅黑.ttf',
            bold: '微软雅黑.ttf',
            italics: '微软雅黑.ttf',
            bolditalics: '微软雅黑.ttf'
          }
        }

        window.pdfMake.createPdf(dd).download(this.itemName + '.pdf')
        this.downStatus = '已成功下载合同文件，请关闭当前页面'
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
        that.$http.get(api.contractId.format(uniqueId), {})
        .then (function(response) {
          if (response.data.meta.status_code === 200) {
            var item = response.data.data
            if (item) {
              that.itemId = item.id
              // 重新渲染
              that.$nextTick(function() {
                that.itemName = item.item_name + '合作协议'
                that.form = item
                // 生成pdf插件太大，实现懒加载
                require.ensure(['../../../../../static/js/pdfmake.min.js', '../../../../../static/js/vfs_fonts.js'], function (require) {
                  require('../../../../../static/js/pdfmake.min.js')
                  require('../../../../../static/js/vfs_fonts.js')
                  that.downBtn()
                })
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

  .container {
    min-height: 300px;
  }

  .show-box {
    text-align: center;
  }
  .show-box p {
    font-size: 2rem;
  }

</style>
