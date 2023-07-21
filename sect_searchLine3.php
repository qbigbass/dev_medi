<?$APPLICATION->IncludeComponent(
	"dresscode:search.line",
	"version3",
	array(
		"IBLOCK_ID" => 17,
		"IBLOCK_TYPE" => "catalog",
		"NUM_CATEGORIES" => "1",
		"TOP_COUNT" => "5",
		"CHECK_DATES" => "N",
		"SHOW_OTHERS" => "N",
		"PAGE" => "#SITE_DIR#search/index.php",
		"CATEGORY_0_TITLE" => "",
		"CATEGORY_0" => array(
			0 => "iblock_catalog",
		),
		"CATEGORY_0_iblock_catalog" => array(
			0 => "17",
		),
		"CATEGORY_OTHERS_TITLE" => "Прочее",
		"SHOW_INPUT" => "Y",
		"INPUT_ID" => "searchQuery",
		"CONTAINER_ID" => "topSearch3",
		"PRICE_CODE" => array(
			0 => SITE_ID == 's2'? "BASE_SPB" :"BASE",
		),
		"SHOW_PREVIEW" => "Y",
		"PREVIEW_WIDTH" => "75",
		"PREVIEW_HEIGHT" => "75",
		"CONVERT_CURRENCY" => "Y",
		"COMPONENT_TEMPLATE" => "version4",
		"ORDER" => "rank",
		"USE_LANGUAGE_GUESS" => "Y",
		"PRICE_VAT_INCLUDE" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "",
		"CURRENCY_ID" => "RUB",
		"FILTER_NAME" => "",
		"SHOW_PREVIEW_TEXT" => "Y",
		"CATEGORY_0_iblock_offers" => array(
			0 => "3",
		),
		"SHOW_PROPS" => "",
		"ANIMATE_HINTS" => array(
		),
		"ANIMATE_HINTS_SPEED" => "1"
	),
	false
);?>
