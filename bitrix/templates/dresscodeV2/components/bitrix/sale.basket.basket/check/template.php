<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

if (!empty($arResult['ITEMS'])) {
    $allCount = 0;
    $allDiscount = 0; ?>
    <div class="table-simple-wrap">
        <table class="table-simple">
            <tr>
                <th>Наименование</th>
                <th>Количество</th>
                <th>Цена</th>
                <th>Скидка</th>
                <th>Сумма</th>
            </tr>
            <!--	BASKET ITEMS BLOCK	-->
            <? $aviability = true; ?>
            <? foreach ($arResult['ITEMS'] as $arItem): ?>
                <tr class="<? if ($arItem['STORE_AMOUNT']['AMOUNT'] <= 0) { ?>not_available<? } ?>">
                    <td class="name_content "><span class="ff-medium"><?= $arItem['NAME'] ?></span>
                        <? if ($arItem['STORE_AMOUNT']['AMOUNT'] <= 0) {
                            $aviability = false; ?>
                            <span class="not_available">нет в наличии в салоне</span>
                        <? } ?>
                        <br>
                        <span class=""><? if (!empty($arItem['PROPS'])): ?>
                                <div class="basket-properties">
                                    <? foreach ($arItem['PROPS'] as $arProp):
                                        if ($arProp['CODE'] != 'DISCOUNT_NAME') continue; ?>
                                        <?= $arProp['NAME'] ?>
                                        <?= $arProp['VALUE'] ?>
                                        <br>
                                    <? endforeach; ?>
                                </div>
                            <? endif; ?>
                            </span>
                        <span class="mtz_item" data-cons-id="<?= $arItem['ID'] ?>">Добавить консультанта</span>
                        <input type="search" maxlength="4" name="precheck[itemcons][<?= $arItem['ID'] ?>]"
                               class="itemcons<?= $arItem['ID'] ?> itemcons save_field"
                               value="<?= ($_SESSION['precheck_data']['itemcons'][$arItem['ID']] ?
                                   $_SESSION['precheck_data']['itemcons'][$arItem['ID']] : "") ?>"
                            <?= ($_SESSION['precheck_data']['itemcons'][$arItem['ID']] ?
                                "" : "style='display:none;'") ?>
                        />
                    </td>
                    <td><?= $arItem['QUANTITY'];
                        $allCount += $arItem['QUANTITY'] ?>&nbsp;шт.
                    </td>
                    <td><?= number_format($arItem['BASE_PRICE'], 0, ".", "&nbsp;")
                        ?>&nbsp;руб.
                    </td>
                    <td><?
                        $discount = $arItem['BASE_PRICE'] - $arItem['PRICE'];
                        echo($discount > 0 ? '-' : '');
                        if ($discount > 0) {
                            echo number_format($discount, 0, ".", "&nbsp;");
                        }
                        echo($discount > 0 ? '&nbsp;руб.' : '');
                        $allDiscount += ($arItem['BASE_PRICE'] - $arItem['PRICE']) * $arItem['QUANTITY'];
                        ?>
                    </td>
                    <td><?= $arItem['SUM'] ?>
                    </td>
                </tr>
                <? //echo "<pre>";print_r($arItem);echo "</pre>";
                ?>
            <? endforeach; ?>
            <tr>
                <td class="text-right ff-medium">Итого:</td>
                <td class="ff-medium"><?= $allCount; ?>&nbsp;шт.</td>
                <td class="ff-medium"><?= number_format($arResult['BASKET_SUM'], 0, ".", "&nbsp;")
                    ?>&nbsp;руб.
                </td>
                <td class="ff-medium"><?= number_format($allDiscount, 0, ".", "&nbsp;")
                    ?>&nbsp;руб.
                </td>
                <td class="ff-medium" style="font-size: 110%;"><?= $arResult['allSum_FORMATED'] ?> </td>

            </tr>
        </table>
        <input type="hidden" name="aviability" id="aviability" value="<?= intval($aviability) ?>">
    </div>
    
    <?php
}
