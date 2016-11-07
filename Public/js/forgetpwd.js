var phone_point = 0;
var email_point = 0;
var register_type = $("input[name='regist-type']:checked").val();


$('.btn-eyes').on('click', function () {
    if ($(this).hasClass('glyphicon-eye-close')) {
        $(this).removeClass('glyphicon-eye-close').addClass('glyphicon-eye-open').siblings('input').attr('type', 'text');
    } else {
        $(this).removeClass('glyphicon-eye-open').addClass('glyphicon-eye-close').siblings('input').attr('type', 'password');
    }
});

$('input[name="regist-type"]').on('change', function (e) {
    var obj = {
        'phone-radio': 'hide-phone',
        'email-radio': 'hide-email'
    };

    $('.form-group[class*="hide-"]').show();
    $('.' + obj[e.target.id]).hide();
});

function countDown(second) {
    var time = second;
    if (time > 0) {
        time--;
        $('.btn-send-phone').addClass('moving').text(time + 's');
        setTimeout(function () {
            countDown(time);
        }, 1000)
    } else {
        $('.btn-send-phone').removeClass('moving').text('重新发送');
        return false;
    }
}

$(document).ready(function () {
    $("#registForm").validate({
        rules: {
            phone: {
                required: '#phone-radio:checked',
                minlength: 11,
                maxlength: 11,
                number: true
            },
            phoneCode: {
                required: '#phone-radio:checked',
                minlength: 4
            },
            email: {
                required: '#email-radio:checked',
                email: true
            }
        },
        messages: {
            phone: {
                required: '请输入手机号',
                minlength: '请输入合法的手机号码',
                maxlength: '请输入合法的手机号码',
                number: '请输入合法的手机号码'
            },
            phoneCode: {
                required: '请输入收到的手机验证码',
                minlength: '手机验证码不能少于4位'
            },
            email: {
                required: '请输入邮箱号',
                email: '请输入合法的邮箱号'
            }
        },
        submitHandler: function(form){
            if((register_type == 1 && phone_point == 1) || (register_type == 2 && email_point == 1)){
                form.submit();
            }
        }
    });
});