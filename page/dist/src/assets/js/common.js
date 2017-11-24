'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.calcImgSize = calcImgSize;
function calcImgSize() {
  var width = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 1520;
  var height = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 2880;
  var maxVal = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;

  var oh = width;
  var ow = height;
  var nw = document.documentElement.clientWidth;
  var nh = nw / ow * oh;
  if (ow !== 0 && oh !== 0) {
    if (nh > 800 && maxVal) {
      nh = 800;
    }
  } else {
    console.log('height:' + height);
  }
  return nh + 'px';
}
//# sourceMappingURL=common.js.map