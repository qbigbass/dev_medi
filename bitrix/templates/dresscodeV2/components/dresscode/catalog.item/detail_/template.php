<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	//comp mode
	$this->setFrameMode(true);
	//css
	$this->addExternalCss($templateFolder."/css/review.css");
	$this->addExternalCss($templateFolder."/css/media.css");
	$this->addExternalCss($templateFolder."/css/set.css");
	//js
	$this->addExternalJS($templateFolder."/js/morePicturesCarousel.js");
	$this->addExternalJS($templateFolder."/js/pictureSlider.js");
	$this->addExternalJS($templateFolder."/js/zoomer.js");
	$this->addExternalJS($templateFolder."/js/tags.js");
	$this->addExternalJS($templateFolder."/js/plus.js");
	$this->addExternalJS($templateFolder."/js/tabs.js");
	$this->addExternalJS($templateFolder."/js/sku.js");
	//global vars
	global $USER, $relatedFilter, $similarFilter, $servicesFilter;
	//other
	$countPropertyElements = 7;
	$morePhotoCounter = 0;
	$propertyCounter = 0;
	$uniqID = CAjax::GetComponentID($this->__component->__name, $this->__component->__template->__name, false);
	//edit
	if(!empty($arResult["PARENT_PRODUCT"]["EDIT_LINK"])){
		$this->AddEditAction($arResult["ID"], $arResult["PARENT_PRODUCT"]["EDIT_LINK"], CIBlock::GetArrayByID($arResult["PARENT_PRODUCT"]["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arResult["ID"], $arResult["PARENT_PRODUCT"]["DELETE_LINK"], CIBlock::GetArrayByID($arResult["PARENT_PRODUCT"]["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage("CT_BNL_ELEMENT_DELETE_CONFIRM")));
	}
	if(!empty($arResult["EDIT_LINK"])){
		$this->AddEditAction($arResult["ID"], $arResult["EDIT_LINK"], CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arResult["ID"], $arResult["DELETE_LINK"], CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage("CT_BNL_ELEMENT_DELETE_CONFIRM")));
	}
