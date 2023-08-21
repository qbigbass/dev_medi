<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/form.style.css", true);?>
<div class="form_reserve">

<?if (empty($arResult['SALON'])):
?>
	<p>К сожалению, выбранного Вами товара нет в наличии. </p>
<?
else:
?>
<?if ($arResult["isFormErrors"] == "Y"):?>
    <div class="error alert">
    <?=$arResult["FORM_ERRORS_TEXT"]?>
    </div>
<?else:
if (!empty($arResult["FORM_NOTE"])):?>
    <div class="succes alert">
    <?=$arResult["FORM_NOTE"]?>
    </div>

	<script>fbq('track', 'Schedule');</script>
    <?endif;?>
<?endif;?>

<?
if ($arResult["isFormDescription"] == "Y" || $arResult["isFormTitle"] == "Y" || $arResult["isFormImage"] == "Y")
{
?>
<?/***********************************************************************************
					form header
***********************************************************************************/?>

	<div class="form_description"><p><?=$arResult["FORM_DESCRIPTION"]?></p></div>

<?
}
/***********************************************************************************
						form questions
***********************************************************************************/
$offername = htmlspecialchars($arResult['OFFER']['NAME'], ENT_QUOTES);
?>

	<input name="form_text_19" type="hidden" value="<?=$offername?>" data-price='<?=$arResult['OFFER']['CATALOG_PRICE_1']?>'/>


    <?
	foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion)
	{
		if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden')
		{
			echo $arQuestion["HTML_CODE"];
		}
		else
		{
	?>

            <?if (in_array($arQuestion['STRUCTURE'][0]['ID'], array(19, 22) )):
			continue;?>
            <?else:?>
            <?if ($arQuestion['STRUCTURE'][0]['ID'] == 16):?>
			<div >
			<div class="form_text_16 form_error_wrap">
				<input type="text" required placeholder="Ваше имя*" name="form_text_16" id="form_text_16" value="<?=(isset($_REQUEST['form_text_16']) ? htmlspecialchars($_REQUEST['form_text_16']) : '');?>">
				<p class="error_form_text_16"><span style="display:none">Поле  не заполнено</span></p>
			</div>
			</div>
            <?elseif ($arQuestion['STRUCTURE'][0]['ID'] == 17):?>
			<div  >
				<div class="form_text_17 form_error_wrap">
					<input type="text" placeholder="Телефон*"  required name="form_text_17" value="<?=(isset($_REQUEST['form_text_17']) ? htmlspecialchars($_REQUEST['form_text_17']) : '');?>">
				<p class="error_form_text_17 webFormItemError"><span style="display:none">Поле не заполнено</span></p>
			</div></div>
			<?elseif ($arQuestion['STRUCTURE'][0]['ID'] == 18):?>
			<div  >
				<div class="form_text_18 form_error_wrap">
				<input type="text"  id="form_text_18" placeholder="Номер дисконтной карты (если есть)" name="form_text_18" value="<?=(isset($_REQUEST['form_text_18']) ? htmlspecialchars($_REQUEST['form_text_18']) : '');?>">

				<p class="error_form_text_18 webFormItemError"><span style="display:none">Проверьте введенный номер</span></p>
			</div></div>
			<?elseif ($arQuestion['STRUCTURE'][0]['ID'] == 21):?>

			<input type="hidden" id="address_input" name="form_text_21" value="<?=(isset($_REQUEST['form_text_21']) ? htmlspecialchars($_REQUEST['form_text_21']) : '');?>">

            <?elseif ($arQuestion['STRUCTURE'][0]['ID'] == 20):?>
            <div class="form_text_20">
				<label for="select_salon">Салон:</label>
                <select name="form_text_20" id="select_salon" required="true">
                    <?foreach($arResult['SALON'] as $k=>$salon):?>
                    <option value="<?=(!empty($salon['METRO']['NAME']) ? htmlspecialchars($salon['METRO']['NAME'], ENT_QUOTES) : htmlspecialchars($salon['ADDRESS'], ENT_QUOTES) ) ?>" data-id="<?=$salon['ID']?>" data-title="<?=$salon['DESCRIPTION']?>"
						data-worktime="<?=$salon['SCHEDULE']?>" data-address="<?=$salon['ADDRESS']?>" <?if (isset($_REQUEST['form_text_20']) && $_REQUEST['form_text_20'] == $salon['METRO']['NAME']){?>selected="true"<?}elseif (isset($_REQUEST['s']) && $_REQUEST['s'] == $salon['ID']){?>selected="true"<?}?>><?=(!empty($salon['METRO']['NAME']) ? 'м. '.htmlspecialchars($salon['METRO']['NAME'], ENT_QUOTES) : htmlspecialchars($salon['ADDRESS'], ENT_QUOTES) ) ?></option>
                    <?endforeach;?>
                </select>
                <div id="salon_reserve_desc" >

                    <div class="salon-contacts">
                        <span id="salon-adres">ул. Дмитрия Ульянова, 16, к. 1 </span>
                    </div>
                    <div class="salon-shedule">
                        <span id="salon-shedule">ежедневно с 9:00 до 21:00</span>
                    </div>


                </div>
                <script>
                $(document).ready(function() {


                    $("#select_salon").on("change", set_salon_info);

                    function set_salon_info(){
                        $salon = $("#select_salon option:selected");
                        $salon_address = $salon.attr("data-address");
                        $salon_title = $salon.attr("data-title");
                        $salon_worktime = $salon.attr("data-worktime");

                        $salon_info = '<div class="salon-contacts"><span id="salon-adres">' + $salon_address
                         + '</span></div><div class="salon-shedule"><span id="salon-shedule">' + $salon_worktime + '</span>';

						$("#address_input").val($salon_address);

                        $("#salon_reserve_desc").html($salon_info);
                    }

                    set_salon_info();
                });
                </script>
            </div>
            <?
            else:
                        ?>
			<td class="td1">
				<?if (is_array($arResult["FORM_ERRORS"]) && array_key_exists($FIELD_SID, $arResult['FORM_ERRORS'])):?>
				<span class="error-fld" title="<?=htmlspecialcharsbx($arResult["FORM_ERRORS"][$FIELD_SID])?>"></span>
				<?endif;?>
				<label for="form_<?=strtolower($arQuestion['STRUCTURE'][0]['FIELD_TYPE']);?>_<?=$arQuestion['STRUCTURE'][0]['ID']?>"><?=$arQuestion["CAPTION"]?></label><?if ($arQuestion["REQUIRED"] == "Y"):?><?=$arResult["REQUIRED_SIGN"];?><?endif;?>
				<?=$arQuestion["IS_INPUT_CAPTION_IMAGE"] == "Y" ? "<br />".$arQuestion["IMAGE"]["HTML_CODE"] : ""?>
			</td>
			<td class="td2">
            <?=$arQuestion["HTML_CODE"]?></td>
            <?endif;?>
            <?endif;?>
		</tr>
	<?
		}
	} //endwhile
	?>
	<label class="label_SIMPLE_FORM_4 push" for="agree_SIMPLE_FORM_4" style="max-width:300px;">
		<input id="agree_SIMPLE_FORM_4" class="checkbox_SIMPLE_FORM_4" value="22"  type="checkbox" >
		<?=htmlspecialchars_decode($arResult['QUESTIONS']['AGREE']['CAPTION'])?>
		<p class="error_form_agree"><span style="display:none">Необходимо согласие</span></p>
		</label>
		<p class="error_form_text_1"></p>

		<script>
			var $goalParams = {'<?=$offername?>' : '<?=$arResult['OFFER']['CATALOG_PRICE_2']?>'};
		</script>

		<a href="javascript:void(0);" class="reserve_send" id="GTM_booking_button" >Забронировать</a>

