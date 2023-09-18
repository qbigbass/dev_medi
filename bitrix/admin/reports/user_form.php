<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?><!DOCTYPE HTML PUBLIC
        "-//W3C//DTD HTML 4.0 Transitional//EN">
<? CModule::IncludeModule('iblock'); ?>
<html xmlns:v="urn:schemas-microsoft-com:vml"
      xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:w="urn:schemas-microsoft-com:office:word"
      xmlns="http://www.w3.org/TR/REC-html40">

<head>
    <meta http-equiv=Content-Type content="text/html; charset=<?= LANG_CHARSET ?>">
    <title langs="ru">Простой бланк заказа</title>
    <style>
        <!--
        td {
            box-sizing: border-box;
        }

        .td1 {
            width: 120px;
        }

        .td2 {
            width: 75px;
        }

        .td3 {
            width: 350px;
        }

        .td4 {
            width: 90px;
        }

        .td6 {
            width: 60px;
        }

        .td7 {
            width: 60px;
        }

        .product-table td {
            padding: 6px;
            border-bottom: 1px solid #000;
            border-left: 1px solid #000;
        }

        .blank {
            display: block;
            border-bottom: 1px solid #000;
        }

        @media print {
            .no-print, .no-print * {
                display: none !important;
            }
        }

        -->
    </style>
</head>

<body bgcolor=white lang=RU style='font-size: 12px; font-family: Arial, sans-serif;'>
<?
$page = IntVal($page);
if ($page <= 0)
    $page = 1;

//        global $USER;
//        if($USER->IsAdmin()) {
//            print_r($arOrderProps);
//        }
?>

