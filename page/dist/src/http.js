'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _promise = require('babel-runtime/core-js/promise');

var _promise2 = _interopRequireDefault(_promise);

var _axios = require('axios');

var _axios2 = _interopRequireDefault(_axios);

var _index = require('./store/index');

var _index2 = _interopRequireDefault(_index);

var _mutationTypes = require('./store/mutation-types');

var types = _interopRequireWildcard(_mutationTypes);

var _router = require('./router');

var _router2 = _interopRequireDefault(_router);

var _qs = require('qs');

var _qs2 = _interopRequireDefault(_qs);

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) newObj[key] = obj[key]; } } newObj.default = obj; return newObj; } }

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var axiosInstance = _axios2.default.create({
  baseURL: process.env.API_ROOT,
  timeout: 50000,
  withCredentials: true,
  headers: {
    'Content-Type': 'application/x-www-form-urlencoded',

    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Credentials': 'true',
    'Access-Control-Allow-Headers': 'Origin, X-Requested-With, Content-Type, Accept',
    'X-Requested-With': 'XMLHttpRequest'
  },
  transformRequest: [function (data) {
    data = _qs2.default.stringify(data);
    return data;
  }]

});

axiosInstance.interceptors.request.use(function (config) {
  if (_index2.default.state.event.token) {
    config.headers.Authorization = 'Bearer ' + _index2.default.state.event.token;
  }
  return config;
}, function (err) {
  return _promise2.default.reject(err);
});

axiosInstance.interceptors.response.use(function (response) {
  return response;
}, function (error) {
  if (error.response) {
    switch (error.response.status) {
      case 401:
        _index2.default.commit(types.USER_SIGNOUT);
        _router2.default.replace({
          path: '/login',
          query: {
            redirect: _router2.default.currentRoute.fullPath
          }
        });
    }
  }

  return _promise2.default.reject(error.response.data);
});

exports.default = axiosInstance;
//# sourceMappingURL=http.js.map