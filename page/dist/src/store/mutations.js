'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _defineProperty2 = require('babel-runtime/helpers/defineProperty');

var _defineProperty3 = _interopRequireDefault(_defineProperty2);

var _stringify = require('babel-runtime/core-js/json/stringify');

var _stringify2 = _interopRequireDefault(_stringify);

var _mutations;

var _mutationTypes = require('./mutation-types.js');

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var isLoggedIn = function isLoggedIn() {
  var token = localStorage.getItem('token');
  if (token) {
    return JSON.parse(token);
  } else {
    return false;
  }
};

var userInfo = function userInfo() {
  var user = localStorage.getItem('user');
  if (user) {
    return JSON.parse(user);
  } else {
    return false;
  }
};

var prevUrlName = function prevUrlName() {
  var urlName = localStorage.getItem('prev_url_name');
  if (urlName) {
    return urlName;
  } else {
    return null;
  }
};

var msgCount = function msgCount() {
  var messageCount = localStorage.getItem('msgCount');
  if (messageCount) {
    return messageCount;
  } else {
    return 0;
  }
};

var state = {
  token: isLoggedIn() || null,
  user: userInfo() || {},
  loading: false,
  apiUrl: 'http://sa.taihuoniao.com',
  imgUrl: 'http://sa.taihuoniao.com',
  prevUrlName: prevUrlName(),
  msgCount: msgCount(),
  indexConf: {
    isFooter: true,
    isSearch: true,
    isBack: false,
    isShare: false,
    title: '' },
  pmdHeight: '0px',
  isMob: false
};

var IsMobile = function IsMobile() {
  var sUserAgent = navigator.userAgent;
  var mobileAgents = ['Android', 'iPhone', 'Symbian', 'WindowsPhone', 'iPod', 'BlackBerry', 'Windows CE'];
  var ismob = 0;

  for (var i = 0; i < mobileAgents.length; i++) {
    if (sUserAgent.indexOf(mobileAgents[i]) > -1) {
      ismob = 1;
      break;
    }
  }

  if (ismob) {
    return true;
  } else {
    return false;
  }
};

var mutations = (_mutations = {}, (0, _defineProperty3.default)(_mutations, _mutationTypes.USER_SIGNIN, function (state, token) {
  localStorage.setItem('token', null);
  localStorage.setItem('token', (0, _stringify2.default)(token));
  state.token = token;
}), (0, _defineProperty3.default)(_mutations, _mutationTypes.USER_SIGNOUT, function (state) {
  localStorage.removeItem('token');
  localStorage.removeItem('user');
  localStorage.setItem('msgCount', 0);
  state.token = false;
}), (0, _defineProperty3.default)(_mutations, _mutationTypes.USER_INFO, function (state, user) {
  localStorage.setItem('user', {});
  localStorage.setItem('user', (0, _stringify2.default)(user));
  state.user = user;
}), (0, _defineProperty3.default)(_mutations, _mutationTypes.MSG_COUNT, function (state, msgCount) {
  if (msgCount < 0) {
    msgCount = 0;
  }
  localStorage.setItem('msgCount', (0, _stringify2.default)(msgCount));
  state.msgCount = msgCount;
}), (0, _defineProperty3.default)(_mutations, _mutationTypes.PREV_URL_NAME, function (state, urlName) {
  localStorage.setItem('prev_url_name', urlName);
  state.prevUrlName = urlName;
}), (0, _defineProperty3.default)(_mutations, _mutationTypes.CLEAR_PREV_URL_NAME, function (state) {
  localStorage.removeItem('prev_url_name');
  state.prevUrlName = null;
}), (0, _defineProperty3.default)(_mutations, 'INIT_PAGE', function INIT_PAGE(state) {
  if (IsMobile()) {
    state.isMob = true;
  } else {
    state.isMob = false;
  }
}), _mutations);

exports.default = {
  state: state,
  mutations: mutations
};
//# sourceMappingURL=mutations.js.map