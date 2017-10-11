export function calcImgSize(src) {
  var img = new Image()
  img.src = src
  var oh = img.naturalHeight
  var ow = img.naturalWidth
  var nw = document.documentElement.clientWidth
  console.log(oh, ow)
  if (ow !== 0 || oh !== 0) {
    return (nw / ow * oh) + 'px'
  }
}
