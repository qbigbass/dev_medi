$(function(){

	var selectTab = function(event){

		//jquery vars
		var $this = $(this);
		var $auth = $this.parents(".bx-auth");

		//tabs select
		var $typeSelectorLinks = $auth.find(".bx-auth-type-select-link");
		var $typeCurrentSelector = $this.parents(".bx-auth-type-select-item");

		//tabs
		var $tabsParent = $auth.find(".bx-auth-type-items");
		var $tabs = $tabsParent.find(".bx-auth-type-item");

		//tab index
		var tabIndex = $typeCurrentSelector.index();

		//remove state class
		$tabs.removeClass("active");
		$typeSelectorLinks.addClass("btn-border");

		//set state class
		$tabs.eq(tabIndex).addClass("active");
		$this.removeClass("btn-border");

		//block actions
		return event.preventDefault();

	};

	//binds
	$(document).on("click", ".bx-auth-type-select-link", selectTab);




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

		$("#user_phone").on("keyup change", checkForm);
		$("#smsloginform").on("submit", sendAuthCode);
		$("#user_code").on("keyup change", checkCode);
		$("#smscodeform").on("submit", loginByCode);
    }

    function checkForm(){
        $phone = $("#user_phone");
		$("#check_auth_submit").hide();
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
            $("#send_auth_code").removeAttr("disabled");
            hideLoader();
        }
        else {
            $("#send_auth_code").attr("disabled", "disabled");
			$("#send_auth_code").show();
			$("#code_input").hide();
            hideLoader();
        }
    }

	function sendAuthCode() {
        $phone = $("#user_phone");
		$phone_num = $phone.val().replace(/\D/g, '');
        $error = 0;
        showLoader();

		$.ajax({
			url: '/ajax/lmx/client/',
			data: 'action=send_auth_sms&phone='+$phone_num,
			method: 'POST',
			dataType: 'json',
			success:  function(data) {
				if (data.status == 'ok'){

					$("#code_input").show();

					$("#send_auth_code").attr("disabled", "disabled");
					$("#send_auth_code").hide();
					//$("#smsloginform").off("submit");

				}

				else if (data.status == 'error')
				{
					$("#code_input").hide();
					console.log(data);

				}

			},
			complete: function(data) {
				hideLoader();

			}
		});
		return false;
	}

	function checkCode() {
		$code = $("#user_code").val();

		if ($code.length == 6){
			$("#check_auth_code").removeAttr("disabled");
			$("#check_auth_submit").show();

			//$("#confirm_code").on("change blur", confirmCode);
		}
		else {

			$("#check_auth_submit").hide();
			$("#check_auth_code").attr("disabled", "disabled");
		}

	}

	function loginByCode(){
		showLoader();

		$code = $("#user_code").val();
		var $phone = $("#user_phone").val();

		$phone_num = $phone.replace(/\D/g, '');
		 $("#user_code").removeClass("error");

		$.ajax({
			url: '/ajax/lmx/client/',
			data: 'action=check_auth_sms&code='+$code+'&phone='+$phone_num,

			dataType: 'json',
			success:  function(data) {
				if (data.status == 'confirmed' || data.status == 'ok'){
					// код отправлен
                    document.location.reload();

				}
				else if (data.status == 'error')
				{
					$("#user_code").addClass("error");
				}
			},
			complete: function(){

				hideLoader();
			}
		});
		return false;
	}
});
