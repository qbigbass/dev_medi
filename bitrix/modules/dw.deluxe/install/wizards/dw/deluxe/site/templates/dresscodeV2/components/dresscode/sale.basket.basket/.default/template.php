<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?use Bitrix\Main\Localization\Loc;?>
<?$this->setFrameMode(false);?>

<?
	//check created order
	if(!empty($arResult["CONFIRM_ORDER"]) && $arResult["CONFIRM_ORDER"] == "Y"){
		//confirm page
		include_once($_SERVER["DOCUMENT_ROOT"]."/".$templateFolder."/confirm_template.php");
		return false;
	}
?>

<?
	$arBasketTemplates = array(
		"SQUARES" => array(
			"CHANGE_URL" => $APPLICATION->GetCurPageParam("basketView=squares", array("basketView")),
			"TEMPLATE_FILE" => "/include/basket_squares.php",
			"CLASS_NAME" => "squares"
		),
		"TABLE" => array(
			"CHANGE_URL" => $APPLICATION->GetCurPageParam("basketView=table", array("basketView")),
			"TEMPLATE_FILE" => "/include/basket_table.php",
			"CLASS_NAME" => "table"
		)
	);

	if(!empty($_GET["basketView"]) && !empty($arBasketTemplates[strtoupper($_GET["basketView"])])){
		setcookie("DW_BASKET_TEMPLATE", strtolower($_GET["basketView"]), time() + 3600000);
		$arBasketTemplates[strtoupper($_GET["basketView"])]["SELECTED"] = "Y";
		$_COOKIE["DW_BASKET_TEMPLATE"] = strtolower($_GET["basketView"]);
	}

	elseif(!empty($_COOKIE["DW_BASKET_TEMPLATE"])){
		$arBasketTemplates[strtoupper($_COOKIE["DW_BASKET_TEMPLATE"])]["SELECTED"] = "Y";
	}

	else{
		$arBasketTemplates[key($arBasketTemplates)]["SELECTED"] = "Y";
	}

?>