<script>

$(".reserve_send").on("click",function() {
		$phone = $('input[name=form_text_17]').val().replace(/\+77/,"+7");
		$('input[name=form_text_17]').val($phone);
		$phone = $('input[name=form_text_17]').val();
		$card = $('input[name=form_text_18]').val();
		$address = $('input[name=form_text_21]').val();
		var $error = 0;
		if ($("#agree_SIMPLE_FORM_4").prop('checked') == false)
		{
			$(".error_form_agree span").show();
			$error = 1;
		}
		else {
			$(".error_form_agree span").hide();
		}
		if($phone == "" || $phone.replace(/\_/g, "").length < 12)
		{
			$error = 1;
			$(".form_text_17").addClass("reserve_form_error");
			$(".form_text_17").find("span").show();
		}
		else
		{
			$(".form_text_17").removeClass("reserve_form_error");
			$(".form_text_17").find("span").hide();
		}

		if($card != "" && ($card.length < 5 || $card.length > 7))
		{
			$error = 1;
			$(".form_text_18").addClass("reserve_form_error");
			$(".form_text_18").find("span").show();
		}
		else
		{
			$(".form_text_18").removeClass("reserve_form_error");
			$(".form_text_18").find("span").hide();
		}
		// FIO
		if($('input[name=form_text_16]').val()=="" )
		{
			$error = 1;
			$(".form_text_16").addClass("reserve_form_error");
			$(".form_text_16").find("span").show();
		}
		else
		{
			$(".form_text_16").removeClass("reserve_form_error");
			$(".form_text_16").find("span").hide();
		}

		if ($error == 0)
		{
			BX.ajax({
				url: '/ajax/catalog/',
				data: {
					action: "reserve_send",
					sname: $('input[name=form_text_16]').val(),
					sphone: $('input[name=form_text_17]').val(),
					sprod: $('input[name=form_text_19]').val(),
					sstore: $('select[name=form_text_20]').val(),
					scard: $('input[name=form_text_18]').val(),
					saddress: $address

				},
				method: 'POST',
				dataType: 'html',
				onsuccess: function(data){
					$(".medi_popup_Content").empty();
					$(".medi_popup_Content").html(data);
					/* TODO: установить цели аналитики*/

					ym(30121774, 'reachGoal', 'BOOKING', {'<?=$offername?>' : '<?=$arResult['OFFER']['CATALOG_PRICE_1']?>'});


                    var _tmr = window._tmr || (window._tmr = []);
                    _tmr.push({"type":"reachGoal","id":3206755,"goal":"purchase"});

				window.dataLayer = window.dataLayer || [];
				   dataLayer.push({
					'event': 'gtm-event',
					'gtm-event-category': 'Booking',
					'gtm-event-action': 'Click',
					'gtm-event-label': 'SendBooking',
					'gtm-event-item' : '<?=$offername?>',
					'gtm-event-item-price' : '<?=$arResult['OFFER']['CATALOG_PRICE_1']?>'
				});

				window.dataLayer = window.dataLayer || [];
                dataLayer.push({
                'ecommerce': {
                  'currencyCode': 'RUB',
                  'purchase': {
                    'actionField': {'id': 'booking<?=date(dmYHi)?>', 'coupon':'', 'affiliation':'Бронирование товара'},
                    'products': [{
						'name': '<?=$offername?>',
						 'id': '<?=$arResult['OFFER']['ID']?>',
						 'price': '<?=$arResult['OFFER']['CATALOG_PRICE_1']?>',
						 'brand': '<?=$arResult['OFFER']['PROPERTY_ATT_BRAND_NAME']?>',
						 'category': '<?=$arResult['OFFER']['CATEGORY']?>',
						 'variant': '<?=$arResult['OFFER']['PROPERTY_CML2_ARTICLE_VALUE']?>',
						 'quantity': 1
                    }]
                  }
                },
                'event': 'gtm-ee-event',
                'gtm-ee-event-category': 'Enhanced Ecommerce',
                'gtm-ee-event-action': 'Purchase',
                'gtm-ee-event-non-interaction': 'False',
                });

				},
				onfailure: function(){

					$(".medi_popup_Content").empty();
					$(".medi_popup_Content").html("<p>Произошла ошибка, попробуйте ещё раз.</p>");
				}
			});
		}

	return false;
	});
</script>


<?=$arResult["FORM_FOOTER"]?>
<script>
$(document).ready(function() {

	$("input[name='form_text_17']").on("change", function(){
		if ($(this).val() != ""){

			$(".form_text_17").removeClass("reserve_form_error");
			$(".form_text_17").find("span").hide();
		}
	});
	$("input[name='form_text_16']").on("change", function(){
		if ($(this).val() != ""){

			$(".form_text_16").removeClass("reserve_form_error");
			$(".form_text_16").find("span").hide();
		}
	});

	if ($("input[name='form_text_17']").length) {

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


		$("input[name='form_text_17']").mask("+7 (999) 999-99-99", delFirstEight);

    }

});
</script>


<?endif;?>
 </div>
