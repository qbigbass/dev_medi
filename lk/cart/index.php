<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Корзина");
?>
<h1>Корзина</h1>
<?$APPLICATION->IncludeComponent(
	"dresscode:sale.basket.basket",
	"standartOrder2", 
	array(
		"COMPONENT_TEMPLATE" => "standartOrder",
		"PATH_TO_PAYMENT" => "",
		"MIN_SUM_TO_PAYMENT" => "",
		"BASKET_PICTURE_WIDTH" => "",
		"BASKET_PICTURE_HEIGHT" => "",
		"REGISTER_USER" => "Y",
		"LAZY_LOAD_PICTURES" => "Y",
		"HIDE_MEASURES" => "Y",
		"USE_MASKED" => "Y",
		"DISABLE_FAST_ORDER" => "Y",
		"MASKED_FORMAT" => "+7 (999) 999-99-99",
		"HIDE_NOT_AVAILABLE" => "N",
		"PRODUCT_PRICE_CODE" => array(
			0 => "BASE",
		),
		"GIFT_CONVERT_CURRENCY" => "Y",
		"GIFT_CURRENCY_ID" => "RUB",
		"PART_STORES_AVAILABLE" => "",
		"ALL_STORES_AVAILABLE" => "",
		"NO_STORES_AVAILABLE" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
