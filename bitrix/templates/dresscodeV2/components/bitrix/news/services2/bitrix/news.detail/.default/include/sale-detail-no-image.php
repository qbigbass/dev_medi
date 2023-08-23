<div class="global-block-container">
	<div class="global-content-block">
		<div class="blog-banner banner-no-image">
			<div class="banner-elem">
				<div class="tb">
					<div class="text-wrap tc">
						<div class="tb">
							<div class="tr">
								<div class="tc">
									<?if(!empty($arResult["DISPLAY_PROPERTIES"]["STOCK_DATE"]["VALUE"])):?>
										<div class="date"><?=$arResult["DISPLAY_PROPERTIES"]["STOCK_DATE"]["VALUE"];?></div>
									<?endif;?>
									<?if(!empty($arResult["NAME"])):?>
										<h1 class="ff-medium"><?=$arResult["NAME"]?></h1>
									<?endif;?>
									<?/*if(!empty($arResult["PREVIEW_TEXT"])):?>
										<div class="descr"><?=$arResult["PREVIEW_TEXT"]?></div>
									<?endif;*/?>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="detail-text-wrap">
			<?=$arResult["DETAIL_TEXT"]?>

			<?//__($arResult);
			if ($arResult['PROPERTIES']['WEBFORM_LIST']['VALUE'] != "" && $arResult['PROPERTIES']['WEBFORM_LIST']['VALUE_SORT'] > 0 && $arResult['FORM_ID'] > 0 && $arResult['FORM_IN_TEXT'] != 1):?>
				<div class="serviceWebForm">
					<?
					// Форма бронирования товара в салоне
					$APPLICATION->IncludeComponent(
						"bitrix:form.result.new",
						 $arResult['PROPERTIES']['WEBFORM_LIST']['VALUE_XML_ID'],
						array(
							"AJAX_MODE" => "N",
							"AJAX_OPTION_ADDITIONAL" => "",
							"AJAX_OPTION_HISTORY" => "N",
							"AJAX_OPTION_JUMP" => "N",
							"AJAX_OPTION_STYLE" => "Y",
							"CACHE_TIME" => "3600",
							"CACHE_TYPE" => "N",
							"CHAIN_ITEM_LINK" => "",
							"CHAIN_ITEM_TEXT" => "",
							"COMPOSITE_FRAME_MODE" => "A",
							"COMPOSITE_FRAME_TYPE" => "AUTO",
							"EDIT_ADDITIONAL" => "N",
							"EDIT_STATUS" => "N",
							"IGNORE_CUSTOM_TEMPLATE" => "N",
							"NOT_SHOW_FILTER" => array(
								0 => "",
								1 => "",
							),
							"NOT_SHOW_TABLE" => array(
								0 => "",
								1 => "",
							),
							"RESULT_ID" => $_REQUEST[RESULT_ID],
							"SEF_MODE" => "N",
							"SHOW_ADDITIONAL" => "N",
							"SHOW_ANSWER_VALUE" => "Y",
							"SHOW_EDIT_PAGE" => "N",
							"SHOW_LIST_PAGE" => "N",
							"SHOW_STATUS" => "N",
							"SHOW_VIEW_PAGE" => "N",
							"START_PAGE" => "new",
							"SUCCESS_URL" => "",
							"HIDDEN_FIELDS" => array(
								0 => "AGREE",
							),
							"USE_EXTENDED_ERRORS" => "Y",
							"WEB_FORM_ID" => $arResult['PROPERTIES']['WEBFORM_LIST']['VALUE_SORT'],
							"COMPONENT_TEMPLATE" => $arResult['PROPERTIES']['WEBFORM_LIST']['VALUE_XML_ID'],
							"LIST_URL" => "",
							"EDIT_URL" => "",
							"VARIABLE_ALIASES" => array(
								"WEB_FORM_ID" => "WEB_FORM_ID",
								"RESULT_ID" => "RESULT_ID",
							)
						),
						false
					);
					?>
				</div>
			<?endif;?>
			<div class="btn-simple-wrap">
				<a href="<?=$arResult["LIST_PAGE_URL"]?>" class="btn-simple btn-micro"><?=GetMessage("NEWS_BACK")?></a>
			</div>
			<br>
		</div>
	</div>
	<?/*global $arrFilter; $arrFilter["!ID"] = $arResult["ID"];
	$arrFilter["PROPERTY_CITY_VALUE"] = $GLOBALS['medi']['region_cities'][SITE_ID];?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:news.list",
		"serviceDetail",
		array_merge($arParams, array("CHECK_DATES" => "Y", "NEWS_COUNT" => 3, "FILTER_NAME" => "arrFilter", "INCLUDE_IBLOCK_INTO_CHAIN" => "N", "ADD_SECTIONS_CHAIN" => "N", "ADD_ELEMENT_CHAIN" => "N", "SET_TITLE" => "N", "DISPLAY_TOP_PAGER" => "N", "DISPLAY_BOTTOM_PAGER" => "N")),
		$component
	);*/?>
</div>

<script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js" charset="utf-8"></script>
<script src="//yastatic.net/share2/share.js" charset="utf-8"></script>
