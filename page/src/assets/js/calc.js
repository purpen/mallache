export function calcImgSize(src) {
  // var img = new Image()
  // img.src = src
  // var oh = img.naturalHeight
  // var ow = img.naturalWidth
  var oh = 1300
  var ow = 2880
  var nw = document.documentElement.clientWidth
  if (ow !== 0 || oh !== 0) {
    return (nw / ow * oh) + 'px'
  }
}
