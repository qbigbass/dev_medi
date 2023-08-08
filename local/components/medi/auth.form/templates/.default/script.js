$(function () {

    var ctrlDown = false,
        ctrlKey = 17,
        cmdKey = 91,
        vKey = 86,
        cKey = 67;

    $(document).keydown(function (e) {
        if (e.keyCode == ctrlKey || e.keyCode == cmdKey) ctrlDown = true;
    }).keyup(function (e) {
        if (e.keyCode == ctrlKey || e.keyCode == cmdKey) ctrlDown = false;
    });

    if ($("#user_phone").length) {

        var delFirstEight = {
            onKeyPress: function (val, e, field, options) {

                if (val == '+7 () --') {
                    field.val("");
                }
                if (val.replace(/\D/g, '').length === 11) {
                    val = val.replace('(89', '(9');
                    field.val(val);
                }
                if (val.replace(/\D/g, '').length === 2) {
                    val = val.replace('8', '');
                    field.val(val);
                }
                field.mask("+7 (999) 999-99-99", options);
            },
            placeholder: "+7 (___) ___-__-__"

        };

        // phone mask
        $("#user_phone").mask("+7 (999) 999-99-99", delFirstEight);

        $("#user_phone").on("keyup change", checkPhoneForm);
        $("#loginform").on("submit", sendAuthCode);
        $("#user_code").on("keyup change", checkCode);
        $("#smscodeform").on("submit", loginByCode);


        $("#AGREE").on("change", checkRegCode);

        var $user_phone = $("#user_phone").val();

        $("#anketa_form_data").on("submit", confirmReg);

        $("#subscribe_confirm_close").on("click", function () {
            subs_var = 1;
            $("#subscribe_confirm").hide();
        });
        $("#subscribe_confirm.answer_close").on("click", function () {
            subs_var = 1;
            $("#subscribe_confirm").hide();
        });

        var $attention = 0;
        var $sattention = 0;
    }

    $(".question").on("click", showHelp);
    $(".answer.answer_close").on("click", showHelp);
    $attention = 0;

    function showHelp() {
        $qid = $(this).data('id');
        $(".answer").hide();
        if ($qid != '') {
            if ($attention == 0) {
                $(".answer." + $qid).css("display", "inline");
                $attention = 1;
            } else {
                $(".answer." + $qid).hide();
                $attention = 0;
            }
        }
    }

    function showAttention() {
        if ($attention == 0) {
            $(".anketa_title .answer").css("display", "inline");
            $attention = 1;
            $(".subs_field .answer").hide();
            $sattention = 0;
        } else {

            $(".anketa_title .answer").hide();
            $attention = 0;
        }
    }

    function showSubAttention() {
        if ($sattention == 0) {
            $(".subs_field .answer").css("display", "inline");
            $sattention = 1;
            $(".anketa_title .answer").hide();
            $attention = 0;
        } else {

            $(".subs_field .answer").hide();
            $sattention = 0;
        }
    }

    function checkPhoneForm(e) {
        $phone = $("#user_phone");

        $("#check_auth_code").hide();
        $("#user_code").removeClass("error");

        $(".agree_block").hide();
        $("#start_reg").hide();
        $("#resend_auth").hide();

        if (ctrlDown && (e.keyCode == cKey || e.keyCode == vKey)) return;

        if ($phone.val() != $user_phone) {
            $("#check_auth_submit").attr("disabled", "disabled");
            $error = 0;
            showLoader();

            $phone.removeClass("error");

            if ($phone.val().replace(/\D/g, '').length != 11) {
                $error = 1;
                $phone.addClass("error");
            } else {
                $error = 0;
                $phone.removeClass("error");
            }
            if ($error == 0) {
                $("#send_auth_code").show().removeAttr("disabled");

                hideLoader();
            } else {
                $("#send_auth_code").attr("disabled", "disabled");

                $("#code_input").hide();
                hideLoader();
            }
            $user_phone = $phone.val();
        }
    }

    function sendAuthCode() {
        $phone = $("#user_phone");
        $("#user_code").val("");
        $phone_num = $phone.val().replace(/\D/g, '');

        $("#start_reg").hide();
        $error = 0;
        showLoader();

        $.ajax({
            url: '/ajax/lmx/user/',
            data: 'action=check_phone&phone=' + $phone_num,
            method: 'POST',
            dataType: 'json',
            success: function (data) {
                if (data.status == 'ok') {

                    $("#code_input").show();
                    $("#user_code").focus();
                    $("#resend_auth_submit").off("click");
                    $("#send_auth_code").attr("disabled", "disabled").hide();
                    $("#resend_auth").show();

                    var $resend_time = 60;

                    let timerId = setTimeout(function resendCounter($timer = 60) {

                        if ($timer > 1) {
                            $timer--;

                            $("#resend_auth_submit").html("Отправить код ещё раз - " + $timer).addClass("link-dashed").removeClass("theme-link-dashed").off("click");
                            timerId = setTimeout(resendCounter, 1000, $timer);
                        } else {
                            $("#resend_auth_submit").html("Отправить код ещё раз").removeClass("link-dashed").addClass("theme-link-dashed").on("click", sendAuthCode);
                            clearTimeout(timerId);
                        }// (*)
                    }, 1000);

                    //$("#smsloginform").off("submit");

                }
                // new Registration
                else if (data.status == 'new_send') {
                    $("#code_input").show();

                    $("#send_auth_code").attr("disabled", "disabled");
                    $("#send_auth_code").hide();

                    $("#check_auth_submit").hide();
                    $("#start_reg_submit").show().removeAttr("disabled");

                    $("#smscodeform").attr("id", "smsregform").off("submit").on("submit", checkRegCode);

                    $("#user_code").off("change keyup").on("change keyup", checkRegCode);
                    $("#AGREE").on("change", checkRegCode);

                    $(".agree_block").show();


                    $("#resend_auth").show();
                    var $resend_time = 60;

                    let timerId = setTimeout(function resendCounter($timer = 60) {

                        if ($timer > 1) {
                            $timer--;

                            $("#resend_auth_submit").html("Отправить код еще раз - " + $timer).addClass("link-dashed").removeClass("theme-link-dashed").off("click");
                            timerId = setTimeout(resendCounter, 1000, $timer);
                        } else {
                            $("#resend_auth_submit").html("Отправить код ещё раз").removeClass("link-dashed").addClass("theme-link-dashed").on("click", sendAuthCode);
                            clearTimeout(timerId);
                        }// (*)
                    }, 1000);


                } else if (data.status == 'error') {
                    $("#code_input").hide();

                }

            },
            complete: function (data) {
                hideLoader();

            }
        });
        return false;
    }

    function checkCode() {
        $code = $("#user_code").val();

        $("#user_code").removeClass("error");
        if ($code.length == 6) {
            $("#check_auth_code").removeAttr("disabled").show();
            $("#check_auth_submit").show();

            //$("#confirm_code").on("change blur", confirmCode);
        } else {

            $("#check_auth_submit").hide();
            $("#check_auth_code").attr("disabled", "disabled");
        }

    }

    function loginByCode() {
        showLoader();

        $backurl = $("input[name='BACKURL']").val();

        $code = $("#user_code").val();
        var $phone = $("#user_phone").val();

        $phone_num = $phone.replace(/\D/g, '');
        $("#user_code").removeClass("error");

        $.ajax({
            url: '/ajax/lmx/user/',
            data: 'action=check_confirm_code&code=' + $code + '&phone=' + $phone_num,

            dataType: 'json',
            success: function (data) {
                if (data.status == 'confirmed' || data.status == 'ok') {
                    let loymaxUserId = data.loymaxUserId;
                    let _gcTracker=_gcTracker||[];
                    // JS-трекер Loymax (Событие: авторизация и регистрация)
                    _gcTracker.push(['user_login', { user_id: loymaxUserId }]);
                    // код отправлен
                    if ($backurl != '' && $backurl !== undefined) {
                        window.location = $backurl;
                    } else {
                        document.location.reload();
                    }

                } else if (data.status == 'error') {
                    $("#user_code").addClass("error");
                }
            },
            complete: function () {

                hideLoader();
            }
        });
        return false;
    }

    function checkRegCode() {
        $code = $("#user_code").val();

        $agree = $("#AGREE");

        $error = 0;
        if ($agree.prop("checked") === false) {
            $error = 1;
            $agree.addClass("error");
            hideLoader();
            $("#start_reg").hide();
            $("#smsregform").off("submit");
            $("#start_reg_submit").attr("disabled", "disabled");
        } else {

            if ($code.length == 6) {
                $("#start_reg").show();
                $("#start_reg_submit").removeAttr("disabled");
                $("#smsregform").off("submit").on("submit", confirmCode);

                $("#resend_auth").hide();
                $("#confirm_code").on("change blur", checkRegCode);
            } else {

                $("#start_reg").hide();
                $("#smsregform").off("submit");
                $("#start_reg_submit").attr("disabled", "disabled");
            }
        }
        return false;


    }

    function confirmCode() {
        showLoader();
        var $code = $("#user_code").val();
        var $phone = $("#user_phone").val();

        $phone_num = $phone.replace(/\D/g, '');
        $("#user_code").removeClass("error");
        $.ajax({
            url: '/ajax/lmx/client/',
            data: 'action=check_confirm_code&code=' + $code + '&phone=' + $phone_num,

            dataType: 'json',
            success: function (data) {
                if (data.status == 'confirmed') {
                    // код отправлен
                    $(".confirm_phone_form").hide();
                    $(".reg_form").hide();

                    $(".reg_anketa_form").css("display", "flex");

                    $("#submit_anketa").removeAttr("disabled").on("click", confirmReg);

                    //$("#anketa_form_data").on("submit", confirmReg);

                    $("#user_regphone2").val($phone);


                } else if (data.status == 'fail') {
                    $(".reg_form").show();
                    $(".reg_anketa_form").hide();

                    //$("#anketa_form_data").off("submit");

                    $("#submit_anketa").off("click");
                    $("#submit_anketa").attr("disabled", "disabled");
                    $("#send_code").removeAttr("disabled");
                    $("#confirm_code").addClass("error");
                    $("#AGREE").attr("disabled");
                }
            },
            complete: function () {

                hideLoader();
            }
        });
        return false;
    }

    var subs_var = 0;

    function confirmReg() {

        showLoader();

        $(".error").removeClass('error');
        $phone = $("#user_regphone2");
        $email = $("#user_email");
        $name = $("#user_name");
        $lname = $("#user_lname");
        $date = $("#user_date");
        $sex = $("input[name='SEX']:checked").val();

        $error = 0;

        if ($name.val().length <= 2) {
            $error = 1;
            $name.addClass("error");
            hideLoader();
        }

        if ($lname.val().length <= 2) {
            $error = 1;
            $lname.addClass("error");
            hideLoader();
        }

        if ($date.val() == "") {
            $error = 1;
            $date.addClass("error");
            hideLoader();
        }
        if ($sex != "1" && $sex != '2') {
            $error = 1;
            $(".user_sex_block").addClass("error");
            hideLoader();
        }

        if ($phone.val().replace(/\D/g, '').length != 11) {
            $error = 1;
            $phone.addClass("error");
            hideLoader();
        } else if ($("#susbscibe_checkbox").prop("checked") == false && subs_var == 0) {
            $error = 1;
            $("#subscribe_confirm").show();
            hideLoader();
        }

        if ($error == 0) {
            $("#reg_form_info").html("").hide();
            $(".error").removeClass('error');
            $phone_num = $phone.val().replace(/\D/g, '');
            data = $("#anketa_form_data").serialize();

            data.phone = $phone_num;
            data.action = 'finish_reg';

            $.ajax({
                url: '/ajax/lmx/client/',
                data: data,
                method: 'POST',
                dataType: 'json',
                success: function (data) {
                    if (data.status == 'finished') {
                        // код отправлен
                        $("#reg_form_info").html("Спасибо, регистрация успешно пройдена! Сейчас вы будете перенаправлены в <a href='/lk/'>личный кабинет</a>.").addClass("success").show();
                        ym(30121774, 'reachGoal', 'USER_REGISTERED');
/*
						var dataLayer = window.dataLayer || [];
                        dataLayer.push({'event': 'click', 'eventCategory': 'Event', 'eventAction': 'USER_REGISTERED'});*/
                        window.dataLayer = window.dataLayer || [];
                        dataLayer.push({
                            'event': 'gtm-event',
                            'gtm-event-category': 'Event',
                            'gtm-event-action': 'Click',
                            'gtm-event-label': 'USER_REGISTERED',
                        });
                        setTimeout(function () {

                            window.location.href = '/lk/';
                        }, 2000);
                    } else if (data.status == 'error') {
                        if (data.error == 'registered') {
                            $("#reg_form_info").html("Номер телефона уже зарегистрирован.  <a href='/lk/'>Войти в личный кабинет</a> или <a href='/lk/remind/'>восстановить пароль</a>?").show();

                        } else {
                            $("#reg_form_info").html(data.text).show();
                        }

                    }
                    hideLoader();
                },
                complete: function () {

                    hideLoader();
                }
            });
        } else {
            $("#reg_form_info").html("Проверьте поля анкеты.").addClass("alert").show();
        }
        return false;
    }

});
