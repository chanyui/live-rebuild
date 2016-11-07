$('.btn-eyes').on('click', function () {
    if ($(this).hasClass('glyphicon-eye-close')) {
        $(this).removeClass('glyphicon-eye-close').addClass('glyphicon-eye-open').siblings('input').attr('type', 'text');
    } else {
        $(this).removeClass('glyphicon-eye-open').addClass('glyphicon-eye-close').siblings('input').attr('type', 'password');
    }
});

$(document).ready(function () {
    $("#registForm").validate({
        rules: {
            password: {
                required: true,
                minlength: 6,
                maxlength: 16
            },
            confirm_password: {
                required: true,
                minlength: 6,
                maxlength: 16,
                equalTo: "#password"
            }
        },
        messages: {
            password: {
                required: "请输入密码",
                minlength: "密码长度不能小于 6 位",
                maxlength: "密码长度不能大于 16 位"
            },
            confirm_password: {
                required: "请输入密码",
                minlength: "密码长度不能小于 6 位",
                maxlength: "密码长度不能大于 16 位",
                equalTo: "两次密码输入不一致"
            }
        },
        submitHandler: function(form){
            form.submit();
        }
    });
});