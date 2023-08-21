<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

//__($arResult);
?>

<? // BEGIN .order-form ?>
<div class="order-form" xmlns="http://www.w3.org/1999/html">

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

        <? // BEGIN .order-form__author ?>
        <div class="order-form__author">
            <div class="container">
                <div class="row">
                    <div class="six columns">
                        <label for="order-form__authorname" class="inline-block" >Автор заявки:</label>

                        <input id="order-form__authorname" type="text" class="u-full-width" name="form_text_94" disabled
                               value="<?=$arResult['sResultCreator']?>"/>
                    </div>
                    <div class="six columns">
                        <label for="order-form__contractor">Исполнитель:</label>

                        <select id="order-form__contractor" class="u-full-width" name="form_text_60">
                            <? foreach ($arResult['QUESTIONS']['contractor']['STRUCTURE'] as $arOption): ?>
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
                        <input id="order-form__call-date" class="u-full-width  datepicker" type="text" name="form_text_52" value="<?= empty($arResult['arrVALUES']['form_text_52']) ? '' : $arResult['arrVALUES']['form_text_52']; ?>">
                    </div>
                    <div class="two columns">
                        <label for="order-form__delivery-date" class="order-form__label--truncate">Дата выезда:</label>
                        <input id="order-form__call-date" class="u-full-width datepicker" type="text"  name="form_text_53" value="<?= empty($arResult['arrVALUES']['form_text_53']) ? '' : $arResult['arrVALUES']['form_text_53']; ?>">
                    </div>
                    <div class="three columns">
                        <label for="order-form__delivery-interval">Время выезда:</label>
                        <select id="order-form__delivery-interval" class="u-full-width" data-action="enableElementOnSelect" data-target="#order-form__delivery-time" name="form_dropdown_delivery_interval">
                            <? foreach ($arResult['QUESTIONS']['delivery_interval']['STRUCTURE'] as $arOption): ?>
                                <option value="<?= $arOption['ID'] ?>"<?= $arOption['ID'] == $arResult['arrVALUES']['form_dropdown_delivery_interval'] ? ' selected' : ''; ?> data-enable="<?= $arOption['ID'] == '58' || $arOption['ID'] == '59' ? 'true' : 'false' ?>"><?= $arOption['MESSAGE']; ?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                    <div class="two columns">
                        <label for="order-form__delivery-time">Время:</label>
                        <input id="order-form__delivery-time" class="u-full-width timepicker" type="text" name="form_text_61" <?= $arResult['arrVALUES']['form_dropdown_delivery_interval'] != '58' || $arResult['arrVALUES']['form_dropdown_delivery_interval'] != '59' ? 'disabled' : ''; ?>  value="<?if (
                            ( $arResult['arrVALUES']['form_dropdown_delivery_interval'] == '58' ||  $arResult['arrVALUES']['form_dropdown_delivery_interval'] == '59') && !empty($arResult['arrVALUES']['form_text_61'])){ echo $arResult['arrVALUES']['form_text_61'];} ?>">
                    </div>
                    <div class="three columns">
                        <label for="order-form__order-status">Статус заявки:</label>
                        <select id="order-form__order-status" class="u-full-width">
                            <option value="4" selected>В работе</option>
                        </select>
                    </div>
                </div>


                <div class="row">
                    <div class="two columns">
                        <label for="order-form__receipt-code">Индекс:</label>
                        <input id="order-form__receipt-code" class="u-full-width" type="text" name="form_text_62" value="<?= empty($arResult['arrVALUES']['form_text_62']) ? '' : $arResult['arrVALUES']['form_text_62']; ?>">
                    </div>
                    <div class="five columns">
                        <label for="order-form__doctor-sif">Ф.И.О.:</label>
                        <input id="order-form__doctor-sif" class="u-full-width" type="text" name="form_text_63" value="<?= empty($arResult['arrVALUES']['form_text_63']) ? '' : $arResult['arrVALUES']['form_text_63']; ?>">
                    </div>
                    <div class="five columns">
                        <label for="order-form__ip">ИП:</label>

                        <select id="order-form__ip" class="u-full-width" data-action="enableElementOnSelect" data-target="#order-form__ip" name="form_dropdown_IP">
                            <?

                             foreach ($arResult['QUESTIONS']['IP']['STRUCTURE'] as $arOption): ?>
                                <option value="<?= $arOption['ID'] ?>"<?= $arOption['ID'] == $arResult['arrVALUES']['form_dropdown_ip'] ? ' selected' : ''; ?>><?= $arOption['MESSAGE']; ?></option>
                            <? endforeach; ?>
                        </select>
                        </select>
                    </div>

                </div>
                <div class="row">
                    <div class="twelve columns">
                        <label for="order-form__contact-address" class="order-form__label--truncate">Адрес выезда:</label>
                        <textarea id="order-form__contact-address" rows="1" class="u-full-width" name="form_textarea_73"><?= empty($arResult['arrVALUES']['form_textarea_73']) ? '' : $arResult['arrVALUES']['form_textarea_73']; ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="twelve columns">
                <b>Отгрузка Товара:</b><br/><br/>
                <textarea name="form_textarea_105" style="display:none;" id="shipment"></textarea>
            </div>
        </div>
        <?$sh = 0;
        while($sh < 12){?>
        <div class="container">
            <div class="row">
                <div class="three columns">
                    <input class="u-full-width shipment_str shipment_str_<?=$sh?>" placeholder="Артикул" type="text" id="shimpent_articul_<?=$sh?>"  value="">
                </div>
                <div class="two columns">
                    <input class="u-full-width shipment_str shipment_str_<?=$sh?>" placeholder="Размер" type="text" id="shimpent_size_<?=$sh?>"  value="">
                </div>
                <div class="two columns">
                    <input class="u-full-width shipment_str shipment_str_<?=$sh?>" placeholder="Цвет" type="text" id="shimpent_color_<?=$sh?>"  value="">
                </div>
                <div class="two columns">
                    <input class="u-full-width shipment_str shipment_str_<?=$sh?>" placeholder="Кол-во" type="number" id="shimpent_quantity_<?=$sh?>"  value="">
                </div>
                <div class="three columns">
                    <input class="u-full-width shipment_str shipment_str_<?=$sh?>" placeholder="Сеть" type="number" id="shimpent_netprice_<?=$sh?>"  value="">
                </div>

            </div>
            <div class="separator--dashed"></div><br/>
        </div>
        <?
        $sh++;
        }?>
        <div class="container">
            <div class="twelve columns">
                <b>ФАКТ:</b><br/><br/>
                <textarea name="form_textarea_106" style="display:none;" id="getting"></textarea>
            </div>
        </div>
        <?$sh = 0;
        while($sh < 12){?>
        <div class="container">
            <div class="row">
                <div class="three columns">
                    <input class="u-full-width getting_str" placeholder="Артикул" type="text" id="getting_articul_<?=$sh?>"  value="">
                </div>
                <div class="two columns">
                    <input class="u-full-width getting_str" placeholder="Размер" type="text" id="getting_size_<?=$sh?>"  value="">
                </div>
                <div class="two columns">
                    <input class="u-full-width getting_str" placeholder="Цвет" type="text" id="getting_color_<?=$sh?>"  value="">
                </div>
                <div class="two columns">
                    <input class="u-full-width getting_str" placeholder="Кол-во" type="number" id="getting_quantity_<?=$sh?>"  value="">
                </div>
                <div class="three columns">
                    <input class="u-full-width getting_str" placeholder="Сеть" type="number" id="getting_netprice_<?=$sh?>"  value="">
                </div>
            </div>
            <div class="separator--dashed"></div><br/>
        </div>
        <?
        $sh++;
        }?>

        <? // SUMBIT .order-form__product  ?>
        <div class="order-form__submit">
            <div class="container">
                <div class="row">
                    <div class="five columns offset-by-seven">

                        <input type="hidden" name="form_radio_type" value="104"/>

                        <input <?= (intval($arResult["F_RIGHT"]) < 10 ? "disabled=\"disabled\"" : ""); ?> type="submit" name="web_form_submit" id="web_form_submit_but" value="<?= htmlspecialcharsbx(strlen(trim($arResult["arForm"]["BUTTON"])) <= 0 ? GetMessage("FORM_ADD") : $arResult["arForm"]["BUTTON"]); ?>" class="button-primary u-full-width">
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
