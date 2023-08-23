<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

//__($arResult, true);
?>

<? // BEGIN .order-form ?>
<div class="order-form">

    <? // Ошибки и предупреждения в форме ?>
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
        <?= $arResult["FORM_HEADER"]; ?>
        <?= bitrix_sessid_post(); ?>

        <? // BEGIN .order-form__author ?>
        <div class="order-form__author print-hidden ">
            <div class="container">
                <div class="row">
                    <div class="six columns">
                        <label for="order-form__authorname" class="inline-block" >Автор заявки:</label>
                        <input id="order-form__authorname" type="text" class="u-full-width" name="form_text" disabled
                               value="<?=$arResult['sResultCreator']?>"/>
                    </div>
                    <div class="six columns">
                        <label for="order-form__contractor">Исполнитель:</label>

                        <select id="order-form__contractor" class="u-full-width" name="form_text_60">
                            <? foreach ($arResult['ContrInfo'] as $arOption): ?>
                                <option value="<?= $arOption['NAME'] ?>" <?=($arOption['SELECTED'] == 'Y' ? ' selected' : '' );?>><?= $arOption['NAME']; ?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

    <?// lpu form
    if ($arResult['arrVALUES']['form_radio_type'] == '103'):?>

        <div class="order-form__main-info">
            <div class="container">
                <div class="row">
                    <div class="four columns">
                        <label for="order-form__visit-date">Дата визита:</label>
                        <input id="order-form__visit-date" class="u-full-width  datepicker" type="text" name="form_text_101" value="<?=  $arResult['arrVALUES']['form_text_101'] ?>"/>
                    </div>

                    <div class="four columns">
                        <label for="order-form__title">Название ЛПУ:</label>
                        <input id="order-form__title" class="u-full-width " type="text" name="form_text_99" value="<?= $arResult['arrVALUES']['form_text_99']?>"/>
                </div>
                <div class="four columns">
                        <label for="order-form__address">Адрес ЛПУ:</label>
                        <input id="order-form__address" class="u-full-width " type="text" name="form_text_100" value="<?=  $arResult['arrVALUES']['form_text_100']; ?>"/>
                    </div>
                </div>


                <div class="row">
                    <div class="twelwe columns">
                        <label for="order-form__comment">Комментарий:</label>
                        <textarea id="order-form__comment" rows="1" class="u-full-width" name="form_textarea_72"><?=
                            $arResult['arrVALUES']['form_textarea_72']; ?></textarea>

                    </div>

                </div>
            </div>
        </div>

    <?// client form
    else:
    ?>

        <div class="order-form__main-info">
            <div class="container">
                <div class="row">
                    <div class="two columns">
                        <label for="order-form__call-date">Дата звонка:</label>
                        <input id="order-form__call-date" class="u-full-width  datepicker" type="text" name="form_text_52" value="<?= empty($arResult['arrVALUES']['form_text_52']) ? '' : $arResult['arrVALUES']['form_text_52']; ?>"/>
                    </div>
                    <div class="two columns">
                        <label for="order-form__delivery-date" class="order-form__label--truncate">Дата доставки:</label>
                        <input id="order-form__call-date" class="u-full-width datepicker" type="text"  name="form_text_53" value="<?= empty($arResult['arrVALUES']['form_text_53']) ? '' : $arResult['arrVALUES']['form_text_53']; ?>">
                    </div>
                    <div class="three columns">
                        <label for="order-form__delivery-interval">Время доставки:</label>
                        <select id="order-form__delivery-interval" class="u-full-width" data-action="enableElementOnSelect" data-target="#order-form__delivery-time" name="form_dropdown_delivery_interval">
                            <? foreach ($arResult['QUESTIONS']['delivery_interval']['STRUCTURE'] as $arOption): ?>
                                <option value="<?= $arOption['ID'] ?>"<?= $arOption['ID'] == $arResult['arrVALUES']['form_dropdown_delivery_interval'] ? ' selected' : ''; ?> data-enable="<?= $arOption['ID'] == '58' ||  $arOption['ID'] == '59' ? 'true' : 'false' ?>"><?= $arOption['MESSAGE']; ?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                    <div class="two columns">
                        <label for="order-form__delivery-time">Время:</label>
                        <input id="order-form__delivery-time" class="u-full-width timepicker" type="text" name="form_text_61" <?= $arResult['arrVALUES']['form_dropdown_delivery_interval'] != '58' && $arResult['arrVALUES']['form_dropdown_delivery_interval'] != '59' ? 'disabled' : ''; ?>  value="<?if (
                            ( $arResult['arrVALUES']['form_dropdown_delivery_interval'] == '58' ||  $arResult['arrVALUES']['form_dropdown_delivery_interval'] == '59') && !empty($arResult['arrVALUES']['form_text_61'])){ echo $arResult['arrVALUES']['form_text_61'];} ?>">
                    </div>
                    <div class="three columns">
                        <label for="order-form__order-status">Статус заявки:</label>
                        <select id="order-form__client-gender" class="u-full-width" name="status_MEDI_ORTO">
                            <option value="4" <?= $arResult['arResultData']['STATUS_ID'] == '4' ? 'selected' : ''; ?>>В работе</option>
                            <option value="5" <?= $arResult['arResultData']['STATUS_ID'] == '5' ? 'selected' : ''; ?>>Выполнена</option>
                            <option value="6" <?= $arResult['arResultData']['STATUS_ID'] == '6' ? 'selected' : ''; ?>>Отменена</option>
                        </select>
                    </div>
                </div>


                <div class="row">
                    <div class="two columns">
                        <label for="order-form__receipt-code">Код рецепта:</label>
                        <input id="order-form__receipt-code" class="u-full-width" type="text" name="form_text_62" value="<?= empty($arResult['arrVALUES']['form_text_62']) ? '' : $arResult['arrVALUES']['form_text_62']; ?>">
                    </div>
                    <div class="five columns">
                        <label for="order-form__doctor-sif">Ф.И.О. врача:</label>
                        <input id="order-form__doctor-sif" class="u-full-width" type="text" name="form_text_63" value="<?= empty($arResult['arrVALUES']['form_text_63']) ? '' : $arResult['arrVALUES']['form_text_63']; ?>">
                    </div>
                    <div class="five columns">
                        <label for="order-form__medical-facility">ЛПУ:</label>
                        <input id="order-form__medical-facility" class="u-full-width" type="text" name="form_text_64" value="<?= empty($arResult['arrVALUES']['form_text_64']) ? '' : $arResult['arrVALUES']['form_text_64']; ?>">
                    </div>
                </div>
            </div>
        </div>

        <? // BEGIN .order-form__client ?>
        <div class="order-form__client">
            <div class="container">
                <div class="row">
                    <div class="eight columns">
                        <label for="order-form__client-sif" class="inline-block" >Ф.И.О. пациента:</label>
                        <? /* <span class="order-form__mic-holder">
                          <div class="icon icon--25 icon__mic--25 bind__google-speech cursor__pointer" data-speech-element-id="order-form__client-sif"></div>
                          </span> */ ?>
                        <input id="order-form__client-sif" class="u-full-width" type="text" name="form_text_65" value="<?= empty($arResult['arrVALUES']['form_text_65']) ? '' : $arResult['arrVALUES']['form_text_65']; ?>"/>
                    </div>
                    <div class="two columns">
                        <label for="order-form__client-age">Возраст:</label>
                        <input id="order-form__client-age" class="u-full-width" type="text" name="form_text_66" value="<?= empty($arResult['arrVALUES']['form_text_66']) ? '' : $arResult['arrVALUES']['form_text_66']; ?>">
                    </div>
                    <div class="two columns">
                        <label for="order-form__client-gender">Пол пациента:</label>
                        <select id="order-form__client-gender" class="u-full-width" name="form_dropdown_client_gender">
                            <? foreach ($arResult['QUESTIONS']['client_gender']['STRUCTURE'] as $arOption): ?>
                                <option value="<?= $arOption['ID'] ?>"<?= $arOption['ID'] == $arResult['arrVALUES']['form_dropdown_client_gender'] ? ' selected' : ''; ?>><?= $arOption['MESSAGE']; ?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="six columns">
                        <label for="order-form__delivery-cost" class="order-form__label--truncate">Контактный телефон:</label>
                        <input id="order-form__contact-phone" class="u-full-width" type="text" name="form_text_71" value="<?= empty($arResult['arrVALUES']['form_text_71']) ? '' : $arResult['arrVALUES']['form_text_71']; ?>"/>
                    </div>
                    <div class="six columns">
                        <label for="order-form__comment">Комментарий:</label>
                        <textarea id="order-form__comment" rows="1" class="u-full-width" name="form_textarea_72"><?= empty($arResult['arrVALUES']['form_textarea_72']) ? '' : $arResult['arrVALUES']['form_textarea_72']; ?></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="twelve columns">
                        <label for="order-form__contact-address" class="order-form__label--truncate">Адрес доставки:</label>
                        <textarea id="order-form__contact-address" rows="1" class="u-full-width" name="form_textarea_73"><?= empty($arResult['arrVALUES']['form_textarea_73']) ? '' : $arResult['arrVALUES']['form_textarea_73']; ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <? // #END .order-form__client ?>


        <? // BEGIN .order-form__product
        ?>
        <div class="order-form__product">
            <div class="container">
                <div class="row">
                    <div class="four columns">
                        <label for="order-form__product-model">Модель:</label>
                        <input id="order-form__product-model" class="u-full-width" type="text" name="form_text_74" value="<?= empty($arResult['arrVALUES']['form_text_74']) ? '' : $arResult['arrVALUES']['form_text_74']; ?>"/>
                    </div>
                    <div class="two columns">
                        <label for="order-form__product-side">Сторона:</label>
                        <select id="order-form__product-side" class="u-full-width" name="form_dropdown_product_side">
                            <? foreach ($arResult['QUESTIONS']['product_side']['STRUCTURE'] as $arOption): ?>
                                <option value="<?= $arOption['ID'] ?>"<?= $arOption['ID'] == $arResult['arrVALUES']['form_dropdown_product_side'] ? ' selected' : ''; ?>><?= $arOption['MESSAGE']; ?></option>
                            <? endforeach; ?>
                        </select>
                    </div>

                    <div class="six columns">
                        <label for="order-form__product_sku">Артикул изделия:</label>
                        <input id="order-form__product_sku" class="u-full-width" type="text" name="form_text_79" value="<?= empty($arResult['arrVALUES']['form_text_79']) ? '' : $arResult['arrVALUES']['form_text_79']; ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="four columns">
                        <label for="order-form__client-measurements">Мерки:</label>
                        <input id="order-form__client-measurements" class="u-full-width" type="text"name="form_text_80" value="<?= empty($arResult['arrVALUES']['form_text_80']) ? '' : $arResult['arrVALUES']['form_text_80']; ?>">
                    </div>

                    <div class="two columns">
                        <label for="order-form__client-weight">Рост/Вес:</label>
                        <input id="order-form__client-weight" class="u-full-width" type="text" name="form_text_81" value="<?= empty($arResult['arrVALUES']['form_text_81']) ? '' : $arResult['arrVALUES']['form_text_81']; ?>"/>
                    </div>
                    <div class="six columns">
                        <label for="order-form__product_sku">Размер:</label>
                        <input id="order-form__product_sku" class="u-full-width" type="text" name="form_text_83" value="<?= empty($arResult['arrVALUES']['form_text_83']) ? '' : $arResult['arrVALUES']['form_text_83']; ?>"/>
                    </div>
                </div>
                <div class="row">
                    <div class="four columns">
                        <label for="order-form__full-price" class="order-form__label--truncate">Цена без скидки:</label>
                        <input id="order-form__full-price" class="u-full-width" type="text" name="form_text_84" value="<?= empty($arResult['arrVALUES']['form_text_84']) ? '' : $arResult['arrVALUES']['form_text_84']; ?>">
                    </div>
                    <div class="two columns">
                        <label for="order-form__discount-reason" class="order-form__label--truncate">Обоснование скидки:</label>
                        <select id="order-form__discount-reason" class="u-full-width" name="form_dropdown_discount_reason" data-action="enableElementOnSelect" data-target="#order-form__dicsount-card">
                            <? foreach ($arResult['QUESTIONS']['discount_reason']['STRUCTURE'] as $arOption): ?>
                                <option value="<?= $arOption['ID'] ?>"<?= $arOption['ID'] == $arResult['arrVALUES']['form_dropdown_discount_reason'] ? ' selected' : ''; ?> data-enable="<?= $arOption['ID'] == '91' ? 'true' : 'false' ?>"><?= $arOption['MESSAGE']; ?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                    <div class="two columns">
                        <label for="order-form__dicsount-card" class="order-form__label--truncate">Номер ДК:</label>
                        <input id="order-form__dicsount-card" class="u-full-width" type="text" <?= $arResult['arrVALUES']['form_dropdown_discount_reason'] != '91' ? 'disabled' : ''; ?> name="form_text_96" value="<?= $arResult['arrVALUES']['form_dropdown_discount_reason'] == '91' && !empty($arResult['arrVALUES']['form_text_96']) ? $arResult['arrVALUES']['form_text_96'] : ''; ?>">
                    </div>
                    <div class="four columns">
                        <label for="order-form__discount-value" class="order-form__label--truncate">Цена со скидкой:</label>
                        <input id="order-form__discount-value" class="u-full-width" type="text" name="form_text_89" value="<?= empty($arResult['arrVALUES']['form_text_89']) ? '' : $arResult['arrVALUES']['form_text_89']; ?>">
                    </div>

                </div>
                <div class="row">
                    <div class="two columns">
                        <label class="order-form__label--with-margin">Оплата:</label>
                    </div>
                    <div class="four columns">
                    <select id="order-form__payment_type" class="u-full-width" name="form_dropdown_payment_type">
                        <? foreach ($arResult['QUESTIONS']['payment_type']['STRUCTURE'] as $arOption): ?>
                            <option value="<?= $arOption['ID'] ?>"<?= $arOption['ID'] == $arResult['arrVALUES']['form_dropdown_payment_type'] ? ' selected' : ''; ?>><?= $arOption['MESSAGE']; ?></option>
                        <? endforeach; ?>
                    </select>
                    </div>


                    <div class="three columns">
                        <label for="order-form__delivery-cost" class="order-form__label--truncate">Выезд специалиста:</label>
                    </div>
                    <div class="three columns">
                        <select id="order-form__delivery-cost" class="u-full-width" name="form_dropdown_delivery_price">
                            <? foreach ($arResult['QUESTIONS']['delivery_price']['STRUCTURE'] as $arOption): ?>
                                <option value="<?= $arOption['ID'] ?>"<?= $arOption['ID'] == $arResult['arrVALUES']['form_dropdown_delivery_price'] ? ' selected' : ''; ?>><?= $arOption['MESSAGE']; ?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <? // #END .order-form__product  ?>
    <?endif;?>
        <? // SUMBIT .order-form__product  ?>
        <div class="order-form__submit">
            <div class="container">
                <div class="row">
                    <div class="five columns offset-by-seven">
                        <input  type="hidden" name="form_radio_type" value="<?= empty
                        ($arResult['arrVALUES']['form_radio_type']) ? '' : $arResult['arrVALUES']['form_radio_type']; ?>">

                        <input <?= (intval($arResult["F_RIGHT"]) < 10 ? "disabled=\"disabled\"" : ""); ?> type="submit" name="web_form_submit" value="<?= htmlspecialcharsbx(strlen(trim($arResult["arForm"]["BUTTON"])) <= 0 ? GetMessage("FORM_ADD") : $arResult["arForm"]["BUTTON"]); ?>" class="button-primary u-full-width">
                        <? /* if ($arResult["F_RIGHT"] >= 15): ?>
                          <input type="hidden" name="web_form_apply" value="Y">
                          <input type="submit" name="web_form_apply" value="<?= GetMessage("FORM_APPLY") ?>" class="button-primary u-full-width">
                          <? endif; ?>
                          <input type="reset" value="<?= GetMessage("FORM_RESET"); ?>" class="button-primary u-full-width">
                         */ ?>
                    </div>
                </div>
            </div>
        </div>
        <?= $arResult["FORM_FOOTER"]; ?>
    <? endif; ?>
</div>
<?
// #END .order-form ?>