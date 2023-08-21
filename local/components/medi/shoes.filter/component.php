<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600;


if(/*$arParams['CITIES'] &&*/ $this->StartResultCache(false, serialize($arParams)))
{
    CModule::IncludeModule("iblock");

    $IBLOCK_ID = $arParams['IBLOCK_ID'];
    $BRAND_IBLOCK_ID =  $arParams['BRAND_IBLOCK_ID'];
    $arPropValues = array();

    $forWho = [];
    // для кого
    $dbItems = CIBlockElement::GetList(
        ['PROPERTY_FOR_WHO_SORT'=>'ASC'],
        [
            'IBLOCK_ID' => $IBLOCK_ID,
            'ACTIVE' => 'Y',
            "INCLUDE_SUBSECTIONS"=>"Y",
            "SECTION_ID"=>$arParams["SECTION_ID"],
            ["PROPERTY_REGION_VALUE"=> $GLOBALS['medi']['region_cities'][SITE_ID]]
        ],
        ['PROPERTY_FOR_WHO'], // group
        false,
        ['PROPERTY_FOR_WHO', 'PROPERTY_FOR_WHO_VALUE']
    );
    while($arItem = $dbItems->GetNext(true, false)) {
        if (!empty($arItem['PROPERTY_FOR_WHO_VALUE']))
        {
            $forWho[$arItem['PROPERTY_FOR_WHO_ENUM_ID']] = $arItem;
        }
    }
    ksort($forWho);
    $PROPS['FOR_WHO'] = $forWho;
	//__($PROPS);

    // сезон
    $season = [];
    $dbItems = CIBlockElement::GetList(
        ['PROPERTY_SEASON_SORT'=>'ASC'],
        [
            'IBLOCK_ID' => $IBLOCK_ID,
            'ACTIVE' => 'Y',
            "INCLUDE_SUBSECTIONS"=>"Y",
            "SECTION_ID"=>$arParams["SECTION_ID"],
            ["PROPERTY_REGION_VALUE"=> $GLOBALS['medi']['region_cities'][SITE_ID]]
        ],
        ['PROPERTY_SEASON'], // group
        false,
        ['PROPERTY_SEASON', 'PROPERTY_SEASON_VALUE']
    );
    while($arItem = $dbItems->GetNext(true, false)) {
        if (!empty($arItem['PROPERTY_SEASON_VALUE']))
        {
            $season[$arItem['PROPERTY_SEASON_ENUM_ID']] = $arItem;
        }
    }
    ksort($season);
    $PROPS['SEASON'] = $season;

    $dbItems = CIBlockElement::GetList(
        [],
        [
            'IBLOCK_ID' => $IBLOCK_ID,
            'ACTIVE' => 'Y',
            "INCLUDE_SUBSECTIONS"=>"Y",
            "SECTION_ID"=>$arParams["SECTION_ID"],
            ["PROPERTY_REGION_VALUE"=> $GLOBALS['medi']['region_cities'][SITE_ID]]
        ],
        ['PROPERTY_PRODUCT_TYPE'], // group
        false,
        ['PROPERTY_PRODUCT_TYPE', 'PROPERTY_PRODUCT_TYPE_VALUE']
    );
    while($arItem = $dbItems->GetNext(true, false)) {
        if (!empty($arItem['PROPERTY_PRODUCT_TYPE_VALUE']))
        {
            $PROPS['PRODUCT_TYPE'][] = $arItem;
        }
    }

	foreach($PROPS['PRODUCT_TYPE'] as $key => $value) {

		$offers_id = [];
 		$cntOffers = 0;

		$dbItems = CIBlockElement::GetList(
			['ID'=>'ASC'],
			[
				'IBLOCK_ID' => $IBLOCK_ID,
				'ACTIVE' => 'Y',
				["PROPERTY_REGION_VALUE"=> $GLOBALS['medi']['region_cities'][SITE_ID]],
				'PROPERTY_PRODUCT_TYPE_VALUE' => $value['PROPERTY_PRODUCT_TYPE_VALUE']
			],
			false, // group
			false,
			['ID', "NAME"]
		);
		$items = [];
		while($arItem = $dbItems->GetNext(true, false)) {

			$items[] = $arItem['ID'];

		}
		if (count($items) > 0) {
			$dbOffers = CIBlockElement::GetList(
				['ID'=>'ASC'],
				[
					'IBLOCK_ID' => $OFFERS_IBLOCK_ID,
					'ACTIVE' => 'Y',
					/*"PROPERTY_REGION_VALUE"=> $GLOBALS['medi']['region_cities'][SITE_ID],*/
					'PROPERTY_CML2_LINK' => $items

				],
				false, // group
				false,
				['ID']
			);

			while($arOffer = $dbOffers->GetNext()) {
				$offers_id[] = $arOffer['ID'];
			}
			if (!empty($offers_id)){
				$query = 'SELECT count(UF_OFFER_ID)  FROM `b_mrobuv` as ob WHERE UF_OFFER_ID IN ('.implode(",", $offers_id).')';
				//echo $query."<br>";
				$obRes = $DB->Query($query);

				if($arRes = $obRes->Fetch())
				{
					$cntOffers = $arRes['count(UF_OFFER_ID)'];
				}
			}
			else{

				unset($PROPS['PRODUCT_TYPE'][$key]);
			}
		}
		else{
			unset($PROPS['PRODUCT_TYPE'][$key]);
		}

		if ($cntOffers == '0') unset($PROPS['PRODUCT_TYPE'][$key]);
	}

    $arPropValues = array();
    $dbItems = CIBlockElement::GetList(
        [],
        [
            'IBLOCK_ID' => $IBLOCK_ID,
            'ACTIVE' => 'Y',
            "INCLUDE_SUBSECTIONS"=>"Y",
            "SECTION_ID"=>$arParams["SECTION_ID"],
            ["PROPERTY_REGION_VALUE"=> $GLOBALS['medi']['region_cities'][SITE_ID]]
        ],
        ['PROPERTY_USE_FOR'], // group
        false,
        ['PROPERTY_USE_FOR', 'PROPERTY_USE_FOR_VALUE']
    );
    while($arItem = $dbItems->GetNext(true, false)) {
        if (!empty($arItem['PROPERTY_USE_FOR_VALUE']))
        {
            $PROPS['USE_FOR'][] = $arItem;
        }
    }

    $arrBrands = [];
	$arBrands = [];
	$arrBrandsSort = [];// для сортировки
    $obBrands = CIBlockElement::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>$BRAND_IBLOCK_ID, "ACTIVE"=>"Y", "!PROPERTY_SHOES"=>false), false, false, ['ID', 'NAME', 'SORT']);
    while($brands = $obBrands->GetNext())
    {
        $arrBrands[$brands["SORT"]] = [$brands["ID"], $brands["NAME"]];

    	$arrBrandsSort[$brands["ID"]] = $brands["SORT"];
    }
	ksort($arrBrands);
	foreach($arrBrands AS $k => $v)
	{
		$arBrands[$v[0]] = $v[1];
	}

    $arPropValues = array();
    $dbItems = CIBlockElement::GetList(
        [],
        [
            'IBLOCK_ID' => $IBLOCK_ID,
            'ACTIVE' => 'Y',
            "INCLUDE_SUBSECTIONS"=>"Y",
            "SECTION_ID"=>$arParams["SECTION_ID"],
            "PROPERTY_REGION_VALUE"=> $GLOBALS['medi']['region_cities'][SITE_ID]
        ],
        ['PROPERTY_ATT_BRAND'], // group
        false,
        [ 'PROPERTY_ATT_BRAND', 'PROPERTY_ATT_BRAND_NAME']
    );
    while($arItem = $dbItems->GetNext()) {
        if (!empty($arItem['PROPERTY_ATT_BRAND_VALUE']) &&  $arBrands[$arItem['PROPERTY_ATT_BRAND_VALUE']])
        {
            $arItem['NAME'] = $arBrands[$arItem['PROPERTY_ATT_BRAND_VALUE']];
            $PROPS['ATT_BRAND'][$arrBrandsSort[$arItem['PROPERTY_ATT_BRAND_VALUE']]] = $arItem;

        }
    }
	ksort($PROPS['ATT_BRAND']);

	foreach($PROPS['ATT_BRAND'] as $key => $value) {

		$offers_id = [];
 		$cntOffers = 0;


		$dbItems = CIBlockElement::GetList(
			['ID'=>'ASC'],
			[
				'IBLOCK_ID' => $IBLOCK_ID,
				'ACTIVE' => 'Y',
				"PROPERTY_REGION_VALUE"=> $GLOBALS['medi']['region_cities'][SITE_ID],
				'PROPERTY_ATT_BRAND.ID' => $value['PROPERTY_ATT_BRAND_VALUE']
			],
			false, // group
			false,
			['ID', 'IBLOCK_ID', "NAME"]
		);
		$items = [];
		while($arItem = $dbItems->GetNext(true, false)) {

			$items[] = $arItem['ID'];

		}
		if (count($items) > 0) {
			$dbOffers = CIBlockElement::GetList(
				['ID'=>'ASC'],
				[
					'IBLOCK_ID' => $OFFERS_IBLOCK_ID,
					'ACTIVE' => 'Y',
					/*"PROPERTY_REGION_VALUE"=> $GLOBALS['medi']['region_cities'][SITE_ID],*/
					'PROPERTY_CML2_LINK.ID' => $items

				],
				false, // group
				false,
				['ID', 'IBLOCK_ID']
			);
			$offers_id = [];
			while($arOffer = $dbOffers->GetNext()) {

				$offers_id[$arOffer['ID']] = $arOffer['ID'];
			}
			if (!empty($offers_id)){
				$query = 'SELECT count(UF_OFFER_ID)  FROM `b_mrobuv` as ob WHERE UF_OFFER_ID IN ('.implode(",", $offers_id).')';
				//echo $query."<br>";
				$obRes = $DB->Query($query);

				if($arRes = $obRes->Fetch())
				{
					$cntOffers = $arRes['count(UF_OFFER_ID)'];
					if ($cntOffers > 0 ) $PROPS['ATT_BRAND'][$key]['COUNT'] = $cntOffers;
				}
			}
			else{
				unset($PROPS['ATT_BRAND'][$key]);
			}
		}
		else{
			unset($PROPS['ATT_BRAND'][$key]);
		}

		if ($cntOffers == '0') unset($PROPS['ATT_BRAND'][$key]);
	}
    $arResult['PROPERTIES'] = $PROPS;


    $this->IncludeComponentTemplate();
}
?>
