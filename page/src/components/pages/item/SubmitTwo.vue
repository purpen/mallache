<template>
  <div class="container">

    <v-progress></v-progress>
    <el-row :gutter="24">

      <el-col :span="18">
        <div class="content">

          <div class="item">
            <div class="banner" @click="openTypeBtn(1)">
              <p class="fl"><img src="../../../assets/images/icon/product.png" /></p>
              <div class="fl banner-title">
                <p class="title">产品设计</p>
                <p class="des">产品策略/产品设计/结构设计</p>             
              </div>
              <p class="fr">
                <i class="fa fa-angle-up fa-3x" aria-hidden="true" v-if="form.type === 1"></i>
                <i class="fa fa-angle-down fa-3x" aria-hidden="true" v-else></i>
              </p>
            </div>
            <transition name="slide-fade">
            <div class="type-content" v-show="form.type === 1">

              <p>设计类别</p>
              <div class="category-box">
                <el-button :class="{ 'tag': true, active: d.id === form.design_type ? true : false }" :key="index" @click="designTypeBtn(d.id)" v-for="(d, index) in typeDesignOptions">{{ d.name }}</el-button>
              </div>

              <p>产品类别</p>
              <div class="category-box">
                <el-button :class="{ 'tag': true, active: d.id === form.field ? true : false }" :key="index" @click="fieldBtn(d.id)" v-for="(d, index) in fieldOptions">{{ d.name }}</el-button>               
              </div>

              <p>所属行业</p>
              <div class="category-box">
                <el-button :class="{ 'tag': true, active: d.id === form.industry ? true : false }" :key="index" @click="industryBtn(d.id)" v-for="(d, index) in industryOptions">{{ d.name }}</el-button>
              </div>
            
            </div>

          </transition>
          </div>

          <div class="item">
            <div class="banner" @click="openTypeBtn(2)">
              <p class="fl"><img src="../../../assets/images/icon/ui.png" /></p>
              <div class="fl banner-title">
                <p class="title">UI／UX设计</p>
                <p class="des">app 设计／网页设计</p>             
              </div>
              <p class="fr">
                <i class="fa fa-angle-up fa-3x" aria-hidden="true" v-if="form.type === 2"></i>
                <i class="fa fa-angle-down fa-3x" aria-hidden="true" v-else></i>
              </p>
            </div>
            <transition name="slide-fade">
            <div class="type-content" v-show="form.type === 2">

              <p>设计类别</p>
              <div class="category-box">
                <el-button :class="{ 'tag': true, active: d.id === form.design_type ? true : false }" :key="index" @click="designTypeBtn(d.id)" v-for="(d, index) in typeDesignOptions">{{ d.name }}</el-button>
              </div>
            
            </div>

          </transition>
          </div>

          <div class="clear"></div>
          <div class="submit-btn">
            <el-button type="success" class="is-custom" size="large" :loading="isLoadingBtn" @click="submit">保存并继续</el-button>
          </div>

        
        </div>
      </el-col>
      <el-col :span="6">
        <div class="slider">
          <p class="slide-img"><img src="../../../assets/images/icon/zan.png" /></p>
          <p class="slide-str">100家推荐</p>
          <p class="slide-des">根据你当前填写的项目需求，系统为你匹配出符合条件的设计公司</p>
        </div>

        <div class="slider info">
          <p>项目需求填写</p>
          <p class="slide-des">为了充分了解企业需求，达成合作，针对以下问题为了保证反馈的准确性，做出客观真实的简述，请务必由高层管理人员亲自填写。</p>
          <div class="blank20"></div>
          <p>项目预算设置</p>
          <p class="slide-des">产品研发费用通常是由产品设计、结构设计、硬件开发、样机、模具等费用构成，以普通消费电子产品为例设计费用占到产品研发费用10-20%，设置有竞争力的项目预算，能吸引到实力强的设计公司参与到项目中，建议预算设置到产品研发费用的20-30%。</p>
        </div>
      </el-col>
    </el-row>
  </div>
</template>

