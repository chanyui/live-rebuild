var updateScroll = function() {
  var h = document.getElementById("msgBox").offsetHeight;
  document.body.scrollTop = h
}
var timer = function() {
  window.setTimeout(function() {
    window.location.reload();
    updateScroll();
    if (typeof parent.scrollChatWindow === "function") {
      // parent.scrollChatWindow(h);
    }
  }, 10000);
}

updateScroll();

var pqs = new ParsedQueryString();
var parameterNames = pqs.params(false);
if(location.href.indexOf("islive=true") > -1){
  timer();
  // alert("这是直播，开启聊天窗口自动刷新");
}
