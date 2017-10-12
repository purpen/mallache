/* eslint-disable */
/*
 * phenix base js 
 */
var phenix = {}

/**
 * 允许多附件上传
 */
phenix.record_asset_id = function(class_id, id){
    var ids = $('#'+class_id).val();
    if (ids.length == 0){
        ids = id;
    }else{
        if (ids.indexOf(id) == -1){
            ids += ','+id;
        }
    }
    $('#'+class_id).val(ids);
};

//移除附件id
phenix.remove_asset_id = function(class_id, id){
    var ids = $('#'+class_id).val();
    var ids_arr = ids.split(',');
    var is_index_key = phenix.in_array(ids_arr,id);
    ids_arr.splice(is_index_key,1);
    ids = ids_arr.join(',');
    $('#'+class_id).val(ids);
};

//查看字符串是否在数组中存在
phenix.in_array = function(arr, val) {
    var i;
    for (i = 0; i < arr.length; i++) {
        if (val === arr[i]) {
            return i;
        }
    }
    return -1;
}; // 返回-1表示没找到，返回其他值表示找到的索引

// 社会化分享
phenix.share_list = function(pic_url, title, link, site) {
	// 链接，标题，网站名称，子窗口别称，网站链接
  if (!link) {
    link = document.location;
  }
  link = encodeURIComponent(link);

  if (!title) {
	  title = document.title.substring(0,100);
  }
  title = encodeURIComponent(title);
  if (!site) {
	  site = '太火鸟-铟果SaaS';
  }
	var source = encodeURIComponent('太火鸟-铟果SaaS'), windowName = 'tShare';
	
	var getParamsOfShareWindow = function(width, height) {
		return ['toolbar=0,status=0,resizable=1,width=' + width + ',height=' + height + ',left=',(screen.width-width)/2,',top=',(screen.height-height)/2].join('');
	}
	
  document.getElementById('wechat-share').addEventListener('click',function(){
		return false;
  },false);
	
  document.getElementById('sina-share').addEventListener('click',function(){
		var url = 'http://v.t.sina.com.cn/share/share.php?url=' + link + '&title=' + title + '&pic=' + pic_url;
		var params = getParamsOfShareWindow(607, 523);
		window.open(url, windowName, params);
		return false;
  },false);

  document.getElementById('qzone-share').addEventListener('click',function(){
		var url = 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=' + link + '&title=' + title + '&pics=' + pic_url + '&site=' + site;
		var params = getParamsOfShareWindow(600, 560);
		window.open(url, windowName, params);
		return false;
  },false);

  /**
  document.getElementById('tencent-share').addEventListener('click',function(){
		var url = 'http://v.t.qq.com/share/share.php?title=' + title + '&url=' + link + '&site=' + site + '&pic=' + pic_url;
		var params = getParamsOfShareWindow(634, 668);
		window.open(url, windowName, params);
		return false;
  },false);

  document.getElementById('douban-share').addEventListener('click',function(){
		var url = 'http://www.douban.com/recommend/?url=' + link + '&title=' + title + '&pic=' + pic_url;
		var params = getParamsOfShareWindow(450, 350);
		window.open(url, windowName, params);
		return false;
  },false);

  document.getElementById('renren-share').addEventListener('click',function(){
		var url = 'http://share.renren.com/share/buttonshare?link=' + link + '&title=' + title + '&pic=' + pic_url;
		var params = getParamsOfShareWindow(626, 436);
		window.open(url, windowName, params);
		return false;
  },false);

  document.getElementById('kaixin001-share').addEventListener('click',function(){
		var url = 'http://www.kaixin001.com/repaste/share.php?rurl=' + link + '&rcontent=' + link + '&rtitle=' + title + '&pic=' + pic_url;
		var params = getParamsOfShareWindow(540, 342);
		window.open(url, windowName, params);
		return false;
  },false);
	
  document.getElementById('netease-share').addEventListener('click',function(){
		var url = 'http://t.163.com/article/user/checkLogin.do?link=' + link + 'source=' + source + '&info='+ title + ' ' + link;
		var params = getParamsOfShareWindow(642, 468);
		window.open(url, windowName, params);
		return false;
  },false);
	
  document.getElementById('facebook-share').addEventListener('click',function(){
		var url = 'http://facebook.com/share.php?u=' + link + '&t=' + title;
		var params = getParamsOfShareWindow(626, 436);
		window.open(url, windowName, params);
		return false;
  },false);
 
  document.getElementById('twitter-share').addEventListener('click',function(){
		var url = 'http://twitter.com/share?url=' + link + '&text=' + title;
		var params = getParamsOfShareWindow(500, 375);
		window.open(url, windowName, params);
		return false;
  },false);
 
  document.getElementById('delicious-share').addEventListener('click',function(){
		var url = 'http://delicious.com/post?url=' + link + '&title=' + title;
		var params = getParamsOfShareWindow(550, 550);
		window.open(url, windowName, params);
		return false;
  },false);

  **/
	
};

export default phenix;
