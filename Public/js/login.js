$('.btn-eyes').on('click', function () {
    if ($(this).hasClass('glyphicon-eye-close')) {
        $(this).removeClass('glyphicon-eye-close').addClass('glyphicon-eye-open').prev('input').attr('type', 'text');
    } else {
        $(this).removeClass('glyphicon-eye-open').addClass('glyphicon-eye-close').prev('input').attr('type', 'password');
    }
});

$("#loginForm").submit(function(){
    var username = $("#username").val();
        username = $.trim(username);
    if(username.length == 0){
        $(".login-tip").css('display','block').text('请输入用户名/邮箱/手机！');
        return false;
    }
    var password = $("#password").val();
        password = $.trim(password);
    if(password.length == 0){
        $(".login-tip").css('display','block').text('请输入密码！');
        return false;
    }
    var password = $("#password").val();
        password = $.trim(password);
    if(password.length < 6 || password.length > 16){
        $(".login-tip").css('display','block').text('密码错误！');
        return false;
    }
});