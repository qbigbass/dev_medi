<?//check empty request
if (empty($_POST["SEARCH_QUERY"])) {
    die();
} ?>
<? if (!empty($_POST["SITE_ID"])) {
    define("SITE_ID", $_POST["SITE_ID"]);
} ?>
<? define("STOP_STATISTICS", true); ?>
<? define("NO_AGENT_CHECK", true); ?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); ?>
<? include($_SERVER["DOCUMENT_ROOT"] . "/" . $APPLICATION->GetCurDir() . "lang/" . LANGUAGE_ID . "/ajax.php"); ?>
<? if (!CModule::IncludeModule("iblock") || !CModule::IncludeModule("search")) {
    die("Include modules error, search, iblock");
}
$GLOBALS['searchFilter'] = [
    "PROPERTY_REGION_VALUE" => $GLOBALS['medi']['region_cities'][SITE_ID],
    "CATALOG_AVAILABLE" => "Y",
    ">CATALOG_QUANTITY" => 0
];
//globals
global $arrFilter; ?>
    <div style="display: none">
        <? $_REQUEST['q'] = $_REQUEST['SEARCH_QUERY'];
        $arIDS = $APPLICATION->IncludeComponent(
            "arturgolubev:search.page",
            "catalog",
            array(
                "ACTION_VARIABLE" => "action",
                "AJAX_MODE" => "N",
                "AJAX_OPTION_ADDITIONAL" => "",
                "AJAX_OPTION_HISTORY" => "N",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "Y",
                "BASKET_URL" => "/personal/cart",
                "CACHE_TIME" => "36000000",
                "CACHE_TYPE" => "A",
                "CHECK_DATES" => "Y",
                "COMPOSITE_FRAME_MODE" => "A",
                "COMPOSITE_FRAME_TYPE" => "AUTO",
                "CONVERT_CURRENCY" => "N",
                "DEFAULT_SORT" => "rank",
                "DETAIL_URL" => "",
                "DISPLAY_COMPARE" => "N",
                "DISPLAY_BOTTOM_PAGER" => "N",
                "DISPLAY_TOP_PAGER" => "N",
                "ELEMENT_SORT_FIELD" => "RANK",
                "ELEMENT_SORT_FIELD2" => "sort",
                "ELEMENT_SORT_ORDER" => "asc",
                "ELEMENT_SORT_ORDER2" => "asc",
                "FILTER_NAME" => "searchFilter",
                "HIDE_NOT_AVAILABLE" => "Y",
                "HIDE_NOT_AVAILABLE_OFFERS" => "Y",
                "IBLOCK_ID" => "17",
                "IBLOCK_TYPE" => "catalog",
                "INPUT_PLACEHOLDER" => "",
                "LINE_ELEMENT_COUNT" => "6",
                "NO_WORD_LOGIC" => "N",
                "OFFERS_CART_PROPERTIES" => array("CML2_ARTICLE", "COLOR", "SIZE", "LENGTH", "ELASTIC_TIPS2", "HEIGHT", "WIDE_THIGH", "SIDE", "CUP", "WIDTH"),
                "OFFERS_FIELD_CODE" => array("", ""),
                "OFFERS_LIMIT" => "5",
                "OFFERS_PROPERTY_CODE" => array("CML2_ARTICLE", ""),
                "OFFERS_SORT_FIELD" => "RANK",
                "OFFERS_SORT_FIELD2" => "sort",
                "OFFERS_SORT_ORDER" => "desc",
                "OFFERS_SORT_ORDER2" => "asc",
                "PAGER_DESC_NUMBERING" => "N",
                "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                "PAGER_SHOW_ALL" => "N",
                "PAGER_SHOW_ALWAYS" => "N",
                "PAGER_TEMPLATE" => "round",
                "PAGER_TITLE" => "Товары",
                "PAGE_ELEMENT_COUNT" => "6",
                "PAGE_RESULT_COUNT" => "1800",
                "PRICE_CODE" => array(),
                "PRICE_VAT_INCLUDE" => "Y",
                "PRODUCT_ID_VARIABLE" => "id",
                "PRODUCT_PROPERTIES" => array(),
                "PRODUCT_PROPS_VARIABLE" => "prop",
                "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                "PROPERTY_CODE" => array("CML2_ARTICLE", ""),
                "SECTION_ID_VARIABLE" => "SECTION_ID",
                "SECTION_URL" => "",
                "SHOW_HISTORY" => "Y",
                "SHOW_PRICE_COUNT" => "1",
                "SHOW_WHEN" => "N",
                "SHOW_WHERE" => "N",
                "USE_LANGUAGE_GUESS" => "Y",
                "USE_PRICE_COUNT" => "N",
                "USE_PRODUCT_QUANTITY" => "N",
                "arrFILTER" => array("iblock_catalog"),
                "arrFILTER_iblock_catalog" => array("17"),
                "arrWHERE" => array()
            )
        ); ?>
    </div>
<? //_c($GLOBALS['searchFilter']);
//vars
$arItemsIds = $arIDS;

