/**
 ** ###### BASE ##########
 */
module.exports = [
  // 404
  {
    path: '*',
    name: '404',
    meta: {
      title: '找不到此页面'
    },
    component: require('components/pages/notfind/notfind') // webpack 设置新的别名 components
  }
]
