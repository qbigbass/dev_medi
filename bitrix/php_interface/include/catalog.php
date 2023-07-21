<?

use Bitrix\Main,
    Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Sale,
    Yandex\Market;

use Bitrix\Main\Grid\Declension;

// Дополнительная обработка фида
$eventManager = Main\EventManager::getInstance();

define('IBLOCK_CHUNK_SIZE', 200);

AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "ClearIblockCacheHandler");
AddEventHandler("iblock", "OnAfterIBlockElementAdd", "ClearIblockCacheHandler");
AddEventHandler("iblock", "OnAfterIBlockElementDelete", "ClearIblockCacheHandler");

function ClearIblockCacheHandler($arFields)
{
    if (($arFields['IBLOCK_ID'] == 17 || $arFields['IBLOCK_ID'] == 19) and (!isset($arFields["RESULT"]) or $arFields["RESULT"])) {
        $tag = 'iblock_id_' . $arFields['IBLOCK_ID'] . '_chunk_' . intval($arFields['ID'] / IBLOCK_CHUNK_SIZE);
        
        $taggedCache = \Bitrix\Main\Application::getInstance()->getTaggedCache();
        $taggedCache->clearByTag($tag);
    }
}


include_once("market.php");


function recountGoodsDiscount(/*arParams*/)
{
    CModule::IncludeModule("iblock");
    
    $curentTime = mktime();
    
    $price = 'CATALOG_PRICE_1';
    $max_price = 'CATALOG_PRICE_2';
    
    $price_spb = 'CATALOG_PRICE_6';
    $max_price_spb = 'CATALOG_PRICE_5';
    
    $price_ru = 'CATALOG_PRICE_8';
    $max_price_ru = 'CATALOG_PRICE_7';
    
    $obElm = CIBlockElement::GetList(
        ["TIMESTAMP_X" => "DESC"],
        ["IBLOCK_ID" => "17", "ACTIVE" => "Y"],
        false,
        false,
        ["ID", "IBLOCK_ID", "NAME", "TIMESTAMP_X", $price, $max_price, $price_spb, $max_price_spb, $price_ru,
            $max_price_ru, 'PROPERTY_PRICE_DIFF', 'PROPERTY_PRICE_DIFF_SPB', 'PROPERTY_PRICE_DIFF_RU']
    );
    $p = 0;
    while ($arElm = $obElm->GetNext()) {
        $arFields = [];
        
        $p++;
        $arFields['PRICE_DIFF'] = $arElm[$max_price] - $arElm[$price];
        
        $arFields['PRICE_DIFF_SPB'] = $arElm[$max_price_spb] - $arElm[$price_spb];
        $arFields['PRICE_DIFF_RU'] = $arElm[$max_price_ru] - $arElm[$price_ru];
        
        if ($arFields['PRICE_DIFF'] != $arElm['PROPERTY_PRICE_DIFF_VALUE'] ||
            $arFields['PRICE_DIFF_SPB'] != $arElm['PROPERTY_PRICE_DIFF_SPB_VALUE'] ||
            $arFields['PRICE_DIFF_RU'] != $arElm['PROPERTY_PRICE_DIFF_RU_VALUE']
        ) {
            
            CIBlockElement::SetPropertyValuesEx($arElm['ID'], 17, $arFields);
        }
    }
    
    return 'recountGoodsDiscount();';
}

// Событие по завершению импорта из 1С
AddEventHandler('catalog', 'OnSuccessCatalogImport1C', 'customCatalogImportStep_subj');

function customCatalogImportStep_subj($arParams)
{
    
    mail("makoviychuk@mediexp.ru", "1c  import step finished", "1c  import step  finished <pre>" . print_r($arParams, 1) . "</pre>");
    
    return true;
}

function disableNotAvailableSKU($SECTION_ID = 75)
{
    
    $arElements = [];
    
    $obElm = CIBlockElement::GetList(
        ["ID" => "ASC"],
        ["IBLOCK_ID" => "17", "ACTIVE" => "Y", "SECTION_ID" => $SECTION_ID, "INCLUDE_SUBSECTIONS" => "Y", "SECTION_ACTIVE" => "Y"],
        false,
        false,
        ["ID", "IBLOCK_ID"]
    );
    //echo "<pre>";
    while ($arElm = $obElm->GetNext()) {
        //print_r($arElm);
        $obElm2 = CIBlockElement::GetList(
            ["ID" => "ASC"],
            ["IBLOCK_ID" => "19", "ACTIVE" => "Y", "CATALOG_AVAILABLE" => "N", "PROPERTY_CML2_LINK.ID" => $arElm['ID']],
            false,
            false,
            ["ID", "IBLOCK_ID", "NAME", "ACTIVE"]
        );
        while ($arElm2 = $obElm2->GetNext()) {
            $arElements[] = $arElm2;
        }
    }
    if (!empty($arElements)) {
        $el = new CIBlockElement;
        
        foreach ($arElements as $k => $elm) {
            $res = $el->Update($elm['ID'], ['ACTIVE' => 'N']);
        }
    }
    return "disableNotAvailableSKU(" . $SECTION_ID . ");";
}

