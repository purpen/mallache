export function calcImgSize(width, height) {
  // var img = new Image()
  // img.src = src
  // var oh = img.naturalHeight
  // var ow = img.naturalWidth
  var oh = width || 1520
  var ow = height || 2880
  var nw = document.documentElement.clientWidth
  if (window.document.getElementsByClassName('banner').length) {
    window.document.getElementsByClassName('banner')[0].style.height = (nw / ow * oh) + 'px'
  }
  var nh = nw / ow * oh
  if (ow !== 0 && oh !== 0) {
    if (nh > 800) {
      nh = 800
    }
  } else {
    console.log('width: 0')
    return false
  }
  return nh + 'px'
}