?>
<div id="<?=$this->GetEditAreaId($arResult["ID"]);?>">
	<?$this->SetViewTarget("after_breadcrumb_container");?>
		<h1 class="changeName"><?=$APPLICATION->GetTitle(false);?></h1>
	<?$this->EndViewTarget();?>
	<div id="catalogElement" class="item<?if(!empty($arResult["SKU_OFFERS"])):?> elementSku<?endif;?>" data-product-iblock-id="<?=$arParams["IBLOCK_ID"]?>" data-from-cache="<?=$arResult["FROM_CACHE"]?>" data-convert-currency="<?=$arParams["CONVERT_CURRENCY"]?>" data-currency-id="<?=$arParams["CURRENCY_ID"]?>" data-hide-not-available="<?=$arParams["HIDE_NOT_AVAILABLE"]?>" data-currency="<?=$arResult["EXTRA_SETTINGS"]["CURRENCY"]?>" data-product-id="<?=!empty($arResult["~ID"]) ? $arResult["~ID"] : $arResult["ID"]?>" data-iblock-id="<?=$arResult["SKU_INFO"]["IBLOCK_ID"]?>" data-prop-id="<?=$arResult["SKU_INFO"]["SKU_PROPERTY_ID"]?>" data-hide-measure="<?=$arParams["HIDE_MEASURES"]?>" data-price-code="<?=implode("||", $arParams["PRODUCT_PRICE_CODE"])?>" data-deactivated="<?=$arParams["SHOW_DEACTIVATED"]?>">
		<div id="elementSmallNavigation">
			<?if(!empty($arResult["TABS"])):?>
				<div class="tabs changeTabs">
					<?foreach ($arResult["TABS"] as $it => $arTab):?>
						<div class="tab<?if($arTab["ACTIVE"] == "Y"):?> active<?endif;?><?if($arTab["DISABLED"] == "Y"):?> disabled<?endif;?>" data-id="<?=$arTab["ID"]?>"><a href="<?if(!empty($arTab["LINK"])):?><?=$arTab["LINK"]?><?else:?>#<?endif;?>"><span><?=$arTab["NAME"]?></span></a></div>
					<?endforeach;?>
				</div>
			<?endif;?>
		</div>
		<div id="tableContainer">
			<div id="elementNavigation" class="column">
				<?if(!empty($arResult["TABS"])):?>
					<div class="tabs changeTabs">
						<?foreach ($arResult["TABS"] as $it => $arTab):?>
							<div class="tab<?if($arTab["ACTIVE"] == "Y"):?> active<?endif;?><?if($arTab["DISABLED"] == "Y"):?> disabled<?endif;?>" data-id="<?=$arTab["ID"]?>"><a href="<?if(!empty($arTab["LINK"])):?><?=$arTab["LINK"]?><?else:?>#<?endif;?>"><?=$arTab["NAME"]?><img src="<?=$arTab["PICTURE"]?>" alt="<?=$arTab["NAME"]?>"></a></div>
						<?endforeach;?>
					</div>
				<?endif;?>
			</div>
			<div id="elementContainer" class="column">
				<div class="mainContainer" id="browse">
					<div class="col">
						<?if(!empty($arResult["PROPERTIES"]["OFFERS"]["VALUE"])):?>
							<div class="markerContainer">
								<?foreach ($arResult["PROPERTIES"]["OFFERS"]["VALUE"] as $ifv => $marker):?>
									<div class="marker <?=strstr($arResult["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv], "m-") ? $arResult["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv] : "m-def"?> "><?=$marker?></div>
								<?endforeach;?>
							</div>
						<?endif;?>

						<?if(!empty($arResult["IMAGES"])):?>
							<div id="pictureContainer">
								<div class="pictureSlider">
									<?foreach ($arResult["IMAGES"] as $ipr => $arNextPicture):?>
										<div class="item">
											<a href="<?=$arNextPicture["LARGE_IMAGE"]["SRC"]?>" title="<?=GetMessage("CATALOG_ELEMENT_ZOOM")?>"  class="zoom" data-small-picture="<?=$arNextPicture["SMALL_IMAGE"]["SRC"]?>" data-large-picture="<?=$arNextPicture["LARGE_IMAGE"]["SRC"]?>"><img src="<?=$arNextPicture["MEDIUM_IMAGE"]["SRC"]?>" alt="<?if(!empty($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"])):?><?=$arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]?><?else:?><?=$arResult["NAME"]?><?endif;?><?if(intval($ipr) > 0):?> <?=GetMessage("CATALOG_ELEMENT_DETAIL_PICTURE_LABEL")?> <?=$ipr+1?><?endif;?>" title="<?if(!empty($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"])):?><?=$arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]?><?else:?><?=$arResult["NAME"]?><?endif;?><?if(intval($ipr) > 0):?> <?=GetMessage("CATALOG_ELEMENT_DETAIL_PICTURE_LABEL")?> <?=$ipr+1?><?endif;?>"></a>
										</div>
									<?endforeach;?>
								</div>
							</div>
							<div id="moreImagesCarousel"<?if(empty($arResult["IMAGES"]) || count($arResult["IMAGES"]) <= 1):?> class="hide"<?endif;?>>
								<div class="carouselWrapper">
									<div class="slideBox">
										<?if(empty($arResult["IMAGES"]) || count($arResult["IMAGES"]) > 1):?>
											<?foreach ($arResult["IMAGES"] as $ipr => $arNextPicture):?>
												<div class="item">
													<a href="<?=$arNextPicture["LARGE_IMAGE"]["SRC"]?>" data-large-picture="<?=$arNextPicture["LARGE_IMAGE"]["SRC"]?>" data-small-picture="<?=$arNextPicture["SMALL_IMAGE"]["SRC"]?>">
														<img src="<?=$arNextPicture["SMALL_IMAGE"]["SRC"]?>" alt="">
													</a>
												</div>
											<?endforeach;?>
										<?endif;?>
									</div>
								</div>
								<div class="controls">
									<a href="#" id="moreImagesLeftButton"></a>
									<a href="#" id="moreImagesRightButton"></a>
								</div>
							</div>
						<?endif;?>
					</div>
					<div class="secondCol col<?if(empty($arResult["PREVIEW_TEXT"]) && empty($arResult["SKU_OFFERS"]) && empty($arResult["PROPERTIES"])):?> hide<?endif;?>">
						<div class="brandImageWrap">
							<?if(!empty($arResult["BRAND"]["PICTURE"])):?>
								<a href="<?=$arResult["BRAND"]["DETAIL_PAGE_URL"]?>" class="brandImage"><img src="<?=$arResult["BRAND"]["PICTURE"]["src"]?>" alt="<?=$arResult["BRAND"]["NAME"]?>"></a>
							<?endif;?>
							<?$APPLICATION->IncludeComponent(
								"dresscode:catalog.sale.item",
								".default",
								array(
									"PRODUCT_ID" => (!empty($arResult["PARENT_PRODUCT"]["ID"]) ? $arResult["PARENT_PRODUCT"]["ID"] : $arResult["ID"]),
									"IBLOCK_TYPE" => $arParams["SALE_IBLOCK_TYPE"],
									"IBLOCK_ID" => $arParams["SALE_IBLOCK_ID"]
								),
								false
							);?>
						</div>
						<div class="reviewsBtnWrap">
							<div class="row">
								<a class="label">
									<img src="<?=SITE_TEMPLATE_PATH?>/images/reviews.png" alt="" class="icon">
									<span class="<?if(!empty($arResult["REVIEWS"]) && count($arResult["REVIEWS"]) > 0):?>countReviewsTools<?endif;?>"><?=GetMessage("REVIEWS_COUNT")?> <?=!empty($arResult["REVIEWS"]) ? count($arResult["REVIEWS"]) : 0?></span>

								</a>
							</div>
							<?if($arParams["SHOW_REVIEW_FORM"]):?>
								<div class="row">
									<a href="#" class="reviewAddButton label"><img src="<?=SITE_TEMPLATE_PATH?>/images/addReviewSmall.png" alt="<?=GetMessage("REVIEWS_ADD")?>" class="icon"><span class="labelDotted"><?=GetMessage("REVIEWS_ADD")?></span></a>
								</div>
							<?endif;?>
							<?if(!empty($arResult["PROPERTIES"]["CML2_ARTICLE"]["VALUE"])):?>
								<div class="row article">
									<?=GetMessage("CATALOG_ART_LABEL")?><span class="changeArticle" data-first-value="<?=$arResult["PROPERTIES"]["CML2_ARTICLE"]["VALUE"]?>"><?=$arResult["PROPERTIES"]["CML2_ARTICLE"]["VALUE"]?></span>
								</div>
							<?endif;?>
						</div>
						<?if(!empty($arResult["PREVIEW_TEXT"])):?>
							<div class="description">
								<!-- <h2 class="heading"><?=GetMessage("CATALOG_ELEMENT_PREVIEW_TEXT_LABEL")?></h2> -->
								<div class="changeShortDescription" data-first-value='<?=$arResult["PREVIEW_TEXT"]?>'><?=$arResult["PREVIEW_TEXT"]?></div>
							</div>
						<?endif;?>
						<?if(!empty($arResult["SKU_OFFERS"])):?>
							<?if(!empty($arResult["SKU_PROPERTIES"]) && $level = 1):?>
								<div class="elementSkuVariantLabel"><?=GetMessage("SKU_VARIANT_LABEL")?></div>
								<?foreach ($arResult["SKU_PROPERTIES"] as $propName => $arNextProp):?>
									<?if(!empty($arNextProp["VALUES"])):?>
										<?if($arNextProp["LIST_TYPE"] == "L" && $arNextProp["HIGHLOAD"] != "Y"):?>
											<div class="elementSkuProperty elementSkuDropDownProperty" data-name="<?=$propName?>" data-level="<?=$level++?>" data-highload="<?=$arNextProp["HIGHLOAD"]?>">
												<div class="elementSkuPropertyName"><?=preg_replace("/\[.*\]/", "", $arNextProp["NAME"])?>:</div>
												<div class="skuDropdown">
													<ul class="elementSkuPropertyList skuDropdownList">
														<?foreach ($arNextProp["VALUES"] as $xml_id => $arNextPropValue):?>
															<?if($arNextPropValue["SELECTED"] == "Y"):?>
																<?$currentSkuValue = $arNextPropValue["DISPLAY_VALUE"];?>
															<?endif;?>
															<li class="skuDropdownListItem elementSkuPropertyValue<?if($arNextPropValue["DISABLED"] == "Y"):?> disabled<?elseif($arNextPropValue["SELECTED"] == "Y"):?> selected<?endif;?>" data-name="<?=$propName?>" data-value="<?=$arNextPropValue["VALUE"]?>">
																<a href="#" class="elementSkuPropertyLink skuPropertyItemLink"><?=$arNextPropValue["DISPLAY_VALUE"]?></a>
															</li>
														<?endforeach;?>
													</ul>
													<span class="skuCheckedItem"><?=$currentSkuValue?></span>
												</div>
											</div>
										<?else:?>
											<div class="elementSkuProperty" data-name="<?=$propName?>" data-level="<?=$level++?>" data-highload="<?=$arNextProp["HIGHLOAD"]?>">
												<div class="elementSkuPropertyName"><?=preg_replace("/\[.*\]/", "", $arNextProp["NAME"])?>:<?
                                                if ($propName=='SIZE' && $arResult['PROPERTIES']['SIZE_CHART']['VALUES_LIST']){
                                                    ?><span class="size_select get_medi_popup_Window" data-id="<?=$arResult['PROPERTIES']['SIZE_CHART']['VALUE']?>" data-title="Подбор размера" data-img = "<?=$arResult['PROPERTIES']['SIZE_CHART']['VALUES_LIST']['IMG']['SRC'] ?>" data-svg = "<?=$arResult['PROPERTIES']['SIZE_CHART']['VALUES_LIST']['SVG']['SRC'] ?>"  id="GTM_size_map">подобрать размер</span><?
                                                    }
                                                ?></div>
												<ul class="elementSkuPropertyList">
													<?foreach ($arNextProp["VALUES"] as $xml_id => $arNextPropValue):?>
														<li class="elementSkuPropertyValue<?if($arNextPropValue["DISABLED"] == "Y"):?> disabled<?elseif($arNextPropValue["SELECTED"] == "Y"):?> selected<?endif;?>" data-name="<?=$propName?>" data-value="<?=$arNextPropValue["VALUE"]?>">
															<a href="#" class="elementSkuPropertyLink" <?if (!empty($arNextPropValue["IMAGE"])): ?>title="<?=$arNextPropValue["DISPLAY_VALUE"] ?>"<?endif; ?>>
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

						<div class="changePropertiesNoGroup">
							<?$APPLICATION->IncludeComponent(
								"dresscode:catalog.properties.list",
								"no-group",
								array(
									"PRODUCT_ID" => $arResult["ID"],
									"COUNT_PROPERTIES" => $countPropertyElements,
									"ELEMENT_LAST_SECTION_ID" => $arResult["LAST_SECTION"]["ID"]
								),
								false
							);?>
						</div>
					</div>
				</div>
				<div id="smallElementTools">
					<div class="smallElementToolsContainer">
						<?include($_SERVER["DOCUMENT_ROOT"]."/".$templateFolder."/include/right_section.php");?>
					</div>
				</div>

				<?// NOTE: TECHNOLOGIES?>
                <?
				if (!empty($arResult['PROPERTIES']['TECHNOLOGIES']['VALUES_LIST'])):?>
                <div id="tech">
                     <h2 class="heading"><?=GetMessage("CATALOG_ELEMENT_TECH_HEADING") ?></h2>
                        <div class="techList">
                        <?foreach($arResult['PROPERTIES']['TECHNOLOGIES']['VALUES_LIST'] AS $p => $tech)
                        {?>
							<div onfocus="focusClick()" onfocusout="focusOutClick()" tabindex="0" class="tooltip techItem">
							  <img src="<?=$tech['IMG']['src']; ?>" >
							  <span class="tooltip-text-hover">
								<p><?=htmlspecialchars($tech['UF_FULL_DESCRIPTION']); ?></p>

								<?if(!empty($tech['UF_LINK'])){ ?>
								<a class="tooltip-link" href="<?=$tech['UF_LINK']; ?>">Подробнее</a>
								<?}?>
							  </span>
							  <span class="tooltip-text-focus">
								<img onclick="closeTooltip()" class="close-button" src="<?=SITE_TEMPLATE_PATH?>/components/dresscode/catalog.item/detail/images/clear.png">
								<p><?=htmlspecialchars($tech['UF_FULL_DESCRIPTION']); ?></p>
								<?if(!empty($tech['UF_LINK'])){ ?>
								<a class="tooltip-link" href="<?=$tech['UF_LINK']; ?>">Подробнее</a>
								<?}?>
							  </span>
							</div>
                        <?}
                        ?>
                        </div>
                </div>
                <?endif; ?>

				<?// NOTE: SERVICES?>
				<?if($arParams["SHOW_SERVICES"] == "Y" && !empty($servicesFilter)):?>
					<?$APPLICATION->IncludeComponent(
						"dresscode:catalog.section",
						"services",
						array(
							"IBLOCK_TYPE" => $arParams["SERVICES_IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["SERVICES_IBLOCK_ID"],
							"CONVERT_CURRENCY" => "Y",
							"CURRENCY_ID" => $arResult["EXTRA_SETTINGS"]["CURRENCY"],
							"DISPLAY_HEADING" => "Y",
							"ADD_SECTIONS_CHAIN" => "N",
							"COMPONENT_TEMPLATE" => "services",
							"SECTION_ID" => $_REQUEST["SECTION_ID"],
							"SECTION_CODE" => "",
							"SECTION_USER_FIELDS" => array(
								0 => "",
								1 => "",
							),
							"ELEMENT_SORT_FIELD" => "sort",
							"ELEMENT_SORT_ORDER" => "asc",
							"ELEMENT_SORT_FIELD2" => "id",
							"ELEMENT_SORT_ORDER2" => "desc",
							"FILTER_NAME" => "servicesFilter",
							"INCLUDE_SUBSECTIONS" => "Y",
							"SHOW_ALL_WO_SECTION" => "Y",
							"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
							"PAGE_ELEMENT_COUNT" => "8",
							"LINE_ELEMENT_COUNT" => "3",
							"PROPERTY_CODE" => array(
								0 => "",
								1 => "",
							),
							"OFFERS_LIMIT" => "1",
							"BACKGROUND_IMAGE" => "-",
							"SECTION_URL" => "",
							"DETAIL_URL" => "",
							"SECTION_ID_VARIABLE" => "SECTION_ID",
							"SEF_MODE" => "N",
							"AJAX_MODE" => "N",
							"AJAX_OPTION_JUMP" => "N",
							"AJAX_OPTION_STYLE" => "Y",
							"AJAX_OPTION_HISTORY" => "N",
							"AJAX_OPTION_ADDITIONAL" => "undefined",
							"CACHE_TYPE" => "Y",
							"CACHE_TIME" => "36000000",
							"CACHE_GROUPS" => "Y",
							"SET_TITLE" => "N",
							"SET_BROWSER_TITLE" => "N",
							"BROWSER_TITLE" => "-",
							"SET_META_KEYWORDS" => "N",
							"META_KEYWORDS" => "-",
							"SET_META_DESCRIPTION" => "N",
							"META_DESCRIPTION" => "-",
							"SET_LAST_MODIFIED" => "N",
							"USE_MAIN_ELEMENT_SECTION" => "N",
							"CACHE_FILTER" => "Y",
							"ACTION_VARIABLE" => "action",
							"PRODUCT_ID_VARIABLE" => "id",
							"PRICE_CODE" => $arParams["PRODUCT_PRICE_CODE"],
							"USE_PRICE_COUNT" => "N",
							"SHOW_PRICE_COUNT" => "1",
							"PRICE_VAT_INCLUDE" => "Y",
							"BASKET_URL" => "/personal/cart/",
							"USE_PRODUCT_QUANTITY" => "N",
							"PRODUCT_QUANTITY_VARIABLE" => "undefined",
							"ADD_PROPERTIES_TO_BASKET" => "Y",
							"PRODUCT_PROPS_VARIABLE" => "prop",
							"PARTIAL_PRODUCT_PROPERTIES" => "N",
							"PRODUCT_PROPERTIES" => array(
							),
							"PAGER_TEMPLATE" => "round",
							"DISPLAY_TOP_PAGER" => "N",
							"DISPLAY_BOTTOM_PAGER" => "N",
							"PAGER_TITLE" => GetMessage("CATALOG_ELEMENT_ACCEESSORIES"),
							"PAGER_SHOW_ALWAYS" => "N",
							"PAGER_DESC_NUMBERING" => "N",
							"PAGER_DESC_NUMBERING_CACHE_TIME" => "3600000",
							"PAGER_SHOW_ALL" => "N",
							"PAGER_BASE_LINK_ENABLE" => "N",
							"SET_STATUS_404" => "N",
							"SHOW_404" => "N",
							"MESSAGE_404" => ""
						),
						false
					);?>
				<?endif;?>

				<?// NOTE: OFFERS?>
				<?if($arParams["DISPLAY_OFFERS_TABLE"] == "Y" && !empty($arResult["SKU_OFFERS"])):?>
					<?if(
						(!empty($arResult["SHOW_SKU_TABLE"]) && empty($arResult["PARENT_PRODUCT"]["PROPERTIES"]["SHOW_SKU_TABLE"])) ||
						(!empty($arResult["SHOW_SKU_TABLE"]) && !empty($arResult["PARENT_PRODUCT"]["PROPERTIES"]["SHOW_SKU_TABLE"]) && $arResult["PARENT_PRODUCT"]["PROPERTIES"]["SHOW_SKU_TABLE"]["VALUE_XML_ID"] == "Y") ||
						(!empty($arResult["PARENT_PRODUCT"]["PROPERTIES"]["SHOW_SKU_TABLE"]) && $arResult["PARENT_PRODUCT"]["PROPERTIES"]["SHOW_SKU_TABLE"]["VALUE_XML_ID"] == "Y")
					):?>
						<?$APPLICATION->IncludeComponent(
							"dresscode:catalog.product.offers",
							".default",
							array(
								"DISPLAY_PICTURE_COLUMN" => $arParams["OFFERS_TABLE_DISPLAY_PICTURE_COLUMN"],
								"NAV_COUNT_ELEMENTS" => $arParams["OFFERS_TABLE_PAGER_COUNT"],
								"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
								"PRODUCT_PRICE_CODE" => $arParams["PRODUCT_PRICE_CODE"],
								"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
								"PRODUCT_ID" => $arResult["PARENT_PRODUCT"]["ID"],
								"HIDE_MEASURES" => $arParams["HIDE_MEASURES"],
								"CURRENCY_ID" => $arParams["CURRENCY_ID"],
								"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
								"CACHE_TIME" => $arParams["CACHE_TIME"],
								"CACHE_TYPE" => $arParams["CACHE_TYPE"],
								"IBLOCK_ID" => $arParams["IBLOCK_ID"],
								"PAGER_TEMPLATE" => "round",
								"PAGER_NAV_HEADING" => "",
								"PICTURE_HEIGHT" => "50",
								"PICTURE_WIDTH" => "50",
							),
							false
						);?>
					<?endif;?>
				<?endif;?>
				<?// NOTE: COMPLECT?>
				<?if(!empty($arResult["COMPLECT"]["ITEMS"])):?>
					<div id="complect">
						<h2 class="heading"><?=GetMessage("ELEMENT_COMPLECT_HEADING")?></h2>
						<div class="complectList">
							<?foreach($arResult["COMPLECT"]["ITEMS"] as $inc => $arNextComplect):?>
								<div class="complectListItem">
									<div class="complectListItemWrap">
										<div class="complectListItemTable">
											<div class="complectListItemCelImage">
												<div class="complectListItemPicture">
													<a href="<?=$arNextComplect["DETAIL_PAGE_URL"]?>" class="complectListItemPicLink"><img src="<?=$arNextComplect["PICTURE"]["src"]?>" alt="<?=$arNextComplect["NAME"]?>"></a>
												</div>
											</div>
											<div class="complectListItemCelText">
												<div class="complectListItemName">
													<a href="<?=$arNextComplect["DETAIL_PAGE_URL"]?>" class="complectListItemLink"><span class="middle"><?=$arNextComplect["NAME"]?></span></a>
												</div>
												<a class="complectListItemPrice">
													<?=$arNextComplect["PRICE"]["PRICE_FORMATED"]?>
													<?if($arParams["HIDE_MEASURES"] != "Y" && !empty($arResult["MEASURES"][$arNextComplect["CATALOG_MEASURE"]]["SYMBOL_RUS"])):?>
														<span class="measure"> /<?if(!empty($arNextComplect["QUANTITY"]) && $arNextComplect["QUANTITY"] != 1):?> <?=$arNextComplect["QUANTITY"]?><?endif;?> <?=$arResult["MEASURES"][$arNextComplect["CATALOG_MEASURE"]]["SYMBOL_RUS"]?></span>
													<?endif;?>
													<?if($arNextComplect["PRICE"]["PRICE_DIFF"] > 0):?>
														<s class="discount"><?=$arNextComplect["PRICE"]["BASE_PRICE_FORMATED"]?></s>
													<?endif;?>
												</a>
											</div>
										</div>
									</div>
								</div>
							<?endforeach;?>
						</div>
						<div class="complectResult">
							<?=GetMessage("CATALOG_ELEMENT_COMPLECT_PRICE_RESULT")?>
							<div class="complectPriceResult"><?=CCurrencyLang::CurrencyFormat($arResult["PRICE"]["DISCOUNT_PRICE"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true)?></div>
							<?if(!empty($arResult["PRICE"]["DISCOUNT"])):?>
								<s class="discount"><?=CCurrencyLang::CurrencyFormat($arResult["PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true)?></s>
								<div class="complectResultEconomy">
									<?=GetMessage("CATALOG_ELEMENT_COMPLECT_ECONOMY")?> <span class="complectResultEconomyValue"><?=CCurrencyLang::CurrencyFormat($arResult["PRICE"]["RESULT_PRICE"]["DISCOUNT"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true)?></span>
								</div>
							<?endif;?>
						</div>
					</div>
				<?endif;?>
				<?// NOTE: GIFTS?>
				<?CBitrixComponent::includeComponentClass("bitrix:sale.products.gift");
					$APPLICATION->IncludeComponent(
						"bitrix:sale.products.gift",
						".default",
						array(
							"IBLOCK_ID" => $arParams["IBLOCK_ID"],
							"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
							"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
							"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
							"PRODUCT_ROW_VARIANTS" => "",
							"PAGE_ELEMENT_COUNT" => 8,
							"DEFERRED_PRODUCT_ROW_VARIANTS" => \Bitrix\Main\Web\Json::encode(
								SaleProductsGiftComponent::predictRowVariants(
									1,
									1
								)
							),
							"DEFERRED_PAGE_ELEMENT_COUNT" => 8,//$arParams["GIFTS_DETAIL_PAGE_ELEMENT_COUNT"],
							"SHOW_DISCOUNT_PERCENT" => $arParams["GIFTS_SHOW_DISCOUNT_PERCENT"],
							"DISCOUNT_PERCENT_POSITION" => $arParams["DISCOUNT_PERCENT_POSITION"],
							"SHOW_OLD_PRICE" => $arParams["GIFTS_SHOW_OLD_PRICE"],
							"PRODUCT_DISPLAY_MODE" => "Y",
							"PRODUCT_BLOCKS_ORDER" => $arParams["GIFTS_PRODUCT_BLOCKS_ORDER"],
							"TEXT_LABEL_GIFT" => $arParams["GIFTS_DETAIL_TEXT_LABEL_GIFT"],
							"LABEL_PROP_".$arParams["IBLOCK_ID"] => array(),
							"LABEL_PROP_MOBILE_".$arParams["IBLOCK_ID"] => array(),
							"LABEL_PROP_POSITION" => $arParams["LABEL_PROP_POSITION"],

							"ADD_TO_BASKET_ACTION" => (isset($arParams["ADD_TO_BASKET_ACTION"]) ? $arParams["ADD_TO_BASKET_ACTION"] : ""),
							"MESS_BTN_BUY" => $arParams["~GIFTS_MESS_BTN_BUY"],
							"MESS_BTN_ADD_TO_BASKET" => $arParams["~GIFTS_MESS_BTN_BUY"],
							"MESS_BTN_DETAIL" => $arParams["~MESS_BTN_DETAIL"],
							"MESS_BTN_SUBSCRIBE" => $arParams["~MESS_BTN_SUBSCRIBE"],

							"SHOW_PRODUCTS_".$arParams["IBLOCK_ID"] => "Y",
							"PROPERTY_CODE_".$arParams["IBLOCK_ID"] => $arParams["LIST_PROPERTY_CODE"],
							"PROPERTY_CODE_MOBILE".$arParams["IBLOCK_ID"] => $arParams["LIST_PROPERTY_CODE_MOBILE"],
							"PROPERTY_CODE_".$arResult["OFFERS_IBLOCK"] => $arParams["OFFER_TREE_PROPS"],
							"OFFER_TREE_PROPS_".$arResult["OFFERS_IBLOCK"] => $arParams["OFFER_TREE_PROPS"],
							"CART_PROPERTIES_".$arResult["OFFERS_IBLOCK"] => $arParams["OFFERS_CART_PROPERTIES"],
							"ADDITIONAL_PICT_PROP_".$arParams["IBLOCK_ID"] => (isset($arParams["ADD_PICT_PROP"]) ? $arParams["ADD_PICT_PROP"] : ""),
							"ADDITIONAL_PICT_PROP_".$arResult["OFFERS_IBLOCK"] => (isset($arParams["OFFER_ADD_PICT_PROP"]) ? $arParams["OFFER_ADD_PICT_PROP"] : ""),
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
							"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE"],
							"PRODUCT_SUBSCRIPTION" => $arParams["PRODUCT_SUBSCRIPTION"],
							"TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
							"PRICE_CODE" => $arParams["PRODUCT_PRICE_CODE"],
							"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
							"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
							"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
							"BASKET_URL" => $arParams["BASKET_URL"],
							"ADD_PROPERTIES_TO_BASKET" => $arParams["ADD_PROPERTIES_TO_BASKET"],
							"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
							"PARTIAL_PRODUCT_PROPERTIES" => $arParams["PARTIAL_PRODUCT_PROPERTIES"],
							"USE_PRODUCT_QUANTITY" => "N",
							"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
							"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
							"POTENTIAL_PRODUCT_TO_BUY" => array(
								"ID" => isset($arResult["ID"]) ? $arResult["ID"] : null,
								"MODULE" => isset($arResult["MODULE"]) ? $arResult["MODULE"] : "catalog",
								"PRODUCT_PROVIDER_CLASS" => isset($arResult["PRODUCT_PROVIDER_CLASS"]) ? $arResult["PRODUCT_PROVIDER_CLASS"] : "CCatalogProductProvider",
								"QUANTITY" => isset($arResult["QUANTITY"]) ? $arResult["QUANTITY"] : null,
								"IBLOCK_ID" => isset($arResult["IBLOCK_ID"]) ? $arResult["IBLOCK_ID"] : null,

								"PRIMARY_OFFER_ID" => isset($arResult["OFFERS"][$arResult["OFFERS_SELECTED"]]["ID"])
									? $arResult["OFFERS"][$arResult["OFFERS_SELECTED"]]["ID"]
									: null,
								"SECTION" => array(
									"ID" => isset($arResult["SECTION"]["ID"]) ? $arResult["SECTION"]["ID"] : null,
									"IBLOCK_ID" => isset($arResult["SECTION"]["IBLOCK_ID"]) ? $arResult["SECTION"]["IBLOCK_ID"] : null,
									"LEFT_MARGIN" => isset($arResult["SECTION"]["LEFT_MARGIN"]) ? $arResult["SECTION"]["LEFT_MARGIN"] : null,
									"RIGHT_MARGIN" => isset($arResult["SECTION"]["RIGHT_MARGIN"]) ? $arResult["SECTION"]["RIGHT_MARGIN"] : null,
								),
							),

							"USE_ENHANCED_ECOMMERCE" => $arParams["USE_ENHANCED_ECOMMERCE"],
							"DATA_LAYER_NAME" => $arParams["DATA_LAYER_NAME"],
							"BRAND_PROPERTY" => $arParams["BRAND_PROPERTY"]
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);
				?>
				<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.set.constructor",
	".default",
	array(
		"ELEMENT_ID" => $arResult["ID"],
		"CURRENCY_ID" => "RUB",
		"PRICE_CODE" => array(
		),
		"IBLOCK_ID" => "1",
		"OFFERS_CART_PROPERTIES" => "",
		"BASKET_URL" => "/personal/cart/",
		"CACHE_TIME" => "36000000",
		"PRICE_VAT_INCLUDE" => "N",
		"CONVERT_CURRENCY" => "Y",
		"CACHE_GROUPS" => "Y",
		"CACHE_TYPE" => "Y",
		"COMPONENT_TEMPLATE" => ".default",
		"IBLOCK_TYPE_ID" => "catalog",
		"BUNDLE_ITEMS_COUNT" => "3"
	),
	false
);?>
				<?if(!empty($arResult["DETAIL_TEXT"])):?>
					<div id="detailText">
						<h2 class="heading"><?=GetMessage("CATALOG_ELEMENT_DETAIL_TEXT_HEADING")?></h2>
						<div class="changeDescription" data-first-value='<?=str_replace("'", "", $arResult["~DETAIL_TEXT"])?>'><?=$arResult["~DETAIL_TEXT"]?></div>
					</div>
				<?endif;?>
				<div class="changePropertiesGroup">
					<?$APPLICATION->IncludeComponent(
	"dresscode:catalog.properties.list",
	"group",
	array(
		"PRODUCT_ID" => $arResult["ID"],
		"ELEMENT_LAST_SECTION_ID" => $arResult["LAST_SECTION"]["ID"],
		"COMPONENT_TEMPLATE" => "group",
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "1",
		"PROP_NAME" => "",
		"PROP_VALUE" => "",
		"ELEMENTS_COUNT" => "20",
		"POP_LAST_ELEMENT" => "Y",
		"SELECT_FIELDS" => array(
			0 => "",
			1 => "*",
			2 => "",
		),
		"SORT_PARAMS_VALUE" => 6000,
		"SORT_PROPERTY_NAME" => "7",
		"SORT_VALUE" => "ASC",
		"PICTURE_WIDTH" => "200",
		"PICTURE_HEIGHT" => "140",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "360000"
	),
	false
);?>
				</div>
				<?if(!empty($arResult["ELEMENT_TAGS"]) && !empty($arParams["CATALOG_SHOW_TAGS"]) && $arParams["CATALOG_SHOW_TAGS"] == "Y"):?>
					<?$index = 1;?>
					<div id="detailTags"<?if($arParams["HIDE_TAGS_ON_MOBILE"] == "Y"):?> class="mobileHidden"<?endif;?>>
						<h2 class="heading"><?=GetMessage("CATALOG_ELEMENT_DETAIL_TAGS_HEADING")?></h2>
						<div class="detailTagsItems">
							<?foreach($arResult["ELEMENT_TAGS"] as $tagIndex => $nextTag):?>
								<div class="detailTagsItem<?if($arParams["MAX_VISIBLE_TAGS_DESKTOP"] < $index):?> desktopHidden<?endif;?><?if($arParams["MAX_VISIBLE_TAGS_MOBILE"] < $index):?> mobileHidden<?endif;?>">
									<a href="<?=$nextTag["LINK"]?>" class="detailTagsLink<?if(!empty($nextTag["SELECTED"]) && $nextTag["SELECTED"] == "Y"):?> selected<?endif;?>"><?=$nextTag["NAME"]?><?if(!empty($nextTag["SELECTED"]) && $nextTag["SELECTED"] == "Y"):?><span class="reset">&#10006;</span><?endif;?></a>
								</div>
								<?$index++;?>
							<?endforeach;?>
							<?if(count($arResult["ELEMENT_TAGS"]) > $arParams["MAX_VISIBLE_TAGS_MOBILE"] || count($arResult["ELEMENT_TAGS"]) > $arParams["MAX_VISIBLE_TAGS_DESKTOP"]):?>
								<div class="detailTagsItem moreButton<?if($arParams["MAX_VISIBLE_TAGS_DESKTOP"] > count($arResult["ELEMENT_TAGS"])):?> desktopHidden<?endif;?><?if($arParams["MAX_VISIBLE_TAGS_MOBILE"] > count($arResult["ELEMENT_TAGS"])):?> mobileHidden<?endif;?>"><a href="#" class="detailTagsLink moreButtonLink" data-last-label="<?=GetMessage("CATALOG_ELEMENT_TAGS_MORE_BUTTON_HIDE");?>"><?=GetMessage("CATALOG_ELEMENT_TAGS_MORE_BUTTON")?></a></div>
							<?endif;?>
						</div>
					</div>
				<?endif;?>

				<?// NOTE: RELATED?>
		        <?if($arResult["SHOW_RELATED"] == "Y"):?>
		        	<div id="related" class="productsListName" data-list-name="<?=GetMessage("CATALOG_ELEMENT_ACCEESSORIES")?>" >
						<h2 class="heading"><?=GetMessage("CATALOG_ELEMENT_ACCEESSORIES")?> (<?=$arResult["RELATED_COUNT"] <= 8 ? $arResult["RELATED_COUNT"] : 8?>)</h2>
						<?$APPLICATION->IncludeComponent(
						"dresscode:catalog.section",
						"squares",
						array(
							"IBLOCK_TYPE" => "catalog",
							"IBLOCK_ID" => "17",
							"CONVERT_CURRENCY" => "Y",
							"CURRENCY_ID" => "RUB",
							"ADD_SECTIONS_CHAIN" => "N",
							"COMPONENT_TEMPLATE" => "squares",
							"SECTION_ID" => $_REQUEST["SECTION_ID"],
							"SECTION_CODE" => "",
							"SECTION_USER_FIELDS" => array(
								0 => "",
								1 => "",
							),
							"ELEMENT_SORT_FIELD" => "sort",
							"ELEMENT_SORT_ORDER" => "asc",
							"ELEMENT_SORT_FIELD2" => "id",
							"ELEMENT_SORT_ORDER2" => "desc",
							"FILTER_NAME" => "relatedFilter",
							"INCLUDE_SUBSECTIONS" => "Y",
							"SHOW_ALL_WO_SECTION" => "Y",
							"HIDE_NOT_AVAILABLE" => "Y",
							"PAGE_ELEMENT_COUNT" => "4",
							"LINE_ELEMENT_COUNT" => "3",
							"PROPERTY_CODE" => array(
								0 => "",
								1 => "",
							),
							"OFFERS_LIMIT" => "1",
							"BACKGROUND_IMAGE" => "-",
							"SECTION_URL" => "",
							"DETAIL_URL" => "",
							"SECTION_ID_VARIABLE" => "SECTION_ID",
							"SEF_MODE" => "N",
							"AJAX_MODE" => "N",
							"AJAX_OPTION_JUMP" => "N",
							"AJAX_OPTION_STYLE" => "Y",
							"AJAX_OPTION_HISTORY" => "N",
							"AJAX_OPTION_ADDITIONAL" => "undefined",
							"CACHE_TYPE" => "Y",
							"CACHE_TIME" => "36000000",
							"CACHE_GROUPS" => "Y",
							"SET_TITLE" => "N",
							"SET_BROWSER_TITLE" => "N",
							"BROWSER_TITLE" => "-",
							"SET_META_KEYWORDS" => "N",
							"META_KEYWORDS" => "-",
							"SET_META_DESCRIPTION" => "N",
							"META_DESCRIPTION" => "-",
							"SET_LAST_MODIFIED" => "N",
							"USE_MAIN_ELEMENT_SECTION" => "N",
							"CACHE_FILTER" => "Y",
							"ACTION_VARIABLE" => "action",
							"PRODUCT_ID_VARIABLE" => "id",
							"PRICE_CODE" => array(
								0 => "BASE",
							),
							"USE_PRICE_COUNT" => "N",
							"SHOW_PRICE_COUNT" => "1",
							"PRICE_VAT_INCLUDE" => "Y",
							"BASKET_URL" => "/personal/cart/",
							"USE_PRODUCT_QUANTITY" => "N",
							"PRODUCT_QUANTITY_VARIABLE" => "undefined",
							"ADD_PROPERTIES_TO_BASKET" => "Y",
							"PRODUCT_PROPS_VARIABLE" => "prop",
							"PARTIAL_PRODUCT_PROPERTIES" => "N",
							"PRODUCT_PROPERTIES" => "",
							"PAGER_TEMPLATE" => "round",
							"DISPLAY_TOP_PAGER" => "N",
							"DISPLAY_BOTTOM_PAGER" => "N",
							"PAGER_TITLE" => GetMessage("CATALOG_ELEMENT_ACCEESSORIES"),
							"PAGER_SHOW_ALWAYS" => "N",
							"PAGER_DESC_NUMBERING" => "N",
							"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
							"PAGER_SHOW_ALL" => "N",
							"PAGER_BASE_LINK_ENABLE" => "N",
							"SET_STATUS_404" => "N",
							"SHOW_404" => "N",
							"MESSAGE_404" => "",
							"LAZY_LOAD_PICTURES" => "Y",
							"HIDE_MEASURES" => "Y",
							"DISABLE_INIT_JS_IN_COMPONENT" => "N"
						),
						false
					);?>
					</div>
				<?endif;?>

				<?// NOTE: REVIEWS?>
		        <?if(isset($arResult["REVIEWS"])):?>
		        	<div id="catalogReviews">
				        <h2 class="heading"><?=GetMessage("REVIEW")?> (<?=count($arResult["REVIEWS"])?>) <?if($arParams["SHOW_REVIEW_FORM"]):?><a href="#" class="reviewAddButton"><?=GetMessage("REVIEWS_ADD")?></a><?endif;?></h2>
				        <ul id="reviews">
				            <?foreach($arResult["REVIEWS"] as $i => $arReview):
								$date_publish = $arReview["DATE_CREATE"];
								if ($arReview["ACTIVE_FROM"] != '' ) { $date_publish = $arReview["ACTIVE_FROM"]; } ?>
				                <li class="reviewItem<?if($i > 2):?> hide<?endif?>">
				                    <div class="reviewTable">
				                    	<div class="reviewColumn">
				                    		<div class="reviewDate">
						                        <div class="label"><?=GetMessage("REVIEWS_DATE")?></div> <?=FormatDate(array(
						                        "tommorow" => "tommorow",
						                        "today" => "today",
						                        "yesterday" => "yesterday",
						                        "d" => 'j F',
						                         "" => 'j F Y',
						                        ), MakeTimeStamp($date_publish, "DD.MM.YYYY HH:MI:SS"));
						                        ?>
				                    		</div>
					                    	<div class="reviewName">
					                    		<div class="label"><?=GetMessage("REVIEWS_AUTHOR")?></div> <?=$arReview["PROPERTY_NAME_VALUE"]?>
					                    	</div>

							            </div>
				                    	<div class="reviewColumn">
	                		                <?if(!empty($arReview["~PROPERTY_DIGNITY_VALUE"])):?>
		                		                <div class="advantages">
								                    <span class="label"><?=GetMessage("DIGNIFIED")?> </span>
								                    <p><?=$arReview["~PROPERTY_DIGNITY_VALUE"]?></p>
								                </div>
							                <?endif;?>
							                <?if(!empty($arReview["~PROPERTY_SHORTCOMINGS_VALUE"])):?>
								                <div class="limitations">
								                    <span class="label"><?=GetMessage("FAULTY")?> </span>
								                    <p><?=$arReview["~PROPERTY_SHORTCOMINGS_VALUE"]?></p>
								                </div>
							                <?endif;?>
							                <?if(!empty($arReview["~DETAIL_TEXT"])):?>
								                <div class="impressions">
								                    <span class="label"><?=GetMessage("IMPRESSION")?></span>
								                    <p><?=$arReview["~DETAIL_TEXT"]?></p>
								                </div>
							                <?endif;?>

											<?if($arReview['~PROPERTY_ANSWER_VALUE']['TEXT']){?>
											<div class=" reviewAnswer">
												<a class="reviewAnswerAuthor">
													Ответ специалиста:
												</a>

												<div class="reviewAnswerText">
													<?=$arReview['~PROPERTY_ANSWER_VALUE']['TEXT']?>
												</div>
											</div>
											<?}?>

				                    		<div class="controls">
						                        <span><?=GetMessage("REVIEWSUSEFUL")?></span>
						                        <a href="#" class="good" data-id="<?=$arReview["ID"]?>"><?=GetMessage("YES")?> (<span><?=!empty($arReview["PROPERTY_GOOD_REVIEW_VALUE"]) ? $arReview["PROPERTY_GOOD_REVIEW_VALUE"] : 0 ?></span>)</a>
						                        <a href="#" class="bad" data-id="<?=$arReview["ID"]?>"><?=GetMessage("NO")?> (<span><?=!empty($arReview["PROPERTY_BAD_REVIEW_VALUE"]) ? $arReview["PROPERTY_BAD_REVIEW_VALUE"] : 0 ?></span>)</a>
						                    </div>
				                    	</div>


				                    </div>

				                </li>
				            <?endforeach;?>
				        </ul>
				        <?if(count($arResult["REVIEWS"]) > 3):?><a href="#" id="showallReviews" data-open="N"><?=GetMessage("SHOWALLREVIEWS")?></a><?endif;?>
			      	</div>
			    <?endif;?>
				<?// NOTE: REVIEW_FORM?>
		        <?if($USER->IsAuthorized()):?>
		            <?if($arParams["SHOW_REVIEW_FORM"]):?>
			            <div id="newReview">
			                <h2 class="heading"><?=GetMessage("ADDAREVIEW")?></h2>
			                <form action="" method="GET">
			                    <div class="newReviewTable">
			                    	<div class="left">
										<label><?=GetMessage("EXPERIENCE")?></label>
										<?if(!empty($arResult["NEW_REVIEW"]["EXPERIENCE"])):?>
										    <ul class="usedSelect">
										        <?foreach ($arResult["NEW_REVIEW"]["EXPERIENCE"] as $arExp):?>
										            <li><a href="#" data-id="<?=$arExp["ID"]?>"><?=$arExp["VALUE"]?></a></li>
										        <?endforeach;?>
										    </ul>
										<?endif;?>
										<label><?=GetMessage("DIGNIFIED")?></label>
										<textarea rows="10" cols="45" name="DIGNITY"  maxlength="2000"></textarea>
			                    	</div>
			                    	<div class="right">
										<label><?=GetMessage("FAULTY")?></label>
										<textarea rows="10" cols="45" name="SHORTCOMINGS"  maxlength="2000"></textarea>
										<label><?=GetMessage("IMPRESSION")?></label>
										<textarea rows="10" cols="45" name="COMMENT"  maxlength="2000"></textarea>
										<label><?=GetMessage("INTRODUCEYOURSELF")?></label>
										<input type="text" name="NAME"  maxlength="50"><a href="#" class="submit" data-id="<?=$arParams["REVIEW_IBLOCK_ID"]?>"><?=GetMessage("SENDFEEDBACK")?></a>
			                    	</div>
			                    </div>
			                    <input type="hidden" name="USED" id="usedInput" value="" />
			                    <input type="hidden" name="RATING" id="ratingInput" value="0"/>
			                    <input type="hidden" name="PRODUCT_NAME" value="<?=$arResult["NAME"]?>"/>
			                    <input type="hidden" name="PRODUCT_ID" value="<?if(!empty($arResult["PARENT_PRODUCT"])):?><?=$arResult["PARENT_PRODUCT"]["ID"]?><?else:?><?=$arResult["ID"]?><?endif;?>"/>
			                </form>
			            </div>
			        <?endif;?>
		        <?endif;?>

				<?// NOTE: SIMILAR ?>
				<?if($arResult["SHOW_SIMILAR"] == "Y"):?>
		        	<div id="similar" class="productsListName" data-list-name="<?=GetMessage("CATALOG_ELEMENT_SIMILAR")?>"  >
						<h2 class="heading"><?=GetMessage("CATALOG_ELEMENT_SIMILAR")?> (<?=$arResult["SIMILAR_COUNT"] <= 8 ? $arResult["SIMILAR_COUNT"] : 8?>)</h2>
						<?$APPLICATION->IncludeComponent(
	"dresscode:catalog.section",
	"squares",
	array(
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "17",
		"CONVERT_CURRENCY" => "Y",
		"CURRENCY_ID" => "RUB",
		"ADD_SECTIONS_CHAIN" => "N",
		"COMPONENT_TEMPLATE" => "squares",
		"SECTION_ID" => $_REQUEST["SECTION_ID"],
		"SECTION_CODE" => "",
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"ELEMENT_SORT_FIELD" => "shows",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_SORT_FIELD2" => "sort",
		"ELEMENT_SORT_ORDER2" => "desc",
		"FILTER_NAME" => "similarFilter",
		"INCLUDE_SUBSECTIONS" => "Y",
		"SHOW_ALL_WO_SECTION" => "Y",
		"HIDE_NOT_AVAILABLE" => "Y",
		"PAGE_ELEMENT_COUNT" => "4",
		"LINE_ELEMENT_COUNT" => "3",
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"OFFERS_LIMIT" => "1",
		"BACKGROUND_IMAGE" => "-",
		"SECTION_URL" => "",
		"DETAIL_URL" => "",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"SEF_MODE" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "undefined",
		"CACHE_TYPE" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "Y",
		"SET_TITLE" => "N",
		"SET_BROWSER_TITLE" => "N",
		"BROWSER_TITLE" => "-",
		"SET_META_KEYWORDS" => "N",
		"META_KEYWORDS" => "-",
		"SET_META_DESCRIPTION" => "N",
		"META_DESCRIPTION" => "-",
		"SET_LAST_MODIFIED" => "N",
		"USE_MAIN_ELEMENT_SECTION" => "N",
		"CACHE_FILTER" => "Y",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRICE_CODE" => array(
			0 => "BASE",
		),
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"BASKET_URL" => "/personal/cart/",
		"USE_PRODUCT_QUANTITY" => "N",
		"PRODUCT_QUANTITY_VARIABLE" => "undefined",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRODUCT_PROPERTIES" => "",
		"PAGER_TEMPLATE" => "round",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => GetMessage("CATALOG_ELEMENT_SIMILAR"),
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"SET_STATUS_404" => "N",
		"SHOW_404" => "N",
		"MESSAGE_404" => "",
		"LAZY_LOAD_PICTURES" => "Y",
		"HIDE_MEASURES" => "Y",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N"
	),
	false
);?>
					</div>
				<?endif;?>

				<?// NOTE: STORES AMOUNT;?>
				<?if($arParams["HIDE_AVAILABLE_TAB"] != "Y" && !$arResult['DONT_SHOW_REST']):?>
					<div id="storesContainer">
						<?$arStoresParams = array(
								"COMPONENT_TEMPLATE" => ".default",
								"STORES" => array(
								),
								"ELEMENT_ID" => !empty($arResult["PARENT_PRODUCT"]["ID"]) ? $arResult["PARENT_PRODUCT"]["ID"] : $arResult["ID"],
								"OFFER_ID" => !empty($arResult["PARENT_PRODUCT"]["ID"]) ? $arResult["ID"] : "",
								"ELEMENT_CODE" => "",
								"STORE_PATH" => "/stores/#store_code#/",
								"CACHE_TYPE" => "Y",
								"CACHE_TIME" => "36000000",
								"MAIN_TITLE" => "",
								"USER_FIELDS" => array(
									0 => "",
									1 => "",
								),
								"FIELDS" => array(
									0 => "TITLE",
									1 => "ADDRESS",
									2 => "DESCRIPTION",

									4 => "EMAIL",
									5 => "IMAGE_ID",
									6 => "COORDINATES",
									7 => "SCHEDULE",
									8 => "",
								),
								"SHOW_EMPTY_STORE" => "Y",
								"USE_MIN_AMOUNT" => "Y",
								"SHOW_GENERAL_STORE_INFORMATION" => "N",
								"MIN_AMOUNT" => "0",
								"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
								"IBLOCK_ID" => $arParams["IBLOCK_ID"],
							);
						?>
						<?$APPLICATION->IncludeComponent(
							"dresscode:catalog.store.amount",
							".default",
							$arStoresParams,
							false
						);?>
					</div>
					<script type="text/javascript">
						var elementStoresComponentParams = <?=\Bitrix\Main\Web\Json::encode($arStoresParams)?>;
					</script>
				<?endif;?>
				<?// NOTE: FILES?>
				<?if(!empty($arResult['PROPERTIES']["DOCS"])):?>
				<div id="files">
					<h2 class="heading">Документы</h2>
					<div class="wrap">
	 					<div class="items">
							<?foreach ($arResult['PROPERTIES']["DOCS"] as $ifl => $arFile):
								if (!is_file($_SERVER['DOCUMENT_ROOT'].$arFile['FILE']["SRC"])) continue;
						 ?>
								<?
									if($arFile['FILE']["CONTENT_TYPE"] == "application/pdf"){
										$fileType = "Pdf";
									}elseif($arFile['FILE']["CONTENT_TYPE"] == "application/msword" || $arFile['FILE']["CONTENT_TYPE"] == "application/vnd.openxmlformats-officedocument.wordprocessingml.document"){
										$fileType = "Word";
									}elseif($arFile['FILE']["CONTENT_TYPE"] == "image/jpeg" || $arFile['FILE']["CONTENT_TYPE"] == "image/png"){
										$fileType = "Image";
									}elseif($arFile['FILE']["CONTENT_TYPE"] == "text/plain"){
										$fileType = "Text";
									}elseif($arFile['FILE']["CONTENT_TYPE"] == "application/vnd.ms-excel" || $arFile['FILE']["CONTENT_TYPE"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
										$fileType = "Excel";
									}else{
										$fileType = "";
									}
								?>
								<div class="item">
									<div class="tb">
										<div class="tbr">
											<div class="icon">
												<a href="<?=$arFile['FILE']["SRC"]?>">
													<img src="<?=SITE_TEMPLATE_PATH?>/images/file<?=$fileType?>.png" alt="<?=$arFile["NAME"]?>">
												</a>
											</div>
											<div class="info">
												<a href="<?=$arFile['FILE']["SRC"]?>" class="name" target="_blank" title="<?=$arFile["NAME"]?> - <?=$arResult["NAME"]?> "><span><?=preg_replace("/\[.*\]/", "", trim($arFile["NAME"]))?></span></a>
												<small class="parentName"><?=CFile::FormatSize($arFile['FILE']["FILE_SIZE"])?>, <?=strtolower($fileType)?></small>
											</div>
										</div>
									</div>
								</div>
							<?endforeach;?>
						</div>
					</div>
				</div>
				<?endif;?>
				<?if(!empty($arResult["VIDEO"])):?>
					<div id="video">
						<h2 class="heading"><?=GetMessage("VIDEO_HEADING")?></h2>
						<div class="wrap">
							<div class="items sz<?=count($arResult["VIDEO"])?>">
								<?foreach ($arResult["VIDEO"] as $ivp => $videoValue):?>
									<div class="item">
										<iframe src="<?=$videoValue?>" allowfullscreen class="videoFrame"></iframe>
									</div>
								<?endforeach;?>
							</div>
						</div>
					</div>
				<?endif;?>
			</div>
			<div id="elementTools" class="column">
				<div class="fixContainer">
					<?if(!empty($arResult["EXTRA_SETTINGS"]["SHOW_TIMER"])):?>
						<div class="specialTime smallSpecialTime" id="timer_<?=$arResult["EXTRA_SETTINGS"]["TIMER_UNIQ_ID"];?>_<?=$uniqID?>">
							<div class="specialTimeItem">
								<div class="specialTimeItemValue timerDayValue">0</div>
								<div class="specialTimeItemlabel"><?=GetMessage("TIMER_DAY_LABEL")?></div>
							</div>
							<div class="specialTimeItem">
								<div class="specialTimeItemValue timerHourValue">0</div>
								<div class="specialTimeItemlabel"><?=GetMessage("TIMER_HOUR_LABEL")?></div>
							</div>
							<div class="specialTimeItem">
								<div class="specialTimeItemValue timerMinuteValue">0</div>
								<div class="specialTimeItemlabel"><?=GetMessage("TIMER_MINUTE_LABEL")?></div>
							</div>
							<div class="specialTimeItem">
								<div class="specialTimeItemValue timerSecondValue">0</div>
								<div class="specialTimeItemlabel"><?=GetMessage("TIMER_SECOND_LABEL")?></div>
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
					<?require($_SERVER["DOCUMENT_ROOT"]."/".$templateFolder."/include/right_section.php");?>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="elementError">
  <div id="elementErrorContainer">
    <span class="heading"><?=GetMessage("ERROR")?></span>
    <a href="#" id="elementErrorClose"></a>
    <p class="message"></p>
    <a href="#" class="close"><?=GetMessage("CLOSE")?></a>
  </div>
</div>
<div class="cheaper-product-name"><?=$arResult["NAME"]?></div>
<?if(!empty($arParams["DISPLAY_CHEAPER"]) && $arParams["DISPLAY_CHEAPER"] == "Y"):?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:form.result.new",
		"modal",
		array(
			"CACHE_TIME" => "3600000",
			"CACHE_TYPE" => "Y",
			"CHAIN_ITEM_LINK" => "",
			"CHAIN_ITEM_TEXT" => "",
			"EDIT_URL" => "result_edit.php",
			"IGNORE_CUSTOM_TEMPLATE" => "N",
			"LIST_URL" => "result_list.php",
			"SEF_MODE" => "N",
			"SUCCESS_URL" => "",
			"USE_EXTENDED_ERRORS" => "N",
			"WEB_FORM_ID" => $arParams["CHEAPER_FORM_ID"],
			"COMPONENT_TEMPLATE" => "modal",
			"MODAL_BUTTON_NAME" => "",
			"MODAL_BUTTON_CLASS_NAME" => "cheaper label hidden changeID".(empty($arResult["PRICE"]) || $arResult["CATALOG_AVAILABLE"] != "Y" ? " disabled" : ""),
			"VARIABLE_ALIASES" => array(
				"WEB_FORM_ID" => "WEB_FORM_ID",
				"RESULT_ID" => "RESULT_ID",
			)
		),
		false
	);?>
<?endif;?>
<div itemscope itemtype="http://schema.org/Product" class="microdata">
	<meta itemprop="name" content="<?=$arResult["NAME"]?>" />
	<link itemprop="url" href="<?=$arResult["DETAIL_PAGE_URL"]?>" />
	<link itemprop="image" href="<?=$arResult["IMAGES"][0]["LARGE_IMAGE"]["SRC"]?>" />
	<meta itemprop="brand" content="<?=$arResult["BRAND"]["NAME"]?>" />
	<meta itemprop="model" content="<?=$arResult["PROPERTIES"]["CML2_ARTICLE"]["VALUE"]?>" />
	<meta itemprop="productID" content="<?=$arResult["ID"]?>" />
	<meta itemprop="category" content="<?=$arResult["SECTION"]["NAME"]?>" />
	<?/*if(!empty($arResult["PROPERTIES"]["RATING"]["VALUE"])):?>
		<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
			<meta itemprop="ratingValue" content="<?=$arResult["PROPERTIES"]["RATING"]["VALUE"]?>">
			<meta itemprop="reviewCount" content="<?=intval($arResult["PROPERTIES"]["VOTE_COUNT"]["VALUE"])?>">
		</div>
	<?endif;*/?>
	<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
		<meta itemprop="priceCurrency" content="<?=$arResult["EXTRA_SETTINGS"]["CURRENCY"]?>" />
		<meta itemprop="price" content="<?=$arResult["PRICE"]["DISCOUNT_PRICE"]?>" />
		<link itemprop="url" href="<?=$arResult["DETAIL_PAGE_URL"]?>" />
		<?if($arResult["CATALOG_QUANTITY"] > 0):?>
            <link itemprop="availability" href="http://schema.org/InStock">
        <?else:?>
       		<link itemprop="availability" href="http://schema.org/OutOfStock">
        <?endif;?>
	</div>
	<?if(!empty($arResult["PREVIEW_TEXT"])):?>
		<meta itemprop="description" content='<?=$arResult["PREVIEW_TEXT"]?>' />
	<?endif;?>
	<?if(empty($arResult["PREVIEW_TEXT"]) && !empty($arResult["DETAIL_TEXT"])):?>
		<meta itemprop="description" content='<?=$arResult["DETAIL_TEXT"]?>' />
	<?endif;?>
</div>

<script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js" charset="utf-8"></script>
<script src="//yastatic.net/share2/share.js" charset="utf-8"></script>
<script type="text/javascript">

	var CATALOG_LANG = {
		REVIEWS_HIDE: "<?=GetMessage("REVIEWS_HIDE")?>",
		REVIEWS_SHOW: "<?=GetMessage("REVIEWS_SHOW")?>",
		OLD_PRICE_LABEL: "<?=GetMessage("OLD_PRICE_LABEL")?>",
	};

	var elementAjaxPath = "<?=$templateFolder."/ajax.php"?>";
	var _topMenuNoFixed = true;

</script>

<?
$secturl = explode("/", $arResult['DETAIL_PAGE_URL']);
$sectcount = count($secturl) - 1;
unset($secturl[$sectcount]); unset($secturl[0]);unset($secturl[1]);
//наименование списка товаров, из которого перешел пользователь: каталог, просмотренные, с этим покупают, похожие изделия, хит продаж, распродажа
//__($_SERVER);
?>

<script>
window.dataLayer = window.dataLayer || [];
dataLayer.push({
'ecommerce': {
  'currencyCode': 'RUB',
  'detail': {
    'products': [{
     'name': '<?=!empty($arResult["PARENT_PRODUCT"]["NAME"]) ? $arResult["PARENT_PRODUCT"]["NAME"] : $arResult["NAME"]?>',
      'id': '<?=(!empty($arResult["PARENT_PRODUCT"]["ID"]) ? $arResult["PARENT_PRODUCT"]["ID"] : $arResult["ID"])?>',
      'price': '<?=$arResult["PRICE"]["RESULT_PRICE"]["BASE_PRICE"]?>',
      'brand': '<?=$arResult["BRAND"]["NAME"]?>',
      'category': '<?=implode("/",$secturl);?>',
      'variant': '<?=$arResult["PROPERTIES"]["CML2_ARTICLE"]["VALUE"]?>',
     }]
  },
},
'event': 'gtm-ee-event',
'gtm-ee-event-category': 'Enhanced Ecommerce',
'gtm-ee-event-action': 'Product Details',
'gtm-ee-event-non-interaction': 'True',
});
dataLayer.push({ 'event': 'view_item', 'value': parseInt(<?=$arResult["PRICE"]["RESULT_PRICE"]["BASE_PRICE"]?>), 'items': [ { 'id': '<?=(!empty($arResult["PARENT_PRODUCT"]["ID"]) ? $arResult["PARENT_PRODUCT"]["ID"] : $arResult["ID"])?>', 'google_business_vertical': 'retail' } ] });
</script>
