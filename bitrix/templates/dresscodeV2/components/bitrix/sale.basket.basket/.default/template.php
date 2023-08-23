<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?><? use Bitrix\Main\Localization\Loc; ?>
<? $this->setFrameMode(false); ?>

<?
//check created order
if (!empty($arResult["CONFIRM_ORDER"]) && $arResult["CONFIRM_ORDER"] == "Y") {
    //confirm page
    include_once($_SERVER["DOCUMENT_ROOT"] . "/" . $templateFolder . "/confirm_template.php");
    return false;
}
?>


<? if (!empty($arResult["ITEMS"])): ?>
    <?
    //vars
    $personTypeIndex = 0;
    $component = $this->getComponent();
    $countPos = 0;
    ?>

    <div id="personalCart" class="DwBasket">
        
        <? if ($USER->IsAuthorized() && array_intersect([20], $USER->GetUserGroupArray())) {
            ?>
            <span class="h3 ff-medium">Данные клиента:</span>
            <div class="salon_client_form flex">

                <div>
                    <form id="client_form"><input type="text" id="client_input"
                                                  value="<?= ($_SESSION['lmx']['phone'] ? $_SESSION['lmx']['phone'] : $_SESSION['lmx']['card']); ?>"
                                                  placeholder="Телефон или ДК клиента"
                                                  <? if (isset($_SESSION['lmx']['token'])){ ?>disabled<? } ?>/> <? if (isset($_SESSION['lmx']['token'])) { ?>
                            <input type="submit" value="Сменить" id="clientDeActivate"><? } else { ?><input
                                type="submit" value="ОК" id="clientActivate"><? } ?></form>
                </div>
                <div id="client_auth_status"></div>
                <? if (isset($_SESSION['lmx']['token'])) {/*echo "<pre>";print_r($_SESSION['lmx']);echo "<pre>";*/ ?>
                    <div id="client_full_info"><?= $_SESSION['lmx']['lastName'] ?> <?= $_SESSION['lmx']['firstName'] ?> <?= $_SESSION['lmx']['patronymicName'] ?>
                        <br><?= $_SESSION['lmx']['client_card'] ?><? //print_r($_SESSION)?></div>
                <? } ?>
            </div>
        
        
        <? } ?>
        <div id="basketTopLine">
            <div id="tabsControl">
                
                <? /*<div class="item"><a href="<?=SITE_DIR?>personal/cart/order/" id="scrollToOrder" class="orderMove selected"><?=Loc::getMessage("BASKET_TABS_ORDER_MAKE")?></a></div>*/ ?>
                <div class="item"><a href="#" id="allClear"
                                     class="clearAllBasketItems active-link"><?= Loc::getMessage("BASKET_TABS_CLEAR") ?></a>
                </div>
            </div>
        </div>
        <div id="basketProductList" class="productsListName" data-list-name="Корзина">
            <? include($_SERVER["DOCUMENT_ROOT"] . "/" . $templateFolder . "/include/basket_squares.php"); ?>
        </div>

        <div class="couponLine">
            <div class="flex">
                <form id="coupon">
                    <? if (!empty($_SESSION['lmxapp']['coupon'])) { ?>
                        <input placeholder="<?= Loc::getMessage("COUPON_LABEL") ?>"
                               value="<?= $_SESSION['lmxapp']['coupon'] ?>" disabled name="user" class="couponField">
                        <input type="submit" value="<?= Loc::getMessage("COUPON_DEACTIVATE") ?>"
                               class="couponDeActivate">
                    <? } else {
                        ?>
                        <input placeholder="<?= Loc::getMessage("COUPON_LABEL") ?>"
                               value="<?= $_SESSION['lmxapp']['coupon'] ?>" name="user" class="couponField"><input
                                type="submit" value="<?= Loc::getMessage("COUPON_ACTIVATE") ?>" class="couponActivate">
                        <?
                    } ?>
                </form>
            </div>
        </div>

        <div class="orderLine">
            <div class="flex">
                <div id="sum">
                    <span class="label "><?= Loc::getMessage("TOTAL_QTY") ?></span>
                    <span class="price  countItems"><?= $countPos ?></span>
                    <span class="label">Стоимость:</span>
                    <span class="price">
						<span class="basketAllSum" style="color:#666;">
                            <? if ($arResult["DISCOUNT_PRICE_ALL"] > 0 && $arResult["BASKET_SUM"] != $arResult["allSum"]) {
                                // $arResult["BASKET_SUM"] =  $arResult["BASKET_SUM"] ;
                            }
                            echo FormatCurrency($arResult["BASKET_SUM"], $arResult["CURRENCY"]["CODE"]); ?></span>
					</span>
                    <span class="label">Скидка:</span>
                    <span class="price">
						<span class="discountSum" style="color:#e20074;">
                            <?= ($arResult["BASKET_SUM"] - $arResult["allSum"] != 0 ? "-" . number_format($arResult["BASKET_SUM"] - $arResult["allSum"], 0, '.', ' ') . '&nbsp;руб.' : $arResult["DISCOUNT_PRICE_FORMATED"]); ?>
                        </span>
					</span>
                    <span class="label ff-medium">Итого:</span>
                    <span class="price">
						<span class="basketSum"><?= FormatCurrency($arResult["allSum"], $arResult["CURRENCY"]["CODE"]); ?></span>
					</span>
                </div>
                <a href="<?= SITE_DIR ?>personal/cart/order/" id="newOrder"
                   class="show-always btn-simple btn-medium"><?= GetMessage("BASKET_TABS_ORDER_MAKE") ?></a>
                
                <? if ($USER->IsAuthorized() && array_intersect([20], $USER->GetUserGroupArray())
                    && $USER->GetLogin() == 'kalita') { ?>
                    <div class="precheck">
                        <a href="/salons/check/" type="submit" value="Предчек"
                           class="precheck btn-simple btn-medium btn-border">Предчек</a>
                    </div>
                <? } ?>
            </div>
        </div>
        
        <? if (!$USER->IsAuthorized()) { ?>
            <div class="loginForm">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-discount" width="24"
                     height="24" viewBox="0 0 24 24" stroke-width="2" stroke="#e20074" fill="none"
                     stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle">
                    <path stroke="none" d="M0 0h24v24H0z"/>
                    <line x1="9" y1="15" x2="15" y2="9"/>
                    <circle cx="9.5" cy="9.5" r=".5" fill="currentColor"/>
                    <circle cx="14.5" cy="14.5" r=".5" fill="currentColor"/>
                    <path d="M5 7.2a2.2 2.2 0 0 1 2.2 -2.2h1a2.2 2.2 0 0 0 1.55 -.64l.7 -.7a2.2 2.2 0 0 1 3.12 0l.7 .7a2.2 2.2 0 0 0 1.55 .64h1a2.2 2.2 0 0 1 2.2 2.2v1a2.2 2.2 0 0 0 .64 1.55l.7 .7a2.2 2.2 0 0 1 0 3.12l-.7 .7a2.2 2.2 0 0 0 -.64 1.55 v1a2.2 2.2 0 0 1 -2.2 2.2h-1a2.2 2.2 0 0 0 -1.55 .64l-.7 .7a2.2 2.2 0 0 1 -3.12 0l-.7 -.7a2.2 2.2 0 0 0 -1.55 -.64h-1a2.2 2.2 0 0 1 -2.2 -2.2v-1a2.2 2.2 0 0 0 -.64 -1.55l-.7 -.7a2.2 2.2 0 0 1 0 -3.12l.7 -.7a2.2 2.2 0 0 0 .64 -1.55 v-1"/>
                </svg>&nbsp;<a href="/lk/?backurl=/personal/cart/" class="ff-medium medi-color">Авторизуйтесь или
                    зарегистрируйтесь, чтобы рассчитать персональные скидки</a>&nbsp;<svg
                        xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-discount" width="24"
                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="#e20074" fill="none"
                        stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle">
                    <path stroke="none" d="M0 0h24v24H0z"/>
                    <line x1="9" y1="15" x2="15" y2="9"/>
                    <circle cx="9.5" cy="9.5" r=".5" fill="currentColor"/>
                    <circle cx="14.5" cy="14.5" r=".5" fill="currentColor"/>
                    <path d="M5 7.2a2.2 2.2 0 0 1 2.2 -2.2h1a2.2 2.2 0 0 0 1.55 -.64l.7 -.7a2.2 2.2 0 0 1 3.12 0l.7 .7a2.2 2.2 0 0 0 1.55 .64h1a2.2 2.2 0 0 1 2.2 2.2v1a2.2 2.2 0 0 0 .64 1.55l.7 .7a2.2 2.2 0 0 1 0 3.12l-.7 .7a2.2 2.2 0 0 0 -.64 1.55 v1a2.2 2.2 0 0 1 -2.2 2.2h-1a2.2 2.2 0 0 0 -1.55 .64l-.7 .7a2.2 2.2 0 0 1 -3.12 0l-.7 -.7a2.2 2.2 0 0 0 -1.55 -.64h-1a2.2 2.2 0 0 1 -2.2 -2.2v-1a2.2 2.2 0 0 0 -.64 -1.55l-.7 -.7a2.2 2.2 0 0 1 0 -3.12l.7 -.7a2.2 2.2 0 0 0 .64 -1.55 v-1"/>
                </svg>
            </div>
        <? } ?>

        <div class="clear"></div>
    </div>
    
    <? if (!empty($arResult["RELATED_CART"])):
        $GLOBALS['relatedFilter'] = ['ID' => array_values($arResult['RELATED_CART']), 'ACTIVE' => "Y"]; ?>

        <div id="related" class="productsListName" data-list-name="Вам может понравиться">
            <a href="#" class="btnLeft"></a>
            <a href="#" class="btnRight"></a>
            <h2 class="ff-medium h3">МЫ ТАКЖЕ РЕКОМЕНДУЕМ:</h2><br>
            <div class="items productList productsListName slideBox" id="related_slide"
                 data-list-name="related_in_cart">
                <? foreach ($arResult['RELATED_CART'] as $index => $arElement): ?>
                    
                    <? $APPLICATION->IncludeComponent(
                        "dresscode:catalog.item",
                        "default_fast",
                        array(
                            
                            "CACHE_TYPE" => "N",
                            "CACHE_TIME" => "36000",
                            "FILTER_NAME" => "relatedFilter",
                            
                            "PRODUCT_ID" => $arElement,
                            "IBLOCK_TYPE" => "catalog",
                            "IBLOCK_ID" => "17",
                            "PICTURE_WIDTH" => "220",
                            "PICTURE_HEIGHT" => "200",
                            
                            "HIDE_NOT_AVAILABLE" => "Y",
                            "HIDE_MEASURES" => "Y",
                            "LAZY_LOAD_PICTURES" => "Y",
                            "PRODUCT_PRICE_CODE" => array(
                                0 => $GLOBALS['medi']['price'][SITE_ID],
                            ),
                            "CONVERT_CURRENCY" => "N",
                            "COMPOSITE_FRAME_MODE" => "A",
                            "COMPOSITE_FRAME_TYPE" => "AUTO",
                            "POS_COUNT" => $pos_counter,
                            "LIST_TYPE" => "related_in_cart"
                        ),
                        false
                    ); ?>
                    <? $pos_counter++; ?>
                <? endforeach; ?>
            </div>
        </div>
        <script type="text/javascript">
            $("#related").mediCarousel({
                leftButton: "#related .btnLeft",
                rightButton: "#related .btnRight",
                countElement: 5,
                resizeElement: true,
                resizeAutoParams: {
                    1920: 5,
                    1480: 4,
                    1100: 3,
                    800: 2,
                    370: 1
                }
            });
        </script>
    <? endif; ?>


    <!-- <a href="<?= SITE_DIR ?>personal/cart/order/" class="btn-simple btn-medium goToOrder<? if (empty($arResult["IS_MIN_ORDER_AMOUNT"])): ?> hidden<? endif; ?>"><img src="<?= SITE_TEMPLATE_PATH ?>/images/order.png"><?= GetMessage("BASKET_TABS_ORDER_MAKE") ?></a> -->

    <div class="detail-text-wrap flex">
        <? /*<div class="flex-item">
			<div class="tc">
				<div class="fastBayLabel">Оформление заказа</div>
				<div class="fastBayText">Оформите покупку, уточнив адрес и&nbsp;стоимость доставки, способ оплаты и&nbsp;указав ФИО покупателя</div>
				<a href="<?=SITE_DIR?>personal/cart/order/" id="newOrder" class="show-always btn-simple btn-medium"><?=GetMessage("BASKET_TABS_ORDER_MAKE")?></a>
			</div>
	</div>
	<div class="flex-item">
			<div class="tc">
				<div class="fastBayLabel">Быстрое оформление заказа</div>
				<div class="fastBayText">Оформите покупку, заполнив только имя и&nbsp;телефон, мы&nbsp;свяжемся с&nbsp;Вами для&nbsp;уточнения всех деталей и&nbsp;подтверждения заказа по&nbsp;телефону</div>
				<a href="#" class="show-always btn-simple btn-micro" id="fastBasketOrder"><?=GetMessage("FAST_BUY_PRODUCT_BTN_TEXT")?></a>
			</div>
	</div>*/ ?>
    </div>

    <div class="basketError error1">
        <div class="basketErrorContainer">
            <div class="errorPicture"><img src="<?= $templateFolder ?>/images/error.jpg" alt="" title=""></div>
            <div class="errorHeading"><?= Loc::getMessage("ORDER_ERROR_1_HEADING") ?></div>
            <a href="#" class="basketErrorClose errorClose"><span class="errorCloseLink"></span></a>
            <div class="errorMessage"><?= Loc::getMessage("ORDER_ERROR_1") ?></div>
            <a href="#" class="basketErrorClose btn-simple btn-small"><?= Loc::getMessage("ORDER_CLOSE") ?></a>
        </div>
    </div>
    <div class="basketError error2">
        <div class="basketErrorContainer">
            <div class="errorPicture"><img src="<?= $templateFolder ?>/images/error.jpg" alt="" title=""></div>
            <div class="errorHeading"><?= Loc::getMessage("ORDER_ERROR_2_HEADING") ?></div>
            <a href="#" class="basketErrorClose errorClose"><span class="errorCloseLink"></span></a>
            <div class="errorMessage"><?= Loc::getMessage("ORDER_ERROR_2") ?></div>
            <a href="#" class="basketErrorClose btn-simple btn-small"><?= Loc::getMessage("ORDER_CLOSE") ?></a>
        </div>
    </div>
    <div class="basketError error3">
        <div class="basketErrorContainer">
            <div class="errorPicture"><img src="<?= $templateFolder ?>/images/error.jpg" alt="" title=""></div>
            <div class="errorHeading"><?= Loc::getMessage("ORDER_ERROR_3_HEADING") ?></div>
            <a href="#" class="basketErrorClose errorClose"><span class="errorCloseLink"></span></a>
            <div class="errorMessage"><?= Loc::getMessage("ORDER_ERROR_3") ?></div>
            <a href="#" class="basketErrorClose btn-simple btn-small"><?= Loc::getMessage("ORDER_CLOSE") ?></a>
        </div>
    </div>
