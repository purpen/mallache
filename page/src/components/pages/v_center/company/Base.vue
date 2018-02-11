<template>
  <div class="container blank40">
    <el-row :gutter="24">
      <v-menu currentName="profile" :class="[isMob ? 'v-menu' : '']"></v-menu>
      <el-col :xs="24" :sm="20" :md="20" :lg="20">
        <div class="right-content">
          <v-menu-sub></v-menu-sub>

          <div :class="['content-box', isMob ? 'content-box-m' : '']" v-loading.body="isLoading">

            <el-row :gutter="gutter" :class="['item', isMob ? 'item-m item-mAvatar' : '']">
              <el-col :span="titleSpan" class="title avatarhead">
                <p>公司logo</p>
                <span v-if="isMob">{{ avatarStr }}</span>
              </el-col>
              <el-col  :xs="12" :sm="20" :md="20" :lg="20" class="content avatarcontent">
                <el-upload
                  class="avatar-uploader"
                  :action="uploadParam.url"
                  :show-file-list="false"
                  :data="uploadParam"
                  :on-progress="avatarProgress"
                  :on-success="handleAvatarSuccess"
                  :before-upload="beforeAvatarUpload">
                  <img v-if="imageUrl" :src="imageUrl" class="avatar">
                  <i v-else class="avatar-uploader-icon"></i>
                  <div slot="tip" class="el-upload__tip" v-if="!isMob">{{ avatarStr }}</div>
                </el-upload>

              </el-col>
            </el-row>

            <el-row :gutter="gutter" :class="['item', isMob ? 'item-m' : '']">
              <el-col :span="titleSpan" class="title">
                <p>公司简称</p>
              </el-col>
              <el-col :span="contentSpan" class="content">
                <el-input v-if="element.company_abbreviation" v-model="form.company_abbreviation" style="width: 300px;"
                          placeholder="如: 太火鸟"></el-input>
                <p v-else>{{ form.company_abbreviation }}</p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.company_abbreviation" title="保存" href="javascript:void(0)"
                   @click="saveBtn('company_abbreviation', ['company_abbreviation'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('company_abbreviation')">编辑</a>
              </el-col>
            </el-row>

            <el-row :gutter="gutter" :class="['item', isMob ? 'item-m' : '']">
              <el-col :span="titleSpan" class="title">
                <p>公司英文名称</p>
              </el-col>
              <el-col :span="contentSpan" class="content">
                <el-input v-if="element.company_english" v-model="form.company_english" style="width: 300px;"
                          placeholder="如: thn"></el-input>
                <p v-else>{{ form.company_english }}</p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.company_english" title="保存" href="javascript:void(0)"
                   @click="saveBtn('company_english', ['company_english'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('company_english')">编辑</a>
              </el-col>
            </el-row>

            <el-row :gutter="gutter" :class="['item', isMob ? 'item-m' : '']">
              <el-col :span="titleSpan" class="title">
                <p>联系人信息</p>
              </el-col>
              <el-col :span="contentSpan" class="content">

                <el-form label-position="left" label-width="50px" style="width: 300px;" v-if="element.contact">
                  <el-form-item label="姓名" style="margin: 0">
                    <el-input v-model="form.contact_name"></el-input>
                  </el-form-item>
                  <el-form-item label="职位" style="margin: 0">
                    <el-input v-model="form.position"></el-input>
                  </el-form-item>
                  <el-form-item label="手机" style="margin: 0">
                    <el-input v-model="form.phone"></el-input>
                  </el-form-item>
                  <el-form-item label="邮箱" style="margin: 0">
                    <el-input v-model="form.email"></el-input>
                  </el-form-item>
                </el-form>

                <div v-else>
                  <p v-show="form.contact_name">{{ form.contact_name }}</p>
                  <p v-show="form.position">{{ form.position }}</p>
                  <p v-show="form.phone">{{ form.phone }}</p>
                  <p v-show="form.email">{{ form.email }}</p>
                </div>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.contact" title="保存" href="javascript:void(0)" @click="saveBtn('contact', ['contact_name', 'phone', 'email', 'position'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('contact')">编辑</a>
              </el-col>
            </el-row>

            <el-row :gutter="gutter" :class="['item', isMob ? 'item-m' : '']">
              <el-col :span="titleSpan" class="title">
                <p>地址</p>
              </el-col>
              <el-col :span="contentSpan" class="content">
                <el-form label-position="top" label-width="50px" style="width: 90%;" v-show="element.address">
                  <region-picker :provinceProp="province" :cityProp="city" :districtProp="district"
                                 :isFirstProp="isFirst" titleProp="详细地址" propStyle="margin: 0;"
                                 @onchange="change"></region-picker>
                  <el-form-item label="" prop="address" style="margin: 0">
                    <el-input v-model="form.address" name="address" ref="address" placeholder="街道地址"></el-input>
                  </el-form-item>
                </el-form>
                <div v-show="!element.address">
                  <p>{{ form.province_value }} {{ form.city_value }} {{ form.area_value }}</p>
                  <p>{{ form.address }}</p>
                </div>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.address" title="保存" href="javascript:void(0)"
                   @click="saveBtn('address', ['province', 'city', 'area', 'address'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('address')">编辑</a>
              </el-col>
            </el-row>

            <el-row :gutter="gutter" :class="['item', isMob ? 'item-m' : '']">
              <el-col :span="titleSpan" class="title">
                <p>公司规模</p>
              </el-col>
              <el-col :span="contentSpan" class="content">

                <el-select v-model.number="form.company_size" placeholder="请选择" v-if="element.company_size">
                  <el-option
                    v-for="item in sizeOptions"
                    :label="item.label"
                    :key="item.index"
                    :value="item.value">
                  </el-option>
                </el-select>

                <p v-else>{{ form.company_size_val }}</p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.company_size" title="保存" href="javascript:void(0)"
                   @click="saveBtn('company_size', ['company_size'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('company_size')">编辑</a>
              </el-col>
            </el-row>

            <el-row :gutter="gutter" :class="['item', isMob ? 'item-m' : '']">
              <el-col :span="titleSpan" class="title">
                <p>公司营收</p>
              </el-col>
              <el-col :span="contentSpan" class="content">

                <el-select v-model.number="form.revenue" placeholder="请选择" v-if="element.revenue">
                  <el-option
                    v-for="item in revenueOptions"
                    :label="item.label"
                    :key="item.index"
                    :value="item.value">
                  </el-option>
                </el-select>

                <p v-else>{{ form.revenue_value }}</p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.revenue" title="保存" href="javascript:void(0)"
                   @click="saveBtn('revenue', ['revenue'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('revenue')">编辑</a>
              </el-col>
            </el-row>
            <el-row :gutter="gutter" :class="['item', isMob ? 'item-m' : '']">
              <el-col :span="titleSpan" class="title">
                <p>实名认证</p>
              </el-col>
              <el-col :span="contentSpan" class="content">
                <p>{{ form.verify_status_label }}</p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <router-link v-if="form.verify_status === 0 || form.verify_status === 2" to="/vcenter/company/accreditation">去认证</router-link>
              </el-col>
            </el-row>

            <el-row :gutter="gutter" :class="['item', isMob ? 'item-m' : '']">
              <el-col :span="titleSpan" class="title">
                <p>专业优势</p>
              </el-col>
              <el-col :span="contentSpan" class="content">

                <el-input
                  type="textarea"
                  :rows="3"
                  v-if="element.advantage"
                  placeholder="专业优势"
                  v-model="form.professional_advantage">
                </el-input>

                <p v-else>{{ form.professional_advantage }}</p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.advantage" title="保存" href="javascript:void(0)"
                   @click="saveBtn('advantage', ['professional_advantage'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('advantage')">编辑</a>
              </el-col>
            </el-row>

            <el-row :gutter="gutter" :class="['item', isMob ? 'item-m' : '']">
              <el-col :span="titleSpan" class="title">
                <p>公司简介</p>
              </el-col>
              <el-col :span="contentSpan" class="content">

                <el-input
                  type="textarea"
                  :rows="3"
                  v-if="element.profile"
                  placeholder="公司简介"
                  v-model="form.company_profile">
                </el-input>

                <p v-else>{{ form.company_profile }}</p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.profile" title="保存" href="javascript:void(0)"
                   @click="saveBtn('profile', ['company_profile'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('profile')">编辑</a>
              </el-col>
            </el-row>

            <el-row :gutter="gutter" :class="['item', isMob ? 'item-m' : '']">
              <el-col :span="titleSpan" class="title">
                <p>奖项荣誉</p>
              </el-col>
              <el-col :span="contentSpan" class="content">

                <el-input
                  type="textarea"
                  :rows="3"
                  v-if="element.awards"
                  placeholder="请输入内容"
                  v-model="form.awards">
                </el-input>

                <p v-else>{{ form.awards }}</p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.awards" title="保存" href="javascript:void(0)"
                   @click="saveBtn('awards', ['awards'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('awards')">编辑</a>
              </el-col>
            </el-row>

            <el-row :gutter="gutter" :class="['item', isMob ? 'item-m' : '']">
              <el-col :span="titleSpan" class="title">
                <p>网址</p>
              </el-col>
              <el-col :span="contentSpan" class="content">

                <el-input v-model="form.web" placeholder="" v-if="element.web">
                  <template slot="prepend">http://</template>
                </el-input>

                <p v-else><a :href="form.web_p" target="_blank">{{ form.web_p }}</a></p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.web" title="保存" href="javascript:void(0)" @click="saveBtn('web', ['web'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('web')">编辑</a>
              </el-col>
            </el-row>

            <el-row :gutter="gutter" :class="['item', isMob ? 'item-m' : '']">
              <el-col :span="titleSpan" class="title">
                <p>微信公众号</p>
              </el-col>
              <el-col :span="contentSpan" class="content">

              <el-input v-model="form.weixin_id" placeholder="请输入微信公众号" v-if="element.weixin_id">
              </el-input>

                <p v-else><a>{{ form.weixin_id }}</a></p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.weixin_id" title="保存" href="javascript:void(0)" @click="saveBtn('weixin_id', ['weixin_id'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('weixin_id')">编辑</a>
              </el-col>
            </el-row>

            <el-row :gutter="gutter" :class="['item', isMob ? 'item-m' : '']">
              <el-col :span="titleSpan" class="title">
                <p>擅长领域</p>
              </el-col>
              <el-col :span="contentSpan" class="content">

                <div v-if="element.good_field" class="type-content">
                  <el-button :class="{ 'tag': true }" size="small" :key="index"
                             @click="selectFieldBtn(d.value, d.label)" v-for="(d, index) in fieldOptions">{{ d.label }}
                  </el-button>

                  <div class="edit-field-tag field-box">
                    <el-tag
                      v-for="(d, index) in form.good_field_value"
                      :key="index"
                      :closable="true"
                      @close="delFieldBtn(index)"
                    >
                      {{ d }}
                    </el-tag>
                  </div>
                </div>
                <p class="field-box" v-else>
                  <el-tag
                    v-for="(d, index) in form.good_field_value"
                    :key="index"
                    :closable="false">
                    {{ d }}
                  </el-tag>
                </p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.good_field" title="保存" href="javascript:void(0)"
                   @click="saveBtn('good_field', ['good_field'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('good_field')">编辑</a>
              </el-col>
            </el-row>

            <el-row :gutter="gutter" :class="['item', isMob ? 'item-m' : '']">
              <el-col :span="titleSpan" class="title">
                <p>分公司</p>
              </el-col>
              <el-col :span="contentSpan" class="content">

                <div v-if="element.branch">
                  <el-col :xs="4" :sm="2" :md="2" :lg="2">
                    <el-switch
                      style="height: 40px; line-height: 50px;"
                      @change="isBranch"
                      v-model="is_branch"
                      on-text=""
                      off-text="">
                    </el-switch>
                  </el-col>
                  <el-col :xs="6" :sm="3" :md="3" :lg="3" v-show="is_branch">
                    <el-input v-model.number="form.branch_office" :disabled="!is_branch" placeholder="">
                      <template slot="append">家</template>
                    </el-input>
                  </el-col>
                </div>

                <p v-else>{{ form.branch }}</p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.branch" title="保存" href="javascript:void(0)"
                   @click="saveBtn('branch', ['branch_office'])">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('branch')">编辑</a>
              </el-col>
            </el-row>

            <!-- <el-row :gutter="gutter" :class="['item', isMob ? 'item-m' : '']">
              <el-col :span="titleSpan" class="title">
                <p>高新企业</p>
              </el-col>
              <el-col :span="contentSpan" class="content">
                <div v-if="element.high_tech_enterprises">
                  <el-col :span="16">
                    <el-date-picker
                      v-model="form.high_tech_enterprises[0].time"
                      type="date"
                      placeholder="认定时间">
                    </el-date-picker>
                    <el-select v-model.number="form.high_tech_enterprises[0].type" placeholder="认定级别" v-if="element.high_tech_enterprises">
                      <el-option
                        v-for="(item, index) in companyGradeOptions"
                        :label="item.label"
                        :key="index"
                        :value="item.value">
                      </el-option>
                    </el-select>
                  </el-col>
                </div>
                <p v-if="!element.high_tech_enterprises && form.high_tech_enterprises.length" v-for="(ele, index) in form.high_tech_enterprises" :key="ele.time + index">{{ ele.time}}{{ ele.val }}</p>
              </el-col>
              <el-col :span="editSpan" class="edit">
                <a v-if="element.high_tech_enterprises" title="保存" href="javascript:void(0)"
                   @click="saveBtn('high_tech_enterprises', ['high_tech_enterprises'], true)">保存</a>
                <a v-else href="javascript:void(0)" title="编辑" @click="editBtn('high_tech_enterprises')">编辑</a>
              </el-col>
            </el-row> -->
          </div>
        </div>
      </el-col>
    </el-row>
  </div>

</template>

<script>
  import vMenu from '@/components/pages/v_center/Menu'
  import vMenuSub from '@/components/pages/v_center/company/MenuSub'
  // 城市联动
  import RegionPicker from '@/components/block/RegionPicker'
  import api from '@/api/api'
  import '@/assets/js/format'
  import typeData from '@/config'
  import auth from '@/helper/auth'

  export default {
    name: 'vcenter_company_base',
    components: {
      vMenu,
      vMenuSub,
      RegionPicker
    },
    data () {
      return {
        gutter: 0,
        titleSpan: this.$store.state.event.isMob === true ? 12 : 3,
        contentSpan: this.$store.state.event.isMob === true ? 24 : 19,
        editSpan: 2,
        isLoaded: false,
        isLoading: false,
        avatarStr: '点击图像上传Logo，只能上传jpg/gif/png文件，且不超过2M',
        isFirst: false,
        is_branch: false,
        companyId: '',
        province: '',
        city: '',
        district: '',
        items: {},
        form: {
          company_abbreviation: '',
          company_english: '',
          company_type: '',
          good_field: [],
          branch: '',
          registration_number: '',
          web: '',
          company_size: '',
          company_size_val: '',
          revenue: '',
          revenue_value: '',
          branch_office: '',
          high_tech_enterprises: [{
            time: '',
            type: -1
          }],
          contact_name: '',
          email: '',
          phone: '',
          position: ''
        },
        element: {
          company_abbreviation: false,
          company_english: false,
          contact: false,
          good_field: false,
          address: false,
          company_size: false,
          revenue: false,
          advantage: false,
          profile: false,
          awards: false,
          web: false,
          weixin_id: false,
          branch: false,
          high_tech_enterprises: false,
          test: false
        },
        uploadParam: {
          'url': '',
          'token': '',
          'x:random': '',
          'x:user_id': this.$store.state.event.user.id,
          'x:target_id': '',
          'x:type': 0
        },
        imageUrl: '',
        userId: this.$store.state.event.user.id
      }
    },
    computed: {
      // 擅长领域下拉选项
      fieldOptions() {
        let items = []
        for (let i = 0; i < typeData.FIELD.length; i++) {
          let item = {
            value: typeData.FIELD[i]['id'],
            label: typeData.FIELD[i]['name']
          }
          items.push(item)
        }
        return items
      },
      // 公司规模
      sizeOptions() {
        let items = []
        for (let i = 0; i < typeData.COMPANY_SIZE.length; i++) {
          let item = {
            value: typeData.COMPANY_SIZE[i]['id'],
            label: typeData.COMPANY_SIZE[i]['name']
          }
          items.push(item)
        }
        return items
      },
      // 公司营收
      revenueOptions() {
        let items = []
        for (let i = 0; i < typeData.COMPANY_REVENUE.length; i++) {
          let item = {
            value: typeData.COMPANY_REVENUE[i]['id'],
            label: typeData.COMPANY_REVENUE[i]['name']
          }
          items.push(item)
        }
        return items
      },
      // 公司等级
      companyGradeOptions() {
        let items = []
        for (let i = 0; i < typeData.GRADE.length; i++) {
          let item = {
            value: typeData.GRADE[i]['id'],
            label: typeData.GRADE[i]['name']
          }
          items.push(item)
        }
        return items
      },
      isMob() {
        return this.$store.state.event.isMob
      }
    },
    methods: {
      // 删除领域标签
      delFieldBtn(index) {
        this.form.good_field_value.splice(index, 1)
        this.form.good_field.splice(index, 1)
      },
      // 选择领域
      selectFieldBtn(cId, cName) {
        if (this.$phenix.in_array(this.form.good_field, cId) === -1) {
          this.form.good_field_value.push(cName)
          this.form.good_field.push(cId)
        }
      },
      editBtn(mark) {
        if (!mark) {
          return false
        }
        this.element[mark] = true
        // if (mark === 'high_tech_enterprises') {
        //   this.form.high_tech_enterprises.push({time: '', type: ''})
        // }
      },
      isBranch(val) {
        if (val === true) {
          this.is_branch = true
          this.form.branch_office = 1
        } else {
          this.is_branch = false
          this.form.branch_office = 0
        }
      },
      saveBtn(mark, nameArr, multi = false) {
        let that = this
        let row = {}
        if (multi) {
          row = this.form[nameArr[0]]
        } else {
          for (let i = 0; i < nameArr.length; i++) {
            let name = nameArr[i]
            row[name] = this.form[name]
            if (!row[name]) {
              if (name === 'area') {
                row['area'] = 0
              } else if (mark === 'branch') {
                continue
              } else {
                this.$message.error('请完善您的公司信息！')
                return false
              }
            }
          }
        }
        // 处理网址前缀
        if (mark === 'web' && row['web']) {
          let urlRegex = /http:\/\/|https:\/\//
          if (!urlRegex.test(that.form.web)) {
            row.web = 'http://' + row['web']
          }
        }
        // 验证简介长度
        if (mark === 'profile' && row['company_profile']) {
          if (row['company_profile'].length > 500) {
            this.$message.error('不能超过500个字符！')
            return false
          }
        }
        // 验证优势长度
        if (mark === 'advantage' && row['professional_advantage']) {
          if (row['professional_advantage'].length > 500) {
            this.$message.error('不能超过500个字符！')
            return false
          }
        }
        // 验证奖项荣誉长度
        if (mark === 'awards' && row['awards']) {
          if (row['awards'].length > 500) {
            this.$message.error('不能超过500个字符！')
            return false
          }
        }

        // 高新企业
        if (mark === 'high_tech_enterprises') {
          for (let i = 0; i < row.length; i++) {
            if (!row[i].time) {
              this.$message.error('请完善您的公司信息！111')
              return
            } else {
              row[i].time = row[i].time.format('yyyy-MM-dd')
            }
            if (!row[i].type) {
              this.$message.error('请完善您的公司信息！222')
              return
            }
          }
          row = {'high_tech_enterprises': JSON.stringify(row)}
        }
        that.$http({method: 'PUT', url: api.designCompany, data: row})
          .then(function (response) {
            if (response.data.meta.status_code === 200) {
              that.element[mark] = false
              let item = response.data.data
              if (mark === 'address') {
                that.form.province_value = item.province_value
                that.form.city_value = item.city_value
                that.form.area_value = item.area_value
              } else if (mark === 'company_size') {
                that.form.company_size_val = item.company_size_val
              } else if (mark === 'revenue') {
                that.form.revenue_value = item.revenue_value
              } else if (mark === 'web') {
                that.form.web_p = row.web
                let urlRegex = /http:\/\/|https:\/\//
                if (urlRegex.test(row.web)) {
                  that.form.web = row.web.replace(urlRegex, '')
                }
              } else if (mark === 'branch') {
                that.form.branch = row.web
                if (that.form.branch_office > 0) {
                  that.form.branch = that.form.branch_office + '家'
                } else {
                  that.form.branch = '无'
                }
              } else if (mark === 'high_tech_enterprises') {
                for (let i = 0; i < that.form.high_tech_enterprises.length; i++) {
                  that.form.high_tech_enterprises[i].time = that.form.high_tech_enterprises[i].time.format('yyyy-MM-dd')
                  switch (that.form.high_tech_enterprises[i].type) {
                    case 1:
                      that.form.high_tech_enterprises[i].val = '市级高新技术企业'
                      break
                    case 2:
                      that.form.high_tech_enterprises[i].val = '省级高新技术企业'
                      break
                    case 3:
                      that.form.high_tech_enterprises[i].val = '国家级高新技术企业'
                      break
                  }
                }
              }
            } else {
              that.$message.error(response.data.meta.message)
            }
          })
          .catch(function (error) {
            that.$message.error(error.message)
          })
      },
      change: function (obj) {
        this.province = this.form.province = obj.province
        this.city = this.form.city = obj.city
        this.district = this.form.area = obj.district
      },
      avatarProgress() {
        this.avatarStr = '上传中...'
      },
      handleAvatarSuccess(res, file) {
        this.imageUrl = URL.createObjectURL(file.raw)
        this.avatarStr = '点击图像上传Logo，只能上传jpg/gif/png文件，且不超过2M'
        // 查询用户表，更新头像到本地
        let that = this
        that.$http.get(api.user, {})
          .then(function (response) {
            if (response.data.meta.status_code === 200) {
              if (response.data.data) {
                auth.write_user(response.data.data)
              }
            }
          })
          .catch(function (error) {
            that.$message.error(error.message)
          })
      },
      beforeAvatarUpload(file) {
        const arr = ['image/jpeg', 'image/gif', 'image/png', 'image/png']
        const isLt2M = file.size / 1024 / 1024 < 2
        this.uploadParam['x:type'] = 6

        if (arr.indexOf(file.type) === -1) {
          this.$message.error('上传头像格式不正确!')
          return false
        }
        if (!isLt2M) {
          this.$message.error('上传头像图片大小不能超过 2MB!')
          return false
        }
      },
      // 去认证
      goVerify() {
        this.$router.push({name: 'vcenterComputerIdentification'})
      }
    },
    watch: {},
    created: function () {
      let uType = this.$store.state.event.user.type
      // 如果是需求方，跳转到相应页面
      if (uType !== 2) {
        this.$router.replace({name: 'vcenterDComputerBase'})
        return
      }
      const that = this
      that.isLoading = true
      that.$http.get(api.designCompany, {})
        .then(function (response) {
          that.isLoading = false
          that.isFirst = true
          if (response.data.meta.status_code === 200) {
            if (response.data.data) {
              // 重新渲染
              that.$nextTick(function () {
                that.form = response.data.data
                that.form.company_size = that.form.company_size === 0 ? '' : that.form.company_size
                that.companyId = response.data.data.id
                that.uploadParam['x:target_id'] = response.data.data.id
                that.province = response.data.data.province === 0 ? '' : response.data.data.province
                that.city = response.data.data.city === 0 ? '' : response.data.data.city
                that.district = response.data.data.area === 0 ? '' : response.data.data.area
                that.form.web_p = that.form.web
                that.form.verify_status_label = typeData.VERIFY_STATUS[that.form.verify_status]
                if (response.data.data.branch_office !== 0) {
                  that.is_branch = true
                }
                // 处理网址前缀
                if (that.form.web) {
                  let urlRegex = /http:\/\/|https:\/\//
                  if (urlRegex.test(that.form.web)) {
                    that.form.web = that.form.web.replace(urlRegex, '')
                  }
                }
                that.form.branch = '无'
                if (that.form.branch_office > 0) {
                  that.form.branch = that.form.branch_office + '家'
                }
                if (response.data.data.logo_image) {
                  that.imageUrl = response.data.data.logo_image.logo
                }
                if (that.form.high_tech_enterprises.length) {
                  for (let i of that.form.high_tech_enterprises) {
                    switch (i.type) {
                      case 1:
                        i.val = '市级高新技术企业'
                        break
                      case 2:
                        i.val = '省级高新技术企业'
                        break
                      case 3:
                        i.val = '国家级高新技术企业'
                        break
                    }
                  }
                }
              })
            }
          }
        })
        .catch(function (error) {
          that.isLoading = false
          that.$message.error(error.message)
        })

      // 加载图片token
      that.$http.get(api.upToken, {})
        .then(function (response) {
          if (response.data.meta.status_code === 200) {
            if (response.data.data) {
              that.uploadParam['token'] = response.data.data.upToken
              that.uploadParam['x:random'] = response.data.data.random
              that.uploadParam.url = response.data.data.upload_url
            }
          }
        })
        .catch(function (error) {
          that.$message.error(error.message)
        })
    }
  }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .right-content .content-box-m {
    margin: 0;
    padding: 0 15px;
  }

  .right-content .content-box {
    padding: 0 20px;
  }

  .item {
    margin: 5px 0;
    padding: 10px 0;
    border-bottom: 1px solid #d2d2d2;
  }
  .content-box .item:last-child {
    border-bottom: none;
  }

  .item-m {
    padding: 0 0 10px 0;
    margin: 0;
    position: relative;
  }

  /* .item .el-col {
    padding: 10px 0 10px 0;
  }

  .item .el-col .el-col {
    padding: 0
  } */

  .item-m .el-col {
    padding: 0;
  }

  .item .edit {
    padding-left: 10px;
  }

  .item-m .edit {
    position: absolute;
    width: 36px;
    right: 0;
    top: 8px;
    line-height: 21px;
  }

  .item p {
    line-height: 1.6;
  }

  .title {
    margin: 0;
    padding: 0;
  }
  .title p {
    line-height: 36px;
    color: #666;
    font-size: 1.5rem;
  }

  .item-m .title p {
    margin: 8px 0;
    color: #222;
    line-height: 21px;
    font-weight: 400;
  }

  .item .content {
    padding: 6px 0
  }

  .item-m .content {
    color: #666;
    border: 1px solid #E6E6E6;
    padding: 4px 8px;
    min-height: 30px;
  }

  .item-mAvatar {
    padding: 10px 0 20px;
  }

  .item-mAvatar .avatarhead p {
    margin: 0 0 6px 0;
  }

  .item-mAvatar .avatarhead span {
    font-size: 10px;
    line-height: 1.1;
    color: #999;
  }

  .item-m .avatarcontent {
    border: none;
    display: flex;
    justify-content: flex-end;
  }

  .edit a {
    font-size: 1.3rem;
    color: #0995F8;
  }

  .item-m .edit a {
    color: #FF5A5F;
  }

  .avatar-uploader .el-upload {
    border: 1px dashed #d2d2d2;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }

  .avatar-uploader .el-upload:hover {
    border-color: #20a0ff;
  }

  .avatar-uploader-icon {
    display: block;
    border-radius: 50%;
    color: #999;
    background: url('../../../../assets/images/avatar_default.png') no-repeat;
    background-size: contain;
    width: 100px;
    height: 100px;
    line-height: 100px;
    text-align: center;
  }

  .item-m .avatar-uploader-icon {
    width: 40px;
    height: 40px;
    line-height: 40px;
  }

  .avatar {
    width: 100px;
    height: 100px;
    display: block;
  }

  .item-m .avatar {
    width: 40px;
    height: 40px;
  }

  .type-content .el-checkbox-button {
    margin: 3px 0;
  }

  .field-box .el-tag {
    margin: 5px;
  }

  .edit-field-tag {
    margin-top: 20px;
  }

  .type-content p {
    color: #222;
    font-size: 1.8rem;
    margin: 20px 0 10px 0;
  }

  .tag {
    margin: 5px 0;
  }

  .tag:hover {
    border: 1px solid #FF5A5F;
    color: #FF5A5F;
  }

  .tag.active {
    border: 1px solid #FF5A5F;
    color: #FF5A5F;
  }

  .MmenuHide {
    margin-left: 0;
  }

  .el-upload__tip {
    color: #999;
  }
  @media screen and (max-width: 767px) {
    .item-m .content {
      border: none;
      padding: 0;
    }
  }
</style>
