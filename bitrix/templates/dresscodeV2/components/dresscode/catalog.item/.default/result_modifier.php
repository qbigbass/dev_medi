<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

if (!empty($arResult)) {
    
    if ($arParams['CACHE_TYPE'] === 'A') {
        $taggedCache = \Bitrix\Main\Application::getInstance()->getTaggedCache();
        $taggedCache->abortTagCache(); // сбрасываем стандартные теги
        
        $tag = 'iblock_id_' . $arParams['IBLOCK_ID'] . '_chunk_' . intval($arResult['ID'] / IBLOCK_CHUNK_SIZE);
        
        $taggedCache->startTagCache($this->__component->getCachePath());
        $taggedCache->registerTag($tag); // Ставим свой тег
    }
    
    CModule::IncludeModule("catalog");
    CModule::IncludeModule("iblock");
    CModule::IncludeModule("sale");
    
    $filter = array(
        "ACTIVE" => "Y",
        "PRODUCT_ID" => $arResult["ID"],
        "+SITE_ID" => SITE_ID,
        "ISSUING_CENTER" => 'Y',
        "UF_BOOKING" => true
    );
    $rsProps = CCatalogStore::GetList(
        array('TITLE' => 'ASC', 'ID' => 'ASC'),
        $filter,
        false,
        false,
        ["ID", "ACTIVE", "PRODUCT_AMOUNT"]
    );
    $arResult['SALON_AVAILABLE'] = 0;
    $arResult['SALON_COUNT'] = 0;
    while ($sStore = $rsProps->GetNext()) {
        $arResult['SALON_AVAILABLE'] += $sStore['PRODUCT_AMOUNT'];
        
        if ($sStore['PRODUCT_AMOUNT'] > 0) {
            $arResult['SALON_COUNT']++;
        }
    }
    
    $maxprice_id = $GLOBALS['medi']['max_price_id'][SITE_ID];
    
    // max price отображение старой цены
    if (!empty($arResult['SKU_OFFERS'])) {
        $obOffer = CIBlockElement::GetList([], ["IBLOCK_ID" => $arResult['IBLOCK_ID'], "ID" => array_keys($arResult['SKU_OFFERS']), "ACTIVE" => "Y"], false, false, ["ID", "CATALOG_PRICE_" . $maxprice_id]);
        while ($arOffer = $obOffer->GetNext()) {
            $arResult['MAX_PRICE'][$arOffer['ID']] = $arOffer['CATALOG_PRICE_' . $maxprice_id];
        }
    } else {
        $obOffer = CIBlockElement::GetList([], ["IBLOCK_ID" => $arResult['IBLOCK_ID'], "ID" => $arResult['ID'], "ACTIVE" => "Y"], false, false, ["ID", "CATALOG_PRICE_" . $maxprice_id]);
        while ($arOffer = $obOffer->GetNext()) {
            $arResult['MAX_PRICE'][$arOffer['ID']] = $arOffer['CATALOG_PRICE_' . $maxprice_id];
        }
    }
    
    
    $checkID = $arResult['PARENT_PRODUCT']['ID'] ? $arResult['PARENT_PRODUCT']['ID'] : $arResult['ID'];
    $mainElem = CIBlockElement::GetList([], ['ID' => $checkID, false, false, ['IBLOCK_SECTION_ID']]);
    if ($arMainElem = $mainElem->GetNext()) {
        // определяем корневую категорию
        $arSects = [];
        $nav = CIBlockSection::GetNavChain(false, $arMainElem['IBLOCK_SECTION_ID']);
        while ($arSectionPath = $nav->GetNext()) {
            $arSects[] = $arSectionPath;
        }
    }
    
    
    /*w2l($GLOBALS['medi']['max_price_id'], 1, 'component.item.log');
    w2l($arResult['ID'], 1, 'component.item.log');

    w2l($arResult['MAX_PRICE'], 1, 'component.item.log');*/
    //NOTE Управление показом кнопок
    
    
    // "Возможно изготовление на заказ"
    $arResult['DISPLAY_BUTTONS']['MTM_BUTTON'] = false;
    if (isset($arResult['PROPERTIES']['MTM']['VALUE'])
        && $arResult['PROPERTIES']['MTM']['VALUE'] == 'Да'
    ) {
        $arResult['DISPLAY_BUTTONS']['MTM_BUTTON'] = true;
    }
    
    // "Только под заказ"
    $arResult['DISPLAY_BUTTONS']['ONLY_ORDER_BUTTON'] = false;
    if (isset($arResult['PROPERTIES']['ORDER_ONLY']['VALUE'])
        && $arResult['PROPERTIES']['ORDER_ONLY']['VALUE'] == 'Да'
    ) {
        $arResult['DISPLAY_BUTTONS']['ONLY_ORDER_BUTTON'] = true;
    }
    
    // Показывать или скрывать кнопку "В корзину"
    $arResult['DISPLAY_BUTTONS']['CART_BUTTON'] = true;
    if (isset($arResult['PROPERTIES']['NO_CART_BUTTON']['VALUE'])
        && $arResult['PROPERTIES']['NO_CART_BUTTON']['VALUE'] == 'Да'
    ) {
        $arResult['DISPLAY_BUTTONS']['CART_BUTTON'] = false;
    }
    global $USER;
    // Проверка для показа кнопки салонам
    if ($USER->IsAuthorized()
        && !empty(array_intersect([20, 1], $USER->GetUserGroupArray())))
    {
        $rsUsers = CUser::GetList(($by="id"), ($order="desc"), ['ID'=>$USER->GetID()], ['SELECT'=>['UF_SKLAD']]);
        if ($salonUser = $rsUsers->GetNext())
        {
            $arResult['SALON'] = $salonUser;
        }
        $filter = array(
            "ACTIVE" => "Y",
            "PRODUCT_ID" => $arResult['ID'],
            "+SITE_ID" => SITE_ID,
            "XML_ID" => $arResult['SALON']['UF_SKLAD']
        );
        $rsProps = CCatalogStore::GetList(
            array('TITLE' => 'ASC', 'ID' => 'ASC'),
            $filter,
            false,
            false,
            ['UF_*', 'PRODUCT_AMOUNT', "XML_ID"]
        );
        if ($salonInfo = $rsProps->GetNext())
        {
            if ($salonInfo['PRODUCT_AMOUNT'] > 0) {
                $arResult['DISPLAY_BUTTONS']['CART_BUTTON'] = true;
                $arResult['CAN_BUY'] = 'Y';
                $arResult['CATALOG_AVAILABLE'] = 'Y';
                $arResult['CATALOG_QUANTITY'] += $salonInfo['PRODUCT_AMOUNT'];
            }
        }
    }
    
    
    // Показывать или скрывать кнопку "Забронировать в салоне"
    $arResult['DISPLAY_BUTTONS']['RESERV_BUTTON'] = true;
    if (isset($arResult['PROPERTIES']['NO_RESERV_BUTTON']['VALUE'])
        && $arResult['PROPERTIES']['NO_RESERV_BUTTON']['VALUE'] == 'Да'
    ) {
        $arResult['DISPLAY_BUTTONS']['RESERV_BUTTON'] = false;
    }
    // Показывать или скрывать кнопку "Запись на изготовление стелек"
    $arResult['DISPLAY_BUTTONS']['INSOLE_BUTTON'] = false;
    if (isset($arResult['PROPERTIES']['INSOLE_BUTTON']['VALUE'])
        && $arResult['PROPERTIES']['INSOLE_BUTTON']['VALUE'] == 'Да'
    ) {
        $arResult['DISPLAY_BUTTONS']['INSOLE_BUTTON'] = true;
    }
    
    if ($USER->IsAuthorized() && !empty(array_intersect([29], $USER->GetUserGroupArray()))) {
        $arResult['DISPLAY_BUTTONS']['SMP_BUTTON'] = true;
        
        $arResult['DISPLAY_BUTTONS']['INSOLE_BUTTON'] = false;
        $arResult['DISPLAY_BUTTONS']['RESERV_BUTTON'] = false;
        $arResult['DISPLAY_BUTTONS']['CART_BUTTON'] = false;
        $arResult['DISPLAY_BUTTONS']['ONLY_ORDER_BUTTON'] = false;
    }
    
    $this->__component->arResult['MAX_PRICE'] = $arResult['MAX_PRICE'];
    $this->__component->arResult['DISPLAY_BUTTONS'] = $arResult['DISPLAY_BUTTONS'];
    $this->__component->SetResultCacheKeys(array('MAX_PRICE', 'DISPLAY_BUTTONS'));
}
