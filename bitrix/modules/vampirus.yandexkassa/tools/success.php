<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

CModule::IncludeModule('sale');

$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$APPLICATION->SetTitle(GetMessage("VAMPIRUS.YANDEXKASSA_SUCCESS_TITLE"));

$orderID = $_REQUEST['OrderId'];
$order = CSaleOrder::GetByID($orderID);

if($order){
	$statusPageURL = sprintf('%s/%s', GetPagePath('personal/orders'), (int)$orderID);
}

?>

<?php if (!($arOrder = CSaleOrder::GetByID($orderID))): ?>
	<?=GetMessage("VAMPIRUS.YANDEXKASSA_SUCCESS_NOTFOUND", array('#ORDER_ID#' => htmlspecialchars($orderID)))?>
<?php else: ?>
	<?=GetMessage("VAMPIRUS.YANDEXKASSA_SUCCESS_THNX")?><br/>
	<?=GetMessage("VAMPIRUS.YANDEXKASSA_SUCCESS_LINK", array('#LINK#' => $statusPageURL))?>
<?php endif; ?>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>