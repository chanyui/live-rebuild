var time = 0;
$(document).ready(function () {
    time = $('#time-control').attr('data-time');
    coumtDownTime();
});

function coumtDownTime() {
    var minSeconds = time,
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
        return false;
    }

}

function renderHtml(status, data) {
    if (status) {
        $('.data-day').text(data.d);
        $('.data-hour').text(data.h);
        $('.data-minute').text(data.m);
        $('.data-second').text(data.s);
    }
}