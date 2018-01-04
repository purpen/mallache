/* eslint-disable */

/**
 * 定义全局变量
 */
var d3in = {}

/**
 * 计算图片尺寸--未使用
 */
d3in.calcImgSize = function (width = 1520, height = 2880, maxVal = true) {
  let oh = width
  let ow = height
  let nw = document.documentElement.clientWidth
  let nh = nw / ow * oh
  if (ow !== 0 && oh !== 0) {
    if (nh > 800 && maxVal) {
      nh = 800
    }
  } else {
    console.log('height:' + height)
  }
  return nh + 'px'
}

/**
 * 插入内容到光标位置
 */
d3in.insertAtCursor = function (myField, myValue) {
    if (document.selection) {
        //IE support
        myField.focus();
        sel = document.selection.createRange();
        sel.text = myValue;
        sel.select();
    } else if (myField.selectionStart || myField.selectionStart == '0') {
        //MOZILLA/NETSCAPE support
        var startPos = myField.selectionStart;
        var endPos = myField.selectionEnd;
        var beforeValue = myField.value.substring(0, startPos);
        var afterValue = myField.value.substring(endPos, myField.value.length);

        myField.value = beforeValue + myValue + afterValue;

        myField.selectionStart = startPos + myValue.length;
        myField.selectionEnd = startPos + myValue.length;
        myField.focus();
    } else {
        myField.value += myValue;
        myField.focus();
    }
}

d3in.test = function () {
  alert('test')
}

export default d3in