<?if(!empty($arResult["ITEMS"])):?>
	<?
		//vars
		$personTypeIndex = 0;
		$component = $this->getComponent();
		$countPos = 0;
	?>

	<div id="personalCart" class="DwBasket">
		<div id="basketTopLine">
			<div id="tabsControl">
				<div class="item"><?=Loc::getMessage("BASKET_TABS_ACTIONS")?></div>
				<div class="item"><a href="<?=SITE_DIR?>personal/cart/order/" id="scrollToOrder" class="orderMove selected"><?=Loc::getMessage("BASKET_TABS_ORDER_MAKE")?></a></div>
				<div class="item"><a href="<?=SITE_DIR?>catalog/"><?=Loc::getMessage("BASKET_TABS_CONTINUE")?></a></div>
				<div class="item"><a href="#" id="allClear" class="clearAllBasketItems"><?=Loc::getMessage("BASKET_TABS_CLEAR")?></a></div>
			</div>
			<?if(!empty($arBasketTemplates)):?>
				<div id="basketView">
						<div class="item">
							<span><?=Loc::getMessage("BASKET_VIEW_LABEL")?></span>
						</div>
					<?foreach ($arBasketTemplates as $arNextBasketTemplate):?>
						<div class="item">
							<a href="<?=$arNextBasketTemplate["CHANGE_URL"]?>" class="<?=$arNextBasketTemplate["CLASS_NAME"]?><?if($arNextBasketTemplate["SELECTED"] == "Y"):?> selected<?endif;?>"></a>
						</div>
					<?endforeach;?>
				</div>
			<?endif;?>
		</div>
		<div id="basketProductList">
			<?if(!empty($_COOKIE["DW_BASKET_TEMPLATE"]) && $_COOKIE["DW_BASKET_TEMPLATE"] == "table"):?>
				<?if(!empty($arBasketTemplates["TABLE"]["TEMPLATE_FILE"])):?>
					<?include($_SERVER["DOCUMENT_ROOT"].$templateFolder.$arBasketTemplates["TABLE"]["TEMPLATE_FILE"]);?>
				<?endif;?>
			<?else:?>
				<?if(!empty($arBasketTemplates["TABLE"]["TEMPLATE_FILE"])):?>
					<?include($_SERVER["DOCUMENT_ROOT"].$templateFolder.$arBasketTemplates["SQUARES"]["TEMPLATE_FILE"]);?>
				<?endif;?>
			<?endif;?>
		</div>
		<div class="orderLine">
			<div id="sum">
				<span class="label hd"><?=Loc::getMessage("TOTAL_QTY")?></span>
				<span class="price hd countItems"><?=$countPos?></span>
				<span class="label"><?=Loc::getMessage("TOTAL_SUM")?></span>
				<span class="price">
					<span class="basketSum"><?=FormatCurrency($arResult["BASKET_SUM"], $arResult["CURRENCY"]["CODE"]);?></span>
				</span>
			</div>
			<form id="coupon">
				<input placeholder="<?=Loc::getMessage("COUPON_LABEL")?>" name="user" class="couponField"><input type="submit" value="<?=Loc::getMessage("COUPON_ACTIVATE")?>" class="couponActivate">
			</form>
		</div>
		<div class="minimumPayment<?if(!empty($arResult["IS_MIN_ORDER_AMOUNT"])):?> hidden<?endif;?>">
			<div class="minimumPaymentLeft">
				<div class="paymentIcon"><img src="<?=$templateFolder?>/images/minOrder.png" alt="" title=""></div>
				<div class="paymentMessage">
					<div class="paymentMessageHeading">
						<?=Loc::getMessage("MINIMUM_PAYMENT_HEADING", array(
							"#MIN_PRICE_FORMATED#" => \DigitalWeb\Basket::formatPrice($arParams["MIN_SUM_TO_PAYMENT"]),
						))?>
					</div>
					<div class="paymentMessageText">
						<?=Loc::getMessage("MINIMUM_PAYMENT_TEXT")?>
					</div>
				</div>
			</div>
			<div class="minimumPaymentRight">
				<div class="paymentButtons">
					<div class="paymentButtonsMain">
						<a href="<?=SITE_DIR?>" class="btn-simple btn-small btn-border"><?=Loc::getMessage("MINIMUM_PAYMENT_MAIN_BUTTON")?></a>
					</div>
					<div class="paymentButtonsCatalog">
						<a href="<?=SITE_DIR?>catalog/" class="btn-simple btn-small"><?=Loc::getMessage("MINIMUM_PAYMENT_CATALOG_BUTTON")?></a>
					</div>
				</div>
			</div>
		</div>
		<div class="giftContainer">
			<?$APPLICATION->IncludeComponent("bitrix:sale.gift.basket", ".default", Array(
					"APPLIED_DISCOUNT_LIST" => $arResult["APPLIED_DISCOUNT_LIST"],
					"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
					"CONVERT_CURRENCY" => $arParams["GIFT_CONVERT_CURRENCY"],
					"FULL_DISCOUNT_LIST" => $arResult["FULL_DISCOUNT_LIST"],
					"PRODUCT_PRICE_CODE" => $arParams["PRODUCT_PRICE_CODE"],
					"CURRENCY_ID" => $arParams["GIFT_CURRENCY_ID"],
					"HIDE_MEASURES" => $arParams["HIDE_MEASURES"],
					"PAGE_ELEMENT_COUNT" => "12",
					"LINE_ELEMENT_COUNT" => "12",
					"CACHE_GROUPS" => "Y",
				),
				false
			);?>
		</div>
		<div id="order" class="DwBasketOrder orderContainer<?if(empty($arResult["IS_MIN_ORDER_AMOUNT"])):?> hidden<?endif;?>">
			<span class="title"><?=Loc::getMessage("ORDER_HEADING")?></span>
			<table class="personSelectContainer">
				<tr>
					<td>
						<span><?=Loc::getMessage("ORDER_PERSON")?></span>
					</td>
					<td>
						<?if(!empty($arResult["PERSON_TYPES"])):?>
							<label><?=Loc::getMessage("ORDER_PERSON_SELECT")?></label>
							<select id="personSelect" class="personSelect">
								<?foreach ($arResult["PERSON_TYPES"] as $arPersonType):?>
									<?if($arPersonType["ACTIVE"] === "Y"):?>
										<option value="<?=$arPersonType["ID"]?>" data-id="<?=$arPersonType["ID"]?>"><?=$arPersonType["NAME"]?></option>
									<?endif;?>
								<?endforeach;?>
							</select>
						<?endif;?>
					</td>
				</tr>
			</table>
			<?if(!empty($arResult["PERSON_TYPES"])):?>
				<?foreach($arResult["PERSON_TYPES"] as $arPersonType):?>
					<form id="orderForm_<?=$arPersonType["ID"]?>" enctype="multipart/form-data">
						<table class="orderAreas<?if($personTypeIndex == 0):?> active<?endif;?>" data-person-id="<?=$arPersonType["ID"]?>">
							<?if(!empty($arResult["PROPERTY_GROUPS"])):?>
								<?foreach($arResult["PROPERTY_GROUPS"] as $arGroup):?>
									<?if(\DigitalWeb\Basket::checkGroupProperties($arGroup["ID"], $arPersonType["ID"], $arResult["PROPERTIES"])):?>
										<tr>
											<td><span><?=$arGroup["NAME"]?></span></td>
											<td class="mainPropArea">
												<ul class="userProp">
													<?foreach($arResult["PROPERTIES"] as $arProperty){
														if($arProperty["PROPS_GROUP_ID"] == $arGroup["ID"] && $arProperty["PERSON_TYPE_ID"] == $arPersonType["ID"] && empty($arProperty["RELATION"])){
															printOrderPropertyHTML($arProperty, ' class="propItem"');
														}
													}?>
												</ul>
											</td>
										</tr>
									<?endif;?>
								<?endforeach;?>
							<?endif;?>
						</table>
						<table class="orderAreas<?if($personTypeIndex == 0):?> active<?endif;?>" data-person-id="<?=$arPersonType["ID"]?>">
							<?if(!empty($arResult["ORDER"]["DELIVERIES"])):?>
								<tr>
									<td><span><?=Loc::getMessage("ORDER_DELIVERY")?></span></td>
									<td>
										<span class="label"><?=Loc::getMessage("ORDER_DELIVERY")?></span>
										<select class="deliverySelect" name="DEVIVERY_TYPE" data-default="<?=$arResult["FIRST_DELIVERY"]["ID"]?>">
											<?foreach($arResult["ORDER"]["DELIVERIES"] as $arDevivery):?>
												<option data-price="<?=doubleval($arDevivery["PRICE"])?>" data-name="<?=htmlspecialcharsbx($arDevivery["NAME"])?>" value="<?=$arDevivery["ID"]?>"><?=htmlspecialcharsbx($arDevivery["NAME"])?> <?=$arDevivery["PRICE_FORMATED"]?></option>
											<?endforeach;?>
										</select>
										<?if(!empty($arResult["STORES"])):?>
											<div class="storeSelectContainer<?if(empty($arResult["FIRST_DELIVERY"]["STORES"])):?> hidden<?endif;?>">
												<span class="label"><?=Loc::getMessage("STORE_SELECT")?></span>
												<select class="storeSelect" name="STORE">
													<?foreach($arResult["STORES"] as $arStore):?>
														<?if(in_array($arStore["ID"], $arResult["FIRST_DELIVERY"]["STORES"])):?>
															<option value="<?=$arStore["ID"]?>"><?=$arStore["TITLE"]?> - <?=$arStore["ADDRESS"]?> - <?=$arStore["PRODUCTS_STATUS"]?></option>
														<?endif;?>
													<?endforeach;?>
												</select>
											</div>
										<?endif;?>
										<?if(!empty($arResult["ORDER"]["EXTRA_SERVICES"])):?>
											<div class="extraServiceItems<?if(empty($arResult["ORDER"]["EXTRA_SERVICES"][$arResult["FIRST_DELIVERY"]["ID"]])):?> hidden<?endif;?>">
												<?foreach($arResult["ORDER"]["EXTRA_SERVICES"] as $deliveryId => $nextServices):?>
													<?if(\DigitalWeb\Basket::checkExtraServicesRights($nextServices)):?>
														<div class="extraServicesItemContainer<?if($arResult["FIRST_DELIVERY"]["ID"] != $deliveryId):?> hidden<?endif;?>" data-delivery-id="<?=$deliveryId?>">
															<?foreach($nextServices as $nextService):?>
																<?if(\DigitalWeb\Basket::checkExtraServicesRights(array($nextService))):?>
																	<div class="extraServicesItem" data-service-id="<?=$nextService["ID"]?>">
																		<?printExtraServiceItemHTML($nextService, $arResult["CURRENCY"]["CODE"]);?>
																	</div>
																<?endif;?>
															<?endforeach;?>
														</div>
													<?endif;?>
												<?endforeach;?>
											</div>
										<?endif;?>
										<?if(!empty($arResult["FIRST_DELIVERY"])):?>
											<ul class="userProp">
												<?foreach($arResult["PROPERTIES"] as $arProperty){
													if(!empty($arProperty["RELATION"]) && $arProperty["PERSON_TYPE_ID"] == $arPersonType["ID"]){
														$attrList = ' data-property-id="'.$arProperty["ID"].'" class="propItem deliveryProps'.(empty($arProperty["DELIVERY_RELATION"]) || $arProperty["DELIVERY_RELATION"] == "N" ? ' hidden' : '').'"';
														printOrderPropertyHTML($arProperty, $attrList);
													}
												}?>
											</ul>
										<?endif;?>
									</td>
								</tr>
							<?endif;?>
							<tr>
								<td>
									<span><?=Loc::getMessage("ORDER_PAY")?></span>
								</td>
								<td>
									<span class="label"><?=Loc::getMessage("ORDER_PAY")?></span>
									<select class="paySelect" name="PAY_TYPE">
										<?if(!empty($arResult["ORDER"]["PAYSYSTEMS"])):?>
											<?foreach($arResult["ORDER"]["PAYSYSTEMS"] as $arPaysystem):?>
												<option value="<?=$arPaysystem["ID"]?>"><?=$arPaysystem["NAME"]?></option>
											<?endforeach;?>
										<?else:?>
											<option value="0" data-empty="Y"><?=Loc::getMessage("EMPTY_PAYSYSTEMS")?></option>
										<?endif;?>
									</select>
									<div class="payFromBudget<?if(empty($arResult["ORDER"]["INNER_PAYMENT"])):?> hidden<?endif;?>">
										<input type="checkbox" value="Y" id="payFromBudget_<?=$arPersonType["ID"]?>" name="payFromBudget" class="budgetSwitch"<?if(!empty($arResult["ORDER"]["INNER_PAYMENT"]["RANGE"]["MAX"])):?> data-max="<?=$arResult["ORDER"]["INNER_PAYMENT"]["RANGE"]["MAX"]?>"<?endif;?> data-account-balance="<?=$arResult["USER_ACCOUNT"]["CURRENT_BUDGET"]?>">
										<label for="payFromBudget_<?=$arPersonType["ID"]?>"><?=Loc::getMessage("PAY_FROM_BUDGET")?> (<?=Loc::getMessage("ACCOUNT_BALANCE")?> <?=$arResult["USER_ACCOUNT"]["PRINT_CURRENT_BUDGET"]?>)</label>
									</div>
									<?if(!empty($arResult["PROPERTIES"])):?>
										<ul class="userProp">
											<?foreach($arResult["PROPERTIES"] as $arProperty){
												if(!empty($arProperty["RELATION"]) && $arProperty["PERSON_TYPE_ID"] == $arPersonType["ID"]){
													$attrList = ' data-property-id="'.$arProperty["ID"].'" class="propItem payProps'.(empty($arProperty["PAYSYSTEM_RELATION"]) || $arProperty["PAYSYSTEM_RELATION"] == "N" ? ' hidden' : '').'"';
													printOrderPropertyHTML($arProperty, $attrList);
												}
											}?>
										</ul>
									<?endif;?>
								</td>
							</tr>
							<tr>
								<td></td>
								<td>
									<span class="label"><?=Loc::getMessage("ORDER_COMMENT")?></span>
									<textarea name="comment" class="orderComment"></textarea>
									<div class="personalInfoLabel"><?=Loc::getMessage("PERSONTAL_INFO_ORDER_LABEL")?></div>
								</td>
							</tr>
						</table>
						<input type="hidden" name="PERSON_TYPE" value="<?=$arPersonType["ID"]?>">
					</form>
					<?$personTypeIndex++;?>
				<?endforeach;?>
			<?endif;?>
			<div class="orderLine bottom">
				<div id="sum">
					<a href="#" class="order orderMake" id="orderMake"><img src="<?=SITE_TEMPLATE_PATH?>/images/order.png"> <?=Loc::getMessage("ORDER_GO")?></a>
					<span class="label hd"><?=Loc::getMessage("TOTAL_QTY")?></span> <span class="price hd countItems"><?=$countPos?></span>
					<span class="label<?if(empty($arResult["ORDER"]["DELIVERIES"])):?> hidden<?endif;?>"><?=Loc::getMessage("ORDER_DELIVERY")?>:</span>
					<span class="price<?if(empty($arResult["ORDER"]["DELIVERIES"])):?> hidden<?endif;?>"><span class="deliverySum"><?=$arResult["FIRST_DELIVERY"]["PRICE_FORMATED"];?></span></span>
					<span class="label"><?=Loc::getMessage("ORDER_TOTAL_SUM")?></span>
					<span class="price"><span class="orderSum"><?=FormatCurrency($arResult["BASKET_SUM"] + $arResult["FIRST_DELIVERY"]["PRICE"], $arResult["CURRENCY"]["CODE"]);?></span></span>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="basketError error1">
		<div class="basketErrorContainer">
			<div class="errorPicture"><img src="<?=$templateFolder?>/images/error.jpg" alt="" title=""></div>
			<div class="errorHeading"><?=Loc::getMessage("ORDER_ERROR_1_HEADING")?></div>
			<a href="#" class="basketErrorClose errorClose"><span class="errorCloseLink"></span></a>
			<div class="errorMessage"><?=Loc::getMessage("ORDER_ERROR_1")?></div>
			<a href="#" class="basketErrorClose btn-simple btn-small"><?=Loc::getMessage("ORDER_CLOSE")?></a>
		</div>
	</div>
	<div class="basketError error2">
		<div class="basketErrorContainer">
			<div class="errorPicture"><img src="<?=$templateFolder?>/images/error.jpg" alt="" title=""></div>
			<div class="errorHeading"><?=Loc::getMessage("ORDER_ERROR_2_HEADING")?></div>
			<a href="#" class="basketErrorClose errorClose"><span class="errorCloseLink"></span></a>
			<div class="errorMessage"><?=Loc::getMessage("ORDER_ERROR_2")?></div>
			<a href="#" class="basketErrorClose btn-simple btn-small"><?=Loc::getMessage("ORDER_CLOSE")?></a>
		</div>
	</div>
	<div class="basketError error3">
		<div class="basketErrorContainer">
			<div class="errorPicture"><img src="<?=$templateFolder?>/images/error.jpg" alt="" title=""></div>
			<div class="errorHeading"><?=Loc::getMessage("ORDER_ERROR_3_HEADING")?></div>
			<a href="#" class="basketErrorClose errorClose"><span class="errorCloseLink"></span></a>
			<div class="errorMessage"><?=Loc::getMessage("ORDER_ERROR_3")?></div>
			<a href="#" class="basketErrorClose btn-simple btn-small"><?=Loc::getMessage("ORDER_CLOSE")?></a>
		</div>
	</div>