function updateDefaultPropSort()
{
    
    $el = new CIBlockElement;
    
    $arSizes = [
        '36' => '559e86da-b810-11e9-8113-e03f49499b1d',
        '36.5' => '19efd6e3-c28f-11e9-8114-e03f49499b1d',
        '37' => '5fc7ae87-b810-11e9-8113-e03f49499b1d',
        '37.5' => '19efd6e4-c28f-11e9-8114-e03f49499b1d',
        
        '38' => '5fc7ae88-b810-11e9-8113-e03f49499b1d',
        '38.5' => '2eef0746-c28f-11e9-8114-e03f49499b1d',
        '39' => '6af9c1f8-b810-11e9-8113-e03f49499b1d',
        '40' => '6af9c1f9-b810-11e9-8113-e03f49499b1d',
        '41' => '7428b05d-b810-11e9-8113-e03f49499b1d',
        '41.5' => '9134330f-b815-11e9-8113-e03f49499b1d',
        '42' => '7428b05e-b810-11e9-8113-e03f49499b1d',
        '42.5' => '3cacb6a6-c28f-11e9-8114-e03f49499b1d',
        '43' => '7428b05f-b810-11e9-8113-e03f49499b1d',
        '43.5' => '3cacb6a7-c28f-11e9-8114-e03f49499b1d',
        '44' => '7b94f022-b810-11e9-8113-e03f49499b1d',
        '44.5' => '3cacb6a8-c28f-11e9-8114-e03f49499b1d',
        '45' => '7b94f023-b810-11e9-8113-e03f49499b1d',
        '45.5' => '51ff9aa1-c28f-11e9-8114-e03f49499b1d',
        '46' => '7b94f024-b810-11e9-8113-e03f49499b1d',
        
        '20' => '3b1c861b-7cb4-11e9-810f-e03f49499b1d', // II
        '22' => '3b1c861c-7cb4-11e9-810f-e03f49499b1d', // III
        '24' => '4270e770-7cb4-11e9-810f-e03f49499b1d', // IV
        
        '20' => '26895998-9768-11e9-8110-e03f49499b1d', // M
        '24' => '26895999-9768-11e9-8110-e03f49499b1d', // L
    
    ];
    
    foreach ($arSizes as $size => $xml_id) {
        $obElm = CIBlockElement::GetList(
            ["ID" => "ASC"],
            ["IBLOCK_ID" => "19", "ACTIVE" => "Y", "PROPERTY_SIZE" => $xml_id],
            false,
            false,
            ["ID", "IBLOCK_ID", "SORT", "PROPERTY_CML2_LINK.PROPERTY_FOR_WHO"]
        );
        while ($arElm = $obElm->GetNext()) {
            $ind = $arElm['PROPERTY_CML2_LINK_PROPERTY_FOR_WHO_VALUE'] == 'Женщины' ? 10 : 2;
            if ($size == "36" || $size == '36.5' || $size == '37' || $size == '37.5') {
                $sort = 498;
            } elseif ($size == "41" || $size == '41.5' || $size == '39' || $size == '40') {
                $sort = 500;
            } else {
                $sort = $size * $ind;
            }
            if ($sort > 10) {
                $el->Update($arElm['ID'], ['SORT' => $sort], false, false);
            }
            
        }
    }
    
    return "updateDefaultPropSort();";
}

function setNoVatFix()
{
    
    CModule::IncludeModule("catalog");
    
    $obProd = CCatalogProduct::GetList([], ['VAT_ID' => [5, 6]], false, false, ['ID', 'ELEMENT_IBLOCK_ID', 'ELEMENT_XML_ID', 'ELEMENT_NAME']);
    while ($arProd = $obProd->GetNext()) {
        CCatalogProduct::Update($arProd['ID'], ['VAT_ID' => 1]);
    }
    return 'setNoVatFix();';
}

