<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}

IncludeModuleLangFile(__FILE__);
if (!CModule::IncludeModule("vampirus.yandexkassa") || !CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog")) {
	return;
}

$order_id       = CSalePaySystemAction::GetParamValue("ORDER_ID");
$order_number   = CSalePaySystemAction::GetParamValue("ORDER_NUMBER", "");
$payment_id     = CSalePaySystemAction::GetParamValue("PAYMENT_ID", "");
$payment_number = CSalePaySystemAction::GetParamValue("PAYMENT_NUMBER", "");

$PaymentSubjectType         = CSalePaySystemAction::GetParamValue("PAYMENT_SUBJECT_TYPE", "commodity");
$DeliveryPaymentSubjectType = CSalePaySystemAction::GetParamValue("DELIVERY_PAYMENT_SUBJECT_TYPE", "service");
$PaymentMethodType          = CSalePaySystemAction::GetParamValue("PAYMENT_METHOD_TYPE", "full_payment");

//совместимость со старыми настройками, когда не было номера заказа
if (!$order_number) {
	$order_number = $order_id;
}

$fio = 0;
if ($order_id === 'FIO') {
	$order_id = $order_number;
	$fio      = 1;
}

if (class_exists("Bitrix\Sale\Order")) {
	$saleOrder = Bitrix\Sale\Order::load($order_id);
} else {
	$saleOrder = null;
}
if ($fio) {
	$orderProps     = $saleOrder->getPropertyCollection();
	$name           = $orderProps->getPayerName();
	$customerNumber = trim($name->getViewHtml());
} else {
	$customerNumber = $order_id;
}

if ($payment_id && $payment_number && is_numeric($payment_id)) {
	if (!preg_match('#/1$#', $payment_number)) {
		//если не первая оплата, то изменяем номер заказа на номер платежа
		$order_number = $payment_number;
	}
}

$order = CSaleOrder::GetByID($order_id);

$sum   = CVampiRUSYandexKassaPayment::preparePrice(CSalePaySystemAction::GetParamValue("SHOULD_PAY"));
$email = CSalePaySystemAction::GetParamValue("EMAIL");
if (!$email) {
	$email = CSaleOrderPropsValue::GetList([], ['ORDER_ID' => $order_id, 'CODE' => 'EMAIL'])->Fetch();
	$email = $email['VALUE'];
}

$phoneField = CSaleOrderPropsValue::GetList([], ['ORDER_ID' => $order_id, 'CODE' => 'PHONE'])->Fetch();
$phone      = CVampiRUSYandexKassaPayment::getPhone($phoneField['VALUE']);

$link               = (CVampiRUSYandexKassaPayment::demoMode()) ? "https://demomoney.yandex.ru/eshop.xml" : "https://yoomoney.ru/eshop.xml";
$host               = CVampiRUSYandexKassaPayment::getHost();
$productTaxSettings = CVampiRUSYandexKassaPayment::getProductTax();
$success_url        = $host . '/bitrix/tools/yandexkassa_success.php?OrderId=' . $order_id;
$fail_url           = $host . '/bitrix/tools/yandexkassa_fail.php?OrderId=' . $order_id;

$dbBasketItems = CSaleBasket::GetList(array(), array("ORDER_ID" => $order_id, 'SET_PARENT_ID' => null), false, false, array());
$items         = array();

$discount = $order['DISCOUNT_VALUE'];
if ($order['PRICE'] != $sum) {
	$discount += $order['PRICE'] - $sum;
}

if ($discount) {
	$discount = $discount / ($order['PRICE'] + $order['DISCOUNT_VALUE']);
}
$receipt = array(
	'customer'  => array(),
	'taxSystem' => CVampiRUSYandexKassaPayment::getTaxSystem(),
);

