<?
error_reporting(E_ALL);
ini_set("display_errors", 1);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Мои заказы");

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/lk.css");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/lk.js');


include("pages/init.inc.php");

if ($USER->IsAuthorized())
{



$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.order",
	".default",
	array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/lk/",
		"ORDERS_PER_PAGE" => "10",
		"PATH_TO_PAYMENT" => "/lk/orders/payment/",
		"PATH_TO_BASKET" => "/lk/cart/",
		"SET_TITLE" => "Y",
		"SAVE_IN_SESSION" => "N",
		"NAV_TEMPLATE" => "round",
		"SHOW_ACCOUNT_NUMBER" => "Y",
		"COMPONENT_TEMPLATE" => ".default",
		"PROP_1" => array(
		),
		"PROP_2" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_GROUPS" => "Y",
		"CUSTOM_SELECT_PROPS" => array(
		),
		"HISTORIC_STATUSES" => array(
			0 => "",
		),
		"DETAIL_HIDE_USER_INFO" => array(
			0 => "0",
		),
		"PATH_TO_CATALOG" => "/catalog/",
		"DISALLOW_CANCEL" => "N",
		"RESTRICT_CHANGE_PAYSYSTEM" => array(
			0 => "0",
		),
		"REFRESH_PRICES" => "N",
		"ORDER_DEFAULT_SORT" => "DATE_INSERT",
		"ALLOW_INNER" => "N",
		"ONLY_INNER_FULL" => "N",
		"SEF_URL_TEMPLATES" => array(
			"list" => "?orders",
			"detail" => "orders/detail/#ID#/",
			"cancel" => "orders/cancel/#ID#/",
		)
	),
	false
);?>

<?}else {
	$backurl = !empty($_REQUEST['BACKURL']) ? $_REQUEST['BACKURL'] : '/lk/';
	LocalRedirect($backurl);
}?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
