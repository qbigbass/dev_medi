<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?><? $this->setFrameMode(false); ?>
<? if (!empty($arResult) && $arResult["ID"] > 0): ?>
    <? $uniqID = CAjax::GetComponentID($this->__component->__name, $this->__component->__template->__name, false); ?>
    <?
    // формируем название категории для аналитики, убираем лишние элементы
    $secturl = explode("/", $arResult['DETAIL_PAGE_URL']);
    $sectcount = count($secturl) - 1;
    unset($secturl[$sectcount]);
    unset($secturl[0]);
    unset($secturl[1]);

    if (!empty($arResult["PARENT_PRODUCT"]["EDIT_LINK"])) {
        $this->AddEditAction($arResult["ID"], $arResult["PARENT_PRODUCT"]["EDIT_LINK"], CIBlock::GetArrayByID($arResult["PARENT_PRODUCT"]["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arResult["ID"], $arResult["PARENT_PRODUCT"]["DELETE_LINK"], CIBlock::GetArrayByID($arResult["PARENT_PRODUCT"]["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage("CT_BNL_ELEMENT_DELETE_CONFIRM")));
    }
    if (!empty($arResult["EDIT_LINK"])) {
        $this->AddEditAction($arResult["ID"], $arResult["EDIT_LINK"], CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arResult["ID"], $arResult["DELETE_LINK"], CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage("CT_BNL_ELEMENT_DELETE_CONFIRM")));
    }
    ?>
    <div class="item product sku itemCard"
         id="<?= $this->GetEditAreaId($arResult["ID"]); ?>"
         data-product-iblock-id="<?= $arParams["IBLOCK_ID"] ?>"
         data-position="<?= $arResult['POS_COUNT'] ?>"
         data-from-cache="<?= $arResult["FROM_CACHE"] ?>"
         data-convert-currency="<?= $arParams["CONVERT_CURRENCY"] ?>"
         data-product-price="<?= $arResult['PRICE']['DISCOUNT_PRICE']; ?>"
         data-product-category="<?= implode("/", $secturl); ?>"
         data-product-category_id="<?= ($arResult['PARENT_PRODUCT']['IBLOCK_SECTION_ID'] ? $arResult['PARENT_PRODUCT']['IBLOCK_SECTION_ID'] : $arResult['IBLOCK_SECTION_ID']); ?>"
         data-product-articul="<?= $arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']; ?>"
         data-product-brand="<?= $arResult['PARENT_PRODUCT']['BRAND']; ?>"
         data-currency-id="<?= $arParams["CURRENCY_ID"] ?>"
         data-product-name="<?= ($arResult['PARENT_PRODUCT']['NAME'] ? $arResult['PARENT_PRODUCT']['NAME'] : $arResult['NAME']); ?>"
         data-product-id="<?= ($arResult['PARENT_PRODUCT']['ID'] ? $arResult['PARENT_PRODUCT']['ID'] : $arResult['ID']); ?>"
         data-iblock-id="<?= $arResult["SKU_INFO"]["IBLOCK_ID"] ?>"
         data-prop-id="<?= $arResult["SKU_INFO"]["SKU_PROPERTY_ID"] ?>"
         data-product-width="<?= $arParams["PICTURE_WIDTH"] ?>" data-product-height="<?= $arParams["PICTURE_HEIGHT"] ?>"
         data-hide-measure="<?= $arParams["HIDE_MEASURES"] ?>"
         data-currency="<?= $arResult["EXTRA_SETTINGS"]["CURRENCY"] ?>"
         data-hide-not-available="<?= $arParams["HIDE_NOT_AVAILABLE"] ?>"
         data-price-code="<?= implode("||", $arParams["PRODUCT_PRICE_CODE"]) ?>">
        <script>vViewedProds.push({id: "<?=($arResult['PARENT_PRODUCT']['ID'] ? $arResult['PARENT_PRODUCT']['ID'] : $arResult['ID']);?>"})</script>
        <?

        $prodid = $arResult['PRICE']['PRODUCT_ID'];
        if (!empty($arResult['MAX_PRICE'][$prodid])) {
            $max_price = $arResult['MAX_PRICE'][$prodid];
            $max_price_mindiff = $max_price - 10;
        }
        ?>
        <div class="tabloid nowp">
            <div class="markerContainer">
                <? if (!empty($arResult["PROPERTIES"]["OFFERS"]["VALUE"])): ?>
                    <? foreach ($arResult["PROPERTIES"]["OFFERS"]["VALUE"] as $ifv => $marker):
                        if ((($arParams['LIST_TYPE'] == 'hit' && $marker == 'Хит продаж') ||
                                ($arParams['LIST_TYPE'] == 'new' && $marker == 'Новинка') ||
                                (!in_array($marker, ['Хит продаж', 'Новинка'])) ||
                                !isset($arParams['LIST_TYPE']))
                            && $marker != 'Распродажа'/* && $marker !='%'*/) {
                            $marker = str_replace("Хит продаж", " Хит", $marker); ?>
                            <div class="marker <?= strstr($arResult["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv], "m-") ? $arResult["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv] : "m-def" ?>"><?= $marker ?></div>
                        <? } ?>
                    <? endforeach; ?>
                <? endif; ?>
                <? if (!empty($arResult["PRICE"]["DISCOUNT_PRICE"])): ?>
                    <? if ($arResult["PRICE"]['DISCOUNT_PRICE'] < $max_price_mindiff):
                        $price_diff_percent = 100 - round($arResult["PRICE"]['DISCOUNT_PRICE'] / $max_price * 100, 0);
                    ?>
                        <div class="marker m-sale">
                            <?= '-' . $price_diff_percent . '%'; ?>
                        </div>
                    <? endif; ?>
                <? endif; ?>
            </div>
            <? if (!empty($arResult["EXTRA_SETTINGS"]["SHOW_TIMER"])): ?>
                <div class="specialTime productSpecialTime"
                     id="timer_<?= $arResult["EXTRA_SETTINGS"]["TIMER_UNIQ_ID"]; ?>_<?= $uniqID ?>">
                    <div class="specialTimeItem">
                        <div class="specialTimeItemValue timerDayValue">0</div>
                        <div class="specialTimeItemlabel"><?= GetMessage("PRODUCT_TIMER_DAY_LABEL") ?></div>
                    </div>
                    <div class="specialTimeItem">
                        <div class="specialTimeItemValue timerHourValue">0</div>
                        <div class="specialTimeItemlabel"><?= GetMessage("PRODUCT_TIMER_HOUR_LABEL") ?></div>
                    </div>
                    <div class="specialTimeItem">
                        <div class="specialTimeItemValue timerMinuteValue">0</div>
                        <div class="specialTimeItemlabel"><?= GetMessage("PRODUCT_TIMER_MINUTE_LABEL") ?></div>
                    </div>
                    <div class="specialTimeItem">
                        <div class="specialTimeItemValue timerSecondValue">0</div>
                        <div class="specialTimeItemlabel"><?= GetMessage("PRODUCT_TIMER_SECOND_LABEL") ?></div>
                    </div>
                </div>
            <? endif; ?>
            <? if (!empty($arResult["PROPERTIES"]["TIMER_LOOP"]["VALUE"])): ?>
                <script type="text/javascript">
                  $(document).ready(function () {
                    $("#timer_<?=$arResult["EXTRA_SETTINGS"]["TIMER_UNIQ_ID"];?>_<?=$uniqID?>").dwTimer({
                      timerLoop: "<?=$arResult["PROPERTIES"]["TIMER_LOOP"]["VALUE"]?>",
                        <?if(empty($arResult["PROPERTIES"]["TIMER_START_DATE"]["VALUE"])):?>
                      startDate: "<?=MakeTimeStamp($arResult["DATE_CREATE"], "DD.MM.YYYY HH:MI:SS")?>"
                        <?else:?>
                      startDate: "<?=MakeTimeStamp($arResult["PROPERTIES"]["TIMER_START_DATE"]["VALUE"], "DD.MM.YYYY HH:MI:SS")?>"
                        <?endif;?>
                    });
                  });
                </script>
            <? elseif (!empty($arResult["EXTRA_SETTINGS"]["SHOW_TIMER"]) && !empty($arResult["PROPERTIES"]["TIMER_DATE"]["VALUE"])): ?>
                <script type="text/javascript">
                  $(document).ready(function () {
                    $("#timer_<?=$arResult["EXTRA_SETTINGS"]["TIMER_UNIQ_ID"];?>_<?=$uniqID?>").dwTimer({
                      endDate: "<?=MakeTimeStamp($arResult["PROPERTIES"]["TIMER_DATE"]["VALUE"], "DD.MM.YYYY HH:MI:SS")?>"
                    });
                  });
                </script>
            <? endif; ?>
            <div class="productTable">
                <div class="productColImage">
                    <a href="<?= $arResult["DETAIL_PAGE_URL"] ?>" class="picture">
                        <? if ($arParams["SLIDER_ON"] !== "N"): ?>
                            <? if ($arParams["LAZY_LOAD_PICTURES"] == "Y"): ?>
                                <? if (!empty($arResult["IMAGES"])):?>
                                    <div id="pictureContainer">
                                        <div class="pictureSlider catalog-list <?if(count($arResult["IMAGES"]) > 1):?>more-images<?else:?>one-image<?endif;?> slider single-item">
                                            <? foreach ($arResult["IMAGES"] as $ipr => $arNextPicture): ?>
                                                <?if($ipr < 3):?>
                                                    <div class="item">
                                                        <img
                                                            src="<?= SITE_TEMPLATE_PATH ?>/images/mloader.gif" class="lazy"
                                                            data-lazy="<?= $arNextPicture["SMALL_IMAGE"]["SRC"] ?>"
                                                            alt="<? if (!empty($arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"])): ?><?= $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] ?><? else: ?><?= $arResult["NAME"] ?><? endif; ?>"
                                                            title="<? if (!empty($arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"])): ?><?= $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] ?><? else: ?><?= $arResult["NAME"] ?><? endif; ?>"
                                                        >
                                                    </div>
                                                <? endif; ?>
                                            <? endforeach; ?>
                                        </div>
                                    </div>
                                <? endif; ?>
                            <? else: ?>
                                <? if (!empty($arResult["IMAGES"])): ?>
                                    <div id="pictureContainer">
                                        <div class="pictureSlider catalog-list <?if(count($arResult["IMAGES"]) > 1):?>more-images<?else:?>one-image<?endif;?> slider single-item">
                                            <? foreach ($arResult["IMAGES"] as $ipr => $arNextPicture): ?>
                                                <?if($ipr < 3):?>
                                                    <div class="item">
                                                        <img
                                                            src="<?= $arNextPicture["SMALL_IMAGE"]["SRC"] ?>"
                                                        >
                                                    </div>
                                                <? endif; ?>
                                            <? endforeach; ?>
                                        </div>
                                    </div>
                                <? endif; ?>
                            <? endif; ?>
                        <? else: ?>
                            <? if ($arParams["LAZY_LOAD_PICTURES"] == "Y"): ?>
                                <img src="<?= SITE_TEMPLATE_PATH ?>/images/mloader.gif" class="lazy"
                                     data-lazy="<?= $arResult["PICTURE"]["src"] ?>"
                                     alt="<? if (!empty($arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"])): ?><?= $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] ?><? else: ?><?= $arResult["NAME"] ?><? endif; ?>"
                                     title="<? if (!empty($arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"])): ?><?= $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] ?><? else: ?><?= $arResult["NAME"] ?><? endif; ?>">
                            <? else: ?>
                                <img src="<?= $arResult["PICTURE"]["src"] ?>"
                                     alt="<? if (!empty($arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"])): ?><?= $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] ?><? else: ?><?= $arResult["NAME"] ?><? endif; ?>"
                                     title="<? if (!empty($arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"])): ?><?= $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] ?><? else: ?><?= $arResult["NAME"] ?><? endif; ?>">
                            <? endif; ?>
                        <? endif; ?>
                    </a>
                    <span class="item_brand <? if ($arResult['PROPERTIES']['BRAND_1C']['VALUE'] == 'medi') { ?>flag-medi<? } ?> "><? if ($arResult['PROPERTIES']['BRAND_1C']['VALUE'] != 'medi') { ?><?= $arResult['PROPERTIES']['BRAND_1C']['VALUE'] ?><? } ?>
                    </span>
                    <? if (!empty($arResult["SKU_PROPERTIES"]['COLOR'])): ?>
                        <ul class="item_colors">
                            <? foreach ($arResult["SKU_PROPERTIES"]['COLOR']["VALUES"] as $xml_id => $arNextPropValue): ?>
                                <li class="item_color <? if ($arNextPropValue["DISABLED"] == "Y"): ?> disabled<? elseif ($arNextPropValue["SELECTED"] == "Y"): ?> selected<? endif; ?>"
                                    data-name="<?= $propName ?>" data-value="<?= $arNextPropValue["VALUE"] ?>">
                                    <? if (!empty($arNextPropValue["IMAGE"])): ?>
                                        <img src="<?= $arNextPropValue["IMAGE"]["src"] ?>"
                                             alt="<?= $arNextPropValue["DISPLAY_VALUE"] ?>"
                                             title="<?= $arNextPropValue["DISPLAY_VALUE"] ?>">
                                    <? else: ?>
                                        <?= $arNextPropValue["DISPLAY_VALUE"] ?>
                                    <? endif; ?>
                                </li>
                            <? endforeach; ?>
                        </ul>
                    <? endif; ?>
                </div>
                <div class="productColText">
                    <span class="item_brand <? if ($arResult['PROPERTIES']['BRAND_1C']['VALUE'] == 'medi') { ?>flag-medi<? } ?>"><? if ($arResult['PROPERTIES']['BRAND_1C']['VALUE'] != 'medi') { ?><?= $arResult['PROPERTIES']['BRAND_1C']['VALUE'] ?><? } ?>
                    </span>
                    <a href="<?= $arResult["DETAIL_PAGE_URL"] ?>" class="name">
                        <span class="middle"><?= $arResult["NAME"] ?></span>
                    </a>
                    <? if (!empty($arResult["EXTRA_SETTINGS"])): ?>
                        <a class="price"><?= CCurrencyLang::CurrencyFormat($arResult["PRICE"]["DISCOUNT_PRICE"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true) ?>
                            <script>vViewedProdsPrice += parseInt(<?=$arResult["PRICE"]["DISCOUNT_PRICE"]?>);  </script>
                            <s class="discount"><? if ($arResult['PRICE']['PRICE']['PRICE'] < $max_price_mindiff):
                                    $price_diff = $max_price - $arResult['PRICE']['PRICE']['PRICE']; ?>
                                    <?= CCurrencyLang::CurrencyFormat($max_price, $arResult["EXTRA_SETTINGS"]["CURRENCY"], true) ?>
                                <? elseif (!empty($arResult["PRICE"]["DISCOUNT"])): ?>
                                    <?= CCurrencyLang::CurrencyFormat($arResult["PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true) ?>
                                <? endif; ?>
                            </s>
                        </a>
                        <? if ($arResult['PROPERTIES']['COUNTRY_BRAND']['VALUE'] == 'Германия') { ?>
                            <span class="flag-ge"></span><?
                        }?>
                        <div class="favorite-cart-buttons-wrapper"><!-- Блок для адаптива (<1024px)-->
                            <div class="favoriteContainer _adaptive">
                                <div class="b-card-favorite" data-product-id="<?= !empty($arResult["~ID"]) ? $arResult["~ID"] : $arResult["ID"] ?>"></div>
                            </div>
                            <div class="cartButtons _adaptive">
                                <?if(!empty($arResult["SKU_OFFERS"])):?>
                                    <!-- У товара есть размерные характеристики -->
                                    <? if (
                                        ($arResult["CATALOG_AVAILABLE"] == "Y" && $arResult['DISPLAY_BUTTONS']['CART_BUTTON']) ||
                                        (
                                            ($arResult['DISPLAY_BUTTONS']['INSOLE_BUTTON'] == false && !$arResult['DISPLAY_BUTTONS']['SMP_BUTTON']) &&
                                            (
                                                $arResult['SALON_AVAILABLE'] != "0" ||
                                                $arResult['SALON_COUNT'] != "0"
                                            )
                                        )
                                    ):?>
                                        <!-- Товар можно положить в корзину (товар доступен) или забронировать в салоне (переход в карточку товара) -->
                                        <a href="<?=$arResult["DETAIL_PAGE_URL"]?>" class="changeID add-to-reserve"
                                           data-title="<?= GetMessage("TO_BUY") ?>"
                                           data-id="<?= $arResult["ID"] ?>"
                                           data-action="reserve"><?= GetMessage("TO_BUY") ?>
                                        </a>
                                    <?elseif ($arResult['IBLOCK_SECTION_ID'] == 85):?>
                                        <!-- Только для товаров из раздела "Индивидуальные стельки -->
                                        <? if ($arResult['DISPLAY_BUTTONS']['INSOLE_BUTTON']): ?>
                                            <a href="/services/izgotovlenie-ortopedicheskikh-stelek/#order"
                                               class="magentaBigButton scan">Запись на изготовление</a>
                                        <? endif; ?>
                                    <?endif;?>
                                <?else:?>
                                    <!-- У товара нет размерных характеристик -->
                                    <? if ($arResult["CATALOG_AVAILABLE"] == "Y" && $arResult['DISPLAY_BUTTONS']['CART_BUTTON']):?>
                                        <!-- Товар можно положить в корзину (товар доступен) (переход в карточку товара) -->
                                        <a
                                            href="<?=$arResult["DETAIL_PAGE_URL"]?>"
                                            class="add-to-cart"
                                            data-id="<?= $arResult["ID"] ?>"
                                            id="GTM_add_cart_catalog_<?= ($arParams['LIST_TYPE'] ? $arParams['LIST_TYPE'] . '_' : '') ?><?= $arParams['POS_COUNT'] ?>_<?= $arResult["ID"] ?>"
                                        >
                                            <?= GetMessage("TO_BUY") ?>
                                        </a>
                                    <?elseif(
                                        ($arResult['DISPLAY_BUTTONS']['INSOLE_BUTTON'] == false && !$arResult['DISPLAY_BUTTONS']['SMP_BUTTON']) &&
                                        (
                                            $arResult['SALON_AVAILABLE'] != "0" ||
                                            $arResult['SALON_COUNT'] != "0"
                                        )
                                    ):?>
                                        <!-- Товар можно забронировать в салоне -->
                                        <a
                                            href="#"
                                            class="greyBigButton reserve add-to-reserve changeID get_medi_popup_Window"
                                            data-src="/ajax/catalog/?action=reserve" data-title="<?= GetMessage("BOOK_IN_SALON") ?>"
                                            data-id="<?= $arResult["ID"] ?>"
                                            data-action="reserve"
                                            data-salon-available=<?= $arResult['SALON_AVAILABLE'] ?>
                                            data-salon-count=<?= $arResult['SALON_COUNT'] ?>
                                            data-main-store-amount=<?= $arResult['mainStoreAmount'] ?>
                                        ><?= GetMessage("BOOK_IN_SALON") ?>
                                        </a>
                                    <?endif;?>
                                <?endif;?>
                            </div>
                        </div>
                        <? if (!empty($arResult["SKU_OFFERS"])): ?>
                            <? if (!empty($arResult["SKU_PROPERTIES"]) && $level = 1): ?>
                                <? foreach ($arResult["SKU_PROPERTIES"] as $propName => $arNextProp): ?>
                                    <? if (!empty($arNextProp["VALUES"])): ?>
                                        <? if ($arNextProp["LIST_TYPE"] == "L" && $arNextProp["HIGHLOAD"] != "Y"): ?>
                                            <? foreach ($arNextProp["VALUES"] as $xml_id => $arNextPropValue): ?>
                                                <? if ($arNextPropValue["SELECTED"] == "Y"): ?>
                                                    <? $currentSkuValue = $arNextPropValue["DISPLAY_VALUE"]; ?>
                                                <? endif; ?>
                                            <? endforeach; ?>
                                            <div class="skuProperty oSkuDropDownProperty" data-name="<?= $propName ?>"
                                                 data-level="<?= $level++ ?>"
                                                 data-highload="<?= $arNextProp["HIGHLOAD"] ?>">
                                                <div class="skuPropertyName"><?= preg_replace("/\[.*\]/", "", $arNextProp["NAME"]) ?>
                                                    :
                                                </div>
                                                <div class="oSkuDropdown">
                                                    <span class="oSkuCheckedItem noHideChecked"><?= $currentSkuValue ?></span>
                                                    <ul class="skuPropertyList oSkuDropdownList">
                                                        <? foreach ($arNextProp["VALUES"] as $xml_id => $arNextPropValue): ?>
                                                            <li class="skuPropertyValue oSkuDropdownListItem<? if ($arNextPropValue["DISABLED"] == "Y"): ?> disabled<? elseif ($arNextPropValue["SELECTED"] == "Y"): ?> selected<? endif; ?>"
                                                                data-name="<?= $propName ?>"
                                                                data-value="<?= $arNextPropValue["VALUE"] ?>">
                                                                <a href="#"
                                                                   class="skuPropertyLink oSkuPropertyItemLink"><?= $arNextPropValue["DISPLAY_VALUE"] ?></a>
                                                            </li>
                                                        <? endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        <? else: ?>
                                            <div class="skuProperty" data-name="<?= $propName ?>"
                                                 data-level="<?= $level++ ?>"
                                                 data-highload="<?= $arNextProp["HIGHLOAD"] ?>">
                                                <div class="skuPropertyName"><?= preg_replace("/\[.*\]/", "", $arNextProp["NAME"]) ?></div>
                                                <ul class="skuPropertyList">
                                                    <? foreach ($arNextProp["VALUES"] as $xml_id => $arNextPropValue): ?>
                                                        <li class="skuPropertyValue<? if ($arNextPropValue["DISABLED"] == "Y"): ?> disabled<? elseif ($arNextPropValue["SELECTED"] == "Y"): ?> selected<? endif; ?>"
                                                            data-name="<?= $propName ?>"
                                                            data-value="<?= $arNextPropValue["VALUE"] ?>">
                                                            <a href="#" class="skuPropertyLink">
                                                                <? if (!empty($arNextPropValue["IMAGE"])): ?>
                                                                    <img src="<?= $arNextPropValue["IMAGE"]["src"] ?>"
                                                                         alt="<?= $arNextPropValue["DISPLAY_VALUE"] ?>"
                                                                         title="<?= $arNextPropValue["DISPLAY_VALUE"] ?>">
                                                                <? else: ?>
                                                                    <?= $arNextPropValue["DISPLAY_VALUE"] ?>
                                                                <? endif; ?>
                                                            </a>
                                                        </li>
                                                    <? endforeach; ?>
                                                </ul>
                                            </div>
                                        <? endif; ?>
                                    <? endif; ?>
                                <? endforeach; ?>
                            <? endif; ?>
                        <? endif; ?>

                        <div class="clear"></div>
                        <? if ($arResult["CATALOG_AVAILABLE"] != "Y"): ?>
                            <? if ($arResult['DISPLAY_BUTTONS']['CART_BUTTON']): ?>
                                <a href="#" class="fastBack _desktop fastBut label changeID " data-id="<?= $arResult["ID"] ?>"
                                   <? if ($arResult['SALON_AVAILABLE'] != "0" || $arResult['SALON_COUNT'] != "0" || $arResult["CATALOG_AVAILABLE"] == "Y"){ ?>style="display:none;"
                                   <? } ?>style="display:none;">
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/images/incart.png"
                                         alt="<?= GetMessage("ORDER_NOTAVAIL_LABEL_ALT") ?>"
                                         class="icon"><?= GetMessage("ORDER_NOTAVAIL_LABEL") ?>
                                </a>
                            <? elseif ($arResult['DISPLAY_BUTTONS']['SMP_BUTTON']): ?>
                                <a href="#" class="smpOrder _desktop label changeID " data-id="<?= $arResult["ID"] ?>"
                                   <? if ($arResult["CATALOG_AVAILABLE"] == "N"){ ?>style="display:none;"<? } ?>><img
                                        src="<?= SITE_TEMPLATE_PATH ?>/images/incart.png"
                                        alt="<?= GetMessage("SMP_LABEL_ALT") ?>"
                                        class="icon"><?= GetMessage("SMP_LABEL_ALT") ?></a>
                            <? else: ?>
                                <? // Сканирование стоп, основная кнопка?>
                                <? if ($arResult['DISPLAY_BUTTONS']['INSOLE_BUTTON']): ?>
                                    <a href="/services/izgotovlenie-ortopedicheskikh-stelek/#order"
                                       class="magentaBigButton scan _desktop">Запись на изготовление</a>
                                <? endif; ?>
                            <? endif; ?>
                        <? else: ?>
                            <? // показ кнопки В корзину?>
                            <? if ($arResult['DISPLAY_BUTTONS']['CART_BUTTON']):?>
                                <a href="#" class="addCart _desktop" data-id="<?= $arResult["ID"] ?>"
                                   id="GTM_add_cart_catalog_<?= ($arParams['LIST_TYPE'] ? $arParams['LIST_TYPE'] . '_' : '') ?><?= $arParams['POS_COUNT'] ?>_<?= $arResult["ID"] ?>"><img
                                        src="<?= SITE_TEMPLATE_PATH ?>/images/incart.png"
                                        alt="<?= GetMessage("ADDCART_LABEL") ?>"
                                        class="icon"><?= GetMessage("ADDCART_LABEL") ?></a>
                            <? elseif ($arResult['DISPLAY_BUTTONS']['SMP_BUTTON']): ?>
                                <a href="#" class="smpOrder getSmpFastOrder changeID " data-id="<?= $arResult["ID"] ?>"
                                   <? if ($arResult["CATALOG_AVAILABLE"] == "N"){ ?>style="display:none;"<? } ?>>
                                    <img
                                        src="<?= SITE_TEMPLATE_PATH ?>/images/incart.png"
                                        alt="<?= GetMessage("SMP_LABEL_ALT") ?>"
                                        class="icon"><?= GetMessage("SMP_LABEL_ALT") ?>
                                </a>
                            <? else: ?>
                                <? // Сканирование стоп, основная кнопка?>
                                <? if ($arResult['DISPLAY_BUTTONS']['INSOLE_BUTTON']): ?>
                                    <a href="/services/izgotovlenie-ortopedicheskikh-stelek/#order"
                                       class="magentaBigButton scan _desktop">Запись на изготовление</a>
                                <? endif; ?>
                            <? endif; ?>
                        <? endif; ?>
                    <? endif; ?>
                    <? if ($arResult['DISPLAY_BUTTONS']['INSOLE_BUTTON'] == false && !$arResult['DISPLAY_BUTTONS']['SMP_BUTTON']):?>
                        <a href="#" class="greyBigButton reserve _desktop changeID get_medi_popup_Window"
                           data-src="/ajax/catalog/?action=reserve" data-title="Забронировать в салоне"
                           data-id="<?= $arResult["ID"] ?>"
                           <? if ($arResult['SALON_AVAILABLE'] == "0" || $arResult['SALON_COUNT'] == "0" || $arResult['mainStoreAmount'] > '0' || ($arResult["CATALOG_AVAILABLE"] != "N" && $arResult['DISPLAY_BUTTONS']['CART_BUTTON'] == true)){
                           ?>style="display:none;"<? } ?> data-action="reserve">Забронировать
                        </a>
                    <? endif; ?>
                </div>
                <div class="favoriteContainer _desktop">
                    <div class="b-card-favorite" data-product-id="<?= !empty($arResult["~ID"]) ? $arResult["~ID"] : $arResult["ID"] ?>">
                        <span>В избранное</span>
                    </div>
                </div>
            </div>
        </div>
        <script>
          /* Вешаем обработчик для добавления товара в избранное (Товары из компонента dresscode:catalog.item) */
          $('#<?= $this->GetEditAreaId($arResult["ID"]); ?>').on('click', '.b-card-favorite', function () {
            if ($('input[name=user_auth]').length > 0) {
              let productId = $(this).attr('data-product-id');
              let doAction = '';

              if ($(this).hasClass('active')) {
                doAction = 'delete';
              } else {
                doAction = 'add';
              }

              addFavorite(productId, doAction);
            } else {
              window.location.href = "/lk/?favorite";
            }
          });

          function addFavorite(productId, action) {

            let param = 'id='+productId+"&action="+action;

            $.ajax({
              url: '/ajax/favorite/',
              type: 'GET',
              dataType: 'html',
              data: param,
              success: function (response) {
                let result = $.parseJSON(response);
                let wishCount = 1;

                if (result == 1) {
                  $('.b-card-favorite[data-product-id="'+productId+'"]').addClass('active');
                  $('.b-card-favorite[data-product-id="'+productId+'"]').find('span').text('В избранном');
                  let currentCount = parseInt($('.favorites_link .count').html());

                  if (currentCount >= 0) {
                    wishCount = currentCount + 1;
                  }

                  $('.favorites_link .count').html(wishCount);
                  if (!$('.favorites_link').hasClass('has_items')) {
                    $('.favorites_link').addClass('has_items');
                  }

                  if ($('input[name=favorite_items]').length > 0) {
                    let inputFavoriteItemsValue = $('input[name=favorite_items]').val();

                    if (inputFavoriteItemsValue != '') {
                      let favoriteItems = JSON.parse(inputFavoriteItemsValue);
                      favoriteItems.push(parseInt(productId));
                      let jsonFavoriteItems = JSON.stringify(favoriteItems);
                      $('input[name=favorite_items]').val(jsonFavoriteItems);
                    }
                  }
                }

                if (result == 2) {
                  $('.b-card-favorite[data-product-id="'+productId+'"]').removeClass('active');
                  $('.b-card-favorite[data-product-id="'+productId+'"]').find('span').text('В избранное');
                  wishCount = parseInt($('.favorites_link .count').html()) - 1;

                  if (wishCount == 0) {
                    $('.favorites_link .count').html('');
                    $('.favorites_link').removeClass('has_items');
                  } else {
                    $('.favorites_link .count').html(wishCount);
                  }

                  if ($('input[name=favorite_items]').length > 0) {
                    let inputFavoriteItemsValue = $('input[name=favorite_items]').val();

                    if (inputFavoriteItemsValue != '') {
                      let favoriteItems = JSON.parse(inputFavoriteItemsValue);
                      let indexElem = favoriteItems.indexOf(parseInt(productId));

                      if (indexElem >= 0) {
                        favoriteItems.splice(indexElem, 1);
                        let jsonFavoriteItems = JSON.stringify(favoriteItems);
                        $('input[name=favorite_items]').val(jsonFavoriteItems);
                      }
                    }
                  }
                }
              },
              error: function (jqXHR, textStatus, errorThrown) {
                console.log('Error: '+ errorThrown);
              }
            });
          }
        </script>
    </div>
<? endif; ?>