<?else:?>
	<div id="empty">
		<div class="emptyWrapper">
			<div class="pictureContainer">
				<img src="<?=SITE_TEMPLATE_PATH?>/images/emptyFolder.png" alt="<?=Loc::getMessage("EMPTY_HEADING")?>" class="emptyImg">
			</div>
			<div class="info">
				<h3><?=Loc::getMessage("EMPTY_HEADING")?></h3>
				<p><?=Loc::getMessage("EMPTY_TEXT")?></p>
				<a href="<?=SITE_DIR?>" class="back"><?=Loc::getMessage("MAIN_PAGE")?></a>
			</div>
		</div>
		<?$APPLICATION->IncludeComponent("bitrix:menu", "emptyMenu", Array(
			"ROOT_MENU_TYPE" => "left",
				"MENU_CACHE_TYPE" => "N",
				"MENU_CACHE_TIME" => "3600",
				"MENU_CACHE_USE_GROUPS" => "Y",
				"MENU_CACHE_GET_VARS" => "",
				"MAX_LEVEL" => "1",
				"CHILD_MENU_TYPE" => "left",
				"USE_EXT" => "Y",
				"DELAY" => "N",
				"ALLOW_MULTI_SELECT" => "N",
			),
			false
		);?>
	</div>
<?endif;?>

