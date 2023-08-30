<?
\Bitrix\Main\Loader::registerAutoLoadClasses(
	"dw.deluxe",
	array(
		"DwSKU" => "classes/general/sku.php",
		"DwSkuOffers" => "classes/general/sku-offers.php",
		"DwItemInfo" => "classes/general/item-info.php",
		"DwPrices" => "classes/general/prices.php",
		"DwBonus" => "classes/general/bonus.php"
	)
);
//bonus handler
RegisterModuleDependences("sale", "OnSalePayOrder", "dw.deluxe", "DwBonus", "addBonus");
?>