function customCatalogImportStep(/*arParams*/)
{
    CModule::IncludeModule("iblock");
    
    $curentTime = mktime();
    
    $el = new CIBlockElement;
    
    // PJS для стелек
    $res = $el->Update(41222, ['ACTIVE' => 'Y']);
    
    
    $obElm = CIBlockElement::GetList(
        ["TIMESTAMP_X" => "DESC"],
        ["IBLOCK_ID" => "19", "ACTIVE" => "Y"],
        false,
        ['nTopCount' => 10],
        ["ID", "IBLOCK_ID", "NAME", "TIMESTAMP_X"]
    );
    $upstr = "\r\n Последние измененные SKU: \r\n\r\n";
    $check_time = time() - 26 * 3600;
    $send_email = false;
    while ($arElm = $obElm->GetNext()) {
        if (strtotime($arElm['TIMESTAMP_X']) < $check_time) {
            $send_email = true;
        }
        $upstr .= $arElm['ID'] . "  " . $arElm['NAME'] . " " . $arElm['TIMESTAMP_X'] . " " . $check_time . " "
            . strtotime($arElm['TIMESTAMP_X']) . "  \r\n";
    }
    
    
    // Получаем список свойств для обновления
    // 345  - Я.Маркет   114 - Бренд   154 - Похожие   152 - Сопутствующие
    
    $arElements = [];
    
    
    $obElm = CIBlockElement::GetList(
        ["ID" => "ASC"],
        ["IBLOCK_ID" => "17", "ACTIVE" => "Y"],
        false,
        false,
        ["ID", "IBLOCK_ID", "NAME", "PROPERTY_345", "PROPERTY_114"]
    );
    while ($arElm = $obElm->GetNext()) {
        
        // Ставим категорию Я.Маркета
        if ($arElm['PROPERTY_345_VALUE'] != '') {
            // поиск  id категории маркета
            if (preg_match_all("/\[(\d+)\]/", $arElm['PROPERTY_345_VALUE'], $matches)) {
                if (intval($matches[1][0]) > 0) {
                    $market_cat_id = $matches[1][0];
                    $arElements[$arElm['ID']][181] = $market_cat_id;
                }
            }
        }
        // Устанавливаем бренд
        if ($arElm['PROPERTY_114_VALUE'] != '') {
            
            $obElmBrand = CIBlockElement::GetList([], ["IBLOCK_ID" => 1, "NAME" => $arElm['PROPERTY_114_VALUE']], false, false, ["ID"]);
            
            if ($arElmBrand = $obElmBrand->GetNext()) {
                $arElements[$arElm['ID']][134] = $arElmBrand['ID'];
            }
            
        }
    }
    
    if (!empty($arElements)) {
        
        foreach ($arElements as $k => $arUpdate) {
            if (count($arUpdate[151]) > 20) {
                unset($arUpdate[151]);
            }
            if (count($arUpdate[153]) > 20) {
                unset($arUpdate[153]);
            }
            if (!empty($arUpdate)) {
                CIBlockElement::SetPropertyValuesEx($k, 17, $arUpdate);
            }
        }
    }
    
    $obElm = CIBlockElement::GetList(
        ["ID" => "ASC"],
        ["IBLOCK_ID" => "19", "ACTIVE" => "Y"],
        false,
        false,
        ["ID", "IBLOCK_ID", "NAME", "PROPERTY_368", "PROPERTY_369", "PROPERTY_370", "PROPERTY_371"]
    );
    $arElements = [];
    while ($arElm = $obElm->GetNext()) {
        
        // Устанавливаем  вес
        if (intval($arElm['PROPERTY_368_VALUE']) > 0) {
            $arElements[$arElm['ID']]["WEIGHT"] = $arElm['PROPERTY_368_VALUE'];
        }
        // Устанавливаем  длину
        if (intval($arElm['PROPERTY_369_VALUE']) > 0) {
            
            $arElements[$arElm['ID']]["LENGTH"] = $arElm['PROPERTY_369_VALUE'];
        }
        // Устанавливаем  ширину
        if (intval($arElm['PROPERTY_370_VALUE']) > 0) {
            $arElements[$arElm['ID']]["WIDTH"] = $arElm['PROPERTY_370_VALUE'];
        }
        // Устанавливаем  высоту
        if (intval($arElm['PROPERTY_371_VALUE']) > 0) {
            $arElements[$arElm['ID']]["HEIGHT"] = $arElm['PROPERTY_371_VALUE'];
        }
    }
    if (!empty($arElements)) {
        CModule::IncludeModule("catalog");
        foreach ($arElements as $pid => $elm) {
            CCatalogProduct::Update($pid, $elm);
        }
    }
    
    $obElm = CIBlockElement::GetList(
        ["ID" => "ASC"],
        ["IBLOCK_ID" => "17", "ACTIVE" => "Y"],
        false,
        false,
        ["ID", "IBLOCK_ID", "NAME", "PROPERTY_367", "PROPERTY_162", "PROPERTY_163", "PROPERTY_164"]
    );
    $arElements = [];
    while ($arElm = $obElm->GetNext()) {
        
        // Устанавливаем  вес
        if (intval($arElm['PROPERTY_367_VALUE']) > 0) {
            $arElements[$arElm['ID']]["WEIGHT"] = $arElm['PROPERTY_367_VALUE'];
        }
        // Устанавливаем  длину
        if (intval($arElm['PROPERTY_162_VALUE']) > 0) {
            
            $arElements[$arElm['ID']]["LENGTH"] = $arElm['PROPERTY_162_VALUE'];
        }
        // Устанавливаем  ширину
        if (intval($arElm['PROPERTY_163_VALUE']) > 0) {
            $arElements[$arElm['ID']]["WIDTH"] = $arElm['PROPERTY_163_VALUE'];
        }
        // Устанавливаем  высоту
        if (intval($arElm['PROPERTY_164_VALUE']) > 0) {
            $arElements[$arElm['ID']]["HEIGHT"] = $arElm['PROPERTY_164_VALUE'];
        }
    }
    if (!empty($arElements)) {
        CModule::IncludeModule("catalog");
        foreach ($arElements as $pid => $elm) {
            CCatalogProduct::Update($pid, $elm);
        }
    }
    
    $endTime = mktime() - $curentTime;
    
    if ($send_email) {
        mail("makoviychuk@mediexp.ru", "Failed 1C exchange!!!", "1c after import update finished " . $endTime . "\r\n" . $upstr);
    }
    
    // Проверка фото
    $obElm = CIBlockElement::GetList(
        ["TIMESTAMP_X" => "DESC"],
        ["IBLOCK_ID" => "17", "ACTIVE" => "Y", "DETAIL_PICTURE" => false, "!ID" => 41222],
        false,
        false,
        //['nTopCount' => 50],
        ["ID", "IBLOCK_ID", "NAME", "TIMESTAMP_X"]
    );
    $upstr = "\r\n Товары без фото: \r\n\r\n";
    
    $send_email_new = false;
    while ($arElm = $obElm->GetNext()) {
        $obElm2 = CIBlockElement::GetList(
            ["TIMESTAMP_X" => "DESC"],
            ["IBLOCK_ID" => "19", "ACTIVE" => "Y", "DETAIL_PICTURE" => false, "PROPERTY_CML2_LINK" => $arElm['ID']],
            false,
            false,
            ["ID", "IBLOCK_ID", "NAME", "TIMESTAMP_X"]
        );
        
        if ($arElm2 = $obElm2->GetNext()) {
            $send_email_new = true;
            
            $upstr .= $arElm['ID'] . "  " . $arElm['NAME'] . " " . $arElm['DATE_CREATE'] .
                "https://www.medi-salon.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17" .
                "&type=catalog&lang=ru&ID=" . $arElm['ID'] . "&find_section_section=-1&WF=Y" . "  \r\n";
        } else {
            $obElm3 = CIBlockElement::GetList(
                ["TIMESTAMP_X" => "DESC"],
                ["IBLOCK_ID" => "19", "ACTIVE" => "Y", "PROPERTY_CML2_LINK" => $arElm['ID']],
                false,
                false,
                ["ID", "IBLOCK_ID", "NAME", "TIMESTAMP_X"]
            );
            if (!$arElm3 = $obElm3->GetNext()) {
                $send_email_new = true;
                
                $upstr .= $arElm['ID'] . "  " . $arElm['NAME'] . " " . $arElm['DATE_CREATE'] .
                    "https://www.medi-salon.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17" .
                    "&type=catalog&lang=ru&ID=" . $arElm['ID'] . "&find_section_section=-1&WF=Y" . "  \r\n";
            }
        }
    }
    if ($send_email_new) {
        mail("content@mediexp.ru", "Товары без фото на сайте!", "" . "\r\n" . $upstr);
    }
    
    // activate igli
    $obElm = CIBlockElement::GetList(
        ["ID" => "ASC"],
        ["IBLOCK_ID" => "17", "ACTIVE" => "Y", "SECTION_ID" => 85, "INCLUDE_SUBSECTIONS" => "Y", "SECTION_ACTIVE" => "Y"],
        false,
        false,
        ["ID", "IBLOCK_ID"]
    );
    //echo "<pre>";
    while ($arElm = $obElm->GetNext()) {
        //print_r($arElm);
        $obElm2 = CIBlockElement::GetList(
            ["ID" => "ASC"],
            ["IBLOCK_ID" => "19", "PROPERTY_CML2_LINK.ID" => $arElm['ID']],
            false,
            false,
            ["ID", "IBLOCK_ID", "NAME", "ACTIVE"]
        );
        while ($arElm2 = $obElm2->GetNext()) {
            $arElements[] = $arElm2;
        }
    }
    if (!empty($arElements)) {
        $el = new CIBlockElement;
        
        foreach ($arElements as $k => $elm) {
            $res = $el->Update($elm['ID'], ['ACTIVE' => 'Y']);
        }
    }
    
    return 'customCatalogImportStep();';
}


