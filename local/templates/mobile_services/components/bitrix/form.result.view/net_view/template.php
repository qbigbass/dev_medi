<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
/*echo "<pre>";
print_r( $arResult['arrVALUES']);
echo "</pre>";*/
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
    <div class="order-form__author print-hidden ">
        <div class="container">
            <div class="row">
                <div class="six columns">
                    <label for="order-form__authorname" class="inline-block" >Автор заявки:</label>
                    <input id="order-form__authorname" type="text" class="u-full-width" name="form_text_0" disabled
                           value="<?=$arResult['sResultCreator']?>"/>
                </div>
                <div class="six columns">
                    <label for="order-form__contractor">Исполнитель:</label>

                    <input id="order-form__contractor" type="text" class="u-full-width" name="form_text_60" disabled value="<?= $arResult['RESULT']['contractor']['ANSWER_VALUE'][0]['USER_TEXT'] ?>"/>

                </div>
            </div>
        </div>
    </div>



    <div class="order-form__main-info">
            <div class="container">
                <div class="row">
                    <div class="two columns">
                        <label for="order-form__call-date" class="order-form__label--truncate">Дата звонка:</label>
                        <input id="order-form__call-date" class="u-full-width  datepicker" type="text" name="form_text_76" value="<?= $arResult['RESULT']['call_date']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                    <div class="two columns">
                        <label for="order-form__delivery-date" class="order-form__label--truncate">Дата выезда:</label>
                        <input id="order-form__delivery-date" class="u-full-width datepicker" type="text"  name="form_text_77" value="<?= $arResult['RESULT']['delivery_date']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                    <div class="three columns">
                        <label for="order-form__delivery-interval" class="order-form__label--truncate">Время выезда:</label>
                        <input type="text" id="order-form__delivery-interval" class="u-full-width" name="form_dropdown_delivery_interval" value="<?= $arResult['RESULT']['delivery_interval']['ANSWER_VALUE'][0]['ANSWER_TEXT']; ?>" disabled>
                    </div>
                    <div class="two columns">
                        <label for="order-form__delivery-time" class="order-form__label--truncate">Время:</label>
                        <input id="order-form__delivery-time" class="u-full-width timepicker" type="text" name="form_text_83" value="<?= $arResult['RESULT']['delivery_time']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                    <div class="three columns">
                        <label for="order-form__order-status">Статус заявки:</label>
                        <input type="text" id="order-form__client-status" class="u-full-width" value="<?= $arResult['RESULT_STATUS_TITLE']; ?>" disabled>
                    </div>
                </div>


                <div class="row">
                    <div class="two columns">
                        <label for="order-form__receipt-code">Индекс:</label>
                        <input id="order-form__receipt-code" class="u-full-width" type="text" name="form_text_84" value="<?= $arResult['RESULT']['recipe_number']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                    <div class="five columns">
                        <label for="order-form__doctor-sif">Ф.И.О.:</label>
                        <input id="order-form__doctor-sif" class="u-full-width" type="text" name="form_text_85" value="<?= $arResult['RESULT']['doctor_name']['ANSWER_VALUE'][0]['USER_TEXT']; ?>" disabled>
                    </div>
                    <div class="five columns">

                            <label for="order-form__ip">ИП:</label>
<?__($arResult['RESULT'])?>
                            <input id="order-form__ip" type="text" class="u-full-width" name="form_text_60" disabled value="<?= $arResult['RESULT']['IP']['ANSWER_VALUE'][0]['ANSWER_TEXT'] ?>"/>
                    </div>
                </div>
                <div class="row">
                    <div class="twelve columns">
                        <label for="order-form__client_address" class="order-form__label--truncate">Адрес выезда:</label>
                        <textarea id="order-form__client_address" rows="1" class="u-full-width" name="form_textarea_95" disabled><?= $arResult['RESULT']['client_address']['ANSWER_VALUE'][0]['USER_TEXT']; ?></textarea>
                    </div>
                </div>
            </div>
        </div>




        <? // = $arResult["FORM_FOOTER"]; ?>
    <? endif; ?>
</div>
<div class="separator--dashed print-only"></div>
<div class="additional-form ">
    <div class="container">

        <?// net form
        if (isset($arResult['arrVALUES'][75][104])):?>
        <div class="row">
            <b>Отгрузка Товара:</b><br/>
            <table class="additional-form__table net u-full-width u-max-full-width">
                <tr>
                    <th>Модель</th><th>Размер</th><th>Цвет</th><th>Кол-во</th><th>Сеть</th>
                </tr>
                <?if (!empty($arResult['SHIPMENTS'])){
                    foreach($arResult['SHIPMENTS'] as $sh => $val){
                        foreach ($val as $k => $value) {
                            if (empty($value))
                            {
                                $val[$k] = '&nbsp;';
                            }
                        }
                        ?>
                        <tr>
                            <td><?=$val[0]?></td><td><?=$val[1]?></td><td><?=$val[2]?></td><td><?=$val[3]?></td><td><?=$val[4]?></td>
                        </tr>
                        <?
                    }
                }else{?>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <?}?>
            </table>
        </div>
        <div class="row">
            <b>ФАКТ:</b><br/>
            <table class="additional-form__table net u-full-width u-max-full-width">
                <tr>
                    <th>Модель</th><th>Размер</th><th>Цвет</th><th>Кол-во</th><th>Сеть</th>
                </tr>
                <?if (!empty($arResult['GETTING'])){
                    foreach($arResult['GETTING'] as $sh => $val){
                        foreach ($val as $k => $value) {
                            if (empty($value))
                            {
                                $val[$k] = '&nbsp;';
                            }
                        }
                        ?>
                        <tr>
                            <td><?=$val[0]?></td><td><?=$val[1]?></td><td><?=$val[2]?></td><td><?=$val[3]?></td><td><?=$val[4]?></td>
                        </tr>
                        <?
                    }
                }else{?>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
                <?}?>
            </table>
        </div>
    <?endif;?>
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
<?
// #END .order-form ?>
