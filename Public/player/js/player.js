var pqs = new ParsedQueryString();
var parameterNames = pqs.params(false);
var parameters = {
  src: activity.stream,
  autoPlay: "true",
  verbose: true,
  controlBarAutoHide: "true",
  controlBarPosition: "bottom",
  poster: activity.poster,
  plugin_hls: "/Public/player/StrobeMediaPlayback/HLSDynamicPlugin.swf"
};

for (var i = 0; i < parameterNames.length; i++) {
  var parameterName = parameterNames[i];
  parameters[parameterName] = pqs.param(parameterName) ||
    parameters[parameterName];
}

var wmodeValue = "opaque";
var wmodeOptions = ["direct", "opaque", "transparent", "window"];
// alert(wmodeValue);
if (parameters.hasOwnProperty("wmode")) {
  if (wmodeOptions.indexOf(parameters.wmode) >= 0) {
    wmodeValue = parameters.wmode;
  }
  delete parameters.wmode;
}

$(function() {
  initVideo();
});


var initShare = function() {
  window._bd_share_config = {
    "common": {
      "bdSnsKey": {},
      "bdText": "",
      "bdMini": "2",
      "bdPic": "",
      "bdStyle": "0",
      "bdSize": "32"
    },
    "share": {}
  };
  with(document)
  0[(getElementsByTagName('head')[0] || body).appendChild(createElement('script')).src = 'http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion=' + ~(-new Date() / 36e5)];
}

var initTimer = function() {
  if ($("[data-toggle=timer]").length == 0) {
    return;
  }
  $("[data-toggle=timer]").each(function(index, el) {
    var timer_rt;
    var that = $(this),
      day = that.find(".day"),
      hour = that.find(".hour"),
      minute = that.find(".minute"),
      second = that.find(".second");
    var EndTime = parseInt(that.data("end"));
    var NowTime = parseInt(that.data("now"));
    var nMS,nD,nH,nM,nS;

    function GetRTime() {
      nMS = EndTime - NowTime;
      // console.log(nMS);
      nD = Math.floor(nMS / (1000 * 60 * 60 * 24));
      nH = Math.floor(nMS / (1000 * 60 * 60)) % 24;
      nM = Math.floor(nMS / (1000 * 60)) % 60;
      nS = Math.floor(nMS / 1000) % 60;
      if (nMS < 0) {
        //console.log('已结束');
        clearInterval(timer_rt);
        timeEnd();
      } else {
        day.text(nD);
        hour.text(nH);
        minute.text(nM<10?"0"+nM:nM);
        second.text(nS<10?"0"+nS:nS);
      }
      NowTime = NowTime + 1000;
    }
     timer_rt = window.setInterval(GetRTime, 1000);
  });
}

function timeEnd() {
  $("#timer-wrap").remove();
  createPlayer();
}

function initVideo() {
  if(activity.status == 1){
    //开始直播了
    createPlayer();
  }else{
    // console.log('倒计时');
    //未开始直播
    initShare();
    initTimer();
    $("#timer-wrap").removeClass('hidden');
  }
}

function createPlayer() {
  if ($("html").hasClass('no-touch')) {
    createFlashPlayer();
  } else {
    createH5Player();
  }
}
function createFlashPlayer() {
  swfobject.embedSWF(
    "/Public/player/StrobeMediaPlayback/StrobeMediaPlayback.swf", "StrobeMediaPlayback", "100%", "100%", "10.1.0", "/Public/home/data/StrobeMediaPlayback/expressInstall.swf", parameters, {
      allowFullScreen: "true",
      wmode: wmodeValue
    }, {
      name: "StrobeMediaPlayback"
    }
  );
  // alert('非移动端，创建flash播放器');
}

function createH5Player() {
  var cont = document.getElementById("StrobeMediaPlayback");
  var video = document.createElement("video");
  video.setAttribute("poster", activity.poster);
  video.setAttribute("autoplay", true);
  video.setAttribute("controls", true);
  video.setAttribute("webkit-playsinline", true);
  cont.appendChild(video);
  var source = document.createElement("source");
  source.setAttribute("src", activity.stream);
  source.setAttribute("type", "video/mp4");
  video.appendChild(source);
}
