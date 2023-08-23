<?
\Bitrix\Main\Loader::registerAutoLoadClasses(
	"dw.deluxe",
	array(
		//new namespace
		"\DigitalWeb\Basket" => "classes/general/basket.php",
		"\DigitalWeb\BasketAjax" => "classes/general/basket-ajax.php",
		//old
		"DwSkuOffers" => "classes/general/sku-offers.php",
		"DwProductEvents" => "classes/general/product-events.php",
		"DwItemInfo" => "classes/general/item-info.php",
		"DwSettings" => "classes/general/settings.php",
		"DwBuffer" => "classes/general/buffer.php",
		"DwPrices" => "classes/general/prices.php",
		"DwBonus" => "classes/general/bonus.php"
	)
);
//deluxe events
//bonus events
$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->registerEventHandler("sale", "OnSaleOrderPaid", "dw.deluxe", "DwBonus", "addBonus");
//buffer events
$eventManager->registerEventHandler("main", "OnEndBufferContent", "dw.deluxe", "DwBuffer", "modifyBuffer");
//product events
//catalog product
$eventManager->registerEventHandler("catalog", "Bitrix\Catalog\Model\Product::".Bitrix\Main\Entity\DataManager::EVENT_ON_AFTER_ADD, "dw.deluxe", "DwProductEvents", "productUpdate");
$eventManager->registerEventHandler("catalog", "Bitrix\Catalog\Model\Product::".Bitrix\Main\Entity\DataManager::EVENT_ON_AFTER_UPDATE, "dw.deluxe", "DwProductEvents", "productUpdate");
//iblock
$eventManager->registerEventHandler("iblock", "OnAfterIBlockElementUpdate", "dw.deluxe", "DwProductEvents", "productAfterSave");
$eventManager->registerEventHandler("iblock", "OnAfterIBlockElementAdd", "dw.deluxe", "DwProductEvents", "productAfterSave");
//catalog price
$eventManager->registerEventHandler("catalog", "OnPriceUpdate", "dw.deluxe", "DwProductEvents", "productAfterSave");
$eventManager->registerEventHandler("catalog", "OnPriceAdd", "dw.deluxe", "DwProductEvents", "productAfterSave");
?>