<script>
  import vProgress from '@/components/pages/item/Progress'
  import typeData from '@/config'
  import api from '@/api/api'
  export default {
    name: 'item_submit_two',
    components: {
      vProgress
    },
    data () {
      return {
        itemId: '',
        isLoadingBtn: false,
        transitionName: 'expand',
        form: {
          type: '',
          design_type: '',
          field: '',
          industry: ''
        },
        msg: ''
      }
    },
    methods: {
      submit() {
        const that = this
        var row = {}
        if (that.form.type === 1) {
          if (!that.form.design_type || !that.form.field || !that.form.industry) {
            that.$message.error('添写信息不完整!')
            return false
          }
          row = {
            type: that.form.type,
            design_type: that.form.design_type,
            field: that.form.field,
            industry: that.form.industry
          }
        } else if (that.form.type === 2) {
          if (!that.form.design_type) {
            that.$message.error('添写信息不完整!')
            return false
          }
          row = {
            type: that.form.type,
            design_type: that.form.design_type
          }
        } else {
          that.$message.error('请选择设计类型!')
          return false
        }

        if (that.form.stage_status < 1) {
          row.stage_status = 1
        }
        var apiUrl = null
        var method = null

        if (that.itemId) {
          method = 'put'
          apiUrl = api.demandId.format(that.itemId)
        } else {
          method = 'post'
          apiUrl = api.demand
        }

        that.isLoadingBtn = true
        that.$http({method: method, url: apiUrl, data: row})
        .then (function(response) {
          that.isLoadingBtn = false
          if (response.data.meta.status_code === 200) {
            that.$message.success('提交成功！')
            if (response.data.data.item.type === 1) {
              that.$router.push({name: 'itemSubmitThree', params: {id: response.data.data.item.id}})
            } else if (response.data.data.item.type === 2) {
              that.$router.push({name: 'itemSubmitUIThree', params: {id: response.data.data.item.id}})
            }
            return false
          } else {
            that.isLoadingBtn = false
            that.$message.error(response.data.meta.message)
          }
        })
        .catch (function(error) {
          that.$message.error(error.message)
          that.isLoadingBtn = false
          console.log(error.message)
        })
      },
      typeChange(d) {
        if (d === 1) {
          this.typeSwitch1 = true
          this.typeSwitch2 = false
        } else if (d === 2) {
          this.typeSwitch2 = true
          this.typeSwitch1 = false
        }
      },
      // 点击分类按钮
      openTypeBtn(typeId) {
        if (typeId === this.form.type) {
          this.form.type = ''
        } else {
          this.form.type = typeId
          // 清空已选子类
          // this.form.design_type = ''
          // this.form.field = ''
          // this.form.industry = ''
        }
      },
      designTypeBtn(typeId) {
        this.form.design_type = typeId
      },
      fieldBtn(typeId) {
        this.form.field = typeId
      },
      industryBtn(typeId) {
        this.form.industry = typeId
      }
    },
    computed: {
      typeOptions() {
        return typeData.COMPANY_TYPE
      },
      typeDesignOptions() {
        var index = 0
        if (this.form.type === 1) {
          index = 0
        } else if (this.form.type === 2) {
          index = 1
        } else {
          return []
        }

        return typeData.COMPANY_TYPE[index].designType
      },
      fieldOptions() {
        var index
        if (this.form.type === 1) {
          index = 0
        } else if (this.form.type === 2) {
          index = 1
        } else {
          return []
        }

        return typeData.COMPANY_TYPE[index].field
      },
      industryOptions() {
        var index
        if (this.form.type === 1) {
          index = 0
        } else if (this.form.type === 2) {
          index = 1
        } else {
          return []
        }

        return typeData.COMPANY_TYPE[index].industry
      }
    },
    created: function() {
      const that = this
      var id = this.$route.params.id
      if (id) {
        that.itemId = id
        that.$http.get(api.demandId.format(id), {})
        .then (function(response) {
          if (response.data.meta.status_code === 200) {
            var row = response.data.data.item
            that.form.type = row.type
            that.form.design_type = row.design_type
            that.form.field = row.field
            that.form.industry = row.industry
            that.form.stage_status = row.stage_status
            console.log(response.data.data)
          } else {
            that.$message.error(response.data.meta.message)
            console.log(response.data.meta.message)
            that.$router.push({name: 'home'})
            return false
          }
        })
        .catch (function(error) {
          that.$message.error(error.message)
          console.log(error.message)
          that.$router.push({name: 'home'})
          return false
        })
      } else {
        that.$message.error('缺少请求参数！')
        that.$router.push({name: 'home'})
      }
    },
    watch: {
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .content {
    border: 1px solid #ccc;
  }

  .item {
    height: 100%;
  }
  .banner {
    height: 50px;
    background-color: #FAFAFA;
    padding: 12px;
    border-bottom: 1px solid #D2D2D2;
    cursor: pointer;
  }
  .banner-title{
    margin: 0 0 0 20px;
    padding-top: 5px;
    line-height: 25px;
  }
  .banner-title p img {
    vertical-align: middle;
  }
  .banner-title p.title{
    font-size: 2.3rem;
    font-weight: 450;
    color: #333;
  }
  .banner-title .des{
    font-size: 1rem;
    color: #666; 
  }

  .type-content{
    padding: 20px 20px 50px 20px;
  
  }
  .type-content p {
    color: #222;
    font-size: 1.8rem;
    margin: 20px 0 10px 0;
  }

  .tag {
    margin: 5px 3px 5px 3px;
  }
  .tag:hover {
    border: 1px solid #FF5A5F;
    color: #FF5A5F; 
  }
  .tag.active {
    border: 1px solid #FF5A5F;
    color: #FF5A5F;
  }



  .slider {
    border: 1px solid #ccc;
    height: 250px;
    text-align:center;
  }
  .slider.info {
    height: 350px;
    text-align: left;
  }
  .slider p {
    margin: 25px;
  }
  .slider.info p {
    margin: 10px 20px;
  }

  .submit-btn {
    height: 30px;
    border-top: 1px solid #ccc;
    margin: 30px 20px 20px 20px;
    padding: 20px 0 10px 0;
    text-align: right;
  }
  .submit-btn button {
  }

  .slide-img {
    padding-top: 20px;
  }
  .slide-img img {
    
  }
  .slide-str {
    font-size: 2rem;
  }
  .slide-des {
    color: #666;
    line-height: 1.5;
    font-size: 1rem;
  }

  .collapse-banner {
    line-height: 30px;
  }

.slide-fade-enter, .slide-fade-leave-active {
  opacity: 0;
  transform: translateX(-300px);
}


</style>
