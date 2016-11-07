
  var updateScroll = function() {
    var h = document.getElementById("msgBox").offsetHeight;
    document.body.scrollTop = h
  }
  var timer = function() {
    window.setInterval(function() {
      window.location.reload();
      updateScroll();
      if (typeof parent.scrollChatWindow === "function") {
        // parent.scrollChatWindow(h);
      }
    }, 10000);
  }

updateScroll();
// timer();
