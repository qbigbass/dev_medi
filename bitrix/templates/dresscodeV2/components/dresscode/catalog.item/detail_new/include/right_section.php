<? use Bitrix\Main\Grid\Declension; ?>
<div class="mainTool">
    <? if (!empty($arResult["PRICE"])): ?>
        <? if ($arResult["EXTRA_SETTINGS"]["COUNT_PRICES"] > 1): ?>
            <a class="price changePrice getPricesWindow" data-id="<?= $arResult["ID"] ?>">
                <? if (!empty($arResult["PRICE"]["DISCOUNT"])): ?>
                    <span class="priceBlock">

						<? if (!empty($arResult["PRICE"]["RESULT_PRICE"]["DISCOUNT"])): ?>
                            <span class="oldPriceLabel"><?= GetMessage("OLD_PRICE_DIFFERENCE_LABEL") ?> <span
                                        class="economy"><?= CCurrencyLang::CurrencyFormat($arResult["PRICE"]["RESULT_PRICE"]["DISCOUNT"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true) ?></span></span>
                        <? endif; ?>
					</span>
                <? endif; ?>
                <span class="priceContainer"><span class="priceIcon"></span><span class="priceVal"
                                                                                  data-price="<?= $arResult["PRICE"]["DISCOUNT_PRICE"] ?>"><?= CCurrencyLang::CurrencyFormat($arResult["PRICE"]["DISCOUNT_PRICE"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true) ?></span>
				<? if (!empty($arResult["PRICE"]["DISCOUNT"])): ?>
                    <span class="oldPriceLabel"><s
                                class="discount"><?= CCurrencyLang::CurrencyFormat($arResult["PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true) ?></s></span>
                <? endif; ?>
				</span>
                <? if ($arParams["HIDE_MEASURES"] != "Y" && !empty($arResult["EXTRA_SETTINGS"]["MEASURES"][$arResult["CATALOG_MEASURE"]]["SYMBOL_RUS"])): ?>
                    <span class="measure"> / <?= $arResult["EXTRA_SETTINGS"]["MEASURES"][$arResult["CATALOG_MEASURE"]]["SYMBOL_RUS"] ?></span>
                <? endif; ?>
                <? if (!empty($arResult["PROPERTIES"]["BONUS"]["VALUE"])): ?>
                    <span class="purchaseBonus"><span
                                class="theme-color">+ <?= $arResult["PROPERTIES"]["BONUS"]["VALUE"] ?></span><?= $arResult["PROPERTIES"]["BONUS"]["NAME"] ?></span>
                <? endif; ?>
            </a>
        <? else: ?>
            <a class="price changePrice">
                <?
                $prodid = $arResult['PRICE']['PRODUCT_ID'];
                if (!empty($arResult['MAX_PRICE'][$prodid])) {
                    $max_price = $arResult['MAX_PRICE'][$prodid];
                    $max_price_mindiff = $max_price - 100;
                }
                ?>
                <span class="priceBlock">
				<? if (!empty($arResult["PRICE"]["DISCOUNT"])): ?>
                    <span class="priceContainer">
                        <span class="priceVal"
                              data-price="<?= $arResult["PRICE"]["DISCOUNT_PRICE"] ?>"><?= CCurrencyLang::CurrencyFormat($arResult["PRICE"]["DISCOUNT_PRICE"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true) ?></span>
                        <? if ($arParams["HIDE_MEASURES"] != "Y" && !empty($arResult["EXTRA_SETTINGS"]["MEASURES"][$arResult["CATALOG_MEASURE"]]["SYMBOL_RUS"])): ?>
                            <span class="measure"> / <?= $arResult["EXTRA_SETTINGS"]["MEASURES"][$arResult["CATALOG_MEASURE"]]["SYMBOL_RUS"] ?></span>
                        <? endif; ?>
                    </span>
                    <span class="oldPriceLabel"><s
                                class="discount"><?= CCurrencyLang::CurrencyFormat($arResult["PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true) ?></s></span>
						<? if (!empty($arResult["PRICE"]["RESULT_PRICE"]["DISCOUNT"])): ?>
                        <span class="oldPriceLabel"><?= GetMessage("OLD_PRICE_DIFFERENCE_LABEL") ?> <span
                                    class="economy"><?= CCurrencyLang::CurrencyFormat($arResult["PRICE"]["RESULT_PRICE"]["DISCOUNT"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true) ?></span></span>
                    <? endif; ?>
                
                <? elseif ($arResult['PRICE']['PRICE']['PRICE'] < $max_price_mindiff):
                    $price_diff = $max_price - $arResult['PRICE']['PRICE']['PRICE']; ?>
                    <span class="priceContainer">
					<span class="priceVal"
                          data-price="<?= $arResult["PRICE"]["DISCOUNT_PRICE"] ?>"><?= CCurrencyLang::CurrencyFormat($arResult["PRICE"]["DISCOUNT_PRICE"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true) ?></span>
					<? if ($arParams["HIDE_MEASURES"] != "Y" && !empty($arResult["EXTRA_SETTINGS"]["MEASURES"][$arResult["CATALOG_MEASURE"]]["SYMBOL_RUS"])): ?>
                        <span class="measure"> / <?= $arResult["EXTRA_SETTINGS"]["MEASURES"][$arResult["CATALOG_MEASURE"]]["SYMBOL_RUS"] ?></span>
                    <? endif; ?>
				</span>
                    <span class="oldPriceLabel"><s
                                class="discount"><?= CCurrencyLang::CurrencyFormat($max_price, $arResult["EXTRA_SETTINGS"]["CURRENCY"], true) ?></s></span>
						<? if (!empty($price_diff)): ?>
                    <span class="oldPriceLabel"><?= GetMessage("OLD_PRICE_DIFFERENCE_LABEL") ?> <span
                                class="economy"><?= CCurrencyLang::CurrencyFormat($price_diff, $arResult["EXTRA_SETTINGS"]["CURRENCY"], true) ?></span></span>
                <? endif; ?>
                <? else: ?>
                    <span class="priceContainer">
					<span class="priceVal"
                          data-price="<?= $arResult["PRICE"]["DISCOUNT_PRICE"] ?>"><?= CCurrencyLang::CurrencyFormat($arResult["PRICE"]["DISCOUNT_PRICE"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true) ?></span>
					<? if ($arParams["HIDE_MEASURES"] != "Y" && !empty($arResult["EXTRA_SETTINGS"]["MEASURES"][$arResult["CATALOG_MEASURE"]]["SYMBOL_RUS"])): ?>
                        <span class="measure"> / <?= $arResult["EXTRA_SETTINGS"]["MEASURES"][$arResult["CATALOG_MEASURE"]]["SYMBOL_RUS"] ?></span>
                    <? endif; ?>
				</span>
                <? endif; ?>
                    
                    <? if (!empty($arResult["PROPERTIES"]["BONUS"]["VALUE"])): ?>
                        <span class="purchaseBonus"><span
                                    class="theme-color">+ <?= $arResult["PROPERTIES"]["BONUS"]["VALUE"] ?></span><?= $arResult["PROPERTIES"]["BONUS"]["NAME"] ?></span>
                    <? endif; ?>
            </a>
        <? endif; ?>
    <? else: ?>
        <a class="price changePrice"><?= GetMessage("REQUEST_PRICE_LABEL") ?></a>
    <? endif; ?>
    <? if ($arResult['PROPERTIES']['GPO']['VALUE'] == 'Да') { ?>
        <div class="columnRowWrap delivery_methods ff-medium">
            <div style="display:flex;align-items: baseline;">
                <img src="/bitrix/templates/dresscodeV2/components/dresscode/catalog.item/detail_new/images/go.svg"
                     style="margin-right:10px;">
                <div><a href="/services/individualnyy-podbor-ortopedicheskikh-izdeliy/" class="link"
                        style="color:#e20074;border-bottom: 1px dashed;text-decoration: none;" target="_blank">Выезд
                        специалиста по ортезированию на дом</a></div>
            </div>
        </div>
    <? } ?>
    
    <? // Только для региональных сайтов
    if (in_array(SITE_ID, ['s4', 's5', 's6', 's8'])):?>
        <div class="columnRowWrap delivery_methods ff-medium">
            <p>Обращаем Ваше внимание, что ассортимент и стоимость изделий могут отличаться от интернет-магазина.
                Уточняйте в <a href="/<?= $GLOBALS['medi']['sfolder'][SITE_ID] ?>/salons/"
                               <? if ($arResult['SALON_COUNT'] > 0){
                               ?>onclick="document.getElementById('chacor5').checked='true';"<? } ?>
                               class="available_link link medi-color">салоне</a> стоимость и наличие.</p>
        </div>
    <? endif; ?>
    
    <? if (!empty($arResult['ACTION_BLOCK'])): ?>
        
        <? // Вывод акций через метки "Признак акции" в карточке
        
        if (is_array($arResult['ACTION_BLOCK'])) {
            ?>
            <div class="columnRow">
            <div class="bindAction">
                <div class="ff-medium row h3">Товар участвует в
                    акци<?= (count($arResult['ACTION_BLOCK']) > 1 ? 'ях' : 'и') ?>:
                </div>
                <?
                $timerOn = false;
                ?>
                <? foreach ($arResult['ACTION_BLOCK'] as $sact) {?>
                    <?
                    $hide_link = $sact['PROPERTY_HIDE_VALUE'] == 'Да' ? 'Y' : 'N';
//                    $dayDiff = '';
//
//                    if ($sact['DATE_ACTIVE_TO'] > 0) {
//                        $date = DateTime::createFromFormat('d.m.Y H:i:s', $sact['DATE_ACTIVE_TO']);
//                        $now = new DateTime();
//                        if ($date) {
//                            $dayDiff = $date->diff($now)->format('%a');
//                            if ($dayDiff > 0) {
//                                $sDeclension = new Declension('день', 'дня', 'дней');
//                                $dayDiff_str = '<br/><span class="action_over">Заканчивается через ' . $dayDiff . '&nbsp;' . $sDeclension->get($dayDiff) . '</span>';
//                            }
//                        }
//                    }
                    $date = DateTime::createFromFormat('d.m.Y H:i:s', $sact['ACTIVE_TO']);
                    $now = new DateTime();
                    $daysLeft = $date->diff($now)->format('%a'); // Кол-во дней до окончания акции

                    $showAction = false;
                    if ( (int)$sact['PROPERTY_CNT_DAYS_TIMER_VALUE'] > 0 && $daysLeft <= (int)$sact['PROPERTY_CNT_DAYS_TIMER_VALUE'] ) {
                        $showAction = true;
                    } else {
                        if ($daysLeft <= DAYS_END_ACTION) {
                            $showAction = true;
                        }
                    }

                    if ($sact['PROPERTY_TIMER_ON_VALUE'] === 'Да' && $sact['ACTIVE_TO'] > 0 && !$timerOn && $showAction) {
                        $timerUniqId = $this->randString();
                        $endDate = MakeTimeStamp($sact['ACTIVE_TO'], "DD.MM.YYYY HH:MI:SS");
                        $timerOn = true;
                    } else {
                        $timerOn = false;
                    }
                    ?>
                    <div class="tb row <?if($timerOn):?>action-wrapper<?endif;?>">
                        <div class="tb">
                            <div class="tc bindActionImage"><? if ($hide_link == 'N'){
                                ?><a href="<?= $sact['DETAIL_PAGE_URL'] ?>" target="_blank"><?
                                    } ?><span class="image"></span><? if ($hide_link == 'N'){
                                    ?></a><?
                            } ?></div>
                            <div class="tc bindActionTitle"><? if ($hide_link == 'N'){
                                ?><a href="<?= $sact['DETAIL_PAGE_URL'] ?>" target="_blank"><?
                                    } ?><?= $sact['NAME'] ?><? if ($hide_link == 'N'){
                                    ?></a><?
                            } ?><?
                                //                            if ($dayDiff > 0) {
                                //                                echo $dayDiff_str;
                                //                            }
                                ?></div>
                        </div>

                        <!-- Таймер окончания акции -->
                        <? if ($timerOn):?>
                            <div class="tb timer">
                                <div class="timer__title">До конца акции осталось:</div>
                                <div class="specialTime smallSpecialTime"
                                     id="timer_<?= $timerUniqId; ?>_<?= $uniqID ?>">
                                    <div class="specialTimeItem">
                                        <div class="specialTimeItemValue timerDayValue">0</div>
                                        <div class="specialTimeItemlabel"><?= GetMessage("TIMER_DAY_LABEL") ?></div>
                                    </div>
                                    <div class="specialTimeItem">
                                        <div class="specialTimeItemValue timerHourValue">0</div>
                                        <div class="specialTimeItemlabel"><?= GetMessage("TIMER_HOUR_LABEL") ?></div>
                                    </div>
                                    <div class="specialTimeItem">
                                        <div class="specialTimeItemValue timerMinuteValue">0</div>
                                        <div class="specialTimeItemlabel"><?= GetMessage("TIMER_MINUTE_LABEL") ?></div>
                                    </div>
                                    <div class="specialTimeItem">
                                        <div class="specialTimeItemValue timerSecondValue">0</div>
                                        <div class="specialTimeItemlabel"><?= GetMessage("TIMER_SECOND_LABEL") ?></div>
                                    </div>
                                </div>
                            </div>
                            <script type="text/javascript">
                                $(document).ready(function () {
                                    $("#timer_<?=$timerUniqId;?>_<?=$uniqID?>").dwTimer({
                                        endDate: "<?=$endDate?>"
                                    });
                                });
                            </script>
                        <?endif;?>
                    </div>
                    <?
                } ?>
            </div>
            <?
            ?></div><?
        } // Вывод акции через привязку товаров к акции
        else {
            ?>
            <div class="columnRow"><? echo trim($arResult['ACTION_BLOCK']); ?></div><?
        }
        ?>
    <? endif; ?>

    <?
    // Проверим наличие SKU на складах и в салонах
    $offerId = $arResult['ID'];
    $storeAmounts = $arResult['SKU_OFFERS'][$offerId]['storeamounts'];
    $arrStoresOnline = [];

    if (!empty($storeAmounts) && !$checkAvailable) {
        if (SITE_ID == 's1') {
            $arrStoresOnline = ['6','46','48','51','52','65','83']; // Склады для ИМ в Москве
            $strCity = 'Москве';
        } elseif (SITE_ID == 's2') {
            $arrStoresOnline = ['66','67']; // Склады для ИМ в С-Петербурге
            $strCity = 'Санкт-Петербургу';
        }

        if (!empty($arrStoresOnline)) {
            $avaiProductSalon = [];
            $avaiProductStore = [];

            foreach ($storeAmounts as $arrAmounts) {
                if ($arrAmounts['PRODUCT_AMOUNT'] > 0) {
                    if (in_array($arrAmounts['ID'], $arrStoresOnline)) {
                        $avaiProductStore[] = $arrAmounts['ID'];
                    } else {
                        $avaiProductSalon[] = $arrAmounts['ID'];
                    }
                }
            }

            if (!empty($avaiProductSalon) && empty($avaiProductStore)) {
                $deliveryMessage = '<p>доступен только для самовывоза</p>';
            } else {
                // Проверяем стоимость товара
                if ($arResult["PRICE"]["DISCOUNT_PRICE"] < 1000) {
                    $deliveryMessage = '<p>250 рублей доставка по ' . $strCity . ' и области - <span class="delivery_term">завтра</span></p>';
                } else {
                    $deliveryMessage = '<p>Бесплатная доставка по ' . $strCity . ' и области &ndash; <span class="delivery_term">завтра</span></p>';
                }
            }
        }
        $checkAvailable = true; // Так как данный файл подключается 2 раза на странице
    }?>

    <? if (in_array(SITE_ID, ['s1', 's2', 's7'])) { ?>
        <div class="columnRowWrap delivery_methods ">
            <div class="row">
                <div class="row delivery">
                    <? if (SITE_ID == 's1' || SITE_ID == 's2'):?>
                        <?=$deliveryMessage?>
                    <? elseif (SITE_ID == 's7'): ?>
                        <? if ($arResult["PRICE"]["DISCOUNT_PRICE"] < 1000): ?>
                            <p>250 рублей доставка по
                                <nobr>Нижнему Новгороду</nobr>
                                и области - <span class="delivery_term_nn">2&ndash;3&nbsp;дня</span></p>
                        <? else: ?>
                            <p>Бесплатная доставка по
                                <nobr>Нижнему Новгороду</nobr>
                                и области - <span class="delivery_term_nn">2&ndash;3&nbsp;дня</span></p>
                        <? endif; ?>
                    <? endif; ?>
                </div>
            </div>
        </div>
    <? } ?>

    <div class="columnRowWrap">
        <div class="row columnRow">
            <? if (!empty($arResult["PRICE"])): ?>
                <? // Нет в наличии ?>
                <? if ($arResult["CATALOG_AVAILABLE"] != "Y" && $arResult['DISPLAY_BUTTONS']['INSOLE_BUTTON'] !== true): ?>
                    <? //if($arResult["CATALOG_SUBSCRIBE"] == "Y"  && !$arResult['DISPLAY_BUTTONS']['CART_BUTTON']):?>
                    <? /*<a href="#" class="addCart subscribe changeID changeQty changeCart" data-id="<?=$arResult["ID"]?>" data-quantity="<?=$arResult["EXTRA_SETTINGS"]["BASKET_STEP"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/subscribe.png" alt="<?=GetMessage("SUBSCRIBE_LABEL")?>" class="icon"><?=GetMessage("SUBSCRIBE_LABEL")?></a>*/
                    ?>
                    <a href="#" class="fastBack label changeID changeCart" data-id="<?= $arResult["ID"] ?>"
                       <? if ($arResult['SALON_COUNT'] > '0' || $arResult['DISPLAY_BUTTONS']['CART_BUTTON'] !== true): ?>style="display:none;"<? endif; ?>><img
                                src="<?= SITE_TEMPLATE_PATH ?>/images/incart.png"
                                alt="<?= GetMessage("ORDER_NOTAVAIL_LABEL_ALT") ?>"
                                class="icon"><?= GetMessage("ORDER_NOTAVAIL_LABEL") ?></a>
                    
                    <? /*else:?>
						<a href="#" class="addCart changeID changeQty changeCart disabled" data-id="<?=$arResult["ID"]?>" data-quantity="<?=$arResult["EXTRA_SETTINGS"]["BASKET_STEP"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/incart.png" alt="<?=GetMessage("ADDCART_LABEL")?>" class="icon"><?=GetMessage("ADDCART_LABEL")?></a>
					<?endif;*/ ?>
                    
                    <? // В наличии?>
                <? else: ?>
                    <? // показ кнопки В корзину?>
                    <? if ($arResult['DISPLAY_BUTTONS']['CART_BUTTON'] == true): ?>
                        <a href="#" class="addCart changeID changeQty changeCart" data-id="<?= $arResult["ID"] ?>"
                           data-quantity="<?= $arResult["EXTRA_SETTINGS"]["BASKET_STEP"] ?>"><img
                                    src="<?= SITE_TEMPLATE_PATH ?>/images/basket.svg"
                                    alt="<?= GetMessage("ADDCART_LABEL") ?>"
                                    class="icon"><?= GetMessage("ADDCART_LABEL") ?></a>
                    
                    <? //  заказная позиция и в наличии нет.
                    else:?>
                        <? if ($arResult['DISPLAY_BTTONS']['INSOLE_BUTTON'] !== true) {
                            ?>
                            <a href="#" class="fastBack label changeID changeCart" data-id="<?= $arResult["ID"] ?>"
                               <? if ($arResult['SALON_COUNT'] == '0' || $arResult['DISPLAY_BUTTONS']['CART_BUTTON'] !== true){
                               ?>style="display:none;"<? } ?>><img src="<?= SITE_TEMPLATE_PATH ?>/images/incart.png"
                                                                   alt="<?= GetMessage("ORDER_NOTAVAIL_LABEL_ALT") ?>"
                                                                   class="icon"><?= GetMessage("ORDER_NOTAVAIL_LABEL") ?>
                            </a>
                        <? } ?>
                    <? endif; ?>
                
                <? endif; ?>
                <? // запрос цены?>
            <?
            endif; ?>
        </div>
        <? //NOTE купить в 1 клик?>
        <? if ($arResult['DISPLAY_BUTTONS']['CART_BUTTON']): ?>
            <div class="row columnRow fastOrderWrap">
                <a href="#"
                   class="fastOrder label changeID<? if (empty($arResult["PRICE"]) || $arResult["CATALOG_AVAILABLE"] != "Y"): ?> disabled<? endif; ?>"
                   data-id="<?= $arResult["ID"] ?>" id="GTM_fastorder_card_get"><?= GetMessage("FASTORDER_LABEL") ?></a>
            </div>
        <? endif; ?>
        <? if ($arResult['DISPLAY_BUTTONS']['SMP_BUTTON']): ?>
            <div class="row columnRow smpOrderWrap">
                <a href="#"
                   class="smpOrder getSmpFastOrder changeID<? if (empty($arResult["PRICE"]) || $arResult["CATALOG_AVAILABLE"] != "Y"): ?> disabled<? endif; ?>"
                   data-id="<?= $arResult["ID"] ?>" id="GTM_smporder_card_get"><?= GetMessage("SMPORDER_LABEL") ?></a>
            </div>
        <? endif; ?>
    </div>

    <div class="columnRowWrap availability_left">
        <div class="columnRow ">
            <div class="availability_block">
                <? if ($arResult['DISPLAY_BUTTONS']['RESERV_BUTTON'] && $arResult['SALON_COUNT'] > 0 && $arResult['DONT_SHOW_REST'] == false):
                    
                    $sDeclension = new Declension('салоне', 'салонах', 'салонах');
                    $avail_salons = $sDeclension->get($arResult['SALON_COUNT']); ?>

                    <a href="#" class="available_link link"
                       onclick="document.getElementById('chacor5').checked='true';">В наличии
                        в <?= $arResult['SALON_COUNT'] . '&nbsp;' . $avail_salons ?></a>
                
                <? endif; ?>
            </div>
            <div>
                <? if ($arResult['DISPLAY_BUTTONS']['RESERV_BUTTON']): ?>
                    <a href="#" class="greyBigButton reserve changeID get_medi_popup_Window"
                       data-src="/ajax/catalog/?action=reserve" data-title="Забронировать в салоне"
                       data-id="<?= $arResult["ID"] ?>"
                       <? if ($arResult['SALON_AVAILABLE'] == "0" || $arResult['SALON_COUNT'] == "0" || $arResult["CATALOG_AVAILABLE"] != "N"){ ?>style="display:none;"<? } ?>
                       data-action="reserve">Забронировать</a>
                <? endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="secondTool">
    <? // Сканирование стоп, основная кнопка?>
    <? if ($arResult['DISPLAY_BUTTONS']['INSOLE_BUTTON']): ?>
        <div class="row columnRow">
            <a href="/services/izgotovlenie-ortopedicheskikh-stelek/#order" class="magentaBigButton scan "
               target="_blank">Запись на изготовление</a>
        </div>
    <? endif; ?>
</div>