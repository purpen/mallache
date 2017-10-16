export function calcImgSize(width, height) {
  var oh = width || 1520
  var ow = height || 2880
  var nw = document.documentElement.clientWidth
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
