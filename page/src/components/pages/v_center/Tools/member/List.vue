<template>
  <div class="container blank40 control min-height350">
    <el-row :gutter="20" class="anli-elrow">
      <v-menu currentName="member"></v-menu>
      <el-col :span="isMob ? 24 : 20" v-loading.body="isLoading">

        <el-button @click="genInviteKey">生成邀请链接</el-button>

        <p>urlKey: {{ urlKey }}</p>

        <el-table
          v-if="!isMob"
          key="withdraw"
          :data="tableData"
          style="width: 100%">
          <el-table-column
            min-width="100"
            prop="account"
            label="手机号">
          </el-table-column>
          <el-table-column
            align="center"
            prop="realname"
            label="姓名">
          </el-table-column>
          <el-table-column
            prop="company_role_label"
            label="权限">
          </el-table-column>
          <el-table-column
            min-width="140"
            prop="created_at"
            label="创建时间">
          </el-table-column>
          <el-table-column width="100" label="操作">
            <template slot-scope="scope">
              <p v-if="isCompanySystemAdmin">
                <a href="javascript:void(0);" v-if="scope.row.company_role === 0" @click="setCompanyRole(scope.$index, scope.row)">设为管理员</a>
                <a href="javascript:void(0);" v-if="scope.row.company_role === 10" @click="setCompanyRole(scope.$index, scope.row)">设为成员</a>
              </p>
              <p v-if="isCompanySystemAdmin">
                <a href="javascript:void(0);" @click="setMemberRemove(scope.$index, scope.row)">踢除</a>
              </p>
            </template>
          </el-table-column>
        </el-table>

      </el-col>
    </el-row>
  </div>
</template>

<script>
  import vMenu from '@/components/pages/v_center/Menu'
  import api from '@/api/api'

  export default {
    name: 'vcenter_member_list',
    components: {
      vMenu
    },
    data () {
      return {
        userId: this.$store.state.event.user.id,
        isLoading: false,
        itemList: [],
        tableData: [],
        urlKey: '',
        query: {
          page: 1,
          pageSize: 20,
          totalCount: 0,
          sort: 0
        },
        test: ''
      }
    },
    methods: {
      // 判断是否为公司账号
      isCompany() {
        let uType = this.$store.state.event.user.type
        if (uType === 2) {
          return true
        } else {
          return false
        }
      },
      // 生成邀请链接
      genInviteKey() {
        const that = this
        that.$http.get(api.inviteKey, {})
          .then(function (response) {
            if (response.data.meta.status_code === 200) {
              that.urlKey = response.data.data.rand_string
            }
          })
          .catch(function (error) {
            that.$message.error(error.message)
          })
      },
      // 设置权限
      setCompanyRole(index, user) {
        const that = this
        let role = 0
        if (user.company_role === 0) {
          role = 10
        } else if (user.company_role === 10) {
          role = 0
        }
        that.$http.put(api.designMemberSetRole, {set_user_id: user.id, company_role: role})
          .then(function (response) {
            if (response.data.meta.status_code === 200) {
              that.tableData[index].company_role = role
              if (role === 10) {
                that.tableData[index].company_role_label = '管理员'
              } else if (role === 0) {
                that.tableData[index].company_role_label = '成员'
              }
            } else {
              that.$message.error(response.data.meta.message)
            }
          })
          .catch(function (error) {
            that.$message.error(error.message)
          })
      },
      // 删除成员
      setMemberRemove(index, user) {
        console.log(index, user)
      }
    },
    computed: {
      isMob() {
        return this.$store.state.event.isMob
      },
      // 是否为系统管理员
      isCompanySystemAdmin() {
        let companyRoleId = this.$store.state.event.user.company_role
        if (companyRoleId === 20) {
          return true
        }
        return false
      },
      // 是否为管理员
      isCompanyAdmin() {
        let companyRoleId = this.$store.state.event.user.company_role
        if (companyRoleId === 20 || companyRoleId === 10) {
          return true
        }
        return false
      }
    },
    created: function () {
      const that = this
      that.$http.get(api.designMemberList, {})
        .then(function (response) {
          if (response.data.meta.status_code === 200) {
            that.itemList = response.data.data
            that.query.totalCount = response.data.meta.pagination.total_pages

            for (let i = 0; i < that.itemList.length; i++) {
              let item = that.itemList[i]
              item['created_at'] = item.created_at.date_format().format('yyyy-MM-dd hh:mm')
              item['company_role_label'] = ''
              switch (item.company_role) {
                case 0:
                  item['company_role_label'] = '成员'
                  break
                case 10:
                  item['company_role_label'] = '管理员'
                  break
                case 20:
                  item['company_role_label'] = '超级管理员'
                  break
              }

              that.tableData.push (item)
            } // endfor
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

</style>
