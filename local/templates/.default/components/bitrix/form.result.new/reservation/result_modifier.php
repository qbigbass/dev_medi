<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CModule::IncludeModule('sale');
CModule::IncludeModule('catalog');

$offer_id = $_REQUEST['p'];

$sFilter  = array(
	"PRODUCT_ID"=>$offer_id,
	"ACTIVE" =>"Y",
	">PRODUCT_AMOUNT" => 0,
	"ISSUING_CENTER" => "Y",
	"UF_SALON"=>true
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

if (!empty($arStores))
{
	$elmId = $arStores[0]['ELEMENT_ID'];

	$props = array(
		"NAME",
		"PROPERTY_CML2_ARTICLE",
		'PROPERTY_CML2_LINK',
		"CATALOG_PRICE_1",
		"CATALOG_PRICE_2",
		'PROPERTY_ATT_BRAND.NAME',
		'DETAIL_PAGE_URL',
		'IBLOCK_SECTION_ID'
		);

		$obOffer = CIblockElement::GetList(array(), array("IBLOCK_ID"=>19, "ID"=>$offer_id), false, false, $props);
		if ($arOffer = $obOffer->GetNext())
		{
			if($arOffer['PROPERTY_CML2_LINK_VALUE'] > 0)
			{
				$obParent = CIblockElement::GetList(array(), array("IBLOCK_ID"=>17, "ID"=>$arOffer['PROPERTY_CML2_LINK_VALUE']), false, false, $props);
				if ($arParent = $obParent->GetNext())
				{ 
					$arOffer['PROPERTY_ATT_BRAND_NAME'] = $arParent['PROPERTY_ATT_BRAND_NAME'];
					$arOffer['IBLOCK_SECTION_ID'] = $arParent['IBLOCK_SECTION_ID'];
					$arOffer['DETAIL_PAGE_URL'] = $arParent['DETAIL_PAGE_URL'];
				}

			}
			$arResult['OFFER'] = $arOffer;
		}else {
			$obOffer = CIblockElement::GetList(array(), array("IBLOCK_ID"=>17, "ID"=>$offer_id), false, false, $props);
			if ($arOffer = $obOffer->GetNext())
			{
				$arResult['OFFER'] = $arOffer;
			}
		}
		if ($arResult['OFFER']['DETAIL_PAGE_URL'])
		{
			$cat = explode("/", $arResult['OFFER']['DETAIL_PAGE_URL'] );
			array_pop($cat);array_shift($cat);array_shift($cat);
			$arResult['OFFER']['CATEGORY'] = implode("/", $cat);
		}
		$arResult['SALON'] = $arStores;
		$arResult['OFFERID'] = $offer_id;
	}