<script>
	var basketLang = {
		"max-quantity": '<?=Loc::getMessage("MAX_QUANTITY")?>',
		"empty-paysystems": '<?=Loc::getMessage("EMPTY_PAYSYSTEMS")?>',
		"empty-deliveries": '<?=Loc::getMessage("EMPTY_DELIVERIES")?>',
	};
</script>

<script>
	var ajaxDir = "<?=$componentPath?>";
	var siteId = "<?=$component->getSiteId()?>";
	var siteCurrency = <?=\Bitrix\Main\Web\Json::encode($arResult["CURRENCY"]);?>;
	var basketParams = <?=\Bitrix\Main\Web\Json::encode(\DigitalWeb\Basket::clearParams($arParams));?>;
	var maskedUse = "<?=$arParams["USE_MASKED"]?>";
	var maskedFormat = "<?=$arParams["MASKED_FORMAT"]?>";
</script>

<?function getHTMLDataAttrs($arProperty = array(), $dataAttr = ""){
	if($arProperty["IS_PROFILE_NAME"] == "Y") $dataAttr .= ' data-profile-name="Y"';
	if($arProperty["IS_EMAIL"] == "Y") $dataAttr .= ' data-mail="Y"';
	if($arProperty["IS_PAYER"] == "Y") $dataAttr .= ' data-payer="Y"';
	if($arProperty["IS_LOCATION4TAX"] == "Y") $dataAttr .= ' data-location4tax="Y"';
	if($arProperty["IS_FILTERED"] == "Y") $dataAttr .= ' data-filtred="Y"';
	if($arProperty["IS_ZIP"] == "Y") $dataAttr .= ' data-zip="Y"';
	if($arProperty["IS_PHONE"] == "Y") $dataAttr .= ' data-mobile="Y"';
	if($arProperty["IS_ADDRESS"] == "Y") $dataAttr .= ' data-address="Y"';
	return $dataAttr;
}?>

