<? IncludeTemplateLangFile(__FILE__); ?>
<? $APPLICATION->ShowViewContent("landing_page_bottom_text_container"); ?>
<? if (INDEX_PAGE != "Y"): ?></div><? endif; ?>
</div>
<? $APPLICATION->ShowViewContent("no_main_container"); ?>

<? global $USER; ?>


<div id="footer"<? if (!empty($TEMPLATE_FOOTER_VARIANT) && $TEMPLATE_FOOTER_VARIANT != "default"): ?> class="variant_<?= $TEMPLATE_FOOTER_VARIANT ?>"<? endif; ?>>
    <div class="fc">
        <div class="limiter">
            <div id="rowFooter">
                <div id="leftFooter">
                    <div class="footerRow">
                        <div class="column">
                            <span class="heading"><? $APPLICATION->IncludeFile(SITE_DIR . "sect_footer_menu_heading.php", array(), array("MODE" => "text", "NAME" => GetMessage("SECT_FOOTER_MENU_HEADING"), "TEMPLATE" => "sect_footer_menu_heading.php")); ?></span>
                            <? $APPLICATION->IncludeComponent(
                                "bitrix:menu",
                                "footerCatalog",
                                array(
                                    "ROOT_MENU_TYPE" => "left",
                                    "MENU_CACHE_TYPE" => "A",
                                    "MENU_CACHE_TIME" => "36000000",
                                    "MENU_CACHE_USE_GROUPS" => "N",
                                    "MENU_CACHE_GET_VARS" => array(),
                                    "MAX_LEVEL" => "1",
                                    "CHILD_MENU_TYPE" => "top",
                                    "USE_EXT" => "Y",
                                    "DELAY" => "N",
                                    "ALLOW_MULTI_SELECT" => "N",
                                    "COMPONENT_TEMPLATE" => "footerCatalog",
                                    "CACHE_SELECTED_ITEMS" => "N"
                                ),
                                false
                            ); ?>
                        </div>
                        <div class="column">
                            <span class="heading"><? $APPLICATION->IncludeFile(SITE_DIR . "sect_footer_menu_heading2.php", array(), array("MODE" => "text", "NAME" => GetMessage("SECT_FOOTER_MENU_HEADING2"), "TEMPLATE" => "sect_footer_menu_heading2.php")); ?></span>
                            <? $APPLICATION->IncludeComponent(
                                "bitrix:menu",
                                "footerOffers",
                                array(
                                    "ROOT_MENU_TYPE" => "service",
                                    "MENU_CACHE_TYPE" => "A",
                                    "MENU_CACHE_TIME" => "360000",
                                    "MENU_CACHE_USE_GROUPS" => "N",
                                    "MENU_CACHE_GET_VARS" => array(),
                                    "MAX_LEVEL" => "1",
                                    "CHILD_MENU_TYPE" => "top",
                                    "USE_EXT" => "N",
                                    "DELAY" => "Y",
                                    "ALLOW_MULTI_SELECT" => "N",
                                    "COMPONENT_TEMPLATE" => "footerOffers",
                                    "CACHE_SELECTED_ITEMS" => "N"
                                ),
                                false
                            ); ?>
                        </div>
                        <div class="column">
                            <span class="heading"><? $APPLICATION->IncludeFile(SITE_DIR . "sect_footer_menu_heading3.php", array(), array("MODE" => "text", "NAME" => GetMessage("SECT_FOOTER_MENU_HEADING3"), "TEMPLATE" => "sect_footer_menu_heading3.php")); ?></span>
                            <? $APPLICATION->IncludeComponent(
                                "bitrix:menu",
                                "footerHelp",
                                array(
                                    "ROOT_MENU_TYPE" => "company",
                                    "MENU_CACHE_TYPE" => "A",
                                    "MENU_CACHE_TIME" => "3600000",
                                    "MENU_CACHE_USE_GROUPS" => "N",
                                    "MENU_CACHE_GET_VARS" => array(),
                                    "MAX_LEVEL" => "1",
                                    "CHILD_MENU_TYPE" => "top",
                                    "USE_EXT" => "N",
                                    "DELAY" => "N",
                                    "ALLOW_MULTI_SELECT" => "N",
                                    "CACHE_SELECTED_ITEMS" => "N",
                                    "COMPONENT_TEMPLATE" => "footerHelp"
                                ),
                                false
                            ); ?>
                        </div>
                    </div>
                </div>
                <div id="rightFooter">
                    <table class="rightTable">
                        <tr class="footerRow">
                            <td class="leftColumn">
                                <? $APPLICATION->IncludeFile(SITE_DIR . "sect_footer_left.php", array(), array("MODE" => "text", "NAME" => GetMessage("SECT_FOOTER_LEFT"), "TEMPLATE" => "sect_footer_left.php")); ?>
                                <? $APPLICATION->IncludeFile(SITE_DIR . "sect_footer_left2.php", array(), array("MODE" => "text", "NAME" => GetMessage("SECT_FOOTER_LEFT2"), "TEMPLATE" => "sect_footer_left2.php")); ?>
                                <? $APPLICATION->IncludeFile(SITE_DIR . "sect_footer_left3.php", array(), array("MODE" => "text", "NAME" => GetMessage("SECT_FOOTER_LEFT3"), "TEMPLATE" => "sect_footer_left3.php")); ?>
                            </td>
                            <td class="rightColumn">
                                <div class="wrap">
                                    <? $APPLICATION->IncludeFile(SITE_DIR . "sect_footer_social.php", array(), array("MODE" => "text", "NAME" => GetMessage("SECT_FOOTER_LEFT4"), "TEMPLATE" => "sect_footer_social.php")); ?>
                                    <? $APPLICATION->IncludeFile(SITE_DIR . "sect_footer_left4.php", array(), array("MODE" => "text", "NAME" => GetMessage("SECT_FOOTER_LEFT4"), "TEMPLATE" => "sect_footer_left4.php")); ?>
                                    <? if (!empty($arTemplateSettings["TEMPLATE_GOOGLE_CODE"])): ?>
                                        <?= trim($arTemplateSettings["TEMPLATE_GOOGLE_CODE"]) ?>
                                    <? endif; ?>
                                    <? if (!empty($arTemplateSettings["TEMPLATE_COUNTERS_CODE"])): ?>
                                        <?= trim($arTemplateSettings["TEMPLATE_COUNTERS_CODE"]) ?>
                                    <? endif; ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="footer_small">
    <div class="fc">
        <div class="limiter">
            <div class="column">
                <? $APPLICATION->IncludeFile(SITE_DIR . "sect_footer_social.php", array(), array("MODE" => "text", "NAME" => GetMessage("SECT_FOOTER_LEFT4"), "TEMPLATE" => "sect_footer_social.php")); ?>
            </div>

            <div class="column">
                <span class="heading">Сервис и поддержка</span>
                <? $APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "footerOffers",
                    array(
                        "ROOT_MENU_TYPE" => "service",
                        "MENU_CACHE_TYPE" => "A",
                        "MENU_CACHE_TIME" => "3600000",
                        "MENU_CACHE_USE_GROUPS" => "N",
                        "MENU_CACHE_GET_VARS" => array(),
                        "MAX_LEVEL" => "1",
                        "CHILD_MENU_TYPE" => "top",
                        "USE_EXT" => "N",
                        "DELAY" => "N",
                        "ALLOW_MULTI_SELECT" => "N",
                        "COMPONENT_TEMPLATE" => "footerOffers",
                        "CACHE_SELECTED_ITEMS" => "N"
                    ),
                    false
                ); ?>
            </div>
            <div class="column">
                <span class="heading"><? $APPLICATION->IncludeFile(SITE_DIR . "sect_footer_menu_heading3.php", array(), array("MODE" => "text", "NAME" => GetMessage("SECT_FOOTER_MENU_HEADING3"), "TEMPLATE" => "sect_footer_menu_heading3.php")); ?></span>
                <? $APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "footerHelp",
                    array(
                        "ROOT_MENU_TYPE" => "company",
                        "MENU_CACHE_TYPE" => "A",
                        "MENU_CACHE_TIME" => "3600000",
                        "MENU_CACHE_USE_GROUPS" => "N",
                        "MENU_CACHE_GET_VARS" => array(),
                        "MAX_LEVEL" => "1",
                        "CHILD_MENU_TYPE" => "top",
                        "USE_EXT" => "N",
                        "DELAY" => "N",
                        "ALLOW_MULTI_SELECT" => "N",
                        "CACHE_SELECTED_ITEMS" => "N",
                        "COMPONENT_TEMPLATE" => "footerHelp"
                    ),
                    false
                ); ?>
            </div>
            <div class="b_phones">
                <? $APPLICATION->IncludeFile(SITE_DIR . "sect_footer_mob_phones.php", array(), array("MODE" => "text", "NAME" => "", "TEMPLATE" => "sect_footer_mob_phones.php")); ?>
            </div>

            <p class="copyrt">2014-<?= date("Y") ?> &copy; Интернет-магазин ортопедических салонов medi</p>
        </div>
    </div>
