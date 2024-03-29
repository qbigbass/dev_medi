<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;

/** @var array $arCurrentValues */
/** @global CUserTypeManager $USER_FIELD_MANAGER */
global $USER_FIELD_MANAGER;

if (!Loader::includeModule("iblock"))
	return;

if (!Loader::includeModule("search"))
	return;

$boolCatalog = Loader::includeModule("catalog");

$arIBlockType = CIBlockParameters::GetIBlockTypes();
$arIBlock = array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array(
	"TYPE" => $arCurrentValues["IBLOCK_TYPE"],
	"ACTIVE" => "Y",
));
while ($arr = $rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arProperty = array();
$arProperty_LNS = array();
$arProperty_N = array();
$arProperty_X = array();
if ($arCurrentValues['IBLOCK_ID'] > 0)
{
	$rsProp = CIBlockProperty::GetList(array(
		"sort" => "asc",
		"name" => "asc",
	), array(
		"IBLOCK_ID" => $arCurrentValues["IBLOCK_ID"],
		"ACTIVE" => "Y",
	));
	while ($arr = $rsProp->Fetch())
	{
		if ($arr["PROPERTY_TYPE"] != "F")
			$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];

		if ($arr["PROPERTY_TYPE"] == "N")
			$arProperty_N[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];

		if ($arr["PROPERTY_TYPE"] != "F")
		{
			if ($arr["MULTIPLE"] == "Y")
				$arProperty_X[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
			elseif ($arr["PROPERTY_TYPE"] == "L")
				$arProperty_X[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
			elseif ($arr["PROPERTY_TYPE"] == "E" && $arr["LINK_IBLOCK_ID"] > 0)
				$arProperty_X[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
		}
	}
}

$arProperty_UF = array();
$arSProperty_LNS = array();
$arUserFields = $USER_FIELD_MANAGER->GetUserFields("IBLOCK_".$arCurrentValues["IBLOCK_ID"]."_SECTION", 0, LANGUAGE_ID);
foreach ($arUserFields as $FIELD_NAME => $arUserField)
{
	$arUserField['LIST_COLUMN_LABEL'] = (string)$arUserField['LIST_COLUMN_LABEL'];
	$arProperty_UF[$FIELD_NAME] = $arUserField['LIST_COLUMN_LABEL'] ? '['.$FIELD_NAME.']'.$arUserField['LIST_COLUMN_LABEL'] : $FIELD_NAME;
	if ($arUserField["USER_TYPE"]["BASE_TYPE"] == "string")
		$arSProperty_LNS[$FIELD_NAME] = $arProperty_UF[$FIELD_NAME];
}

$arOffers = CIBlockPriceTools::GetOffersIBlock($arCurrentValues["IBLOCK_ID"]);
$OFFERS_IBLOCK_ID = is_array($arOffers) ? $arOffers["OFFERS_IBLOCK_ID"] : 0;
$arProperty_Offers = array();
if ($OFFERS_IBLOCK_ID)
{
	$rsProp = CIBlockProperty::GetList(array(
		"sort" => "asc",
		"name" => "asc",
	), array(
		"IBLOCK_ID" => $OFFERS_IBLOCK_ID,
		"ACTIVE" => "Y",
	));
	while ($arr = $rsProp->Fetch())
	{
		if ($arr["PROPERTY_TYPE"] != "F")
			$arProperty_Offers[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}

$arSort = CIBlockParameters::GetElementSortFields(
	array('SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO'),
	array('KEY_LOWERCASE' => 'Y')
);

$arPrice = array();
if ($boolCatalog)
{
	$arSort = array_merge($arSort, CCatalogIBlockParameters::GetCatalogSortFields());
	$rsPrice = CCatalogGroup::GetList($v1 = "sort", $v2 = "asc");
	while ($arr = $rsPrice->Fetch())
		$arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];
}
else
{
	$arPrice = $arProperty_N;
}


$arSort1 = $arSort;

$saleModuleInfo = CModule::CreateModuleObject('main');
if(CheckVersion($saleModuleInfo->MODULE_VERSION, '19.5.0'))
{
	$arSort1["rank"] = Loc::getMessage("SEARCH_RANK_SORT"); 
}

$arAscDesc = array(
	"asc" => Loc::getMessage("CP_BCSE_SORT_ASC"),
	"desc" => Loc::getMessage("CP_BCSE_SORT_DESC"),
);
$arComponentParameters = array(
	"GROUPS" => array(
		"PRICES" => array(
			"NAME" => Loc::getMessage("CP_BCSE_GROUPS_PRICES"),
		),
		"SEARCH" => array(
			"NAME" => Loc::getMessage("CP_BCSE_GROUPS_SEARCH"),
		),
	),
	"PARAMETERS" => array(
		"AJAX_MODE" => array(),
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage("CP_BCSE_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage("CP_BCSE_IBLOCK_ID"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"ELEMENT_SORT_FIELD" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => Loc::getMessage("CP_BCSE_ELEMENT_SORT_FIELD"),
			"TYPE" => "LIST",
			"VALUES" => $arSort1,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "sort",
		),
		"ELEMENT_SORT_ORDER" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => Loc::getMessage("IBLOCK_ELEMENT_SORT_ORDER"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "asc",
			"ADDITIONAL_VALUES" => "Y",
		),
		"ELEMENT_SORT_FIELD2" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => Loc::getMessage("CP_BCSE_ELEMENT_SORT_FIELD2"),
			"TYPE" => "LIST",
			"VALUES" => $arSort,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "id",
		),
		"ELEMENT_SORT_ORDER2" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => Loc::getMessage("IBLOCK_ELEMENT_SORT_ORDER2"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "desc",
			"ADDITIONAL_VALUES" => "Y",
		),
		"SECTION_URL" => CIBlockParameters::GetPathTemplateParam("SECTION", "SECTION_URL", Loc::getMessage("IBLOCK_SECTION_URL"), "", "URL_TEMPLATES"),
		"DETAIL_URL" => CIBlockParameters::GetPathTemplateParam("DETAIL", "DETAIL_URL", Loc::getMessage("IBLOCK_DETAIL_URL"), "", "URL_TEMPLATES"),
		"BASKET_URL" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => Loc::getMessage("IBLOCK_BASKET_URL"),
			"TYPE" => "STRING",
			"DEFAULT" => "/personal/basket.php",
		),
		"ACTION_VARIABLE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => Loc::getMessage("IBLOCK_ACTION_VARIABLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "action",
		),
		"PRODUCT_ID_VARIABLE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => Loc::getMessage("IBLOCK_PRODUCT_ID_VARIABLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "id",
		),
		"PRODUCT_QUANTITY_VARIABLE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => Loc::getMessage("CP_BCS_PRODUCT_QUANTITY_VARIABLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "quantity",
		),
		"PRODUCT_PROPS_VARIABLE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => Loc::getMessage("CP_BCS_PRODUCT_PROPS_VARIABLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "prop",
		),
		"SECTION_ID_VARIABLE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => Loc::getMessage("IBLOCK_SECTION_ID_VARIABLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "SECTION_ID",
		),
		"DISPLAY_COMPARE" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => Loc::getMessage("T_IBLOCK_DESC_DISPLAY_COMPARE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"PAGE_ELEMENT_COUNT" => array(
			"PARENT" => "VISUAL",
			"NAME" => Loc::getMessage("IBLOCK_PAGE_ELEMENT_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "30",
		),
		"LINE_ELEMENT_COUNT" => array(
			"PARENT" => "VISUAL",
			"NAME" => Loc::getMessage("IBLOCK_LINE_ELEMENT_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "3",
		),
		"PROPERTY_CODE" => array(
			"PARENT" => "VISUAL",
			"NAME" => Loc::getMessage("CP_BCS_PROPERTY_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty,
			"ADDITIONAL_VALUES" => "Y",
		),
		"OFFERS_FIELD_CODE" => CIBlockParameters::GetFieldCode(Loc::getMessage("CP_BCS_OFFERS_FIELD_CODE"), "VISUAL"),
		"OFFERS_PROPERTY_CODE" => array(
			"PARENT" => "VISUAL",
			"NAME" => Loc::getMessage("CP_BCS_OFFERS_PROPERTY_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty_Offers,
			"ADDITIONAL_VALUES" => "Y",
		),
		"OFFERS_SORT_FIELD" => array(
			"PARENT" => "VISUAL",
			"NAME" => Loc::getMessage("CP_BCS_OFFERS_SORT_FIELD"),
			"TYPE" => "LIST",
			"VALUES" => $arSort,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "sort",
		),
		"OFFERS_SORT_ORDER" => array(
			"PARENT" => "VISUAL",
			"NAME" => Loc::getMessage("CP_BCS_OFFERS_SORT_ORDER"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "asc",
			"ADDITIONAL_VALUES" => "Y",
		),
		"OFFERS_SORT_FIELD2" => array(
			"PARENT" => "VISUAL",
			"NAME" => Loc::getMessage("CP_BCS_OFFERS_SORT_FIELD2"),
			"TYPE" => "LIST",
			"VALUES" => $arSort,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "id",
		),
		"OFFERS_SORT_ORDER2" => array(
			"PARENT" => "VISUAL",
			"NAME" => Loc::getMessage("CP_BCS_OFFERS_SORT_ORDER2"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "desc",
			"ADDITIONAL_VALUES" => "Y",
		),
		"OFFERS_LIMIT" => array(
			"PARENT" => "VISUAL",
			"NAME" => Loc::getMessage('CP_BCS_OFFERS_LIMIT'),
			"TYPE" => "STRING",
			"DEFAULT" => 5,
		),
		"PRICE_CODE" => array(
			"PARENT" => "PRICES",
			"NAME" => Loc::getMessage("IBLOCK_PRICE_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arPrice,
		),
		"USE_PRICE_COUNT" => array(
			"PARENT" => "PRICES",
			"NAME" => Loc::getMessage("IBLOCK_USE_PRICE_COUNT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"SHOW_PRICE_COUNT" => array(
			"PARENT" => "PRICES",
			"NAME" => Loc::getMessage("IBLOCK_SHOW_PRICE_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "1",
		),
		"PRICE_VAT_INCLUDE" => array(
			"PARENT" => "PRICES",
			"NAME" => Loc::getMessage("IBLOCK_VAT_INCLUDE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"PRODUCT_PROPERTIES" => array(
			"PARENT" => "PRICES",
			"NAME" => Loc::getMessage("CP_BCS_PRODUCT_PROPERTIES"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty_X,
		),
		"USE_PRODUCT_QUANTITY" => array(
			"PARENT" => "PRICES",
			"NAME" => Loc::getMessage("CP_BCS_USE_PRODUCT_QUANTITY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"CACHE_TIME" => array(
			"DEFAULT" => 36000000,
		),
		"CHECK_DATES" => array(
			"PARENT" => "SEARCH",
			"NAME" => Loc::getMessage("SEARCH_CHECK_DATES"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"USE_LANGUAGE_GUESS" => array(
			"PARENT" => "SEARCH",
			"NAME" => Loc::getMessage("CP_BSP_USE_LANGUAGE_GUESS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"INPUT_PLACEHOLDER" => array(
			"PARENT" => "SEARCH",
			"NAME" => Loc::getMessage("TP_BSP_INPUT_PLACEHOLDER"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"SHOW_HISTORY" => array(
			"PARENT" => "SEARCH",
			"NAME" => Loc::getMessage("SEARCH_SHOW_HISTORY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
	),
);
CIBlockParameters::AddPagerSettings($arComponentParameters, Loc::getMessage("T_IBLOCK_DESC_PAGER_CATALOG"), true, true);

if ($boolCatalog)
{
	$arComponentParameters["PARAMETERS"]['HIDE_NOT_AVAILABLE'] = array(
		'PARENT' => 'DATA_SOURCE',
		'NAME' => Loc::getMessage('CP_BCS_HIDE_NOT_AVAILABLE'),
		'TYPE' => 'LIST',
		'DEFAULT' => 'N',
		'VALUES' => array(
			'Y' => Loc::getMessage('CP_BCS_HIDE_NOT_AVAILABLE_HIDE'),
			'L' => Loc::getMessage('CP_BCS_HIDE_NOT_AVAILABLE_LAST'),
			'N' => Loc::getMessage('CP_BCS_HIDE_NOT_AVAILABLE_SHOW')
		),
		'ADDITIONAL_VALUES' => 'N'
	);
	$arComponentParameters['PARAMETERS']['HIDE_NOT_AVAILABLE_OFFERS'] = array(
		'PARENT' => 'DATA_SOURCE',
		'NAME' => Loc::getMessage('CP_BCS_HIDE_NOT_AVAILABLE_OFFERS'),
		'TYPE' => 'LIST',
		'DEFAULT' => 'N',
		'VALUES' => array(
			'Y' => Loc::getMessage('CP_BCS_HIDE_NOT_AVAILABLE_OFFERS_HIDE'),
			'L' => Loc::getMessage('CP_BCS_HIDE_NOT_AVAILABLE_OFFERS_SUBSCRIBE'),
			'N' => Loc::getMessage('CP_BCS_HIDE_NOT_AVAILABLE_OFFERS_SHOW')
		)
	);
	if (Loader::includeModule('currency'))
	{
		$arComponentParameters["PARAMETERS"]['CONVERT_CURRENCY'] = array(
			'PARENT' => 'PRICES',
			'NAME' => Loc::getMessage('CP_BCS_CONVERT_CURRENCY'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
		);
		if (isset($arCurrentValues['CONVERT_CURRENCY']) && 'Y' == $arCurrentValues['CONVERT_CURRENCY'])
		{
			$arComponentParameters['PARAMETERS']['CURRENCY_ID'] = array(
				'PARENT' => 'PRICES',
				'NAME' => Loc::getMessage('CP_BCS_CURRENCY_ID'),
				'TYPE' => 'LIST',
				'VALUES' => \Bitrix\Currency\CurrencyManager::getCurrencyList(),
				'DEFAULT' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
				"ADDITIONAL_VALUES" => "Y",
			);
		}
	}
}

if (!$OFFERS_IBLOCK_ID)
{
	unset($arComponentParameters["PARAMETERS"]["OFFERS_FIELD_CODE"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_PROPERTY_CODE"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_SORT_FIELD"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_SORT_ORDER"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_SORT_FIELD2"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_SORT_ORDER2"]);
}
else
{
	unset($arComponentParameters["PARAMETERS"]["PRODUCT_PROPERTIES"]);
	$arComponentParameters["PARAMETERS"]["OFFERS_CART_PROPERTIES"] = array(
		"PARENT" => "PRICES",
		"NAME" => Loc::getMessage("CP_BCS_OFFERS_CART_PROPERTIES"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arProperty_Offers,
	);
}