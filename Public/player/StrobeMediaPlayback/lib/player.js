var pqs = new ParsedQueryString();
var parameterNames = pqs.params(false);
var parameters = {
  src: activity.stream,
  autoPlay: "true",
  verbose: true,
  controlBarAutoHide: "true",
  controlBarPosition: "bottom",
  poster: activity.poster,
  plugin_hls: "StrobeMediaPlayback/HLSDynamicPlugin.swf"
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

function initVideo() {
  if ($("html").hasClass('no-touch')) {
    createFlashPlayer();
  } else {
    createH5Player();
  }
}

function createFlashPlayer() {
  swfobject.embedSWF(
    "StrobeMediaPlayback/StrobeMediaPlayback.swf", "StrobeMediaPlayback", "100%", "100%", "10.1.0", "StrobeMediaPlayback/expressInstall.swf", parameters, {
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
