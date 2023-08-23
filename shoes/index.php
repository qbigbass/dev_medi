<?
define("SHOES_PAGE", "Y");
ini_set("display_errors", 1);
ini_set("error_reporting", E_ALL ^ E_NOTICE ^ E_DEPRECATED);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Подбор обуви");
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/shoes.css");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/shoes.js");
?>

<div class="limiter">

	<?$APPLICATION->IncludeComponent("bitrix:breadcrumb", ".default", Array(
		"START_FROM" => "0",
			"PATH" => "",
			"SITE_ID" => "-",
		),
		false
	);?>
	<h1 class="ff-medium"><?$APPLICATION->ShowTitle();?></h1>
</div>

<? $APPLICATION->IncludeComponent(
	"medi:shoes.filter",
	".default",
	Array(
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"SECTION_ID" => 88,
		"IBLOCK_ID" => 17,
		"OFFERS_IBLOCK_ID" => 19,
		"BRAND_IBLOCK_ID" => 1
 	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
