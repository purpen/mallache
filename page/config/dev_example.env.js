var merge = require('webpack-merge')
var prodEnv = require('./prod.env')

module.exports = merge(prodEnv, {
  VUE_ENV: '"server"',
  NODE_ENV: '"development"',
  API_ROOT: '"http://localhost:8081/api"'
})