AddEventHandler("sale", "OnSaleComponentOrderOneStepDelivery", "mediModifyDelivery");
function mediModifyDelivery(&$arFields)
{
    $dayDeclension = new Declension('&nbsp;день', '&nbsp;дня', '&nbsp;дней');
    
    if (!empty($arFields['DELIVERY'])) {
        foreach ($arFields['DELIVERY'] as $deliveryId => $arDelivery) {
            if (in_array($deliveryId, [61, 62, 64, 65])) {
                continue;
            }
            if (!empty($arDelivery['PERIOD_TEXT'])) {
                $nums = preg_match_all("/\d/", $arDelivery['PERIOD_TEXT'], $periods);
                if ($nums) {
                    foreach ($periods[0] as $i => $period) {
                        $periods[0][$i] = $period + 2;
                    }
                    if (count($periods[0]) == 1) {
                        $arFields['DELIVERY'][$deliveryId]['PERIOD_TEXT'] = $periods[0][0] . $dayDeclension->get($periods[0][0]);;
                    }
                    if (count($periods[0]) == 2) {
                        $arFields['DELIVERY'][$deliveryId]['PERIOD_TEXT'] = $periods[0][0] . '  &ndash; ' . $periods[0][1] . $dayDeclension->get($periods[0][1]);
                    }
                }
            }
        }
    }
}

// Округление стоимости доставки при рассчете в компоненте bitrix:sale.order.ajax
AddEventHandler("sale", "OnSaleCalculateOrderDelivery", "mediModifyOrder");

function mediModifyOrder(&$arOrder)
{
    
    if ($arOrder['PRICE_DELIVERY'] && ($arOrder['PRICE_DELIVERY'] > 0)) {
        $arOrder['PRICE_DELIVERY'] = ceil($arOrder['PRICE_DELIVERY'] / 10) * 10;
    }
    if ($arOrder['DELIVERY_PRICE'] && ($arOrder['DELIVERY_PRICE'] > 0)) {
        $arOrder['DELIVERY_PRICE'] = ceil($arOrder['DELIVERY_PRICE'] / 10) * 10;
    }
}

function updateRegionsCount()
{
    
    $bs = new CIBlockSection;
    
    $obSect = CIBlockSection::GetList(
        ["ID" => "ASC"],
        ["IBLOCK_ID" => "17", "ACTIVE" => "Y"],
        false,
        ["ID"]
    );
    $arSections = [];
    while ($arSect = $obSect->GetNext()) {
        $arSections[] = $arSect['ID'];
    }
    if (!empty($arSections)) {
        // получаем ID пользовательского свойства в котором будем сохранять доступность раздела в регоине
        $rsData = CUserTypeEntity::GetList(array(), array("FIELD_NAME" => "UF_REGIONS"));
        if ($arRes = $rsData->Fetch()) {
            $USER_FIELD_ID = $arRes['ID'];
        }
        $rsRegs = CUserFieldEnum::GetList(array(), array(
            "USER_FIELD_ID" => $USER_FIELD_ID
        ));
        $arProps = [];
        while ($arRegs = $rsRegs->GetNext()) {
            $arProps[$arRegs['ID']] = $arRegs['XML_ID'];
        }
        foreach ($arSections as $k => $section_id) {
            $arResult = [];
            
            foreach ($GLOBALS['medi']['region_cities'] as $sid => $city) {
                $cntElm = CIBlockElement::GetList(
                    ["ID" => "ASC"],
                    ["IBLOCK_ID" => "17", "ACTIVE" => "Y", "SECTION_ID" => $section_id, "INCLUDE_SUBSECTIONS" => "Y", "PROPERTY_REGION_VALUE" => $city],
                    [],
                    false
                );
                $arResult[$sid] = $cntElm;
                
            }
            $arFields = [];
            foreach ($arProps as $p => $prop) {
                if ($arResult[$prop] > 0)
                    $arFields[] = $p;
            }
            if (!empty($arResult)) {
                $arFields = [
                    "UF_REGIONS" => $arFields
                ];
                $bs->Update($section_id, $arFields);
            }
            
        }
    }
    
    return 'updateRegionsCount();';
}

