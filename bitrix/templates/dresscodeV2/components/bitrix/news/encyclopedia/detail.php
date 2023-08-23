<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>
<?
//get element id by element code
if (empty($arResult["VARIABLES"]["ELEMENT_ID"])) {
    
    //cache id
    $arCacheId = array(
        "TYPE" => "V_BLOG_CACHE",
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
        "ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
    );
    
    //start cache
    $obCache = new CPHPCache();
    
    //get from cache
    if ($obCache->InitCache(36000000, serialize($arCacheId), "/")) {
        $arCachedVars = $obCache->GetVars();
        $arResult["VARIABLES"]["ELEMENT_ID"] = $arCachedVars["ELEMENT_ID"];
        $arReferenceId = $arCachedVars["PRODUCTS_REFERENCE"];
        $relatedLinksID = $arCachedVars["RELATED"];
    } //no cache
    elseif ($obCache->StartDataCache()) {
        
        //select fields
        $arSelect = array("ID");
        
        //select by code
        $arFilter = array(
            "=CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "ACTIVE_DATE" => "Y",
            "ACTIVE" => "Y"
        );
        
        //get element
        $rsElement = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        if ($arElementData = $rsElement->GetNext()) {
            //save id
            $arResult["VARIABLES"]["ELEMENT_ID"] = $arElementData["ID"];
            
            //get product reference
            $arReferenceId = array();
            $rsProperty = CIBlockElement::GetProperty($arParams["IBLOCK_ID"], $arResult["VARIABLES"]["ELEMENT_ID"], array("sort" => "asc"), array("CODE" => "PRODUCTS_REFERENCE"));
            while ($obProperty = $rsProperty->GetNext()) {
                if (!empty($obProperty["VALUE"])) {
                    $arReferenceId[] = $obProperty["VALUE"];
                }
            }
            
            
            $obRelated = CIBlockElement::GetList(
                ['ID' => "DESC"],
                [
                    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                    'ACTIVE' => 'Y'
                ],
                false,
                ['nPageSize' => 3, 'nElementID' => $arElementData["ID"]],
                ['ID', 'NAME', "IBLOCK_ID", 'DETAIL_PAGE_URL']);
            $relatedLinksID = [];
            while ($arRelated = $obRelated->GetNext()) {
                if ($arRelated['ID'] != $arElementData["ID"]) {
                    $relatedLinksID[] = $arRelated['ID'];
                }
            }
            if (count($relatedLinksID) < 6) {
                $i = 6 - count($relatedLinksID);
                $obRelated = CIBlockElement::GetList(
                    ['ID' => "ASC"],
                    [
                        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                        'ACTIVE' => 'Y'
                    ],
                    false,
                    ['nTopCount' => $i],
                    ['ID', 'NAME', "IBLOCK_ID", 'DETAIL_PAGE_URL']);
                while ($arRelated = $obRelated->GetNext()) {
                    if ($arRelated['ID'] != $arElementData["ID"]) {
                        $relatedLinksID[] = $arRelated['ID'];
                    }
                }
            }
            
            //save cache vars
            $obCache->EndDataCache(
                array(
                    "ELEMENT_ID" => $arElementData["ID"],
                    "PRODUCTS_REFERENCE" => $arReferenceId,
                    "RELATED" => $relatedLinksID
                )
            );
        }
        
    }
}
?>
<? $arParams["DETAIL_PROPERTY_CODE"][] = "BG_IMAGE"; ?>
<? $ELEMENT_ID = $APPLICATION->IncludeComponent(
    "bitrix:news.detail",
    "",
    array(
        "DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
        "DISPLAY_NAME" => $arParams["DISPLAY_NAME"],
        "DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
        "DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "FIELD_CODE" => $arParams["DETAIL_FIELD_CODE"],
        "PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
        "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["detail"],
        "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
        "META_KEYWORDS" => $arParams["META_KEYWORDS"],
        "META_DESCRIPTION" => $arParams["META_DESCRIPTION"],
        "BROWSER_TITLE" => $arParams["BROWSER_TITLE"],
        "SET_CANONICAL_URL" => $arParams["DETAIL_SET_CANONICAL_URL"],
        "DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
        "SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
        "SET_TITLE" => $arParams["SET_TITLE"],
        "MESSAGE_404" => $arParams["MESSAGE_404"],
        "SET_STATUS_404" => $arParams["SET_STATUS_404"],
        "SHOW_404" => $arParams["SHOW_404"],
        "FILE_404" => $arParams["FILE_404"],
        "INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
        "ADD_SECTIONS_CHAIN" => "N",
        "ACTIVE_DATE_FORMAT" => $arParams["DETAIL_ACTIVE_DATE_FORMAT"],
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
        "USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
        "GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
        "DISPLAY_TOP_PAGER" => $arParams["DETAIL_DISPLAY_TOP_PAGER"],
        "DISPLAY_BOTTOM_PAGER" => $arParams["DETAIL_DISPLAY_BOTTOM_PAGER"],
        "PAGER_TITLE" => $arParams["DETAIL_PAGER_TITLE"],
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => $arParams["DETAIL_PAGER_TEMPLATE"],
        "PAGER_SHOW_ALL" => $arParams["DETAIL_PAGER_SHOW_ALL"],
        "CHECK_DATES" => $arParams["CHECK_DATES"],
        "ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
        "ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
        "IBLOCK_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["news"],
        "USE_SHARE" => $arParams["USE_SHARE"],
        "SHARE_HIDE" => $arParams["SHARE_HIDE"],
        "SHARE_TEMPLATE" => $arParams["SHARE_TEMPLATE"],
        "SHARE_HANDLERS" => $arParams["SHARE_HANDLERS"],
        "SHARE_SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
        "SHARE_SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
        "ADD_ELEMENT_CHAIN" => (isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : '')
    ),
    $component
); ?>
<? if (!empty($arReferenceId)): ?>
    <?
    global $arrFilter;
    $arrFilter = array("ID" => $arReferenceId);
    ?>
    <div class="h2 ff-medium blog-title-product"><?= GetMessage("V_BLOG_LABEL"); ?></div>

    <div class="productsListName" data-list-name="<?= $APPLICATION->GetCurDir() ?>">
        <? $APPLICATION->IncludeComponent(
            "dresscode:catalog.section",
            "squares",
            array(
                "IBLOCK_TYPE" => $arParams["PRODUCT_IBLOCK_TYPE"],
                "IBLOCK_ID" => $arParams["PRODUCT_IBLOCK_ID"],
                "ELEMENT_SORT_FIELD" => "id",
                "ELEMENT_SORT_ORDER" => $arReferenceId,
                "INCLUDE_SUBSECTIONS" => "Y",
                "FILTER_NAME" => "arrFilter",
                "PRICE_CODE" => $arParams["PRODUCT_PRICE_CODE"],
                "PROPERTY_CODE" => $arParams["PRODUCT_PROPERTY_CODE"],
                "PAGER_TEMPLATE" => "round",
                "PAGE_ELEMENT_COUNT" => 20,
                "CONVERT_CURRENCY" => $arParams["PRODUCT_CONVERT_CURRENCY"],
                "CURRENCY_ID" => $arParams["PRODUCT_CURRENCY_ID"],
                "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
                "HIDE_MEASURES" => $arParams["HIDE_MEASURES"],
                "SET_BROWSER_TITLE" => "N",
                "SET_TITLE" => "N",
                "SECTION_ID" => "0",
                "SHOW_ALL_WO_SECTION" => "Y",
                "ADD_SECTIONS_CHAIN" => "N",
                "CACHE_FILTER" => "N",
                "CACHE_TYPE" => "Y",
                "AJAX_MODE" => "Y"
            ),
            $component
        ); ?>
    </div>
