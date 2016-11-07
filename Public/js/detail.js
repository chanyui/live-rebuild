var time = 0;
$(document).ready(function () {
    time = $('#time-control').attr('data-time');
    coumtDownTime();
});

function coumtDownTime() {
    var minSeconds = time-5000,
            nowMinSeconds = (new Date()).getTime(),
            minusMinSeconds = (minSeconds - nowMinSeconds) / 1000;

    if (minusMinSeconds > 0) {
        setTimeout(function () {
            coumtDownTime();
            var hours = Math.floor(minusMinSeconds / 60 / 60),
                    m = Math.floor((minusMinSeconds - hours * 60 * 60) / 60),
                    s = Math.floor((minusMinSeconds - hours * 60 * 60 - m * 60)),
                    d = parseInt(hours / 24),
                    h = hours - d * 24;
            var data = {
                d: d,
                h: h,
                m: m,
                s: s
            };

            renderHtml(true, data);
        }, 1000)
    } else {
        toggleInfoWithAd(2);//直播时切换右侧信息
        toggleVideo();
        return false;
    }

}

function renderHtml(status, data) {
    if (status) {
        $('.time-d').text(data.d);
        $('.time-h').text(data.h);
        $('.time-m').text(data.m);
        $('.time-s').text(data.s);
    }
}

//右侧广告和活动信息的切换
function toggleInfoWithAd(status){

    if(status==1){
        $('#activityinfo').show();
        $('#activityad').hide();
    }else{
        var adnum = "{$adnum}";

        if(adnum != 0){
            $('#activityinfo').hide();
            $('#activityad').show();
        }else{
            $('#activityinfo').show();
            $('#activityad').hide();
        }
    }
}

$(".open-model").on('click',function(){
    $('#myModal').modal('show');

});

$("#activitycontent").click(function(){
    $(".activitycontent").show();$(".enter-list,.comments-container").hide();
    $(this).addClass('active').siblings().removeClass('active');
});
$("#enter-list").click(function(){
    $(".enter-list").show();$(".activitycontent,.comments-container").hide();
    $(this).addClass('active').siblings().removeClass('active');
});

$("#comments-container").click(function(){
    $(".comments-container").show();$(".enter-list,.activitycontent").hide();
    $(this).addClass('active').siblings().removeClass('active');
});
