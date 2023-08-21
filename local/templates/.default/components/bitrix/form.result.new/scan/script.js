$(function(){

	var sendWebForm = function(event, data){

		var formData = new FormData(this);

        var requiredErrorPosition = false;
		var requiredError = false;

		var $thisForm = $(this).addClass("loading");
		var $parentThis = $thisForm.parents(".webFormDw");
		var $thisFormFields = $thisForm.find(".webFormItemField");
		var $thisFormErrors = $thisForm.find(".webFormItemError");
		var $submitButton = $thisForm.find('input[type="submit"]').addClass("loading");
		var $webFormError = $thisForm.find(".webFormError");
		var $webFormCaptchaSid = $thisForm.find(".webFormCaptchaSid");
		var $webFormCaptchaImage = $thisForm.find(".webFormCaptchaImage");

		var formId = $parentThis.data("id");

		$thisFormFields.each(function(i, nextField){

			var $nextField = $(nextField);
			if($nextField.data("required") == "Y"){
				var $nextFieldEx = $nextField.find('input[type="text"],input[type="tel"], input[type="password"], input[type="email"], input[type="file"], select, textarea');

                if ($nextFieldEx.hasClass('PHONE_input')){
                    if($nextFieldEx.val().length != '18')
                    {
                        requiredErrorPosition = $nextFieldEx.offset().top;
                        requiredError = true;
                        $nextFieldEx.addClass('error');
                    }
                }
				if($nextFieldEx.attr("name")){
					if(!$nextFieldEx.val() || $nextFieldEx.val().length == 0){
						$nextFieldEx.addClass("error");
						if(!requiredError){
							requiredErrorPosition = $nextFieldEx.offset().top;
							requiredError = true;
						}
					}
				}

			}
		});

		var $personalInfo = $thisForm.find("#personalInfoFieldStatic");
		if(!$personalInfo.prop("checked")){
			$personalInfo.addClass("error");
			requiredError = true;
		}

		if(requiredError == false){
	  		$.ajax({
	  			url: webFormAjaxDir + "?action=scan_send&FORM_ID=" + formId + "&SITE_ID=" + webFormSiteId,
	  			data: formData,
			    cache: false,
		        contentType: false,
		        processData: false,
		        enctype: "multipart/form-data",
		        type: "POST" ,
		        dataType: "json",
	  			success: function(response){

	  				//remove error labels
	  				$thisFormErrors.empty().removeClass("visible");
	  				$webFormError.empty().removeClass("visible");

		  			if(response["SUCCESS"] != "Y"){

			  			//set errors
			  			$.each(response["ERROR"], function(nextId, nextValue){
			  				var $errorItemContainer = $("#WEB_FORM_ITEM_" + nextId);
			  				if(nextId != 0 && $errorItemContainer){
			  					$errorItemContainer.find(".webFormItemError").html(nextValue).addClass("visible");
			  				}else{
			  					$webFormError.append(nextValue).addClass("visible");
			  				}
			  			});

			  			// reload captcha
			  			if(response["CAPTCHA"]){
							$webFormCaptchaSid.val(response["CAPTCHA"]["CODE"]);
							$webFormCaptchaImage.attr("src", response["CAPTCHA"]["PICTURE"]);
						}

					}else{
						$("#webFormMessage_" + formId).show();
						$thisForm[0].reset();

						(function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)}; m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)}) (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym"); ym(30121774, "init", { clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true, ecommerce:"dataLayer" });

						ym(30121774, 'reachGoal', 'scan_click');

						var _tmr = window._tmr || (window._tmr = []);
						_tmr.push({"type":"reachGoal","id":3206755,"goal":"scan_click"});
						
						waitForFbq(function(){
							fbq('track', 'SubmitApplication', {content_name: 'scan'});
						});
						waitForVk(function(){
							VK.Goal('submit_application');
						});
						
					}

		  			//remove loader
		  			$thisForm.removeClass("loading");
		  			$submitButton.removeClass("loading");



		  		}

	  		});
	  	}else{

	  		if(requiredErrorPosition){
	  			$("html, body").animate({
	  				"scrollTop": requiredErrorPosition - $(window).height() / 2
	  			}, 250);
	  		}

	  		$thisForm.removeClass("loading");
	  		$submitButton.removeClass("loading");
	  	}

		return event.preventDefault();

	}

	var removeErrors = function(event){
		$(this).removeClass("error");
	};

	var webFormExit = function(event){
		$(".webFormMessage").hide();
		return event.preventDefault();
	}


	var delFirstEight = {
	  onKeyPress: function(val, e, field, options) {

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
	$(".form-phone").mask("+7 (999) 999-99-99", delFirstEight);


	$(document).on("focus", ".webFormItemField input, .webFormItemField select, .webFormItemField textarea", removeErrors);
	$(document).on("click", ".webFormMessageExit", webFormExit);
	$(document).on("submit", ".webFormDw form", sendWebForm);

	if ($('form[name="SCAN"]').length) {
		$scroll_to_form = 0;
		waitForYm(function () {
			$(window).scroll(function () {

				if ($(window).scrollTop() + $(window).height() > $('form[name="SCAN"]').offset().top && $scroll_to_form == 0) {
					ym(30121774, 'reachGoal', 'scroll_to_scanform');
					$scroll_to_form = 1;
				}
			});
		});
	}
});
