
$(function(){

    var sendWebFormCareer = function(event, data){

		var formData = new FormData(this);
		var requiredErrorPosition = false;
		var requiredError = false;


		var $thisForm = $(this);//.addClass("loading");

        showLoader();
		var $parentThis = $thisForm.parents("#webForm");
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
				var $nextFieldEx = $nextField.find('input[type="text"], input[type="tel"], input[type="password"], input[type="file"], select, textarea');
				if($nextFieldEx.attr("name")){
					if(!$nextFieldEx.val() || $nextFieldEx.val().length == 0){
						$nextFieldEx.addClass("error");
						if(!requiredError){
							requiredErrorPosition = $nextFieldEx.offset().top;
							requiredError = true;
						}
					}
                    if($nextFieldEx.attr("name") == 'form_text_216' && $nextFieldEx.val().length < 18){
						$nextFieldEx.addClass("error");
						if(!requiredError){
							requiredErrorPosition = $nextFieldEx.offset().top;
							requiredError = true;
						}
					}
                    if($nextFieldEx.attr("name") == 'form_text_242' && $nextFieldEx.val().length < 18){
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
	  			url: webFormAjaxDir + "?action=form_send&SITE_ID=" + webFormSiteId,
	  			data: formData,
			    cache: false,
		        contentType: false,
		        processData: false,
		        //enctype: "multipart/form-data",
		        type: "POST" ,
		        dataType: "json",
	  			success: function(response){

	  				//remove error labels
	  				$thisFormErrors.empty().removeClass("visible");
	  				$webFormError.empty().removeClass("visible");

		  			if(response["SUCCESS"] != "Y"){

			  			//set errors
			  			$.each(response["ERROR"], function(nextId, nextValue){
                            console.log(nextId);console.log(nextValue);
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
						$("#webFormMessage_"+$("input[name='WEB_FORM_ID']").val()).css({
							display: "block"
						});
                        $("#webForm form").hide();

                    	$("#webForm.form-wrap").hide();
                        $(".webFormMessageDescription").html("Ваше сообщение успешно отправлено. Мы обязательно рассматриваем все обращения и возвращаемся с обратной связью.");
						$thisForm[0].reset();
                        hideLoader();
					}

		  			//remove loader
		  			$submitButton.removeClass("loading");
                    hideLoader();

		  		}

	  		});
	  	}else{

	  		if(requiredErrorPosition){
	  			$("html, body").animate({
	  				"scrollTop": requiredErrorPosition - $(window).height() / 2
	  			}, 250);
	  		}

            hideLoader();
	  		$submitButton.removeClass("loading");
	  	}

		return event.preventDefault();

	}

	var removeErrors = function(event){
		$(this).removeClass("error");
	};

	var webFormExit = function(event){
		$("#webForm.form-wrap").hide();
		return event.preventDefault();

	}
	var showCareerForm = function(event){
		$("#webForm.form-wrap").show();
        $("#webForm form").show();
        $(".webFormMessage").hide();
		return event.preventDefault();

	}

    $(document).on("focus", ".webFormItemField input, .webFormItemField select, .webFormItemField textarea", removeErrors);
    $(document).on("click", ".close.closeWindow", webFormExit);
    $(document).on("click", ".showCareerForm", showCareerForm);
    $(document).on("submit", "#webForm form", sendWebFormCareer);

});
