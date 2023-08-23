<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Результаты поиска");
$GLOBALS['searchFilter'] = ["PROPERTY_REGION_VALUE" => $GLOBALS['medi']['region_cities'][SITE_ID]];
?>

<? $APPLICATION->IncludeComponent(
    "dresscode:search",
    "new",
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
        "DETAIL_URL" => "",
        "DISPLAY_BOTTOM_PAGER" => "N",
        "DISPLAY_COMPARE" => "N",
        "DISPLAY_TOP_PAGER" => "N",
        "ELEMENT_SORT_FIELD" => "RANK",
        "ELEMENT_SORT_FIELD2" => "id",
        "ELEMENT_SORT_ORDER" => "asc",
        "ELEMENT_SORT_ORDER2" => "desc",
        "HIDE_NOT_AVAILABLE" => "Y",
        "HIDE_NOT_AVAILABLE_OFFERS" => "N",
        "IBLOCK_ID" => "17",
        "IBLOCK_TYPE" => "catalog",
        "INPUT_PLACEHOLDER" => "",
        "LINE_ELEMENT_COUNT" => "4",
        "OFFERS_CART_PROPERTIES" => array("CML2_ARTICLE", "COLOR", "SIZE", "LENGTH", "ELASTIC_TIPS2",
            "HEIGHT", "WIDE_THIGH", "SIDE", "CUP", "WIDTH"),
        "OFFERS_FIELD_CODE" => array("", ""),
        "OFFERS_LIMIT" => "5",
        "OFFERS_PROPERTY_CODE" => array("CML2_ARTICLE", ""),
        "OFFERS_SORT_FIELD" => "sort",
        "OFFERS_SORT_FIELD2" => "id",
        "OFFERS_SORT_ORDER" => "asc",
        "OFFERS_SORT_ORDER2" => "desc",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => "round",
        "PAGER_TITLE" => "Товары",
        "PAGE_ELEMENT_COUNT" => "24",
        "PRICE_CODE" => array(
            0 => SITE_ID == 's2' ? "BASE_SPB" : "BASE",
        ),
        "PRICE_VAT_INCLUDE" => "Y",
        "PRODUCT_ID_VARIABLE" => "id",
        "PRODUCT_PROPERTIES" => array(),
        "PROPERTY_CODE" => array("CML2_ARTICLE", ""),
        "PRODUCT_PROPS_VARIABLE" => "prop",
        "PRODUCT_QUANTITY_VARIABLE" => "quantity",
        "SECTION_ID_VARIABLE" => "SECTION_ID",
        "SECTION_URL" => "",
        "SHOW_HISTORY" => "Y",
        "SHOW_PRICE_COUNT" => "1",
        "USE_LANGUAGE_GUESS" => "N",
        "USE_PRICE_COUNT" => "N",
        "USE_PRODUCT_QUANTITY" => "N",
        "NO_WORD_LOGIC" => "Y"
    )
); ?>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");