function updateRegionsAvailability()
{
    
    CModule::IncludeModule("iblock");
    CModule::IncludeModule("catalog");
    
    $IBLOCK_ID = 17;
    $SKU_IBLOCK_ID = 19;
    
    $cities = [
        "Москва" => "1",
        "Россия" => "1",
        "Санкт-Петербург" => "2",
        "Казань" => ["1", "3"],
        "Калининград" => ["1", "4"],
        "Тюмень" => ["1", "5"],
        "Ростов-на-Дону" => ["1", "6"],
        "Нижний Новгород" => ["1", "7"],
        "Екатеринбург" => ["1", "8"],
    ];
    
    $arElements = [];
    
    //print_r($arElm);
    $obElm2 = CIBlockElement::GetList(
        ["ID" => "ASC"],
        ["IBLOCK_ID" => $SKU_IBLOCK_ID, "ACTIVE" => "Y"],
        false,
        false,
        // ["nTopCount"=>1],
        ["ID", "IBLOCK_ID", "NAME", "ACTIVE", "PROPERTY_REGION"]
    );
    while ($arElm2 = $obElm2->GetNext()) {
        
        $updateRegions = [];
        
        if (!empty($arElm2['PROPERTY_REGION_VALUE'])) {
            foreach ($arElm2['PROPERTY_REGION_VALUE'] as $k => $region) {
                $prop_id = $k;
                if ($region == 'Россия') $region = 'Москва';
                
                $dbResult = CCatalogStore::GetList(
                    array(),
                    array('ACTIVE' => 'Y', "UF_CITY" => $cities[$region], "PRODUCT_ID" => $arElm2['ID'], ">PRODUCT_AMOUNT" => 0),
                    false,
                    false,
                    array("ID", "TITLE", "ACTIVE", "PRODUCT_AMOUNT", "ELEMENT_ID", "UF_CITY")
                );
                if ($arStores = $dbResult->GetNext()) {
                    
                    $updateRegions[$arElm2['ID']][] = $k;
                    
                    if ($region != 'Санкт-Петербург') {
                        $updateRegions[$arElm2['ID']][] = $k;
                    }
                    
                    $arElm2['PRODUCT_AMOUNT'][$k] = $arStores['PRODUCT_AMOUNT'];
                    $arElm2['PRODUCT_AMOUNT_CITY'][$region] = $arStores['PRODUCT_AMOUNT'];
                }
            }
        }
        
        if (!empty($updateRegions)) {
            foreach ($updateRegions as $ElmID => $update) {
                CIBlockElement::SetPropertyValuesEx($ElmID, false, array("REGION" => $update));
            }
        }
    }
    
    // простые товары
    $obElm = CIBlockElement::GetList(
        ["ID" => "ASC"],
        ["IBLOCK_ID" => $IBLOCK_ID, "ACTIVE" => "Y"],
        false,
        false,
        // ["nTopCount"=>1],
        ["ID", "IBLOCK_ID", "NAME", "ACTIVE", "PROPERTY_REGION"]
    );
    while ($arElm = $obElm->GetNext()) {
        
        $updateRegions = [];
        
        if (!empty($arElm['PROPERTY_REGION_VALUE'])) {
            foreach ($arElm['PROPERTY_REGION_VALUE'] as $k => $region) {
                $prop_id = $k;
                if ($region == 'Россия') $region = 'Москва';
                
                $dbResult = CCatalogStore::GetList(
                    array(),
                    array('ACTIVE' => 'Y', "UF_CITY" => $cities[$region], "PRODUCT_ID" => $arElm['ID'], ">PRODUCT_AMOUNT" => 0),
                    false,
                    false,
                    array("ID", "TITLE", "ACTIVE", "PRODUCT_AMOUNT", "ELEMENT_ID", "UF_CITY")
                );
                if ($arStores = $dbResult->GetNext()) {
                    
                    $updateRegions[$arElm['ID']][] = $k;
                    
                    $arElm['PRODUCT_AMOUNT'][$k] = $arStores['PRODUCT_AMOUNT'];
                    $arElm['PRODUCT_AMOUNT_CITY'][$region] = $arStores['PRODUCT_AMOUNT'];
                }
            }
        }
        
        if (!empty($updateRegions)) {
            foreach ($updateRegions as $ElmID => $update) {
                CIBlockElement::SetPropertyValuesEx($ElmID, false, array("REGION" => $update));
            }
        }
    }
    
    return 'updateRegionsAvailability();';
}

/**
 * Агент, очистка устаревшего кеша
 * @return string
 */