<table align="center" border="0" cellpadding="0" cellspacing="0" style="font-size: 16px;">
    <tbody>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>
            <img src="/bitrix/templates/dresscodeV2/images/logo_print.png" width="70" alt="medi">
        </td>
        <td colspan="5" style="height: 24px; font-size: 24px;">Заказ №<?= $arOrder["ACCOUNT_NUMBER"]; ?> на доставку
            [www.medi-salon.ru]
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td class="td2" style="height: 24px; font-weight: bold;">
            Оператор:
        </td>
        <td class="td1">
            <div class="blank"><?= $arOrderProps['MANAGER'] ? $arOrderProps['MANAGER'] : '&nbsp;'; ?></div>
        </td>
        <td class="td2" style="height: 24px; font-weight: bold;">
            &nbsp;&nbsp;&nbsp;Источник:
        </td>
        <td class="td1" style="text-align: center;">
            <div class="blank">
                <?= $arOrderProps['ORDER_REF'] ? $arOrderProps['ORDER_REF'] : '&nbsp;'; ?>
            </div>
        </td>
        <td class="td2" style="height: 24px; font-weight: bold;">
            &nbsp;&nbsp;&nbsp;Дата:
        </td>
        <td class="td1" style="text-align: center;">
            <div class="blank">
                <?= $arOrder['DATE_INSERT_FORMAT'] ? $arOrder['DATE_INSERT_FORMAT'] : '&nbsp;'; ?>
            </div>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td style="height: 24px; font-weight: bold;" class="td1">ФИО:</td>
        <td colspan="5">
            <div class="blank"><?= $arOrderProps['FIO'] ? $arOrderProps['FIO'] : '&nbsp;'; ?></div>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td style="height: 24px; font-weight: bold;" class="td1">Телефон:</td>
        <td colspan="2">
            <div class="blank"><?= $arOrderProps['PHONE'] ? $arOrderProps['PHONE'] : '&nbsp;'; ?></div>
        </td>
        <td style="height: 24px; font-weight: bold;" class="td1">&nbsp;&nbsp;&nbsp;E-mail:</td>
        <td colspan="2" style="text-align:center;">
            <div class="blank"><?= $arOrderProps['EMAIL'] ? $arOrderProps['EMAIL'] : '&nbsp;'; ?></div>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td style="height: 24px; font-weight: bold;" class="td1">Скидка:</td>
        <td colspan="5">
            <div class="blank"><?= $arOrderProps['DISCOUNT_TYPE'] ? $arOrderProps['DISCOUNT_TYPE'] : '&nbsp;'; ?></div>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td style="height: 24px; font-weight: bold;" class="td1">Карта:</td>
        <td colspan="3">
            <div class="blank"><?= $arOrderProps['DC_NUMBER'] ? $arOrderProps['DC_NUMBER'] : '&nbsp;'; ?></div>
        </td>
        <td style="height: 24px; font-weight: bold;" class="td1">&nbsp;&nbsp;&nbsp;Рецепт:</td>
        <td>
            <div class="blank"><?= $arOrderProps['R_NUMBER'] ? $arOrderProps['R_NUMBER'] : '&nbsp;'; ?></div>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <?
    CModule::IncludeModule('sale');
    $sDeliveryName = '';
    
    list($delivery_code, $profile_code) = explode(':', $arOrder['DELIVERY_ID']);
    
    $delivFilter = ["SITE_ID" => $arOrder['LID']];
    if (intval($arOrder['DELIVERY_ID']) > 0) {
        $delivFilter['SID'] = $arOrder['DELIVERY_ID'];
    } else {
        $delivFilter["SID"] = $delivery_code;
    }
    $dbDelivery = CSaleDeliveryHandler::GetList([], $delivFilter);
    
    if ($arDelivery = $dbDelivery->GetNext()) {
        
        if ($arDelivery['SID'] == 'new26') {
            $sDeliveryName .= "Почта России, ";
        }
        
        $sDeliveryName .= $arDelivery['NAME'];
        
        if ($profile_code && trim($arDelivery['PROFILES'][$profile_code]) != '') {
            $sDeliveryName .= ', ' . $arDelivery['PROFILES'][$profile_code]['TITLE'];
        }
        if ($arDelivery['SID'] == 'sdek') {
            $sDeliveryName .= ', ' . $arDelivery['PROFILES'][$profile_code]['TITLE'];
        }
        if ($arDelivery['SID'] == 'boxberry') {
            $sDeliveryName .= ', ' . $arDelivery['PROFILES'][$profile_code]['TITLE'];
        }
    } else {
        if (intval($arOrder['DELIVERY_ID']) > 0) {
            $delivFilter['ID'] = $arOrder['DELIVERY_ID'];
        }
        $dbDelivery = CSaleDelivery::GetList([], $delivFilter);
        
        if ($arDelivery = $dbDelivery->GetNext()) {
            $sDeliveryName .= $arDelivery['NAME'];
            
            if ($profile_code && trim($arDelivery['PROFILES'][$profile_code]) != '') {
                $sDeliveryName .= ', ' . $arDelivery['PROFILES'][$profile_code]['TITLE'];
            }
        }
        
    }
    ?>
    <tr>
        <td style="height: 24px; font-weight: bold;" class="td1">Доставка:</td>
        <td colspan="3">
            <div class="blank">
                <?= $sDeliveryName ? $sDeliveryName : '&nbsp;'; ?>
                <? ?>
            </div>
        </td>
        <td style="height: 24px; font-weight: bold;" class="td1">&nbsp;&nbsp;&nbsp;На дату:</td>
        <td>
            <div class="blank"
                 style="text-align: center;"><?= $arOrderProps['DELIVERY_PLANNED'] ? $arOrderProps['DELIVERY_PLANNED'] : '&nbsp;'; ?>
                <br/>
                <?= $arOrderProps['DELIVERY_FROM'] ? 'с ' . $arOrderProps['DELIVERY_FROM'] : '&nbsp;'; ?>
                <?= $arOrderProps['DELIVERY_TO'] ? 'до ' . $arOrderProps['DELIVERY_TO'] : '&nbsp;'; ?>
            </div>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <?
    if (!empty($arOrder['PAY_SYSTEM_ID'])) {
        $arPaySystem = CSalePaySystem::GetByID($arOrder['PAY_SYSTEM_ID']);
        $sPaySystemName = ($arPaySystem['NAME'] ? $arPaySystem['NAME'] : '&nbsp;');
        if ($arOrder['PAY_SYSTEM_ID'] == 12)
            $sPaySystemName = 'Банковской картой онлайн';
    }
    ?>

    <tr>
        <td style="height: 24px; font-weight: bold;" class="td1">Оплата:</td>
        <td colspan="3">
            <div class="blank">
                <?= $sPaySystemName ? $sPaySystemName : '&nbsp;'; ?>
            </div>
        </td>
        <td style="height: 24px; font-weight: bold;" class="td1">&nbsp;&nbsp;&nbsp;№ резерва:</td>
        <td>
            <div class="blank">&nbsp;<?= $arOrderProps['RESERV_NUMBER']; ?></div>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td style="height: 24px; font-weight: bold;" class="td1" valign="top">Адрес:</td>
        <td colspan="5">
            <div class="blank">
                <?= $arOrderProps['ZIP'] ? $arOrderProps['ZIP'] . ' / индекс предварительно' : '&nbsp;'; ?>
            </div>
            <div class="blank">
                <?= $arOrderProps['LOCATION'] ? $arOrderProps['LOCATION'] : '&nbsp;'; ?>
            </div>
            <?= $arOrderProps['METRO'] ? "<div class='blank'>м. " . $arOrderProps['METRO'] . "</div>" : ''; ?>

            <div class="blank">
                <?= $arOrderProps['STREET'] ? $arOrderProps['STREET'] : '&nbsp;'; ?>
                <?= $arOrderProps['HOUSE'] ? ", д. " . $arOrderProps['HOUSE'] : '&nbsp;'; ?>
                <?= $arOrderProps['ENTRANCE'] ? ", подъезд " . $arOrderProps['ENTRANCE'] : '&nbsp;'; ?>
                <?= $arOrderProps['FLOOR'] ? ", этаж " . $arOrderProps['FLOOR'] : '&nbsp;'; ?>
                <?= $arOrderProps['FLAT'] ? ", кв. " . $arOrderProps['FLAT'] : '&nbsp;'; ?>
                <?= $arOrderProps['ADDRESS'] ? "<br /> " . $arOrderProps['ADDRESS'] : '&nbsp;'; ?>
                <?= $arOrderProps['ADDRESS_INFO'] ? ",  " . $arOrderProps['ADDRESS_INFO'] : '&nbsp;'; ?>
            </div>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <?
    $db_props = CSaleOrderPropsValue::GetOrderProps($arOrder['ID']);
    $delivery_tasks = '';
    $del_tasks = [];
    while ($arProps = $db_props->Fetch()) {
        if ($arProps['CODE'] == 'DELIV_TASKS') {
            $curVal = unserialize($arProps["VALUE"]);
            
            for ($i = 0; $i < count($curVal); $i++) {
                $arVal = CSaleOrderPropsVariant::GetByValue($arProps["ORDER_PROPS_ID"], $curVal[$i]);
                if ($i > 0) $delivery_tasks .= ", ";
                if ($arVal["NAME"] != 'Нет')
                    
                    $delivery_tasks .= htmlspecialchars($arVal["NAME"]);
            }
            $delivery_tasks_name = $arProps['NAME'];
            
        }
    }
    if ($delivery_tasks != '') {
        ?>
        <tr>
            <td style="height: 24px; font-weight: bold;" class="td1" valign="top"><?= $delivery_tasks_name ?>:</td>
            <td colspan="5" style="vertical-align: top;">
                <div style="max-width: 555px;">
                    <?= $delivery_tasks; ?>
                </div>
            </td>
        </tr>
    <? } ?>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td style="height: 24px; font-weight: bold;" class="td1" valign="top">Комментарий клиента:</td>
        <td colspan="5" style="vertical-align: top;">
            <div style="max-width: 555px;">
                <?= $arOrder['USER_DESCRIPTION'] ? $arOrder['USER_DESCRIPTION'] : '&nbsp;'; ?>
            </div>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td style="height: 24px; font-weight: bold;" class="td1" valign="top">Комментарий менеджера:</td>
        <td colspan="5" style="vertical-align: top;">
            <div style="max-width: 555px;">
                <?= $arOrder['COMMENTS'] ? $arOrder['COMMENTS'] : '&nbsp;'; ?>
            </div>
        </td>
    </tr>
    </tbody>