</div>

<div id="footerLine" class="footer_mob">
    <div class="limiter">
        <div class="footer_mob_links">
            <div>
                <a href="/salons/"
                   class="large-link footer_icon_salons <?= (strpos($APPLICATION->GetCurDir(), 'salons/') ? 'active' : '') ?>"><br><span>Салоны</span></a>
            </div>
            <div>
                <a href="/catalog/" onclick="showCatalogMenu(); return false;"
                   class="large-link footer_icon_catalog <?= (strpos($APPLICATION->GetCurDir(), 'catalog/') ? 'active' : ''); ?>"
                   id="catalogSlideButton"><br><span>Каталог</span></a>
            </div>
            <div>
                <?
                $APPLICATION->IncludeComponent("medi:favorites.products", "", []);
                ?>
                <br><span class="<?= (strpos($APPLICATION->GetCurDir(), 'lk/') ? 'active' : '') ?>">Избранное</span>
            </div>
            <div>
                <div id="flushFooterCart">
                    <? $APPLICATION->IncludeComponent(
                        "bitrix:sale.basket.basket.line",
                        "bottomCart",
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
                            "COMPONENT_TEMPLATE" => "bottomCart"
                        ),
                        false
                    ); ?>
                </div>

            </div>
            <div><a href="/lk/"
                    class="large-link footer_icon_lk  <?= (strpos($APPLICATION->GetCurDir(), 'lk/') ? 'active' : '') ?> <?= ($USER->IsAuthorized() ? 'authorized' : '') ?>">
                    <br><span>Профиль</span></a></div>
        </div>

    </div>
