<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

//d7 namespace
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use Bitrix\Iblock;
use Bitrix\Currency;

if(CModule::IncludeModule("iblock") && CModule::IncludeModule("sale") && CModule::IncludeModule("catalog")){

	$arPrice = CCatalogIBlockParameters::getPriceTypesList();
	$arComponentParameters = array(
		"GROUPS" => array(
			"PAYMENT" => array(
				"NAME" => GetMessage("PAYMENT"),
				"SORT" => "10"
			),
			"BASKET_PICTURE" => array(
				"NAME" => GetMessage("BASKET_PICTURE"),
				"SORT" => "100"
			),
			"GIFT" => array(
				"NAME" => GetMessage("GIFT_PARAMS"),
				"SORT" => "200"
			),
			"LABELS" => array(
				"NAME" => GetMessage("LABELS"),
				"SORT" => "300"
			),
		),
		"PARAMETERS" => array(
			"REGISTER_USER" => array(
				"PARENT" => "BASE",
				"NAME" => GetMessage("REGISTER_USER"),
				"TYPE" => "CHECKBOX",
				"REFRESH" => "Y"
			),
			"PATH_TO_PAYMENT" => array(
				"PARENT" => "PAYMENT",
				"NAME" => GetMessage("PATH_TO_PAYMENT"),
				"TYPE" => "STRING"
			),
			"MIN_SUM_TO_PAYMENT" => array(
				"PARENT" => "PAYMENT",
				"NAME" => GetMessage("MIN_SUM_TO_PAYMENT"),
				"TYPE" => "STRING"
			),
			"BASKET_PICTURE_WIDTH" => array(
				"PARENT" => "BASKET_PICTURE",
				"NAME" => GetMessage("BASKET_PICTURE_WIDTH"),
				"TYPE" => "STRING"
			),
			"BASKET_PICTURE_HEIGHT" => array(
		         "PARENT" => "BASKET_PICTURE",
		         "NAME" => GetMessage("BASKET_PICTURE_HEIGHT"),
		         "TYPE" => "STRING"
			),
			"LAZY_LOAD_PICTURES" => array(
				"PARENT" => "PICTURE",
				"NAME" => GetMessage("LAZY_LOAD_PICTURES"),
				"TYPE" => "CHECKBOX",
				"REFRESH" => "Y"
			),
			"HIDE_MEASURES" => array(
				"PARENT" => "BASE",
				"NAME" => GetMessage("HIDE_MEASURES"),
				"TYPE" => "CHECKBOX",
				"REFRESH" => "Y"
			),
			"HIDE_NOT_AVAILABLE" => array(
				"PARENT" => "GIFT",
				"NAME" => GetMessage("HIDE_NOT_AVAILABLE"),
				"TYPE" => "CHECKBOX",
				"REFRESH" => "Y"
			),
			"PRODUCT_PRICE_CODE" => array(
				"PARENT" => "GIFT",
				"NAME" => GetMessage("IBLOCK_PRICE_CODE_GIFT"),
				"TYPE" => "LIST",
				"MULTIPLE" => "Y",
				"VALUES" => $arPrice,
			),
			"PART_STORES_AVAILABLE" => array(
		         "PARENT" => "LABELS",
		         "NAME" => GetMessage("PART_STORES_AVAILABLE"),
		         "TYPE" => "STRING"
			),
			"ALL_STORES_AVAILABLE" => array(
		         "PARENT" => "LABELS",
		         "NAME" => GetMessage("ALL_STORES_AVAILABLE"),
		         "TYPE" => "STRING"
			),
			"NO_STORES_AVAILABLE" => array(
		         "PARENT" => "LABELS",
		         "NAME" => GetMessage("NO_STORES_AVAILABLE"),
		         "TYPE" => "STRING"
			),
			"USE_MASKED" => array(
				"PARENT" => "BASE",
				"NAME" => GetMessage("USE_MASKED"),
				"TYPE" => "CHECKBOX",
				"REFRESH" => "Y"
			),
			"DISABLE_FAST_ORDER" => array(
				"PARENT" => "BASE",
				"NAME" => GetMessage("DISABLE_FAST_ORDER"),
				"TYPE" => "CHECKBOX",
				"REFRESH" => "Y"
			),
			"CACHE_TIME" => Array("DEFAULT" => "36000000"),
		)
	);

	$arComponentParameters["PARAMETERS"]["GIFT_CONVERT_CURRENCY"] = array(
		"PARENT" => "GIFT",
		"NAME" => GetMessage("GIFT_CONVERT_CURRENCY"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "Y",
	);

	if(isset($arCurrentValues["GIFT_CONVERT_CURRENCY"]) && $arCurrentValues["GIFT_CONVERT_CURRENCY"] === "Y"){
		$arComponentParameters["PARAMETERS"]["GIFT_CURRENCY_ID"] = array(
			"PARENT" => "GIFT",
			"NAME" => GetMessage("GIFT_CURRENCY_ID"),
			"TYPE" => "LIST",
			"VALUES" => Currency\CurrencyManager::getCurrencyList(),
			"DEFAULT" => Currency\CurrencyManager::getBaseCurrency(),
			"ADDITIONAL_VALUES" => "Y",
		);
	}

	if(isset($arCurrentValues["USE_MASKED"]) && $arCurrentValues["USE_MASKED"] === "Y"){
		$arComponentParameters["PARAMETERS"]["MASKED_FORMAT"] = array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MASKED_FORMAT"),
			"TYPE" => "LIST",
			"VALUES" => array("+7 (999) 999-99-99" => "+7 (999) 999-99-99 (ru)", "+380 (999) 999-99-99" => "+380 (999) 999-99-99 (ua)", "+375 (999) 999-99-99" => "+375 (999) 999-99-99 (by)"),
			"DEFAULT" => "+7 (999) 999-99-99",
			"ADDITIONAL_VALUES" => "Y",
		);
	}
}
?>
