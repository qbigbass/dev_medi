<?php
$_GET['ORDER_ID']   = 1;
$_GET['PAYMENT_ID'] = 1;

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";

$connection = \Bitrix\Main\Application::getConnection();
$sqlHelper  = $connection->getSqlHelper();
$context    = \Bitrix\Main\Application::getInstance()->getContext();

CModule::IncludeModule("sale");

$data = $connection
	->query("
				SELECT order_id FROM vampirus_yandexkassa_order WHERE id='" . $sqlHelper->forSql($context->getRequest()->get('id')) . "'")
	->fetch();
if (!$data) {
	LocalRedirect('/personal/orders/');
}

$order = \Bitrix\Sale\Order::load($data['order_id']);
if ($order->isPaid() || $order->isCanceled()) {
	LocalRedirect('/personal/orders/');
}
$paymentCollection = $order->getPaymentCollection();
foreach ($paymentCollection as $payment) {
	if ($payment->isPaid()) {
		continue;
	}

	$paymentSystem      = $payment->getPaySystem();
	$paymentSystemClass = get_class($paymentSystem);
	$reflectionClass    = new ReflectionClass($paymentSystemClass);
	$reflectionProperty = $reflectionClass->getProperty('handler');
	$reflectionProperty->setAccessible(true);
	$handler = $reflectionProperty->getValue($paymentSystem);
	if (!($handler instanceof Sale\Handlers\PaySystem\YandexCheckoutVSHandler)) {
		continue;
	}

	$paymentSystem->initiatePay($payment, $context->getRequest());
}
LocalRedirect('/personal/orders/');