</div>
</div>
<div id="overlap"></div>

<? $APPLICATION->IncludeComponent(
    "bitrix:main.include",
    ".default",
    array(
        "AREA_FILE_SHOW" => "sect",
        "AREA_FILE_SUFFIX" => "fastbuy",
        "AREA_FILE_RECURSIVE" => "Y",
        "EDIT_TEMPLATE" => ""
    ),
    false
); ?>
<? $APPLICATION->IncludeComponent(
    "bitrix:main.include",
    ".default",
    array(
        "AREA_FILE_SHOW" => "sect",
        "AREA_FILE_SUFFIX" => "fastorder",
        "AREA_FILE_RECURSIVE" => "Y",
        "EDIT_TEMPLATE" => ""
    ),
    false
); ?>

<? if ($USER->IsAuthorized() && !empty(array_intersect([29], $USER->GetUserGroupArray()))) {
    $APPLICATION->IncludeComponent(
        
        "bitrix:main.include",
        ".default",
        array(
            "AREA_FILE_SHOW" => "sect",
            "AREA_FILE_SUFFIX" => "smporder",
            "AREA_FILE_RECURSIVE" => "Y",
            "EDIT_TEMPLATE" => ""
        ),
        false
    );
} ?>

<? $APPLICATION->IncludeComponent(
    "bitrix:main.include",
    ".default",
    array(
        "AREA_FILE_SHOW" => "sect",
        "AREA_FILE_SUFFIX" => "landing",
        "AREA_FILE_RECURSIVE" => "Y",
        "EDIT_TEMPLATE" => ""
    ),
    false
); ?>

<? $APPLICATION->IncludeComponent(
    "dresscode:settings",
    ".default",
    array(
        "COMPONENT_TEMPLATE" => ".default",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "3600000"
    ),
    false
); ?>

<div id="upButton">
    <a href="#"></a>
</div>

<script type="text/javascript">
    var ajaxPath = "<?=SITE_DIR?>ajax.php";
    var SITE_DIR = "<?=SITE_DIR?>";
    var SITE_ID = "<?=SITE_ID?>";
    var TEMPLATE_PATH = "<?=SITE_TEMPLATE_PATH?>";
</script>

<script type="text/javascript">
    var LANG = {
        BASKET_ADDED: "<?=GetMessage("BASKET_ADDED")?>",
        WISHLIST_ADDED: "<?=GetMessage("WISHLIST_ADDED")?>",
        ADD_COMPARE_ADDED: "<?=GetMessage("ADD_COMPARE_ADDED")?>",
        ADD_CART_LOADING: "<?=GetMessage("ADD_CART_LOADING")?>",
        ADD_BASKET_DEFAULT_LABEL: "<?=GetMessage("ADD_BASKET_DEFAULT_LABEL")?>",
        ADDED_CART_SMALL: "<?=GetMessage("ADDED_CART_SMALL")?>",
        CATALOG_AVAILABLE: "<?=GetMessage("CATALOG_AVAILABLE")?>",
        GIFT_PRICE_LABEL: "<?=GetMessage("GIFT_PRICE_LABEL")?>",
        CATALOG_ON_ORDER: "<?=GetMessage("CATALOG_ON_ORDER")?>",
        CATALOG_NO_AVAILABLE: "<?=GetMessage("CATALOG_NO_AVAILABLE")?>",
        FAST_VIEW_PRODUCT_LABEL: "<?=GetMessage("FAST_VIEW_PRODUCT_LABEL")?>",
        CATALOG_ECONOMY: "<?=GetMessage("CATALOG_ECONOMY")?>",
        WISHLIST_SENDED: "<?=GetMessage("WISHLIST_SENDED")?>",
        REQUEST_PRICE_LABEL: "<?=GetMessage("REQUEST_PRICE_LABEL")?>",
        REQUEST_PRICE_BUTTON_LABEL: "<?=GetMessage("REQUEST_PRICE_BUTTON_LABEL")?>",
        ADD_SUBSCRIBE_LABEL: "<?=GetMessage("ADD_SUBSCRIBE_LABEL")?>",
        REMOVE_SUBSCRIBE_LABEL: "<?=GetMessage("REMOVE_SUBSCRIBE_LABEL")?>"
    };
