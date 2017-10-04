<template>
  <div class="container">
    <div class="blank20"></div>
    <el-row :gutter="20">
      <v-menu currentName="profile"></v-menu>

      <el-col :span="20">
        <div class="right-content">
          <v-menu-sub currentSubName="identification"></v-menu-sub>
          <div class="content-box" v-loading.body="isLoading">
            <div class="form-title">
              <span>企业实名认证</span>
            </div>
            <el-form :label-position="labelPosition" :model="form" :rules="ruleForm" ref="ruleForm" label-width="80px">

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="企业名称" prop="company_name">
                    <el-input v-model="form.company_name" name="company_name" ref="company_name" placeholder="请输入完整的公司名称"></el-input>
                  </el-form-item>           
                </el-col>
              </el-row>

              <el-form-item label="证件类型" prop="company_type">
                <el-select v-model.number="form.company_type" style="width: 350px;" placeholder="请选择证件类型">
                  <el-option
                    v-for="(d, index) in certificateTypeOptions"
                    :label="d.label"
                    :key="index"
                    :value="d.value">
                  </el-option>
                </el-select>
              </el-form-item>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="统一社会信用代码" prop="registration_number">
                    <el-input v-model="form.registration_number" placeholder=""></el-input>
                  </el-form-item>          
                </el-col>
              </el-row>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="公司法人营业执照" prop="">
                    <el-upload
                      class=""
                      :action="uploadParam.url"
                      :on-preview="handlePreview"
                      :on-remove="handleRemove"
                      :file-list="fileList"
                      :data="uploadParam"
                      :on-error="uploadError"
                      :on-success="uploadSuccess"
                      :before-upload="beforeUpload"
                      list-type="text">
                      <el-button size="small" type="primary">点击上传</el-button>
                      <div slot="tip" class="el-upload__tip">只能上传jpg/pdf文件，且不超过5M</div>
                    </el-upload>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="法人姓名" prop="legal_person">
                    <el-input v-model="form.legal_person" placeholder=""></el-input>
                  </el-form-item>          
                </el-col>
              </el-row>

              <el-form-item label="证件类型" prop="document_type">
                <el-select v-model.number="form.document_type" placeholder="请选择证件类型">
                  <el-option
                    v-for="(d, index) in documentTypeOptions"
                    :label="d.label"
                    :key="index"
                    :value="d.value">
                  </el-option>
                </el-select>
              </el-form-item>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="证件号码" prop="document_number">
                    <el-input v-model="form.document_number" placeholder=""></el-input>
                  </el-form-item>          
                </el-col>
              </el-row>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-form-item label="法人证件" prop="">
                    <el-upload
                      class=""
                      :action="uploadParam.url"
                      :on-preview="handlePreview"
                      :on-remove="handleRemove"
                      :file-list="filePersonList"
                      :data="uploadParam"
                      :on-error="uploadError"
                      :on-success="uploadSuccessPerson"
                      :before-upload="beforeUploadPerson"
                      list-type="picture">
                      <el-button size="small" type="primary">点击上传</el-button>
                      <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过5M</div>
                    </el-upload>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-row :gutter="10">
                <el-col :span="6">
                  <el-form-item label="联系人" prop="contact_name">
                    <el-input v-model="form.contact_name" placeholder=""></el-input>
                  </el-form-item>             
                </el-col>
                <el-col :span="6">
                  <el-form-item label="职位" prop="position">
                    <el-input v-model="form.position" placeholder=""></el-input>
                  </el-form-item>             
                </el-col>
                <el-col :span="6">
                  <el-form-item label="手机" prop="phone">
                    <el-input v-model="form.phone" placeholder=""></el-input>
                  </el-form-item>             
                </el-col>
                <el-col :span="6">
                  <el-form-item label="邮箱" prop="email">
                    <el-input v-model="form.email" placeholder=""></el-input>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-row :gutter="24">
                <el-col :span="12">
                  <el-checkbox v-model="agreement" disabled><a href="javascript:void(0);" @click="agreementBtn">已阅读并同意《铟果SaaS协议》</a></el-checkbox>
                </el-col>
              </el-row>

              <div class="form-btn">
                  <el-button @click="returnBase">返回</el-button>
                  <el-button :loading="isLoadingBtn" class="is-custom" type="primary" @click="submit('ruleForm')">提交审核</el-button>
              </div>
              <div class="clear"></div>
            </el-form>

          </div>
        </div>

      </el-col>
    </el-row>

    <el-dialog
      title="铟果SaaS协议"
      :visible.sync="dialogAgreement"
      top="5%"
      size="large">
      <div>
        <div class="agree-content">
          <p>本协议由缔约双方在自愿、平等、公平及诚实信用原则的基础上，根据《中华人民共和国合同法》等相关法律、法规的规定，经友好协商缔结。</p>
          <p>本协议由协议正文、附件及依据本协议公示于铟果开放平台的各项规则所组成，协议附件及规则与本协议具有同等法律效力，如规则与本协议约定不一致的，以公布生效日期或签署日期在后的文件为准执行。 </p>
          <p>本协议各条的标题仅为方便阅读而设，无意成为本协议的一部分，也不影响本协议的含义或解释。</p>
          <p>&nbsp;</p>
          <p class="a-title">第一条 相关定义及解释</p>
          <p>1.关于铟果</p>
          <p>铟果，太火鸟旗下基于大数据和智能匹配技术的创意交易平台，以京东金融为支持方，2017年正式开始运营，致力重新定义创新，为制造企业和设计服务供应商服务。</p>
          <p>铟果平台同意按照本协议规定发布的操作规则提供基于互联网以及移动网相关服务（以下称"网络服务"），为获得网络服务，平台所有用户应当同意本协议的全部条款并按照页面上的提示完成全部的注册程序。用户在使用过程中完成全部流程即表示完全接受本协议项下的全部条款以及公示于铟果开放平台的各项规则。</p>
          <p>平台有权根据具体需要修订服务条款或各类规则，届时会以系统通知的形式告知所有用户。</p>
          <p>&nbsp;</p>
          <p>在铟果平台注册、发布、承接设计项目的用户，必须是可以代表企业的完全民事行为能力人或法人。用户享有万千民事行为能力人或法人的权利，也要遵守相关的义务，遵守平台规则与相关的法律法规。用户需对平台上的所有操作负法律责任。</p>
          <p>平台所有用户应遵循的原则包括但不仅限于以下条款</p>
          <p>遵守中国有关的法律和法规；</p>
          <p>遵守所有与网络服务有关的网络协议、规定和程序；</p>
          <p>遵守铟果平台所有规定；</p>
          <p>不得为任何非法目的而使用网络服务系统；</p>
          <p>不得利用铟果网络服务系统进行任何可能对互联网正常运转造成不利影响的行为；</p>
          <p>不得侵犯其他任何第三方的专利权、著作权、商标权、名誉权或其他任何合法权益；</p>
          <p>不得通过价格控制等不正当手段进行不当竞争；</p>
          <p>不得采取不正当手段（包括但不仅限于虚假交易、互换好评等方式）提高自身或他人信用度，或采用不正当手段恶意评价其他用户，降低其他用户信用度；</p>
          <p>&nbsp;</p>
          <p>作为铟果平台的用户，我们需要您做到：</p>
          <p>严肃认真对待您在铟果平台上的账号，保证提供的所欲信息真实、准确，如有变更，及时在系统中更改；</p>
          <p>铟果平台账户的所有内容与信息都与您的权利与义务直接相关，请您妥善保管，勿转借他人；若因黑客行为、第三方恶意攻击、用户的保管疏忽导致用户损失的，平台不承担任何责任；</p>
          <p>另，由于铟果与京东金融的合作关系，平台用户在遵守平台规定的同时，还应遵守京东金融相应规定与要求。</p>
          <p>&nbsp;</p>
          <p class="a-title">第二条 服务内容及乙方操作</p>
          <p>1.甲方根据本平台相关条款为经过实名认证的乙方服务，乙方自认可铟果平台所有条款并完成企业实名认证后，可以在铟果平台承接设计项目，与项目需求方订立合同并获得相应收益。</p>
          <p>2.乙方可享受甲方提供的相应服务与保障，包括知识产权保护、隐私保护等</p>
          <p>2（1）.	知识产权</p>
          <p>铟果平台为项目需求方与设计服务供应商提供合作平台，对所有用户提供必需的服务，在服务过程中遵守国家关于版权和知识产权的相关法律法规。</p>
          <p>2（2）.	隐私保护</p>
          <p>铟果承诺不对外公开或向第三方提供单个用户的注册资料及用户在使用网络服务时存储在平台的非公开内容；</p>
          <p>为保证平台正常运转，平台会对用户提交的资料进行审核，如发现不妥之处，有权要求用户更改或完善；</p>
          <p>若用户在平台行为涉及违法违规，平台有权配合相关法律机构进行调查；有以下情况时，铟果不严格遵守对用户的隐私保护条款：</p>
          <p>事先获得用户的明确授权；</p>
          <p>根据有关的法律法规要求或相关政府主管部门的要求；</p>
          <p>为维护平台其他用户、平台自身或社会公众的利益。</p>
          <p>&nbsp;</p>
          <p class="a-title">第三条 双方权利及义务</p>
          <p>1.甲方根据本协议向乙方提供电子商务交易平台及相应技术支持服务，尽力维护“铟果平台”的正常稳定运行，并努力提升和改进技术，对平台功能及服务进行更新、升级，不断提升平台性能和交易效率。</p>
          <p>2.甲方对乙方在使用“铟果平台”过程中遇到的问题及提出的建议和意见积极予以回复，可依乙方需求对其使用“铟果平台”提供合理的指导和培训。</p>
          <p>3.乙方同意甲方根据乙方营业执照所载明的经营范围及乙方申请经营的经营类目，核实及调整乙方在“铟果平台”经营的具体商品的种类、数量和类目范围。</p>
          <p>4.乙方同意并自愿遵守甲方根据国家相关法律法规、政策及平台运营情况，对公示于“铟果平台”的规则、流程、收费标准等进行更新、调整。</p>
          <p>5.乙方同意甲方对乙方提供的相关资质文件及各类信息资料进行审核。甲方审核为形式审核，审核通过并不代表甲方对审核内容的真实性、合法性、准确性、及时性的确认，乙方仍须对其提交的资料的真实性、合法性、准确性、及时性等承担相应的法律责任。</p>
          <p>6.甲方有权对乙方的注册信息、上传的相关数据信息、在“铟果平台”发布的其他信息及交易行为进行监督检查，对发现的违法违规信息及其相关内容，乙方同意甲方不经通知立即删除，对发现的其他问题或疑问有权向乙方发出询问及要求改正的通知，乙方应在接到通知后立即做出说明或改正。对乙方前述不当行为，甲方有权追究其违约、侵权责任并/或解除本协议。</p>
          <p>7.乙方多次违规或者有严重违约、违规情形的，甲方有权对其在铟果平台上的任何操作并要求相应赔偿直至终止本协议等措施，上述措施不足以补偿甲方损失的，甲方有权继续向乙方追偿。</p>
          <p>8.甲方有权要求乙方提供与乙方设计服务相关的信息，在指定的时限内予以回复或给出相应方案，对乙方未及时解决的需求方咨询及投诉，甲方有权对乙方采取相应处理措施。</p>
          <p>9.如因乙方提供的设计服务问题引发项目需求方对甲方及/或“铟果平台”的诉讼，乙方应承担因客户诉讼而产生的全部法律责任，如因此而给甲方及/或“铟果平台”造成损失的，甲方有权要求乙方赔偿甲方及“铟果平台”的全部损失。</p>
          <p>10.乙方同意“铟果平台”根据业务调整情况将本协议项下的权利和义务部分或全部转移给甲方关联公司，但须提前5日通知乙方。</p>
          <p>&nbsp;</p>
          <p class="a-title">第四条 违约责任</p>
          <p>1.乙方向甲方提供虚假、失效的证明文件或其他相关证明，在“铟果平台”发布错误、虚假、违法及不良信息或进行其他违反本协议约定的行为，给甲方及/或“铟果平台”造成任何损失的（损失包括但不限于诉讼费、律师费、赔偿、补偿、行政机关处罚、差旅费等），乙方同意甲方自乙方缴纳的保证金及未结算货款中直接予以扣除，本协议另有约定的除外。 </p>
          <p>2.乙方不论采取何种方式将甲方用户吸引到甲方平台以外的平台或场所进行交易或绕开甲方指定付款方式进行交易的，以及非法获取“铟果平台”系统数据、利用“铟果平台”谋取不正当利益或从事非法活动的，甲方有权扣除乙方全部保证金作为违约金，并保留向乙方继续追究违约责任的权利。</p>
          <p>3.乙方发生违反本协议及铟果平台规则的情形时，甲方除有权按照本条约定要求乙方承担违约责任外，还有权按照“铟果平台”相关管理规则采取相应处罚措施，包括但不仅限于暂时关闭乙方后台管理账户、暂缓支付未结算款项、终止合作等措施。</p>
          <p>&nbsp;</p>
          <p class="a-title">第五条 有限责任及免责</p>
          <p>1.不论在何种情况下，甲方均不对由于电力、网络、电脑、通讯或其他系统的故障、罢工（含内部罢工或劳工骚乱）、劳动争议、暴乱、起义、骚乱、生产力或生产资料不足、火灾、洪水、风暴、爆炸、战争、政府行为等不可抗力，国际、国内法院的命令或第三方的不作为而造成的不能服务或延迟服务承担责任。</p>
          <p>2.本协议项下服务将按“现状”和按“可得到”的状态提供，甲方在此明确声明对服务不作任何明示或暗示的保证，包括但不限于对服务的可适用性、没有错误或疏漏、持续性、准确性、可靠性、适用于某一特定用途。</p>
          <p>3.使用“铟果平台”服务下载或者获取任何资料的行为均出于乙方的独立判断，并由其自行承担因此而可能产生的风险和责任。</p>
          <p>4.法律地位声明：甲方为向乙方及甲方其他用户进行网上交易活动提供交易平台的平台服务提供商，并非乙方与通过“铟果平台”购买乙方商品的甲方其他用户之间交易行为的参与方，甲方不对乙方及参与交易的甲方其他用户的任何口头、书面陈述或承诺，发布的信息及交易行为的真实性、合法性、准确性、及时性、有效性等作任何明示或暗示的保证，亦不承担任何法律责任。若因乙方与参与交易的甲方其他用户之间的交易行为引起的任何法律纠纷，包括但不限于投诉、起诉、举报及税赋等，均由参与交易的双方解决，与甲方及/或“铟果平台”无关。但，乙方怠于履行义务时，甲方有权介入乙方与甲方其他用户间的争议，依据一般人的认知程度对该争议进行判断和处置，乙方应当予以执行。</p>
          <p>5.不可抗力处理：如本协议履行期间，甲乙双方任何一方遭受不可抗力，均应在遭受不可抗力后尽快通知对方，并于通知之日起15日内提供相关证明文件，不可抗力持续达到三十日的，任一方有权经通知对方提前终止本协议。因不可抗力原因而导致本协议中止、终止的，双方均不须向对方承担违约责任。</p>
          <p>&nbsp;</p>
          <p class="a-title">第六条 其他</p>
          <p>1.协议有效期：本协议自双方签署后成立，持续对缔约双方有效。</p>
          <p>2.	举报机制</p>
          <p>若您在平台使用过程中发现任何违法、违规或侵害您权益的现象，请及时与铟果联络，寻求法律手段处理不当现象。</p>
          <p>关于实名认证等铟果平台的具体操作流程与细则，详见“帮助栏”。</p>
          <p>铟果平台对以上协议内容拥有最终解释权。</p>

        </div>
        <div class="diage-footer">
          <el-button type="primary" class="is-custom" @click="agreeTrueBtn">我同意</el-button>
        </div>
      </div>

    </el-dialog>

  </div>
