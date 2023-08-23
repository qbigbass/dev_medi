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

if ($arResult['arQuestions']['SKU'])
{
	$sku_field_id = $arResult['arAnswers']['SKU'][0]['ID'];
	?><input name="form_text_<?=$sku_field_id?>" type="hidden" value="<?=$offername?>"/><?
}

	foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion)
	{
		if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden')
		{
			echo $arQuestion["HTML_CODE"];
		}
		else
		{
            if (in_array($arQuestion['STRUCTURE'][0]['ID'], $arResult['HIDE_FIELDS'] )):
				continue;
            else:
				if ($FIELD_SID == 'CARD'):
					$field_name = "form_text_".$arQuestion['STRUCTURE'][0]['ID'];?>
				<div  >

					<div class="<?=$field_name?> form_error_wrap">
					<label for="<?=$field_name?>">Номер дисконтной карты (если есть):</label>
					<input type="text" id="<?=$field_name?>" class="<?=$FIELD_SID?>_input" required name="<?=$field_name?>" value="<?=(isset($_REQUEST[$field_name]) ? htmlspecialchars($_REQUEST[$field_name]) : '');?>">
					<p class="error_<?=$field_name?>"><span style="display:none">Проверьте введенный номер</span></p>
				</div></div>
				<?elseif ($FIELD_SID == 'ADDRESS'):
					$field_name = "form_text_".$arQuestion['STRUCTURE'][0]['ID'];?>
					<input type="hidden" id="address_input" name="<?=$field_name?>" value="<?=(isset($_REQUEST[$field_name]) ? htmlspecialchars($_REQUEST[$field_name]) : '');?>">

	            <?elseif ($FIELD_SID == "SALON"):?>
	            <div class="form_text_<?=$arQuestion['STRUCTURE'][0]['ID']?>">
					<label for="select_salon">Салон:</label>
	                <select name="form_text_<?=$arQuestion['STRUCTURE'][0]['ID']?>" id="select_salon" required="true">
	                    <?foreach($arResult['SALON'] as $k=>$salon):?>
	                    <option value="<?=(!empty($salon['METRO']['NAME']) ? htmlspecialchars($salon['METRO']['NAME'], ENT_QUOTES)  : $salon['ADDRESS']); ?>" data-id="<?=$salon['ID']?>" data-title="<?=$salon['DESCRIPTION']?>" data-worktime="<?=$salon['SCHEDULE']?>" data-address="<?=$salon['ADDRESS']?>" <?if (isset($_REQUEST['form_text_'.$FIELD_SID]) && $_REQUEST['form_text_'.$FIELD_SID] == $salon['METRO']['NAME']){?>selected="true"<?}elseif (isset($_REQUEST['s']) && $_REQUEST['s'] == $salon['ID']){?>selected="true"<?}?>>м. <?=$salon['METRO']['NAME']?></option>
	                    <?endforeach;?>
	                </select>
	                <div id="salon_reserve_desc">

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

				$field_name = "form_text_".$arQuestion['STRUCTURE'][0]['ID'];?>
				<div >
				<div class="<?=$field_name;?> form_error_wrap">

					<label for="<?=$field_name?>"><?=$arQuestion['CAPTION']?><?=($arQuestion['STRUCTURE'][0]['REQUIRE']=='Y' ? '*' : '');?>:</label>
					<input type="text"  id="<?=$field_name?>" <?=($arQuestion['STRUCTURE'][0]['REQUIRE']=='Y' ? 'required' : '');?> class="<?=($FIELD_SID == 'PHONE' ? 'phonemask' : '');?> <?=$FIELD_SID?>_input" name="<?=$field_name;?>" value="<?=(isset($_REQUEST[$field_name]) ? htmlspecialchars($_REQUEST[$field_name]) : '');?>">
					<p class="error_<?=$field_name?>"><span style="display:none">Поле  не заполнено</span></p>
				</div>
				</div>
            <?endif;?>
		<?endif;?>

	<?
		}
	} //endwhile
	?>
	<label class="label_SIMPLE_FORM_<?=$arResult['arForm']['ID']?> push" for="agree_SIMPLE_FORM_<?=$arResult['arForm']['ID']?>" style="max-width:300px;">
		<input id="agree_SIMPLE_FORM_<?=$arResult['arForm']['ID']?>" class="checkbox_SIMPLE_FORM_<?=$arResult['arForm']['ID']?>" value="<?=$arResult['arAnswers']['AGREE'][0]['ID']?>"  type="checkbox">
		<?=htmlspecialchars_decode($arResult['QUESTIONS']['AGREE']['CAPTION'])?>
		<p class="error_form_agree"><span style="display:none">Необходимо согласие</span></p>
		</label>
		<p class="error_form_text_1"></p>

		<a href="javascript:void(0);" class="<?=$arResult['arForm']['VARNAME']?>_send popup_submit_button"><?=$arResult['arForm']['BUTTON']?></a>

