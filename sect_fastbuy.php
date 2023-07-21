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
<div id="appFastBuy" data-load="<?=SITE_TEMPLATE_PATH?>/images/picLoad.gif">
	<div id="appFastBuyContainer">
		<div class="heading">Запрос заказного изделия <a href="#" class="close closeWindow"></a></div>

			<p class="FastBuy-desc ff-medium">Заполните форму заказа изделия. Наш специалист свяжется с Вами для уточнения деталей заказа.</p>
		<div class="container" id="fastBuyOpenContainer">
			<div class="column">
				<div id="fastBuyPicture"><a href="#" class="url"><img src="<?=SITE_TEMPLATE_PATH?>/images/picLoad.gif" alt="" class="picture"></a></div>
				<div id="fastBuyName"><a href="" class="name url"><span class="middle"></span></a></div>
				<div id="fastBuyPrice" class="price"></div>
			</div>
			<div class="column">
				<form action="<?=SITE_DIR?>callback/" id="fastBuyForm" method="GET">
					<input name="id" type="hidden" id="fastBuyFormId" value="">
					<input name="act" type="hidden" id="fastBuyFormAct" value="fastBack">
					<input name="SITE_ID" type="hidden" id="fastBuyFormSiteId" value="<?=SITE_ID?>">
					<div class="formLine"><input name="name" type="text" placeholder="Имя*" value="" id="fastBuyFormName"></div>
					<div class="formLine"><input name="phone" type="tel" placeholder="Телефон*" value="" id="fastBuyFormTelephone"></div>
					<div class="formLine"><textarea name="message" cols="30" rows="10" placeholder="Комментарий к заказу" id="fastBuyFormMessage"></textarea></div>
					<div class="formLine"><input type="checkbox" name="personalInfoFastBuy" id="personalInfoFastBuy"><label for="personalInfoFastBuy">Я соглашаюсь с <a href="/legality/policy/" class="pilink" target=_blank>Политикой в отношении обработки персональных данных</a>*</label></div>
					<div class="formLine"><?/*<a href="#" id="GTM_ordering_order" ><img src="<?=SITE_TEMPLATE_PATH?>/images/incart.png" alt="Заказать" > Заказать</a>*/?><b>Заказ временно не доступен.</b></div>
				</form>
			</div>
		</div>
		<div id="fastBuyResult">
			<div id="fastBuyResultTitle"></div>
			<div id="fastBuyResultMessage"></div>
			<a href="" id="fastBuyResultClose" class="closeWindow">Закрыть окно</a>
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
					$("#fastBuyFormTelephone").mask("<?=$arParams["MASKED_INPUT_FORMAT"]?>", delFirstEight);
				});
			</script>
		<?endif;?>
	</div>
</div>