<?function printOrderPropertyHTML($arProperty, $attrList = "", $arParams = array()){
	$dataAttr = getHTMLDataAttrs($arProperty);
	$propId = randString(7);?>
	<li<?=$attrList?>>
		<?if(!empty($arProperty["TYPE"]) && $arProperty["TYPE"] != "Y/N"):?>
			<span class="label"><?=$arProperty["NAME"]?><?if($arProperty["REQUIRED"] === "Y"):?>*<?endif;?></span>
			<label><?=$arProperty["DESCRIPTION"]?></label>
		<?endif;?>
		<?if($arProperty["TYPE"] == "STRING" && (empty($arProperty["MULTILINE"]) || $arProperty["MULTILINE"] == "N")):?>
			<input type="text" name="ORDER_PROP_<?=$arProperty["ID"]?>" value="<?=$arProperty["CURRENT_VALUE"]?>" data-required="<?if($arProperty["REQUIRED"] === "Y"):?>Y<?else:?>N<?endif;?>" id="<?=$arProperty["ID"]?>" data-prop-id="<?=$propId?>" data-id="<?=$arProperty["ID"]?>"<?=$dataAttr?>>
			<?if($arProperty["IS_PHONE"] == "Y" && $arParams["USE_MASKED"] == "Y" && !empty($arParams["MASKED_FORMAT"])):?>
				<script>
					$(function(){
						$('input[data-prop-id="<?=$propId?>"]').mask("<?=$arParams["MASKED_FORMAT"]?>");
					});
				</script>
			<?endif;?>
		<?elseif($arProperty["TYPE"] == "STRING" && $arProperty["MULTILINE"] == "Y"):?>
			<textarea name="ORDER_PROP_<?=$arProperty["ID"]?>" data-required="<?if($arProperty["REQUIRED"] === "Y"):?>Y<?else:?>N<?endif;?>" id="<?=$arProperty["ID"]?>" data-id="<?=$arProperty["ID"]?>"<?=$dataAttr?>><?=$arProperty["CURRENT_VALUE"]?></textarea>
		<?elseif($arProperty["TYPE"] == "NUMBER"):?>
			<input type="text" name="ORDER_PROP_<?=$arProperty["ID"]?>" value="<?=$arProperty["CURRENT_VALUE"]?>" data-required="<?if($arProperty["REQUIRED"] === "Y"):?>Y<?else:?>N<?endif;?>" id="<?=$arProperty["ID"]?>" data-id="<?=$arProperty["ID"]?>" data-number="Y"<?=$dataAttr?>>
		<?elseif($arProperty["TYPE"] == "DATE"):?>
			<input type="text" name="ORDER_PROP_<?=$arProperty["ID"]?>" value="<?=$arProperty["CURRENT_VALUE"]?>" data-required="<?if($arProperty["REQUIRED"] === "Y"):?>Y<?else:?>N<?endif;?>" id="<?=$arProperty["ID"]?>" data-id="<?=$arProperty["ID"]?>" data-time="Y" onclick="BX.calendar({node: this, field: this, bHideTime: false, bTime: <?=(!empty($arProperty["ON_SETTINGS"]["TIME"]) && $arProperty["ON_SETTINGS"]["TIME"] == "Y") ? "true" : "false"?>});" class="timeField"<?=$dataAttr?>>
		<?elseif($arProperty["TYPE"] == "Y/N"):?>
			<label><?=$arProperty["DESCRIPTION"]?></label>
			<div class="propLine">
				<input type="checkbox" value="Y"<?if($arProperty["CURRENT_VALUE"] == "Y"):?> checked<?endif;?> data-required="<?if($arProperty["REQUIRED"] === "Y"):?>Y<?else:?>N<?endif;?>" id="<?=$arProperty["ID"]?>" data-id="<?=$arProperty["ID"]?>" name="ORDER_PROP_<?=$arProperty["ID"]?>"<?=$dataAttr?>>
				<label for="<?=$arProperty["ID"]?>"><?=$arProperty["NAME"]?><?if($arProperty["REQUIRED"] === "Y"):?>*<?endif;?></label>
			</div>
		<?elseif($arProperty["TYPE"] == "ENUM" && $arProperty["MULTIPLE"] == "Y" && $arProperty["MULTIELEMENT"] == "Y" && !empty($arProperty["OPTIONS"])):?>
			<?foreach($arProperty["OPTIONS"] as $nextIndex => $nextValue):?>
				<div class="propLine">
					<input type="checkbox" name="ORDER_PROP_<?=$arProperty["ID"]?>" value="<?=$nextIndex?>" data-required="<?if($arProperty["REQUIRED"] === "Y"):?>Y<?else:?>N<?endif;?>" id="<?=$propId?>_<?=$arProperty["ID"]?>_<?=$nextIndex?>"<?=(is_array($arProperty["CURRENT_VALUE"]) && in_array($nextIndex, $arProperty["CURRENT_VALUE"]) ? " checked" : "")?> data-id="<?=$arProperty["ID"]?>"<?=$dataAttr?>>
					<label for="<?=$propId?>_<?=$arProperty["ID"]?>_<?=$nextIndex?>"><?=htmlspecialcharsbx($nextValue)?></label>
				</div>
			<?endforeach;?>
		<?elseif($arProperty["TYPE"] == "ENUM" && $arProperty["MULTIPLE"] == "N" && (empty($arProperty["MULTIELEMENT"]) || $arProperty["MULTIELEMENT"] == "N") && !empty($arProperty["OPTIONS"])):?>
	        <select name="ORDER_PROP_<?=$arProperty["ID"]?>" data-required="<?if($arProperty["REQUIRED"] === "Y"):?>Y<?else:?>N<?endif;?>" data-id="<?=$arProperty["ID"]?>"<?=$dataAttr?>>
		        <?foreach($arProperty["OPTIONS"] as $nextIndex => $nextValue):?>
		            <option value="<?=$nextIndex?>"<?=($arProperty["CURRENT_VALUE"] == $nextIndex ? " selected" : "")?>><?=htmlspecialcharsbx($nextValue)?></option>
		        <?endforeach;?>
	        </select>
		<?elseif($arProperty["TYPE"] == "ENUM" && $arProperty["MULTIPLE"] == "N" && $arProperty["MULTIELEMENT"] == "Y" && !empty($arProperty["OPTIONS"])):?>
			<?foreach($arProperty["OPTIONS"] as $nextIndex => $nextValue):?>
				<div class="propLine">
					<input type="radio" name="ORDER_PROP_<?=$arProperty["ID"]?>" value="<?=$nextIndex?>" data-required="<?if($arProperty["REQUIRED"] === "Y"):?>Y<?else:?>N<?endif;?>" id="<?=$propId?>_<?=$arProperty["ID"]?>_<?=$nextIndex?>"<?=($arProperty["CURRENT_VALUE"] == $nextIndex ? " checked" : "")?> data-id="<?=$arProperty["ID"]?>"<?=$dataAttr?>>
					<label for="<?=$propId?>_<?=$arProperty["ID"]?>_<?=$nextIndex?>"><?=htmlspecialcharsbx($nextValue)?></label>
				</div>
			<?endforeach;?>
		<?elseif($arProperty["TYPE"] == "ENUM" && $arProperty["MULTIPLE"] == "Y" && (empty($arProperty["MULTIELEMENT"]) || $arProperty["MULTIELEMENT"] == "N") && !empty($arProperty["OPTIONS"])):?>
			<select multiple name="ORDER_PROP_<?=$arProperty["ID"]?>" size="<?=((IntVal($arProperty["SIZE"]) > 0) ? $arProperty["SIZE"] : 5)?>" class="multi" data-required="<?if($arProperty["REQUIRED"] === "Y"):?>Y<?else:?>N<?endif;?>" data-id="<?=$arProperty["ID"]?>"<?=$dataAttr?>>
				<?foreach($arProperty["OPTIONS"] as $nextIndex => $nextValue):?>
					<option value="<?=$nextIndex?>"<?=(is_array($arProperty["CURRENT_VALUE"]) && in_array($nextIndex, $arProperty["CURRENT_VALUE"]) ? " selected" : "")?>><?=htmlspecialcharsbx($nextValue)?></option>
				<?endforeach;?>
			</select>
		<?elseif($arProperty["TYPE"] == "FILE"):?>
			<input type="file" name="ORDER_PROP_<?=$arProperty["ID"]?>" data-required="<?if($arProperty["REQUIRED"] === "Y"):?>Y<?else:?>N<?endif;?>" id="<?=$arProperty["ID"]?>" data-id="<?=$arProperty["ID"]?>" class="file" autocomplete="off" data-file="Y"<?if($arProperty["MULTIPLE"] == "Y"):?> multiple<?endif;?> value=""<?=$dataAttr?>>
		<?elseif($arProperty["TYPE"] == "LOCATION"):?>
			<input type="text" name="ORDER_PROP_<?=$arProperty["ID"]?>" value="<?=$arProperty["LOCATION"]["DISPLAY_VALUE"]?>" data-last-id="<?=$arProperty["LOCATION_ID"]?>" data-last-value="<?=$arProperty["LOCATION"]["DISPLAY_VALUE"]?>" data-required="<?if($arProperty["REQUIRED"] === "Y"):?>Y<?else:?>N<?endif;?>" id="<?=$arProperty["ID"]?>" data-id="<?=$arProperty["ID"]?>" class="location" autocomplete="off" data-location="<?=$arProperty["LOCATION_ID"]?>" <?=$dataAttr?>>
			<div class="locationSwitchContainer"></div>
		<?endif;?>
	</li>
<?}?>