function clearExpiredCacheFiles($time = "20")
{
    if (!class_exists('CFileCacheCleaner')) {
        require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/cache_files_cleaner.php');
    }
    
    $curentTime = mktime();
    $endTime = time() + $time;
    $path = '';
    
    //Работаем с устаревшим кешем
    $obCacheCleaner = new CFileCacheCleaner('expired');
    if (!$obCacheCleaner->InitPath($path)) {
        \CEventLog::Add([
            'SEVERITY' => 'ERROR',
            'AUDIT_TYPE_ID' => 'clearExpiredCacheFiles',
            'MODULE_ID' => 'main',
            'ITEM_ID' => __CLASS__,
            'DESCRIPTION' => 'Неверный путь к файлу кеша',
        ]);
    }
    $obCacheCleaner->Start();
    while ($file = $obCacheCleaner->GetNextFile()) {
        if (is_string($file)) {
            $date_expire = $obCacheCleaner->GetFileExpiration($file);
            if ($date_expire) {
                if ($date_expire < $curentTime) {
                    unlink($file);
                }
            }
            if (time() >= $endTime) break;
        }
    }
    
    return 'clearExpiredCacheFiles();';
}

// Исключаем поиск по описаниям
AddEventHandler("search", "BeforeIndex", array("SearchHandlers", "BeforeIndexHandler"));

class SearchHandlers
{
    function BeforeIndexHandler($arFields)
    {
        if ($arFields["MODULE_ID"] == "iblock") {
            if (array_key_exists("BODY", $arFields) && substr($arFields["ITEM_ID"], 0, 1) != "S") // Только для элементов
            {
                $obElm = CIBlockElement::GetList([], ['ID' => $arFields['ITEM_ID'], 'IBLOCK_ID' => $arFields['PARAM2']], false, false, ['PROPERTY_CML2_ARTICLE', 'PROPERTY_SEARCH_PHRASES']);
                if ($arElm = $obElm->GetNext()) {
                    $arFields["TITLE"] .= $arElm['PROPERTY_CML2_ARTICLE_VALUE']
                        . ' ' . $arElm['PROPERTY_SEARCH_PHRASES_VALUE'];
                    $arFields["BODY"] .= $arElm['PROPERTY_CML2_ARTICLE_VALUE'] . ' '
                        . $arElm['PROPERTY_SEARCH_PHRASES_VALUE'] . ' '
                        . $arElm['PROPERTY_CML2_ARTICLE_VALUE'] . ' ' . $arElm['PROPERTY_SEARCH_PHRASES_VALUE'] . '; '
                        . $arElm['PROPERTY_CML2_ARTICLE_VALUE'] . ' ' . $arElm['PROPERTY_SEARCH_PHRASES_VALUE'];
                    
                    $arFields["TAGS"] .= $arElm['PROPERTY_CML2_ARTICLE_VALUE']
                        . ', ' . $arElm['PROPERTY_SEARCH_PHRASES_VALUE'];
                } else {
                    
                    //$arFields["BODY"] = "";
                }
            }
            
            if (substr($arFields["ITEM_ID"], 0, 1) == "S") // Только для разделов
            {
                $arFields['TITLE'] = "";
                $arFields["BODY"] = "";
                $arFields['TAGS'] = "";
            }
        }
        
        return $arFields;
    }
}

// Привязка видов изделия к инфоблоку, для создания древовидного меню (пока только в обуви)
function setTypeTree()
{
    
    $TYPE_IBLOCK_ID = 33;
    $IBLOCK_ID = 17;
    
    CModule::IncludeModule("iblock");
    
    $obElm = CIBlockElement::GetList(
        ["ID" => "ASC"],
        ["IBLOCK_ID" => $IBLOCK_ID, "ACTIVE" => "Y", "SUBSECTION" => 88],
        false,
        false,
        ["ID", "IBLOCK_ID", "NAME", "PROPERTY_PRODUCT_TYPE", "PRODUCT_TYPE_LINK"]
    );
    while ($arElm = $obElm->GetNext()) {
        
        $type_names = $arElm['PROPERTY_PRODUCT_TYPE_VALUE'];
        
        $arSections = [];
        
        if (!empty($type_names)) {
            $arFilter = ["IBLOCK_ID" => $TYPE_IBLOCK_ID, "ACTIVE" => "Y"];
            if (count($type_names) > 1) {
                $arFilter['NAME'] = array_values($type_names);
            } else {
                $arFilter['=NAME'] = array_shift($type_names);
            }
            $obSectType = CIBlockSection::GetList(
                ["ID" => "ASC"],
                $arFilter,
                false,
                ["ID", "IBLOCK_SECTION_ID"]
            );
            while ($arSectType = $obSectType->GetNext()) {
                
                $arSections[] = $arSectType['ID'];
                if ($arSectType['IBLOCK_SECTION_ID'] > 0) {
                    $arSections[] = $arSectType['IBLOCK_SECTION_ID'];
                }
            }
        }
        if (!empty($arSections)) {
            $arUpdate = ['PRODUCT_TYPE_LINK' => array_unique($arSections)];
            CIBlockElement::SetPropertyValuesEx($arElm['ID'], $IBLOCK_ID, $arUpdate);
        }
        
    }
    reindexCatalogFaset();
    return 'setTypeTree();';
}