</table>

<br><br>
<?
//print_r($arOrder);
?>

<br><br>
<? if (count($arBasketIDs) > 0): ?>
    
    <?
    $priceTotal = 0;
    $bUseVat = false;
    $arBasketOrder = array();
    for ($i = 0, $countBasketIds = count($arBasketIDs); $i < $countBasketIds; $i++) {
        $arBasketTmp = CSaleBasket::GetByID($arBasketIDs[$i]);
        
        if (floatval($arBasketTmp["VAT_RATE"]) > 0)
            $bUseVat = true;
        
        $priceTotal += $arBasketTmp["PRICE"] * $arBasketTmp["QUANTITY"];
        
        $arBasketTmp["PROPS"] = array();
        
        $dbBasketProps = CSaleBasket::GetPropsList(
            array("SORT" => "ASC", "NAME" => "ASC"), array("BASKET_ID" => $arBasketTmp["ID"]), false, false, array("ID", "BASKET_ID", "NAME", "VALUE", "CODE", "SORT")
        );
        while ($arBasketProps = $dbBasketProps->GetNext())
            $arBasketTmp["PROPS"][$arBasketProps["ID"]] = $arBasketProps;
        
        // Добавим SKU_NAME
        $arBasketTmp['PRODUCT_CODE'] = '';
        
        if ($arBasketTmp['PRODUCT_ID'] > 0) {
            $rsElement = CIBlockElement::GetByID($arBasketTmp['PRODUCT_ID']);
            
            $dbPropertySKUName = CIBlockElement::GetProperty(CIBlockElement::GetIBlockByID($arBasketTmp['PRODUCT_ID']), $arBasketTmp['PRODUCT_ID'], array("sort" => "asc"), array("CODE" => "CML2_ARTICLE"));
            if ($arPropertySKUName = $dbPropertySKUName->Fetch()) {
                
                
                if ($arPropertySKUName['VALUE']) {
                    $arBasketTmp['PRODUCT_CODE'] = $arPropertySKUName['VALUE'];
                }
            } else if ($arElement = $rsElement->GetNext()) {
                
                if ($arElement['CODE']) {
                    $arBasketTmp['PRODUCT_CODE'] = $arElement['CODE'];
                }
            }
        } else {
            $art = preg_match_all('/\((.{5,18})\)$/u', $arBasketTmp['NAME'], $parts);
            if (!empty($parts[1]))
                $arBasketTmp['PRODUCT_CODE'] = $parts[1][0];
            
            
        }
        
        
        $arBasketOrder[] = $arBasketTmp;
    }


