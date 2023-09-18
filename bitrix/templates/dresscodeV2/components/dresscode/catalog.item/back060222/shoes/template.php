<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(false);?>
<?if(!empty($arResult)):?>
	<?$uniqID = CAjax::GetComponentID($this->__component->__name, $this->__component->__template->__name, false);?>
	<?
	// формируем название категории для аналитики, убираем лишние элементы
	$secturl = explode("/", $arResult['DETAIL_PAGE_URL']);
	$sectcount = count($secturl) - 1;
	unset($secturl[$sectcount]); unset($secturl[0]);unset($secturl[1]);

		if(!empty($arResult["PARENT_PRODUCT"]["EDIT_LINK"])){
			$this->AddEditAction($arResult["ID"], $arResult["PARENT_PRODUCT"]["EDIT_LINK"], CIBlock::GetArrayByID($arResult["PARENT_PRODUCT"]["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arResult["ID"], $arResult["PARENT_PRODUCT"]["DELETE_LINK"], CIBlock::GetArrayByID($arResult["PARENT_PRODUCT"]["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage("CT_BNL_ELEMENT_DELETE_CONFIRM")));
		}
		if(!empty($arResult["EDIT_LINK"])){
			$this->AddEditAction($arResult["ID"], $arResult["EDIT_LINK"], CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arResult["ID"], $arResult["DELETE_LINK"], CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage("CT_BNL_ELEMENT_DELETE_CONFIRM")));
		}
	?>
	<div class="item product sku itemCard" id="<?=$this->GetEditAreaId($arResult["ID"]);?>" data-product-iblock-id="<?=$arParams["IBLOCK_ID"]?>"  data-position = "<?=$arParams['POS_COUNT']?>"  data-from-cache="<?=$arResult["FROM_CACHE"]?>" data-convert-currency="<?=$arParams["CONVERT_CURRENCY"]?>" data-product-price="<?=$arResult['PRICE']['DISCOUNT_PRICE'];?>"  data-product-category="<?=implode("/",$secturl);?>" data-product-articul="<?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'];?>" data-product-brand="<?=$arResult['PARENT_PRODUCT']['BRAND'];?>"  data-currency-id="<?=$arParams["CURRENCY_ID"]?>" data-product-name="<?=($arResult['PARENT_PRODUCT']['NAME'] ? $arResult['PARENT_PRODUCT']['NAME'] :  $arResult['NAME']);?>"  data-product-id="<?=($arResult['PARENT_PRODUCT']['ID'] ? $arResult['PARENT_PRODUCT']['ID'] :  $arResult['ID']);?>" data-iblock-id="<?=$arResult["SKU_INFO"]["IBLOCK_ID"]?>" data-prop-id="<?=$arResult["SKU_INFO"]["SKU_PROPERTY_ID"]?>" data-product-width="<?=$arParams["PICTURE_WIDTH"]?>" data-product-height="<?=$arParams["PICTURE_HEIGHT"]?>" data-hide-measure="<?=$arParams["HIDE_MEASURES"]?>" data-currency="<?=$arResult["EXTRA_SETTINGS"]["CURRENCY"]?>" data-hide-not-available="<?=$arParams["HIDE_NOT_AVAILABLE"]?>" data-price-code="<?=implode("||", $arParams["PRODUCT_PRICE_CODE"])?>">
		<div class="tabloid nowp">
			<a href="#" class="removeFromWishlist" data-id="<?=$arResult["~ID"]?>"></a>
			<?if(!empty($arResult["PROPERTIES"]["OFFERS"]["VALUE"])):?>
				<div class="markerContainer">
					<?foreach ($arResult["PROPERTIES"]["OFFERS"]["VALUE"] as $ifv => $marker):

						if (($arParams['LIST_TYPE'] == 'hit' && $marker  == 'Хит продаж') ||
						($arParams['LIST_TYPE'] == 'sale' && $marker  == 'Распродажа') ||
						($arParams['LIST_TYPE'] == 'new' && $marker  == 'Новинка') ||
						(!in_array($marker, ['Хит продаж','Распродажа','Новинка'])) ||
						!isset($arParams['LIST_TYPE'])) {?>
					    <div class="marker <?=strstr($arResult["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv], "m-") ? $arResult["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv] : "m-def"?>"><?=$marker?></div>
						<?}?>
					<?endforeach;?>
				</div>
			<?endif;?>
			<?/*<div class="rating">
				<i class="m" style="width:<?=(intval($arResult["PROPERTIES"]["RATING"]["VALUE"]) * 100 / 5)?>%"></i>
				<i class="h"></i>
			</div>*/?>
			<?if(!empty($arResult["EXTRA_SETTINGS"]["SHOW_TIMER"])):?>
				<div class="specialTime productSpecialTime" id="timer_<?=$arResult["EXTRA_SETTINGS"]["TIMER_UNIQ_ID"];?>_<?=$uniqID?>">
					<div class="specialTimeItem">
						<div class="specialTimeItemValue timerDayValue">0</div>
						<div class="specialTimeItemlabel"><?=GetMessage("PRODUCT_TIMER_DAY_LABEL")?></div>
					</div>
					<div class="specialTimeItem">
						<div class="specialTimeItemValue timerHourValue">0</div>
						<div class="specialTimeItemlabel"><?=GetMessage("PRODUCT_TIMER_HOUR_LABEL")?></div>
					</div>
					<div class="specialTimeItem">
						<div class="specialTimeItemValue timerMinuteValue">0</div>
						<div class="specialTimeItemlabel"><?=GetMessage("PRODUCT_TIMER_MINUTE_LABEL")?></div>
					</div>
					<div class="specialTimeItem">
						<div class="specialTimeItemValue timerSecondValue">0</div>
						<div class="specialTimeItemlabel"><?=GetMessage("PRODUCT_TIMER_SECOND_LABEL")?></div>
					</div>
				</div>
			<?endif;?>
			<?if(!empty($arResult["PROPERTIES"]["TIMER_LOOP"]["VALUE"])):?>
				<script type="text/javascript">
					$(document).ready(function(){
						$("#timer_<?=$arResult["EXTRA_SETTINGS"]["TIMER_UNIQ_ID"];?>_<?=$uniqID?>").dwTimer({
							timerLoop: "<?=$arResult["PROPERTIES"]["TIMER_LOOP"]["VALUE"]?>",
							<?if(empty($arResult["PROPERTIES"]["TIMER_START_DATE"]["VALUE"])):?>
								startDate: "<?=MakeTimeStamp($arResult["DATE_CREATE"], "DD.MM.YYYY HH:MI:SS")?>"
							<?else:?>
								startDate: "<?=MakeTimeStamp($arResult["PROPERTIES"]["TIMER_START_DATE"]["VALUE"], "DD.MM.YYYY HH:MI:SS")?>"
							<?endif;?>
						});
					});
				</script>
			<?elseif(!empty($arResult["EXTRA_SETTINGS"]["SHOW_TIMER"]) && !empty($arResult["PROPERTIES"]["TIMER_DATE"]["VALUE"])):?>
				<script type="text/javascript">
					$(document).ready(function(){
						$("#timer_<?=$arResult["EXTRA_SETTINGS"]["TIMER_UNIQ_ID"];?>_<?=$uniqID?>").dwTimer({
							endDate: "<?=MakeTimeStamp($arResult["PROPERTIES"]["TIMER_DATE"]["VALUE"], "DD.MM.YYYY HH:MI:SS")?>"
						});
					});
				</script>
			<?endif;?>
		    <div class="productTable">
		    	<div class="productColImage">
					<a href="<?=$arResult["DETAIL_PAGE_URL"]?><?=($arResult['ID']?  "?offerID=".$arResult['ID']: "")?>" class="picture">
						<?if($arParams["LAZY_LOAD_PICTURES"] == "Y"):?>
							<img src="<?=SITE_TEMPLATE_PATH?>/images/mloader.gif" class="lazy" data-lazy="<?=$arResult["PICTURE"]["src"]?>" alt="<?if(!empty($arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"])):?><?=$arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]?><?else:?><?=$arResult["NAME"]?><?endif;?>" title="<?if(!empty($arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"])):?><?=$arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]?><?else:?><?=$arResult["NAME"]?><?endif;?>">
						<?else:?>
							<img src="<?=$arResult["PICTURE"]["src"]?>" alt="<?if(!empty($arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"])):?><?=$arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]?><?else:?><?=$arResult["NAME"]?><?endif;?>" title="<?if(!empty($arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"])):?><?=$arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]?><?else:?><?=$arResult["NAME"]?><?endif;?>">
						<?endif;?>
						<?/*
						<span class="getFastView" data-id="<?=$arResult["ID"]?>"><?=GetMessage("FAST_VIEW_PRODUCT_LABEL")?></span>*/?>
						<?/*if($arResult["CATALOG_AVAILABLE"] == "Y" && $arResult['DISPLAY_BUTTONS']['CART_BUTTON']):?>
						<span class="getFastOrder"  data-id="<?=$arResult["ID"]?>" id="GTM_fastorder_catalog_get"><?=GetMessage("FAST_ORDER_PRODUCT_LABEL")?></span>
						<?endif;*/?>
					</a>
		    	</div>
		    	<div class="productColText">
					<a href="<?=$arResult["DETAIL_PAGE_URL"]?>" class="name"><span class="middle"><?=$arResult["NAME"]?> <?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']?></span></a> 
					<?if(!empty($arResult["EXTRA_SETTINGS"])):?>
						<?//price container?>
						<?
						$prodid = $arResult['PRICE']['PRODUCT_ID'];

						if (!empty($arResult['MAX_PRICE'][$prodid]))
						{
							$max_price = $arResult['MAX_PRICE'][$prodid];
							$max_price_mindiff = $max_price - 100;
						}
						?>
						<?/*if($arResult["EXTRA_SETTINGS"]["COUNT_PRICES"] > 1):?>
							<a class="price getPricesWindow" data-id="<?=$arResult["ID"]?>">
								<span class="priceIcon"></span><?=CCurrencyLang::CurrencyFormat($arResult["PRICE"]["DISCOUNT_PRICE"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true)?>
								<?if($arParams["HIDE_MEASURES"] != "Y" && !empty($arResult["EXTRA_SETTINGS"]["MEASURES"][$arResult["CATALOG_MEASURE"]]["SYMBOL_RUS"])):?>
									<span class="measure"> / <?=$arResult["EXTRA_SETTINGS"]["MEASURES"][$arResult["CATALOG_MEASURE"]]["SYMBOL_RUS"]?></span>
								<?endif;?>
								<s class="discount">
									<?if(!empty($arResult["PRICE"]["DISCOUNT"])):?>
										<?=CCurrencyLang::CurrencyFormat($arResult["PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true)?>
									<?endif;?>
								</s>
							</a>
						<?else:*/?>
							<a class="price"><?=CCurrencyLang::CurrencyFormat($arResult["PRICE"]["DISCOUNT_PRICE"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true)?>
								<?if($arParams["HIDE_MEASURES"] != "Y" && !empty($arResult["EXTRA_SETTINGS"]["MEASURES"][$arResult["CATALOG_MEASURE"]]["SYMBOL_RUS"])):?>
									<span class="measure"> / <?=$arResult["EXTRA_SETTINGS"]["MEASURES"][$arResult["CATALOG_MEASURE"]]["SYMBOL_RUS"]?></span>
								<?endif;?>
								<s class="discount">
									<?if ($arResult['PRICE']['PRICE']['PRICE'] < $max_price_mindiff):
											$price_diff = $max_price - $arResult['PRICE']['PRICE']['PRICE']; ?>
										<?=CCurrencyLang::CurrencyFormat($max_price, $arResult["EXTRA_SETTINGS"]["CURRENCY"], true)?>

									<?elseif(!empty($arResult["PRICE"]["DISCOUNT"])):?>
										<?=CCurrencyLang::CurrencyFormat($arResult["PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true)?>
									<?endif;?>
								</s>
							</a>
						<?//endif;
						?>
						<?if($arResult["CATALOG_AVAILABLE"] != "Y"):?>
							<?//addCart button ?>


								<?/*<a href="#" class="addCart disabled" data-id="<?=$arResult["ID"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/incart.png" alt="<?=GetMessage("ADDCART_LABEL")?>" class="icon"><?=GetMessage("ADDCART_LABEL")?></a>*/?>

									<?if ($arResult['DISPLAY_BUTTONS']['CART_BUTTON']):?>

										<a href="#" class="fastBack fastBut label changeID " data-id="<?=$arResult["ID"]?>" <?if($arResult['SALON_AVAILABLE'] != "0" ||  $arResult['SALON_COUNT'] != "0" || $arResult["CATALOG_AVAILABLE"] == "Y"){?>style="display:none;"<?}?>><img src="<?=SITE_TEMPLATE_PATH?>/images/incart.png" alt="<?=GetMessage("ORDER_NOTAVAIL_LABEL_ALT")?>" class="icon" ><?=GetMessage("ORDER_NOTAVAIL_LABEL")?></a>
									<?else:?>
										<?// Сканирование стоп, основная кнопка?>
										<?if ($arResult['DISPLAY_BUTTONS']['INSOLE_BUTTON']):?>

										<a href="/service/scan/" class="magentaBigButton scan " >Запись на изготовление</a>

										<?endif;?>
									<?endif;?>

						<?else:?>
							<?// показ кнопки В корзину?>
							<?if ($arResult['DISPLAY_BUTTONS']['CART_BUTTON']):/*?>
								<span class="fastOrder fastBut changeID"  data-id="<?=$arResult["ID"]?>" id="GTM_fastorder_catalog_get"><?=GetMessage("FAST_ORDER_PRODUCT_LABEL")?></span>
							<?*/?>
							<a href="#" class="addCart " data-id="<?=$arResult["ID"]?>"  id="GTM_add_cart_catalog_<?=($arParams['LIST_TYPE'] ? $arParams['LIST_TYPE'].'_' : '' )?><?=$arParams['POS_COUNT']?>_<?=$arResult["ID"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/incart.png" alt="<?=GetMessage("ADDCART_LABEL")?>" class="icon" ><?=GetMessage("ADDCART_LABEL")?></a>

							<?else:?>
								<?// Сканирование стоп, основная кнопка?>
								<?if ($arResult['DISPLAY_BUTTONS']['INSOLE_BUTTON']):?>

								<a href="/service/scan/" class="magentaBigButton scan " >Запись на изготовление</a>

								<?endif;?>
							<?endif;?>
						<?endif;?>
					<?else:?>
						<a class="price"><?=GetMessage("REQUEST_PRICE_LABEL")?></a>
						<a href="#" class="addCart disabled requestPrice" data-id="<?=$arResult["ID"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/request.png" alt="" class="icon"><?=GetMessage("REQUEST_PRICE_BUTTON_LABEL")?></a>
					<?endif;?>
					<a href="#" class="greyBigButton reserve changeID get_medi_popup_Window" data-src="/ajax/catalog/?action=reserve" data-title="Забронировать в салоне" data-id="<?=$arResult["ID"]?>"  <?if($arResult['SALON_AVAILABLE'] == "0" ||  $arResult['SALON_COUNT'] == "0" || ($arResult["CATALOG_AVAILABLE"] != "N" && $arResult['DISPLAY_BUTTONS']['CART_BUTTON'] == true)){?>style="display:none;"<?}?> data-action="reserve">Забронировать</a>
					<a href="<?=$arResult["DETAIL_PAGE_URL"]?>" class="btn-simple add-cart"><?=GetMessage("SEE_ON_PAGE")?></a>
					<?/*if ($arResult['DISPLAY_BUTTONS']['CART_BUTTON'] && $arResult["CATALOG_AVAILABLE"] == "Y"):?>
					<a href="#" class="btn-simple fast-order"  data-id="<?=$arResult["ID"]?>"><?=GetMessage("FAST_ORDER_PRODUCT_LABEL")?></a>

					<?endif;*/
					?>
		    	</div>
		    </div>
			<div class="optional">
				<?/* купить в 1 клик

				<div class="row">
					<a href="#" class="fastOrder label<?if(empty($arResult["PRICE"]) || $arResult["CATALOG_AVAILABLE"] != "Y"):?> disabled<?endif;?>" data-id="<?=$arResult["ID"]?>"><?=GetMessage("FASTBACK_LABEL")?></a>
				</div>*/?>

				<?/*<div class="row">
					<a href="#" class="addCompare label" data-id="<?=$arResult["ID"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/compare.png" alt="" class="icon"><?=GetMessage("COMPARE_LABEL")?></a>
					<a href="#" class="addWishlist label" data-id="<?=$arResult["~ID"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/wishlist.png" alt="" class="icon"><?=GetMessage("WISHLIST_LABEL")?></a>
				</div>*/?>
				<?/*
				<div class="row">
					<?if($arResult["CATALOG_QUANTITY"] > 0):?>
						<?if(!empty($arResult["EXTRA_SETTINGS"]["STORES"]) && $arResult["EXTRA_SETTINGS"]["STORES_MAX_QUANTITY"] > 0):?>
							<a href="#" data-id="<?=$arResult["ID"]?>" class="inStock label changeAvailable getStoresWindow"><img src="<?=SITE_TEMPLATE_PATH?>/images/inStock.png" alt="<?=GetMessage("AVAILABLE")?>" class="icon"><span><?=GetMessage("AVAILABLE")?></span></a>
						<?else:?>
							<span class="inStock label changeAvailable"><img src="<?=SITE_TEMPLATE_PATH?>/images/inStock.png" alt="<?=GetMessage("AVAILABLE")?>" class="icon"><span><?=GetMessage("AVAILABLE")?></span></span>
						<?endif;?>
					<?else:?>
						<?if($arResult["CATALOG_AVAILABLE"] == "Y"):?>
							<a class="onOrder label changeAvailable"><img src="<?=SITE_TEMPLATE_PATH?>/images/onOrder.png" alt="" class="icon"><?=GetMessage("ON_ORDER")?></a>
						<?else:?>
							<a class="outOfStock label changeAvailable"><img src="<?=SITE_TEMPLATE_PATH?>/images/outOfStock.png" alt="" class="icon"><?=GetMessage("NOAVAILABLE")?></a>
						<?endif;?>
					<?endif;?>
				</div>*/?>
			</div>
			<?if(!empty($arResult["SKU_OFFERS"])):?>
				<?if(!empty($arResult["SKU_PROPERTIES"]) && $level = 1):?>
					<?foreach ($arResult["SKU_PROPERTIES"] as $propName => $arNextProp):?>
						<?if(!empty($arNextProp["VALUES"])):?>
							<?if($arNextProp["LIST_TYPE"] == "L" && $arNextProp["HIGHLOAD"] != "Y"):?>
								<?foreach ($arNextProp["VALUES"] as $xml_id => $arNextPropValue):?>
									<?if($arNextPropValue["SELECTED"] == "Y"):?>
										<?$currentSkuValue = $arNextPropValue["DISPLAY_VALUE"];?>
									<?endif;?>
								<?endforeach;?>
								<div class="skuProperty oSkuDropDownProperty" data-name="<?=$propName?>" data-level="<?=$level++?>" data-highload="<?=$arNextProp["HIGHLOAD"]?>">
									<div class="skuPropertyName"><?=preg_replace("/\[.*\]/", "", $arNextProp["NAME"])?>:</div>
									<div class="oSkuDropdown">
										<span class="oSkuCheckedItem noHideChecked"><?=$currentSkuValue?></span>
										<ul class="skuPropertyList oSkuDropdownList">
											<?foreach ($arNextProp["VALUES"] as $xml_id => $arNextPropValue):?>
												<li class="skuPropertyValue oSkuDropdownListItem<?if($arNextPropValue["DISABLED"] == "Y"):?> disabled<?elseif($arNextPropValue["SELECTED"] == "Y"):?> selected<?endif;?>" data-name="<?=$propName?>" data-value="<?=$arNextPropValue["VALUE"]?>">
													<a href="#" class="skuPropertyLink oSkuPropertyItemLink"><?=$arNextPropValue["DISPLAY_VALUE"]?></a>
												</li>
											<?endforeach;?>
										</ul>
									</div>
								</div>
							<?else:?>
								<div class="skuProperty" data-name="<?=$propName?>" data-level="<?=$level++?>" data-highload="<?=$arNextProp["HIGHLOAD"]?>">
									<div class="skuPropertyName"><?=preg_replace("/\[.*\]/", "", $arNextProp["NAME"])?></div>
									<ul class="skuPropertyList">
										<?foreach ($arNextProp["VALUES"] as $xml_id => $arNextPropValue):?>
											<li class="skuPropertyValue<?if($arNextPropValue["DISABLED"] == "Y"):?> disabled<?elseif($arNextPropValue["SELECTED"] == "Y"):?> selected<?endif;?>" data-name="<?=$propName?>" data-value="<?=$arNextPropValue["VALUE"]?>">
												<a href="#" class="skuPropertyLink">
													<?if(!empty($arNextPropValue["IMAGE"])):?>
														<img src="<?=$arNextPropValue["IMAGE"]["src"]?>" alt="<?=$arNextPropValue["DISPLAY_VALUE"] ?>"  title="<?=$arNextPropValue["DISPLAY_VALUE"] ?>">
													<?else:?>
														<?=$arNextPropValue["DISPLAY_VALUE"]?>
													<?endif;?>
												</a>
											</li>
										<?endforeach;?>
									</ul>
								</div>
							<?endif;?>
						<?endif;?>
					<?endforeach;?>
				<?endif;?>
			<?endif;?>
			<div class="clear"></div>
		</div>
	</div>
<?endif;?>
