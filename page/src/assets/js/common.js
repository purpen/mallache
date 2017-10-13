export function calcImgSize() {
  // var img = new Image()
  // img.src = src
  // var oh = img.naturalHeight
  // var ow = img.naturalWidth
  var oh = 1300
  var ow = 2880
  var nw = document.documentElement.clientWidth
  if (window.document.getElementsByClassName('banner').length) {
    window.document.getElementsByClassName('banner')[0].style.height = (nw / ow * oh) + 'px'
  }
  if (ow !== 0 || oh !== 0) {
    return (nw / ow * oh) + 'px'
  }
}