//разбрасываем скидку на заказ по товарам
    if (floatval($arOrder["DISCOUNT_VALUE"]) > 0) {
        $arBasketOrder = GetUniformDestribution($arBasketOrder, $arOrder["DISCOUNT_VALUE"], $priceTotal);
    }

//налоги
    $arTaxList = array();
    $db_tax_list = CSaleOrderTax::GetList(array("APPLY_ORDER" => "ASC"), array("ORDER_ID" => $ORDER_ID));
    $iNds = -1;
    $i = 0;
    while ($ar_tax_list = $db_tax_list->Fetch()) {
        $arTaxList[$i] = $ar_tax_list;
        // определяем, какой из налогов - НДС
        // НДС должен иметь код NDS, либо необходимо перенести этот шаблон
        // в каталог пользовательских шаблонов и исправить
        if ($arTaxList[$i]["CODE"] == "NDS")
            $iNds = $i;
        $i++;
    }
    
    
    $i = 0;
    $total_sum = 0;
    ?>

    <table class="product-table" align="center" border="0" cellpadding="0" cellspacing="0">
        <tbody>
        <tr valign="top" style="font-size: 12px; font-weight: bold;">
            <td class="td4" style="border-left: 1px solid transparent;">Наим.</td>
            <td class="td3">Название изделия</td>
            <td class="td2" style="text-align: right;">Цена<br><small>(без скидки)</small></td>
            <td class="td6" style="text-align: right;">Скидка</td>
            <td class="td7">Кол-во</td>
            <td class="td2" style="text-align: right;">Сумма</td>
        </tr>
        <?
        $total_sum = 0;
        $total_sum_without_discount = 0;
        ?>
        <? foreach ($arBasketOrder as $arBasket):
            
            _c($arBasket);
            if ($arBasket['CUSTOM_PRICE'] == 'N') {
                if (intval($arBasket["DISCOUNT_PRICE"]) == 0 && $arBasket["PRICE"] != $arBasket["BASE_PRICE"]) {
                    $full_price = number_format(ceil($arBasket["BASE_PRICE"]), 2, ',', ' ');
                    $discount_price = round(($arBasket["BASE_PRICE"] - $arBasket["PRICE"]) * 100 / (($arBasket["BASE_PRICE"] - $arBasket["PRICE"]) + $arBasket["PRICE"])) . "%";
                } else {
                    $full_price = number_format(ceil($arBasket["DISCOUNT_PRICE"] + $arBasket["PRICE"]), 2, ',', ' ');
                    $discount_price = round($arBasket["DISCOUNT_PRICE"] * 100 / ($arBasket["DISCOUNT_PRICE"] + $arBasket["PRICE"])) . "%";
                }
            } else {
                if (intval($arBasket["DISCOUNT_PRICE"]) == 0 && $arBasket["PRICE"] != $arBasket["BASE_PRICE"]) {
                    $full_price = number_format(ceil($arBasket["BASE_PRICE"]), 2, ',', ' ');
                    $discount_price = round(($arBasket["BASE_PRICE"] - $arBasket["PRICE"]) * 100 / (($arBasket["BASE_PRICE"] - $arBasket["PRICE"]) + $arBasket["PRICE"])) . "%";
                } else {
                    $full_price = number_format(ceil($arBasket["DISCOUNT_PRICE"] + $arBasket["PRICE"]), 2, ',', ' ');
                    $discount_price = round(($arBasket["BASE_PRICE"] - $arBasket["PRICE"]) * 100 / (($arBasket["BASE_PRICE"] - $arBasket["PRICE"]) + $arBasket["PRICE"])) . "%";
                }
            }
            //print_r($arBasket);
            ?>
            <tr valign="top" style="font-size: 12px;">
                <td class="td4" style="border-left: 1px solid transparent;">
                    <?= $arBasket['PRODUCT_CODE'] ? $arBasket['PRODUCT_CODE'] : '&nbsp;'; ?>
                </td>
                <td class="td3">
                    <?= htmlspecialcharsbx($arBasket["NAME"]);
                    ?>
                    <br/>
                    
                    <?
                    foreach ($arBasket['PROPS'] as $key => $prop) {
                        if (!in_array($prop['NAME'], array("Catalog XML_ID", "Product XML_ID", "Складская программа", "Товарный артикул"))) {
                            echo $prop['NAME'] . " - " . $prop['VALUE'] . " | ";
                        }
                    } ?>
                </td>
                <td class="td7" style="text-align: right; white-space: nowrap;"><?= $full_price ?></td>
                <td class="td2" style="text-align: right; white-space: nowrap;">
                    <? //= number_format($arBasket["DISCOUNT_PRICE"], 2, ',', ' ');
                    ?>
                    <?= $discount_price ?>
                </td>
                <td class="td6"><?= intval($arBasket['QUANTITY']); ?></td>
                <td class="td2" style="text-align: right; white-space: nowrap;">
                    <? //= number_format($arBasket["PRICE"] * $arBasket['QUANTITY'][$i], 2, ',', ' ');
                    ?>
                    <?= number_format(ceil($arBasket["PRICE"] * $arBasket['QUANTITY']), 2, ',', ' '); ?>

                </td>
            </tr>
            <?
            $total_sum += $arBasket["PRICE"] * $arBasket['QUANTITY'];
            // __($arOrder["PRICE_DELIVERY"]);
            $total_sum_without_discount += ($arBasket["PRICE"] + $arBasket["DISCOUNT_PRICE"]) * $arBasket['QUANTITY'];
            ?>
        <? endforeach; ?>
        
        <? /*                    <tr valign="top" style="font-size: 12px;">
                      <td colspan="5" style="border-left: 1px solid transparent; text-align: right;">Скидка на заказ:</td>
                      <td style="text-align: right;">
                      <?= number_format($arOrder["DISCOUNT_VALUE"], 2, ',', ' '); ?>
                      </td>
                      </tr>
                     */ ?>
        <? $total_sum_without_discount += floor($arOrder["PRICE_DELIVERY"]); ?>
        <tr valign="top" style="font-size: 12px;">
            <td colspan="5" style="border-left: 1px solid transparent; text-align: right;">Итого (со скидкой):</td>
            <td style="text-align: right;">
                <?= number_format(ceil($total_sum), 2, ',', ' '); ?>
            </td>
        </tr>
        <? $total_sum += floor($arOrder["PRICE_DELIVERY"]); ?>
        <tr valign="top" style="font-size: 12px;">
            <td colspan="5" style="border-left: 1px solid transparent; text-align: right;">Доставка:</td>
            <td style="text-align: right;">
                <?= number_format(floor($arOrder["PRICE_DELIVERY"]), 0, ',', ' '); ?>
            </td>
        </tr>
        <tr valign="top" style="font-size: 12px;">
            <td colspan="5"
                style=" border-bottom: 1px solid transparent; border-left: 1px solid transparent; text-align: right; background-color: #ddd; font-weight: bold;">
                Всего:
            </td>
            <td style="background-color: #ddd; text-align: right; border-bottom: 1px solid transparent;">
                <?= number_format(ceil($total_sum), 2, ',', ' '); ?>
            </td>
        </tr>
        </tbody>
        <? // $total_sum_without_discount += $arOrder["PRICE_DELIVERY"]; ?> </table>

    <div style="page-break-after: always; clear: both;"><br><br><br></div>
    
    
    <?
    /*
     *  Габаритные размеры изделий
     */
    ?>

    <table align="center" border="0" cellpadding="0" cellspacing="0" style="font-size: 16px;" width="745"
           style="width:745px;">
        <tbody>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td style="width: 128px;">
                <img src="/bitrix/templates/dresscodeV2/images/logo_print.png" width="70" alt="">
            </td>
            <td colspan="5" style="height: 24px; font-size: 24px; width: 617px;">Заказ
                №<?= $arOrder["ACCOUNT_NUMBER"]; ?> / Габаритные размеры
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        </tbody>
    </table>
    <table class="product-table" align="center" border="0" cellpadding="0" cellspacing="0" width="745">
        <tbody>
        <tr valign="top" style="font-size: 12px; font-weight: bold;">
            <td class="td4" style="border-left: 1px solid transparent;">Наим.</td>
            <td class="td3">Название изделия</td>
            <td class="td2" style="text-align: right;">ДхШхВ<br><small>(мм)</small></td>
            <td class="td7" style="text-align: right;">Кол-во<br><small>(шт)</small></td>
            <td class="td7" style="text-align: right;">Вес<br><small>(гр)</small></td>
        </tr>
        <?
        $total_weight = 0.0;
        ?>
        <? foreach ($arBasketOrder as $arBasket): ?>
            <?
            $arBasketItemDemensions = array();
            if (!empty($arBasket['DIMENSIONS'])) {
                $arBasketItemDemensions = unserialize(unserialize($arBasket['DIMENSIONS']));
            } else {
                $arBasketItemDemensions = array('LENGTH' => 0, "WIDTH" => 0, 'HEIGHT' => 0);
            }
            ?>
            <tr valign="top" style="font-size: 12px;">
                <td class="td4" style="border-left: 1px solid transparent;">
                    <?= $arBasket['PRODUCT_CODE'] ? $arBasket['PRODUCT_CODE'] : '&nbsp;'; ?>
                </td>
                <td class="td3">
                    <?= htmlspecialcharsbx($arBasket["NAME"]); ?>
                </td>
                <td style="text-align: right; white-space: nowrap;">
                    <?= $arBasketItemDemensions['LENGTH']; ?>&times;<?= $arBasketItemDemensions['WIDTH']; ?>&times;<?= $arBasketItemDemensions['HEIGHT']; ?>
                </td>
                <td class="td7"
                    style="text-align: right; white-space: nowrap;"><?= intval($arBasket['QUANTITY']); ?></td>
                <td class="td7" style="text-align: right; white-space: nowrap;">
                    <?= number_format($arBasket['WEIGHT'], 0, ',', ' '); ?>
                </td>
            </tr>
            <?
            $total_weight += $arBasket['WEIGHT'] * $arBasket['QUANTITY'];
            ?>
        <? endforeach; ?>
        <tr valign="top" style="font-size: 12px;">
            <td colspan="4"
                style=" border-bottom: 1px solid transparent; border-left: 1px solid transparent; text-align: right; background-color: #ddd; font-weight: bold;">
                Вес всего заказа (гр):
            </td>
            <td style="background-color: #ddd; text-align: right; border-bottom: 1px solid transparent;">
                <?= number_format($total_weight, 0, ',', ' '); ?>
            </td>
        </tr>
        </tbody>
        <? $total_sum_without_discount += floor($arOrder["PRICE_DELIVERY"]); ?> </table>
<? endif; ?>
</body>
</html>
