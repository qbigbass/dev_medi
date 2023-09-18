<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
global $nUserID; ?>

<? if (!empty($arResult["ITEMS"])): ?>
    <div id="catalogSection">
        <?
        if ($arParams["DISPLAY_TOP_PAGER"]) {
            ?><? echo $arResult["NAV_STRING"]; ?><?
        }
        ?>
        <div class="items productList">
            <? $pos_counter = 1; ?>
            <? foreach ($arResult["ITEMS"] as $index => $arElement): ?>
                <? $APPLICATION->IncludeComponent(
                    "dresscode:catalog.item",
                    ".default",
                    array(
                        "CACHE_TIME" => $arParams["CACHE_TIME"],
                        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                        "HIDE_MEASURES" => $arParams["HIDE_MEASURES"],
                        "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
                        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                        "PRODUCT_ID" => $arElement["ID"],
                        "PRODUCT_SKU_FILTER" => $arResult["FILTER"],
                        "PICTURE_HEIGHT" => "",
                        "PICTURE_WIDTH" => "",
                        "PRODUCT_PRICE_CODE" => $arParams["PRICE_CODE"],
                        "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                        "LAZY_LOAD_PICTURES" => $arParams["LAZY_LOAD_PICTURES"],
                        "CURRENCY_ID" => $arParams["CURRENCY_ID"],
                        "POS_COUNT" => $pos_counter,
                        "SLIDER_ON" => $arParams["SLIDER_ON"]
                    ),
                    false,
                    array("HIDE_ICONS" => "Y")
                ); ?>
                <? $pos_counter++; ?>
            <? endforeach; ?>
            <div class="clear"></div>
        </div>
        <?
        if ($arParams["DISPLAY_BOTTOM_PAGER"]) {
            ?><? echo $arResult["NAV_STRING"]; ?><?
        }
        ?>
        
        <? if (empty($arParams["HIDE_DESCRIPTION_TEXT"]) || $arParams["HIDE_DESCRIPTION_TEXT"] != "Y"): ?>
            <? if (empty($_GET["PAGEN_" . $arResult["NAV_NUM_PAGE"]]) && empty($arResult["FILTER"]["PROPERTY_TAGS_VALUE"])): ?>
                <div><?= $arResult["~DESCRIPTION"] ?></div>
            <? endif; ?>
        <? endif; ?>
        <script>
            //lazy load
            checkLazyItems();
        </script>
        <?if ($arParams["SLIDER_ON"] !== "N"):?>
            <script>
                slickItems();
            </script>
        <? endif; ?>
        <? if (!empty($arResult['ITEMS'])) {
            
            $cItems = [];
            $mprods = [];
            foreach ($arResult['ITEMS'] as $k => $arItem) {
                $cItems[] = $k;
                $mprods[] = $arItem['ID'];
                
            }
            if (!empty($cItems)) {
                $obElement = CIBlockElement::GetList(['ID' => 'DESC'], ['IBLOCK_ID' => 19, 'ACTIVE' => 'Y', 'PROPERTY_CML2_LINK' => $cItems], ['PROPERTY_CML2_LINK', 'ID'], false, ['ID', 'IBLOCK_ID']);
                $prods = [];
                while ($arElement = $obElement->GetNext()) {
                    $prods[$arElement['PROPERTY_CML2_LINK_VALUE']] = $arElement['ID'];
                }
            }
        }
        if (!empty($prods) && $arParams["DISABLE_CRITEO"] != "Y") {
            ?>
            <script type='text/javascript'>
                var dataLayer = dataLayer || [];
                dataLayer.push({
                    'event': 'crto_listingpage',
                    crto: {
                        'email': '<?=$nUserEmail?>',
                        'products': ['<?=implode("','", $prods);?>']
                    }
                });
            </script>
        <? }elseif (!empty($mprods) && $arParams["DISABLE_CRITEO"] != "Y"){
        ?>
            <script type='text/javascript'>
                var dataLayer = dataLayer || [];
                dataLayer.push({
                    'event': 'crto_listingpage',
                    crto: {
                        'email': '<?=$nUserEmail?>',
                        'products': ['<?=implode("','", $mprods);?>']
                    }
                });
            </script>
        <? } ?>
        <script>
            var _rutarget = window._rutarget || [];
            _rutarget.push({
                'event': 'showCategory',
                'categoryCode': '<?=$arResult['ID']?>',
                'categoryName': '<?=str_replace(["\r\n", "\t", "\n"], " ", $arResult['IPROPERTY_VALUES']['SECTION_PAGE_TITLE'])?>'
            });

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

        <script>
            $products = new Array();
            $(".itemCard").each(function () {
                $products.push({id: $(this).data("product-id"), price: $(this).data("product-price"), quantity: 1});
            });


            window.gdeslon_q = window.gdeslon_q || [];
            window.gdeslon_q.push({
                page_type: "list", //тип страницы: main, list, card, basket, thanks, other
                merchant_id: "104092", //id оффера в нашей системе
                order_id: "", //id заказа
                category_id: "<?=($arResult['ID'] ? $arResult['ID'] : ($arResult['ITEMS'][0]['PARENT_PRODUCT']['IBLOCK_SECTION_ID'] ? $arResult['ITEMS'][0]['PARENT_PRODUCT']['IBLOCK_SECTION_ID'] : $arResult['ITEMS'][0]['IBLOCK_SECTION_ID']))?>", //id текущей категории
                products: $products,
                deduplication: "<?=DEDUPLICATION?>", //параметр дедупликации заказов (по умолчанию - заказ для Gdeslon)
                user_id: "<?=$nUserID?>" //идентификатор пользователя
            });
        </script>
        <script>
        /* Отметим избранные товары на странице (Загрузка списка товаров через AJAX пагинацию) */
        if ($('input[name=favorite_items]').length > 0) {
            console.log('squares template.php!');
            let inputFavoriteItemsValue = $('input[name=favorite_items]').val();
            if (inputFavoriteItemsValue != '') {
                let favoriteItems = JSON.parse(inputFavoriteItemsValue);
                for (let key in favoriteItems) {
                    if ($('.b-card-favorite[data-product-id="' + favoriteItems[key] + '"]')) {
                        $('.b-card-favorite[data-product-id="' + favoriteItems[key] + '"]').addClass('active');
                        $('.b-card-favorite[data-product-id="<?=$favoriteProductItem?>"]').find('span').text('В избранном');
                    }
                }
            }
        }
        </script>
    </div>
