<? $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/headers/header8/css/style.css"); ?>
<? $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/headers/header8/css/types/" . $TEMPLATE_HEADER_TYPE . ".css"); ?>
<?php
ob_start();
$APPLICATION->IncludeComponent("twofingers:location", "", array());
$location_html = ob_get_contents();
ob_end_clean();
?>
<div class="header">
    <div id="subHeader8">
        <div class="limiter">
            <div class="header_mob">
                <div class="flex">
                    <div class="flex">
                        <div class="logo_mob">
                            <? $APPLICATION->IncludeFile(SITE_DIR . "sect_top_logo.php"); ?>
                        </div>
                        <div id="site_name">
                            <? $APPLICATION->IncludeFile(SITE_DIR . "sect_top_text.php"); ?>
                        </div>
                    </div>
                    <div class="b_head_geo">
                        <div id="geoPosition" class="geoPositionBlock">
                            <ul>
                                <li>
                                    <div class="user-geo-position">
                                        <!--noindex-->
                                        <?= $location_html ?>
                                        <!--/noindex-->
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="header_new">
                <div class="b_logo">
                    <? $APPLICATION->IncludeFile(SITE_DIR . "sect_top_logo.php"); ?>
                </div>
                <div class="b_head_main">
                    <div class="b_head_top">
                        <div class="b_head_menu">
                            <? $APPLICATION->IncludeComponent("bitrix:menu", "topMenu7", array(
                                "ROOT_MENU_TYPE" => "top",
                                "MENU_CACHE_TYPE" => "A",
                                "MENU_CACHE_TIME" => "3600000",
                                "MENU_CACHE_USE_GROUPS" => "N",
                                "MENU_CACHE_GET_VARS" => "",
                                "MAX_LEVEL" => "1",
                                "CHILD_MENU_TYPE" => "top",
                                "USE_EXT" => "N",
                                "DELAY" => "N",
                                "ALLOW_MULTI_SELECT" => "N",
                                "CACHE_SELECTED_ITEMS" => "N"
                            ),
                                false
                            ); ?>
                        </div>
                        <div class="b_head_geo">
                            <div id="geoPosition2" class="geoPositionBlock">
                                <ul>
                                    <li>
                                        <div class="user-geo-position">
                                            <!--noindex-->
                                            <?= $location_html; ?>
                                            <!--/noindex-->
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="b_head_mid">
                        <div id="site_name" class="b_head_text">
                            <? $APPLICATION->IncludeFile(SITE_DIR . "sect_top_text.php"); ?>
                        </div>
                        <div class="b_head_search">
                            <div id="topSearchLine">
                                <div class="topSearchDesktop">
                                    <? $APPLICATION->IncludeComponent(
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
                                                0 => SITE_ID == 's2' ? "BASE_SPB" : "BASE",
                                            ),
                                            "SHOW_PREVIEW" => "Y",
                                            "PREVIEW_WIDTH" => "75",
                                            "PREVIEW_HEIGHT" => "75",
                                            "CONVERT_CURRENCY" => "Y",
                                            "COMPONENT_TEMPLATE" => "version3",
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
                                            "ANIMATE_HINTS" => array(),
                                            "ANIMATE_HINTS_SPEED" => "1"
                                        ),
                                        false
                                    ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="b_head_phone">
                            <? $APPLICATION->IncludeComponent(
                                "bitrix:main.include",
                                ".default",
                                array(
                                    "AREA_FILE_SHOW" => "sect",
                                    "AREA_FILE_SUFFIX" => "phone",
                                    "AREA_FILE_RECURSIVE" => "Y",
                                    "EDIT_TEMPLATE" => ""
                                ),
                                false
                            ); ?>
                            <a href="#" class="openWebFormModal link callBack" data-id="2">Заказать звонок</a>
                        </div>

                        <div class="b_head_user">
                            <div class="topAuthContainer">
                                <? $APPLICATION->IncludeComponent("bitrix:system.auth.form", "top3", array(
                                    "REGISTER_URL" => "",
                                    "FORGOT_PASSWORD_URL" => "",
                                    "PROFILE_URL" => "",
                                    "SHOW_ERRORS" => "N",
                                ),
                                    false
                                ); ?>
                            </div>
                        </div>
                        <div class="b_head_cart cart">
                            <div id="flushTopCart">
                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:sale.basket.basket.line",
                                    "topCart_new",
                                    array(
                                        "HIDE_ON_BASKET_PAGES" => "N",
                                        "PATH_TO_BASKET" => SITE_DIR . "personal/cart/",
                                        "PATH_TO_ORDER" => SITE_DIR . "personal/order/make/",
                                        "PATH_TO_PERSONAL" => SITE_DIR . "personal/",
                                        "PATH_TO_PROFILE" => SITE_DIR . "personal/",
                                        "PATH_TO_REGISTER" => SITE_DIR . "login/",
                                        "POSITION_FIXED" => "N",
                                        "SHOW_AUTHOR" => "N",
                                        "SHOW_EMPTY_VALUES" => "Y",
                                        "SHOW_NUM_PRODUCTS" => "Y",
                                        "SHOW_PERSONAL_LINK" => "N",
                                        "SHOW_PRODUCTS" => "Y",
                                        "SHOW_TOTAL_PRICE" => "Y",
                                        "COMPONENT_TEMPLATE" => "topCart",
                                        "SHOW_DELAY" => "N",
                                        "SHOW_NOTAVAIL" => "N",
                                        "SHOW_SUBSCRIBE" => "N",
                                        "SHOW_IMAGE" => "Y",
                                        "SHOW_PRICE" => "Y",
                                        "SHOW_SUMMARY" => "Y"
                                    ),
                                    false
                                ); ?>
                            </div>
                        </div>
                    </div>

                </div>
                <div id="site_name-mobile" class="b_head_text">
                    <? $APPLICATION->IncludeFile(SITE_DIR . "sect_top_text.php"); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="menuContainerColor">
        <? $APPLICATION->IncludeComponent(
            "bitrix:menu",
            "catalogMenu",
            array(
                "ROOT_MENU_TYPE" => "left",
                "MENU_CACHE_TYPE" => "A",
                "MENU_CACHE_TIME" => "864000",
                "MENU_CACHE_USE_GROUPS" => "N",
                "MENU_CACHE_GET_VARS" => array(),
                "MAX_LEVEL" => "2",
                "CHILD_MENU_TYPE" => "top",
                "USE_EXT" => "Y",
                "DELAY" => "N",
                "ALLOW_MULTI_SELECT" => "N",
                "CACHE_SELECTED_ITEMS" => "N",
                "COMPONENT_TEMPLATE" => "catalogMenu"
            ),
            false,
            array(
                "ACTIVE_COMPONENT" => "Y"
            )
        ); ?>
    </div>
</div>

<? $APPLICATION->IncludeComponent(
    "medi:topalert",
    "mobile",
    array(
        "IBLOCK_ID" => 26,
        "IS_MOBILE" => "Y",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "864000",
    ),
    false
); ?>