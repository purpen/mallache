/**
 ** ###### 管理工具 ##########
 */
module.exports = [
  {
    path: '/vcenter/cloud_drive/list',
    name: 'vcenterCloudDriveList',
    meta: {
      title: '铟果云盘',
      requireAuth: true
    },
    component: require('@/components/pages/v_center/Tools/cloud_drive/List')
  }
]