<?function printExtraServiceItemHTML($extraServiceItem = array(), $currencyCode = "RUB"){

	if(!empty($extraServiceItem)){
		if($extraServiceItem["CLASS_NAME"] == "\Bitrix\Sale\Delivery\ExtraServices\Enum"){
			if(!empty($extraServiceItem["PARAMS"]["PRICES"])){?>
				<?if(!empty($extraServiceItem["NAME"])):?>
					<div class="serviceName"><?=$extraServiceItem["NAME"]?></div>
				<?endif;?>
				<?if(!empty($extraServiceItem["DESCRIPTION"])):?>
					<div class="serviceDescription"><?=$extraServiceItem["DESCRIPTION"]?></div>
				<?endif;?>
				<div class="serviceSelectItem">
					<select name="extra_<?=$extraServiceItem["ID"]?>" class="extraServiceSwitch" data-id="<?=$extraServiceItem["ID"]?>" data-default="<?=$extraServiceItem["INIT_VALUE"]?>" data-last="<?=!empty($extraServiceItem["INIT_VALUE"]) ? $extraServiceItem["PARAMS"]["PRICES"][$extraServiceItem["INIT_VALUE"]]["PRICE"] : 0;?>">
						<?foreach($extraServiceItem["PARAMS"]["PRICES"] as $serviceItemId => $nextServiceItem):?>
							<option value="<?=$serviceItemId?>" data-price="<?=$nextServiceItem["PRICE"]?>"<?if($extraServiceItem["INIT_VALUE"] == $serviceItemId):?> selected<?endif;?>><?=$nextServiceItem["TITLE"]?> <b><?=FormatCurrency($nextServiceItem["PRICE"], $currencyCode);?></option>
						<?endforeach;?>
					</select>
				</div>
			<?}
		}

		elseif($extraServiceItem["CLASS_NAME"] == "\Bitrix\Sale\Delivery\ExtraServices\Checkbox"){?>
			<?$extraId = \Bitrix\Main\Security\Random::getString(8);?>
			<div class="serviceBoxContainer">
				<input type="checkbox" value="Y" id="service_<?=$extraId?>" name="service_<?=$extraServiceItem["ID"]?>" class="extraServiceSwitch" data-id="<?=$extraServiceItem["ID"]?>" data-default="<?=$extraServiceItem["INIT_VALUE"]?>" data-price="<?=$extraServiceItem["PARAMS"]["PRICE"]?>"<?if($extraServiceItem["INIT_VALUE"] == "Y"):?> checked<?endif;?>>
				<label for="service_<?=$extraId?>"><?=$extraServiceItem["NAME"]?> <span class="servicePrice"><b>(<?=FormatCurrency($extraServiceItem["PARAMS"]["PRICE"], $currencyCode);?>)</b></span></label>
				<div class="serviceDescription"><?=$extraServiceItem["DESCRIPTION"]?></div>
			</div>
		<?}

		elseif($extraServiceItem["CLASS_NAME"] == "\Bitrix\Sale\Delivery\ExtraServices\Quantity"){?>
			<div class="extraServiceQuantity">
				<div class="serviceHeadingContainer">
					<div class="serviceName"><?=$extraServiceItem["NAME"]?></div>
					<div class="servicePrice"><?=Loc::getMessage("PRICE_FOR_PIECE");?> <b><?=FormatCurrency($extraServiceItem["PARAMS"]["PRICE"], $currencyCode);?></b></div>
					<div class="serviceTotalSum"><b><?=Loc::getMessage("TOTAL_SUM")?> (<span class="extraServiceItemSum"><?=FormatCurrency(($extraServiceItem["PARAMS"]["PRICE"] * $extraServiceItem["INIT_VALUE"]), $currencyCode);?></span>)</b></div>
				</div>
				<div class="serviceDescription"><?=$extraServiceItem["DESCRIPTION"]?></div>
				<input type="text" name="service_<?=$extraServiceItem["ID"]?>" value="<?=$extraServiceItem["INIT_VALUE"]?>" class="extraServiceSwitch" data-id="<?=$extraServiceItem["ID"]?>" data-default="<?=$extraServiceItem["INIT_VALUE"]?>" data-last="<?=$extraServiceItem["INIT_VALUE"]?>" data-price="<?=$extraServiceItem["PARAMS"]["PRICE"]?>">
			</div>
		<?}

	}

}?>