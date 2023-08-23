var scoder_modalcoupon;
var scoder_modalcoupon_answer;

function sc_get_cookie(name) {
	var matches = document.cookie.match(new RegExp(
	"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
	));
	return matches ? decodeURIComponent(matches[1]) : undefined;
}
function sc_set_cookie(name, value, options) {
	options = options || {};

	var expires = options.expires;

	if (typeof expires == "number" && expires) {
		var d = new Date();
		d.setTime(d.getTime() + expires * 1000);
		expires = options.expires = d;
	}
	if (expires && expires.toUTCString) {
		options.expires = expires.toUTCString();
	}

	value = encodeURIComponent(value);

	var updatedCookie = name + "=" + value;

	for (var propName in options) {
		updatedCookie += "; " + propName;
		var propValue = options[propName];
		if (propValue !== true) {
			updatedCookie += "=" + propValue;
		}
	}

	document.cookie = updatedCookie;
}
function sc_delete_cookie(name) {
	setCookie(name, "", {
		expires: -1
	})
}
function sc_gift_modal_show()
{
	scoder_modalcoupon.show();
}
function sc_gift_modal_close()
{
	//записать в куки, что окно закрыто и не нужно обновлять
	BX.ajax({
		url: '/bitrix/components/scoder/modalcoupon/ajax.api.php?'+BX.message('bitrix_sessid_get')+'&TIMECLOSE='+parseInt(BX.message('TIMECLOSE')),
		method: 'POST',
		data: {'action':"SC_CLOSE"},
		onsuccess: function(data){
			sc_set_cookie(
				'SC_CLOSE_COUPON',
				'Y',
				{
					expires: parseInt(BX.message('TIMECLOSE'))
				}
			);		//пишем в куки
		},
		onfailure: function(){
			alert(BX.message('SCODER_ERROR'));
		}
	});
}
BX.ready(function(){
	scoder_modalcoupon = new BX.PopupWindow("scoder_modalcoupon_popup", null, {
		autoHide : (BX.message('AUTO_HIDE')=="Y"?true:false),
		content: BX('sc-modalcoupon-form'),
		zIndex: 0,
		offsetLeft: 0,
		offsetTop: 0,
		closeByEsc : true,
		overlay: {
			 backgroundColor: '#aaa', opacity: '0.7'
		  },
		draggable: {restrict: false},
		events: {
			onAfterPopupShow: function () {
				$('.sc-toggle').hide();
			},
			onPopupClose: function(){			//обработчик события закрытия модального окна
				$('.sc-toggle').show();
				sc_gift_modal_close();
			}
		},
	}); 
	scoder_modalcoupon_answer = new BX.PopupWindow("scoder_modalcoupon_answer_popup", null, {
		autoHide : (BX.message('AUTO_HIDE')=="Y"?true:false),
		content: BX('sc-modal-answer'),
		zIndex: 0,
		offsetLeft: 0,
		offsetTop: 0,
		closeByEsc : true,
		overlay: {
			 backgroundColor: '#aaa', opacity: '0.7'
		},
		draggable: {restrict: false},
		events: {
			onAfterPopupShow: function () {
				$('.sc-toggle').hide();
				sc_set_cookie(
					'SC_YEAR_HIDE',
					'Y',
					{
						expires:62208000
					}
				);	
			},
		},
	}); 
	
	var is_userconsent = true;
	$(document).on('submit', '#sc-coupon-form', function(e) {
		var msg = $(this).serialize();

		$('.sc-errorMsg').html('').hide();
		
		if (BX.message('USER_CONSENT') == 'Y')
		{
			is_userconsent = false;
			var sc_control = BX.UserConsent.load(BX('sc-coupon-form'));			//проверка согласия
			if (!sc_control)
			{
				$('.sc-errorMsg').show().html(BX.message('SCODER_ERROR'));
			}
			//если согласен
			if ($('input[name="sc_modalcoupon_userconsent_input"]').prop( "checked" ))
				is_userconsent = true;
		}
		
		if (is_userconsent)
		{
			$('.sc-errorMsg').html('').hide();
			BX.ajax({
				url: '/bitrix/components/scoder/modalcoupon/ajax.api.php?USE_CAPTCHA='+BX.message('USE_CAPTCHA'),
				method: 'POST',
				data: msg,
				onsuccess: function(data){
					
					var obj = jQuery.parseJSON(data);
					if (obj.RESULT == 'FIND')
					{
						$('.sc-errorMsg').show().html(BX.message('SCODER_ERROR_EMAIL'));
					}
					else if (obj.RESULT == 'SUCCES')		//если создан успешно
					{
						sc_gift_modal_close();
						scoder_modalcoupon.close();
						scoder_modalcoupon_answer.show();	//показать ответ
					}
					else if (obj.RESULT == 'ERROR')
					{
						$('.sc-errorMsg').show().html(obj.TEXT);
					}
					
				},
				onfailure: function(){
					$('.sc-errorMsg').show().html(BX.message('SCODER_ERROR'));
				}
			});
		}
		else
		{
			$('.sc-errorMsg').show().html(BX.message('SCODER_ERROR_USERCONSENT'));
			
		}
		
		return false;
	});
	
	//закрыли иконку
	$(document).on('click', '#sc-icon-close', function(e) {
		$('.sc-toggle').hide();
		//если указано "Не отображать иконку после закрытия"
		if (BX.message('NOT_SHOW_ICON_AFTER_CLOSE')=='Y')
		{
			sc_set_cookie(
				'SC_NOT_SHOW_ICON_AFTER_CLOSE',
				'Y',
				{
					expires:62208000
				}
			);
		}
		return false;
	}); 
	//нажали на подарок
	$(document).on('click', '.sc-toggle', function(e) {
		sc_gift_modal_show();
	}); 
	//закрыть окно ответа
	$(document).on('click', '#sc-button-close', function(e) {
		scoder_modalcoupon_answer.close();
		if (BX.message('IS_RELOAD_WINDOW')=='Y')
		{
			location.reload();
		}
		
	}); 
	
	//если купон не закрыт
	if (sc_get_cookie('SC_CLOSE_COUPON') != 'Y'
		&& sc_get_cookie('SC_YEAR_HIDE') !='Y'
			&& BX.message('IS_CLOSED')!="Y")
	{
		setTimeout(sc_gift_modal_show, parseInt(BX.message('TIMEOUT')));
		$('.sc-toggle').hide();		//скрываем иконку
	}

	$('.sc-captcha-update').on('click', function(e) {
		$('#whiteBlock').show();
		BX.ajax({
			url: '/bitrix/components/scoder/modalcoupon/ajax.api.php?',
			method: 'POST',
			data: {'action':"SC_UPDATE_CAPTCHA", "sessid": $('#sessid').val()},
			onsuccess: function(data){
				$('#captchaImg').attr('src','/bitrix/tools/captcha.php?captcha_sid='+data);
	            $('#captchaSid').val(data);
	            $('#whiteBlock').hide();
			},
			onfailure: function(){
				alert(BX.message('SCODER_ERROR'));
			}
		});
	}); 

	moveModal();
	$( window ).resize(function() {
		moveModal();
	});
	if (sc_get_cookie('SC_NOT_SHOW_ICON_AFTER_CLOSE') == 'Y')
	{
		$('.sc-toggle').hide();
	}
}); 

function moveModal() {
	$('#scoder_modalcoupon_popup').css('margin-top', $('.bx-logo').outerHeight()-1+'px');
	// $('#scoder_modalcoupon_popup').css('margin-top', $('.bx-logo').outerHeight()-1+'px');
}