<? endif; ?>

<? if (!empty($relatedLinksID)) { ?>
    <div class="h2 ff-medium txtc" style="margin:2em 0 1em;">Популярные статьи</div>
    <div class="light-bg" id="relatedArticles">
        
        <? $GLOBALS['articleLinksFilter'] = ['ID' => $relatedLinksID]; ?>
        <? $APPLICATION->IncludeComponent(
            "bitrix:news.list",
            "slideArticles",
            array(
                "IBLOCK_TYPE" => "info",
                "IBLOCK_ID" => 3,
                "NEWS_COUNT" => 6,
                "SORT_BY1" => "ID",
                "SORT_ORDER1" => "DESC",
                "SET_TITLE" => "N",
                "SET_LAST_MODIFIED" => "N",
                "SET_STATUS_404" => "N",
                "SHOW_404" => "N",
                "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "2592000",
                "CACHE_FILTER" => "Y",
                "CACHE_GROUPS" => "N",
                "DISPLAY_TOP_PAGER" => "N",
                "DISPLAY_BOTTOM_PAGER" => "N",
                "PAGER_SHOW_ALWAYS" => "N",
                "PROPERTY_CODE" => ['PREVIEW_IMG'],
                "FILTER_NAME" => "articleLinksFilter",
            ),
            $component
        ); ?>

        <div class=" mob">
            <? $APPLICATION->IncludeComponent(
                "dresscode:slider",
                "articleLinks",
                array(
                    "IBLOCK_TYPE" => "",
                    "IBLOCK_ID" => "3",
                    "CACHE_TYPE" => "N",
                    "CACHE_TIME" => "360000",
                    "PICTURE_WIDTH" => "440",
                    "PICTURE_HEIGHT" => "440",
                    "COMPONENT_TEMPLATE" => ".default",
                    "FILTER_NAME" => "articleLinksFilter"
                ),
                false
            ); ?>
        </div>
    </div>
<? } ?>

