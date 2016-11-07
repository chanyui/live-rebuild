$('input[name="role_type"]').on('change', function (e) {
    var obj = {
        'for-people': 'hide-people',
        'for-company': 'hide-company'
    };

    $('.form-group[class*="hide-"]').show();
    $('.' + obj[e.target.id]).hide();
});


