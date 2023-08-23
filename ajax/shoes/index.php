<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Grid\Declension;

$action = '';
if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], array('search', 'show_result'))) {
    $action = strval($_REQUEST['action']);
} else
    die();

if ($action == 'search') {

    $itemID = [];  // Данные HL MRObuv
    $IBitemID = []; // Данные IB 17 основной каталог
    $IBofferID = []; // Данные IB 19 sku

    $result = shoesFilter();

    $matches = countMatches();

    $itemID = $result[0];
    $IBitemID = $result[1];
    $IBofferID = $result[2];


    if (!empty($IBofferID) )
    {

        $arResult = array_intersect($itemID, $IBofferID);

        $arRes['find'] = count($arResult);
        $arRes['arr'] = $arResult;
        $arRes['matches'] = $matches;
    }
    else {
        $arRes['find'] = 0;
    }


    $word = new Declension('товар', 'товара', 'товаров');

    $arRes['word']  = $word->get($arRes['find']);

	echo json_encode($arRes);

/*	}
	else{
		echo json_encode(array("status"=>"empty"));
	}*/
}
elseif ($action == 'show_result')
{
    $itemID = [];  // Данные HL MRObuv
    $IBitemID = []; // Данные IB 17 основной каталог
    $IBofferID = []; // Данные IB 19 sku

    $result = shoesFilter();

    $itemID = $result[0];
    $IBitemID = $result[1];
    $IBofferID = $result[2];

    $arResult = array_intersect($itemID, $IBofferID);

    if (empty($arResult))
    {
        echo "По вашему запросу ничего не найдено. Утончите запрос.";
    }
    else{


        CModule::IncludeModule("iblock");
        $arResultID = [];
        $offElement = CIBlockElement::GetList([], ['IBLOCK_ID'=>19, 'ID'=>$arResult, 'ACTIVE'=>"Y","PROPERTY_REGION_VALUE"=> $GLOBALS['medi']['region_cities'][SITE_ID] ], false, false, ["IBLOCK_ID", "ID",'PROPERTY_CML2_LINK']);
        while($ofElm = $offElement->GetNext() )
        {
            $arResultID[] = $ofElm['PROPERTY_CML2_LINK_VALUE'];
        }

        $GLOBALS['shoesFilter'] = ['ID'=>$arResultID  ];

        echo "<Br><h2 class='ff-medium h2'>Результаты подбора:</h2><br>";

        $pos_counter = 1;
        foreach ($arResult as $index => $arElement):
            $APPLICATION->IncludeComponent(
                "dresscode:catalog.item",
                "shoes",
                array(
                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                    "HIDE_MEASURES" => "Y",
                    "HIDE_NOT_AVAILABLE" => "Y",
                    "IBLOCK_ID" => 19,
                    "IBLOCK_TYPE" => 'catalog',
                    "PRODUCT_ID" => $arElement,
                    "PRODUCT_SKU_FILTER" => ['ID'=>$arElement],//['ID' => $arResult],
                    "PICTURE_HEIGHT" => "",
                    "PICTURE_WIDTH" => "",
                    "PRODUCT_PRICE_CODE" => array(
            			0 => "BASE",
            		),
                    "CONVERT_CURRENCY" => "N",
                    "LAZY_LOAD_PICTURES" => "N",
                    "CURRENCY_ID" => "RUB",
                    "POS_COUNT" => $pos_counter
                ),
                false,
                array("HIDE_ICONS" => "Y")
            );
            $pos_counter++;
        endforeach;?>
        <div class="clear"></div>
        <?
    }

}


