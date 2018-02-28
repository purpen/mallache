<template>
  <div class="container">

    <v-progress :typeStep="true" :itemId="form.id" :step="form.stage_status"></v-progress>
    <el-row :gutter="24">
      <el-col :span="24" v-if="isMob">
        <div id="right_box">
          <div class="slider info">
            <p class="slide-des slide-info" style="">关于设计类型</p>
            <p class="slide-des">根据您的项目需求，选择相应的设计服务类别，这里只能单选。</p>
          </div>
        </div>
      </el-col>
      <el-col :span="isMob ? 24 : 19">
        <div class="content">

          <div class="item">
            <div class="item_banner" @click="openTypeBtn(1)">
              <p class="fl"><img src="../../../assets/images/icon/product.png"/></p>
              <div class="fl banner-title">
                <p class="title">产品设计</p>
                <p class="des">产品策略／产品外观设计／结构设计</p>
              </div>
              <p class="fr">
                <i class="fa fa-angle-up fa-2x" aria-hidden="true" v-if="form.type === 1"></i>
                <i class="fa fa-angle-down fa-2x" aria-hidden="true" v-else></i>
              </p>
            </div>
            <transition name="slide-fade">
              <div class="type-content" v-show="form.type === 1">

                <p>设计类别</p>
                <div class="category-box" v-if="typeDesignOptions.length">
                  <el-button @click="designTypeBtn(d.id)" v-for="(d, index) in typeDesignOptions"  :key="index" :class="{ 'tag': true, active: d.active}"  size="small">{{ d.name }}
                  </el-button>
                </div>

                <p class="classify">产品类别</p>
                <div class="category-box">
                  <el-button @click="fieldBtn(d.id)" v-for="(d, index) in fieldOptions" :key="index"  :class="{ 'tag': true, active: d.id === form.field ? true : false }" size="small">{{ d.name }}
                  </el-button>
                </div>

                <!--
                  <p>所属行业</p>
                  <div class="category-box">
                    <el-button :class="{ 'tag': true, active: d.id === form.industry ? true : false }" :key="index" @click="industryBtn(d.id)" v-for="(d, index) in industryOptions">{{ d.name }}</el-button>
                  </div>
                  -->

              </div>
            </transition>
          </div>

          <div class="item" style="clear:both;">
            <div class="item_banner" @click="openTypeBtn(2)">
              <p class="fl"><img src="../../../assets/images/icon/ui.png"/></p>
              <div class="fl banner-title">
                <p class="title">UI／UX设计</p>
                <p class="des">app 设计／网页设计</p>
              </div>
              <p class="fr">
                <i class="fa fa-angle-up fa-2x" aria-hidden="true" v-if="form.type === 2"></i>
                <i class="fa fa-angle-down fa-2x" aria-hidden="true" v-else></i>
              </p>
            </div>
            <transition name="slide-fade">
              <div class="type-content" v-show="form.type === 2">

                <p>设计类别</p>
                <div class="category-box" v-if="typeDesignOptions.length">
                  <el-button :class="{ 'tag': true, active: d.id === form.design_types ? true : false }" :key="index" @click="designTypeBtn(d.id)" v-for="(d, index) in typeDesignOptions" size="small">{{ d.name }}
                  </el-button>
                </div>
              </div>

            </transition>
          </div>

          <div class="clear"></div>
          <div class="submit-btn">
            <el-button type="primary" class="is-custom" size="large" :loading="isLoadingBtn" @click="submit">保存并继续
            </el-button>
          </div>

        </div>
      </el-col>
      <el-col :span="5" v-if="!isMob">
        <div id="right_box">
          <div class="slider info">
            <p>提示</p>
            <p class="slide-des slide-info" style="">关于设计类型：</p>
            <p class="slide-des">根据您的项目需求，选择相应的设计服务类别，这里只能单选。</p>
          </div>
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
    data() {
      return {
        itemId: '',
        isLoadingBtn: false,
        transitionName: 'expand',
        matchCount: '',
        form: {
          type: '',
          design_types: [],
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
          if (!that.form.design_types.length || !that.form.field) {
            that.$message.error('添写信息不完整!')
            return false
          }
          row = {
            type: that.form.type,
            design_types: JSON.stringify(that.form.design_types),
            field: that.form.field
          }
        } else if (that.form.type === 2) {
          if (!that.form.design_types.lgnth) {
            that.$message.error('添写信息不完整!')
            return false
          }
          row = {
            type: that.form.type,
            design_types: JSON.stringify(that.form.design_types)
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

        method = 'put'
        apiUrl = api.demandId.format(that.itemId)

        that.isLoadingBtn = true
        that.$http({method: method, url: apiUrl, data: row})
          .then(function (response) {
            that.isLoadingBtn = false
            if (response.data.meta.status_code === 200) {
              that.$message.success('提交成功！')
              sessionStorage.setItem('position', 172)
              if (response.data.data.item.type === 1) {
                that.$router.push({
                  name: 'itemSubmitThree',
                  params: {
                    id: response.data.data.item.id
                  }
                })
              } else if (response.data.data.item.type === 2) {
                that.$router.push({
                  name: 'itemSubmitUIThree',
                  params: {
                    id: response.data.data.item.id
                  }
                })
              }
              return false
            } else {
              that.isLoadingBtn = false
              that.$message.error(response.data.meta.message)
            }
          })
          .catch(function (error) {
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
        this.form.design_types = []
        this.form.field = ''
        if (typeId === this.form.type) {
          this.form.type = ''
        } else {
          this.form.type = typeId
          // 清空已选子类
          for (let i of this.typeDesignOptions) {
            i.active = false
          }
        }
      },
      designTypeBtn(typeId) {
        if (this.form.design_types.indexOf(typeId) === -1) {
          this.form.design_types.push(typeId)
        } else {
          this.form.design_types.splice(this.form.design_types.indexOf(typeId), 1)
        }
        this.typeDesignOptions[typeId - 1].active = !this.typeDesignOptions[typeId - 1].active
        console.log(this.form.design_types, this.typeDesignOptions)
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
      },
      isMob() {
        return this.$store.state.event.isMob
      }
    },
    mounted: function () {},
    created: function () {
      const that = this
      var id = this.$route.params.id
      if (id) {
        that.itemId = id
        that.$http.get(api.demandId.format(id), {})
          .then(function (response) {
            if (response.data.meta.status_code === 200) {
              var row = response.data.data.item
              that.form.id = row.id
              that.form.type = row.type
              if (row.design_types) {
                that.form.design_types = row.design_types
              } else {
                that.form.design_types = []
              }
              for (let i of row.design_types) {
                that.typeDesignOptions[i - 1].active = true
              }
              that.form.field = row.field
              that.form.stage_status = row.stage_status
            } else {
              that.$message.error(response.data.meta.message)
              console.log(response.data.meta.message)
              that.$router.push({
                name: 'home'
              })
              return false
            }
          })
          .catch(function (error) {
            that.$message.error(error.message)
            that.$router.push({
              name: 'home'
            })
          })
      } else {
        that.$message.error('缺少请求参数！')
        that.$router.push({
          name: 'home'
        })
      }
    },
    watch: {}
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

  .item_banner {
    height: 60px;
    background-color: #FAFAFA;
    padding: 12px;
    border-bottom: 1px solid #D2D2D2;
    cursor: pointer;
  }

  .banner-title {
    margin: 0 0 0 20px;
    padding-top: 0;
    line-height: 20px;
  }

  .item_banner p img {
    width: 70%;
    vertical-align: middle;
  }

  .banner-title p.title {
    font-size: 1.8rem;
    font-weight: 500;
    color: #333;
  }

  .banner-title .des {
    font-size: 1rem;
    color: #666;
  }

  .type-content {
    padding: 20px 20px 50px 20px;
  }

  .type-content p {
    color: #222;
    font-size: 1.8rem;
    margin: 20px 0 10px 0;
  }

  .tag {
    margin: 5px 3px 5px 3px;
    color: #222;
  }

  .tag:hover,.tag:focus {
    border: 1px solid #999;
    color: #222;
  }

   .tag:active {
    border: 1px solid #666;
    color: #222;
   }

  .tag.active {
    border: 1px solid #FF5A5F;
    color: #FF5A5F;
  }

  .slider {
    border: 1px solid #ccc;
    /*height: 250px;*/
    text-align: center;
  }

  .slider.info {
    /*height: 300px;*/
    text-align: left;
  }

  .slider p {
    margin: 25px;
  }

  .slider.info p {
    margin: 10px 20px;
  }

  .submit-btn {
    height: 40px;
    border-top: 1px solid #ccc;
    margin: 30px 20px 20px 20px;
    padding: 10px 0 10px 0;
    text-align: right;
  }

  .slide-img {
    padding-top: 20px;
  }

  .slide-img img {
  }

  .slide-str {
    font-size: 2rem;
  }

  .slide-str img {
    vertical-align: middle;
  }

  .slider.info p.slide-des {
    color: #666;
    line-height: 1.5;
    font-size: 1rem;
  }

  .collapse-banner {
    line-height: 30px;
  }

  .slide-fade-enter,
  .slide-fade-leave-active {
    opacity: 0;
    transform: translateX(-300px);
  }

  @media screen and (max-width: 767px) {
    .slider.info {
      padding-top: 10px;
      text-align: center;
    }

    .slider.info p {
      margin: 0 15px;
    }

    .content, .slider {
      border: none;
    }

    .content {
      padding: 0 15px;
    }

    .item_banner {
      margin-top: 10px;
      border: 1px solid #D2D2D2;
    }

    .banner-title {
      margin-left: 0;
    }

    .submit-btn {
      text-align: center;
      border-top: none;
      margin: 0;
    }

    .submit-btn button {
      width: 100%;
      height: 40px;
      padding: 0;
      line-height: 40px;
    }

    .type-content {
      padding: 15px 10px;
      border: 1px solid #D2D2D2;
      border-top: none;
    }

    .classify {
      padding-top: 10px;
    }

    .type-content p {
      margin-top: 0;
    }

  }
</style>
