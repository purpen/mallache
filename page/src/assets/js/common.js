export function calcImgSize(width = 1520, height = 2880, maxVal = true) {
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
