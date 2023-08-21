<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true){
	die();
}

if(!empty($arResult)){

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

	// max price отображение старой цены
	if (!empty($arResult['SKU_OFFERS'])){

		$dbPriceType = CCatalogGroup::GetList(
				array("SORT" => "ASC"),
				array("NAME"=>$GLOBALS['medi']['max_price'][SITE_ID])
			);
			$priceid = 2;
		if ($arPriceType = $dbPriceType->Fetch())
		{
			$priceid = $arPriceType['ID'];
		}

		foreach($arResult['SKU_OFFERS'] AS $prodid => $offer)
		{
			$obOffer = CIBlockElement::GetList([], ["IBLOCK_ID" => $offer['IBLOCK_ID'], "ID" => $prodid], false, false, ["CATALOG_PRICE_".$priceid]);
			if ($arOffer = $obOffer->GetNext())
			{
				$arResult['MAX_PRICE'][$prodid] = $arOffer['CATALOG_PRICE_'.$priceid];
			}
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