//convert encoding
/*$_POST["SEARCH_QUERY"] = !defined("BX_UTF") ? iconv("UTF-8", "windows-1251//ignore", $_POST["SEARCH_QUERY"]) : $_POST["SEARCH_QUERY"];

//convert case
if(!empty($_POST["CONVERT_CASE"]) && $_POST["CONVERT_CASE"] == "Y"){
	$arLang = CSearchLanguage::GuessLanguage($_POST["SEARCH_QUERY"]);
	if(is_array($arLang) && $arLang["from"] != $arLang["to"]){
		$_POST["SEARCH_QUERY"] = CSearchLanguage::ConvertKeyboardLayout($_POST["SEARCH_QUERY"], $arLang["from"], $arLang["to"]);
	}
}

//search
$obSearch = new CSearch;
$arSearchParams = array(
   "QUERY" => $_POST["SEARCH_QUERY"],
   "SITE_ID" => $_POST["SITE_ID"],
   "MODULE_ID" => "iblock",
   "PARAM2" => intval($_POST["IBLOCK_ID"])
);
$obSearch->Search($arSearchParams, array(), array("STEMMING" => !empty($_POST["STEMMING"]) && $_POST["STEMMING"] == "Y"));
while($searchItem = $obSearch->fetch()){
	if(is_numeric($searchItem["ITEM_ID"])){
		$arItemsIds[$searchItem["ITEM_ID"]] = $searchItem["ITEM_ID"];
	}
}*/

$obOffersExists = CIBlockElement::GetList(array(), array("IBLOCK_ID" => 17, "ACTIVE" => "Y", "ID" => $arItemsIds, "PROPERTY_REGION_VALUE" => $GLOBALS['medi']['region_cities'][SITE_ID]), false, false, array("PROPERTY_CML2_LINK.ID"));
$arOffersIds = [];
if ($arOffers = $obOffersExists->GetNext()) {

// добавим в поиск товары у которых sku с подзодящим артикулом
    $obOffers = CIBlockElement::GetList(array(), array(
        "IBLOCK_ID" => 19, "ACTIVE" => "Y",
        "?PROPERTY_CML2_ARTICLE" => $_POST["SEARCH_QUERY"],
        "PROPERTY_REGION_VALUE" => $GLOBALS['medi']['region_cities'][SITE_ID],
        "CATALOG_AVAILABLE" => "Y",
        ">CATALOG_QUANTITY" => 0
    ), false, false, array("PROPERTY_CML2_LINK.ID"));
    $arOffersIds = [];
    while ($arOffers = $obOffers->GetNext()) {
        
        $arItemsIds[$arOffers['PROPERTY_CML2_LINK_ID']] = $arOffers['PROPERTY_CML2_LINK_ID'];
    }
}
//push ids
$arrFilter["ID"] = array_values($arItemsIds);

$arrFilter["PROPERTY_REGION_VALUE"] = $GLOBALS['medi']['region_cities'][SITE_ID];

?>
<? if (!empty($arItemsIds)): ?>
    <h1><?= GetMessage("SEARCH_HEADING") ?> <a href="#" id="searchProductsClose"></a></h1>
    <? $APPLICATION->IncludeComponent(
        "dresscode:catalog.section",
        "squares",
        array(
            "IBLOCK_TYPE" => $_POST["IBLOCK_TYPE"],
            "IBLOCK_ID" => intval($_POST["IBLOCK_ID"]),
            "ELEMENT_SORT_FIELD" => $_POST["ELEMENT_SORT_FIELD"],
            "ELEMENT_SORT_ORDER" => $_POST["ELEMENT_SORT_ORDER"],
            
            "ELEMENT_SORT_FIELD2" => "sort",
            
            "ELEMENT_SORT_ORDER2" => "asc",
            "PROPERTY_CODE" => $_POST["PROPERTY_CODE"],
            "PAGE_ELEMENT_COUNT" => 6,//$_POST["PAGE_ELEMENT_COUNT"],
            "PAGE_RESULT_COUNT" => 1800,//$_POST["PAGE_ELEMENT_COUNT"],
            "LAZY_LOAD_PICTURES" => $_POST["LAZY_LOAD_PICTURES"],
            "PRICE_CODE" => $_POST["PRICE_CODE"],
            "PAGER_TEMPLATE" => "round_search",
            "CONVERT_CURRENCY" => $_POST["CONVERT_CURRENCY"],
            "CURRENCY_ID" => $_POST["CURRENCY_ID"],
            "FILTER_NAME" => "arrFilter",
            "ADD_SECTIONS_CHAIN" => "N",
            "SHOW_ALL_WO_SECTION" => "Y",
            "DISPLAY_BOTTOM_PAGER" => "N",
            "DISPLAY_TOP_PAGER" => "N",
            "AJAX_MODE" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "CACHE_TYPE" => "N",
            "CACHE_FILTER" => "Y",
            "AJAX_OPTION_HISTORY" => "N",
            "HIDE_NOT_AVAILABLE" => $_POST["HIDE_NOT_AVAILABLE"],
            "HIDE_MEASURES" => $_POST["HIDE_MEASURES"],
        )
    );
    ?>
    <a href="<?= SITE_DIR ?>search/?q=<?= htmlspecialcharsbx($_POST["SEARCH_QUERY"]) ?>"
       class="searchAllResult"><span><?= GetMessage("SEARCH_ALL_RESULT") ?></span></a>
<? else: ?>
    <div class="errorMessage"><?= GetMessage("SEARCH_ERROR_FOR_EMPTY_RESULT") ?><a href="#"
                                                                                   id="searchProductsClose"></a>
    </div>
<? endif; ?>
<?

