/**
 * Created by admin on 2016/10/26.
 */
$(function () {
    $('#myCarousel').carousel({
        pause: false,
        interval: 2000,
        wrap: true
    });
    $('#myCar').carousel({
        pause: true,
        interval: false
    });
    
});