<script>

	$(".<?=$arResult['arForm']['VARNAME']?>_send").on("click",function() {
			$phone = $('.phonemask').val().replace(/\+77/,"+7");

			$('input.phonemask').val($phone);
			$phone = $('.phonemask').val();

			$card = $('input.CARD_input').val();

			$address = $('#address_input').val();

			var $error = 0;
			if ($("#agree_SIMPLE_FORM_<?=$arResult['arForm']['ID']?>").prop('checked') == false)
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
				$('input.phonemask').parent("div").addClass("reserve_form_error");

				$('input.phonemask').parent("div").find("span").show();
			}
			else
			{
				$('input.phonemask').parent("div").removeClass("reserve_form_error");
				$('input.phonemask').parent("div").find("span").hide();
			}

			if($card.length && ($card.length < 5 || $card.length > 7))
			{
				$error = 1;
				$("input.CARD_input").parent("div").addClass("reserve_form_error");
				$("input.CARD_input").parent("div").find("span").show();
			}
			else
			{
				$("input.CARD_input").parent("div").removeClass("reserve_form_error");
				$("input.CARD_input").parent("div").find("span").hide();
			}
			// FIO
			if($('input.FIO_input').val()=="" )
			{
				$error = 1;
				$("input.FIO_input").parent("div").addClass("reserve_form_error");
				$("input.FIO_input").parent("div").find("span").show();
			}
			else
			{
				$("input.FIO_input").parent("div").removeClass("reserve_form_error");
				$("input.FIO_input").parent("div").find("span").hide();
			}

			if ($error == 0)
			{
				$.ajax({
					url: '/ajax/catalog/',
					data: {
						action: "<?=$arResult['arForm']['VARNAME']?>_send",
						sname: $('input.FIO_input').val(),
						sphone: $('input.PHONE_input').val(),
						sprod: $('input[name=form_text_<?=$sku_field_id?>]').val(),
						sstore: $('#select_salon').val(),
						scard: $('input.CARD_input').val(),
						saddress: $address

					},
					method: 'POST',
					dataType: 'html',
					success: function(data){
						$(".medi_popup_Content").empty().html(data);

						/* TODO: установить цели аналитики
						ym(30121774, 'reachGoal', 'BOOKING');
						window.dataLayer = window.dataLayer || [];
					   dataLayer.push({
						'event': 'gtm-event',
						'gtm-event-category': 'Booking',
						'gtm-event-action': 'Click',
						'gtm-event-label': 'SendBooking',
						});*/
					},
					error: function(){
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


		if ($(".phonemask").length) {
			$(".phonemask").mask("+~0000000000", {'translation': {"~": {pattern: /[7|8]/}}, placeholder: "+7__________" });
			$(".phonemask").on("keyup", function(){
				$val = $(this).val();
				if ($val.length == 12){
					$fix = $val.replace(/\+8/, "+7");
					$(this).val($fix);
				}
			});
	    }

	});
	</script>

<?endif;?>
 </div>
