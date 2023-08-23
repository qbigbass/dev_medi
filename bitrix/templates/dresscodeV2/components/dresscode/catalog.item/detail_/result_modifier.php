<?

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true){
	die();
}

use \Bitrix\Main\Loader;
use \Bitrix\Highloadblock as HL;

if(!empty($arResult)){

	//include modules
	CModule::IncludeModule("catalog");
	CModule::IncludeModule("iblock");
	CModule::IncludeModule("sale");

	//global vars
	global $USER;

	// set vars
	$parentElementId = !empty($arResult["PARENT_PRODUCT"]) ? $arResult["PARENT_PRODUCT"]["ID"] : $arResult["ID"];
	$userId = $USER->GetID();

	// max price отображение старой цены
	if (!empty($arResult['SKU_OFFERS'])){
		foreach($arResult['SKU_OFFERS'] AS $prodid => $offer)
		{
			$obOffer = CIBlockElement::GetList([], ["IBLOCK_ID" => $offer['IBLOCK_ID'], "ID" => $prodid], false, false, ["CATALOG_PRICE_2"]);
			if ($arOffer = $obOffer->GetNext())
			{
				$arResult['MAX_PRICE'][$prodid] = $arOffer['CATALOG_PRICE_2'];
			}
		}
	}

	//__($arResult['IMAGES']);


	// наличие sku в салонах, для показа кнопки забронировать

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
	while ($sStore = $rsProps->GetNext())
	{
		$arResult['SALON_AVAILABLE'] += $sStore['PRODUCT_AMOUNT'];
	}
	// blocks
	//get complect for product

	$arComplectID = array();
	$arResult["COMPLECT"] = array();

	$rsComplect = CCatalogProductSet::getList(
		array("SORT" => "ASC"),
		array(
			"TYPE" => 1,
			"OWNER_ID" => $parentElementId,
			"!ITEM_ID" => $parentElementId
		),
		false,
		false,
		array("*")
	);

	while ($arComplectItem = $rsComplect->Fetch()) {
		$arResult["COMPLECT"]["ITEMS"][$arComplectItem["ITEM_ID"]] = $arComplectItem;
		$arComplectID[$arComplectItem["ITEM_ID"]] = $arComplectItem["ITEM_ID"];
	}

	if(!empty($arComplectID)){

		$arResult["COMPLECT"]["RESULT_PRICE"] = 0;
		$arResult["COMPLECT"]["RESULT_BASE_DIFF"] = 0;
		$arResult["COMPLECT"]["RESULT_BASE_PRICE"] = 0;

		$arSelect = Array("ID", "IBLOCK_ID", "NAME", "DETAIL_PICTURE", "DETAIL_PAGE_URL", "CATALOG_MEASURE");
		$arFilter = Array("ID" => $arComplectID, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
		$rsComplectProducts = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
		while($obComplectProducts = $rsComplectProducts->GetNextElement()){

			$complectProductFields = $obComplectProducts->GetFields();
			if(!empty($arResult["PRODUCT_PRICE_ALLOW"])){
				$arPriceCodes = array();
				foreach($arResult["PRODUCT_PRICE_ALLOW"] as $ipc => $arNextAllowPrice){
					$dbPrice = CPrice::GetList(
				        array(),
				        array(
				            "PRODUCT_ID" => $complectProductFields["ID"],
				            "CATALOG_GROUP_ID" => $arNextAllowPrice["ID"]
				        )
				    );
					if($arPriceValues = $dbPrice->Fetch()){
						$arPriceCodes[] = array(
							"ID" => $arNextAllowPrice["ID"],
							"PRICE" => $arPriceValues["PRICE"],
							"CURRENCY" => $arPriceValues["CURRENCY"],
							"CATALOG_GROUP_ID" => $arNextAllowPrice["ID"]
						);
					}
				}
			}

			if(!empty($arResult["PRODUCT_PRICE_ALLOW"]) && !empty($arPriceCodes) || empty($arParams["PRICE_CODE"]))
				$complectProductFields["PRICE"] = CCatalogProduct::GetOptimalPrice($complectProductFields["ID"], 1, $USER->GetUserGroupArray(), "N", $arPriceCodes);

			$complectProductFields["PRICE"]["DISCOUNT_PRICE"] = $complectProductFields["PRICE"]["DISCOUNT_PRICE"] * $arResult["COMPLECT"]["ITEMS"][$complectProductFields["ID"]]["QUANTITY"];
			$complectProductFields["PRICE"]["DISCOUNT_PRICE"] -= $complectProductFields["PRICE"]["DISCOUNT_PRICE"] * $arResult["COMPLECT"]["ITEMS"][$complectProductFields["ID"]]["DISCOUNT_PERCENT"] / 100;
			$complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] = $complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] * $arResult["COMPLECT"]["ITEMS"][$complectProductFields["ID"]]["QUANTITY"];
			$complectProductFields["PRICE"]["PRICE_DIFF"] = $complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] - $complectProductFields["PRICE"]["DISCOUNT_PRICE"];
			$complectProductFields["PRICE"]["BASE_PRICE_FORMATED"] = CurrencyFormat($complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $arResult["EXTRA_SETTINGS"]["CURRENCY"]);
			$complectProductFields["PRICE"]["PRICE_FORMATED"] = CurrencyFormat($complectProductFields["PRICE"]["DISCOUNT_PRICE"], $arResult["EXTRA_SETTINGS"]["CURRENCY"]);
			$complectProductFields["PICTURE"] = CFile::ResizeImageGet($complectProductFields["DETAIL_PICTURE"], array("width" => 250, "height" => 250), BX_RESIZE_IMAGE_PROPORTIONAL, false);
			$arResult["CATALOG_MEASURE"][$complectProductFields["CATALOG_MEASURE"]] = $complectProductFields["CATALOG_MEASURE"];
			$arResult["COMPLECT"]["RESULT_PRICE"] += $complectProductFields["PRICE"]["DISCOUNT_PRICE"];
			$arResult["COMPLECT"]["RESULT_BASE_PRICE"] += $complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"];
			$arResult["COMPLECT"]["RESULT_BASE_DIFF"] += $complectProductFields["PRICE"]["PRICE_DIFF"];

			$complectProductFields = array_merge(
				$arResult["COMPLECT"]["ITEMS"][$complectProductFields["ID"]],
				$complectProductFields
			);

			//get picture by parent sku product
			if(empty($complectProductFields["PICTURE"]["src"])){
				$skuProductInfo = CCatalogSKU::getProductList($complectProductFields["ID"]);
				if(!empty($skuProductInfo)){
					foreach ($skuProductInfo as $itx => $skuProductInfoElement) {
						$productBySku = CIBlockElement::GetByID($skuProductInfoElement["ID"]);
						if(!empty($productBySku)){
							if($arResProductSku = $productBySku->GetNextElement()){
								$arResProductSkuFields = $arResProductSku->GetFields();
								if(!empty($arResProductSkuFields["DETAIL_PICTURE"])){
									$complectProductFields["PICTURE"] = CFile::ResizeImageGet($arResProductSkuFields["DETAIL_PICTURE"], array("width" => 250, "height" => 250), BX_RESIZE_IMAGE_PROPORTIONAL, false);
								}
							}
						}
					}
				}
			}

			// set empty picture
			if(empty($complectProductFields["PICTURE"]["src"])){
				$complectProductFields["PICTURE"]["src"] = SITE_TEMPLATE_PATH."/images/empty.png";
			}

			$arResult["COMPLECT"]["ITEMS"][$complectProductFields["ID"]] = $complectProductFields;

		}

		$arResult["COMPLECT"]["RESULT_PRICE_FORMATED"] = CurrencyFormat($arResult["COMPLECT"]["RESULT_PRICE"], $arResult["EXTRA_SETTINGS"]["CURRENCY"]);
		$arResult["COMPLECT"]["RESULT_BASE_DIFF_FORMATED"] = CurrencyFormat($arResult["COMPLECT"]["RESULT_BASE_DIFF"], $arResult["EXTRA_SETTINGS"]["CURRENCY"]);
		$arResult["COMPLECT"]["RESULT_BASE_PRICE_FORMATED"] = CurrencyFormat($arResult["COMPLECT"]["RESULT_BASE_PRICE"], $arResult["EXTRA_SETTINGS"]["CURRENCY"]);

	}

	//NOTE: services
	if(!empty($arResult["PROPERTIES"]["SERVICES"]["VALUE"])){

		//globals
		global $servicesFilter;

		//set filter
		$servicesFilter = array("ID" => $arResult["PROPERTIES"]["SERVICES"]["VALUE"], "ACTIVE" => "Y");

	}

	//NOTE: related products
	if (intval($arResult["RELATED_COUNT"]) > 0){

		//filter var for catalog.section
		global $relatedFilter;

		//set filter
		$relatedFilter = array("ID" => $arResult["PROPERTIES"]["RELATED_PRODUCT"]["VALUE"], "ACTIVE" => "Y");

		//show tab flag
		$arResult["SHOW_RELATED"] = "Y";

	}

	//NOTE reviews

	//show form for new review
	$arParams["SHOW_REVIEW_FORM"] = $arParams["USE_REVIEW"] == "Y";
	$reviewProductId = array($arResult["ID"]);
	if(!empty($arResult["PARENT_PRODUCT"])){
		$reviewProductId = $arResult["PARENT_PRODUCT"]["ID"];
	}

	if(!empty($arParams["REVIEW_IBLOCK_ID"])){

		$arSelect = Array("ID", "DATE_CREATE","ACTIVE_FROM", "DETAIL_TEXT", "PROPERTY_DIGNITY", "PROPERTY_SHORTCOMINGS", "PROPERTY_EXPERIENCE", "PROPERTY_GOOD_REVIEW", "PROPERTY_BAD_REVIEW", "PROPERTY_NAME", "PROPERTY_RATING", "PROPERTY_ANSWER");
		$arFilter = Array("IBLOCK_ID" => $arParams["REVIEW_IBLOCK_ID"], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "CODE" => $reviewProductId);
		$rsReviews = CIBlockElement::GetList(Array("ACTIVE_FROM" => "DESC",  "CREATED_DATE"=> "DESC"), $arFilter, false, false, $arSelect);
		$vote_count = 0;
		$vote_sum = 0;
		while($arReviews = $rsReviews->GetNext()){

			$arResult["REVIEWS"][] = $arReviews;

			$vote_count++;
			$vote_sum += $arReviews['PROPERTY_RATING_VALUE'];
		}
		if (count($arResult['REVIEWS']) > 0)
		{
			$arResult['PROPERTIES']['RATING']['VALUE'] = $vote_sum/$vote_count;
			$PROPERTY_VALUES = ['RATING' => $arResult['PROPERTIES']['RATING']['VALUE'], 'VOTE_SUM' => $vote_sum, 'VOTE_COUNT' => $vote_count];
			$res = CIBlockElement::SetPropertyValuesEx($reviewProductId, false, $PROPERTY_VALUES);

		}

		$expEnums = CIBlockPropertyEnum::GetList(Array("DEF" => "DESC", "SORT" => "ASC"), Array("IBLOCK_ID" => $arParams["REVIEW_IBLOCK_ID"], "CODE" => "EXPERIENCE"));
		while($enumValues = $expEnums->GetNext()){
			$arResult["NEW_REVIEW"]["EXPERIENCE"][] = array(
				"ID" => $enumValues["ID"],
				"VALUE" => $enumValues["VALUE"]
			);
		}

		if($userId == $arResult["PROPERTIES"]["USER_ID"]["VALUE"] || $userId == false){
			$arParams["SHOW_REVIEW_FORM"] = false;
		}

	}

	//NOTE similar products
	if (intval($arResult["SIMILAR_COUNT"]) > 0){

		//filter var for catalog.section
		global $similarFilter;

		//set filter
		$similarFilter = $arResult["SIMILAR_FILTER"];

		//show tab flag
		$arResult["SHOW_SIMILAR"] = "Y";

	}

	if($arResult["CATALOG_QUANTITY"] > 0 || $arResult['SALON_AVAILABLE'] > 0){
		if(!empty($arResult["EXTRA_SETTINGS"]["STORES"])){
			$arResult["SHOW_STORES"] = "Y";
		}
	}

	$storeAmount = 0;
	// наличие в салонах
	$filter = array(
		"ACTIVE" => "Y",
		"PRODUCT_ID" => $arParams["PRODUCT_ID"],
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
	while ($mStore = $rsProps->GetNext())
	{
	//__($mStore);
		$storeAmount += $mStore['PRODUCT_AMOUNT'];
	}

	//NOTE tabs



	$arResult["TABS"]["CATALOG_ELEMENT_BACK"] = array("PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco1.png", "NAME" => GetMessage("CATALOG_ELEMENT_BACK"), "LINK" => $arResult["LAST_SECTION"]["SECTION_PAGE_URL"]);
	$arResult["TABS"]["CATALOG_ELEMENT_OVERVIEW"] = array(
		"PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco2.png",
		"NAME" => GetMessage("CATALOG_ELEMENT_OVERVIEW"),
		"ACTIVE" => "Y",
		"ID" => "browse"
	);

    if (!empty($arResult['PROPERTIES']['TECHNOLOGIES'])):
        $arResult["TABS"]["TECHNOLOGIES"] = array(
        "DISABLED" =>  "N",
        "PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco11.png",
        "NAME" => GetMessage("CATALOG_TECHNOLOGIES"),
        "ID" => "tech"
        );


        // Получаем  список технологий товара
        $hlbl = 19; // Указываем ID нашего highloadblock блока к которому будет делать запросы.
        $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();

        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();

        $rsData = $entity_data_class::getList(array(
        "select" => array("*"),
        "order" => array("ID" => "ASC"),
        "filter" => array("UF_XML_ID"=>$arResult['PROPERTIES']['TECHNOLOGIES']['VALUE'])
        ));

        $arTech = [];
        while ($arData = $rsData->Fetch()) {
            if ($arData['UF_FILE'] > 0) {
                $arData["IMG"] = CFile::ResizeImageGet($arData['UF_FILE'], array("width" => 80, "height" => 80), BX_RESIZE_IMAGE_PROPORTIONAL, false);
            }
            $arTech[] = $arData;
        }
        if (!empty($arTech)) {
            $arResult['PROPERTIES']['TECHNOLOGIES']['VALUES_LIST'] = $arTech;
        }
    endif;

    //NOTE Получаем  размерную сетку товара
    if (!empty($arResult['PROPERTIES']['SIZE_CHART'])):

        $hlbl = 5; // Указываем ID нашего highloadblock блока к которому будет делать запросы.
        $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();

        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();

        $rsData = $entity_data_class::getList(array(
        "select" => array("*"),
        "order" => array("ID" => "ASC"),
        "filter" => array("UF_XML_ID"=>$arResult['PROPERTIES']['SIZE_CHART']['VALUE'])  // Задаем параметры фильтра выборки
        ));

        $arSizeChart = [];
        if ($arData = $rsData->Fetch()) {
            if ($arData['UF_FILE'] > 0) {
                $arData["IMG"] = CFile::GetFileArray($arData['UF_FILE']);
            }
			if ($arData['UF_SVG'] > 0) {
                $arData["SVG"] = CFile::GetFileArray($arData['UF_SVG']);
            }
            $arSizeChart = $arData;
        }
        if (!empty($arSizeChart)) {
            $arResult['PROPERTIES']['SIZE_CHART']['VALUES_LIST'] = $arSizeChart;
        }
    endif;

	//NOTE Получаем  документы

	if (!empty($arResult['PROPERTIES']['MANUAL_1C'])):

		$hlbl = 20; // Указываем ID нашего highloadblock блока к которому будет делать запросы.
		$hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();

		$entity = HL\HighloadBlockTable::compileEntity($hlblock);
		$entity_data_class = $entity->getDataClass();

		$rsData = $entity_data_class::getList(array(
		"select" => array("*"),
		"order" => array("ID" => "ASC"),
		"filter" => array("UF_XML_ID"=>$arResult['PROPERTIES']['MANUAL_1C']['VALUE'])  // Задаем параметры фильтра выборки
		));

		$arDoc = [];
		if ($arData = $rsData->Fetch()) {

			if ($arData['UF_FILE'] > 0) {
				$arData["FILE"] = CFile::GetFileArray($arData['UF_FILE']);
			}
			$arDoc = $arData;
		}
		if (!empty($arDoc)) {
			$arDoc['NAME'] = trim(str_replace("[Документы]", "", $arResult['PROPERTIES']['MANUAL_1C']['NAME']));
			$arResult['PROPERTIES']['DOCS']['MANUAL_1C'] = $arDoc;
		}
	endif;
	if (!empty($arResult['PROPERTIES']['SERT_1C'])):

		$hlbl = 23; // Указываем ID нашего highloadblock блока к которому будет делать запросы.
		$hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();

		$entity = HL\HighloadBlockTable::compileEntity($hlblock);
		$entity_data_class = $entity->getDataClass();

		$rsData = $entity_data_class::getList(array(
		"select" => array("*"),
		"order" => array("ID" => "ASC"),
		"filter" => array("UF_XML_ID"=>$arResult['PROPERTIES']['SERT_1C']['VALUE'])  // Задаем параметры фильтра выборки
		));

		$arDoc = [];
		if ($arData = $rsData->Fetch()) {

			if ($arData['UF_FILE'] > 0) {
				$arData["FILE"] = CFile::GetFileArray($arData['UF_FILE']);
			}
			$arDoc = $arData;
		}
		if (!empty($arDoc)) {
			$arDoc['NAME'] = trim(str_replace("[Документы]", "", $arResult['PROPERTIES']['SERT_1C']['NAME']));
			$arResult['PROPERTIES']['DOCS']['SERT_1C'] = $arDoc;
		}
	endif;
	if (!empty($arResult['PROPERTIES']['RU_1C'])):

		$hlbl = 21; // Указываем ID нашего highloadblock блока к которому будет делать запросы.
		$hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();

		$entity = HL\HighloadBlockTable::compileEntity($hlblock);
		$entity_data_class = $entity->getDataClass();

		$rsData = $entity_data_class::getList(array(
		"select" => array("*"),
		"order" => array("ID" => "ASC"),
		"filter" => array("UF_XML_ID"=>$arResult['PROPERTIES']['RU_1C']['VALUE'])  // Задаем параметры фильтра выборки
		));

		$arDoc = [];
		if ($arData = $rsData->Fetch()) {

			if ($arData['UF_FILE'] > 0) {
				$arData["FILE"] = CFile::GetFileArray($arData['UF_FILE']);
			}
			$arDoc = $arData;
		}
		if (!empty($arDoc)) {
			$arDoc['NAME'] = trim(str_replace("[Документы]", "", $arResult['PROPERTIES']['RU_1C']['NAME']));
			$arResult['PROPERTIES']['DOCS']['RU_1C'] = $arDoc;
		}
	endif;
	if (!empty($arResult['PROPERTIES']['DECLARATION_1C'])):

		$hlbl = 22; // Указываем ID нашего highloadblock блока к которому будет делать запросы.
		$hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();

		$entity = HL\HighloadBlockTable::compileEntity($hlblock);
		$entity_data_class = $entity->getDataClass();

		$rsData = $entity_data_class::getList(array(
		"select" => array("*"),
		"order" => array("ID" => "ASC"),
		"filter" => array("UF_XML_ID"=>$arResult['PROPERTIES']['DECLARATION_1C']['VALUE'])  // Задаем параметры фильтра выборки
		));

		$arDoc = [];
		if ($arData = $rsData->Fetch()) {

			if ($arData['UF_FILE'] > 0) {
				$arData["FILE"] = CFile::GetFileArray($arData['UF_FILE']);
			}
			$arDoc = $arData;
		}
		if (!empty($arDoc)) {
			$arDoc['NAME'] = trim(str_replace("[Документы]", "", $arResult['PROPERTIES']['DECLARATION_1C']['NAME']));
			$arResult['PROPERTIES']['DOCS']['DECLARATION_1C'] = $arDoc;
		}
	endif;

	$arResult["TABS"]["CATALOG_ELEMENT_SET"] = array(
		"DISABLED" => CCatalogProductSet::isProductHaveSet($parentElementId, CCatalogProductSet::TYPE_GROUP) ? "N" : "Y",
		"PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco3.png",
		"NAME" => GetMessage("CATALOG_ELEMENT_SET"),
		"ID" => "set"
	);

	$arResult["TABS"]["CATALOG_ELEMENT_COMPLECT"] = array(
		"DISABLED" => !empty($arResult["COMPLECT"]) ? "N" : "Y",
		"PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco3.png",
		"NAME" => GetMessage("CATALOG_ELEMENT_COMPLECT"),
		"ID" => "complect"
	);

	$arResult["TABS"]["CATALOG_ELEMENT_DESCRIPTION"] = array(
		"DISABLED" => !empty($arResult["DETAIL_TEXT"]) ? "N" : "Y",
		"PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco8.png",
		"NAME" => GetMessage("CATALOG_ELEMENT_DESCRIPTION"),
		"ID" => "detailText"
	);

	$arResult["TABS"]["CATALOG_ELEMENT_CHARACTERISTICS"] = array(
		"DISABLED" => !empty($arResult["PROPERTIES"]) ? "N" : "Y",
		"PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco9.png",
		"NAME" => GetMessage("CATALOG_ELEMENT_CHARACTERISTICS"),
		"ID" => "elementProperties"
	);

	$arResult["TABS"]["CATALOG_ELEMENT_ACCEESSORIES"] = array(
		"DISABLED" => $arResult["SHOW_RELATED"] == "Y" ? "N" : "Y",
		"PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco5.png",
		"NAME" => GetMessage("CATALOG_ELEMENT_ACCEESSORIES"),
		"ID" => "related"
	);

	$arResult["TABS"]["CATALOG_ELEMENT_REVIEW"] = array(
		"DISABLED" => !empty($arResult["REVIEWS"]) ? "N" : "Y",
		"PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco4.png",
		"NAME" => GetMessage("CATALOG_ELEMENT_REVIEW"),
		"ID" => "catalogReviews"
	);

	$arResult["TABS"]["CATALOG_ELEMENT_SIMILAR"] = array(
		"DISABLED" => $arResult["SHOW_SIMILAR"] == "Y" ? "N" : "Y",
		"PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco6.png",
		"NAME" => GetMessage("CATALOG_ELEMENT_SIMILAR"),
		"ID" => "similar"
	);

	$arResult['DONT_SHOW_REST'] = false;
	if(isset($arResult['PROPERTIES']['DONT_SHOW_REST']['VALUE'])
		&& $arResult['PROPERTIES']['DONT_SHOW_REST']['VALUE'] == 'Да'
	)
	{
		$arResult['DONT_SHOW_REST'] = true;
	}
	else{
		$arResult["TABS"]["CATALOG_ELEMENT_AVAILABILITY"] = array(
			"DISABLED" =>
			 $arResult["SHOW_STORES"] == "Y" &&
			 $arParams["HIDE_AVAILABLE_TAB"] != "Y"
					&& empty($arResult["COMPLECT"]["ITEMS"])
					&& !$arResult['DONT_SHOW_REST']  // не установлена опчия скрывать салоны
					&& $arResult['SALON_AVAILABLE'] > 0  // есть в слалонах
					 ? "N" : "Y",
			"PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco7.png",
			"NAME" => GetMessage("CATALOG_ELEMENT_AVAILABILITY"),
			"ID" => "stores"
		);
	}
	$arResult["TABS"]["CATALOG_ELEMENT_FILES"] = array(
		"DISABLED" => !empty($arResult["PROPERTIES"]['DOCS']) ? "N" : "Y",
		"PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco11.png",
		"NAME" => GetMessage("CATALOG_ELEMENT_FILES"),
		"ID" => "files"
	);

	$arResult["TABS"]["CATALOG_ELEMENT_VIDEO"] = array(
		"DISABLED" => !empty($arResult["VIDEO"]) ? "N" : "Y",
		"PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco10.png",
		"NAME" => GetMessage("CATALOG_ELEMENT_VIDEO"),
		"ID" => "video"
	);



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

?>
