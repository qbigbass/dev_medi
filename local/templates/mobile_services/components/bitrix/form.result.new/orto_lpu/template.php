<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

//__($arResult);
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

        <? // BEGIN .order-form__author ?>
        <div class="order-form__author">
            <div class="container">
                <div class="row">
                    <div class="six columns">
                        <label for="order-form__authorname" class="inline-block" >Автор заявки:</label>

                        <input id="order-form__authorname" type="text" class="u-full-width"   disabled
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
                    <div class="four columns">
                        <label for="order-form__visit-date">Дата визита:</label>
                        <input id="order-form__visit-date" class="u-full-width  datepicker" type="text" name="form_text_101" value="<?= empty($arResult['arrVALUES']['form_text_101']) ? '' : $arResult['arrVALUES']['form_text_101']; ?>">
                    </div>

                    <div class="four columns">
                        <label for="order-form__title">Название ЛПУ:</label>
                        <input id="order-form__title" class="u-full-width " type="text" name="form_text_99" value="<?= empty($arResult['arrVALUES']['form_text_99']) ? '' : $arResult['arrVALUES']['form_text_99']; ?>">
                    </div>
                    <div class="four columns">
                        <label for="order-form__address">Адрес ЛПУ:</label>
                        <input id="order-form__address" class="u-full-width " type="text" name="form_text_100" value="<?= empty($arResult['arrVALUES']['form_text_100']) ? '' : $arResult['arrVALUES']['form_text_100']; ?>">
                    </div>
                </div>


                <div class="row">
                    <div class="twelwe columns">
                        <label for="order-form__comment">Комментарий:</label>
                        <textarea id="order-form__comment" rows="1" class="u-full-width" name="form_textarea_72"><?= empty($arResult['arrVALUES']['form_textarea_72']) ? '' : $arResult['arrVALUES']['form_textarea_72']; ?></textarea>

                    </div>

                </div>
            </div>
        </div>



        <? // SUMBIT .order-form__product  ?>
        <div class="order-form__submit">
            <div class="container">
                <div class="row">
                    <div class="five columns offset-by-seven">

                        <input type="hidden" name="form_radio_type" value="103"/>

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