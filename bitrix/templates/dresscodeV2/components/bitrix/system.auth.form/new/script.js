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
		$("#user_phone2").mask("+7 (999) 999-99-99", delFirstEight);

		$("#user_phone").on("change keyup", checkForm);
		$("#user_pass").on("change keyup", checkForm);
    }

    function checkForm(){
        $phone = $("#user_phone");
        $pass = $("#user_pass");
        $error = 0;
        showLoader();

        $pass.removeClass("error");
        $phone.removeClass("error");

        if ($phone.val().replace(/\D/g, '').length != 11)
        {
            $error = 1;
            $phone.addClass("error");
        }
        else if ($pass.val() == '')
        {
            console.log($pass.val());
            $error = 1;
            $pass.addClass("error");
        }
        else {
            $error = 0;
            $pass.removeClass("error");
            $phone.removeClass("error");
        }
        if ($error == 0)
        {
            $("#send_auth").removeAttr("disabled");
            hideLoader();
        }
        else {
            $("#send_auth").attr("disabled", "disabled");
            hideLoader();
        }
    }
});
