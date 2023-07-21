<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Восстановление пароля");

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/lk.css");
?>
<div class="reg_page">
    <div class="reg_head">
        <div class="reg_title">Восстановление пароля</div>

        <div class="reg_link2auth">
            Нет аккаунта? <a href="/personal/register/">Регистрация</a>
        </div>
    </div>

    <div id="status_message"></div>
    <form method="post"  id="remindform" action="<?=$arResult["AUTH_URL"]?>" name="remindform">
        <?if(!empty($arResult["BACKURL"])):?>
            <input type="hidden" name="BACKURL" value="<?=$arResult["BACKURL"]?>" />
        <?endif?>
        <input type="hidden" name="AUTH_BY" value="PASSWORD" />
        <input type="hidden" name="SITE_ID" value="<?=SITE_ID?>" />
    <div class="reg_form">
        <div class="tr">
            <label for="user_phone">Номер телефона <span class="starrequired">*</span></label><br>
            <input type="tel" id="user_phone" autocomplete="false" name="USER_LOGIN" value="<?=$arResult["USER_LOGIN"]?>" placeholder="+7 (___) ___-__-__"/>
        </div>

        <div class="tr submit">
            <input type="submit" id="send_auth" name="send_auth" disabled="disabled"  class="submit_button" value="Отправить код"/>
        </div>
        <br><br>
        <a href="/lk/" class="remind_link">Авторизация</a>
    </div>
    </form>

    <form  id="remindConfirmForm" style="display:none" method="post">
        <input type="hidden" id="user_phone2" />
        <div class="reg_form">
            <div class="tr">
                <label for="code">Код подтверждения <span class="starrequired">*</span></label><br>
                <input type="text" id="code" autocomplete="false" name="code" value="" maxlength="6"/>
            </div>

            <div class="tr">
                <label for="password">Новый пароль: <span class="starrequired">*</span></label><br>
                <input type="password" id="password" autocomplete="false" name="password" value="" />
            </div>


            <div class="tr">
                <label for="password2">Повторите новый пароль: <span class="starrequired">*</span></label><br>
                <input type="password" id="password2" autocomplete="false" name="password2" value="" />
            </div>

            <div class="tr submit">
                <input type="submit" id="send_confirm_pass" name="send_confirm_pass"   class="submit_button" value="Сменить пароль"/>
            </div>
        </div>
    </form>


</div>

<script>
$(function(){


	if ($("#user_phone").length) {

		var delFirstEight = {
		  onKeyPress: function(val, e, field, options) {

			if (val == '+7 () --')
			{
  				field.val("");
		  	}
		    if (val.replace(/\D/g, '').length===11)
  			{
				val = val.replace('(89','(9');
				field.val(val);
			}
			if (val.replace(/\D/g, '').length===2)
			{
				val = val.replace('8','');
				field.val(val);
			 }
			 field.mask("+7 (999) 999-99-99", options);
			},
			placeholder: "+7 (___) ___-__-__"

		};

		// phone mask
		$("#user_phone").mask("+7 (999) 999-99-99", delFirstEight);

		$("#user_phone").on("change keyup", checkRemindForm);
    }

    function checkRemindForm(){
        $phone = $("#user_phone");
        $error = 0;
        showLoader();

        $phone.removeClass("error");

        if ($phone.val().replace(/\D/g, '').length != 11)
        {
            $error = 1;
            $phone.addClass("error");
        }
        else {
            $error = 0;
            $phone.removeClass("error");
        }
        if ($error == 0)
        {
            $("#send_auth").removeAttr("disabled");
            hideLoader();
            return true;
        }
        else {
            $("#send_auth").attr("disabled", "disabled");
            hideLoader();
            return false;
        }
    }

    $("#remindform").on("submit", function() {
        if (checkRemindForm())
        {
            $phone = $("#user_phone");
            $error = 0;
            showLoader();

            if ($phone.val().replace(/\D/g, '').length != 11)
    		{
    			$error = 1;
    			$phone.addClass("error");
    			hideLoader();
    		}
    		else {
    			$error = 0;
    			$phone.removeClass("error");
    		}

            if ($error == 0)
    		{
    			$phone_num = $phone.val().replace(/\D/g, '');
                $("#user_phone2").val($phone_num );

    			$("#user_phone").attr("disabled", "disabled");
    			$.ajax({
                    url: '/ajax/lmx/client/',
    				data: 'action=remind_pass&phone='+$phone_num,

    				dataType: 'json',
    				success:  function(data) {

                        if (data.status == 'ok' ){
    						// код отправлен
                            $("#code").val("");
                            $("#password").val("");
                            $("#password2").val("");
    						$("#remindConfirmForm").show();
                            $("#remindform").hide();
                            $("#remindConfirmForm").on("submit", remindConfirmForm);

    					}
                        else {
                            $("#remindform").show();

                			$("#user_phone").removeAttr("disabled");
                            $("#remindConfirmForm").off("submit");
    						$("#remindConfirmForm").hie();
                        }
    					console.log(data);
                    },
                    complete: function(){
                        hideLoader();
                    }
                });
            }

            return false;
        }
        else {
            return false;
        }
    });

    function remindConfirmForm() {
        $code = $("#code");
        $pass = $("#password");
        $pass2 = $("#password2");

        $phone = $("#user_phone2");
        $phone_num = $phone.val().replace(/\D/g, '');

        $pass.removeClass('error');
        $pass2.removeClass('error');
        $code.removeClass('error');

        if ($code.val() == '')
        {
            $code.addClass('error');
        }
        else if ($pass.val() == '' || $pass.val() != $pass2.val())
        {
            $pass.addClass('error');
            $pass2.addClass('error');

        }
        else {
            $.ajax({
                url: '/ajax/lmx/client/',
                data: 'action=remind_pass_confirm&phone='+$phone_num+'&pass='+$pass.val()+'&code='+$code.val(),

                dataType: 'json',
                success:  function(data) {
 
                    if (data.data.data.access_token){
                        // код отправлен
                        $("#status_message").removeClass("fail success").addClass("success").html("Пароль успешно обновлен.  Сейчас вы будете перенаправлены в <a href='/lk/'>личный кабинет</a>.").show();

                        setTimeout(function () {

            				window.location.href = '/lk/';
            			},  2000);

                    }
                    else {
                        if (data.data.result == 'incorrect code')
                        {
                            $code.addClass('error');
                            $("#status_message").removeClass("fail success").addClass("fail").html("Введен не верный код подтверждения. Побробуйте снова.").show();
                        }
                        else{
                            $("#status_message").removeClass("fail success").addClass("fail").html("Произошла ошибка.  Пожалуйста, обновите страницу и побробуйте снова.").show();
                        }
                    }
                },
                complete: function(){
                    hideLoader();
                }
            });
        }

        return false;
    }
});

</script>
