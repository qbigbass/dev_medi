<?
//include module
\Bitrix\Main\Loader::includeModule("dw.deluxe");

//get template settings
$arTemplateSettings = DwSettings::getInstance()->getCurrentSettings();

//check settings
if(!empty($arTemplateSettings)){

	//set params
	$arParams["USE_MASKED_INPUT"] = !empty($arTemplateSettings["TEMPLATE_USE_MASKED_INPUT"]) ? $arTemplateSettings["TEMPLATE_USE_MASKED_INPUT"] : "N";

	//get masked input format
	if($arParams["USE_MASKED_INPUT"] == "Y"){
		$arParams["MASKED_INPUT_FORMAT"] = !empty($arTemplateSettings["TEMPLATE_MASKED_INPUT_CUSTOM_FORMAT"]) ? $arTemplateSettings["TEMPLATE_MASKED_INPUT_CUSTOM_FORMAT"] : $arTemplateSettings["TEMPLATE_MASKED_INPUT_FORMAT"];
	}

}?>
<div id="appFastOrder" data-load="<?=SITE_TEMPLATE_PATH?>/images/picLoad.gif">
	<div id="appFastOrderContainer">
		<div class="heading">Заказать в 1 клик <a href="#" class="close closeWindow"></a></div>

			<p class="FastOrder-desc ff-medium">Заполните форму быстрого заказа. Наш специалист свяжется с Вами для уточнения деталей заказа.</p>
		<div class="container" id="FastOrderOpenContainer">
			<div class="column">
				<div id="FastOrderPicture"><a href="#" class="url"><img src="<?=SITE_TEMPLATE_PATH?>/images/picLoad.gif" alt="" class="picture"></a></div>
				<div id="FastOrderName"><a href="" class="name url"><span class="middle"></span></a></div>
				<div id="FastOrderPrice" class="price"></div>
			</div>
			<div class="column">
				<form action="<?=SITE_DIR?>callback/" id="FastOrderForm" method="GET" >
					<input name="id" type="hidden" id="FastOrderFormId" value="">
					<input name="act" type="hidden" id="FastOrderFormAct" value="fastOrder">
					<input name="SITE_ID" type="hidden" id="FastOrderFormSiteId" value="<?=SITE_ID?>">
					<div class="formLine"><input name="name" type="text" placeholder="Имя*" value="" id="FastOrderFormName"></div>
					<div class="formLine"><input name="phone" type="tel" placeholder="Телефон*" value="" id="FastOrderFormTelephone"></div>
					<div class="formLine"><textarea name="message" cols="30" rows="10" placeholder="Комментарий к заказу" id="FastOrderFormMessage"></textarea></div>
					<div class="formLine"><input type="checkbox" name="personalInfoFastOrder" id="personalInfoFastOrder"><label for="personalInfoFastOrder">Я соглашаюсь с <a href="/legality/policy/" class="pilink" target=_blank>Политикой в отношении обработки персональных данных</a>*</label></div>
					<div class="formLine"><a href="#" class="send_fastorder" id="GTM_fastorder_card_send" ><img src="<?=SITE_TEMPLATE_PATH?>/images/incart.png" alt="Заказать" >Заказать</a></div>
				</form>
			</div>
		</div>
		<div id="FastOrderResult">
			<div id="FastOrderResultTitle"></div>
			<div id="FastOrderResultMessage"></div>
			<a href="" id="FastOrderResultClose" class="closeWindow">Закрыть окно</a>
        </div>
        <?if(!empty($arParams["USE_MASKED_INPUT"])):?>
			<script>
				$(function(){
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
					$("#FastOrderFormTelephone").mask("<?=$arParams["MASKED_INPUT_FORMAT"]?>", delFirstEight);
				});
			</script>
		<?endif;?>
	</div>
</div>
