<? define("NO_HEAD_BREADCRUMB", "Y");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Действующие акции | Официальный интернет-магазин medi"); ?>
<? $GLOBALS['stockFilter'] = ["PROPERTY_CITY_VALUE" => $GLOBALS['medi']['region_cities'][SITE_ID]]; ?>
<?
if (!empty($APPLICATION->GetCurDir())) {
    $url_path = explode("/", $APPLICATION->GetCurDir());
    if (!empty($url_path[2])) {
        $iblockId = 38; // ID инфоблока купонных акций

        // check coupons
        $coupon_alias = $url_path[2];

        $dbRes = CIBlockSection::GetList(['sort' => 'asc'], ["IBLOCK_ID" => $iblockId,
            "ACTIVE" => "Y", "CODE" => $coupon_alias], false, ["ID", "NAME", "DESCRIPTION", "UF_*"]);

        if ($arAction = $dbRes->Fetch()) {
            $actionId = $arAction['ID'];
            //$APPLICATION->RestartBuffer();
            include_once($_SERVER['DOCUMENT_ROOT'] . '/x/coupon/index.php');
            die;
        }

    }
}
?>
<? $APPLICATION->IncludeComponent(
    "bitrix:news",
    "secret-stocks",
    array(
        "ADD_ELEMENT_CHAIN" => "Y",
        "ADD_SECTIONS_CHAIN" => "Y",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "BROWSER_TITLE" => "NAME",
        "CACHE_FILTER" => "Y",
        "CACHE_GROUPS" => "N",
        "CACHE_TIME" => "36000",
        "CACHE_TYPE" => "A",
        "CHECK_DATES" => "Y",
        "DETAIL_ACTIVE_DATE_FORMAT" => "d.m.Y",
        "DETAIL_DISPLAY_BOTTOM_PAGER" => "Y",
        "DETAIL_DISPLAY_TOP_PAGER" => "N",
        "DETAIL_FIELD_CODE" => array(
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
            10 => "DATE_ACTIVE_FROM",
            11 => "ACTIVE_FROM",
            12 => "DATE_ACTIVE_TO",
            13 => "ACTIVE_TO",
            14 => "SHOW_COUNTER",
            15 => "SHOW_COUNTER_START",
            16 => "IBLOCK_TYPE_ID",
            17 => "IBLOCK_ID",
            18 => "IBLOCK_CODE",
            19 => "IBLOCK_NAME",
            20 => "IBLOCK_EXTERNAL_ID",
            21 => "DATE_CREATE",
            22 => "CREATED_BY",
            23 => "CREATED_USER_NAME",
            24 => "TIMESTAMP_X",
            25 => "MODIFIED_BY",
            26 => "USER_NAME",
            27 => "",
        ),
        "DETAIL_PAGER_SHOW_ALL" => "N",
        "DETAIL_PAGER_TEMPLATE" => "",
        "DETAIL_PAGER_TITLE" => "Страница",
        "DETAIL_PROPERTY_CODE" => array(
            0 => "BLOG_POST_ID",
            1 => "STOCK_DATE",
            2 => "BLOG_COMMENTS_CNT",
            3 => "PRODUCTS_REFERENCE",
            4 => "",
        ),
        "DETAIL_SET_CANONICAL_URL" => "Y",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "DISPLAY_NAME" => "Y",
        "DISPLAY_TOP_PAGER" => "N",
        "FILTER_NAME" => "stockFilter",
        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
        "HIDE_MEASURES" => "Y",
        "HIDE_NOT_AVAILABLE" => "Y",
        "IBLOCK_ID" => "25",
        "IBLOCK_TYPE" => "info",
        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
        "LIST_ACTIVE_DATE_FORMAT" => "d.m.Y",
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
            10 => "DATE_ACTIVE_FROM",
            11 => "ACTIVE_FROM",
            12 => "DATE_ACTIVE_TO",
            13 => "ACTIVE_TO",
            14 => "SHOW_COUNTER",
            15 => "SHOW_COUNTER_START",
            16 => "IBLOCK_TYPE_ID",
            17 => "IBLOCK_ID",
            18 => "IBLOCK_CODE",
            19 => "IBLOCK_NAME",
            20 => "IBLOCK_EXTERNAL_ID",
            21 => "DATE_CREATE",
            22 => "CREATED_BY",
            23 => "CREATED_USER_NAME",
            24 => "TIMESTAMP_X",
            25 => "MODIFIED_BY",
            26 => "USER_NAME",
            27 => "",
        ),
        "LIST_PROPERTY_CODE" => array(
            0 => "BLOG_POST_ID",
            1 => "STOCK_DATE",
            2 => "BLOG_COMMENTS_CNT",
            3 => "PRODUCTS_REFERENCE",
            4 => "",
        ),
        "MESSAGE_404" => "",
        "META_DESCRIPTION" => "-",
        "META_KEYWORDS" => "-",
        "NEWS_COUNT" => "20",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => ".default",
        "PAGER_TITLE" => "Новости",
        "PREVIEW_TRUNCATE_LEN" => "",
        "PRODUCT_CONVERT_CURRENCY" => "Y",
        "PRODUCT_IBLOCK_TYPE" => "catalog",
        "PRODUCT_IBLOCK_ID" => "17",
        "PRODUCT_PRICE_CODE" => array(
            0 => $GLOBALS['medi']['price'][SITE_ID],
        ),
        "PRODUCT_PROPERTY_CODE" => array(
            0 => "",
            1 => "",
        ),
        "SEF_MODE" => "Y",
        "SET_LAST_MODIFIED" => "Y",
        "SET_STATUS_404" => "Y",
        "SET_TITLE" => "Y",
        "SHOW_404" => "Y",
        "SHOW_BLOG_COMMENTS" => "N",
        "SORT_BY1" => "SORT",
        "SORT_BY2" => "SORT",
        "SORT_ORDER1" => "ASC",
        "SORT_ORDER2" => "ASC",
        "STRICT_SECTION_CHECK" => "N",
        "USE_CATEGORIES" => "N",
        "USE_FILTER" => "Y",
        "USE_PERMISSIONS" => "N",
        "USE_RATING" => "N",
        "USE_REVIEW" => "N",
        "USE_RSS" => "N",
        "USE_SEARCH" => "N",
        "COMPONENT_TEMPLATE" => "sales",
        "SEF_FOLDER" => "/x/",
        "FILE_404" => "/x/404.php",
        "PRODUCT_CURRENCY_ID" => "RUB",
        "FILTER_FIELD_CODE" => array(
            0 => "",
            1 => "",
        ),
        "FILTER_PROPERTY_CODE" => array(
            0 => "",
            1 => "",
        ),
        "COMPOSITE_FRAME_MODE" => "A",
        "COMPOSITE_FRAME_TYPE" => "AUTO",
        "SEF_URL_TEMPLATES" => array(
            "news" => "",
            "section" => "",
            "detail" => "#ELEMENT_CODE#/",
        )
    ),
    false
); ?>
<br><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