<? else:
    unset($_SESSION['lmxapp']['coupon']);
    unset($_SESSION['lmxapp']['coupon_ok']);
    ?>
    <div id="empty">
        <div class="emptyWrapper">
            <div class="pictureContainer">
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/emptyFolder.png"
                     alt="<?= Loc::getMessage("EMPTY_HEADING") ?>" class="emptyImg">
            </div>
            <div class="info">
                <h3><?= Loc::getMessage("EMPTY_HEADING") ?></h3>
                <p><?= Loc::getMessage("EMPTY_TEXT") ?></p>
                <a href="<?= SITE_DIR ?>" class="back"><?= Loc::getMessage("MAIN_PAGE") ?></a>
            </div>
            <br/><br/><br/><br/>
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
		);*/
        ?>
    </div>
<? endif; ?>

<script>
    var basketLang = {
        "max-quantity": '<?=Loc::getMessage("MAX_QUANTITY")?>',
        "empty-paysystems": '<?=Loc::getMessage("EMPTY_PAYSYSTEMS")?>',
        "empty-deliveries": '<?=Loc::getMessage("EMPTY_DELIVERIES")?>',
    };
</script>

<script>
    var ajaxDir = "<?=SITE_TEMPLATE_PATH?>/components/bitrix/sale.basket.basket/.default";
    var siteId = "<?=$component->getSiteId()?>";
    var siteCurrency = <?=\Bitrix\Main\Web\Json::encode($arResult["CURRENCY"]);?>;
    var basketParams = <?=\Bitrix\Main\Web\Json::encode(\DigitalWeb\Basket::clearParams($arParams));?>;
    var maskedUse = "<?=$arParams["USE_MASKED"]?>";
    var maskedFormat = "<?=$arParams["MASKED_FORMAT"]?>";
