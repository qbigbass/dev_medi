function fbq (){
    return;
}

function showLoader()
{
    $('body').css({'overflow' : 'hidden'});
    $('body .loader:first').show();
    $('body .overlay:first').show();
}
function hideLoader()
{
    $('body').css({'overflow' : ''});
    $('body .loader:first').hide();
    $('body .overlay:first').hide();
}

function waitForFbq(callback){
    return true;
    if(typeof fbq !== 'undefined'){
        callback()
    } else {
        setTimeout(function () {
            waitForFbq(callback)
        }, 100)
    }
}
function waitForVk(callback){
    if(typeof VK !== 'undefined'){
        callback()
    } else {
        setTimeout(function () {
            waitForVk(callback)
        }, 100)
    }
}
function waitForGtag(callback){
    if(typeof gtag !== 'undefined'){
        callback()
    } else {
        setTimeout(function () {
            waitForGtag(callback)
        }, 100)
    }
}
function waitForYm(callback){
    if(typeof ym !== 'undefined'){
        callback()
    } else {
        setTimeout(function () {
            waitForYm(callback)
        }, 100)
    }
}
window.onload=function(){if($("#medi-openning-more-button").length){$("#medi-openning-more-button").click(function(){$(this).data("status")?($(this).html("Подробнее"),$(this).data("status",!1)):($(this).html("Скрыть"),$(this).data("status",!0))});var t=document.querySelector("#medi-openning-more-button"),o=!0;document.body.addEventListener("click",function(e){t.contains(e.target)&&(o=o?(document.getElementsByClassName("medi-openning-shadow-block")[0].classList.add("is-active"),!1):(document.getElementsByClassName("medi-openning-shadow-block is-active")[0].classList.remove("is-active"),!0))})}}

window.onload=function(){
    if($("#dots").length){
var dotsArray = document.getElementsByClassName("dot");
    var modsArray = document.getElementsByClassName("header-content");
    let parentBlock = document.getElementById("dots");
    var selectedElement;
    var i;
    parentBlock.onclick = function(event) {
        let target = event.target;
        while (target != this) {
            if (target.className == 'dot') {
                highlightNodes(target);
                return;
            }
            if (target.className == 'header-content') {
                highlightMods(target);
                return;
            }
            target = target.parentNode;
        }
    }
    function highlightNodes(node) {
        if (selectedElement) {
            var elementsArray = [dotsArray[i], modsArray[i]];
            for (let count in elementsArray)
            elementsArray[count].classList.remove('is-active');
        }
        selectedElement = node;
        for (let key in dotsArray)
        if (dotsArray[key] == node) i = [key];
        var elementsArray = [dotsArray[i], modsArray[i]];
        for (let count in elementsArray)
        elementsArray[count].classList.add('is-active');
    }

    function highlightMods(node) {
        if (selectedElement) {
            var elementsArray = [dotsArray[i], modsArray[i]];
            for (let count in elementsArray)
            elementsArray[count].classList.remove('is-active');
        }
        selectedElement = node;
        for (let key in modsArray)
        if (modsArray[key] == node) i = [key];
        var elementsArray = [dotsArray[i], modsArray[i]];
        for (let count in elementsArray)
        elementsArray[count].classList.add('is-active');
    }
}
}

$(document).ready(function() {

    $(".top_alert  .close").on("click", function(){
        $(".top_alert").slideUp();
            $.ajax({
                url: '/ajax/salon/?action=hide_alert&id='+$(this).data("id"),
                success:  function(data) {

                }
            });
    });

     hideLoader();
	 if ($('#bg-layer-for-tooltip').length){
		 $('#bg-layer-for-tooltip').hide();
	 }

     $("#footer_small .heading").on("click", function(){
        $(this).parents(".column").children(".footerMenu").toggle();
     });

    //$("#stick_col").sticky({topSpacing:0, bottomSpacing: 503,className:"sticked"});

});

