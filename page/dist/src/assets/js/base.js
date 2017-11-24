'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.IsMobile = IsMobile;

var phenix = {};

phenix.record_asset_id = function (class_id, id) {
  var ids = $('#' + class_id).val();
  if (ids.length == 0) {
    ids = id;
  } else {
    if (ids.indexOf(id) == -1) {
      ids += ',' + id;
    }
  }
  $('#' + class_id).val(ids);
};

phenix.remove_asset_id = function (class_id, id) {
  var ids = $('#' + class_id).val();
  var ids_arr = ids.split(',');
  var is_index_key = phenix.in_array(ids_arr, id);
  ids_arr.splice(is_index_key, 1);
  ids = ids_arr.join(',');
  $('#' + class_id).val(ids);
};

phenix.in_array = function (arr, val) {
  var i;
  for (i = 0; i < arr.length; i++) {
    if (val === arr[i]) {
      return i;
    }
  }
  return -1;
};function IsMobile() {
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
}

exports.default = phenix;
//# sourceMappingURL=base.js.map