<? elseif ($arParams['HIDE_EMPTY'] != 'Y'): ?>
    <div id="empty">
        <div class="emptyWrapper">
            <div class="pictureContainer">
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/emptyFolder.png" alt="<?= GetMessage("EMPTY_HEADING") ?>"
                     class="emptyImg">
            </div>
            <div class="info">
                <h3><?= GetMessage("EMPTY_HEADING") ?></h3>
                <p><?= GetMessage("EMPTY_TEXT") ?></p>
                <a href="<?= SITE_DIR ?>" class="back"><?= GetMessage("MAIN_PAGE") ?></a>
            </div>
        </div>
        <? /*$APPLICATION->IncludeComponent("bitrix:menu", "emptyMenu", Array(
			"ROOT_MENU_TYPE" => "left",
				"MENU_CACHE_TYPE" => "N",
				"MENU_CACHE_TIME" => "3600",
				"MENU_CACHE_USE_GROUPS" => "Y",
				"MENU_CACHE_GET_VARS" => "",
				"MAX_LEVEL" => "1",
				"CHILD_MENU_TYPE" => "left",
				"USE_EXT" => "Y",
				"DELAY" => "N",
				"ALLOW_MULTI_SELECT" => "N",
			),
			false
		);*/ ?>
    </div>
<? endif; ?>
