<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

CModule::IncludeModule('sale');
CModule::IncludeModule('vampirus.yandexkassa');

$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$APPLICATION->SetTitle(GetMessage("VAMPIRUS.YANDEXKASSA_PAYMENT_RESULT_TITLE"));



$paymentId = $_REQUEST['id'];
$order = false;
$class = 'vampiruskassa_fail';
$orderId = false;
if ($paymentId) {
	$transactionId = $_SESSION['YANDEXCHECKOUTVS_ID'];
	list($orderId, $paymentId) = \Bitrix\Sale\PaySystem\Manager::getIdsByPayment($paymentId);
	$order  = \Bitrix\Sale\Order::load($orderId);



	$paymentCollection = $order->getPaymentCollection();
	$payment = $paymentCollection->getItemById($paymentId);

	$status = CVampiRUSYandexkassaPayment::getTransactionStatus($payment, $transactionId);
	switch($status) {
		case 'pending':$class="vampiruskassa_success";$msg = GetMessage("VAMPIRUS.YANDEXKASSA_PENDING_PAYMENT");break;
		case 'waiting_for_capture':$class="vampiruskassa_success";$msg = GetMessage("VAMPIRUS.YANDEXKASSA_SUCCESS_RESULT");break;
		case 'succeeded':$class="vampiruskassa_success";$msg = GetMessage("VAMPIRUS.YANDEXKASSA_SUCCESS_RESULT");break;
		case 'canceled':$class="vampiruskassa_fail";$msg = GetMessage("VAMPIRUS.YANDEXKASSA_FAIL_RESULT");break;
		default:$class="vampiruskassa_fail";$msg = GetMessage("VAMPIRUS.YANDEXKASSA_EMPTY_RESULT");
	}

}

if($order){
	$statusPageURL = sprintf('%s/%s', GetPagePath('personal/orders'), (int)$orderId);
}

?>
<style>
	.vampiruskassa_success, .vampiruskassa_fail {
		    display: flex;
		    align-items: center;
	}
	.vampiruskassa_success:before{
		display: inline-block;
		content:'\2713';
		color: #41ca38;
		border: 2px solid #41ca38;
		font-size: 40px;
	    width: 40px;
	    height: 40px;
	    line-height: 32px;
	    text-align: center;
	    margin-right: 15px;
	    border-radius: 50%;

	}

	.vampiruskassa_fail:before{
		display: block;
		content:'\2A2F';
		color: #fb4234;
		border: 2px solid #fb4234;
		font-size: 40px;
	    width: 40px;
	    height: 40px;
	    line-height: 32px;
	    text-align: center;
	    margin-right: 15px;
	    border-radius: 50%;
	}
</style>
<p class="vampiruskassa_result_page <?=$class?>">
<?php if (!$orderId): ?>
	<?=GetMessage("VAMPIRUS.YANDEXKASSA_SUCCESS_NOTFOUND", array('#ORDER_ID#' => htmlspecialchars($orderId)))?>
<?php else: ?>
	<span>
	<?=$msg?><br/>
	<?=GetMessage("VAMPIRUS.YANDEXKASSA_SUCCESS_LINK", array('#LINK#' => $statusPageURL))?>
	</span>
<?php endif; ?>
</p>
<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>