function focusClick() {
    $('.tooltip-text-hover').each(function(){
        $(this).attr('style', 'opacity:0;transition:0.3s;');
    });
}
function focusOutClick() {

    $('.tooltip-text-hover').each(function(){
        $(this).attr('style', '');
    });
}
function closeTooltip() {
	$(".tooltip").blur();
}

$(function(){

    if ($('.tooltip').length){

        $('.tooltip').focus(function(){
          $('#bg-layer-for-tooltip').fadeIn("300");
        });
        $('.tooltip').focusout(function(){
          $('#bg-layer-for-tooltip').fadeOut("300");
        });


    }

	var sendWebForm = function(event, data){

		var formData = new FormData(this);
		var requiredErrorPosition = false;
		var requiredError = false;


		var $thisForm = $(this);//.addClass("loading");

        showLoader();
		var $parentThis = $thisForm.parents(".form_reserve.scan");
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
                    if($nextFieldEx.attr("name") == 'form_text_34' && $nextFieldEx.val().length < 18){
						$nextFieldEx.addClass("error");
						if(!requiredError){
							requiredErrorPosition = $nextFieldEx.offset().top;
							requiredError = true;
						}
					}
                    if($nextFieldEx.attr("name") == 'form_text_86' && $nextFieldEx.val().length < 18){
						$nextFieldEx.addClass("error");
						if(!requiredError){
							requiredErrorPosition = $nextFieldEx.offset().top;
							requiredError = true;
						}
					}
                    if($nextFieldEx.attr("name") == 'form_text_116' && $nextFieldEx.val().length < 18){
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
	  			url: webFormAjaxDir + "?action=scan_send&SITE_ID=" + webFormSiteId,
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
						$("#webFormMessage_"+$("input[name='WEB_FORM_ID']").val()).css({
							display: "block"
						});
						$thisForm[0].reset();
                        if (response['RES']['WEB_FORM_NAME'] == 'PODOLOG'){
                            window.dataLayer = window.dataLayer || [];
                           dataLayer.push({
                            'event': 'gtm-event',
                            'gtm-event-category': 'Event',
                            'gtm-event-action': 'Click',
                            'gtm-event-label': 'PodologBut',
                            });
    						ym(30121774, 'reachGoal', 'podolog_click');
                        }
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
	$(".phonemask").mask("+7 (999) 999-99-99", delFirstEight);

    $("#WEB_FORM_ITEM_TELEPHONE input").mask("+7 (999) 999-99-99", delFirstEight);

	$(document).on("focus", ".webFormItemField input, .webFormItemField select, .webFormItemField textarea", removeErrors);
	$(document).on("click", ".webFormMessageExit", webFormExit);
	$(document).on("submit", ".form_reserve.scan form", sendWebForm);

    if ($(".addphonemask").length){
        document.addEventListener("DOMContentLoaded", function() {
            var input = document.querySelector(".addphonemask");

            input.addEventListener("input", amask);
            input.addEventListener("focus", amask);
            input.addEventListener("blur", amask);

            function amask(event) {
                var blank = "+7 (___) ___-__-__";

                var i = 0;
                var val = this.value.replace(/\D/g, "").replace(/^8/, "7").replace(/^9/, "79");
                if (val.replace(/\D/g, '').length===1)
                {
                    val = '7';
                }
                if (val.replace(/\D/g, '').length===2)
                {
                    val = val.replace('78','7');
                }

                this.value = blank.replace(/./g, function(char) {
                    if (/[_\d]/.test(char) && i < val.length) return val.charAt(i++);
                    return i >= val.length ? "" : char;
                });

                if (event.type == "blur") {
                    if (this.value.length == 2) this.value = "";
                } else {
                    setCursorPosition(this, this.value.length);
                }
            };

            /***/
            function setCursorPosition(elem, pos) {
                elem.focus();

                if (elem.setSelectionRange) {
                    elem.setSelectionRange(pos, pos);
                    return;
                }

                if (elem.createTextRange) {
                    var range = elem.createTextRange();
                    range.collapse(true);
                    range.moveEnd("character", pos);
                    range.moveStart("character", pos);
                    range.select();
                    return;
                }
            }
        });
    }

});