function shoesFilter (){
    global $DB;

    $filter = [];

    $check_avail = array();
	if (!empty($_REQUEST['fullness']) &&  floatval($_REQUEST['fullness']) >= 10) {
        $filter['fullness'] = floatval($_REQUEST['fullness']);
    }
    if (!empty($_REQUEST['length'] &&  floatval($_REQUEST['length']) >= 10)) {
        $filter['length'] = floatval($_REQUEST['length']);
    }
    if (!empty($_REQUEST['for_who']))
    {
        foreach($_REQUEST['for_who'] AS $k=>$fw)
        {
            $filter['for_who'][] = intval($fw);
        }
    }
    if (!empty($_REQUEST['season']))
    {
        foreach($_REQUEST['season'] AS $k=>$fw)
        {
            $filter['season'][] = intval($fw);
        }
    }
    if (!empty($_REQUEST['medical']))
    {
        foreach($_REQUEST['medical'] AS $k=>$fw)
        {
            $filter['medical'][] = intval($fw);
        }
    }
    if (!empty($_REQUEST['brandOf']))
    {
        foreach($_REQUEST['brandOf'] AS $k=>$fw)
        {
            $filter['brandOf'][] = intval($fw);
        }
    }
    if (!empty($_REQUEST['offerType']))
    {
        foreach($_REQUEST['offerType'] AS $k=>$fw)
        {
            $filter['offerType'][] = intval($fw);
        }
    }


	if (!empty($filter))
	{
		$query = 'SELECT DISTINCT ob.ID, UF_OFFER_ID AS offer_id  FROM `b_mrobuv` as ob WHERE UF_OFFER_ID > 0 ';
		$q_str = '';
		$q_tables = '';


		if (!empty($filter['length']))
		{
			$q_str .= " AND (((ob.UF_DLINASTOPYMIN <= '".$filter['length']."' AND ob.UF_DLINASTOPYMAKS >= '".$filter['length']."') OR  ob.UF_DLINASTOPY = '".$filter['length']."' ))  ";
		}
		if (!empty($filter['fullness']))
		{
            $q_str .= " AND ((ob.UF_POLNOTASTOPYMIN <= '".$filter['length']."' AND ob.UF_POLNOTASTOPYMAKS >= '".$filter['length']."' ) OR (ob.UF_POLNOTASTOPYMIN IS NULL  AND ob.UF_POLNOTASTOPYMAKS >= '".$filter['length']."')  OR (ob.UF_POLNOTASTOPYMAKS IS NULL  AND ob.UF_POLNOTASTOPYMIN <= '".$filter['length']."') )";
        }



		$obRes = $DB->Query($query.$q_str);
        $itemID = [];
		while ($arRes = $obRes->Fetch())
		{
            $itemID[] = $arRes['offer_id'];
			//$arRes['query'] = $query.$q_str;

		}

        $arPropFilter = [];
        if (!empty($filter['offerType']))
		{
            $arPropFilter[] = ['PROPERTY_PRODUCT_TYPE' => $filter['offerType']];
        }
        if (!empty($filter['season']))
		{
            $arPropFilter[] = ['PROPERTY_SEASON' => $filter['season']];
        }
        if (!empty($filter['medical']))
		{
            $arPropFilter[] = ['PROPERTY_USE_FOR' => $filter['medical']];
        }
        if (!empty($filter['brandOf']))
		{
            $arPropFilter['PROPERTY_ATT_BRAND.ID'] = $filter['brandOf'];
        }
        if (!empty($filter['for_who']))
		{
            $arPropFilter['PROPERTY_FOR_WHO'] = $filter['for_who'];
        }


        CModule::IncludeModule("iblock");
        $arFilter = ['IBLOCK_ID' => 17, 'ACTIVE' => 'Y',"PROPERTY_REGION_VALUE"=> $GLOBALS['medi']['region_cities'][SITE_ID]];
        if (!empty($arPropFilter))
        {
            $arFilter[] = ["LOGIC"=>"AND", $arPropFilter];
        }
        $obElement = CIBlockElement::GetList([], $arFilter, false, false, ['ID']);
        $IBitemID = [];
        while($arElm = $obElement->GetNext())
        {
            $IBitemID[] = $arElm['ID'];
        }
        if (!empty($IBitemID)){
            $IBofferID = [];
            $offElement = CIBlockElement::GetList([], ['IBLOCK_ID'=>19, 'PROPERTY_CML2_LINK.ID'=>$IBitemID, 'ACTIVE'=>"Y","PROPERTY_REGION_VALUE"=> $GLOBALS['medi']['region_cities'][SITE_ID]], false, false, ['ID']);
            while($ofElm = $offElement->GetNext() )
            {
                $IBofferID[] = $ofElm['ID'];
            }
        }
    }
    return [$itemID, $IBitemID, $IBofferID];
}

function getShoesProps(){
    CModule::IncludeModule("iblock");

    $IBLOCK_ID = 17;
    $BRAND_IBLOCK_ID =  1;
    $arPropValues = array();


    $dbItems = CIBlockElement::GetList(
        [],
        [
            'IBLOCK_ID' => $IBLOCK_ID,
            'ACTIVE' => 'Y',
            "INCLUDE_SUBSECTIONS"=>"Y",
            "SECTION_ID"=>88,
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

    $arPropValues = array();
    $dbItems = CIBlockElement::GetList(
        [],
        [
            'IBLOCK_ID' => $IBLOCK_ID,
            'ACTIVE' => 'Y',
            "INCLUDE_SUBSECTIONS"=>"Y",
            "SECTION_ID"=>88,
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

    $arBrands = [];
    $obBrands = CIBlockElement::GetList(Array("NAME"=>"ASC"), Array("IBLOCK_ID"=>$BRAND_IBLOCK_ID, "ACTIVE"=>"Y"), false, false, ['ID', 'NAME']);
    while($brands = $obBrands->GetNext())
    {
      $arBrands[$brands["ID"]] = $brands["NAME"];
    }

    $arPropValues = array();
    $dbItems = CIBlockElement::GetList(
        [],
        [
            'IBLOCK_ID' => $IBLOCK_ID,
            'ACTIVE' => 'Y',
            "INCLUDE_SUBSECTIONS"=>"Y",
            "SECTION_ID"=>88,
            ["PROPERTY_REGION_VALUE"=> $GLOBALS['medi']['region_cities'][SITE_ID]]
        ],
        ['PROPERTY_ATT_BRAND'], // group
        false,
        [ 'PROPERTY_ATT_BRAND', 'PROPERTY_ATT_BRAND_NAME']
    );
    while($arItem = $dbItems->GetNext()) {
        if (!empty($arItem['PROPERTY_ATT_BRAND_VALUE']))
        {
            $arItem['NAME'] = $arBrands[$arItem['PROPERTY_ATT_BRAND_VALUE']];
            $PROPS['ATT_BRAND'][] = $arItem;
                //__($arItem);
        }
    }

    return $PROPS;
}

function countMatches(){

    $PROPS = getShoesProps();

    foreach ($PROPS as $key => $value) {
        if (is_array($value))
        {
            //echo $key;
            foreach($value as $kk=>$prop)
            {
                if ($prop['CNT'] > 0){

                    //print_r($prop);
                }
                else {
                    // code...
                }
            }
        }
    }
    //return true;
}
