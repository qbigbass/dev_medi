<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CModule::IncludeModule('sale');
CModule::IncludeModule('catalog');

$arResult['HIDE_FIELDS'] = [];
if (!empty($arParams['HIDDEN_FIELDS']))
{
	foreach ($arParams['HIDDEN_FIELDS'] as $key) {
		$arResult['HIDE_FIELDS'][] = $arResult['arAnswers'][$key][0]['ID'];
	}
}

if (intval($_REQUEST['p']) > 0){

$offer_id = $_REQUEST['p'];

$sFilter  = array(
	"PRODUCT_ID"=>$offer_id,
	"ACTIVE" =>"Y",
	"ISSUING_CENTER" => "Y",
	"UF_SALON"=>true,
	"UF_ESHOP_ORDERS" => true
);


$def_city  = $GLOBALS['medi']['site_order'][SITE_ID];
$sFilter['UF_CITY'] = $def_city;

$resStore = CCatalogStore::GetList(array("SORT"=>"ASC"), $sFilter, false, false, array("ID", "SCHEDULE",  "ADDRESS", "DESCRIPTION", "ACTIVE","PRODUCT_AMOUNT","ELEMENT_ID", "UF_METRO"));
while($sklad = $resStore->Fetch())
{
	$sklad['ADDRESS'] = preg_replace("/[0-9]{6},/", "", $sklad["ADDRESS"]);
	$metro = unserialize($sklad['UF_METRO']);
	if (!empty($metro[0]))
	{
			$rsElm = CIBlockElement::GetList(array(), array("ID" => $metro[0], "IBLOCK_ID" => "23", "ACTIVE"=>"Y"), false, false, array("ID", "NAME", "IBLOCK_SECTION_ID"));
			if ($arMetro = $rsElm -> GetNext()) {

				$rsSect = CIBlockSection::GetList(array("NAME"=>"ASC"), array( "IBLOCK_ID" => "23", "ACTIVE"=>"Y", "ID"=> $arMetro['IBLOCK_SECTION_ID']), false, array("NAME", "PICTURE", "IBLOCK_SECTION_ID" ));
				if ($arSect = $rsSect->GetNext()) {
					if ($arSect['PICTURE'] > 0) {
						$arSect['ICON'] = CFile::GetFileArray($arSect["PICTURE"]);
					}
					$arMetro['SECTION'] = $arSect;
				}
				  $sklad['METRO'] = $arMetro;
			}

	}

	$arStores[] = $sklad;
}
}
if (!empty($_REQUEST['p']))
{
	$elmId = $arStores[0]['ELEMENT_ID'];

	$props = array(
		"NAME",
		"PROPERTY_CML2_ARTICLE",
		);

	$obOffer = CIblockElement::GetList(array(), array("IBLOCK_ID"=>19, "ID"=>$offer_id), false, false, $props);
	if ($arOffer = $obOffer->GetNext())
	{
		$arResult['OFFER'] = $arOffer;
	}

	$arResult['SALON'] = $arStores;
	$arResult['OFFERID'] = $offer_id;
}