</template>

<script>
  import vMenu from '@/components/pages/v_center/Menu'
  import vMenuSub from '@/components/pages/v_center/company/MenuSub'
  import api from '@/api/api'
  import typeData from '@/config'

  import '@/assets/js/format'

  export default {
    name: 'vcenter_company_identification',
    components: {
      vMenu,
      vMenuSub
    },
    data () {
      var checkNumber = (rule, value, callback) => {
        if (!value) {
          return callback(new Error('请添写公司注册号!'))
        }
        setTimeout(() => {
          var len = value.toString().length
          if (len === 15 || len === 18) {
            callback()
          } else {
            callback(new Error('注册号长度应为15或18位'))
          }
        }, 1000)
      }
      return {
        isLoading: false,
        isLoadingBtn: false,
        userId: this.$store.state.event.user.id,
        companyId: '',
        labelPosition: 'top',
        fileList: [],
        filePersonList: [],
        upToken: null,
        uploadParam: {
          'url': '',
          'token': '',
          'x:random': '',
          'x:user_id': this.$store.state.event.user.id,
          'x:target_id': '',
          'x:type': 0
        },
        imageUrl: '',
        agreement: false,
        dialogAgreement: false,
        form: {
          company_name: '',
          company_type: '',
          registration_number: '',
          legal_person: '',
          document_number: '',
          document_type: '',
          contact_name: '',
          position: '',
          phone: '',
          email: '',

          test: ''
        },

        ruleForm: {
          company_name: [
            { required: true, message: '请添写公司全称', trigger: 'blur' }
          ],
          company_type: [
            { type: 'number', required: true, message: '请选择证件类型', trigger: 'change' }
          ],
          registration_number: [
            { validator: checkNumber, trigger: 'blur' }
          ],
          legal_person: [
            { required: true, message: '请添写法人真实姓名', trigger: 'blur' }
          ],
          document_number: [
            { required: true, message: '请添写法人证件号码', trigger: 'blur' },
            { max: 20, min: 4, message: '证件号码格式不正确', trigger: 'blur' }
          ],
          document_type: [
            { type: 'number', required: true, message: '请选择法人证件类型', trigger: 'change' }
          ],
          contact_name: [
            { required: true, message: '请添写联系人', trigger: 'blur' }
          ],
          phone: [
            { required: true, message: '请添写联系电话', trigger: 'blur' }
          ],
          email: [
            { required: true, message: '请添写联系人邮箱', trigger: 'blur' },
            { type: 'email', message: '请输入正确的邮箱格式', trigger: 'blur' }
          ],
          position: [
            { required: true, message: '请添写联系人职位', trigger: 'blur' }
          ]
        }
      }
    },
    methods: {
      submit(formName) {
        const that = this
        if (this.agreement !== true) {
          that.$message.error('公司认证之前请先确认条款!')
          return false
        }
        that.$refs[formName].validate((valid) => {
          // 验证通过，提交
          if (valid) {
            var row = {
              registration_number: that.form.registration_number,
              company_name: that.form.company_name,
              company_type: that.form.company_type,
              legal_person: that.form.legal_person,
              document_number: that.form.document_number,
              document_type: that.form.document_type,
              contact_name: that.form.contact_name,
              position: that.form.position,
              email: that.form.email,
              phone: that.form.phone
            }

            if (that.companyId) {
            } else {
              if (that.uploadParam['x:random']) {
                row.random = that.uploadParam['x:random']
              }
            }
            that.isLoadingBtn = true
            that.$http({method: 'POST', url: api.designCompany, data: row})
            .then (function(response) {
              that.isLoadingBtn = false
              if (response.data.meta.status_code === 200) {
                that.$message.success('提交成功,等待审核')
                that.$router.push({name: 'vcenterComputerAccreditation'})
                return false
              } else {
                that.$message.error(response.data.meta.message)
              }
            })
            .catch (function(error) {
              that.$message.error(error.message)
              return false
            })
          } else {
            return false
          }
        })
      },
      // 返回基本信息页
      returnBase() {
        this.$router.push({name: 'vcenterComputerBase'})
      },
      handleRemove(file, fileList) {
        if (file === null) {
          return false
        }
        var assetId = file.response.asset_id
        const that = this
        that.$http.delete(api.asset.format(assetId), {})
        .then (function(response) {
          if (response.data.meta.status_code === 200) {
          } else {
            that.$message.error(response.data.meta.message)
            return false
          }
        })
        .catch (function(error) {
          that.$message.error(error.message)
          console.log(error.message)
          return false
        })
      },
      // 同意协议弹层
      agreementBtn () {
        this.dialogAgreement = true
      },
      // 确认协议并同意
      agreeTrueBtn () {
        this.dialogAgreement = false
        this.agreement = true
      },
      handlePreview(file) {
        console.log(file)
      },
      handleChange(value) {
        console.log(value)
      },
      uploadError(err, file, fileList) {
        this.$message.error(err + '附件上传失败!')
      },
      uploadSuccess(response, file, fileList) {
      },
      uploadSuccessPerson(response, file, fileList) {
      },
      beforeUpload(file) {
        const arr = ['image/jpeg', 'image/gif', 'image/png', 'application/pdf']
        const isLt5M = file.size / 1024 / 1024 < 5

        this.uploadParam['x:type'] = 3

        console.log(file)
        if (arr.indexOf(file.type) === -1) {
          this.$message.error('上传文件格式不正确!')
          return false
        }
        if (!isLt5M) {
          this.$message.error('上传文件大小不能超过 5MB!')
          return false
        }
      },
      beforeUploadPerson(file) {
        const arr = ['image/jpeg', 'image/gif', 'image/png']
        const isLt5M = file.size / 1024 / 1024 < 5

        this.uploadParam['x:type'] = 10

        console.log(file)
        if (arr.indexOf(file.type) === -1) {
          this.$message.error('上传文件格式不正确!')
          return false
        }
        if (!isLt5M) {
          this.$message.error('上传文件大小不能超过 5MB!')
          return false
        }
      }
    },
    computed: {
      documentTypeOptions() {
        var items = []
        for (var i = 0; i < typeData.DOCUMENT_TYPE.length; i++) {
          var item = {
            value: typeData.DOCUMENT_TYPE[i]['id'],
            label: typeData.DOCUMENT_TYPE[i]['name']
          }
          items.push(item)
        }
        return items
      },
      certificateTypeOptions() {
        var items = []
        for (var i = 0; i < typeData.COMPANY_CERTIFICATE_TYPE.length; i++) {
          var item = {
            value: typeData.COMPANY_CERTIFICATE_TYPE[i]['id'],
            label: typeData.COMPANY_CERTIFICATE_TYPE[i]['name']
          }
          items.push(item)
        }
        return items
      }
    },
    watch: {
    },
    created: function() {
      var uType = this.$store.state.event.user.type
      // 如果非设计公司，跳到相应页面
      if (uType !== 2) {
        this.$router.replace({name: 'vcenterDComputerIdentification'})
        return
      }
      const that = this
      that.isLoading = true
      that.$http.get(api.designCompany, {})
      .then (function(response) {
        that.isLoading = false
        if (response.data.meta.status_code === 200) {
          if (response.data.data) {
            // 重新渲染
            that.$nextTick(function() {
              that.form = response.data.data
              that.form.registration_number = that.form.registration_number
              that.form.company_type = that.form.company_type === 0 ? '' : that.form.company_type
              that.form.document_type = that.form.document_type === 0 ? '' : that.form.document_type
              that.companyId = response.data.data.id
              that.uploadParam['x:target_id'] = response.data.data.id

              if (response.data.data.logo_image) {
                that.imageUrl = response.data.data.logo_image.small
              }
              // 法人营业执照
              if (response.data.data.license_image) {
                var files = []
                for (var i = 0; i < response.data.data.license_image.length; i++) {
                  if (i > 5) {
                    break
                  }
                  var obj = response.data.data.license_image[i]
                  var item = {}
                  item['response'] = {}
                  item['name'] = obj['name']
                  item['url'] = obj['small']
                  item['response']['asset_id'] = obj['id']
                  files.push(item)
                }
                that.fileList = files
              }
              // 法人证件
              if (response.data.data.document_image) {
                var personFiles = []
                for (var j = 0; j < response.data.data.document_image.length; j++) {
                  if (j > 5) {
                    break
                  }
                  var pObj = response.data.data.document_image[j]
                  var personItem = {}
                  personItem['response'] = {}
                  personItem['name'] = pObj['name']
                  personItem['url'] = pObj['small']
                  personItem['response']['asset_id'] = pObj['id']
                  personFiles.push(personItem)
                }
                that.filePersonList = personFiles
              }
            })
          }
        }
      })
      .catch (function(error) {
        that.$message.error(error.message)
        that.isLoading = false
        return false
      })

      that.$http.get(api.upToken, {})
      .then (function(response) {
        if (response.data.meta.status_code === 200) {
          if (response.data.data) {
            that.uploadParam['token'] = response.data.data.upToken
            that.uploadParam['x:random'] = response.data.data.random
            that.uploadParam.url = response.data.data.upload_url
          }
        }
      })
      .catch (function(error) {
        that.$message.error(error.message)
        return false
      })
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .form-btn {
    float: right;
  }
  .form-btn button {
    width: 120px;
  }

  .diage-footer {
    text-align: center;
  }
  .diage-footer button {
    padding: 10px 30px;
  }

  .agree-content {
  
  }
  .agree-content h3 {
  
  }
  .agree-content p {
    line-height: 1.5;
    font-size: 1.2rem;
  }
  .agree-content .a-title {
    font-size: 1.3rem;
    line-height: 2;
    color: #222;
  }


</style>