</script>

<? function getHTMLDataAttrs($arProperty = array(), $dataAttr = "")
{
    if ($arProperty["IS_PROFILE_NAME"] == "Y") $dataAttr .= ' data-profile-name="Y"';
    if ($arProperty["IS_EMAIL"] == "Y") $dataAttr .= ' data-mail="Y"';
    if ($arProperty["IS_PAYER"] == "Y") $dataAttr .= ' data-payer="Y"';
    if ($arProperty["IS_LOCATION4TAX"] == "Y") $dataAttr .= ' data-location4tax="Y"';
    if ($arProperty["IS_FILTERED"] == "Y") $dataAttr .= ' data-filtred="Y"';
    if ($arProperty["IS_ZIP"] == "Y") $dataAttr .= ' data-zip="Y"';
    if ($arProperty["IS_PHONE"] == "Y") $dataAttr .= ' data-mobile="Y"';
    if ($arProperty["IS_ADDRESS"] == "Y") $dataAttr .= ' data-address="Y"';
    return $dataAttr;
} ?>

<? function printOrderPropertyHTML($arProperty, $attrList = "")
{
    $dataAttr = getHTMLDataAttrs($arProperty);
    $propId = randString(7); ?>
    <li<?= $attrList ?>>
        <? if (!empty($arProperty["TYPE"]) && $arProperty["TYPE"] != "Y/N"): ?>
            <span class="label"><?= $arProperty["NAME"] ?><? if ($arProperty["REQUIRED"] === "Y"): ?>*<? endif; ?></span>
            <label><?= $arProperty["DESCRIPTION"] ?></label>
        <? endif; ?>
        <? if ($arProperty["TYPE"] == "STRING" && (empty($arProperty["MULTILINE"]) || $arProperty["MULTILINE"] == "N")): ?>
            <input type="text" name="ORDER_PROP_<?= $arProperty["ID"] ?>" value="<?= $arProperty["CURRENT_VALUE"] ?>"
                   data-required="<? if ($arProperty["REQUIRED"] === "Y"): ?>Y<? else: ?>N<? endif; ?>"
                   id="<?= $arProperty["ID"] ?>" data-id="<?= $arProperty["ID"] ?>"<?= $dataAttr ?>>
        <? elseif ($arProperty["TYPE"] == "STRING" && $arProperty["MULTILINE"] == "Y"): ?>
            <textarea name="ORDER_PROP_<?= $arProperty["ID"] ?>"
                      data-required="<? if ($arProperty["REQUIRED"] === "Y"): ?>Y<? else: ?>N<? endif; ?>"
                      id="<?= $arProperty["ID"] ?>"
                      data-id="<?= $arProperty["ID"] ?>"<?= $dataAttr ?>><?= $arProperty["CURRENT_VALUE"] ?></textarea>
        <? elseif ($arProperty["TYPE"] == "NUMBER"): ?>
            <input type="text" name="ORDER_PROP_<?= $arProperty["ID"] ?>" value="<?= $arProperty["CURRENT_VALUE"] ?>"
                   data-required="<? if ($arProperty["REQUIRED"] === "Y"): ?>Y<? else: ?>N<? endif; ?>"
                   id="<?= $arProperty["ID"] ?>" data-id="<?= $arProperty["ID"] ?>" data-number="Y"<?= $dataAttr ?>>
        <? elseif ($arProperty["TYPE"] == "DATE"): ?>
            <input type="text" name="ORDER_PROP_<?= $arProperty["ID"] ?>" value="<?= $arProperty["CURRENT_VALUE"] ?>"
                   data-required="<? if ($arProperty["REQUIRED"] === "Y"): ?>Y<? else: ?>N<? endif; ?>"
                   id="<?= $arProperty["ID"] ?>" data-id="<?= $arProperty["ID"] ?>" data-time="Y"
                   onclick="BX.calendar({node: this, field: this, bTime: <?= (!empty($arProperty["TIME"]) && $arProperty["TIME"] == "Y") ? "true" : "false" ?>});"
                   class="timeField"<?= $dataAttr ?>>
        <? elseif ($arProperty["TYPE"] == "Y/N"): ?>
            <label><?= $arProperty["DESCRIPTION"] ?></label>
            <div class="propLine">
                <input type="checkbox" value="Y"<? if ($arProperty["CURRENT_VALUE"] == "Y"): ?> checked<? endif; ?>
                       data-required="<? if ($arProperty["REQUIRED"] === "Y"): ?>Y<? else: ?>N<? endif; ?>"
                       id="<?= $arProperty["ID"] ?>" data-id="<?= $arProperty["ID"] ?>"
                       name="ORDER_PROP_<?= $arProperty["ID"] ?>"<?= $dataAttr ?>>
                <label for="<?= $arProperty["ID"] ?>"><?= $arProperty["NAME"] ?><? if ($arProperty["REQUIRED"] === "Y"): ?>*<? endif; ?></label>
            </div>
        <? elseif ($arProperty["TYPE"] == "ENUM" && $arProperty["MULTIPLE"] == "Y" && $arProperty["MULTIELEMENT"] == "Y" && !empty($arProperty["OPTIONS"])): ?>
            <? foreach ($arProperty["OPTIONS"] as $nextIndex => $nextValue): ?>
                <div class="propLine">
                    <input type="checkbox" name="ORDER_PROP_<?= $arProperty["ID"] ?>" value="<?= $nextIndex ?>"
                           data-required="<? if ($arProperty["REQUIRED"] === "Y"): ?>Y<? else: ?>N<? endif; ?>"
                           id="<?= $propId ?>_<?= $arProperty["ID"] ?>_<?= $nextIndex ?>"<?= (is_array($arProperty["CURRENT_VALUE"]) && in_array($nextIndex, $arProperty["CURRENT_VALUE"]) ? " checked" : "") ?>
                           data-id="<?= $arProperty["ID"] ?>"<?= $dataAttr ?>>
                    <label for="<?= $propId ?>_<?= $arProperty["ID"] ?>_<?= $nextIndex ?>"><?= htmlspecialcharsbx($nextValue) ?></label>
                </div>
            <? endforeach; ?>
        <? elseif ($arProperty["TYPE"] == "ENUM" && $arProperty["MULTIPLE"] == "N" && $arProperty["MULTIELEMENT"] == "N" && !empty($arProperty["OPTIONS"])): ?>
            <select name="ORDER_PROP_<?= $arProperty["ID"] ?>"
                    data-required="<? if ($arProperty["REQUIRED"] === "Y"): ?>Y<? else: ?>N<? endif; ?>"
                    data-id="<?= $arProperty["ID"] ?>"<?= $dataAttr ?>>
                <? foreach ($arProperty["OPTIONS"] as $nextIndex => $nextValue): ?>
                    <option value="<?= $nextIndex ?>"<?= ($arProperty["CURRENT_VALUE"] == $nextIndex ? " selected" : "") ?>><?= htmlspecialcharsbx($nextValue) ?></option>
                <? endforeach; ?>
            </select>
        <? elseif ($arProperty["TYPE"] == "ENUM" && $arProperty["MULTIPLE"] == "N" && $arProperty["MULTIELEMENT"] == "Y" && !empty($arProperty["OPTIONS"])): ?>
            <? foreach ($arProperty["OPTIONS"] as $nextIndex => $nextValue): ?>
                <div class="propLine">
                    <input type="radio" name="ORDER_PROP_<?= $arProperty["ID"] ?>" value="<?= $nextIndex ?>"
                           data-required="<? if ($arProperty["REQUIRED"] === "Y"): ?>Y<? else: ?>N<? endif; ?>"
                           id="<?= $propId ?>_<?= $arProperty["ID"] ?>_<?= $nextIndex ?>"<?= ($arProperty["CURRENT_VALUE"] == $nextIndex ? " checked" : "") ?>
                           data-id="<?= $arProperty["ID"] ?>"<?= $dataAttr ?>>
                    <label for="<?= $propId ?>_<?= $arProperty["ID"] ?>_<?= $nextIndex ?>"><?= htmlspecialcharsbx($nextValue) ?></label>
                </div>
            <? endforeach; ?>
        <? elseif ($arProperty["TYPE"] == "ENUM" && $arProperty["MULTIPLE"] == "Y" && $arProperty["MULTIELEMENT"] == "N" && !empty($arProperty["OPTIONS"])): ?>
            <select multiple name="ORDER_PROP_<?= $arProperty["ID"] ?>"
                    size="<?= ((IntVal($arProperty["SIZE"]) > 0) ? $arProperty["SIZE"] : 5) ?>" class="multi"
                    data-required="<? if ($arProperty["REQUIRED"] === "Y"): ?>Y<? else: ?>N<? endif; ?>"
                    data-id="<?= $arProperty["ID"] ?>"<?= $dataAttr ?>>
                <? foreach ($arProperty["OPTIONS"] as $nextIndex => $nextValue): ?>
                    <option value="<?= $nextIndex ?>"<?= (is_array($arProperty["CURRENT_VALUE"]) && in_array($nextIndex, $arProperty["CURRENT_VALUE"]) ? " selected" : "") ?>><?= htmlspecialcharsbx($nextValue) ?></option>
                <? endforeach; ?>
            </select>
        <? elseif ($arProperty["TYPE"] == "FILE"): ?>
            <input type="file" name="ORDER_PROP_<?= $arProperty["ID"] ?>"
                   data-required="<? if ($arProperty["REQUIRED"] === "Y"): ?>Y<? else: ?>N<? endif; ?>"
                   id="<?= $arProperty["ID"] ?>" data-id="<?= $arProperty["ID"] ?>" class="file" autocomplete="off"
                   data-file="Y"<? if ($arProperty["MULTIPLE"] == "Y"): ?> multiple<? endif; ?>
                   value=""<?= $dataAttr ?>>
        <? elseif ($arProperty["TYPE"] == "LOCATION"): ?>
            <input type="text" name="ORDER_PROP_<?= $arProperty["ID"] ?>"
                   value="<?= $arProperty["LOCATION"]["DISPLAY_VALUE"] ?>"
                   data-last-id="<?= $arProperty["LOCATION_ID"] ?>"
                   data-last-value="<?= $arProperty["LOCATION"]["DISPLAY_VALUE"] ?>"
                   data-required="<? if ($arProperty["REQUIRED"] === "Y"): ?>Y<? else: ?>N<? endif; ?>"
                   id="<?= $arProperty["ID"] ?>" data-id="<?= $arProperty["ID"] ?>" class="location" autocomplete="off"
                   data-location="<?= $arProperty["LOCATION_ID"] ?>" <?= $dataAttr ?>>
            <div class="locationSwitchContainer"></div>
        <? endif; ?>
    </li>
<? } ?>