// Привязка и отвязка товаров от специальных разделов (новинки, хиты продаж, распродажи и т.п.)
function updateSpecialCats()
{
    
    CModule::IncludeModule("iblock");
    
    $IBLOCK_ID = 17;
    // ID и коды разделов
    $cats = ['new' => 629, 'sale' => 630, 'hit' => 631];
    // ID и коды меток свойства PROPERTY_OFFERS
    $signs = ['new' => 509, 'sale' => 15336, 'hit' => 510];
    
    $el = new CIBlockElement;
    
    // Проверка товаров уже привязанных к спец.разделам
    foreach ($cats as $alias => $cat_id) {
        
        $obElm = CIBlockElement::GetList(
            ["ID" => "ASC"],
            ["IBLOCK_ID" => $IBLOCK_ID, "ACTIVE" => "Y", "SECTION_ID" => $cat_id],
            false,
            false,
            ["ID", "NAME", "PROPERTY_OFFERS", "IBLOCK_SECTION_ID"]
        );
        while ($arElm = $obElm->GetNext()) {
            // Если нет соответствующей метки у товара, товар надо открепить от раздела
            
            // получаем основной раздел
            $main_group_id = $arElm['IBLOCK_SECTION_ID'];
            
            // получаем все остальные привязки к разделам
            $db_old_groups = CIBlockElement::GetElementGroups($arElm['ID'], true);
            $ar_new_groups = array();
            while ($ar_group = $db_old_groups->Fetch())
                $ar_new_groups[] = $ar_group["ID"];
            
            // убираем лишний раздел
            if (in_array($cat_id, $ar_new_groups)) {
                $remove_index = array_search($cat_id, $ar_new_groups);
                unset($ar_new_groups[$remove_index]);
                
                $el->Update($arElm['ID'], ['IBLOCK_SECTION' => $ar_new_groups, 'IBLOCK_SECTION_ID' => $main_group_id, 'TIMESTAMP_X' => false]);
            }
        }
    }
    
    // Проверка свойства всех товаров и привязка их к спец.разделам, если еще не привязаны
    foreach ($signs as $alias => $prop_id) {
        
        $obElm = CIBlockElement::GetList(
            ["ID" => "ASC"],
            ["IBLOCK_ID" => $IBLOCK_ID, "ACTIVE" => "Y", "PROPERTY_OFFERS" => $prop_id],
            false,
            false,
            ["ID", "NAME", "PROPERTY_OFFERS", "IBLOCK_SECTION_ID"]
        );
        while ($arElm = $obElm->GetNext()) {
            // Если есть соответствующая метки у товара, товар надо прикрепить к разделу
            
            // получаем основной раздел
            $main_group_id = $arElm['IBLOCK_SECTION_ID'];
            
            // получаем все остальные привязки к разделам
            $db_old_groups = CIBlockElement::GetElementGroups($arElm['ID'], true);
            $ar_new_groups = array();
            while ($ar_group = $db_old_groups->Fetch())
                $ar_new_groups[] = $ar_group["ID"];
            
            // добавляем  раздел
            if (!in_array($cats[$alias], $ar_new_groups)) {
                
                $ar_new_groups[] = $cats[$alias];
                
                $el->Update($arElm['ID'], ['IBLOCK_SECTION' => $ar_new_groups, 'IBLOCK_SECTION_ID' => $main_group_id, 'TIMESTAMP_X' => false]);
            }
        }
    }
    // активируем разделы
    $cats = array_merge($cats, ['actions' => 688]);  // 688 бой
    $bs = new CIBlockSection;
    foreach ($cats as $alias => $cat_id) {
        $res = $bs->Update($cat_id, ['ACTIVE' => 'Y']);
    }
    
    
    unset($cats);
    
    $cats = ['actions' => 688];  // 688 бой 652 dev3
    
    $obElm = CIBlockElement::GetList(
        ["ID" => "ASC"],
        ["IBLOCK_ID" => $IBLOCK_ID, "ACTIVE" => "Y", "SECTION_ID" => $cats['actions'], "PROPERTY_ACTION_SIGN" => false],
        false,
        false,
        ["ID", "NAME", "PROPERTY_ACTION_SIGN", "IBLOCK_SECTION_ID"]
    );
    
    while ($arElm = $obElm->GetNext()) {
        // Если нет  метки у товара, товар надо открепить от раздела
        
        // получаем основной раздел
        $main_group_id = $arElm['IBLOCK_SECTION_ID'];
        
        // получаем все остальные привязки к разделам
        $db_old_groups = CIBlockElement::GetElementGroups($arElm['ID'], true);
        $ar_new_groups = array();
        while ($ar_group = $db_old_groups->Fetch())
            $ar_new_groups[] = $ar_group["ID"];
        
        // убираем лишний раздел
        if (in_array($cats['actions'], $ar_new_groups)) {
            $remove_index = array_search($cats['actions'], $ar_new_groups);
            unset($ar_new_groups[$remove_index]);
            
            $el->Update($arElm['ID'], ['IBLOCK_SECTION' => $ar_new_groups, 'IBLOCK_SECTION_ID' => $main_group_id, 'TIMESTAMP_X' => false]);
        }
    }
    
    // Проверка свойства всех товаров и привязка их к разделу акции, если еще не привязаны
    $obElm = CIBlockElement::GetList(
        ["ID" => "ASC"],
        ["IBLOCK_ID" => $IBLOCK_ID, "ACTIVE" => "Y", "!PROPERTY_ACTION_SIGN" => false],
        false,
        false,
        ["ID", "NAME", "IBLOCK_SECTION_ID"]
    );
    while ($arElm = $obElm->GetNext()) {
        // Если есть соответствующая метки у товара, товар надо прикрепить к разделу
        
        // получаем основной раздел
        $main_group_id = $arElm['IBLOCK_SECTION_ID'];
        
        // получаем все остальные привязки к разделам
        $db_old_groups = CIBlockElement::GetElementGroups($arElm['ID'], true);
        $ar_new_groups = array();
        while ($ar_group = $db_old_groups->Fetch())
            $ar_new_groups[] = $ar_group["ID"];
        
        // добавляем  раздел
        if (!in_array($cats['actions'], $ar_new_groups)) {
            
            $ar_new_groups[] = $cats['actions'];
            
            $el->Update($arElm['ID'], ['IBLOCK_SECTION' => $ar_new_groups, 'IBLOCK_SECTION_ID' => $main_group_id, 'TIMESTAMP_X' => false]);
        }
    }
    
    reindexCatalogFaset();
    
    return 'updateSpecialCats();';
}

