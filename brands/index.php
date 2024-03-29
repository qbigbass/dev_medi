<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Бренды");?>
<?$GLOBALS['arrBrandFilter'] = ['!PROPERTY_SHOW' => false, "PROPERTY_REGION_VALUE"=> $GLOBALS['medi']['region_cities'][SITE_ID]];?>
<?$APPLICATION->IncludeComponent(
	"bitrix:news",
	"brands",
	array(
		"IBLOCK_TYPE" => "info",
		"IBLOCK_ID" => "1",
		"NEWS_COUNT" => "20",
		"USE_SEARCH" => "N",
		"USE_RSS" => "N",
		"USE_RATING" => "N",
		"USE_CATEGORIES" => "N",
		"USE_REVIEW" => "N",
		"USE_FILTER" => "Y",
		"SORT_BY1" => "SORT",
		"SORT_ORDER1" => "ASC",
		"SORT_BY2" => "TIMESTAMP_X",
		"SORT_ORDER2" => "DESC",
		"CHECK_DATES" => "Y",
		"SEF_MODE" => "Y",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "8640000",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "N",
		"SET_STATUS_404" => "Y",
		"SET_TITLE" => "Y",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"ADD_ELEMENT_CHAIN" => "Y",
		"USE_PERMISSIONS" => "N",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"USE_SHARE" => "N",
		"PREVIEW_TRUNCATE_LEN" => "",
		"LIST_ACTIVE_DATE_FORMAT" => "",
		"LIST_FIELD_CODE" => array(
			0 => "ID",
			1 => "CODE",
			2 => "XML_ID",
			3 => "NAME",
			4 => "TAGS",
			5 => "SORT",
			6 => "PREVIEW_TEXT",
			7 => "PREVIEW_PICTURE",
			8 => "DETAIL_TEXT",
			9 => "DETAIL_PICTURE",
			10 => "",
		),
		"LIST_PROPERTY_CODE" => array(
			0 => "OFFERS",
			1 => "",
		),
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"DISPLAY_NAME" => "Y",
		"META_KEYWORDS" => "-",
		"META_DESCRIPTION" => "-",
		"BROWSER_TITLE" => "-",
		"DETAIL_ACTIVE_DATE_FORMAT" => "",
		"DETAIL_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"DETAIL_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"DETAIL_DISPLAY_TOP_PAGER" => "N",
		"DETAIL_DISPLAY_BOTTOM_PAGER" => "Y",
		"DETAIL_PAGER_TITLE" => "Страница",
		"DETAIL_PAGER_TEMPLATE" => "",
		"DETAIL_PAGER_SHOW_ALL" => "Y",
		"PAGER_TEMPLATE" => "round",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Бренды",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "3600000",
		"PAGER_SHOW_ALL" => "N",
		"TAGS_CLOUD_ELEMENTS" => "150",
		"PERIOD_NEW_TAGS" => "",
		"DISPLAY_AS_RATING" => "rating",
		"FONT_MAX" => "50",
		"FONT_MIN" => "10",
		"COLOR_NEW" => "3E74E6",
		"COLOR_OLD" => "C0C0C0",
		"TAGS_CLOUD_WIDTH" => "100%",
		"SEF_FOLDER" => "/brands/",
		"AJAX_OPTION_ADDITIONAL" => "",
		"COMPONENT_TEMPLATE" => "brands",
		"SET_LAST_MODIFIED" => "N",
		"DETAIL_SET_CANONICAL_URL" => "N",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"SHOW_404" => "Y",
		"MESSAGE_404" => "",
		"PRODUCT_IBLOCK_TYPE" => "catalog",
		"PRODUCT_IBLOCK_ID" => "17",
		"PRODUCT_FILTER_NAME" => "arrFilter",
		"FILTER_NAME" => "arrBrandFilter",
		"PRODUCT_PROPERTY_CODE" => array(
			0 => "CML2_ARTICLE",
			1 => "PRODUCT_TYPE",
			2 => "KK",
			3 => "SKU_COLOR",
			4 => "ATT_BRAND",
			5 => "COUNTRY_BRAND",
			6 => "COUNTRY_MANUFACTURE",
			7 => "FOR_WHO",
			8 => "",
		),
		"PRODUCT_PRICE_CODE" => array(
			0 => $GLOBALS['medi']['price'][SITE_ID],
		),
		"PRODUCT_CONVERT_CURRENCY" => "N",
		"PRODUCT_CURRENCY_ID" => "RUB",
		"HIDE_NOT_AVAILABLE" => "Y",
		"FILTER_PRICE_CODE" => array(
			0 =>$GLOBALS['medi']['price'][SITE_ID],
		),
		"HIDE_MEASURES" => "Y",
		"STRICT_SECTION_CHECK" => "N",
		"FILE_404" => "",
		"SEF_URL_TEMPLATES" => array(
			"news" => "",
			"section" => "",
			"detail" => "#ELEMENT_CODE#/",
		)
	),
	false
);?><?$APPLICATION->IncludeComponent(
	"dresscode:slider",
	"middle",
	array(
		"IBLOCK_TYPE" => "slider",
		"IBLOCK_ID" => "9",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600000",
		"PICTURE_WIDTH" => "1476",
		"PICTURE_HEIGHT" => "202",
		"COMPONENT_TEMPLATE" => "middle"
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
