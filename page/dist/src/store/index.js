'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _vue = require('vue');

var _vue2 = _interopRequireDefault(_vue);

var _vuex = require('vuex');

var _vuex2 = _interopRequireDefault(_vuex);

var _mutations = require('./mutations');

var _mutations2 = _interopRequireDefault(_mutations);

var _action = require('./action');

var _action2 = _interopRequireDefault(_action);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

_vue2.default.use(_vuex2.default);

var debug = process.env.NODE_ENV !== 'production';
_vue2.default.config.debug = debug;
_vue2.default.config.warnExpressionErrors = false;

exports.default = new _vuex2.default.Store({
  modules: {
    actions: _action2.default,
    event: _mutations2.default
  },
  strict: debug
});
//# sourceMappingURL=index.js.map