if ($email) {
	$receipt['customer']['email'] = $email;
}
if ($phone) {
	$receipt['customer']['phone'] = $phone;
}
$inn = CSalePaySystemAction::GetParamValue("INN", "");
if ($inn) {
	$receipt['customer']['inn']       = $inn;
	$receipt['customer']['full_name'] = CSalePaySystemAction::GetParamValue("FILL_NAME", "");
}
$i          = 0;
$total_sum  = 0;
$orderItems = array();
while ($arItems = $dbBasketItems->Fetch()) {
	if ($arItems['PRICE'] == 0) {
		continue;
	}

	$orderItems[] = $arItems;
}
$shipping = CVampiRUSYandexKassaPayment::preparePrice($order['PRICE_DELIVERY']);
foreach ($orderItems as $arItems) {
	if (!$productTaxSettings) {
		$product_info = CCatalogProduct::GetByID($arItems['PRODUCT_ID']);
		$productTax   = CVampiRUSYandexKassaPayment::convertVatId($product_info['VAT_ID']);
	} else {
		$productTax = $productTaxSettings;
	}
	$i++;
	if ($i == count($orderItems)) {
		$subtotal = $sum - round($shipping * (1 - $discount), 2) - $total_sum;

		$final = round($subtotal / $arItems['QUANTITY'], 2);
		if (abs($final * $arItems['QUANTITY'] - $subtotal) > 0.001) {
			if ($arItems['QUANTITY'] == 1.0) {
				$final = $subtotal;
			} elseif ($arItems['QUANTITY'] > 1.0) {
				$arItems['QUANTITY'] = $arItems['QUANTITY'] - 1;
				$items[]             = array(
					'text'               => mb_substr($arItems['NAME'], 0, 128),
					'quantity'           => 1,
					'price'              => array(
						'amount'   => number_format($subtotal - $final * $arItems['QUANTITY'], 2, '.', ''),
						'currency' => 'RUB',
					),
					'tax'                => $productTax,
					'paymentSubjectType' => $PaymentSubjectType,
					'paymentMethodType'  => $PaymentMethodType,
				);
			} else {
				$qty = round($arItems['QUANTITY'] / 2.0, 3);
				$arItems['QUANTITY'] -= $qty;
				$items[] = array(
					'text'               => mb_substr($arItems['NAME'], 0, 128),
					'quantity'           => $qty,
					'price'              => array(
						'amount'   => number_format(round(($subtotal - $final * $arItems['QUANTITY']) / $qty, 2), 2, '.', ''),
						'currency' => 'RUB',
					),
					'tax'                => $productTax,
					'paymentSubjectType' => $PaymentSubjectType,
					'paymentMethodType'  => $PaymentMethodType,
				);
			}
		}
	} else {
		$final    = CVampiRUSYandexKassaPayment::preparePrice($arItems['PRICE'] * (1 - $discount));
		$subtotal = round($final * $arItems['QUANTITY'], 2);
	}
	$total_sum += $subtotal;
	$items[] = array(
		'text'               => mb_substr($arItems['NAME'], 0, 128),
		'quantity'           => $arItems['QUANTITY'],
		'price'              => array(
			'amount'   => number_format($final, 2, '.', ''),
			'currency' => 'RUB',
		),
		'tax'                => $productTax,
		'paymentSubjectType' => $PaymentSubjectType,
		'paymentMethodType'  => $PaymentMethodType,
	);
}
if ($shipping > 0) {
	$deliveryTax = CVampiRUSYandexKassaPayment::getDeliveryTax();
	if (!$deliveryTax) {
		if ($saleOrder) {
			$delivery_id = $saleOrder->getField('DELIVERY_ID');
		} else {
			$delivery_id = $order['DELIVERY_ID'];
		}
		if (is_numeric($delivery_id)) {
			$delivery = \Bitrix\Sale\Delivery\Services\Manager::getById($delivery_id);
			$vat_id   = $delivery['VAT_ID'];
		} else {
			$delivery = \Bitrix\Sale\Delivery\Services\Manager::getObjectByCode($delivery_id);
			$vat_id   = $delivery->getParentService()->getVatId();
		}
		$deliveryTax = CVampiRUSYandexKassaPayment::convertVatId($vat_id);

	}
	$items[] = array(
		'text'               => GetMessage('VAMPIRUS.YANDEXKASSA_DELIVERY'),
		'quantity'           => 1,
		'price'              => array(
			'amount'   => number_format(round($shipping * (1 - $discount), 2), 2, '.', ''),
			'currency' => 'RUB',
		),
		'tax'                => $deliveryTax,
		'paymentSubjectType' => $DeliveryPaymentSubjectType,
		'paymentMethodType'  => $PaymentMethodType,
	);
}
$receipt['items'] = $items;
$ptype            = CSalePaySystemAction::GetParamValue("CARD");
if ($ptype == '0') {
	$ptype = '';
}
$receipt = \Bitrix\Main\Web\Json::encode($receipt);

?>
<form method="post" action="<?=$link?>" name="yandexkassa_form">
<input type="hidden" name="scid" value="<?=CVampiRUSYandexKassaPayment::getScid()?>">
<input type="hidden" name="ShopId" value="<?=CVampiRUSYandexKassaPayment::getShopId()?>">
<input type="hidden" name="Sum" value="<?=$sum?>">
<input type="hidden" name="customerNumber" value="<?=$customerNumber?>">
<input type="hidden" name="orderNumber" value="<?=$order_number?>">
<input type="hidden" name="cps_email" value="<?=CSalePaySystemAction::GetParamValue("EMAIL")?>">
<?php if ($phone):?>
<input type="hidden" name="cps_phone" value="8<?=substr($phone,-10)?>">
<?php endif; ?>
<input type="hidden" name="cms_name" value="1c-bitrix_vampirus">
<input type="hidden" name="OrderDetails" value="<?=GetMessage('VAMPIRUS.YANDEXKASSA_ORDER_DESC')?><?=$order_number?>">
<input type="hidden" name="ym_merchant_receipt" value='<?=$receipt?>'>
<?
if (CSalePaySystemAction::GetParamValue("HOLD", 0)) {
	$custom_field = CVampiRUSYandexKassaPayment::insertInvoice($order_id, $sum, $receipt);
	?>
<input type="hidden" name="custom_field" value='<?=$custom_field?>'>
	<?
}
if ($payment_id && $payment_number && is_numeric($payment_id)) {?>
	<input type="hidden" name="payment_id" value='<?=$payment_id?>'>
<?php }?>
<input name="paymentType"  value="<?=$ptype?>" type="hidden">
<input name="CustEMail"  value="<?=CSalePaySystemAction::GetParamValue("EMAIL")?>" type="hidden">
<input name="shopSuccessURL" type="hidden" value="<?=$success_url?>">
<input name="shopFailURL" type="hidden" value="<?=$fail_url?>">
<input class="btn btn-primary button yandexkassa_payment_button " type='submit' name='pay' value='<?=GetMessage('VAMPIRUS.YANDEXKASSA_GO_TO_PAYMENT')?>'>
</form>