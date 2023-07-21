<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

//__( $arResult['ContrInfo'], true);
?>

<? // BEGIN .order-form  ?>
<div class="order-form">

    <? // Ошибки и предупреждения в форме  ?>
    <? if ($arResult["isFormErrors"] == "Y"): ?>
        <div class="container">
            <div class="twelve columns">
                <?= $arResult["FORM_ERRORS_TEXT"]; ?>
            </div>
        </div>
    <? endif; ?>
    <div class="container">
        <div class="twelve columns">
            <?= $arResult["FORM_NOTE"]; ?>
        </div>
    </div>

    <? if ($arResult['isFormNote'] != 'Y'): ?>
        <? // Заголовок самой формы ?>
        <? // = $arResult["FORM_HEADER"]; ?>
    <? // BEGIN .order-form__author ?>
    <div class="order-form__author">
        <div class="container">
            <div class="row">
                <div class="six columns">
                    <label for="order-form__authorname" class="inline-block" >Автор заявки:</label>
                    <select id="order-form__authorname" class="u-full-width" name="form_text_94">
                        <? foreach ($arResult['AuthorInfo'] as $arOption): ?>
                            <option value="<?= $arOption['NAME'] ?>" <?=($arOption['SELECTED'] == 'Y' ? ' selected' : '' );?>><?= $arOption['NAME']; ?></option>
                        <? endforeach; ?>
                    </select>
                </div>
                <div class="six columns">
                    <label for="order-form__contractor">Исполнитель:</label>

                    <select id="order-form__contractor" class="u-full-width" name="form_text_95">
                        <? foreach ($arResult['ContrInfo'] as $arOption): ?>
                            <option value="<?= $arOption['NAME'] ?>" <?=($arOption['SELECTED'] == 'Y' ? ' selected' : '' );?>><?= $arOption['NAME']; ?></option>
                        <? endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="order-form__main-info">
            <div class="container">
                <div class="row">
                    <div class="two columns">
                        <label for="order-form__call-date">Дата звонка:</label>
                        <input id="order-form__call-date" class="u-full-width  datepicker" type="text" name="form_text_76" value="<?= $arResult['RESULT']['call_date']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                    <div class="two columns">
                        <label for="order-form__delivery-date" class="order-form__label--truncate">Дата доставки:</label>
                        <input id="order-form__call-date" class="u-full-width datepicker" type="text"  name="form_text_77" value="<?= $arResult['RESULT']['delivery_date']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                    <div class="three columns">
                        <label for="order-form__delivery-interval" class="order-form__label--truncate">Время доставки:</label>
                        <input type="text" id="order-form__delivery-interval" class="u-full-width" name="form_dropdown_delivery_interval" value="<?= $arResult['RESULT']['delivery_interval']['ANSWER_VALUE'][0]['ANSWER_TEXT']; ?>" disabled>
                    </div>
                    <div class="two columns">
                        <label for="order-form__delivery-time" class="order-form__label--truncate">Время (точно):</label>
                        <input id="order-form__delivery-time" class="u-full-width timepicker" type="text" name="form_text_83" value="<?= $arResult['RESULT']['delivery_time']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                    <div class="three columns">
                        <label for="order-form__order-status">Статус заявки:</label>
                        <input type="text" id="order-form__client-gender" class="u-full-width" value="<?= $arResult['RESULT_STATUS_TITLE']; ?>" disabled>
                    </div>
                </div>


                <div class="row">
                    <div class="two columns">
                        <label for="order-form__receipt-code">Код рецепта:</label>
                        <input id="order-form__receipt-code" class="u-full-width" type="text" name="form_text_84" value="<?= $arResult['RESULT']['recipe_number']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                    <div class="five columns">
                        <label for="order-form__doctor-sif">Ф.И.О. врача:</label>
                        <input id="order-form__doctor-sif" class="u-full-width" type="text" name="form_text_85" value="<?= $arResult['RESULT']['doctor_name']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                    <div class="five columns">
                        <label for="order-form__medical-facility">ЛПУ:</label>
                        <input id="order-form__medical-facility" class="u-full-width" type="text" name="form_text_86" value="<?= $arResult['RESULT']['medical_facility']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                </div>
            </div>
        </div>

        <? // BEGIN .order-form__client  ?>
        <div class="order-form__client">
            <div class="container">
                <div class="row">
                    <div class="eight columns">
                        <label for="order-form__client-sif" class="inline-block" >Ф.И.О. пациента:</label>
                        <? /* <span class="order-form__mic-holder">
                          <div class="icon icon--25 icon__mic--25 bind__google-speech cursor__pointer" data-speech-element-id="order-form__client-sif"></div>
                          </span> */ ?>
                        <input id="order-form__client-sif" class="u-full-width" type="text" name="form_text_87"  value="<?= $arResult['RESULT']['client_name']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                    <div class="two columns">
                        <label for="order-form__client-age">Возраст:</label>
                        <input id="order-form__client-age" class="u-full-width" type="text" name="form_text_88"  value="<?= $arResult['RESULT']['client_age']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                    <div class="two columns">
                        <label for="order-form__client-gender">Пол пациента:</label>
                        <input type="text" id="order-form__client-gender" class="u-full-width" name="form_dropdown_client_gender" value="<?= $arResult['RESULT']['client_gender']['ANSWER_VALUE'][0]['ANSWER_TEXT']; ?>" disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="six columns">
                        <label for="order-form__delivery-cost" class="order-form__label--truncate">Контактный телефон:</label>
                        <input id="order-form__contact-phone" class="u-full-width" type="text" name="form_text_90" value="<?= $arResult['RESULT']['client_phone']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                    <div class="six columns">
                        <label for="order-form__comment">Комментарий:</label>
                        <input type="text" id="order-form__comment" class="u-full-width" name="form_text_94" value="<?= $arResult['RESULT']['comment']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="twelve columns">
                        <label for="order-form__contact-address" class="order-form__label--truncate">Адрес доставки:</label>
                        <textarea id="order-form__contact-address" class="u-full-width" name="form_textarea_95" disabled><?= $arResult['RESULT']['client_address']['ANSWER_VALUE'][0]['USER_TEXT']; ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <? // #END .order-form__client  ?>

        <? // BEGIN .order-form__product  ?>
        <div class="order-form__product">
            <div class="container">
                <div class="row">
                    <div class="five columns">
                        <label for="order-form__product-model">Модель:</label>
                        <input id="order-form__product-model" class="u-full-width" type="text" name="form_text_96" value="<?= $arResult['RESULT']['product_model']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                    <div class="two columns">
                        <label for="order-form__product-side">Сторона:</label>
                        <input type="text" id="order-form__product-side" class="u-full-width" name="form_dropdown_product_side"  value="<?= $arResult['RESULT']['product_side']['ANSWER_VALUE'][0]['ANSWER_TEXT']; ?>" disabled>
                        <? foreach ($arResult['QUESTIONS']['product_side']['STRUCTURE'] as $arOption): ?>
                            <option value="<?= $arOption['ID'] ?>"<?= $arOption['ID'] == $arResult['arrVALUES']['form_dropdown_product_side'] ? ' selected' : ''; ?>><?= $arOption['MESSAGE']; ?></option>
                        <? endforeach; ?>
                        </select>
                    </div>

                    <div class="five columns">
                        <label for="order-form__product_sku">Артикул изделия:</label>
                        <input id="order-form__product_sku" class="u-full-width" type="text" name="form_text_100" value="<?= $arResult['RESULT']['product_sku']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="three columns">
                        <label for="order-form__client-measurements">Мерки:</label>
                        <input id="order-form__client-measurements" class="u-full-width" type="text"name="form_text_101" value="<?= $arResult['RESULT']['client_measurements']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                    <div class="two columns">
                        <label for="order-form__client-height">Рост:</label>
                        <input id="order-form__client-height" class="u-full-width" type="text" name="form_text_103" value="<?= $arResult['RESULT']['client_height']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                    <div class="two columns">
                        <label for="order-form__client-weight">Вес:</label>
                        <input id="order-form__client-weight" class="u-full-width" type="text" name="form_text_102" value="<?= $arResult['RESULT']['client_weight']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                    <div class="five columns">
                        <label for="order-form__product_sku">Размер:</label>
                        <input id="order-form__product_sku" class="u-full-width" type="text" name="form_text_104" value="<?= $arResult['RESULT']['product_size']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="two columns">
                        <label for="order-form__full-price" class="order-form__label--truncate">Цена без скидки:</label>
                        <input id="order-form__full-price" class="u-full-width" type="text" name="form_text_105" value="<?= $arResult['RESULT']['full_price']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                    <div class="three columns">
                        <label for="order-form__discount-reason" class="order-form__label--truncate">Обоснование скидки:</label>
                        <input type="text" id="order-form__discount-reason" class="u-full-width" name="form_dropdown_discount_reason" value="<?= $arResult['RESULT']['discount_reason']['ANSWER_VALUE'][0]['ANSWER_TEXT']; ?>" disabled>
                    </div>
                    <div class="two columns">
                        <label for="order-form__dicsount-card" class="order-form__label--truncate">Номер ДК:</label>
                        <input type="text" id="order-form__dicsount-card" class="u-full-width" name="form_text_117" value="<?= $arResult['RESULT']['dk_number']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                    <div class="two columns">
                        <label for="order-form__discount-value" class="order-form__label--truncate">Цена со скидкой:</label>
                        <input id="order-form__discount-value" class="u-full-width" type="text" name="form_text_110" value="<?= $arResult['RESULT']['discount_price']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                    <div class="three columns">
                        <label for="order-form__delivery-cost" class="order-form__label--truncate">Выезд специалиста:</label>
                        <input type="text" id="order-form__delivery-cost" class="u-full-width" name="form_dropdown_delivery_price" value="<?= $arResult['RESULT']['delivery_price']['ANSWER_VALUE'][0]['ANSWER_TEXT']; ?>" disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="two columns">
                        <label class="order-form__label--with-margin">Оплата:</label>
                    </div>
                    <div class="ten columns">
                        <?= $arResult['RESULT']['payment_type']['ANSWER_VALUE'][0]['ANSWER_TEXT']; ?>
                    </div>
                </div>
            </div>
        </div>
        <? // #END .order-form__product   ?>
        <? if ($arResult["isAccessFormResultEdit"] == "Y" && strlen($arParams["EDIT_URL"]) > 0): ?>
            <?
            $href = $arParams["SEF_MODE"] == "Y" ? str_replace("#RESULT_ID#", $arParams["RESULT_ID"], $arParams["EDIT_URL"]) : $arParams["EDIT_URL"] . (strpos($arParams["EDIT_URL"], "?") === false ? "?" : "&") . "RESULT_ID=" . $arParams["RESULT_ID"] . "&WEB_FORM_ID=" . $arParams["WEB_FORM_ID"];
            ?>
            <div class="order-form__submit print-hidden">
                <div class="container">
                    <div class="row">
                        <div class="two columns">
                            <a class="cta cta--print"><i class="cta__icon"></i> <span class="cta__text">Распечатать</span></a>
                        </div>
                        <div class="five columns">
                            &nbsp;
                        </div>
                        <div class="five columns">
                            <button data-edit-url="<?= $href; ?>" class="button-primary u-full-width order-form__submit-button"><?= GetMessage("FORM_EDIT"); ?></button>
                            <?
                            /* if ($arResult["F_RIGHT"] >= 15): ?>
                              <input type="hidden" name="web_form_apply" value="Y">
                              <input type="submit" name="web_form_apply" value="<?= GetMessage("FORM_APPLY") ?>" class="button-primary u-full-width">
                              <? endif; ?>
                              <input type="reset" value="<?= GetMessage("FORM_RESET"); ?>" class="button-primary u-full-width">
                             */
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <? endif; ?>
        <? // SUMBIT .order-form__product   ?>
        <? // = $arResult["FORM_FOOTER"]; ?>
    <? endif; ?>
</div>
<div class="separator--dashed print-only"></div>
<div class="additional-form print-only">
    <div class="container">
        <div class="row">
            <table class="additional-form__table u-full-width u-max-full-width">
                <tr>
                    <th>Модель</th><th>Размер</th><th>Цвет</th><th>Кол-во</th><th>Цена</th><th>Скидка</th><th>Цена со скидкой</th><th>Основание скидки</th>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="6" style="font-weight: 500;">Общая сумма покупки</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
            </table>
        </div>
        <div class="row additiona-form__sign">
            <div class="six columns">
                <span class="additional-form__sign-text">Специалист:</span> <span class="additiona-form__sign-placeholder"></span>
            </div>
            <div class="six columns">
                <span class="additional-form__sign-text">Подпись клиента:</span> <span class="additiona-form__sign-placeholder"></span>
            </div>
        </div>
    </div>
</div>
<?
// #END .order-form ?>