<? function printExtraServiceItemHTML($extraServiceItem = array(), $currencyCode = "RUB")
{
    
    if (!empty($extraServiceItem)) {
        if ($extraServiceItem["CLASS_NAME"] == "\Bitrix\Sale\Delivery\ExtraServices\Enum") {
            if (!empty($extraServiceItem["PARAMS"]["PRICES"])) {
                ?>
                <? if (!empty($extraServiceItem["NAME"])): ?>
                    <div class="serviceName"><?= $extraServiceItem["NAME"] ?></div>
                <? endif; ?>
                <? if (!empty($extraServiceItem["DESCRIPTION"])): ?>
                    <div class="serviceDescription"><?= $extraServiceItem["DESCRIPTION"] ?></div>
                <? endif; ?>
                <div class="serviceSelectItem">
                    <select name="extra_<?= $extraServiceItem["ID"] ?>" class="extraServiceSwitch"
                            data-id="<?= $extraServiceItem["ID"] ?>"
                            data-default="<?= $extraServiceItem["INIT_VALUE"] ?>"
                            data-last="<?= !empty($extraServiceItem["INIT_VALUE"]) ? $extraServiceItem["PARAMS"]["PRICES"][$extraServiceItem["INIT_VALUE"]]["PRICE"] : 0; ?>">
                        <? foreach ($extraServiceItem["PARAMS"]["PRICES"] as $serviceItemId => $nextServiceItem): ?>
                            <option value="<?= $serviceItemId ?>"
                                    data-price="<?= $nextServiceItem["PRICE"] ?>"<? if ($extraServiceItem["INIT_VALUE"] == $serviceItemId): ?> selected<? endif; ?>><?= $nextServiceItem["TITLE"] ?>
                                <b><?= FormatCurrency($nextServiceItem["PRICE"], $currencyCode); ?></option>
                        <? endforeach; ?>
                    </select>
                </div>
                <?
            }
        } elseif ($extraServiceItem["CLASS_NAME"] == "\Bitrix\Sale\Delivery\ExtraServices\Checkbox") {
            ?>
            <? $extraId = \Bitrix\Main\Security\Random::getString(8); ?>
            <div class="serviceBoxContainer">
                <input type="checkbox" value="Y" id="service_<?= $extraId ?>"
                       name="service_<?= $extraServiceItem["ID"] ?>" class="extraServiceSwitch"
                       data-id="<?= $extraServiceItem["ID"] ?>" data-default="<?= $extraServiceItem["INIT_VALUE"] ?>"
                       data-price="<?= $extraServiceItem["PARAMS"]["PRICE"] ?>"<? if ($extraServiceItem["INIT_VALUE"] == "Y"): ?> checked<? endif; ?>>
                <label for="service_<?= $extraId ?>"><?= $extraServiceItem["NAME"] ?> <span
                            class="servicePrice"><b>(<?= FormatCurrency($extraServiceItem["PARAMS"]["PRICE"], $currencyCode); ?>)</b></span></label>
                <div class="serviceDescription"><?= $extraServiceItem["DESCRIPTION"] ?></div>
            </div>
            <?
        } elseif ($extraServiceItem["CLASS_NAME"] == "\Bitrix\Sale\Delivery\ExtraServices\Quantity") {
            ?>
            <div class="extraServiceQuantity">
                <div class="serviceHeadingContainer">
                    <div class="serviceName"><?= $extraServiceItem["NAME"] ?></div>
                    <div class="servicePrice"><?= Loc::getMessage("PRICE_FOR_PIECE"); ?>
                        <b><?= FormatCurrency($extraServiceItem["PARAMS"]["PRICE"], $currencyCode); ?></b></div>
                    <div class="serviceTotalSum"><b><?= Loc::getMessage("TOTAL_SUM") ?> (<span
                                    class="extraServiceItemSum"><?= FormatCurrency(($extraServiceItem["PARAMS"]["PRICE"] * $extraServiceItem["INIT_VALUE"]), $currencyCode); ?></span>)</b>
                    </div>
                </div>
                <div class="serviceDescription"><?= $extraServiceItem["DESCRIPTION"] ?></div>
                <input type="text" name="service_<?= $extraServiceItem["ID"] ?>"
                       value="<?= $extraServiceItem["INIT_VALUE"] ?>" class="extraServiceSwitch"
                       data-id="<?= $extraServiceItem["ID"] ?>" data-default="<?= $extraServiceItem["INIT_VALUE"] ?>"
                       data-last="<?= $extraServiceItem["INIT_VALUE"] ?>"
                       data-price="<?= $extraServiceItem["PARAMS"]["PRICE"] ?>">
            </div>
            <?
        }
        
    }
    
} ?>
