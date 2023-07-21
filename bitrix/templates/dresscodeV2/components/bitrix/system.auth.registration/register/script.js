$(function(){

	if ($("#user_regphone").length) {

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
		$("#user_regphone").mask("+7 (999) 999-99-99", delFirstEight);
		$("#user_regphone2").mask("+7 (999) 999-99-99", delFirstEight);

		$("#change_phone").on("click", changePhone);


		$("#user_regphone").on("change", checkForm);
		$("#AGREE").on("change", checkForm);
		$("#regform").on("submit", sendForm);
		//$(".reg_help.question").on("click", showAttention);

		$("#subscribe_confirm_close").on("click", function(){
			subs_var = 1;
			$("#subscribe_confirm").hide();
		})

		//$(".subscribe_help.question").on("click", showSubAttention);
		//$("#anketa_form_data").on("submit", finishReg);
		var $attention = 0;var $sattention = 0;
	}

	$(".question").on("click", showHelp);
	$attention = 0;
	function showHelp(){
		$qid = $(this).data('id');
		$(".answer").hide();
		if ($qid != ''){
			if ($attention == 0)
			{
				$(".answer."+$qid).css("display", "inline");
				$attention = 1;
			}
			else {
				$(".answer."+$qid).hide();
				$attention = 0;
			}
		}
	}

	function showAttention(){
		if ($attention == 0)
		{
			$(".anketa_title .answer").css("display", "inline");
			$attention = 1;
			$(".subs_field .answer").hide();
			$sattention = 0;
		}
		else {

			$(".anketa_title .answer").hide();
			$attention = 0;
		}
	}
	function showSubAttention(){
		if ($sattention == 0)
		{
			$(".subs_field .answer").css("display", "inline");
			$sattention = 1;
			$(".anketa_title .answer").hide();
			$attention = 0;
		}
		else {

			$(".subs_field .answer").hide();
			$sattention = 0;
		}
	}

	function changePhone(){

		$phone = $("#user_regphone");
		$agree = $("#AGREE");

		$("#send_code").off('click');

		$(".confirm_phone_form").hide();
		doStep(1);

		$("#send_code").attr("disabled", "disabled");

		$agree.removeClass("error").removeAttr("disabled");
		$phone.removeClass("error").removeAttr("disabled");

		$("#user_regphone").val("");
	}

    function checkForm(){
		$phone = $("#user_regphone");
		$agree = $("#AGREE");
		$error = 0;
		showLoader();
		$("#send_code").off('click');

		$agree.removeClass("error");
		$phone.removeClass("error");

		if ($phone.val().replace(/\D/g, '').length != 11)
		{
			$error = 1;
			$phone.addClass("error");
		}
		else if ($agree.prop("checked") === false)
		{
			$error = 1;
			$agree.addClass("error");
		}
		else {
			$error = 0;
			$agree.removeClass("error");
			$phone.removeClass("error");
			$("#reg_form_info").hide().html("");
		}
		if ($error == 0)
		{
			$("#send_code").removeAttr("disabled").on("click", sendForm);
			hideLoader();
		}
		else {
			$("#send_code").attr("disabled", "disabled");
			hideLoader();
			$("#send_code").off('click');
		}
	}

	function sendForm(){

		showLoader();
		$phone = $("#user_regphone");
		$agree = $("#AGREE");
		$error = 0;
		$("#send_code").off('click');
		if ($phone.val().replace(/\D/g, '').length != 11)
		{
			$error = 1;
			$phone.addClass("error");
			hideLoader();
			$("#send_code").off('click');
		}
		else if ($agree.prop("checked") === false)
		{
			$error = 1;
			$agree.addClass("error");
			hideLoader();
			$("#send_code").off('click');
		}
		else {
			$error = 0;
			$agree.removeClass("error");
			$phone.removeClass("error");

		}
		if ($error == 0)
		{
			$phone_num = $phone.val().replace(/\D/g, '');

			$("#send_code").attr("disabled", "disabled");
			$("#user_regphone").attr("disabled", "disabled");
			$("#AGREE").attr("disabled", "disabled");
			$.ajax({
                url: '/ajax/lmx/client/',
				data: 'action=get_confirm_code&phone='+$phone_num,

				dataType: 'json',
				success:  function(data) {
					console.log(data);
					if (data.status == 'send'){
						// код отправлен
						$("#reg_form_info").html("").hide();
						$(".confirm_phone_form").show();
						doStep(2);
						$("#confirm_code").on("change ", checkCode);

					}
					else if (data.status == 'anketa')
					{
						$(".reg_anketa_form").css("display", "flex");
						$("#user_regphone2").val($phone_num);

						$("#user_regphone2").mask("+7 (999) 999-99-99", delFirstEight);

						$("#confirm_code").off("change");
						$("#user_name").val("");
						$(".reg_form").hide();
						$(".confirm_phone_form").hide();
						doStep(3);
					}
					else if (data.status == 'lk')
					{
						$(".reg_anketa_form").css("display", "flex");
						$("#user_regphone2").val($phone_num);

						$("#user_regphone2").mask("+7 (999) 999-99-99", delFirstEight);

						$("#user_name").val("");
						$(".reg_form").hide();
						$(".confirm_phone_form").hide();
						doStep(3);
					}
					else if (data.status == 'error')
					{
						if (data.error == 'registered')
						{
							$("#reg_form_info").html("Номер телефона уже зарегистрирован.  <a href='/lk/'>Войти в личный кабинет</a> или <a href='/lk/remind/'>восстановить пароль</a>?").show();
							doStep(1);

							$(".confirm_phone_form").hide();
							$("#user_regphone").removeAttr("disabled").addClass("error");
							$("#AGREE").removeAttr("disabled");
						}
						else if (data.error == 'need_pass')
						{
							$("#reg_form_info").html("Номер телефона уже зарегистрирован.  <a href='/lk/'>Войти в личный кабинет</a> или <a href='/lk/remind/'>восстановить пароль</a>?").show();
							doStep(1);

							$(".confirm_phone_form").hide();
							$("#user_regphone").removeAttr("disabled").addClass("error");
							$("#AGREE").removeAttr("disabled");
						}
						else {
								if (data.text)
									$("#reg_form_info").html(data.text).show();
						}

						$("#send_code").removeAttr("disabled");
						$("#user_regphone").attr("disabled");
						$("#AGREE").attr("disabled");
					}

					hideLoader();
                }
            });
		}
		return false;
	}

	function checkCode(){
		$code = $("#confirm_code").val();


		$("#reg_form_info").html("").hide();

		if ($code.length == 6){
			$("#check_code").removeAttr("disabled");
			$("#check_code").on("click", confirmCode);

			//$("#confirm_code").on("change blur", confirmCode);
		}
		else {

			$("#check_code").off("click");
			$("#check_code").attr("disabled", "disabled");
		}
	}
	function confirmCode(){
		showLoader();
		var $code = $("#confirm_code").val();
		var $phone = $("#user_regphone").val();

		$phone_num = $phone.replace(/\D/g, '');
		$("#confirm_code").removeClass("error");

		$.ajax({
			url: '/ajax/lmx/client/',
			data: 'action=check_confirm_code&code='+$code+'&phone='+$phone_num,

			dataType: 'json',
			success:  function(data) {
				if (data.status == 'confirmed'){
					// код отправлен
					$(".confirm_phone_form").hide();
					$(".reg_form").hide();

					$(".reg_anketa_form").css("display", "flex");

					$("#submit_anketa_form").removeAttr("disabled").on("submit", confirmReg);
					 $("#user_regphone2").val($phone);

				}
				else if (data.status == 'fail')
				{$(".reg_form").show();
					$(".reg_anketa_form").hide();
					$("#submit_anketa").attr("disabled", "disabled");
					$("#send_code").removeAttr("disabled");
					$("#confirm_code").addClass("error");
					$("#AGREE").attr("disabled");
				}
			},
			complete: function(){

				hideLoader();
			}
		});

	}


	function doStep($num){
		$(".reg_step").removeClass('active').removeClass('now');
		$(".reg_step_line").removeClass('active');

		if ($num == '1')
		{
			$(".reg_step.step1").addClass('active').addClass('now');
		}
		else if ($num == '2')
		{
			$(".reg_step.step1").addClass('active');
			$(".reg_step.step2").addClass('active').addClass('now');
			$(".reg_step_line.step1").addClass('active');
		}
		else if($num == '3')
		{
			$(".reg_step.step1").addClass('active');
			$(".reg_step.step2").addClass('active');
			$(".reg_step.step3").addClass('active').addClass('now');
			$(".reg_step_line.step1").addClass('active');
			$(".reg_step_line.step2").addClass('active');
		}
		else if($num == '4')
		{
			/*$(".reg_form").hide();
			$(".reg_anketa_form").hide();*/
			$("#reg_form_info").html("Спасибо, регистрация успешно пройдена! Сейчас вы будете перенаправлены в <a href='/lk/'>личный кабинет</a>.").addClass("success").show();
			setTimeout(function () {

				window.location.href = '/lk/';
			},  1200);
		}
	}
	var subs_var = 0;
	function confirmReg(){

		showLoader();

		$phone = $("#user_regphone2");
		$email = $("#user_email");
		$name = $("#user_name");
		$lname = $("#user_lname");
		$date = $("#user_date");
		$sex = $("input[name='USER_SEX']:checked").val();
		//$user_pass = $("#reguser_pass");
		//$user_pass2 = $("#user_pass2");
		$error = 0;

		if ($phone.val().replace(/\D/g, '').length != 11)
		{
			$error = 1;
			$phone.addClass("error");
			hideLoader();
		}
		/*else if ($user_pass.val() != $user_pass2.val() || $user_pass.val() == "")
		{
			$error = 1;
			$user_pass.addClass("error");
			$user_pass2.addClass("error");
			hideLoader();
		}*/
		else if ($("#susbscibe_checkbox").prop("checked") == false && subs_var == 0)
		{
			$error = 1;
			$("#subscribe_confirm").show();
								hideLoader();
		}
		else {
			$error = 0;
			//$user_pass.removeClass("error");
			//$user_pass2.removeClass("error");
		}
		if ($error == 0)
		{
			$phone_num = $phone.val().replace(/\D/g, '');
			data = $("#anketa_form_data").serialize();
			/*data.phone = $("#user_regphone2").val();
			data.email = $("#user_email").val();
			data.sex = $sex;
			data.name = $("#user_name").val();
			data.lname = $("#user_lname").val();
			data.pass = $("#user_pass").val();
			data.date = $("#user_date").val();*/
			data.phone = $phone_num;
			data.action = 'finish_reg';

			$.ajax({
				url: '/ajax/lmx/client/',
				data: data,
				method: 'POST',
				dataType: 'json',
				success:  function(data) {
					console.log(data);
					if (data.status == 'finished'){
						// код отправлен
						console.log("ok");
						doStep(4);
					}

					else if (data.status == 'error')
					{
						console.log(data);
						if (data.error == 'registered')
						{
							$("#reg_form_info").html("Номер телефона уже зарегистрирован.  <a href='/lk/'>Войти в личный кабинет</a> или <a href='/lk/remind/'>восстановить пароль</a>?").show();
							doStep(1);

							$(".confirm_phone_form").hide();
							$("#user_regphone").removeAttr("disabled").addClass("error");
							$("#AGREE").removeAttr("disabled");
						}

						else {
							$("#reg_form_info").html(data.text).show();
						}

						$("#send_code").removeAttr("disabled");
						$("#user_regphone").attr("disabled");
						$("#AGREE").attr("disabled");
					}
				//	doStep(4);
					hideLoader();
				},
				complete: function(){

					hideLoader();
				}
			});
		}
		return false;
	}

	$("#anketa_form_data").on("submit", confirmReg);


	/*var authFormSubmit = function(event){

		//jquery vars
		var $form = $(this);
		var $formFields = $form.find("input").removeClass("error");
		var $userPersonalInfoReg = $form.find("#userPersonalInfoReg");

		//other
		var fieldsVerification = true;

		//check personal info
		if($userPersonalInfoReg.length !== 0 && !$userPersonalInfoReg.prop("checked")){
			$userPersonalInfoReg.addClass("error");
			fieldsVerification = false;
		}

		//verification fields
		$formFields.each(function(){

			//get jquery object for next field
			var $nextField = $(this);

			//check filling
			if($nextField.data("required") == "Y" && $nextField.val() ==""){

				//set error class
				$nextField.addClass("error");

				//set error flag
				fieldsVerification = false;

			}
		});

		//check errors
		if(fieldsVerification === false){
			return event.preventDefault();
		}

	};

	//bind
	$(document).on("submit", ".bx-auth-register-form", authFormSubmit);*/

});