</script>

<script type="text/javascript">
    <?if(!empty($arTemplateSettings)):?>
    var globalSettings = {
        <?foreach($arTemplateSettings as $settingsIndex => $nextSettingValue):?>
        <?if(!DwSettings::checkSecretSettingsByIndex($settingsIndex)):?>
        "<?=$settingsIndex?>": '<?=$nextSettingValue?>',
        <?endif;?>
        <?endforeach;?>
    }
    <?endif;?>
</script>
<div id="bg-layer-for-tooltip"></div>
<? $APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    array(
        "AREA_FILE_RECURSIVE" => "Y",
        "AREA_FILE_SHOW" => "file",
        "AREA_FILE_SUFFIX" => "alert",
        "EDIT_TEMPLATE" => "",
        "PATH" => "/include/alert.php"
    )
); ?>
<script>
    waitForVk(function () {
        if (vViewedProdsPrice > 0) {
            const eventParams = {
                "products": vViewedProds,

                //"business_value" : 88,
                "total_price": vViewedProdsPrice
            };
            <?if (!strpos($APPLICATION->GetCurDir(), 'catalog/') && !strpos($APPLICATION->GetCurDir(), 'personal/')) {?>
            <?if ($APPLICATION->GetCurDir() == '/') {
            $view_event = 'view_home';
        } else {
            $view_event = 'view_other';
        }?>
            VK.Retargeting.ProductEvent(PRICE_LIST_ID, '<?=$view_event;?>', eventParams);
            <?}?>
        }
    });
</script>
<? if ($nUserID > 0):
    ?>
    <script>
        waitForYm(function () {
            ym(30121774, 'userParams', {
                UserID: <?=$nUserID?>
            });
        });</script><?
endif; ?>

<? $Mobile_Detect = new Mobile_Detect;
if ($Mobile_Detect->isMobile()) {
    $phone = $GLOBALS['medi']['phones'][SITE_ID];
    $sphone = str_replace([' ', '-'], '', $phone);
    ?>
    
    <? php/*
<a href="tel:<?=$sphone?>" class="phButton  link callBack" id="phoneMobButton" onclick="ym(30121774, 'reachGoal', 'CLICK_PHONE');return true;"></a>
*/
    ?>
<? }/*else{
    ?>
    <a href="#" class="phButton openWebFormModal link callBack" id="phoneButton" data-id="2" ></a><?
    }*/ ?>

<!--noindex-->
<? $APPLICATION->IncludeComponent(
    "bitrix:main.include",
    ".default",
    array(
        "AREA_FILE_SHOW" => "sect",
        "AREA_FILE_SUFFIX" => "callBack",
        "AREA_FILE_RECURSIVE" => "Y",
        "EDIT_TEMPLATE" => ""
    ),
    false
); ?><!--/noindex-->
<?
global $FAVORITE_ITEMS;
?>
<script>
    $('.b-card-favorite').each(function (){
        $(this).removeClass('active');
    });
</script>
<?
/* Отмечаем избранные товары во всех блоках на всех страницах */
if (!empty($FAVORITE_ITEMS)) {
    ?>
    <input type="hidden" name="favorite_items" value="<?=json_encode($FAVORITE_ITEMS)?>">
    <?php
    foreach ($FAVORITE_ITEMS as $favoriteProductItem) {?>
        <script>
            if ($('.b-card-favorite[data-product-id="<?=$favoriteProductItem?>"]')) {
                $('.b-card-favorite[data-product-id="<?=$favoriteProductItem?>"]').addClass('active');
            }
        </script>
    <?}
}
if ($USER->IsAuthorized()) {?>
    <input type="hidden" name="user_auth" value="1">
    <?
}
?>
</body>
</html>