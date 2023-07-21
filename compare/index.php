<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Сравнение товаров");
?><h1>Список сравнения</h1>
<?$APPLICATION->IncludeComponent(
	"dresscode:catalog.compare", 
	".default", 
	array(
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "17",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "360000",
		"COMPONENT_TEMPLATE" => ".default",
		"PRODUCT_PRICE_CODE" => array(
			0 => "BASE",
		),
		"LAZY_LOAD_PICTURES" => "Y"
	),
	false
);?><br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>