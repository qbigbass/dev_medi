<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Предчек");

$APPLICATION->SetAdditionalCSS("/salons/check/style.css");
$APPLICATION->AddHeadScript("/salons/check/script.js");
?>
<h1>Предчек</h1>


<? if (!$USER->IsAuthorized() || !array_intersect([20], $USER->GetUserGroupArray())) {
    LocalRedirect("/salons/auth/");
}

$basket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), SITE_ID);

if (count($basket->getQuantityList()) == 0) {
    
    LocalRedirect("/personal/cart/");
    die;
}

?>
<br/>
<form id="precheck_form">
    <div class="precheck_page">
        <div class="client_info">
            <span class="h2 ff-medium">Данные клиента</span>

            <div class="precheck_form">
                <div class="field_row">
                    <label for="client_phone">Телефон:</label>
                    <input type="tel" id="client_phone" name="precheck[phone]" class="phonemask save_field"
                           value="<?= ($_SESSION['lmx']['phone'] ? $_SESSION['lmx']['phone'] : ($_SESSION['precheck_data']['phone'] ?
                               $_SESSION['precheck_data']['phone'] : "")) ?>" required=""/>
                </div>
                <div class="field_row checkbox_row">
                    <input type="checkbox" class="personalInfoField save_field" id="has_recipe"
                           name="precheck[has_recipe]" value="Y" <?= ($_SESSION['precheck_data']['has_recipe'] == 'Y' ?
                        'checked="checked"' : "") ?>>
                    <label class="label-for" for="has_recipe">
                        Есть рецепт?
                    </label>
                </div>

                <div class="field_row has_recipe">
                    <label for="client_phone">Номер рецепта:</label>
                    <input type="text" id="client_phone"
                           name="precheck[reciept]" value="<?= ($_SESSION['precheck_data']['reciept'] ?
                        $_SESSION['precheck_data']['reciept'] : "") ?>" class="save_field"/>
                </div>
                <div class="field_row has_recipe">
                    <label for="client_comment">Комментарий:</label>
                    <textarea id="client_comment" name="precheck[comment]"
                              class="save_field"><?= ($_SESSION['precheck_data']['comment'] ?
                            $_SESSION['precheck_data']['comment'] : "") ?></textarea>
                </div>
                <div class="field_row">
                    <label for="client_coupon">Подарочный купон:</label>
                    <input type="text" id="client_coupon" name="precheck[coupon]"
                           value="<?= ($_SESSION['precheck_data']['coupon'] ?
                               $_SESSION['precheck_data']['coupon'] : "") ?>" class="save_field"/>
                </div>
                <div class="field_row">
                    <label for="client_mtz">Консультант:</label>
                    <input type="search" min="0000" max="9999" id="client_mtz" name="precheck[mtz]"
                           value="<?= ($_SESSION['precheck_data']['mtz'] ?
                               $_SESSION['precheck_data']['mtz'] : "") ?>" class="save_field" required/>
                </div>
                <div class="field_row submit_row">
                    <input type="submit" name="precheck_send" id="precheck_send" class="btn-simple btn-medium"
                           value="Отправить заказ"/>
                </div>
            </div>

        </div>
        <div class="basket_info">

        <span class="h2 ff-medium">Состав заказа
        <a href="/personal/cart/" class="btn-simple btn-micro btn-border">изменить</a></span>
            <? $APPLICATION->IncludeComponent("bitrix:sale.basket.basket", "check", array(
                "COMPONENT_TEMPLATE" => "",
                "PATH_TO_PAYMENT" => "",
                "MIN_SUM_TO_PAYMENT" => "",
                "BASKET_PICTURE_WIDTH" => "100",
                "BASKET_PICTURE_HEIGHT" => "100",
                "REGISTER_USER" => "Y",
                "LAZY_LOAD_PICTURES" => "Y",
                "HIDE_MEASURES" => "Y",
                "USE_MASKED" => "Y",
                "DISABLE_FAST_ORDER" => "N",
                "MASKED_FORMAT" => "+7 (999) 999-99-99",
                "HIDE_NOT_AVAILABLE" => "Y",
                "PRODUCT_PRICE_CODE" => array(
                    0 => $GLOBALS["medi"]["price"][SITE_ID],
                ),
                "GIFT_CONVERT_CURRENCY" => "Y",
                "GIFT_CURRENCY_ID" => "RUB",
                "PART_STORES_AVAILABLE" => "",
                "ALL_STORES_AVAILABLE" => "",
                "NO_STORES_AVAILABLE" => "",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "36000000",
                "COMPOSITE_FRAME_MODE" => "A",
                "COMPOSITE_FRAME_TYPE" => "AUTO"
            ),
                false
            ); ?>
        </div>

    </div>
</form>


<br>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
