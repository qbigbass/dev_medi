<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Каталог ортопедических изделий салона medi - Купить с доставкой по РФ | Интернет-магазин medi");
$APPLICATION->SetTitle("Каталог ортопедических изделий салона medi");
?>

<? $GLOBALS['arrFilter'] = ["PROPERTY_REGION_VALUE" => $GLOBALS['medi']['region_cities'][SITE_ID]]; ?>
<? $APPLICATION->IncludeComponent(
    "medi:catalog",
    ".default",
    array(
        "SITE_ID" => SITE_ID,
        "IBLOCK_TYPE" => "catalog",
        "IBLOCK_ID" => "17",
        "TEMPLATE_THEME" => "site",
        "HIDE_NOT_AVAILABLE" => "Y",
        "BASKET_URL" => "/personal/cart/",
        "ACTION_VARIABLE" => "action",
        "PRODUCT_ID_VARIABLE" => "id",
        "SECTION_ID_VARIABLE" => "SECTION_ID",
        "PRODUCT_QUANTITY_VARIABLE" => "quantity",
        "PRODUCT_PROPS_VARIABLE" => "prop",
        "SEF_MODE" => "Y",
        "SEF_FOLDER" => "/catalog/",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_JUMP" => "Y",
        "AJAX_OPTION_STYLE" => "Y",
        "AJAX_OPTION_HISTORY" => "Y",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "86400",
        "CACHE_FILTER" => "Y",
        "CACHE_GROUPS" => "N",
        "SET_TITLE" => "Y",
        "CATALOG_SHOW_TAGS" => "Y",
        "CATALOG_MAX_TAGS" => "30",
        "CATALOG_TAGS_USE_IBLOCK_MAIN_SECTION" => "N",
        "CATALOG_TAGS_DETAIL_LINK_VARIANT" => "SECTION",
        "CATALOG_TAGS_MAX_DEPTH_LEVEL" => "5",
        "CATALOG_MAX_VISIBLE_TAGS_DESKTOP" => "10",
        "CATALOG_MAX_VISIBLE_TAGS_MOBILE" => "6",
        "CATALOG_HIDE_TAGS_ON_MOBILE" => "N",
        "CATALOG_TAGS_SORT_FIELD" => "SORT",
        "CATALOG_TAGS_SORT_TYPE" => "ASC",
        "CATALOG_TAGS_DETAIL_SECTION_MAX_DELPH_LEVEL" => "5",
        "ADD_SECTION_CHAIN" => "Y",
        "ADD_ELEMENT_CHAIN" => "Y",
        "SET_STATUS_404" => "Y",
        "DETAIL_DISPLAY_NAME" => "Y",
        "USE_ELEMENT_COUNTER" => "N",
        "USE_FILTER" => "Y",
        "FILTER_NAME" => "arrFilter",
        "FILTER_VIEW_MODE" => "VERTICAL",
        "FILTER_FIELD_CODE" => array(
            0 => "",
            1 => "",
        ),
        "FILTER_PROPERTY_CODE" => array(
            0 => "OFFERS",
            1 => "ATT_BRAND",
            2 => "COLOR",
            3 => "",
        ),
        "FILTER_PRICE_CODE" => array(
            0 => "",
        ),
        "FILTER_OFFERS_FIELD_CODE" => array(
            0 => "",
            1 => "",
        ),
        "FILTER_OFFERS_PROPERTY_CODE" => array(
            0 => "SIZE",
            1 => "",
        ),
        "FILTER_HIDE_PROPS" => [394, 133],
        "USE_REVIEW" => "Y",
        "MESSAGES_PER_PAGE" => "10",
        "USE_CAPTCHA" => "Y",
        "REVIEW_AJAX_POST" => "Y",
        "PATH_TO_SMILE" => "/bitrix/images/forum/smile/",
        "FORUM_ID" => "1",
        "URL_TEMPLATES_READ" => "",
        "SHOW_LINK_TO_FORUM" => "N",
        "USE_COMPARE" => "N",
        "PRICE_CODE" => array(
            0 => $GLOBALS['medi']['price'][SITE_ID],
        ),
        "USE_PRICE_COUNT" => "N",
        "SHOW_PRICE_COUNT" => "1",
        "PRICE_VAT_INCLUDE" => "Y",
        "PRICE_VAT_SHOW_VALUE" => "N",
        "PRODUCT_PROPERTIES" => array(),
        "USE_PRODUCT_QUANTITY" => "Y",
        "CONVERT_CURRENCY" => "N",
        "CURRENCY_ID" => "RUB",
        "QUANTITY_FLOAT" => "N",
        "OFFERS_CART_PROPERTIES" => array(
            0 => "COLOR",
        ),
        "SHOW_TOP_ELEMENTS" => "N",
        "SECTION_COUNT_ELEMENTS" => "Y",
        "SECTION_TOP_DEPTH" => "4",
        "SECTIONS_VIEW_MODE" => "TEXT",
        "SECTIONS_SHOW_PARENT_NAME" => "N",
        "PAGE_ELEMENT_COUNT" => "24",
        "LINE_ELEMENT_COUNT" => "3",
        "ELEMENT_SORT_FIELD" => "SORT",
        "ELEMENT_SORT_ORDER" => "desc",
        "ELEMENT_SORT_FIELD2" => "shows",
        "ELEMENT_SORT_ORDER2" => "desc",
        "LIST_PROPERTY_CODE" => array(),
        "FILTER_INSTANT_RELOAD" => "Y",
        "INCLUDE_SUBSECTIONS" => "Y",
        "LIST_META_KEYWORDS" => "-",
        "LIST_META_DESCRIPTION" => "-",
        "LIST_BROWSER_TITLE" => "-",
        "LIST_OFFERS_FIELD_CODE" => array(
            0 => "",
            1 => "",
        ),
        "LIST_OFFERS_PROPERTY_CODE" => array(
            0 => "",
            1 => "",
        ),
        "LIST_OFFERS_LIMIT" => "1",
        "DETAIL_PROPERTY_CODE" => array(),
        "DETAIL_META_KEYWORDS" => "-",
        "DETAIL_META_DESCRIPTION" => "-",
        "DETAIL_BROWSER_TITLE" => "-",
        "DISPLAY_SUBSCRIBE" => "N",
        "DETAIL_OFFERS_FIELD_CODE" => array(
            0 => "",
            1 => "",
        ),
        "DETAIL_OFFERS_PROPERTY_CODE" => array(
            0 => "",
            1 => "",
        ),
        "LINK_IBLOCK_TYPE" => "",
        "LINK_IBLOCK_ID" => "",
        "LINK_PROPERTY_SID" => "",
        "LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
        "USE_ALSO_BUY" => "N",
        "ALSO_BUY_ELEMENT_COUNT" => "4",
        "ALSO_BUY_MIN_BUYES" => "1",
        "OFFERS_SORT_FIELD" => "sort",
        "OFFERS_SORT_ORDER" => "asc",
        "OFFERS_SORT_FIELD2" => "NAME",
        "OFFERS_SORT_ORDER2" => "asc",
        "PAGER_TEMPLATE" => "round",
        "DISPLAY_TOP_PAGER" => "N",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "PAGER_TITLE" => "Товары",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",
        "PAGER_SHOW_ALL" => "N",
        "ADD_PICT_PROP" => "MORE_PHOTO",
        "LABEL_PROP" => "-",
        "PRODUCT_DISPLAY_MODE" => "Y",
        "OFFER_ADD_PICT_PROP" => "MORE_PHOTO",
        "OFFER_TREE_PROPS" => array(),
        "SHOW_DISCOUNT_PERCENT" => "Y",
        "SHOW_OLD_PRICE" => "Y",
        "MESS_BTN_BUY" => "Купить",
        "MESS_BTN_ADD_TO_BASKET" => "В корзину",
        "MESS_BTN_COMPARE" => "Сравнение",
        "MESS_BTN_DETAIL" => "Подробнее",
        "MESS_NOT_AVAILABLE" => "Нет в наличии",
        "DETAIL_USE_VOTE_RATING" => "Y",
        "DETAIL_VOTE_DISPLAY_AS_RATING" => "rating",
        "DETAIL_USE_COMMENTS" => "Y",
        "DETAIL_BLOG_USE" => "Y",
        "DETAIL_VK_USE" => "N",
        "DETAIL_FB_USE" => "Y",
        "AJAX_OPTION_ADDITIONAL" => "",
        "USE_STORE" => "N",
        "USE_STORE_PHONE" => "Y",
        "USE_STORE_SCHEDULE" => "Y",
        "USE_MIN_AMOUNT" => "N",
        "STORE_PATH" => "/store/#store_id#",
        "MAIN_TITLE" => "Наличие на складах",
        "MIN_AMOUNT" => "10",
        "DETAIL_BRAND_USE" => "Y",
        "DETAIL_BRAND_PROP_CODE" => array(
            0 => "",
            1 => "BRAND_REF",
            2 => "",
        ),
        "ADD_SECTIONS_CHAIN" => "Y",
        "COMMON_SHOW_CLOSE_POPUP" => "N",
        "DETAIL_SHOW_MAX_QUANTITY" => "N",
        "DETAIL_BLOG_URL" => "catalog_comments",
        "DETAIL_BLOG_EMAIL_NOTIFY" => "N",
        "DETAIL_FB_APP_ID" => "",
        "USE_SALE_BESTSELLERS" => "Y",
        "ADD_PROPERTIES_TO_BASKET" => "Y",
        "PARTIAL_PRODUCT_PROPERTIES" => "N",
        "USE_COMMON_SETTINGS_BASKET_POPUP" => "N",
        "TOP_ADD_TO_BASKET_ACTION" => "ADD",
        "SECTION_ADD_TO_BASKET_ACTION" => "ADD",
        "DETAIL_ADD_TO_BASKET_ACTION" => array(
            0 => "BUY",
        ),
        "DETAIL_SHOW_BASIS_PRICE" => "Y",
        "DETAIL_CHECK_SECTION_ID_VARIABLE" => "N",
        "DETAIL_DETAIL_PICTURE_MODE" => "IMG",
        "DETAIL_ADD_DETAIL_TO_SLIDER" => "N",
        "DETAIL_DISPLAY_PREVIEW_TEXT_MODE" => "E",
        "STORES" => array(
            0 => "1",
        ),
        "USER_FIELDS" => array(
            0 => "",
            1 => "",
        ),
        "FIELDS" => array(
            0 => "TITLE",
            1 => "ADDRESS",
            2 => "DESCRIPTION",
            3 => "PHONE",
            4 => "SCHEDULE",
            5 => "EMAIL",
            6 => "IMAGE_ID",
            7 => "COORDINATES",
            8 => "",
        ),
        "SHOW_EMPTY_STORE" => "Y",
        "SHOW_GENERAL_STORE_INFORMATION" => "N",
        "USE_BIG_DATA" => "Y",
        "BIG_DATA_RCM_TYPE" => "any",
        "COMMON_ADD_TO_BASKET_ACTION" => "ADD",
        "COMPONENT_TEMPLATE" => ".default",
        "USE_MAIN_ELEMENT_SECTION" => "Y",
        "SET_LAST_MODIFIED" => "N",
        "SECTION_BACKGROUND_IMAGE" => "-",
        "DETAIL_SET_CANONICAL_URL" => "Y",
        "DETAIL_BACKGROUND_IMAGE" => "-",
        "SHOW_DEACTIVATED" => "N",
        "PAGER_BASE_LINK_ENABLE" => "Y",
        "SHOW_404" => "Y",
        "MESSAGE_404" => "",
        "REVIEW_IBLOCK_TYPE" => "1c_catalog",
        "REVIEW_IBLOCK_ID" => "12",
        "DISABLE_INIT_JS_IN_COMPONENT" => "N",
        "DETAIL_SET_VIEWED_IN_COMPONENT" => "N",
        "USE_GIFTS_DETAIL" => "N",
        "USE_GIFTS_SECTION" => "N",
        "USE_GIFTS_MAIN_PR_SECTION_LIST" => "Y",
        "GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => "12",
        "GIFTS_DETAIL_HIDE_BLOCK_TITLE" => "N",
        "GIFTS_DETAIL_BLOCK_TITLE" => "Выберите один из подарков",
        "GIFTS_DETAIL_TEXT_LABEL_GIFT" => "Подарок",
        "GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT" => "12",
        "GIFTS_SECTION_LIST_HIDE_BLOCK_TITLE" => "N",
        "GIFTS_SECTION_LIST_BLOCK_TITLE" => "Подарки к товарам этого раздела",
        "GIFTS_SECTION_LIST_TEXT_LABEL_GIFT" => "Подарок",
        "GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",
        "GIFTS_SHOW_OLD_PRICE" => "Y",
        "GIFTS_SHOW_NAME" => "Y",
        "GIFTS_SHOW_IMAGE" => "Y",
        "GIFTS_MESS_BTN_BUY" => "Выбрать",
        "GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT" => "12",
        "GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE" => "N",
        "GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE" => "Выберите один из товаров, чтобы получить подарок",
        "SHOW_AVAILABLE_TAB" => "Y",
        "HIDE_AVAILABLE_TAB" => "N",
        "HIDE_MEASURES" => "Y",
        "SHOW_SECTION_BANNER" => "Y",
        "FILE_404" => "",
        "HIDE_NOT_AVAILABLE_OFFERS" => "Y",
        "DETAIL_STRICT_SECTION_CHECK" => "Y",
        "COMPATIBLE_MODE" => "N",
        "PAGER_BASE_LINK" => "",
        "PAGER_PARAMS_NAME" => "arrPager",
        "USER_CONSENT" => "N",
        "USER_CONSENT_ID" => "1",
        "USER_CONSENT_IS_CHECKED" => "Y",
        "USER_CONSENT_IS_LOADED" => "N",
        "DISPLAY_CHEAPER" => "N",
        "CHEAPER_FORM_ID" => "1",
        "DISPLAY_OFFERS_TABLE" => "N",
        "OFFERS_TABLE_PAGER_COUNT" => "10",
        "OFFERS_TABLE_DISPLAY_PICTURE_COLUMN" => "Y",
        "SHOW_SERVICES" => "N",
        "SERVICES_IBLOCK_TYPE" => "info",
        "SERVICES_IBLOCK_ID" => "11",
        "SALE_IBLOCK_TYPE" => "info",
        "SALE_IBLOCK_ID" => "6",
        "HIDE_DELIVERY_CALC" => "Y",
        "LAZY_LOAD_PICTURES" => "Y",
        "SEF_URL_TEMPLATES" => array(
            "sections" => "",
            "section" => "#SECTION_CODE_PATH#/",
            "element" => "#SECTION_CODE_PATH#/#ELEMENT_CODE#.html",
            "compare" => "compare/",
            "smart_filter" => "#SECTION_CODE_PATH#/filter/#SMART_FILTER_PATH#/apply/",
        )
    ),
    false
); ?><br/>

