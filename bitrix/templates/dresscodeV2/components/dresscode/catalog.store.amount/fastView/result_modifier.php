<?
$def_city  = $GLOBALS['medi']['region_cities'][$arResult['STORES'][0]['SITE_ID']];


$sumAmount  = 0; // общее количество товара в салонах
$canOrder = 0;  // Есть ли склады с возможностью заказа
$mainStoreAmount = 0; // количество на складах

// наличие на основных складах
$filter = array(
	"ACTIVE" => "Y",
	"PRODUCT_ID" => $arParams["ELEMENT_ID"],
	"+SITE_ID" => SITE_ID,
	"SHIPPING_CENTER" => 'Y',
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
	$mainStoreAmount += $mStore['PRODUCT_AMOUNT'];
}

if(!empty($arResult["STORES"])){
	foreach ($arResult["STORES"] as $ist => $arStore) {

		$pfx= '';
		if ($GLOBALS['medi']['sfolder'][$arStore['SITE_ID']] != '')
		{
			$pfx = "/".$GLOBALS['medi']['sfolder'][$arStore['SITE_ID']] ;
		}
		$arResult["STORES"][$ist]['DETAIL_PAGE_URL']  = $pfx.'/salons/'.$arStore['CODE'].'/';

		$sumAmount += $arStore['PRODUCT_AMOUNT'];

		if($arStore["REAL_AMOUNT"] > 0 ||   ($mainStoreAmount > 0  && $arStore['UF_ESHOP_ORDERS'] == '1')){
			$arResult["SHOW_STORES"] = "Y";
			//break(1);
		}
        // Если товара нет и заказ не возможен, исключаем склад
        if ($arStore["REAL_AMOUNT"] == 0 && $arStore['UF_ESHOP_ORDERS'] == '0')
        {
            unset($arResult["STORES"][$ist]);
        }
	}

}

if ($sumAmount > 0) $arResult["SHOW_STORES"] = "Y";
