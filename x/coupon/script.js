$(document).ready(function () {

    var interval;

    function getCountAjax() {

        var sessid = $('#sessid').val();
        var action_id = $('#action_id').val();

        $.ajax({
            type: 'POST',
            url: '/ajax/client/promocoupon/',
            dataType: "json",
            data: {
                action: 'get_count',
                sessid: sessid,
                action_id: action_id
            },
            success: function (data) {

                interval = setTimeout(getCountAjax, 5000);
                if (data.count == '0') {
                    $(".coupons_count").html("К сожалению, купоны закончились");
                    $("#name").val("").attr("disabled", "disabled");
                    $("#phone").val("").attr("disabled", "disabled");
                    $(".submit_button").attr("disabled", "disabled");
                    $("#couponForm").off("submit");
                    clearTimeout(interval);
                } else {
                    $("#coupons_count").html(data.count + '&nbsp;' + declOfNum(data.count, ['купон', 'купона', 'купонов']));
                }
            }
        });
    }

    if ($("#action_id").length) {
        getCountAjax();
    }

    $("#couponForm").on("submit", function (e) {

        e.preventDefault(); // предотвращаем перезагрузку страницы

        var phone = $('#phone').val();
        var name = $('#name').val();
        var action_id = $('#action_id').val();
        var sessid = $('#sessid').val();
        var goto = $('input[name="goto"]').val();

        if (!$('#agree:checked').length) {
            $("#requestResult").removeClass("success").addClass("error").html("Необходимо согласие с политикой "
                + "в отношении обработки персональных данных.").show();
            return false;
        }

        grecaptcha.ready(function () {
            grecaptcha.execute('6LfbK6IlAAAAACWPKHjNecUsBIO_XHWWjUtls77I',
                {action: 'submit'}).then(function (token) {

                $.ajax({
                    type: 'POST',
                    url: '/ajax/client/promocoupon/',
                    dataType: "json",
                    data: {
                        action: 'check_phone',
                        action_id: action_id,
                        phone: phone,
                        name: name,
                        sessid: sessid,
                        recaptcha: token
                    },
                    beforeSend: function () {
                        $("#requestResult").removeClass("success").html("").hide();
                    },
                    success: function (response) {
                        if (response['status'] == 'success') {
                            $("#requestResult").addClass("success").html(response.text).show();
                            $(".submit_button").attr("disabled", "disabled");
                            $count = $("#coupons_count").html();
                            $("#coupons_count").html(parseInt($count) - 1);

                            setTimeout(function () {
                                if (goto.length) {
                                    window.location = goto;
                                } else {
                                    window.location = '/';
                                }
                            }, 3000);


                        } else {
                            $("#requestResult").removeClass("success").html(response.text).show();
                        }
                    },
                    error: function (xhr, status, error) {
                        /* $("#requestResult").removeClass("success").html(xhr.response.text).show();
                         console.log('Произошла ошибка при отправке запроса!');
                         console.log(xhr.responseText);*/
                    }
                });
            });
        });

    });

});

function declOfNum(n, text_forms) {
    n = Math.abs(n) % 100;
    var n1 = n % 10;
    if (n > 10 && n < 20) {
        return text_forms[2];
    }
    if (n1 > 1 && n1 < 5) {
        return text_forms[1];
    }
    if (n1 == 1) {
        return text_forms[0];
    }
    return text_forms[2];
}