<? if ($APPLICATION->GetCurDir() == '/catalog/') { ?>
    <div class="medi-openning-shadow-block">
        <div class="medi-shadow-block-content">
            <div class="medi-shadow-block-text-content">
            </div>
            <div class="limiter">
                <h2 class="ff-medium">Что мы предлагаем</h2>
                Ортопедические изделия представляют собой различные средства для профилактики и лечения заболеваний
                опорно-двигательного аппарата. Это могут быть бандажи, шарнирные ортезы, корсеты, компрессионное белье,
                обувь и др. Товары подбираются в зависимости от показаний, целей применения, возраста человека, его
                пола.
                Продукция для профилактики необходимы для предотвращения развития патологических состояний, лечебные —
                для
                коррекции или снижения скорости прогрессирования уже выявленных изменений.
                Притом работают ортопедические изделия по-разному. По своей функции они делятся на:
                <ul class="galka">
                    <li>Фиксирующие. Иммобилизируют пораженную область или сустав (обычно это шарнирные ортезы или
                        шины);
                    </li>
                    <li> Корригирующие. Применяются для исправления выявленных дефектов (как правило, это ортопедические
                        стельки и обувь, корсеты);
                    </li>
                    <li> Функциональные. Защищают и поддерживают поврежденный сустав при выполнении функции (при
                        движениях).
                        Как правило это наиболее технологичные шарнирные ортезы.
                    </li>
                    <li>Компрессионные. Улучшают кровообращение в области сустава, а также способствуют нормализации его
                        двигательной функции. К компрессионным традиционно относят эластичные бандажи.
                    </li>
                    <li> Поддерживающие. Уменьшают нагрузку на определенный участок тела.</li>
                </ul>
                По силе фиксации элементы могут быть мягкими, жесткими и полужесткими. Они могут изготавливаться из
                полимерных или натуральных материалов, содержать пластиковые или металлические элементы конструкции.
                <h2 class="ff-medium">Преимущества ортопедических изделий medi</h2>
                Немецкая компания medi использует только качественные материалы в своей продукции, которая прошла
                множество
                научных исследований и получила награды от мировых исследовательских институтов, что доказывает ее
                эффективность.
                <p>Ассортимент ортопедических салонов medi включает:</p>
                <ul class="galka">
                    <li> компрессионный трикотаж;</li>
                    <li> обувь, стельки;</li>
                    <li> бандажи и корректоры осанки;</li>
                    <li> одежду для спорта;</li>
                    <li> матрасы и подушки;</li>
                    <li> товары после мастэктомии;</li>
                    <li> средства реабилитации: костыли, трости, палки для скандинавской ходьбы;</li>
                    <li> массажеры и тренажеры;</li>
                    <li> БАДы;</li>
                    <li> товары для детей;</li>
                    <li> косметику.</li>
                </ul>
                <a href="/spb/delivery/" class="theme-link-dashed" target="_blank">Доставка</a> производится в пункты
                выдачи
                СДЭК, Boxberry, «Почтой России». Вы также можете вызвать курьера на указанный адрес или забрать заказ
                самостоятельно в ортопедическом салоне. Задать вопросы и получить помощь в выборе изделий можно по
                телефону.

            </div>

        </div>
    </div>
    <div id="medi-openning-more-button" class="medi-position-more-button">
        Подробнее
    </div>


    <script>

        if ($('#medi-openning-more-button').length) {
            $('#medi-openning-more-button').on("click", function () {
                console.log("click");
                if (!$(this).data('status')) {
                    $('.medi-openning-shadow-block').addClass("is-active");
                    $(this).html('Скрыть');
                    $(this).data('status', true);
                } else {
                    $('.medi-openning-shadow-block').removeClass("is-active");
                    $(this).html('Подробнее');
                    $(this).data('status', false);
                }
            });
        }
    </script>
<?
} ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