<? if (!empty($arResult["VARIABLES"]["ELEMENT_ID"]) || !empty($arResult["VARIABLES"]["ELEMENT_CODE"])): ?>
    <? if (!empty($arParams["SHOW_BLOG_COMMENTS"]) && $arParams["SHOW_BLOG_COMMENTS"] == "Y"): ?>
        <? $APPLICATION->IncludeComponent(
            "bitrix:catalog.comments",
            ".default",
            array(
                "AJAX_POST" => "Y",
                "BLOG_TITLE" => "",
                "BLOG_URL" => "",
                "BLOG_USE" => "Y",
                "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                "CACHE_TIME" => $arParams["CACHE_TIME"],
                "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                "COMMENTS_COUNT" => "10",
                "COMPONENT_TEMPLATE" => ".default",
                "ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
                "ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
                "EMAIL_NOTIFY" => "Y",
                "FB_APP_ID" => "",
                "FB_COLORSCHEME" => "light",
                "FB_ORDER_BY" => "reverse_time",
                "FB_TITLE" => "Facebook",
                "FB_USE" => "N",
                "FB_USER_ADMIN_ID" => "",
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                "PATH_TO_SMILE" => "",
                "SHOW_DEACTIVATED" => "N",
                "SHOW_RATING" => "Y",
                "SHOW_SPAM" => "Y",
                "TEMPLATE_THEME" => "black",
                "URL_TO_COMMENT" => "",
                "VK_API_ID" => "",
                "VK_TITLE" => "",
                "VK_USE" => "N",
                "WIDTH" => ""
            ),
            $component,
            array(
                "HIDE_ICONS" => "Y"
            )
        ); ?>
    <? endif; ?>
<? endif; ?>

<div class="btn-simple-wrap">
    <a href="/encyclopedia/" class="btn-simple btn-micro  btn-border">Вернуться к списку статей</a>
</div>
