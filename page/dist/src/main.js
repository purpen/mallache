'use strict';

var _vue = require('vue');

var _vue2 = _interopRequireDefault(_vue);

var _App = require('./App');

var _App2 = _interopRequireDefault(_App);

var _router = require('./router');

var _router2 = _interopRequireDefault(_router);

var _index = require('./store/index');

var _index2 = _interopRequireDefault(_index);

var _http = require('./http');

var _http2 = _interopRequireDefault(_http);

var _elementUi = require('element-ui');

var _elementUi2 = _interopRequireDefault(_elementUi);

var _base = require('@/assets/js/base');

var _base2 = _interopRequireDefault(_base);

var _vueLazyload = require('vue-lazyload');

var _vueLazyload2 = _interopRequireDefault(_vueLazyload);

var _vueAwesomeSwiper = require('vue-awesome-swiper');

var _vueAwesomeSwiper2 = _interopRequireDefault(_vueAwesomeSwiper);

require('babel-polyfill');

require('element-ui/lib/theme-default/index.css');

require('swiper/dist/css/swiper.css');

require('./assets/css/reset.css');

require('./assets/css/font-awesome.min.css');

require('./assets/css/base.css');

require('./assets/css/admin.css');

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

_vue2.default.use(_elementUi2.default);

_vue2.default.use(_vueLazyload2.default, {
  loading: require('assets/images/Bitmap.png')
});

_vue2.default.use(_vueAwesomeSwiper2.default);

_vue2.default.config.productionTip = false;

_vue2.default.prototype.$http = _http2.default;

_vue2.default.prototype.$phenix = _base2.default;

new _vue2.default({
  el: '#app',
  router: _router2.default,
  store: _index2.default,
  template: '<App/>',
  components: { App: _App2.default }
});
//# sourceMappingURL=main.js.map