// установка offer id в HL MRObuv
$eventManager = \Bitrix\Main\EventManager::getInstance();

$eventManager->addEventHandler('', 'MRObuvOnAfterUpdate', 'MRObuvOnUpdate');
$eventManager->addEventHandler('', 'MRObuvOneAferAdd', 'MRObuvOnUpdate');

function MRObuvOnUpdate(\Bitrix\Main\Entity\Event $event)
{
    
    static $bHandlerStop;
    
    if ($bHandlerStop === true)
        return;
    
    $ID = $event->getParameter("id");
    
    if (is_array($ID))
        $ID = $ID["ID"];
    if (!$ID)
        return;
    
    $entity = $event->getEntity();
    
    $entityDataClass = $entity->GetDataClass();
    
    $eventType = $event->getEventType();
    
    $arParameters = $event->getParameters();
    
    $arFields = $event->getParameter("fields");
    
    $result = new \Bitrix\Main\Entity\EventResult();
    
    if (!empty($arFields['UF_NAME'])) {
        
        CModule::IncludeModule("iblock");
        $obElement = CIBlockElement::GetList([], ['IBLOCK_ID' => 19, '=PROPERTY_CML2_ARTICLE' => $arFields['UF_NAME']['VALUE'], false, false, ['ID']]);
        if ($arElement = $obElement->GetNext()) {
            if ($arFields['UF_OFFER_ID'] != $arElement['ID']) {
                $arFields['UF_OFFER_ID'] = intval($arElement['ID']);
                $bHandlerStop = true;
                $result = $entityDataClass::update($ID, array("UF_OFFER_ID" => $arElement['ID']));
                $bHandlerStop = false;
            }
        } else {
            $bHandlerStop = true;
            $result = $entityDataClass::update($ID, array("UF_OFFER_ID" => ''));
            $bHandlerStop = false;
        }
    }
    return $result;
}

function reindexCatalogFaset()
{
    
    $max_execution_time = 20;
    $iblockId = 17;
    
    // Пересоздание фасетного индекса
    // Удалим имеющийся индекс
    Bitrix\Iblock\PropertyIndex\Manager::dropIfExists($iblockId);
    Bitrix\Iblock\PropertyIndex\Manager::markAsInvalid($iblockId);
    
    // Создадим новый индекс
    $index = Bitrix\Iblock\PropertyIndex\Manager::createIndexer($iblockId);
    $index->startIndex();
    $NS = 0;
    
    do {
        $res = $index->continueIndex($max_execution_time);
        $NS += $res;
    } while ($res > 0);
    
    $index->endIndex();
    
    // чистим кэши
    \CBitrixComponent::clearComponentCache("bitrix:catalog.smart.filter");
    \CIBlock::clearIblockTagCache($iblockId);
    
    return $NS;
}

// карта картинок сайта
function imageXmlSitemapGen()
{
    CModule::IncludeModule("iblock");
    $dom = new domDocument("1.0", 'utf-8');
    #$xml = $dom->createElement("xml");
    #$xml ->setAttributeNS(null, 'version', '1.0');
    #$xml ->setAttributeNS(null, 'encoding', 'utf-8');
    #$dom->appendChild($xml);
    $urlset = $dom->createElement("urlset");
    $urlset->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    $urlset->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');
    
    $arSelect = array("ID", "NAME", "DETAIL_PAGE_URL", "DETAIL_PICTURE", "PROPERTY_MORE_PHOTO");
    $arFilter = array("IBLOCK_ID" => 17, "INCLUDE_SUBSECTIONS" => "Y", "ACTIVE" => "Y", "PROPERTY_CITY_VALUE" => 'msk'); //ID Инфоблока и ID раздела с элементами
    $rsElement = CIBlockElement::GetList(array("NAME" => "ASC"), $arFilter, false, false, $arSelect);
    $arResult["ITEMS"] = array();
    $i = 0;
    while ($arItem = $rsElement->GetNext()) {
        $i++;
        //$arItem = $obElement->GetFields();
        //$arItem["PROPERTIES"] = $obElement->GetProperties();
        $google_link = 'https://www.medi-salon.ru' . $arItem['DETAIL_PAGE_URL'];
        $google_img = 'https://www.medi-salon.ru' . CFile::GetPath($arItem['DETAIL_PICTURE']);
        
        $url = $dom->createElement("url");
        $login = $dom->createElement("loc", $google_link);
        $url->appendChild($login);
        
        $image = $dom->createElement("image:image");
        $image2 = $dom->createElement("image:loc", $google_img);
        $image2n = $dom->createElement("image:title", $arItem['NAME']);
        $image->appendChild($image2);
        $image->appendChild($image2n);
        
        if (!empty($arItem['PROPERTY_MORE_PHOTO_VALUE'])) {
            foreach ($arItem['PROPERTY_MORE_PHOTO_VALUE'] as $key => $photo) {
                
                $google_img2 = 'https://www.medi-salon.ru' . CFile::GetPath($photo);
                
                $image = $dom->createElement("image:image");
                $image2 = $dom->createElement("image:loc", $google_img);
                $image2n = $dom->createElement("image:title", $arItem['NAME']);
                $image->appendChild($image2n);
                $image->appendChild($image2);
                $url->appendChild($image);
                
                $i++;
            }
            
        }
        
        $url->appendChild($image);
        
        $urlset->appendChild($url);
        
        
    };
    $dom->appendChild($urlset);
    $dom->save($_SERVER['DOCUMENT_ROOT'] . "/sitemap-image.xml");
    return 'imageXmlSitemapGen();';
}
