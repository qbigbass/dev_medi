<? define("STOP_STATISTICS", true); ?>
<? define("NO_AGENT_CHECK", true); ?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); ?>
<? include($_SERVER['DOCUMENT_ROOT'] . "/" . $APPLICATION->GetCurDir() . "lang/" . LANGUAGE_ID . "/template.php"); ?>
<? if (CModule::IncludeModule("iblock") && CModule::IncludeModule("search")): ?>
    <? global $arrFilter;
    $_REQUEST["SEARCH_QUERY"] = !defined("BX_UTF") ? iconv("UTF-8", "windows-1251//ignore", $_REQUEST["SEARCH_QUERY"]) : $_REQUEST["SEARCH_QUERY"];
    
    //convert case
    if (!empty($_REQUEST["CONVERT_CASE"]) && $_REQUEST["CONVERT_CASE"] == "Y") {
        $arLang = CSearchLanguage::GuessLanguage($_REQUEST["SEARCH_QUERY"]);
        if (is_array($arLang) && $arLang["from"] != $arLang["to"]) {
            $_REQUEST["SEARCH_QUERY"] = CSearchLanguage::ConvertKeyboardLayout($_REQUEST["SEARCH_QUERY"], $arLang["from"], $arLang["to"]);
            $_REQUEST["QUERY_REPLACE"] = true;
        }
    }
    // $arrFilter["NAME"] = "%".trim($_REQUEST["SEARCH_QUERY"])."%";
    
    $arAppendFilter["LOGIC"] = "OR";
    $arAppendFilter["?NAME"] = $_REQUEST["SEARCH_QUERY"];
    $arAppendFilter["?PROPERTY_CML2_ARTICLE"] = $_REQUEST["SEARCH_QUERY"];
    
    
    foreach ($_REQUEST["SEARCH_PROPERTIES"] as $index => $arNextProp) {
        if ($arNextProp["PROPERTY_TYPE"] == "L") {
            $arAppendFilter["?PROPERTY_" . $arNextProp["CODE"] . "_VALUE"] = $_REQUEST["SEARCH_QUERY"];
        } else {
            $arAppendFilter["?PROPERTY_" . $arNextProp["CODE"]] = $_REQUEST["SEARCH_QUERY"];
        }
    }
    
    $arrFilter[] = $arAppendFilter;
    $arFilter = array("IBLOCK_TYPE" => $_REQUEST["IBLOCK_TYPE"],
        "IBLOCK_ID" => intval($_REQUEST["IBLOCK_ID"]),
        "CATALOG_AVAILABLE" => "Y",
        ">CATALOG_QUANTITY" => 0,
        "PROPERTY_REGION_VALUE" => $GLOBALS['medi']['region_cities'][SITE_ID]);
    
    //print_r(array_merge($arrFilter, $arFilter));
    
    $elementCount = CIBlockElement::GetList(array(), array_merge($arrFilter, $arFilter), array(), false);
    ?>
    <? if ($elementCount > 0): ?>
        <h1><?= GetMessage("SEARCH_HEADING") ?> <a href="#" id="searchProductsClose"></a></h1>
        <? $APPLICATION->IncludeComponent(
            "dresscode:catalog.section",
            "squares",
            array(
                "IBLOCK_TYPE" => $_REQUEST["IBLOCK_TYPE"],
                "IBLOCK_ID" => intval($_REQUEST["IBLOCK_ID"]),
                "ELEMENT_SORT_FIELD" => $_REQUEST["ELEMENT_SORT_FIELD"],
                "ELEMENT_SORT_ORDER" => $_REQUEST["ELEMENT_SORT_ORDER"],
                "LAZY_LOAD_PICTURES" => $_REQUEST["LAZY_LOAD_PICTURES"],
                "PROPERTY_CODE" => $_REQUEST["PROPERTY_CODE"],
                "PAGE_ELEMENT_COUNT" => $_REQUEST["PAGE_ELEMENT_COUNT"],
                "PRICE_CODE" => $_REQUEST["PRICE_CODE"],
                "PAGER_TEMPLATE" => "round_search",
                "CONVERT_CURRENCY" => $_REQUEST['CONVERT_CURRENCY'],
                "CURRENCY_ID" => $_REQUEST["CURRENCY_ID"],
                "FILTER_NAME" => $_REQUEST["FILTER_NAME"],
                "ADD_SECTIONS_CHAIN" => "N",
                "SHOW_ALL_WO_SECTION" => "Y",
                "AJAX_MODE" => "N",
                "AJAX_OPTION_JUMP" => "N",
                "CACHE_TYPE" => "Y",
                "CACHE_FILTER" => "Y",
                "AJAX_OPTION_HISTORY" => "N",
                "HIDE_NOT_AVAILABLE" => $_REQUEST["HIDE_NOT_AVAILABLE"],
                "HIDE_MEASURES" => $_REQUEST["HIDE_MEASURES"],
            )
        );
        ?>
        <a href="/search/?q=<?= htmlspecialcharsbx($_REQUEST["SEARCH_QUERY"]) ?>"
           class="searchAllResult"><span><?= GetMessage("SEARCH_ALL_RESULT") ?></span></a>
    <? else: ?>
        <div class="errorMessage"><?= GetMessage("SEARCH_ERROR_FOR_EMPTY_RESULT") ?><a href="#"
                                                                                       id="searchProductsClose"></a>
        </div>
    <? endif; ?>
<? endif; ?>
