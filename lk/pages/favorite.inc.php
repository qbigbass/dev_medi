<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $USER;

if ($USER->IsAuthorized()) {
    $idUser = $USER->GetID();
    $rsUser = CUser::GetByID($idUser);
    $arUser = $rsUser->Fetch();
    $arrFilter["ID"] = $arUser['UF_FAVORITIES'];

    if (!empty($arrFilter["ID"])) {
        $APPLICATION->IncludeComponent(
            "dresscode:catalog.wishlist",
            'squares',
            array(
                "USER_ID" => $idUser,
                "AJAX_MODE" => "Y",
                "VIEW_MODE" => "squares",
                "IBLOCK_TYPE" => "catalog",
                "IBLOCK_ID" => 17,
                "ELEMENT_SORT_FIELD" => "SORT",
                "ELEMENT_SORT_ORDER" => "asc",
                "ELEMENT_SORT_FIELD2" => "shows",
                "ELEMENT_SORT_ORDER2" => "desc",
                "PROPERTY_CODE" => [],
                "META_KEYWORDS" => "-",
                "META_DESCRIPTION" => "-",
                "BROWSER_TITLE" => "-",
                "INCLUDE_SUBSECTIONS" => "Y",
                "BASKET_URL" => "/personal/cart/",
                "ACTION_VARIABLE" => "action",
                "PRODUCT_ID_VARIABLE" => "id",
                "SECTION_ID_VARIABLE" => "SECTION_ID",
                "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                "PRODUCT_PROPS_VARIABLE" => "prop",
                "FILTER_NAME" => "arrFilter",
                "CACHE_TYPE" => "N",
                "CACHE_TIME" => 86400,
                "CACHE_FILTER" => "N",
                "CACHE_GROUPS" => "N",
                "SET_TITLE" => "N",
                "SET_STATUS_404" => "N",
                "DISPLAY_COMPARE" => "N",
                "PAGE_ELEMENT_COUNT" => 10,
                "LINE_ELEMENT_COUNT" => 3,
                "PRICE_CODE" => [
                    0 => "BASE"
                ],
                "USE_PRICE_COUNT" => "N",
                "SHOW_PRICE_COUNT" => 1,
                "HIDE_MEASURES" => "Y",
                "PRICE_VAT_INCLUDE" => "Y",
                "USE_PRODUCT_QUANTITY" => "Y",
                "ADD_PROPERTIES_TO_BASKET" => "Y",
                "PARTIAL_PRODUCT_PROPERTIES" => '',
                "PRODUCT_PROPERTIES" => [],
                "SHOW_SECTION_BANNER" => "Y",
                "DISPLAY_TOP_PAGER" => "N",
                "DISPLAY_BOTTOM_PAGER" => "Y",
                "PAGER_TITLE" => "",
                "PAGER_SHOW_ALWAYS" => "N",
                "PAGER_TEMPLATE" => "round",
                "PAGER_DESC_NUMBERING" => "N",
                "PAGER_DESC_NUMBERING_CACHE_TIME" => 36000000,
                "PAGER_SHOW_ALL" => "N",

                "OFFERS_CART_PROPERTIES" => [
                    0 => "COLOR"
                ],
                "OFFERS_FIELD_CODE" => [],
                "OFFERS_PROPERTY_CODE" => [],
                "OFFERS_SORT_FIELD" => "sort",
                "OFFERS_SORT_ORDER" => "asc",
                "OFFERS_SORT_FIELD2" => "NAME",
                "OFFERS_SORT_ORDER2" => "asc",
                "OFFERS_LIMIT" => 1,
                "DETAIL_URL" => "/catalog/URL_TEMPLATES" . "#SECTION_CODE_PATH#/#ELEMENT_CODE#.html",
                'CONVERT_CURRENCY' => "N",
                'CURRENCY_ID' => "RUB",
                'HIDE_NOT_AVAILABLE' => "Y",
                'LABEL_PROP' => "-",
                'ADD_PICT_PROP' => "MORE_PHOTO",
                'PRODUCT_DISPLAY_MODE' => "Y",
                'OFFER_ADD_PICT_PROP' => "MORE_PHOTO",
                'OFFER_TREE_PROPS' => [],
                'PRODUCT_SUBSCRIPTION' => "",
                'SHOW_DISCOUNT_PERCENT' => "Y",
                'SHOW_OLD_PRICE' => "Y",
                'MESS_BTN_BUY' => "Купить",
                'MESS_BTN_ADD_TO_BASKET' => "В корзину",
                'MESS_BTN_SUBSCRIBE' => "",
                'MESS_BTN_DETAIL' => "Подробнее",
                'MESS_NOT_AVAILABLE' => "Нет в наличии",
                "USE_MAIN_ELEMENT_SECTION" => "Y",
                'HIDE_NOT_AVAILABLE_OFFERS' => "Y",
                "LAZY_LOAD_PICTURES" => "Y",
                'TEMPLATE_THEME' => "site",
                "ENABLED_SKU_FILTER" => "Y",
                "ADD_SECTIONS_CHAIN" => "N"
            ),
            $component
        );
    } else {
        ?>
        <div class="favorite-empty">
            <p>В избранном пусто</p>
            <p>Добавляйте товары с помощью
                <img src="<?=SITE_TEMPLATE_PATH?>/images/favoriteIcoPinkActive20.png" alt="">
            </p>
        </div>
        <?
    }
}