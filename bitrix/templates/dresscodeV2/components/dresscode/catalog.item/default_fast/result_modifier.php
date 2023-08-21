<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true){
	die();
}

if(!empty($arResult)){
	CModule::IncludeModule("catalog");
	CModule::IncludeModule("iblock");
	CModule::IncludeModule("sale");

    if ($arParams['CACHE_TYPE'] === 'A') {
        $taggedCache = \Bitrix\Main\Application::getInstance()->getTaggedCache();
        $taggedCache->abortTagCache(); // сбрасываем стандартные теги

        $tag = 'iblock_id_' . $arParams['IBLOCK_ID'] . '_chunk_' . intval($arResult['ID'] / IBLOCK_CHUNK_SIZE);

        $taggedCache->startTagCache($this->__component->getCachePath());
        $taggedCache->registerTag($tag); // Ставим свой тег
    }

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
	while ($sStore = $rsProps->GetNext())
	{
		$arResult['SALON_AVAILABLE'] += $sStore['PRODUCT_AMOUNT'];

		if ($sStore['PRODUCT_AMOUNT'] > 0){
			$arResult['SALON_COUNT']++;
		}
	}

	$maxprice_id =  $GLOBALS['medi']['max_price_id'][SITE_ID];

	// max price отображение старой цены
	if (!empty($arResult['SKU_OFFERS'])){
		$obOffer = CIBlockElement::GetList([], ["IBLOCK_ID" => $offer['IBLOCK_ID'], "ID" => array_keys($arResult['SKU_OFFERS'])], false, false, ["ID", "CATALOG_PRICE_".$maxprice_id]);
		while ($arOffer = $obOffer->GetNext())
		{
			$arResult['MAX_PRICE'][$arOffer['ID']] = $arOffer['CATALOG_PRICE_'.$maxprice_id];
		}
	}
	else {
		$obOffer = CIBlockElement::GetList([], ["IBLOCK_ID" => $arResult['IBLOCK_ID'], "ID" => array_keys($arResult['ID'])], false, false, ["ID", "CATALOG_PRICE_".$maxprice_id]);
		while ($arOffer = $obOffer->GetNext())
		{
			$arResult['MAX_PRICE'][$arOffer['ID']] = $arOffer['CATALOG_PRICE_'.$maxprice_id];
		}
	}


	//NOTE Управление показом кнопок


	// "Возможно изготовление на заказ"
	$arResult['DISPLAY_BUTTONS']['MTM_BUTTON'] = false;
	if(isset($arResult['PROPERTIES']['MTM']['VALUE'])
		&& $arResult['PROPERTIES']['MTM']['VALUE'] == 'Да'
	)
	{
		$arResult['DISPLAY_BUTTONS']['MTM_BUTTON'] = true;
	}

	// "Только под заказ"
	$arResult['DISPLAY_BUTTONS']['ONLY_ORDER_BUTTON'] = false;
	if(isset($arResult['PROPERTIES']['ORDER_ONLY']['VALUE'])
		&& $arResult['PROPERTIES']['ORDER_ONLY']['VALUE'] == 'Да'
	)
	{
		$arResult['DISPLAY_BUTTONS']['ONLY_ORDER_BUTTON'] = true;
	}

	// Показывать или скрывать кнопку "В корзину"
	$arResult['DISPLAY_BUTTONS']['CART_BUTTON'] = true;
	if(isset($arResult['PROPERTIES']['NO_CART_BUTTON']['VALUE'])
		&& $arResult['PROPERTIES']['NO_CART_BUTTON']['VALUE'] == 'Да'
	)
	{
		$arResult['DISPLAY_BUTTONS']['CART_BUTTON'] = false;
	}

	// Показывать или скрывать кнопку "Забронировать в салоне"
	$arResult['DISPLAY_BUTTONS']['RESERV_BUTTON'] = true;
	if(isset($arResult['PROPERTIES']['NO_RESERV_BUTTON']['VALUE'])
		&& $arResult['PROPERTIES']['NO_RESERV_BUTTON']['VALUE'] == 'Да'
	)
	{
		$arResult['DISPLAY_BUTTONS']['RESERV_BUTTON'] = false;
	}
	// Показывать или скрывать кнопку "Запись на изготовление стелек"
	$arResult['DISPLAY_BUTTONS']['INSOLE_BUTTON'] = false;
	if(isset($arResult['PROPERTIES']['INSOLE_BUTTON']['VALUE'])
		&& $arResult['PROPERTIES']['INSOLE_BUTTON']['VALUE'] == 'Да'
	)
	{
		$arResult['DISPLAY_BUTTONS']['INSOLE_BUTTON'] = true;
	}
}
